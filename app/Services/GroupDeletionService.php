<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupCpmkRubric;
use App\Models\GroupDocumentReview;
use App\Models\GroupMember;
use App\Models\KknPendaftar;
use App\Models\SupervisorRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class GroupDeletionService
{
    private const DASHBOARD_TABLES = [
        'proposal',
        'form_kesediaan',
        'laporan_akhir',
        'luaran',
        'peer_review',
    ];

    public function deleteGroup(Group $group): void
    {
        DB::transaction(function () use ($group) {
            $group->loadMissing(['members.mahasiswa', 'groupLeader']);

            $leaderNim = $this->resolveLeaderNim($group);
            $judulKegiatan = trim((string) $group->judul_kegiatan);

            $this->deleteLocalPeerReviews($group);
            $this->deleteRegistrationRecord($leaderNim);
            $this->deleteDashboardRecords($leaderNim, $judulKegiatan);
            $this->deleteProposalFile($group);

            SupervisorRequest::where('group_id', $group->id)->delete();
            GroupMember::where('group_id', $group->id)->delete();
            GroupCpmkRubric::where('group_id', $group->id)->delete();
            GroupDocumentReview::where('group_id', $group->id)->delete();

            if (!$group->delete()) {
                throw new \RuntimeException('Gagal menghapus data kelompok dari database.');
            }
        });
    }

    private function resolveLeaderNim(Group $group): ?string
    {
        $leaderNim = $group->groupLeader?->nim
            ?? $group->members->firstWhere('role', 'leader')?->mahasiswa?->nim;

        return filled($leaderNim) ? trim((string) $leaderNim) : null;
    }

    private function deleteLocalPeerReviews(Group $group): void
    {
        if (!Schema::hasTable('peer_review') || !Schema::hasColumn('peer_review', 'group_id')) {
            return;
        }

        DB::table('peer_review')->where('group_id', $group->id)->delete();
    }

    private function deleteRegistrationRecord(?string $leaderNim): void
    {
        if (!filled($leaderNim)) {
            return;
        }

        $pendaftar = KknPendaftar::where('user_nim', $leaderNim)->first();
        if (!$pendaftar) {
            return;
        }

        $pendaftar->anggota()->delete();
        $pendaftar->delete();
    }

    private function deleteDashboardRecords(?string $leaderNim, string $judulKegiatan): void
    {
        if (!filled($leaderNim)) {
            return;
        }

        foreach (self::DASHBOARD_TABLES as $table) {
            try {
                $query = DB::connection('dashboard')->table($table)
                    ->where('user_nim', $leaderNim);

                if ($judulKegiatan !== '' && Schema::connection('dashboard')->hasColumn($table, 'judul_kegiatan')) {
                    $query->where('judul_kegiatan', $judulKegiatan);
                }

                $query->delete();
            } catch (\Throwable $e) {
                // Dashboard DB mungkin tidak tersedia di environment tertentu.
            }
        }
    }

    private function deleteProposalFile(Group $group): void
    {
        $proposalReviewService = app(GroupProposalReviewService::class);
        $proposal = $proposalReviewService->resolveProposalFileDocument($group);

        if (!($proposal['uploaded'] ?? false) || empty($proposal['disk_path'])) {
            return;
        }

        $disk = Storage::disk($proposal['disk'] ?? 'dashboard');
        if ($disk->exists($proposal['disk_path'])) {
            $disk->delete($proposal['disk_path']);
        }
    }
}
