<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Penilaian;
use App\Models\KknPendaftar;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\SupervisorRequest;
use App\Models\FormKesediaan;
use App\Models\GroupDocumentReview;
use App\Services\GroupDocumentStatusService;
use App\Services\GroupProposalReviewService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DosenController extends Controller
{
    /**
     * Dashboard dosen
     */
    public function dashboard()
    {
        $dosen = Auth::user();
        
        // Ambil semua mahasiswa
        $mahasiswa = User::where('role', 'mahasiswa')->get();
        
        // Statistik
        $totalMahasiswa = $mahasiswa->count();
        $mahasiswaSudahDinilai = Penilaian::where('dosen_id', $dosen->id)->count();
        $mahasiswaBelumDinilai = $totalMahasiswa - $mahasiswaSudahDinilai;
        
        // Mahasiswa yang belum dinilai
        $mahasiswaBelumDinilaiList = User::where('role', 'mahasiswa')
            ->whereNotIn('id', Penilaian::where('dosen_id', $dosen->id)->pluck('mahasiswa_id'))
            ->get();
        
        return view('dosen.dashboard', compact('mahasiswa', 'totalMahasiswa', 'mahasiswaSudahDinilai', 'mahasiswaBelumDinilai', 'mahasiswaBelumDinilaiList'));
    }

    /**
     * Halaman mahasiswa bimbingan
     */
    public function mahasiswaBimbingan()
    {
        $dosen = Auth::user();

        $localGroups = Group::with(['members.mahasiswa', 'dosen'])
            ->where('dosen_id', $dosen->id)
            ->orderByDesc('created_at')
            ->get();

        $groups = $localGroups->map(function ($group) use ($dosen) {
            $members = $group->members->map(function ($member) use ($dosen) {
                $nim = $member->mahasiswa->nim ?? null;
                $penilaian = $nim
                    ? DB::table('penilaian')
                        ->where('mahasiswa_nim', $nim)
                        ->where('dosen_id', $dosen->id)
                        ->first()
                    : null;

                return [
                    'name' => $member->mahasiswa->name ?? '-',
                    'nim' => $nim ?? '-',
                    'role' => $member->role === 'leader' ? 'Ketua' : 'Anggota',
                    'status_dinilai' => $penilaian ? 'Sudah Dinilai' : 'Belum Dinilai',
                    'nilai_akhir' => $penilaian->nilai_akhir ?? null,
                ];
            })->values()->all();

            return [
                'id' => $group->id,
                'nama_kelompok' => $group->nama_kelompok,
                'judul_kegiatan' => $group->judul_kegiatan,
                'lokasi_kkn' => $group->lokasi_kkn ?? 'Lokasi KKN',
                'nama_mitra' => $group->nama_mitra ?? 'N/A',
                'lokasi_mitra' => $group->lokasi_mitra ?? 'N/A',
                'members' => $members,
                'dosen_name' => $group->dosen->name ?? $dosen->name,
                'assigned_at' => $group->supervisor_approved_at ?? $group->created_at,
                'status' => $group->status,
                'progress_verifikasi' => $group->progress_verifikasi ?? 0,
            ];
        })->values()->all();

        return view('dosen.mahasiswa-bimbingan', compact('groups'));
    }

    /**
     * Detail mahasiswa bimbingan
     */
    public function detailMahasiswaBimbingan($group_id)
    {
        $dosen = Auth::user();

        $localGroup = Group::with(['members.mahasiswa', 'dosen', 'cpmkRubric'])
            ->where('id', $group_id)
            ->where('dosen_id', $dosen->id)
            ->first();

        if (!$localGroup) {
            abort(404, 'Kelompok tidak ditemukan atau tidak diampu oleh Anda.');
        }

        $members = [];
        foreach ($localGroup->members as $member) {
            $nim = $member->mahasiswa->nim ?? null;
            $penilaian = $nim
                ? DB::table('penilaian')
                    ->where('mahasiswa_nim', $nim)
                    ->where('dosen_id', $dosen->id)
                    ->first()
                : null;

            $statusDinilai = $penilaian ? 'Sudah Dinilai' : 'Belum Dinilai';
            $nilaiAkhir = $penilaian ? $penilaian->nilai_akhir : null;
            $peerReviewAuto = $nim ? $this->getPeerReviewAverageForNim($nim) : null;

            $members[] = [
                'name' => $member->mahasiswa->name ?? '-',
                'nim' => $nim ?? '-',
                'role' => $member->role === 'leader' ? 'Ketua' : 'Anggota',
                'status_dinilai' => $statusDinilai,
                'nilai_akhir' => $nilaiAkhir,
                'proposal_kegiatan' => $penilaian->proposal_kegiatan ?? null,
                'asistensi' => $penilaian->asistensi ?? null,
                'peer_review' => $peerReviewAuto ?? ($penilaian->peer_review ?? null),
                'laporan_akhir' => $penilaian->laporan_akhir ?? null,
                'presentasi_akhir' => $penilaian->presentasi_akhir ?? null,
                'pembimbing_lapangan' => $penilaian->pembimbing_lapangan ?? null,
                'peer_review_complete' => $nim ? $this->isPeerReviewCompleteForNim($nim) : false,
            ];
        }

        $group = [
            'id' => $localGroup->id,
            'nama_kelompok' => $localGroup->nama_kelompok,
            'judul_kegiatan' => $localGroup->judul_kegiatan,
            'lokasi_kkn' => $localGroup->lokasi_kkn ?: 'Lokasi KKN',
            'nama_mitra' => $localGroup->nama_mitra ?: 'N/A',
            'lokasi_mitra' => $localGroup->lokasi_mitra ?: 'N/A',
            'members' => $members,
            'dosen_name' => $localGroup->dosen->name ?? $dosen->name,
            'assigned_at' => $localGroup->supervisor_approved_at ?? $localGroup->created_at,
            'status' => $localGroup->status,
            'progress_verifikasi' => $localGroup->progress_verifikasi ?? 0,
        ];

        $dokumen = $this->getDokumenKelompok($group);
        $cpmkRubric = $localGroup->cpmkRubric;
        $groupModel = $localGroup;

        return view('dosen.detail-mahasiswa-bimbingan', compact('group', 'dokumen', 'cpmkRubric', 'groupModel'));
    }

    /**
     * Halaman penilaian mahasiswa
     */
    public function penilaian()
    {
        // Method ini tidak digunakan untuk halaman penilaian individual
        // Redirect ke mahasiswa bimbingan
        return redirect()->route('dosen.mahasiswa-bimbingan');
    }

    /**
     * Detail mahasiswa untuk penilaian
     */
    public function showPenilaian($mahasiswa_id)
    {
        $dosen = Auth::user();
        
        // Ambil data mahasiswa dari dashboard database
        $mahasiswa = DB::connection('dashboard')
            ->table('kkn_anggota')
            ->where('nim', $mahasiswa_id)
            ->first();
        
        if (!$mahasiswa) {
            abort(404, 'Mahasiswa tidak ditemukan');
        }
        
        // Ambil data pendaftar untuk informasi tambahan
        $pendaftar = DB::connection('dashboard')
            ->table('kkn_pendaftar')
            ->where('id', $mahasiswa->kkn_pendaftar_id)
            ->first();
        
        // Siapkan data mahasiswa untuk view
        $mahasiswaData = [
            'nama' => $mahasiswa->nama,
            'nim' => $mahasiswa->nim,
            'program_studi' => $mahasiswa->program_studi ?? 'Sistem Informasi',
            'judul_kegiatan' => $pendaftar->judul_kegiatan ?? 'Pemberdayaan Masyarakat Pesisir',
            'nama_mitra' => $pendaftar->mitra ?? 'Kampung Blaton',
            'lokasi_mitra' => $pendaftar->lokasi_mitra ?? 'J. Murjani',
            'peran' => $mahasiswa->peran
        ];
        
        // Ambil penilaian yang sudah ada
        $penilaian = Penilaian::where('mahasiswa_nim', $mahasiswa_id)
            ->where('dosen_id', $dosen->id)
            ->first();

        $autoPeerReview = $this->getPeerReviewAverageForNim($mahasiswa_id);
        $peerReviewComplete = $this->isPeerReviewCompleteForNim($mahasiswa_id);

        return view('dosen.penilaian', compact('mahasiswaData', 'penilaian', 'autoPeerReview', 'peerReviewComplete'));
    }

    /**
     * Simpan penilaian mahasiswa
     */
    public function storePenilaian(Request $request)
    {
        try {
            $request->validate([
                'mahasiswa_nim' => 'required|string',
                'dosen_id' => 'required|integer',
                'proposal_kegiatan' => 'nullable|numeric|min:0|max:100',
                'asistensi' => 'nullable|numeric|min:0|max:100',
                'cpmk_p5' => 'nullable|numeric|min:0|max:100',
                'cpmk_c3' => 'nullable|numeric|min:0|max:100',
                'cpmk_a2' => 'nullable|numeric|min:0|max:100',
                'peer_review' => 'nullable|numeric|min:0|max:100',
                'laporan_akhir' => 'nullable|numeric|min:0|max:100',
                'presentasi_akhir' => 'nullable|numeric|min:0|max:100',
                'pembimbing_lapangan' => 'nullable|numeric|min:0|max:100',
                'catatan' => 'nullable|string|max:2000',
            ]);

            if (!$this->isPeerReviewCompleteForNim($request->mahasiswa_nim)) {
                return redirect()->back()
                    ->with('error', 'Nilai akhir belum bisa disimpan karena peer review kelompok belum lengkap.')
                    ->withInput();
            }

            $peerReviewScore = $this->getPeerReviewAverageForNim($request->mahasiswa_nim);
            if ($peerReviewScore === null) {
                $peerReviewScore = (float) ($request->peer_review ?? 0);
            }

            $proposalKegiatan = $request->proposal_kegiatan ?? $request->cpmk_p5 ?? 0;
            $asistensi = $request->asistensi ?? 0;
            $laporanAkhir = $request->laporan_akhir ?? $request->cpmk_a2 ?? 0;
            $presentasiAkhir = $request->presentasi_akhir ?? $request->cpmk_c3 ?? 0;
            $pembimbingLapangan = $request->pembimbing_lapangan ?? 0;

            // Formula final sesuai bobot komponen pada form penilaian akhir.
            $totalNilai = ($proposalKegiatan * 0.20)
                + ($asistensi * 0.10)
                + ($peerReviewScore * 0.15)
                + ($laporanAkhir * 0.20)
                + ($presentasiAkhir * 0.15)
                + ($pembimbingLapangan * 0.20);

            // Save to project-akhir database
            DB::table('penilaian')->updateOrInsert(
                [
                    'mahasiswa_nim' => $request->mahasiswa_nim,
                    'dosen_id' => $request->dosen_id,
                ],
                [
                    'nilai_akhir' => $totalNilai,
                    'proposal_kegiatan' => $proposalKegiatan,
                    'asistensi' => $asistensi,
                    'peer_review' => $peerReviewScore,
                    'laporan_akhir' => $laporanAkhir,
                    'presentasi_akhir' => $presentasiAkhir,
                    'pembimbing_lapangan' => $pembimbingLapangan,
                    'catatan' => $request->catatan ?: null,
                    'tanggal_penilaian' => now(),
                    'updated_at' => now(),
                ]
            );

            // SEND TO dashboard DASHBOARD VIA API
            $hopeUiApiUrl = \App\Helpers\DashboardHelper::getNilaiAkhirReceiveUrl();
            
            // Prepare data for API
            $apiData = [
                'mahasiswa_nim' => $request->mahasiswa_nim,
                'dosen_id' => $request->dosen_id,
                'nilai_akhir' => $totalNilai,
                'proposal_kegiatan' => $proposalKegiatan,
                'asistensi' => $asistensi,
                'peer_review' => $peerReviewScore,
                'laporan_akhir' => $laporanAkhir,
                'presentasi_akhir' => $presentasiAkhir,
                'pembimbing_lapangan' => $pembimbingLapangan,
                'tanggal_penilaian' => now()->format('Y-m-d H:i:s')
            ];

            // Send data to dashboard API using cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $hopeUiApiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'X-Requested-With: XMLHttpRequest'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Log the API call
            \Log::info('Sending nilai to dashboard API', [
                'url' => $hopeUiApiUrl,
                'data' => $apiData,
                'response' => $response,
                'http_code' => $httpCode
            ]);

            // JavaScript for real-time update and notification
            $jsCode = "
                <script>
                    console.log('Nilai berhasil disimpan di project-akhir database');
                    
                    // Send to dashboard API
                    fetch('" . $hopeUiApiUrl . "', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: '" . http_build_query($apiData) . "'
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response from dashboard API:', data);
                        if (data.success) {
                            // Show success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Nilai berhasil disimpan dan dashboard terupdate secara real-time!',
                                showConfirmButton: true,
                                confirmButtonText: 'Lihat Dashboard'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Open dashboard in new tab
                                    window.open('" . \App\Helpers\DashboardHelper::getNilaiAkhirUrl() . "', '_blank');
                                }
                            });
                            
                            // Trigger real-time update on dashboard
                            if (window.opener && window.opener.location.href.includes('nilai-akhir')) {
                                window.opener.location.reload();
                            }
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: 'Nilai berhasil disimpan, tetapi gagal memperbarui dashboard: ' + (data.message || 'Unknown error'),
                                showConfirmButton: true
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error sending data to dashboard API:', error);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Nilai berhasil disimpan, tetapi terjadi kesalahan saat memperbarui dashboard.',
                            showConfirmButton: true
                        });
                    });
                </script>
            ";

            return redirect()->back()->with('success', 'Nilai berhasil disimpan!' . $jsCode);

        } catch (\Exception $e) {
            \Log::error('Error in DosenController::storePenilaian: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan nilai: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function storePenilaianKelompok(Request $request, $group_id)
    {
        $dosen = Auth::user();

        $request->validate([
            'scores' => 'required|array',
            'scores.*.proposal_kegiatan' => 'nullable|numeric|min:0|max:100',
            'scores.*.asistensi' => 'nullable|numeric|min:0|max:100',
            'scores.*.laporan_akhir' => 'nullable|numeric|min:0|max:100',
            'scores.*.presentasi_akhir' => 'nullable|numeric|min:0|max:100',
            'scores.*.pembimbing_lapangan' => 'nullable|numeric|min:0|max:100',
            'scores.*.catatan' => 'nullable|string|max:1000',
        ]);

        $group = Group::where('id', $group_id)
            ->where('dosen_id', $dosen->id)
            ->first();

        if (!$group) {
            return redirect()->back()->with('error', 'Kelompok tidak ditemukan atau tidak diampu oleh Anda.');
        }

        $anggota = GroupMember::with('mahasiswa')
            ->where('group_id', $group_id)
            ->where('status', 'active')
            ->get()
            ->pluck('mahasiswa.nim')
            ->filter()
            ->map(fn ($nim) => (string) $nim)
            ->values()
            ->all();
        $anggotaSet = array_fill_keys($anggota, true);

        $savedCount = 0;
        $incompletePeerReviewNims = [];

        DB::transaction(function () use ($request, $dosen, $anggotaSet, &$savedCount, &$incompletePeerReviewNims) {
            foreach ($request->scores as $nim => $score) {
                $nim = (string) $nim;
                if (!isset($anggotaSet[$nim])) {
                    continue;
                }

                $peerReviewComplete = $this->isPeerReviewCompleteForNim($nim);
                if (!$peerReviewComplete) {
                    $incompletePeerReviewNims[] = $nim;
                }

                $existing = DB::table('penilaian')
                    ->where('mahasiswa_nim', $nim)
                    ->where('dosen_id', $dosen->id)
                    ->first();

                $proposalKegiatan = $this->coalesceScore($score['proposal_kegiatan'] ?? null, $existing->proposal_kegiatan ?? null);
                $asistensi = $this->coalesceScore($score['asistensi'] ?? null, $existing->asistensi ?? null);
                $laporanAkhir = $this->coalesceScore($score['laporan_akhir'] ?? null, $existing->laporan_akhir ?? null);
                $presentasiAkhir = $this->coalesceScore($score['presentasi_akhir'] ?? null, $existing->presentasi_akhir ?? null);
                $pembimbingLapangan = $this->coalesceScore($score['pembimbing_lapangan'] ?? null, $existing->pembimbing_lapangan ?? null);

                $hasAnyManualInput = ($score['proposal_kegiatan'] ?? null) !== null
                    || ($score['asistensi'] ?? null) !== null
                    || ($score['laporan_akhir'] ?? null) !== null
                    || ($score['presentasi_akhir'] ?? null) !== null
                    || ($score['pembimbing_lapangan'] ?? null) !== null;

                if (!$hasAnyManualInput && !$existing) {
                    continue;
                }

                $peerReviewScore = $this->getPeerReviewAverageForNim($nim);
                if ($peerReviewScore === null) {
                    $peerReviewScore = (float) ($existing->peer_review ?? 0);
                }
                $totalNilai = ($proposalKegiatan * 0.20)
                    + ($asistensi * 0.10)
                    + ($peerReviewScore * 0.15)
                    + ($laporanAkhir * 0.20)
                    + ($presentasiAkhir * 0.15)
                    + ($pembimbingLapangan * 0.20);

                DB::table('penilaian')->updateOrInsert(
                    [
                        'mahasiswa_nim' => $nim,
                        'dosen_id' => $dosen->id,
                    ],
                    [
                        'nilai_akhir' => $totalNilai,
                        'proposal_kegiatan' => $proposalKegiatan,
                        'asistensi' => $asistensi,
                        'peer_review' => $peerReviewScore,
                        'laporan_akhir' => $laporanAkhir,
                        'presentasi_akhir' => $presentasiAkhir,
                        'pembimbing_lapangan' => $pembimbingLapangan,
                        'catatan' => $score['catatan'] ?? ($existing->catatan ?? null),
                        'tanggal_penilaian' => now(),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $savedCount++;
            }
        });

        if ($savedCount === 0) {
            return redirect()->back()->with(
                'error',
                'Tidak ada nilai yang tersimpan. ' .
                (count($incompletePeerReviewNims) > 0
                    ? 'Peer review belum lengkap untuk NIM: ' . implode(', ', array_unique($incompletePeerReviewNims))
                    : 'Pastikan data yang diinput valid.')
            );
        }

        if (count($incompletePeerReviewNims) > 0) {
            return redirect()->back()->with(
                'success',
                "Sebagian nilai tersimpan ({$savedCount} mahasiswa). " .
                'Catatan: peer review belum lengkap untuk NIM: ' . implode(', ', array_unique($incompletePeerReviewNims)) . '. Nilai dosen tetap disimpan.'
            );
        }

        return redirect()->back()->with('success', "Penilaian kelompok berhasil disimpan ({$savedCount} mahasiswa).");
    }

    private function coalesceScore($newValue, $existingValue): float
    {
        if ($newValue === '' || $newValue === null) {
            return (float) ($existingValue ?? 0);
        }

        return (float) $newValue;
    }

    /**
     * Ambil dokumen mahasiswa
     */
    private function getDokumenMahasiswa($mahasiswa_id)
    {
        $mahasiswa = User::find($mahasiswa_id);
        
        $dokumen = [];
        
        // Form Konversi (dari database lokal)
        $pendaftar = KknPendaftar::where('user_nim', $mahasiswa->nim)->first();
        if ($pendaftar) {
            $dokumen[] = [
                'jenis' => 'Form Konversi',
                'status' => $pendaftar->status_verifikasi ?? 'pending',
                'file_name' => $pendaftar->file_name ?? '-',
                'upload_date' => $pendaftar->created_at ? $pendaftar->created_at->format('Y-m-d H:i:s') : '-'
            ];
        }
        
        // Dokumen dari dashboard
        $hopeUiTables = ['proposal', 'form_kesediaan', 'laporan_akhir', 'luaran', 'peer_review'];
        
        foreach ($hopeUiTables as $table) {
            $data = DB::connection('dashboard')->table($table)
                ->where('user_nim', $mahasiswa->nim)
                ->first();
            
            if ($data) {
                $dokumen[] = [
                    'jenis' => $this->getJenisDokumen($table),
                    'status' => $data->status ?? 'pending',
                    'file_name' => $data->file_name ?? $data->artikel_file_name ?? '-',
                    'upload_date' => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : '-'
                ];
            }
        }
        
        return $dokumen;
    }

    /**
     * Ambil dokumen kelompok
     */
    private function getDokumenKelompok($group)
    {
        $dokumen = [];
        
        // Ambil NIM ketua kelompok dari data array
        $leader = collect($group['members'])->where('role', 'Ketua')->first();
        if ($leader) {
            $nimKetua = $leader['nim'];
            
            // Form Konversi (dari database lokal)
            $pendaftar = KknPendaftar::where('user_nim', $nimKetua)->first();
            if ($pendaftar) {
                $dokumen[] = [
                    'jenis' => 'Form Konversi',
                    'status' => $pendaftar->status_verifikasi ?? 'pending',
                    'file_name' => $pendaftar->file_name ?? '-',
                    'upload_date' => $pendaftar->created_at ? $pendaftar->created_at->format('Y-m-d H:i:s') : '-'
                ];
            }
            
            // Dokumen dari dashboard
            $hopeUiTables = ['proposal', 'form_kesediaan', 'laporan_akhir', 'luaran', 'peer_review'];
            
            foreach ($hopeUiTables as $table) {
                $data = DB::connection('dashboard')->table($table)
                    ->where('user_nim', $nimKetua)
                    ->first();
                
                if ($data) {
                    $dokumen[] = [
                        'jenis' => $this->getJenisDokumen($table),
                        'status' => $data->status ?? 'pending',
                        'file_name' => $data->file_name ?? $data->artikel_file_name ?? '-',
                        'upload_date' => $data->created_at ? date('Y-m-d H:i:s', strtotime($data->created_at)) : '-'
                    ];
                }
            }
        }
        
        return $dokumen;
    }

    /**
     * Get jenis dokumen berdasarkan table name
     */
    private function getJenisDokumen($table)
    {
        $jenis = [
            'proposal' => 'Proposal Kegiatan',
            'form_kesediaan' => 'Surat Pernyataan Kesediaan Dosen Pembimbing',
            'laporan_akhir' => 'Laporan Akhir',
            'luaran' => 'Luaran',
            'peer_review' => 'Peer Review'
        ];
        
        return $jenis[$table] ?? $table;
    }

    private function getPeerReviewAverageForNim(string $nim): ?float
    {
        $user = User::where('nim', $nim)->first();
        if (!$user) {
            return null;
        }

        $avg = DB::table('peer_review')
            ->where('reviewee_id', $user->id)
            ->whereNotNull('final_score')
            ->avg('final_score');

        return $avg !== null ? round((float) $avg, 2) : null;
    }

    private function isPeerReviewCompleteForNim(string $nim): bool
    {
        $user = User::where('nim', $nim)->first();
        if (!$user) {
            return false;
        }

        $groupId = GroupMember::where('mahasiswa_id', $user->id)
            ->where('status', 'active')
            ->value('group_id');

        if (!$groupId) {
            return false;
        }

        $memberIds = GroupMember::where('group_id', $groupId)
            ->where('status', 'active')
            ->pluck('mahasiswa_id')
            ->values();

        $totalMembers = $memberIds->count();
        if ($totalMembers <= 1) {
            return false;
        }

        $expectedPairs = $totalMembers * ($totalMembers - 1);

        $actualPairs = DB::table('peer_review')
            ->where('group_id', $groupId)
            ->whereIn('reviewer_id', $memberIds)
            ->whereIn('reviewee_id', $memberIds)
            ->whereNotNull('kontribusi_kegiatan')
            ->whereNotNull('tanggung_jawab')
            ->whereNotNull('kerjasama_tim')
            ->whereNotNull('inisiatif_motivasi')
            ->whereNotNull('final_score')
            ->whereColumn('reviewer_id', '!=', 'reviewee_id')
            ->count();

        return $actualPairs >= $expectedPairs;
    }

    public function peerReviewScore(string $mahasiswa_id)
    {
        $score = $this->getPeerReviewAverageForNim($mahasiswa_id);
        $complete = $this->isPeerReviewCompleteForNim($mahasiswa_id);

        return response()->json([
            'peer_review' => $score,
            'peer_review_complete' => $complete,
        ]);
    }

    /**
     * API untuk mendapatkan daftar dosen
     *
     * @OA\Get(
     *     path="/api/dosen/list",
     *     summary="Daftar semua dosen",
     *     description="Mengembalikan daftar seluruh pengguna dengan role dosen.",
     *     tags={"Dosen"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil daftar dosen",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=12),
     *                 @OA\Property(property="name", type="string", example="Dr. Andi"),
     *                 @OA\Property(property="email", type="string", example="andi@kampus.ac.id"),
     *                 @OA\Property(property="nip", type="string", example="198001012005011001")
     *             )
     *         )
     *     )
     * )
     */
    public function getDosenList()
    {
        $dosen = User::where('role', 'dosen')->get();
        
        $result = $dosen->map(function ($dosen) {
            return [
                'id' => $dosen->id,
                'name' => $dosen->name,
                'email' => $dosen->email,
                'nip' => $dosen->nip
            ];
        });
        
        return response()->json($result);
    }

    /**
     * API untuk menerima assignment dari tim penciri
     *
     * @OA\Post(
     *     path="/api/dosen/assign-group",
     *     summary="Menugaskan dosen ke sebuah kelompok",
     *     description="Menerima penugasan dosen pembimbing ke kelompok dari sistem tim penciri.",
     *     tags={"Dosen"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"group_id", "dosen_id"},
     *             @OA\Property(property="group_id", type="integer", example=3, description="ID kelompok yang akan ditugaskan"),
     *             @OA\Property(property="dosen_id", type="integer", example=12, description="ID dosen pembimbing")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assignment berhasil diterima",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Assignment berhasil diterima")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kelompok tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kelompok tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function receiveAssignment(Request $request)
    {
        $request->validate([
            'group_id' => 'required',
            'dosen_id' => 'required'
        ]);
        
        // Update kelompok dengan dosen yang diassign
        $group = Group::find($request->group_id);
        
        if ($group) {
            $group->update([
                'dosen_id' => $request->dosen_id,
                'status' => 'assigned',
                'assigned_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Assignment berhasil diterima']);
        }
        
        return response()->json(['success' => false, 'message' => 'Kelompok tidak ditemukan'], 404);
    }

    public function persetujuanKelompok(Request $httpRequest, GroupProposalReviewService $proposalReviewService)
    {
        $dosen = Auth::user();

        $groups = Group::with([
                'members.mahasiswa',
                'groupLeader',
                'supervisorRequests' => fn ($q) => $q
                    ->where('supervisor_id', $dosen->id)
                    ->with('requester')
                    ->latest(),
            ])
            ->where('dosen_id', $dosen->id)
            ->orderByDesc('created_at')
            ->get();

        $sidebarItems = $groups->map(function (Group $group) use ($proposalReviewService) {
            $supervisorRequest = $group->supervisorRequests->first();
            $statusKey = $this->mapGroupApprovalStatus($group);
            $proposalCard = $proposalReviewService->buildProposalDocumentCardForPersetujuan($group);
            $proposalReview = $proposalReviewService->proposalReviewMeta($group, 'persetujuan');
            $canValidateProposal = in_array($group->status, GroupProposalReviewService::REVIEWABLE_GROUP_STATUSES, true);

            return [
                'group_id' => $group->id,
                'request_id' => $supervisorRequest?->id,
                'nama_kelompok' => $group->nama_kelompok,
                'judul_kegiatan' => $group->judul_kegiatan,
                'nama_mitra' => $group->nama_mitra,
                'lokasi_mitra' => $group->lokasi_mitra,
                'pengaju' => $supervisorRequest?->requester?->name
                    ?? $group->groupLeader?->name
                    ?? '-',
                'status_key' => $statusKey,
                'status_label' => $this->approvalStatusLabel($statusKey),
                'can_approve' => $group->status === 'waiting_supervisor_approval'
                    && $supervisorRequest?->status === 'pending',
                'can_review_proposal' => $canValidateProposal,
                'show_group_approval_actions' => $group->status === 'waiting_supervisor_approval'
                    && $supervisorRequest?->status === 'pending',
                'proposal_card' => $proposalCard,
                'proposal_review' => $proposalReview,
                'members' => $group->members
                    ->where('status', 'active')
                    ->map(fn ($m) => [
                        'name' => $m->mahasiswa->name ?? '-',
                        'nim' => $m->mahasiswa->nim ?? '-',
                        'role' => $m->role === 'leader' ? 'Ketua' : 'Anggota',
                    ])
                    ->values(),
                'group' => $group,
            ];
        })->sortBy(function ($item) {
            return match ($item['status_key']) {
                'menunggu' => 0,
                'diterima' => 1,
                default => 2,
            };
        })->values();

        $stats = [
            'total' => $groups->count(),
            'menunggu' => $sidebarItems->where('status_key', 'menunggu')->count(),
            'diterima' => $sidebarItems->where('status_key', 'diterima')->count(),
            'ditolak' => $sidebarItems->where('status_key', 'ditolak')->count(),
        ];

        $selectedGroupId = (int) $httpRequest->query('group');
        $selectedItem = $sidebarItems->firstWhere('group_id', $selectedGroupId)
            ?? $sidebarItems->first();

        $activeMemberIds = GroupMember::where('status', 'active')->pluck('mahasiswa_id');
        $availableStudents = User::where('role', 'mahasiswa')
            ->whereNotIn('id', $activeMemberIds)
            ->orderBy('name')
            ->get(['id', 'name', 'nim']);

        return view('dosen.persetujuan-kelompok', compact(
            'sidebarItems',
            'selectedItem',
            'stats',
            'availableStudents'
        ));
    }

    public function previewGroupProposal(Group $group)
    {
        $dosen = Auth::user();
        if ((int) $group->dosen_id !== (int) $dosen->id) {
            abort(403);
        }

        $proposal = $this->getGroupProposalDocument($group);
        if (!$proposal || empty($proposal['disk_path'])) {
            abort(404, 'File proposal tidak ditemukan.');
        }

        $disk = \Illuminate\Support\Facades\Storage::disk($proposal['disk']);
        if (!$disk->exists($proposal['disk_path'])) {
            abort(404, 'File proposal tidak ditemukan di storage.');
        }

        return response()->file($disk->path($proposal['disk_path']), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($proposal['file_name'] ?? 'proposal.pdf') . '"',
        ]);
    }

    public function downloadGroupProposal(Group $group)
    {
        $dosen = Auth::user();
        if ((int) $group->dosen_id !== (int) $dosen->id) {
            abort(403);
        }

        $proposal = $this->getGroupProposalDocument($group);
        if (!$proposal || empty($proposal['disk_path'])) {
            abort(404, 'File proposal tidak ditemukan.');
        }

        $disk = \Illuminate\Support\Facades\Storage::disk($proposal['disk']);
        if (!$disk->exists($proposal['disk_path'])) {
            abort(404, 'File proposal tidak ditemukan di storage.');
        }

        return $disk->download(
            $proposal['disk_path'],
            $proposal['file_name'] ?? 'proposal.pdf'
        );
    }

    private function mapGroupApprovalStatus(Group $group): string
    {
        if ($group->status === 'rejected') {
            return 'ditolak';
        }

        if (in_array($group->status, ['active', 'waiting_leader_replacement'], true)) {
            return 'diterima';
        }

        return 'menunggu';
    }

    private function approvalStatusLabel(string $statusKey): string
    {
        return match ($statusKey) {
            'diterima' => 'diterima',
            'ditolak' => 'ditolak',
            default => 'menunggu',
        };
    }

    private function getGroupProposalDocument(Group $group, ?GroupProposalReviewService $proposalReviewService = null): ?array
    {
        $service = $proposalReviewService ?? app(GroupProposalReviewService::class);
        $fileDoc = $service->resolveProposalFileDocument($group);

        if (!($fileDoc['uploaded'] ?? false) || empty($fileDoc['disk_path'])) {
            return null;
        }

        return [
            'file_name' => $fileDoc['file_name'] ?? 'Proposal Kegiatan.pdf',
            'file_size' => $fileDoc['file_size'] ?? null,
            'file_size_label' => $fileDoc['file_size_label'] ?? '-',
            'disk' => $fileDoc['disk'] ?? 'dashboard',
            'disk_path' => $fileDoc['disk_path'],
            'view_url' => route('dosen.persetujuan-kelompok.proposal', $group->id),
            'download_url' => route('dosen.persetujuan-kelompok.proposal-download', $group->id),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    public function approveGroupProposal(
        Request $httpRequest,
        Group $group,
        GroupProposalReviewService $proposalReviewService
    ) {
        return $this->processProposalReview($httpRequest, $group, $proposalReviewService, 'approve', 'persetujuan');
    }

    public function rejectGroupProposal(
        Request $httpRequest,
        Group $group,
        GroupProposalReviewService $proposalReviewService
    ) {
        return $this->processProposalReview($httpRequest, $group, $proposalReviewService, 'reject', 'persetujuan');
    }

    private function processProposalReview(
        Request $httpRequest,
        Group $group,
        GroupProposalReviewService $proposalReviewService,
        string $action,
        string $context
    ) {
        $this->authorizeGroupSupervisor($group);

        if ($action === 'reject') {
            $httpRequest->validate([
                'note' => 'nullable|string|max:1000',
            ]);
        }

        if (!in_array($group->status, GroupProposalReviewService::REVIEWABLE_GROUP_STATUSES, true)) {
            return $this->proposalReviewRedirect($context, $group)
                ->with('error', 'Validasi proposal tidak tersedia pada status kelompok saat ini.');
        }

        $proposal = $this->getGroupProposalDocument($group);
        if (!$proposal) {
            return $this->proposalReviewRedirect($context, $group)
                ->with('error', 'File proposal belum diunggah.');
        }

        $proposalReviewService->reviewProposal(
            $group,
            $action,
            $action === 'reject' ? $httpRequest->input('note') : null,
            (int) Auth::id()
        );

        $group->refresh();

        if ($action === 'approve') {
            $message = $context === 'validasi'
                ? 'Proposal kelompok "' . $group->nama_kelompok . '" berhasil disetujui.'
                : 'Proposal kelompok "' . $group->nama_kelompok . '" berhasil disetujui.';
        } else {
            $message = 'Permintaan revisi proposal telah dikirim. Mahasiswa dapat melihat status di halaman Form Daftar Kelompok.';
        }

        return $this->proposalReviewRedirect($context, $group)->with('success', $message);
    }

    private function proposalReviewRedirect(string $context, Group $group)
    {
        if ($context === 'validasi') {
            return redirect()->route('dosen.validasi-dokumen');
        }

        return redirect()->route('dosen.persetujuan-kelompok', ['group' => $group->id]);
    }

    public function approveSupervisorRequest(SupervisorRequest $request)
    {
        $dosen = Auth::user();
        if ((int) $request->supervisor_id !== (int) $dosen->id) {
            abort(403);
        }

        $group = $request->group;

        $memberIds = GroupMember::where('group_id', $request->group_id)
            ->where('status', 'active')
            ->pluck('mahasiswa_id');

        $conflicts = GroupMember::with('mahasiswa')
            ->whereIn('mahasiswa_id', $memberIds)
            ->where('status', 'active')
            ->where('group_id', '!=', $request->group_id)
            ->get();

        if ($conflicts->isNotEmpty()) {
            $conflictNames = $conflicts
                ->map(fn ($member) => $member->mahasiswa->name ?? 'Unknown')
                ->unique()
                ->implode(', ');

            return redirect()->back()->with(
                'error',
                'Pengajuan tidak bisa disetujui karena ada anggota yang masih aktif di kelompok lain: ' . $conflictNames
            );
        }

        DB::transaction(function () use ($request) {
            $request->update([
                'status' => 'approved',
                'responded_at' => now(),
            ]);

            $request->group->update([
                'status' => 'active',
                'supervisor_approved_at' => now(),
                'catatan' => 'Disetujui dosen pembimbing.',
            ]);
        });

        $group = Group::with(['activeMembers.mahasiswa', 'groupLeader'])->find($request->group_id);
        if (!$group) {
            return redirect()->back()->with('error', 'Kelompok tidak ditemukan saat proses sinkronisasi dashboard.');
        }
        $leaderNim = $group?->groupLeader?->nim;

        $pendaftar = null;
        if (!empty($leaderNim)) {
            $pendaftar = KknPendaftar::where('user_nim', $leaderNim)
                ->where('judul_kegiatan', $group->judul_kegiatan)
                ->latest('id')
                ->first();
        }

        $anggotaData = $group->activeMembers->map(function ($member) {
            return [
                'nama' => $member->mahasiswa->name ?? '-',
                'nim' => $member->mahasiswa->nim ?? '-',
                'program_studi' => $member->mahasiswa->program_studi ?? 'Belum diisi',
                'peran' => $member->role === 'leader' ? 'Ketua' : 'Anggota',
            ];
        })->values()->toArray();

        try {
            $dashboardUrl = \App\Helpers\DashboardHelper::getDashboardUrl();
            $dashboardResponse = Http::timeout(30)->post($dashboardUrl . '/api/kkn/store-from-external', [
                'judul_kegiatan' => $group->judul_kegiatan,
                'mitra' => $group->nama_mitra,
                'lokasi_mitra' => $group->lokasi_mitra,
                'file_path' => $pendaftar->file_path ?? null,
                'file_name' => $pendaftar->file_name ?? null,
                'status' => 'approved',
                'dosen_id' => $group->dosen_id,
                'user_nim' => $leaderNim,
                'anggota' => $anggotaData,
            ]);

            if (!$dashboardResponse->successful()) {
                \Log::error('Gagal kirim data ke dashboard setelah approval dosen', [
                    'group_id' => $group->id,
                    'status' => $dashboardResponse->status(),
                    'body' => $dashboardResponse->body(),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Error kirim data ke dashboard setelah approval dosen: ' . $e->getMessage(), [
                'group_id' => $group?->id,
            ]);
        }

        $agreementSourcePath = 'C:\\Users\\LENOVO\\Downloads\\kesediaan dosen.pdf';
        FormKesediaan::create([
            'judul_kegiatan' => $group->judul_kegiatan,
            'file_name' => basename($agreementSourcePath),
            'file_path' => $agreementSourcePath,
            'user_nim' => $dosen->nip ?? $dosen->nim ?? (string) $dosen->id,
            'status' => 'approved',
            'catatan' => 'Kesediaan dosen tersimpan otomatis saat menyetujui kelompok ID ' . $group->id,
        ]);

        NotificationService::send(
            (int) $request->requested_by,
            'Pengajuan kelompok "' . $group->nama_kelompok . '" telah disetujui dosen pembimbing.',
            route('konversi'),
            'check-circle'
        );

        return redirect()->back()->with('success', 'Pengajuan kelompok berhasil disetujui.');
    }

    public function previewKesediaanPdf()
    {
        $sourcePath = 'C:\\Users\\LENOVO\\Downloads\\kesediaan dosen.pdf';
        if (file_exists($sourcePath)) {
            return response()->file($sourcePath);
        }

        $fallback = public_path('assets/documents/kesediaan-dosen.pdf');
        if (file_exists($fallback)) {
            return response()->file($fallback);
        }

        abort(404, 'File kesediaan dosen belum tersedia.');
    }

    public function rejectSupervisorRequest(Request $httpRequest, SupervisorRequest $request)
    {
        $httpRequest->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $dosen = Auth::user();
        if ((int) $request->supervisor_id !== (int) $dosen->id) {
            abort(403);
        }

        DB::transaction(function () use ($request, $httpRequest) {
            $request->update([
                'status' => 'rejected',
                'responded_at' => now(),
                'note' => $httpRequest->note,
            ]);

            $request->group->update([
                'status' => 'rejected',
                'catatan' => $httpRequest->note ?: 'Pengajuan ditolak dosen pembimbing.',
            ]);
        });

        NotificationService::send(
            (int) $request->requested_by,
            'Pengajuan kelompok "' . $request->group->nama_kelompok . '" ditolak dosen pembimbing.',
            route('konversi'),
            'alert-triangle'
        );

        return redirect()->back()->with('success', 'Pengajuan kelompok berhasil ditolak.');
    }

    public function reAddDroppedMember(Request $request, Group $group)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:users,id',
        ]);

        $dosen = Auth::user();
        if ((int) $group->dosen_id !== (int) $dosen->id) {
            abort(403);
        }

        $activeCount = GroupMember::where('group_id', $group->id)
            ->where('status', 'active')
            ->count();

        if ($activeCount >= 10) {
            return redirect()->back()->with('error', 'Jumlah anggota aktif sudah mencapai batas maksimal 10 orang.');
        }

        $existingActiveMembership = GroupMember::where('mahasiswa_id', $request->mahasiswa_id)
            ->where('status', 'active')
            ->where('group_id', '!=', $group->id)
            ->exists();

        if ($existingActiveMembership) {
            return redirect()->back()->with('error', 'Mahasiswa ini sudah memiliki kelompok aktif lain.');
        }

        $member = GroupMember::where('group_id', $group->id)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->where('status', 'dropped')
            ->first();

        if ($member) {
            $member->update([
                'status' => 'active',
                'dropped_at' => null,
                'drop_reason' => null,
            ]);
        } else {
            $alreadyInGroup = GroupMember::where('group_id', $group->id)
                ->where('mahasiswa_id', $request->mahasiswa_id)
                ->exists();

            if ($alreadyInGroup) {
                return redirect()->back()->with('error', 'Mahasiswa ini sudah terdaftar di kelompok ini.');
            }

            GroupMember::create([
                'group_id' => $group->id,
                'mahasiswa_id' => $request->mahasiswa_id,
                'role' => 'member',
                'status' => 'active',
            ]);
        }

        if ($group->status === 'dropped') {
            $group->update(['status' => 'active']);
        }

        return redirect()->back()->with('success', 'Anggota berhasil ditambahkan ke kelompok.');
    }

    public function assignLeader(Request $request, Group $group)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:users,id',
        ]);

        $dosen = Auth::user();
        if ((int) $group->dosen_id !== (int) $dosen->id) {
            abort(403);
        }

        $candidate = GroupMember::where('group_id', $group->id)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->where('status', 'active')
            ->first();

        if (!$candidate) {
            return redirect()->back()->with('error', 'Calon ketua harus anggota aktif kelompok.');
        }

        DB::transaction(function () use ($group, $candidate) {
            GroupMember::where('group_id', $group->id)
                ->where('role', 'leader')
                ->update(['role' => 'member']);

            $candidate->update(['role' => 'leader']);

            $group->update([
                'leader_id' => $candidate->mahasiswa_id,
                'status' => 'active',
                'catatan' => 'Ketua pengganti telah dipilih dosen pembimbing.',
            ]);
        });

        return redirect()->back()->with('success', 'Ketua pengganti berhasil ditetapkan.');
    }

    public function destroyGroup(Group $group)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        $isAuthorizedDosen = $user->role === 'dosen' && (int) $group->dosen_id === (int) $user->id;

        if (!$isAdmin && !$isAuthorizedDosen) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus kelompok ini.');
        }

        $groupName = $group->nama_kelompok;

        DB::transaction(function () use ($group) {
            SupervisorRequest::where('group_id', $group->id)->delete();
            $group->delete();
        });

        return redirect()->back()->with('success', "Kelompok {$groupName} berhasil dihapus.");
    }

    /**
     * Halaman validasi dokumen luaran kelompok bimbingan.
     */
    public function validasiDokumen(
        GroupDocumentStatusService $documentStatusService,
        GroupProposalReviewService $proposalReviewService
    ) {
        $dosen = Auth::user();

        $groups = Group::with(['groupLeader', 'documentReview'])
            ->where('dosen_id', $dosen->id)
            ->whereIn('status', ['active', 'waiting_leader_replacement'])
            ->orderByDesc('supervisor_approved_at')
            ->get();

        $groupCards = $groups
            ->map(function (Group $group) use ($documentStatusService, $proposalReviewService) {
                $card = $documentStatusService->buildGroupCard($group);
                $card['proposal'] = $proposalReviewService->buildProposalDocumentCard($group);
                $card['subtitle'] = 'Validasi proposal dan luaran kelompok — revisi per dokumen.';

                return $card;
            })
            ->values();

        return view('dosen.validasi-dokumen', compact('groupCards'));
    }

    public function previewValidasiProposal(Group $group)
    {
        $this->authorizeGroupDocumentReview($group);

        return $this->previewGroupProposal($group);
    }

    public function approveValidasiProposal(Request $httpRequest, Group $group, GroupProposalReviewService $proposalReviewService)
    {
        return $this->processProposalReview($httpRequest, $group, $proposalReviewService, 'approve', 'validasi');
    }

    public function rejectValidasiProposal(Request $httpRequest, Group $group, GroupProposalReviewService $proposalReviewService)
    {
        return $this->processProposalReview($httpRequest, $group, $proposalReviewService, 'reject', 'validasi');
    }

    public function approveDocumentItem(
        Request $httpRequest,
        Group $group,
        string $docType,
        GroupDocumentStatusService $documentStatusService
    ) {
        $this->authorizeGroupDocumentReview($group);
        $this->assertValidDocType($docType);

        $card = $documentStatusService->buildGroupCard($group);
        $doc = $card['documents'][$docType] ?? null;

        if (!$doc || !$doc['uploaded']) {
            return redirect()->route('dosen.validasi-dokumen')
                ->with('error', 'Dokumen belum diunggah. Tidak dapat disetujui.');
        }

        if (($doc['review_status'] ?? 'pending') === 'approved') {
            return redirect()->route('dosen.validasi-dokumen')
                ->with('error', 'Dokumen ini sudah disetujui sebelumnya.');
        }

        $documentStatusService->reviewDocument($group, $docType, 'approve', null, (int) Auth::id());

        return redirect()->route('dosen.validasi-dokumen')
            ->with('success', $doc['label'] . ' kelompok "' . $group->nama_kelompok . '" berhasil disetujui.');
    }

    public function rejectDocumentItem(
        Request $httpRequest,
        Group $group,
        string $docType,
        GroupDocumentStatusService $documentStatusService
    ) {
        $this->authorizeGroupDocumentReview($group);
        $this->assertValidDocType($docType);

        $httpRequest->validate([
            'note' => 'nullable|string|max:1000',
        ]);

        $card = $documentStatusService->buildGroupCard($group);
        $doc = $card['documents'][$docType] ?? null;

        if (!$doc || !$doc['uploaded']) {
            return redirect()->route('dosen.validasi-dokumen')
                ->with('error', 'Dokumen belum diunggah. Tidak dapat diminta revisi.');
        }

        $documentStatusService->reviewDocument(
            $group,
            $docType,
            'reject',
            $httpRequest->input('note'),
            (int) Auth::id()
        );

        $group->unsetRelation('documentReview');

        return redirect()->route('dosen.validasi-dokumen')
            ->with('success', 'Permintaan revisi untuk ' . ($doc['label'] ?? 'dokumen') . ' telah dikirim. Mahasiswa hanya perlu mengunggah ulang dokumen tersebut.');
    }

    public function deleteDocumentItem(
        Request $httpRequest,
        Group $group,
        string $docType,
        GroupDocumentStatusService $documentStatusService
    ) {
        $this->authorizeGroupDocumentReview($group);
        $this->assertValidDocType($docType);

        $card = $documentStatusService->buildGroupCard($group);
        $doc = $card['documents'][$docType] ?? null;

        if (!$doc || !$doc['uploaded']) {
            return redirect()->route('dosen.validasi-dokumen')
                ->with('error', 'Tidak ada dokumen yang dapat dihapus.');
        }

        try {
            $documentStatusService->deleteDocument(
                $group,
                $docType,
                null,
                (int) Auth::id()
            );
        } catch (\RuntimeException $e) {
            return redirect()->route('dosen.validasi-dokumen')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('dosen.validasi-dokumen')
            ->with('success', ($doc['label'] ?? 'Dokumen') . ' berhasil dihapus. Mahasiswa dapat mengunggah ulang dokumen yang benar.');
    }

    private function assertValidDocType(string $docType): void
    {
        if (!in_array($docType, GroupDocumentStatusService::DOC_KEYS, true)) {
            abort(404);
        }
    }

    public function downloadGroupLaporanAkhir(Group $group)
    {
        $this->authorizeGroupDocumentReview($group);
        $row = $this->fetchDashboardDocument('laporan_akhir', $group->groupLeader?->nim, $group);

        return $this->serveDashboardFileRow($row, 'Laporan akhir tidak ditemukan.');
    }

    public function downloadGroupArtikel(Group $group)
    {
        $this->authorizeGroupDocumentReview($group);
        $row = $this->fetchDashboardDocument('luaran', $group->groupLeader?->nim, $group);
        if (!$row) {
            abort(404, 'Artikel publikasi tidak ditemukan.');
        }
        $filePath = $row->artikel_file_path ?? $row->file_path ?? null;
        $fileName = $row->artikel_file_name ?? $row->file_name ?? 'Artikel.pdf';

        if (!$filePath || !Storage::disk('dashboard')->exists($filePath)) {
            abort(404, 'Artikel publikasi tidak ditemukan.');
        }

        $disk = Storage::disk('dashboard');
        if (request()->boolean('download')) {
            return $disk->download($filePath, $fileName);
        }

        return response()->file($disk->path($filePath), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    }

    private function authorizeGroupDocumentReview(Group $group): void
    {
        $this->authorizeGroupSupervisor($group);
    }

    private function authorizeGroupSupervisor(Group $group): void
    {
        $dosen = Auth::user();
        if ((int) $group->dosen_id !== (int) $dosen->id) {
            abort(403);
        }
    }

    private function fetchDashboardDocument(string $table, ?string $leaderNim, Group $group): ?object
    {
        if (!$leaderNim) {
            return null;
        }

        try {
            $row = DB::connection('dashboard')->table($table)
                ->where('user_nim', $leaderNim)
                ->orderByDesc('id')
                ->first();

            if (!$row && $group->judul_kegiatan) {
                $row = DB::connection('dashboard')->table($table)
                    ->where('judul_kegiatan', $group->judul_kegiatan)
                    ->orderByDesc('id')
                    ->first();
            }

            return $row;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function serveDashboardFileRow(?object $row, string $notFoundMessage)
    {
        $filePath = $row->file_path ?? null;
        $fileName = $row->file_name ?? 'dokumen.pdf';

        if (!$filePath || !Storage::disk('dashboard')->exists($filePath)) {
            abort(404, $notFoundMessage);
        }

        $disk = Storage::disk('dashboard');
        if (request()->boolean('download')) {
            return $disk->download($filePath, $fileName);
        }

        return response()->file($disk->path($filePath), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    }
}


