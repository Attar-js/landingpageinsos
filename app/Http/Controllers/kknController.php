<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KknPendaftar;
use App\Models\KknAnggota;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\SupervisorRequest;
use App\Models\GroupCpmkRubric;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Helpers\DashboardHelper;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\GroupProposalReviewService;

class KknController extends Controller
{
    /**
     * Menampilkan form pendaftaran KKN
     */
    public function showForm(GroupProposalReviewService $proposalReviewService)
    {
        $user = Auth::user();
        $activeMemberIds = GroupMember::where('status', 'active')->pluck('mahasiswa_id');

        $mahasiswaList = User::where('role', 'mahasiswa')
            ->whereNotIn('id', $activeMemberIds)
            ->orderBy('name')
            ->get(['id', 'name', 'nim']);
        $dosenList = User::where('role', 'dosen')->orderBy('name')->get(['id', 'name', 'nip', 'email']);

        $myGroupMember = GroupMember::with([
                'group.dosen',
                'group.groupLeader',
                'group.members.mahasiswa',
                'group.cpmkRubric',
            ])
            ->where('mahasiswa_id', $user->id)
            ->where('status', 'active')
            ->first();

        $myGroup = $myGroupMember?->group;
        $canFillCpmkRubric = $myGroup
            && $myGroup->isSupervisorApproved()
            && $myGroupMember->role === 'leader';

        $proposalStatus = null;
        if ($myGroup && in_array($myGroup->status, GroupProposalReviewService::REVIEWABLE_GROUP_STATUSES, true)) {
            $proposalStatus = $proposalReviewService->forGroupView($myGroup, $myGroupMember);
        }

        return view('pages.formkonversi', [
            'mahasiswaList' => $mahasiswaList,
            'dosenList' => $dosenList,
            'myGroup' => $myGroup,
            'myGroupMember' => $myGroupMember,
            'cpmkRubric' => $myGroup?->cpmkRubric,
            'canFillCpmkRubric' => $canFillCpmkRubric,
            'proposalStatus' => $proposalStatus,
        ]);
    }

