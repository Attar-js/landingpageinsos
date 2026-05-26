<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupCpmkRubric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimPenciriController extends Controller
{
    public function dashboard()
    {
        [$formKesediaan, $proposal, $laporanAkhir, $luaran, $errorMessage] = $this->fetchVerificationData();
        $counts = $this->buildCounts($formKesediaan, $proposal, $laporanAkhir, $luaran);

        return view('tim-penciri.dashboard', compact('counts', 'errorMessage'));
    }

    public function kesediaan()
    {
        [$formKesediaan, $proposal, $laporanAkhir, $luaran, $errorMessage] = $this->fetchVerificationData();
        $counts = $this->buildCounts($formKesediaan, $proposal, $laporanAkhir, $luaran);
        return view('tim-penciri.kesediaan', compact('formKesediaan', 'counts', 'errorMessage'));
    }

    public function kesediaanProposal()
    {
        [$formKesediaan, $proposal, $laporanAkhir, $luaran, $errorMessage] = $this->fetchVerificationData();
        $counts = $this->buildCounts($formKesediaan, $proposal, $laporanAkhir, $luaran);
        return view('tim-penciri.kesediaan-proposal', compact('formKesediaan', 'proposal', 'counts', 'errorMessage'));
    }

    public function proposal()
    {
        [$formKesediaan, $proposal, $laporanAkhir, $luaran, $errorMessage] = $this->fetchVerificationData();
        $counts = $this->buildCounts($formKesediaan, $proposal, $laporanAkhir, $luaran);
        return view('tim-penciri.proposal', compact('proposal', 'counts', 'errorMessage'));
    }

    public function laporanLuaran()
    {
        [$formKesediaan, $proposal, $laporanAkhir, $luaran, $errorMessage] = $this->fetchVerificationData();
        $counts = $this->buildCounts($formKesediaan, $proposal, $laporanAkhir, $luaran);
        return view('tim-penciri.laporan-luaran', compact('laporanAkhir', 'luaran', 'counts', 'errorMessage'));
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|in:local,dashboard',
            'table' => 'required|in:form_kesediaan,proposal,laporan_akhir,luaran',
            'id' => 'required|integer',
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $payload = [
            'status' => $validated['status'],
            'updated_at' => now(),
        ];

        if (!empty($validated['catatan'])) {
            $payload['catatan'] = $validated['catatan'];
        }

        if ($validated['source'] === 'dashboard') {
            DB::connection('dashboard')
                ->table($validated['table'])
                ->where('id', $validated['id'])
                ->update($payload);
        } else {
            DB::table($validated['table'])
                ->where('id', $validated['id'])
                ->update($payload);
        }

        return redirect()->route('tim-penciri.dashboard')->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function detail(Request $request, int $id)
    {
        $validated = $request->validate([
            'source' => 'required|in:local,dashboard',
            'table' => 'required|in:form_kesediaan,proposal,laporan_akhir,luaran',
        ]);

        $query = $validated['source'] === 'dashboard'
            ? DB::connection('dashboard')->table($validated['table'])
            : DB::table($validated['table']);

        $item = $query->where('id', $id)->first();
        if (!$item) {
            return redirect()->route('tim-penciri.dashboard')->with('error', 'Data tidak ditemukan.');
        }

        $leaderNim = $item->user_nim ?? null;
        $group = null;
        $members = collect();

        if ($leaderNim) {
            $leaderUser = DB::table('users')->where('nim', $leaderNim)->first();
            if ($leaderUser) {
                $groupMember = DB::table('group_members')
                    ->where('mahasiswa_id', $leaderUser->id)
                    ->where('role', 'leader')
                    ->first();

                if ($groupMember) {
                    $group = DB::table('groups')->where('id', $groupMember->group_id)->first();
                    $members = DB::table('group_members')
                        ->join('users', 'users.id', '=', 'group_members.mahasiswa_id')
                        ->where('group_members.group_id', $groupMember->group_id)
                        ->select('users.name', 'users.nim', 'group_members.role', 'group_members.status')
                        ->orderByRaw("CASE WHEN group_members.role = 'leader' THEN 0 ELSE 1 END")
                        ->orderBy('users.name')
                        ->get();
                }
            }
        }

        return view('tim-penciri.detail', [
            'item' => $item,
            'source' => $validated['source'],
            'table' => $validated['table'],
            'group' => $group,
            'members' => $members,
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $validated = $request->validate([
            'source' => 'required|in:local,dashboard',
            'table' => 'required|in:form_kesediaan,proposal,laporan_akhir,luaran',
        ]);

        $query = $validated['source'] === 'dashboard'
            ? DB::connection('dashboard')->table($validated['table'])
            : DB::table($validated['table']);

        $deleted = $query->where('id', $id)->delete();

        if (!$deleted) {
            return redirect()->route('tim-penciri.dashboard')->with('error', 'Data gagal dihapus atau tidak ditemukan.');
        }

        return redirect()->route('tim-penciri.dashboard')->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Daftar kelompok untuk penilaian skor rubrik CPMK.
     */
    public function rubrikCpmk()
    {
        $groups = Group::with(['dosen', 'groupLeader', 'cpmkRubric'])
            ->where('status', 'active')
            ->whereNotNull('supervisor_approved_at')
            ->orderByDesc('supervisor_approved_at')
            ->get();

        return view('tim-penciri.rubrik-cpmk', compact('groups'));
    }

    /**
     * Form isi skor rubrik CPMK untuk satu kelompok.
     */
    public function rubrikCpmkEdit(Group $group)
    {
        if (!$group->isSupervisorApproved()) {
            return redirect()->route('tim-penciri.rubrik-cpmk')
                ->with('error', 'Kelompok belum disetujui dosen pembimbing.');
        }

        $group->load(['dosen', 'groupLeader', 'members.mahasiswa', 'cpmkRubric']);

        return view('tim-penciri.rubrik-cpmk-edit', [
            'group' => $group,
            'rubric' => $group->cpmkRubric,
        ]);
    }

    /**
     * Simpan skor rubrik CPMK (Tim MK Penciri).
     */
    public function storeCpmkRubrikSkor(Request $request, Group $group)
    {
        if (!$group->isSupervisorApproved()) {
            return redirect()->back()
                ->with('error', 'Kelompok belum disetujui dosen pembimbing.');
        }

        $validated = $request->validate([
            'skor_p5' => 'required|numeric|min:0|max:100',
            'skor_c3' => 'required|numeric|min:0|max:100',
            'skor_a2' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string|max:2000',
        ]);

        $existing = GroupCpmkRubric::where('group_id', $group->id)->first();

        if (!$existing || !$existing->hasDeskripsi()) {
            return redirect()->back()
                ->with('error', 'Mahasiswa belum mengisi deskripsi kegiatan. Skor tidak dapat disimpan.')
                ->withInput();
        }

        $existing->update([
            'skor_p5' => $validated['skor_p5'],
            'skor_c3' => $validated['skor_c3'],
            'skor_a2' => $validated['skor_a2'],
            'catatan' => $validated['catatan'] ?? $existing->catatan,
            'skor_filled_by' => Auth::id(),
        ]);

        return redirect()->route('tim-penciri.rubrik-cpmk')
            ->with('success', 'Skor rubrik CPMK untuk kelompok "' . $group->nama_kelompok . '" berhasil disimpan.');
    }

    private function fetchVerificationData(): array
    {
        $errorMessage = null;
        $formKesediaan = DB::table('form_kesediaan')->orderByDesc('created_at')->limit(100)->get();

        try {
            $proposal = DB::connection('dashboard')->table('proposal')->orderByDesc('created_at')->limit(100)->get();
            $laporanAkhir = DB::connection('dashboard')->table('laporan_akhir')->orderByDesc('created_at')->limit(100)->get();
            $luaran = DB::connection('dashboard')->table('luaran')->orderByDesc('created_at')->limit(100)->get();
        } catch (\Throwable $e) {
            $proposal = collect();
            $laporanAkhir = collect();
            $luaran = collect();
            $errorMessage = 'Koneksi ke database dashboard belum tersedia: ' . $e->getMessage();
        }

        $formKesediaan = $this->enrichWithGroupMetadata($formKesediaan);
        $proposal = $this->enrichWithGroupMetadata($proposal);
        $laporanAkhir = $this->enrichWithGroupMetadata($laporanAkhir);
        $luaran = $this->enrichWithGroupMetadata($luaran);

        return [$formKesediaan, $proposal, $laporanAkhir, $luaran, $errorMessage];
    }

    private function buildCounts($formKesediaan, $proposal, $laporanAkhir, $luaran): array
    {
        return [
            'form_kesediaan_pending' => $formKesediaan->where('status', 'pending')->count(),
            'proposal_pending' => $proposal->where('status', 'pending')->count(),
            'laporan_pending' => $laporanAkhir->where('status', 'pending')->count(),
            'luaran_pending' => $luaran->where('status', 'pending')->count(),
        ];
    }

    private function enrichWithGroupMetadata($rows)
    {
        if ($rows->isEmpty()) {
            return $rows;
        }

        $nims = $rows->pluck('user_nim')->filter()->unique()->values();
        if ($nims->isEmpty()) {
            return $rows;
        }

        $userIdByNim = DB::table('users')
            ->whereIn('nim', $nims)
            ->pluck('id', 'nim');

        $dosenIdByNip = DB::table('users')
            ->whereIn('nip', $nims)
            ->pluck('id', 'nip');

        if ($userIdByNim->isEmpty() && $dosenIdByNip->isEmpty()) {
            return $rows;
        }

        $activeCountSubquery = DB::table('group_members')
            ->select('group_id', DB::raw('COUNT(*) as total_anggota'))
            ->where('status', 'active')
            ->groupBy('group_id');

        $groupByLeaderId = DB::table('group_members as gm')
            ->join('groups as g', 'g.id', '=', 'gm.group_id')
            ->leftJoinSub($activeCountSubquery, 'active_members', function ($join) {
                $join->on('active_members.group_id', '=', 'g.id');
            })
            ->whereIn('gm.mahasiswa_id', $userIdByNim->values())
            ->where('gm.role', 'leader')
            ->where('gm.status', 'active')
            ->select(
                'gm.mahasiswa_id',
                'g.nama_mitra',
                'g.lokasi_mitra',
                DB::raw('COALESCE(active_members.total_anggota, 0) as jumlah_anggota')
            )
            ->get()
            ->keyBy('mahasiswa_id');

        $groupByMemberId = DB::table('group_members as gm')
            ->join('groups as g', 'g.id', '=', 'gm.group_id')
            ->leftJoinSub($activeCountSubquery, 'active_members', function ($join) {
                $join->on('active_members.group_id', '=', 'g.id');
            })
            ->whereIn('gm.mahasiswa_id', $userIdByNim->values())
            ->where('gm.status', 'active')
            ->orderByRaw("CASE WHEN gm.role = 'leader' THEN 0 ELSE 1 END")
            ->select(
                'gm.mahasiswa_id',
                'g.nama_mitra',
                'g.lokasi_mitra',
                DB::raw('COALESCE(active_members.total_anggota, 0) as jumlah_anggota')
            )
            ->get()
            ->groupBy('mahasiswa_id')
            ->map(function ($items) {
                return $items->first();
            });

        $groupsByDosenId = DB::table('groups as g')
            ->leftJoinSub($activeCountSubquery, 'active_members', function ($join) {
                $join->on('active_members.group_id', '=', 'g.id');
            })
            ->whereIn('g.dosen_id', $dosenIdByNip->values())
            ->whereIn('g.status', ['active', 'approved', 'assigned'])
            ->select(
                'g.id',
                'g.dosen_id',
                'g.judul_kegiatan',
                'g.nama_mitra',
                'g.lokasi_mitra',
                DB::raw('COALESCE(active_members.total_anggota, 0) as jumlah_anggota')
            )
            ->orderByDesc('g.id')
            ->get()
            ->groupBy('dosen_id');

        $groupsByTitle = DB::table('groups as g')
            ->leftJoinSub($activeCountSubquery, 'active_members', function ($join) {
                $join->on('active_members.group_id', '=', 'g.id');
            })
            ->whereNotNull('g.judul_kegiatan')
            ->whereIn('g.status', ['active', 'approved', 'assigned'])
            ->select(
                'g.id',
                'g.judul_kegiatan',
                'g.nama_mitra',
                'g.lokasi_mitra',
                DB::raw('COALESCE(active_members.total_anggota, 0) as jumlah_anggota')
            )
            ->orderByDesc('g.id')
            ->get()
            ->groupBy('judul_kegiatan')
            ->map(function ($items) {
                return $items->first();
            });

        return $rows->map(function ($row) use ($userIdByNim, $groupByLeaderId, $groupByMemberId, $dosenIdByNip, $groupsByDosenId, $groupsByTitle) {
            $nim = $row->user_nim ?? null;
            $leaderId = $nim ? ($userIdByNim[$nim] ?? null) : null;
            $meta = null;
            if ($leaderId) {
                $meta = $groupByLeaderId[$leaderId] ?? null;
                if (!$meta) {
                    $meta = $groupByMemberId[$leaderId] ?? null;
                }
            }

            if (!$meta && $nim) {
                $dosenId = $dosenIdByNip[$nim] ?? null;
                if ($dosenId && isset($groupsByDosenId[$dosenId])) {
                    $dosenGroups = $groupsByDosenId[$dosenId];
                    $judul = $row->judul_kegiatan ?? null;
                    $meta = $judul
                        ? $dosenGroups->firstWhere('judul_kegiatan', $judul)
                        : $dosenGroups->first();
                    $meta = $meta ?: $dosenGroups->first();
                }
            }

            if (!$meta && !empty($row->judul_kegiatan)) {
                $meta = $groupsByTitle[$row->judul_kegiatan] ?? null;
            }

            if ($meta) {
                if (!isset($row->mitra) || trim((string) $row->mitra) === '') {
                    $row->mitra = $meta->nama_mitra;
                }
                if (!isset($row->lokasi_mitra) || trim((string) $row->lokasi_mitra) === '') {
                    $row->lokasi_mitra = $meta->lokasi_mitra;
                }
                if (!isset($row->jumlah_anggota) || trim((string) $row->jumlah_anggota) === '') {
                    $row->jumlah_anggota = $meta->jumlah_anggota;
                }
            }

            return $row;
        });
    }
}

