<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tes unduh template dokumen via link Google Docs.
 *
 * Alur aplikasi: cek ketersediaan link eksternal dulu, lalu redirect ke Google Docs.
 * Http::fake() dipakai agar test tidak bergantung internet/Google Docs sungguhan.
 *
 * Jalankan: php artisan test --filter=TemplateDownloadTest
 */
class TemplateDownloadTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $proposalDownloadUrl = 'https://docs.google.com/document/d/1FVxC0QixhmCwKX78PpzR110-NhHEggZg/export?format=pdf';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => '10221019',
        ]);
    }

    public function test_berhasil_mengunduh_template_jika_link_google_docs_tersedia(): void
    {
        Http::fake([
            'docs.google.com/*' => Http::response('fake-pdf-content', 200),
        ]);

        $response = $this->actingAs($this->user)
            ->from(route('template.index'))
            ->get(route('template.download', 'proposal'));

        $response->assertRedirect($this->proposalDownloadUrl);
    }

    public function test_gagal_mengunduh_template_jika_link_google_docs_tidak_tersedia(): void
    {
        Http::fake([
            'docs.google.com/*' => Http::response('Not Found', 404),
        ]);

        $response = $this->actingAs($this->user)
            ->from(route('template.index'))
            ->get(route('template.download', 'proposal'));

        $response->assertRedirect(route('template.index'));
        $response->assertSessionHas('error', 'Link unduh template tidak tersedia saat ini.');
    }

    public function test_menolak_unduh_template_dengan_key_tidak_valid(): void
    {
        $this->actingAs($this->user)
            ->get(route('template.download', 'tidak-ada'))
            ->assertNotFound();
    }

    public function test_mengarahkan_ke_login_jika_belum_autentikasi(): void
    {
        $this->get(route('template.download', 'proposal'))
            ->assertRedirect(route('login'));
    }
}