    public function reuploadProposal(
        Request $request,
        Group $group,
        GroupProposalReviewService $proposalReviewService
    ) {
        $user = Auth::user();
        $member = GroupMember::where('group_id', $group->id)
            ->where('mahasiswa_id', $user->id)
            ->where('status', 'active')
            ->where('role', 'leader')
            ->first();

        if (!$member) {
            return redirect()->route('konversi')
                ->with('error', 'Hanya ketua kelompok yang dapat mengunggah ulang proposal.');
        }

        $proposalStatus = $proposalReviewService->forGroupView($group, $member);
        if (!in_array($group->status, GroupProposalReviewService::REVIEWABLE_GROUP_STATUSES, true)) {
            return redirect()->route('konversi')
                ->with('error', 'Upload ulang proposal tidak tersedia pada status kelompok saat ini.');
        }

        if (!($proposalStatus['can_reupload'] ?? false)) {
            return redirect()->route('konversi')
                ->with('error', 'Upload ulang proposal tidak tersedia pada status saat ini.');
        }

        $request->validate([
            'proposal_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $proposalReviewService->storeProposalFile(
                $group->judul_kegiatan,
                $user->nim ?? $request->user_nim,
                $request->file('proposal_file')
            );
            $proposalReviewService->markPendingAfterReupload($group);

            $wasInitialUpload = ($proposalStatus['ui_status'] ?? '') === 'belum_upload';

            return redirect()->route('konversi')
                ->with('success', $wasInitialUpload
                    ? 'Proposal berhasil diunggah. Menunggu validasi dosen pembimbing.'
                    : 'Proposal revisi berhasil diunggah. Menunggu persetujuan dosen pembimbing.');
        } catch (\Exception $e) {
            return redirect()->route('konversi')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan data pendaftaran KKN
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $userNim = $user->nim ?? '10221051';

        $request->validate([
            'judul_kegiatan' => 'required|string|max:255',
            'mitra' => 'required|string|max:100',
            'lokasi_mitra' => 'required|string|max:255',
            'nama_kelompok' => 'required|string|max:100',
            'dosen_id' => 'required|exists:users,id',
            'member_ids' => 'required|array|min:2|max:10',
            'member_ids.*' => 'required|exists:users,id',
            'user_nim' => 'required|string|max:20',
            'proposal_file' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'judul_kegiatan.required' => 'Judul kegiatan harus diisi',
            'mitra.required' => 'Nama mitra harus diisi',
            'lokasi_mitra.required' => 'Lokasi mitra harus diisi',
            'nama_kelompok.required' => 'Nama kelompok harus diisi',
            'dosen_id.required' => 'Dosen pembimbing harus dipilih',
            'member_ids.required' => 'Anggota kelompok harus dipilih',
            'member_ids.min' => 'Minimal 2 mahasiswa dalam satu kelompok',
            'member_ids.max' => 'Maksimal 10 mahasiswa dalam satu kelompok',
            'user_nim.required' => 'NIM user input harus diisi',
            'proposal_file.mimes' => 'File proposal harus berformat PDF',
            'proposal_file.max' => 'Ukuran file proposal maksimal 10MB',
        ]);

        try {
            $memberIds = collect($request->member_ids)->map(fn ($id) => (int) $id)->unique()->values();

            if (!$memberIds->contains($user->id)) {
                return redirect()->back()->with('error', 'Ketua kelompok harus menjadi bagian dari anggota.')->withInput();
            }

            $selectedDosen = User::where('id', $request->dosen_id)->where('role', 'dosen')->first();
            if (!$selectedDosen) {
                return redirect()->back()->with('error', 'Dosen pembimbing yang dipilih tidak valid.')->withInput();
            }

            $existingMembership = GroupMember::whereIn('mahasiswa_id', $memberIds)
                ->where('status', 'active')
                ->with('mahasiswa')
                ->first();

            if ($existingMembership) {
                return redirect()->back()->with('error', 'Terdapat anggota yang sudah berada di kelompok lain: ' . $existingMembership->mahasiswa->name)->withInput();
            }

            $pendaftar = KknPendaftar::create([
                'judul_kegiatan' => trim($request->judul_kegiatan),
                'mitra' => trim($request->mitra),
                'lokasi_mitra' => trim($request->lokasi_mitra),
                'file_path' => null,
                'file_name' => null,
                'status' => 'pending',
                'user_nim' => trim($request->user_nim) ?: $userNim
            ]);

            if ($request->hasFile('proposal_file')) {
                $proposalFile = $request->file('proposal_file');
                $proposalFileName = time() . '_proposal_' . $proposalFile->getClientOriginalName();
                $proposalResponse = Http::timeout(30)
                    ->attach('file', file_get_contents($proposalFile), $proposalFileName, [
                        'Content-Type' => $proposalFile->getMimeType(),
                    ])
                    ->post(DashboardHelper::getApiUrl('proposal/store-from-external'), [
                        'judul_kegiatan' => trim($request->judul_kegiatan),
                        'user_nim' => trim($request->user_nim) ?: $userNim,
                        'status' => 'pending',
                    ]);

                $proposalErrors = $proposalResponse->json('errors', []);
                $needsLegacyProposalPayload = $proposalResponse->status() === 422
                    && is_array($proposalErrors)
                    && array_key_exists('file_content', $proposalErrors)
                    && array_key_exists('file_name', $proposalErrors)
                    && array_key_exists('file_mime_type', $proposalErrors)
                    && array_key_exists('file_size', $proposalErrors);

                if ($needsLegacyProposalPayload) {
                    $proposalContent = file_get_contents($proposalFile);
                    $proposalResponse = Http::timeout(30)->post(
                        DashboardHelper::getApiUrl('proposal/store'),
                        [
                            'judul_kegiatan' => trim($request->judul_kegiatan),
                            'user_nim' => trim($request->user_nim) ?: $userNim,
                            'file_content' => base64_encode($proposalContent),
                            'file_name' => $proposalFileName,
                            'file_mime_type' => $proposalFile->getMimeType(),
                            'file_size' => $proposalFile->getSize(),
                        ]
                    );
                }

                if (!$proposalResponse->successful()) {
                    return redirect()->back()->with('error', 'Gagal mengunggah file proposal: ' . $proposalResponse->body())->withInput();
                }
            }

            $selectedMembers = User::whereIn('id', $memberIds)->where('role', 'mahasiswa')->get();

            $createdGroup = null;

            DB::transaction(function () use ($request, $user, $memberIds, $selectedMembers, $selectedDosen, $pendaftar, &$createdGroup) {
                $group = Group::create([
                    'nama_kelompok' => trim($request->nama_kelompok),
                    'judul_kegiatan' => trim($request->judul_kegiatan),
                    'lokasi_kkn' => trim($request->lokasi_mitra),
                    'nama_mitra' => trim($request->mitra),
                    'lokasi_mitra' => trim($request->lokasi_mitra),
                    'dosen_id' => $selectedDosen->id,
                    'leader_id' => $user->id,
                    'status' => 'waiting_supervisor_approval',
                    'catatan' => 'Menunggu persetujuan dosen pembimbing.',
                ]);

                foreach ($memberIds as $memberId) {
                    GroupMember::create([
                        'group_id' => $group->id,
                        'mahasiswa_id' => $memberId,
                        'role' => $memberId === $user->id ? 'leader' : 'member',
                        'status' => 'active',
                    ]);
                }

                SupervisorRequest::create([
                    'group_id' => $group->id,
                    'supervisor_id' => $selectedDosen->id,
                    'requested_by' => $user->id,
                    'status' => 'pending',
                    'note' => 'Pengajuan dosen pembimbing baru dari pendaftaran kelompok.',
                ]);

                $createdGroup = $group;

                foreach ($selectedMembers as $member) {
                    KknAnggota::create([
                        'kkn_pendaftar_id' => $pendaftar->id,
                        'nama' => $member->name,
                        'nim' => $member->nim ?? '-',
                        'program_studi' => 'Belum diisi',
                        'peran' => $member->id === $user->id ? 'Ketua' : 'Anggota',
                    ]);
                }
            });

            if ($createdGroup) {
                NotificationService::send(
                    (int) $selectedDosen->id,
                    $user->name . ' mengajukan persetujuan kelompok "' . $createdGroup->nama_kelompok . '".',
                    route('dosen.persetujuan-kelompok', ['group' => $createdGroup->id]),
                    'clock'
                );
            }

            $successMessage = $request->hasFile('proposal_file')
                ? 'Pendaftaran kelompok berhasil disimpan dan menunggu persetujuan dosen pembimbing.'
                : 'Pendaftaran kelompok berhasil disimpan. Proposal dapat diunggah setelah kelompok disetujui dosen pembimbing.';

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error('Error in KknController store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan status pendaftaran
     */
    public function status()
    {
        $pendaftar = KknPendaftar::with('anggota')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.status-pendaftaran', compact('pendaftar'));
    }

    /**
     * Menampilkan detail pendaftaran
     */
    public function detail($id)
    {
        $pendaftar = KknPendaftar::with('anggota')->findOrFail($id);
        
        return view('pages.detail-pendaftaran', compact('pendaftar'));
    }

    public function dropSelf(Request $request, Group $group)
    {
        $request->validate([
            'drop_reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        $member = GroupMember::where('group_id', $group->id)
            ->where('mahasiswa_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$member) {
            return redirect()->back()->with('error', 'Anda bukan anggota aktif kelompok ini.');
        }

        DB::transaction(function () use ($group, $member, $request, $user) {
            $member->update([
                'status' => 'dropped',
                'dropped_at' => now(),
                'drop_reason' => trim($request->drop_reason),
            ]);

            if ($member->role === 'leader') {
                $member->update(['role' => 'member']);

                $remainingActiveMembers = GroupMember::where('group_id', $group->id)
                    ->where('status', 'active')
                    ->where('mahasiswa_id', '!=', $user->id)
                    ->count();

                $group->update([
                    'leader_id' => null,
                    'status' => $remainingActiveMembers > 0 ? 'waiting_leader_replacement' : 'dropped',
                    'catatan' => $remainingActiveMembers > 0
                        ? 'Ketua kelompok mengundurkan diri. Menunggu dosen memilih ketua pengganti.'
                        : 'Kelompok tidak memiliki anggota aktif setelah ketua mengundurkan diri.',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Anda berhasil keluar dari kelompok.');
    }

    /**
     * Simpan deskripsi kegiatan rubrik CPMK (ketua kelompok).
     */
    public function storeCpmkRubrik(Request $request, Group $group)
    {
        $user = Auth::user();

        $member = GroupMember::where('group_id', $group->id)
            ->where('mahasiswa_id', $user->id)
            ->where('status', 'active')
            ->where('role', 'leader')
            ->first();

        if (!$member) {
            return redirect()->back()
                ->with('error', 'Hanya ketua kelompok yang dapat mengisi deskripsi kegiatan rubrik CPMK.');
        }

        if (!$group->isSupervisorApproved()) {
            return redirect()->back()
                ->with('error', 'Deskripsi rubrik hanya dapat diisi setelah kelompok disetujui dosen pembimbing.');
        }

        $validated = $request->validate([
            'deskripsi_p5' => 'required|string|max:5000',
            'deskripsi_c3' => 'required|string|max:5000',
            'deskripsi_a2' => 'required|string|max:5000',
        ]);

        $existing = GroupCpmkRubric::where('group_id', $group->id)->first();

        GroupCpmkRubric::updateOrCreate(
            ['group_id' => $group->id],
            [
                'deskripsi_p5' => $validated['deskripsi_p5'],
                'deskripsi_c3' => $validated['deskripsi_c3'],
                'deskripsi_a2' => $validated['deskripsi_a2'],
                'filled_by' => $user->id,
                'skor_p5' => $existing?->skor_p5,
                'skor_c3' => $existing?->skor_c3,
                'skor_a2' => $existing?->skor_a2,
                'catatan' => $existing?->catatan,
                'skor_filled_by' => $existing?->skor_filled_by,
            ]
        );

        return redirect()->route('konversi')
            ->with('success', 'Deskripsi kegiatan rubrik CPMK berhasil disimpan. Skor akan diisi oleh Tim MK Penciri.');
    }
} 

