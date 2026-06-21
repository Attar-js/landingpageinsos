<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupDocumentReview;
use App\Models\GroupMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;

class GroupDocumentStatusService
{
    public const DOC_KEYS = [
        GroupDocumentReview::DOC_LAPORAN,
        GroupDocumentReview::DOC_ARTIKEL,
        GroupDocumentReview::DOC_VIDEO,
    ];

    public function forStudent(int $mahasiswaId): ?array
    {
        $member = GroupMember::with(['group.documentReview', 'group.dosen', 'group.groupLeader'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        if (!$member?->group) {
            return null;
        }

        $group = $member->group;

        if (!in_array($group->status, ['active', 'waiting_leader_replacement'], true)) {
            return [
                'group_id' => $group->id,
                'group_name' => $group->nama_kelompok,
                'dosen_name' => $group->dosen?->name ?? '-',
                'ui_status' => 'kelompok_pending',
                'status_label' => 'Kelompok belum disetujui dosen pembimbing',
                'badge_class' => 'warning',
                'documents' => [],
                'can_upload' => false,
                'can_submit' => false,
            ];
        }

        return $this->forGroup($group);
    }

    public function forGroup(Group $group): array
    {
        $group->loadMissing(['documentReview', 'dosen', 'groupLeader']);

        $documents = $this->buildDocuments($group);
        $review = GroupDocumentReview::query()
            ->where('group_id', $group->id)
            ->first() ?? $this->ensureReviewRecord($group);

        foreach (self::DOC_KEYS as $key) {
            $documents[$key]['review_status'] = $review->statusFor($key);
            $documents[$key]['review_note'] = $review->noteFor($key);
            $documents[$key] = $this->applyDocumentReviewState($documents[$key]);
        }

        $allUploaded = collect($documents)->every(fn ($d) => $d['uploaded']);
        $allApproved = collect($documents)->every(fn ($d) => $d['is_approved']);
        $anyRejected = collect($documents)->contains(fn ($d) => $d['needs_revision'] ?? false);
        $canSubmit = collect($documents)->contains(fn ($d) => $d['can_edit'] ?? false);
        $hasAnyUploaded = collect($documents)->contains(fn ($d) => $d['uploaded'] ?? false);
        $isRevisionUpload = $canSubmit && ($anyRejected || $hasAnyUploaded);

        if ($allApproved && $allUploaded) {
            $uiStatus = 'disetujui';
            $label = 'Semua dokumen disetujui';
            $badgeClass = 'success';
        } elseif ($anyRejected) {
            $revisiCount = collect($documents)->where('needs_revision', true)->count();
            $uiStatus = 'revisi';
            $label = $revisiCount === 1
                ? 'Perlu revisi 1 dokumen'
                : "Perlu revisi {$revisiCount} dokumen";
            $badgeClass = 'danger';
        } elseif ($allUploaded) {
            $uiStatus = 'menunggu';
            $label = 'Menunggu validasi dosen';
            $badgeClass = 'warning';
        } else {
            $uiStatus = 'belum_upload';
            $label = 'Belum mengunggah semua dokumen';
            $badgeClass = 'secondary';
        }

        $revisionNotes = [];
        foreach ($documents as $doc) {
            if ($doc['needs_revision'] ?? false) {
                $revisionNotes[] = [
                    'key' => $doc['key'],
                    'label' => $doc['label'],
                    'note' => filled($doc['review_note'] ?? null)
                        ? $doc['review_note']
                        : 'Dosen meminta revisi dokumen ini.',
                ];
            }
        }

        return [
            'group_id' => $group->id,
            'group_name' => $group->nama_kelompok,
            'judul_kegiatan' => $group->judul_kegiatan ?? '',
            'is_revision_upload' => $isRevisionUpload,
            'dosen_name' => $group->dosen?->name ?? '-',
            'ui_status' => $uiStatus,
            'status_label' => $label,
            'badge_class' => $badgeClass,
            'documents' => $documents,
            'revision_notes' => $revisionNotes,
            'review_note' => collect($revisionNotes)
                ->map(fn ($r) => $r['label'] . ': ' . $r['note'])
                ->implode("\n"),
            'has_uploaded' => $allUploaded,
            'can_upload' => $canSubmit,
            'can_submit' => $canSubmit,
            'reviewed_at' => $review->reviewed_at,
        ];
    }

    public function reviewDocument(Group $group, string $docKey, string $action, ?string $note, int $reviewerId): void
    {
        if (!in_array($docKey, self::DOC_KEYS, true)) {
            abort(422, 'Jenis dokumen tidak valid.');
        }

        $review = $this->ensureReviewRecord($group);
        $status = $action === 'approve' ? 'approved' : 'rejected';

        $review->setItemStatus($docKey, $status, $action === 'reject' ? $note : null);
        $review->reviewed_by = $reviewerId;
        $review->reviewed_at = now();
        $review->syncOverallStatus();
        $review->save();

        $group->loadMissing('dosen');
        $labels = [
            GroupDocumentReview::DOC_LAPORAN => 'Laporan Akhir',
            GroupDocumentReview::DOC_ARTIKEL => 'Artikel',
            GroupDocumentReview::DOC_VIDEO => 'Video Luaran',
        ];
        $docLabel = $labels[$docKey] ?? 'Dokumen';
        $dosenName = $group->dosen?->name ?? NotificationService::actorName($reviewerId);

        if ($action === 'approve') {
            NotificationService::notifyGroupMembers(
                $group,
                $dosenName . ' menyetujui ' . $docLabel . ' kelompok "' . $group->nama_kelompok . '".',
                route('laporanakhir'),
                'check-circle'
            );
        } else {
            $noteSuffix = filled($note) ? ' Catatan: ' . $note : '';
            NotificationService::notifyGroupMembers(
                $group,
                $dosenName . ' meminta revisi ' . $docLabel . ' kelompok "' . $group->nama_kelompok . '".' . $noteSuffix,
                route('laporanakhir'),
                'alert-triangle'
            );
        }
    }

    public function markDocumentPendingAfterUpload(Group $group, string $docKey): void
    {
        if (!in_array($docKey, self::DOC_KEYS, true)) {
            return;
        }

        $review = $this->ensureReviewRecord($group);
        $review->setItemStatus($docKey, 'pending', null);
        $review->syncOverallStatus();
        $review->save();
    }

    public function markAllUploadedPending(Group $group): void
    {
        $review = $this->ensureReviewRecord($group);
        foreach (self::DOC_KEYS as $key) {
            $review->setItemStatus($key, 'pending', null);
        }
        $review->status = 'pending';
        $review->note = null;
        $review->save();
    }

    public function deleteDocument(Group $group, string $docKey, ?string $note, int $reviewerId): void
    {
        if (!in_array($docKey, self::DOC_KEYS, true)) {
            abort(422, 'Jenis dokumen tidak valid.');
        }

        $group->loadMissing('groupLeader');
        $leaderNim = $group->groupLeader?->nim;

        if (!$leaderNim) {
            throw new \RuntimeException('Ketua kelompok tidak ditemukan.');
        }

        $deleted = match ($docKey) {
            GroupDocumentReview::DOC_LAPORAN => $this->deleteLaporanAkhir($group, $leaderNim),
            GroupDocumentReview::DOC_ARTIKEL => $this->deleteArtikel($group, $leaderNim),
            GroupDocumentReview::DOC_VIDEO => $this->deleteVideo($group, $leaderNim),
            default => false,
        };

        if (!$deleted) {
            throw new \RuntimeException('Dokumen tidak ditemukan atau sudah dihapus.');
        }

        $reviewNote = filled($note)
            ? $note
            : 'Dokumen dihapus oleh dosen pembimbing karena pengumpulan tidak sesuai. Silakan unggah ulang.';

        $review = $this->ensureReviewRecord($group);
        $review->setItemStatus($docKey, 'pending', $reviewNote);
        $review->reviewed_by = $reviewerId;
        $review->reviewed_at = now();
        $review->syncOverallStatus();
        $review->save();
    }

    public function buildDocuments(Group $group): array
    {
        $leaderNim = $group->groupLeader?->nim;
        $laporan = $this->fetchDashboardRow('laporan_akhir', $leaderNim, $group);
        $luaran = $this->fetchLuaranRowForGroup($leaderNim, $group);

        $laporanDoc = $this->mapFileDocument($laporan, 'Laporan_Akhir.pdf', 'laporan', $group);
        $artikelPath = $this->resolveArtikelFilePath($luaran);
        $artikelDoc = $this->mapFileDocument(
            $artikelPath ? (object) [
                'file_path' => $artikelPath,
                'file_name' => $this->resolveArtikelFileName($luaran),
                'file_size' => $luaran->file_size ?? null,
            ] : null,
            'Artikel_Publikasi.pdf',
            'artikel',
            $group
        );

        $videoUrl = $luaran->video_aftermovie ?? null;

        return [
            GroupDocumentReview::DOC_LAPORAN => $laporanDoc,
            GroupDocumentReview::DOC_ARTIKEL => $artikelDoc,
            GroupDocumentReview::DOC_VIDEO => [
                'key' => GroupDocumentReview::DOC_VIDEO,
                'label' => 'Video Aftermovie',
                'title' => 'Video Aftermovie',
                'meta' => $videoUrl ? 'Link YouTube / Drive' : 'Belum diunggah',
                'uploaded' => filled(trim((string) $videoUrl)),
                'view_url' => $videoUrl,
                'download_url' => null,
                'action_label' => 'Lihat',
                'action_type' => 'view',
                'existing_video_url' => $videoUrl,
                'existing_artikel_link' => $luaran->artikel_link ?? null,
            ],
        ];
    }

    public function buildGroupCard(Group $group): array
    {
        $payload = $this->forGroup($group);
        $lokasi = $group->lokasi_mitra ?: $group->lokasi_kkn;

        return array_merge($payload, [
            'title' => trim(($group->nama_kelompok ?? 'Kelompok') . ($lokasi ? ' - ' . $lokasi : '')),
            'subtitle' => 'Validasi per dokumen — revisi hanya pada file yang ditolak.',
            'status_key' => match ($payload['ui_status']) {
                'disetujui' => 'disetujui',
                'revisi' => 'revisi',
                default => 'menunggu',
            },
            'is_fully_approved' => $payload['ui_status'] === 'disetujui',
        ]);
    }

    private function ensureReviewRecord(Group $group): GroupDocumentReview
    {
        return GroupDocumentReview::firstOrCreate(
            ['group_id' => $group->id],
            [
                'status' => 'pending',
                'laporan_status' => 'pending',
                'artikel_status' => 'pending',
                'video_status' => 'pending',
            ]
        );
    }

    private function applyDocumentReviewState(array $doc): array
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
        $doc['can_edit'] = $doc['needs_upload'] && !$doc['is_approved'];
        $doc['can_review'] = ($doc['uploaded'] ?? false) && !($doc['is_approved'] ?? false);
        $doc['status_label'] = $this->itemStatusLabel($doc);
        $doc['status_class'] = $this->itemStatusClass($doc);

        return $doc;
    }

    private function itemStatusLabel(array $doc): string
    {
        if (!$doc['uploaded']) {
            return filled($doc['review_note'] ?? null)
                ? 'Perlu unggah ulang'
                : 'Belum diunggah';
        }

        return match ($doc['review_status'] ?? 'pending') {
            'approved' => 'Disetujui',
            'rejected' => 'Perlu revisi',
            default => 'Menunggu validasi',
        };
    }

    private function itemStatusClass(array $doc): string
    {
        if (!$doc['uploaded']) {
            return filled($doc['review_note'] ?? null) ? 'revisi' : 'menunggu';
        }

        return match ($doc['review_status'] ?? 'pending') {
            'approved' => 'disetujui',
            'rejected' => 'revisi',
            default => 'menunggu',
        };
    }

    private function mapFileDocument(?object $row, string $defaultName, string $type, Group $group): array
    {
        $filePath = $row->file_path ?? null;
        $fileName = $row->file_name ?? $defaultName;
        $fileSize = isset($row->file_size) ? (int) $row->file_size : null;
        $uploaded = $filePath && Storage::disk('dashboard')->exists($filePath);

        $downloadRoute = match ($type) {
            'laporan' => 'dosen.validasi-dokumen.laporan',
            'artikel' => 'dosen.validasi-dokumen.artikel',
            default => null,
        };

        $docKey = match ($type) {
            'laporan' => GroupDocumentReview::DOC_LAPORAN,
            'artikel' => GroupDocumentReview::DOC_ARTIKEL,
            default => $type,
        };

        return [
            'key' => $docKey,
            'label' => match ($type) {
                'laporan' => 'Laporan Akhir',
                'artikel' => 'Artikel Publikasi',
                default => 'Dokumen',
            },
            'title' => $fileName,
            'meta' => $uploaded
                ? 'PDF' . ($fileSize ? ' · ' . $this->formatBytes($fileSize) : '')
                : 'Belum diunggah',
            'uploaded' => $uploaded,
            'view_url' => $uploaded && $downloadRoute ? route($downloadRoute, $group->id) : null,
            'download_url' => $uploaded && $downloadRoute ? route($downloadRoute, $group->id) . '?download=1' : null,
            'action_label' => 'Unduh',
            'action_type' => 'download',
        ];
    }

    private function deleteLaporanAkhir(Group $group, string $leaderNim): bool
    {
        $row = $this->fetchDashboardRow('laporan_akhir', $leaderNim, $group);
        if (!$row) {
            return false;
        }

        $this->deleteStorageFile($row->file_path ?? null);

        DB::connection('dashboard')->table('laporan_akhir')->where('id', $row->id)->delete();

        return true;
    }

    private function deleteArtikel(Group $group, string $leaderNim): bool
    {
        $row = $this->fetchLuaranRowForGroup($leaderNim, $group);
        if (!$row) {
            return false;
        }

        $artikelPath = $this->resolveArtikelFilePath($row);
        if (!$artikelPath && empty($row->artikel_file_name ?? null)) {
            return false;
        }

        $this->deleteStorageFile($artikelPath);

        DB::connection('dashboard')->table('luaran')->where('id', $row->id)->update([
            'artikel_file_path' => null,
            'artikel_file_name' => null,
            'artikel_link' => '',
            'updated_at' => now(),
        ]);
        $this->deleteLuaranRowIfEmpty($row->id);

        return true;
    }

    private function deleteVideo(Group $group, string $leaderNim): bool
    {
        $row = $this->fetchLuaranRowForGroup($leaderNim, $group);
        if (!$row) {
            return false;
        }

        $videoUrl = trim((string) ($row->video_aftermovie ?? ''));
        if ($videoUrl === '') {
            return false;
        }

        DB::connection('dashboard')->table('luaran')->where('id', $row->id)->update([
            'video_aftermovie' => '',
            'updated_at' => now(),
        ]);

        $this->deleteLuaranRowIfEmpty($row->id);

        return true;
    }

    private function fetchLuaranRowForGroup(string $leaderNim, Group $group): ?object
    {
        return $this->fetchDashboardRow('luaran', $leaderNim, $group);
    }

    private function deleteLuaranRowIfEmpty(int $luaranId): void
    {
        $row = DB::connection('dashboard')->table('luaran')->where('id', $luaranId)->first();
        if (!$row) {
            return;
        }

        $hasArtikel = filled($this->resolveArtikelFilePath($row));
        $hasVideo = filled($row->video_aftermovie ?? null);
        $hasLink = filled($row->artikel_link ?? null);

        if (!$hasArtikel && !$hasVideo && !$hasLink) {
            DB::connection('dashboard')->table('luaran')->where('id', $luaranId)->delete();
        }
    }

    private function resolveArtikelFilePath(?object $row): ?string
    {
        if (!$row) {
            return null;
        }

        if (filled($row->artikel_file_path ?? null)) {
            return $row->artikel_file_path;
        }

        return filled($row->file_path ?? null) ? $row->file_path : null;
    }

    private function resolveArtikelFileName(?object $row): string
    {
        if (!$row) {
            return 'Artikel_Publikasi.pdf';
        }

        return $row->artikel_file_name
            ?? $row->file_name
            ?? 'Artikel_Publikasi.pdf';
    }

    private function deleteStorageFile(?string $path): void
    {
        if ($path && Storage::disk('dashboard')->exists($path)) {
            Storage::disk('dashboard')->delete($path);
        }
    }

    private function fetchDashboardRow(string $table, ?string $leaderNim, Group $group): ?object
    {
        if (!$leaderNim) {
            return null;
        }

        try {
            if ($group->judul_kegiatan) {
                $byJudul = DB::connection('dashboard')->table($table)
                    ->where('user_nim', $leaderNim)
                    ->where('judul_kegiatan', trim($group->judul_kegiatan))
                    ->orderByDesc('id')
                    ->first();

                if ($byJudul) {
                    return $byJudul;
                }
            }

            return DB::connection('dashboard')->table($table)
                ->where('user_nim', $leaderNim)
                ->orderByDesc('id')
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
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
