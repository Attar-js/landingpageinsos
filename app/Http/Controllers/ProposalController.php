<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use App\Services\GroupProposalReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalController extends Controller
{
    public function index(GroupProposalReviewService $proposalReviewService)
    {
        $proposalStatus = $proposalReviewService->forStudent((int) Auth::id());

        return view('pages.proposalkegiatan', compact('proposalStatus'));
    }

    public function store(Request $request, GroupProposalReviewService $proposalReviewService)
    {
        $proposalStatus = $proposalReviewService->forStudent((int) Auth::id());

        if ($proposalStatus && !($proposalStatus['can_reupload'] ?? false)) {
            $blocked = in_array($proposalStatus['ui_status'] ?? '', ['menunggu', 'disetujui'], true);
            if ($blocked) {
                return redirect()->back()
                    ->with('error', 'Upload proposal tidak tersedia pada status saat ini.');
            }
        }

        $request->validate([
            'judul_kegiatan' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240',
            'user_nim' => 'required|string',
        ]);

        try {
            $proposalReviewService->storeProposalFile(
                $request->judul_kegiatan,
                $request->user_nim,
                $request->file('file')
            );

            $member = GroupMember::with('group')
                ->where('mahasiswa_id', Auth::id())
                ->where('status', 'active')
                ->where('role', 'leader')
                ->first();

            if ($member?->group && in_array($member->group->status, GroupProposalReviewService::REVIEWABLE_GROUP_STATUSES, true)) {
                $proposalReviewService->markPendingAfterReupload($member->group);
            }

            $message = ($proposalStatus['ui_status'] ?? '') === 'revisi'
                ? 'Proposal revisi berhasil diunggah. Menunggu persetujuan dosen pembimbing.'
                : 'Proposal berhasil diunggah!';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
