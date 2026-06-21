<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;

class GroupProposalReviewService
{
    public const REVIEWABLE_GROUP_STATUSES = [
        'waiting_supervisor_approval',
        'active',
        'waiting_leader_replacement',
    ];

    public function forStudent(int $mahasiswaId): ?array
    {
        $member = GroupMember::with(['group.dosen', 'group.groupLeader'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        if (!$member?->group) {
            return null;
        }

        return $this->forGroupView($member->group, $member);
    }

    public function forGroup(Group $group): array
    {
        return $this->forGroupView($group, null);
    }

    public function forGroupView(Group $group, ?GroupMember $member = null): array
    {
        $group->loadMissing(['groupLeader', 'dosen']);
        $proposal = $this->fetchProposalMeta($group);
        $uploaded = (bool) ($proposal['uploaded'] ?? false);
        $status = $group->proposal_review_status ?? 'pending';
        $note = $group->proposal_review_note;

        if (!$uploaded && $status === 'approved') {
            $status = 'pending';
        }

        $isApproved = $uploaded && $status === 'approved';
        $needsRevision = $status === 'rejected' || (!$uploaded && filled($note));
        $isLeader = $member?->role === 'leader';
        $canUploadProposal = $isLeader
            && in_array($group->status, self::REVIEWABLE_GROUP_STATUSES, true)
            && !$uploaded
            && !$needsRevision;
        $canReuploadRevision = $isLeader
            && in_array($group->status, self::REVIEWABLE_GROUP_STATUSES, true)
            && $needsRevision;
        $canReupload = $canUploadProposal || $canReuploadRevision;

        if ($isApproved) {
            $label = 'Proposal disetujui dosen';
            $badge = 'success';
            $uiStatus = 'disetujui';
        } elseif ($needsRevision) {
            $label = 'Perlu revisi proposal';
            $badge = 'danger';
            $uiStatus = 'revisi';
        } elseif ($uploaded) {
            $label = 'Menunggu persetujuan dosen';
            $badge = 'warning';
            $uiStatus = 'menunggu';
        } else {
            $label = 'Belum mengunggah proposal';
            $badge = 'secondary';
            $uiStatus = 'belum_upload';
        }

        return [
            'group_id' => $group->id,
            'group_name' => $group->nama_kelompok,
            'dosen_name' => $group->dosen?->name ?? '-',
            'uploaded' => $uploaded,
            'ui_status' => $uiStatus,
            'status_label' => $label,
            'badge_class' => $badge,
            'review_status' => $status,
            'review_note' => $note,
            'needs_revision' => $needsRevision,
            'is_approved' => $isApproved,
            'is_leader' => $isLeader,
            'can_reupload' => $canReupload,
            'can_upload_initial' => $canUploadProposal,
            'proposal' => $proposal,
            'reviewed_at' => $group->proposal_reviewed_at,
        ];
    }

    public function reviewProposal(Group $group, string $action, ?string $note, int $reviewerId): void
    {
        $group->update([
            'proposal_review_status' => $action === 'approve' ? 'approved' : 'rejected',
            'proposal_review_note' => $action === 'reject' ? $note : null,
            'proposal_reviewed_by' => $reviewerId,
            'proposal_reviewed_at' => now(),
        ]);

        $group->loadMissing('dosen');
        $dosenName = $group->dosen?->name ?? NotificationService::actorName($reviewerId);

        if ($action === 'approve') {
            NotificationService::notifyGroupMembers(
                $group,
                $dosenName . ' menyetujui proposal kelompok "' . $group->nama_kelompok . '".',
                route('konversi'),
                'check-circle'
            );
        } else {
            $noteSuffix = filled($note) ? ' Catatan: ' . $note : '';
            NotificationService::notifyGroupMembers(
                $group,
                $dosenName . ' meminta revisi proposal kelompok "' . $group->nama_kelompok . '".' . $noteSuffix,
                route('konversi'),
                'alert-triangle'
            );
        }
    }

    public function markPendingAfterReupload(Group $group): void
    {
        $group->update([
            'proposal_review_status' => 'pending',
            'proposal_review_note' => null,
        ]);
    }

    public function storeProposalFile(string $judulKegiatan, string $userNim, $file): void
    {
        $fileName = time() . '_proposal_' . $file->getClientOriginalName();

        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->attach('file', file_get_contents($file), $fileName, [
                'Content-Type' => $file->getMimeType(),
            ])
            ->post(\App\Helpers\DashboardHelper::getApiUrl('proposal/store-from-external'), [
                'judul_kegiatan' => $judulKegiatan,
                'user_nim' => $userNim,
                'status' => 'pending',
            ]);

        $errors = $response->json('errors', []);
        $needsLegacyPayload = $response->status() === 422
            && is_array($errors)
            && array_key_exists('file_content', $errors);

        if ($needsLegacyPayload) {
            $response = \Illuminate\Support\Facades\Http::timeout(30)->post(
                \App\Helpers\DashboardHelper::getApiUrl('proposal/store'),
                [
                    'judul_kegiatan' => $judulKegiatan,
                    'user_nim' => $userNim,
                    'file_content' => base64_encode(file_get_contents($file)),
                    'file_name' => $fileName,
                    'file_mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]
            );
        }

        if (!$response->successful()) {
            throw new \RuntimeException('Gagal menyimpan proposal: ' . $response->body());
        }
    }

    public function buildProposalDocumentCard(Group $group): array
    {
        return $this->buildProposalDocumentCardWithRoutes(
            $group,
            'dosen.validasi-dokumen.proposal',
            'validasi'
        );
    }

    public function buildProposalDocumentCardForPersetujuan(Group $group): array
    {
        return $this->buildProposalDocumentCardWithRoutes(
            $group,
            'dosen.persetujuan-kelompok.proposal',
            'persetujuan'
        );
    }

    private function buildProposalDocumentCardWithRoutes(
        Group $group,
        string $previewRouteName,
        string $context
    ): array {
        $fileDoc = $this->resolveProposalFileDocument($group);
        $uploaded = (bool) ($fileDoc['uploaded'] ?? false);

        $doc = [
            'key' => 'proposal',
            'label' => $context === 'persetujuan' ? 'Proposal Kelompok' : 'Proposal Kegiatan',
            'title' => $fileDoc['file_name'] ?? 'Proposal Kegiatan.pdf',
            'meta' => $uploaded
                ? trim('PDF' . (($fileDoc['file_size_label'] ?? '-') !== '-' ? ' • ' . $fileDoc['file_size_label'] : ''))
                : 'Belum diunggah',
            'uploaded' => $uploaded,
            'view_url' => $uploaded ? route($previewRouteName, $group->id) : null,
            'download_url' => $uploaded
                ? ($context === 'persetujuan'
                    ? route('dosen.persetujuan-kelompok.proposal-download', $group->id)
                    : route($previewRouteName, $group->id) . '?download=1')
                : null,
            'action_label' => 'Unduh',
            'action_type' => 'download',
            'review_status' => $group->proposal_review_status ?? 'pending',
            'review_note' => $group->proposal_review_note,
        ];

        $doc = $this->applyProposalReviewState($doc);

        return $this->applyProposalReviewContext($doc, $group, $context);
    }

    private function applyProposalReviewState(array $doc): array
    {
        $reviewStatus = $doc['review_status'] ?? 'pending';

        if (!$doc['uploaded'] && $reviewStatus === 'approved') {
            $reviewStatus = 'pending';
        }

        $doc['review_status'] = $reviewStatus;
        $doc['needs_revision'] = $reviewStatus === 'rejected'
            || (!$doc['uploaded'] && filled($doc['review_note'] ?? null));
        $doc['is_approved'] = $doc['uploaded'] && $reviewStatus === 'approved';
        $doc['needs_upload'] = !$doc['uploaded'] || $doc['needs_revision'];
        $doc['can_edit'] = false;
        $doc['can_review'] = $doc['uploaded'] && !$doc['is_approved'];

        if (!$doc['uploaded']) {
            $doc['status_label'] = filled($doc['review_note'] ?? null)
                ? 'Perlu unggah ulang'
                : 'Belum diunggah';
            $doc['status_class'] = filled($doc['review_note'] ?? null) ? 'revisi' : 'menunggu';
        } else {
            $doc['status_label'] = match ($reviewStatus) {
                'approved' => 'Disetujui',
                'rejected' => 'Perlu revisi',
                default => 'Menunggu validasi',
            };
            $doc['status_class'] = match ($reviewStatus) {
                'approved' => 'disetujui',
                'rejected' => 'revisi',
                default => 'menunggu',
            };
        }

        return $doc;
    }

    private function applyProposalReviewContext(array $doc, Group $group, string $context): array
    {
        if ($context !== 'persetujuan') {
            return $doc;
        }

        $canValidateGroup = in_array($group->status, self::REVIEWABLE_GROUP_STATUSES, true);
        $doc['can_review'] = $canValidateGroup
            && ($doc['uploaded'] ?? false)
            && !($doc['is_approved'] ?? false);

        return $doc;
    }

    public function proposalReviewMeta(Group $group, string $context = 'validasi'): array
    {
        $proposal = $this->fetchProposalMeta($group);
        $uploaded = (bool) ($proposal['uploaded'] ?? false);
        $status = $group->proposal_review_status ?? 'pending';
        $note = $group->proposal_review_note;

        if (!$uploaded && $status === 'approved') {
            $status = 'pending';
        }

        $isApproved = $uploaded && $status === 'approved';
        $needsRevision = $status === 'rejected' || (!$uploaded && filled($note));

        $statusLabel = match (true) {
            !$uploaded => filled($note) ? 'Perlu unggah ulang' : 'Belum diunggah',
            $isApproved => 'Disetujui',
            $status === 'rejected' => 'Perlu revisi',
            default => 'Menunggu validasi',
        };

        $statusClass = match (true) {
            !$uploaded => filled($note) ? 'revisi' : 'menunggu',
            $isApproved => 'disetujui',
            $status === 'rejected' => 'revisi',
            default => 'menunggu',
        };

        $meta = [
            'review_status' => $status,
            'review_note' => $note,
            'status_label' => $statusLabel,
            'status_class' => $statusClass,
            'uploaded' => $uploaded,
            'is_approved' => $isApproved,
            'needs_revision' => $needsRevision,
            'can_review' => $uploaded && !$isApproved,
        ];

        if ($context === 'persetujuan') {
            $card = $this->buildProposalDocumentCardForPersetujuan($group);
            $meta['can_review'] = $card['can_review'] ?? false;
            $meta['status_label'] = $card['status_label'] ?? $statusLabel;
            $meta['status_class'] = $card['status_class'] ?? $statusClass;
        }

        return $meta;
    }

    public function resolveProposalFileDocument(Group $group): array
    {
        $group->loadMissing(['groupLeader', 'members.mahasiswa']);

        $leaderNim = $group->groupLeader?->nim
            ?? $group->members->firstWhere('role', 'leader')?->mahasiswa?->nim;

        if (!$leaderNim) {
            return ['uploaded' => false, 'file_name' => null];
        }

        $row = null;

        if ($group->judul_kegiatan) {
            try {
                $row = DB::connection('dashboard')->table('proposal')
                    ->where('user_nim', $leaderNim)
                    ->where('judul_kegiatan', trim($group->judul_kegiatan))
                    ->orderByDesc('id')
                    ->first();
            } catch (\Throwable $e) {
                $row = null;
            }
        }

        if ($row) {
            $filePath = $row->file_path ?? null;
            $fileName = $row->file_name ?? 'Proposal Kegiatan.pdf';
            $fileSize = isset($row->file_size) ? (int) $row->file_size : null;

            if ($filePath && Storage::disk('dashboard')->exists($filePath)) {
                return [
                    'uploaded' => true,
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'file_size_label' => $fileSize ? $this->formatBytes($fileSize) : '-',
                    'disk' => 'dashboard',
                    'disk_path' => $filePath,
                ];
            }
        }

        return [
            'uploaded' => false,
            'file_name' => 'Proposal Kegiatan.pdf',
        ];
    }

    private function fetchProposalMeta(Group $group): array
    {
        $fileDoc = $this->resolveProposalFileDocument($group);

        return [
            'uploaded' => (bool) ($fileDoc['uploaded'] ?? false),
            'file_name' => $fileDoc['file_name'] ?? 'Proposal Kegiatan.pdf',
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
}
