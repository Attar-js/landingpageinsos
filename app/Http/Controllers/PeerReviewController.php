<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeerReview;
use App\Models\GroupMember;
use App\Models\Penilaian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeerReviewController extends Controller
{
    public function showForm()
    {
        [$isOpen, $windowMessage] = $this->getPeerReviewWindowStatus();
        $user = Auth::user();
        $activeMembership = GroupMember::with(['group', 'group.members.mahasiswa'])
            ->where('mahasiswa_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$activeMembership || !$activeMembership->group || $activeMembership->group->status !== 'active') {
            return view('peer-review', [
                'reviewTargets' => collect(),
                'myGroup' => null,
                'existingReviews' => [],
                'isPeerReviewOpen' => $isOpen,
                'windowMessage' => $windowMessage,
            ])->with('error', 'Peer review hanya tersedia untuk anggota kelompok yang sudah aktif.');
        }

        $group = $activeMembership->group;
        // Satu baris per mahasiswa (hindari duplikat jika ada lebih dari satu record group_members aktif).
        $reviewTargets = $group->members
            ->where('status', 'active')
            ->where('mahasiswa_id', '!=', $user->id)
            ->unique('mahasiswa_id')
            ->values();

        $existingReviews = PeerReview::where('group_id', $group->id)
            ->where('reviewer_id', $user->id)
            ->get()
            ->keyBy('reviewee_id');

        $reviewedCount = $existingReviews
            ->filter(function ($review) {
                return $review->kontribusi_kegiatan !== null
                    && $review->tanggung_jawab !== null
                    && $review->kerjasama_tim !== null
                    && $review->inisiatif_motivasi !== null;
            })
            ->count();
        $totalTargets = $reviewTargets->count();
        $progressPercent = $totalTargets > 0 ? (int) round(($reviewedCount / $totalTargets) * 100) : 0;
        $isPeerReviewOpen = $isOpen;

        return view('peer-review', compact(
            'reviewTargets',
            'group',
            'existingReviews',
            'reviewedCount',
            'totalTargets',
            'progressPercent',
            'isPeerReviewOpen',
            'windowMessage'
        ));
    }

    public function store(Request $request)
    {
        [$isOpen, $windowMessage] = $this->getPeerReviewWindowStatus();
        if (!$isOpen) {
            return redirect()->back()->with('error', $windowMessage ?: 'Periode peer review sedang ditutup.');
        }

        $user = Auth::user();
        $membership = GroupMember::with('group')
            ->where('mahasiswa_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$membership || !$membership->group || $membership->group->status !== 'active') {
            return redirect()->back()->with('error', 'Anda belum berada pada kelompok aktif untuk melakukan peer review.');
        }

        $group = $membership->group;
        $validRevieweeIds = GroupMember::where('group_id', $group->id)
            ->where('status', 'active')
            ->where('mahasiswa_id', '!=', $user->id)
            ->pluck('mahasiswa_id')
            ->unique()
            ->values()
            ->all();

        if (empty($validRevieweeIds)) {
            return redirect()->back()->with('error', 'Tidak ada anggota lain untuk dinilai.');
        }

        $rules = [
            'reviews' => 'required|array',
        ];

        foreach ($validRevieweeIds as $revieweeId) {
            $rules["reviews.$revieweeId.kontribusi_kegiatan"] = 'required|numeric|min:0|max:100';
            $rules["reviews.$revieweeId.tanggung_jawab"] = 'required|numeric|min:0|max:100';
            $rules["reviews.$revieweeId.kerjasama_tim"] = 'required|numeric|min:0|max:100';
            $rules["reviews.$revieweeId.inisiatif_motivasi"] = 'required|numeric|min:0|max:100';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $group, $user, $validRevieweeIds) {
            foreach ($validRevieweeIds as $revieweeId) {
                $row = $validated['reviews'][$revieweeId];
                $finalScore = (
                    (float) $row['kontribusi_kegiatan'] +
                    (float) $row['tanggung_jawab'] +
                    (float) $row['kerjasama_tim'] +
                    (float) $row['inisiatif_motivasi']
                ) / 4;

                PeerReview::updateOrCreate(
                    [
                        'group_id' => $group->id,
                        'reviewer_id' => $user->id,
                        'reviewee_id' => (int) $revieweeId,
                    ],
                    [
                        'kontribusi_kegiatan' => $row['kontribusi_kegiatan'],
                        'tanggung_jawab' => $row['tanggung_jawab'],
                        'kerjasama_tim' => $row['kerjasama_tim'],
                        'inisiatif_motivasi' => $row['inisiatif_motivasi'],
                        'final_score' => round($finalScore, 2),
                        // Backward compatibility with legacy non-null columns.
                        'judul_kegiatan' => $group->judul_kegiatan ?? 'Peer Review Kelompok',
                        'file_name' => 'peer-review-table',
                        'file_path' => null,
                        'user_nim' => $user->nim ?? (string) $user->id,
                        'status' => 'pending',
                        'catatan' => null,
                        'submitted_at' => now(),
                    ]
                );
            }

            $this->syncPeerReviewToPenilaian($group->id, (int) ($group->dosen_id ?? 0), $validRevieweeIds);
        });

        return redirect()->back()->with('success', 'Peer review berhasil disimpan.');
    }

    private function syncPeerReviewToPenilaian(int $groupId, int $dosenId, array $revieweeIds): void
    {
        $rows = PeerReview::selectRaw('reviewee_id, AVG(final_score) as avg_peer')
            ->where('group_id', $groupId)
            ->whereIn('reviewee_id', $revieweeIds)
            ->groupBy('reviewee_id')
            ->get();

        foreach ($rows as $row) {
            $reviewee = DB::table('users')->select('nim')->where('id', $row->reviewee_id)->first();
            if (!$reviewee || empty($reviewee->nim)) {
                continue;
            }

            $existing = DB::table('penilaian')
                ->where('mahasiswa_nim', $reviewee->nim)
                ->when($dosenId > 0, fn ($q) => $q->where('dosen_id', $dosenId))
                ->first();

            if ($existing) {
                DB::table('penilaian')
                    ->where('id', $existing->id)
                    ->update([
                        'peer_review' => round((float) $row->avg_peer, 2),
                        'updated_at' => now(),
                    ]);
                continue;
            }

            if ($dosenId > 0) {
                DB::table('penilaian')->insert([
                    'mahasiswa_nim' => $reviewee->nim,
                    'dosen_id' => $dosenId,
                    'peer_review' => round((float) $row->avg_peer, 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function status()
    {
        return redirect()->route('peer-review.upload');
    }

    private function getPeerReviewWindowStatus(): array
    {
        $now = now();
        $startAt = env('PEER_REVIEW_START_AT');
        $endAt = env('PEER_REVIEW_END_AT');

        $start = $startAt ? Carbon::parse($startAt) : null;
        $end = $endAt ? Carbon::parse($endAt) : null;

        if ($start && $now->lt($start)) {
            return [false, 'Periode peer review belum dibuka. Mulai: ' . $start->format('d-m-Y H:i')];
        }

        if ($end && $now->gt($end)) {
            return [false, 'Periode peer review sudah ditutup. Deadline: ' . $end->format('d-m-Y H:i')];
        }

        if ($start && $end) {
            return [true, 'Periode peer review aktif sampai ' . $end->format('d-m-Y H:i')];
        }

        return [true, 'Periode peer review aktif.'];
    }
}


