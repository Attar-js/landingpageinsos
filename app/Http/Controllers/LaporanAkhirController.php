<?php

namespace App\Http\Controllers;

use App\Models\GroupDocumentReview;
use App\Models\GroupMember;
use App\Services\GroupDocumentStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LaporanAkhirController extends Controller
{
    public function index(GroupDocumentStatusService $documentStatusService)
    {
        $uploadStatus = $documentStatusService->forStudent((int) Auth::id());

        return view('pages.laporanakhir', compact('uploadStatus'));
    }

    public function store(Request $request, GroupDocumentStatusService $documentStatusService)
    {
        $uploadStatus = $documentStatusService->forStudent((int) Auth::id());
        if (!$uploadStatus || !($uploadStatus['can_submit'] ?? false)) {
            return redirect()->back()
                ->with('error', 'Upload tidak dapat dilakukan. Pastikan kelompok sudah disetujui dosen dan status validasi mengizinkan unggah dokumen.');
        }

        $docs = $uploadStatus['documents'];
        $needsLaporan = $docs[GroupDocumentReview::DOC_LAPORAN]['can_edit'] ?? false;
        $needsArtikel = $docs[GroupDocumentReview::DOC_ARTIKEL]['can_edit'] ?? false;
        $needsVideo = $docs[GroupDocumentReview::DOC_VIDEO]['can_edit'] ?? false;
        $needsLuaran = $needsArtikel || $needsVideo;

        $member = GroupMember::with('group')
            ->where('mahasiswa_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if (!$member?->group) {
            return redirect()->back()->with('error', 'Kelompok aktif tidak ditemukan.');
        }

        $group = $member->group;
        $isRevisionUpload = (bool) ($uploadStatus['is_revision_upload'] ?? false);
        $judulKegiatan = trim($group->judul_kegiatan ?? '');

        if ($judulKegiatan === '') {
            return redirect()->back()
                ->with('error', 'Judul kegiatan kelompok belum tersedia. Hubungi dosen pembimbing.')
                ->withInput();
        }

        $rules = [
            'user_nim' => 'required|string',
        ];

        if (!$isRevisionUpload) {
            $rules['judul_kegiatan'] = 'required|string|max:255';
        }

        if ($needsLaporan) {
            $rules['file'] = 'required|file|mimes:pdf|max:10240';
        }
        if ($needsArtikel) {
            $rules['artikel_file'] = 'required|file|mimes:pdf,doc,docx|max:10240';
            $rules['artikel_link'] = 'required|url|max:255';
        } elseif ($needsVideo) {
            $rules['artikel_link'] = 'nullable|url|max:255';
        }
        if ($needsVideo) {
            $rules['video_aftermovie'] = 'required|url|max:255';
        }

        $request->validate($rules);

        $request->merge([
            'judul_kegiatan' => $isRevisionUpload
                ? $judulKegiatan
                : trim((string) $request->input('judul_kegiatan', $judulKegiatan)),
        ]);

        $uploadedItems = [];

        try {
            if ($needsLaporan) {
                $this->uploadLaporanAkhir($request);
                $documentStatusService->markDocumentPendingAfterUpload($group, GroupDocumentReview::DOC_LAPORAN);
                $uploadedItems[] = 'Laporan Akhir';
            }

            if ($needsLuaran) {
                $this->uploadLuaran($request, $docs, $needsArtikel, $needsVideo);
                if ($needsArtikel) {
                    $documentStatusService->markDocumentPendingAfterUpload($group, GroupDocumentReview::DOC_ARTIKEL);
                    $uploadedItems[] = 'Artikel Publikasi';
                }
                if ($needsVideo) {
                    $documentStatusService->markDocumentPendingAfterUpload($group, GroupDocumentReview::DOC_VIDEO);
                    $uploadedItems[] = 'Video Aftermovie';
                }
            }

            $message = count($uploadedItems) === 1
                ? $uploadedItems[0] . ' berhasil diunggah. Menunggu validasi dosen pembimbing.'
                : 'Dokumen berhasil diunggah (' . implode(', ', $uploadedItems) . '). Menunggu validasi dosen pembimbing.';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    private function uploadLaporanAkhir(Request $request): void
    {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('public/laporan-akhir', $fileName);

        $response = Http::timeout(30)
            ->attach('file', file_get_contents($file), $fileName, [
                'Content-Type' => $file->getMimeType(),
            ])
            ->post(\App\Helpers\DashboardHelper::getApiUrl('laporan-akhir/store-from-external'), [
                'judul_kegiatan' => $request->judul_kegiatan,
                'user_nim' => $request->user_nim,
                'status' => 'pending',
            ]);

        $errors = $response->json('errors', []);
        $needsLegacyPayload = $response->status() === 422
            && is_array($errors)
            && array_key_exists('file_content', $errors);

        if ($needsLegacyPayload) {
            $response = Http::timeout(30)->post(\App\Helpers\DashboardHelper::getApiUrl('laporan-akhir/store'), [
                'judul_kegiatan' => $request->judul_kegiatan,
                'user_nim' => $request->user_nim,
                'file_content' => base64_encode(file_get_contents($file)),
                'file_name' => $fileName,
                'file_mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        if (!$response->successful()) {
            Storage::delete($filePath);
            throw new \RuntimeException('Gagal menyimpan laporan akhir: ' . $response->body());
        }
    }

    private function uploadLuaran(Request $request, array $docs, bool $needsArtikel, bool $needsVideo): void
    {
        $videoDoc = $docs[GroupDocumentReview::DOC_VIDEO] ?? [];
        $artikelDoc = $docs[GroupDocumentReview::DOC_ARTIKEL] ?? [];

        $videoUrl = $needsVideo
            ? trim((string) $request->video_aftermovie)
            : ($videoDoc['existing_video_url'] ?? '');

        $artikelLink = $needsArtikel
            ? trim((string) $request->artikel_link)
            : trim((string) ($request->artikel_link ?: ($artikelDoc['existing_artikel_link'] ?? '')));

        if ($needsVideo && $videoUrl === '') {
            throw new \RuntimeException('Tautan video aftermovie wajib diisi.');
        }

        if ($needsArtikel && $artikelLink === '') {
            throw new \RuntimeException('Tautan artikel wajib diisi.');
        }

        $http = Http::timeout(30);
        $payload = [
            'judul_kegiatan' => trim($request->judul_kegiatan),
            'video_aftermovie' => $videoUrl,
            'artikel_link' => $artikelLink,
            'user_nim' => trim($request->user_nim),
        ];

        if ($needsArtikel && $request->hasFile('artikel_file')) {
            $luaranFile = $request->file('artikel_file');
            $luaranFileName = time() . '_luaran_' . $luaranFile->getClientOriginalName();
            $http = $http->attach('artikel_file', file_get_contents($luaranFile), $luaranFileName, [
                'Content-Type' => $luaranFile->getMimeType(),
            ]);
        }

        $response = $http->post(
            \App\Helpers\DashboardHelper::getApiUrl('luaran/store-from-external'),
            $payload
        );

        if (!$response->successful()) {
            throw new \RuntimeException('Gagal menyimpan luaran: ' . $response->body());
        }
    }
}
