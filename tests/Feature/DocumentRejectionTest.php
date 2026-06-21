<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupDocumentReview;
use App\Models\GroupMember;
use App\Models\User;
use App\Services\GroupDocumentStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentRejectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejected_document_status_is_visible_to_student(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'nip' => '198001012005011001']);
        $leader = User::factory()->create(['role' => 'mahasiswa', 'nim' => '10221081']);
        $group = Group::create([
            'nama_kelompok' => 'Kelompok A',
            'judul_kegiatan' => 'Kegiatan A',
            'lokasi_kkn' => 'Balikpapan',
            'dosen_id' => $dosen->id,
            'leader_id' => $leader->id,
            'status' => 'active',
            'supervisor_approved_at' => now(),
        ]);
        GroupMember::create([
            'group_id' => $group->id,
            'mahasiswa_id' => $leader->id,
            'role' => 'leader',
            'status' => 'active',
        ]);
        GroupDocumentReview::create([
            'group_id' => $group->id,
            'status' => 'pending',
            'laporan_status' => 'pending',
            'artikel_status' => 'pending',
            'video_status' => 'pending',
        ]);

        $service = app(GroupDocumentStatusService::class);
        $service->reviewDocument($group, GroupDocumentReview::DOC_LAPORAN, 'reject', 'Perbaiki bab metode', (int) $dosen->id);

        $review = GroupDocumentReview::where('group_id', $group->id)->first();
        $this->assertSame('rejected', $review->laporan_status);
        $this->assertSame('Perbaiki bab metode', $review->laporan_note);

        $studentStatus = $service->forStudent((int) $leader->id);
        $this->assertSame('revisi', $studentStatus['ui_status']);
        $this->assertTrue($studentStatus['documents'][GroupDocumentReview::DOC_LAPORAN]['needs_revision']);
        $this->assertTrue($studentStatus['can_submit']);
        $this->assertNotEmpty($studentStatus['revision_notes']);
    }

    public function test_rejected_document_without_note_still_shows_revision_to_student(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'nip' => '198001012005011002']);
        $leader = User::factory()->create(['role' => 'mahasiswa', 'nim' => '10221082']);
        $group = Group::create([
            'nama_kelompok' => 'Kelompok B',
            'judul_kegiatan' => 'Kegiatan B',
            'lokasi_kkn' => 'Balikpapan',
            'dosen_id' => $dosen->id,
            'leader_id' => $leader->id,
            'status' => 'active',
            'supervisor_approved_at' => now(),
        ]);
        GroupMember::create([
            'group_id' => $group->id,
            'mahasiswa_id' => $leader->id,
            'role' => 'leader',
            'status' => 'active',
        ]);
        GroupDocumentReview::create([
            'group_id' => $group->id,
            'status' => 'pending',
            'laporan_status' => 'pending',
            'artikel_status' => 'pending',
            'video_status' => 'pending',
        ]);

        $service = app(GroupDocumentStatusService::class);
        $service->reviewDocument($group, GroupDocumentReview::DOC_ARTIKEL, 'reject', null, (int) $dosen->id);

        $studentStatus = $service->forStudent((int) $leader->id);
        $this->assertSame('revisi', $studentStatus['ui_status']);
        $this->assertSame('Dosen meminta revisi dokumen ini.', $studentStatus['revision_notes'][0]['note']);
    }
}
