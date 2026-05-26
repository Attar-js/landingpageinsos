<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormKesediaan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardHelper;

class FormKesediaanController extends Controller
{
    /**
     * Menampilkan form kesediaan
     */
    public function showForm()
    {
        return view('form-kesediaan', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Menyimpan data form kesediaan
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $identityNumber = $user->isDosen() ? ($user->nip ?? '') : ($user->nim ?? '');

        $request->validate([
            'judul_kegiatan' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240' // 10MB max
        ], [
            'judul_kegiatan.required' => 'Judul kegiatan harus diisi',
            'file.required' => 'File form kesediaan harus diupload',
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 10MB'
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Kirim ke dashboard dengan multipart agar ukuran request tetap efisien.
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($file), $fileName, [
                    'Content-Type' => $file->getMimeType()
                ])
                ->post(DashboardHelper::getApiUrl('form-kesediaan/store-from-external'), [
                    'judul_kegiatan' => $request->judul_kegiatan,
                    'user_nim' => $identityNumber,
                    'nama_dosen' => $user->isDosen() ? $user->name : null,
                    'nip_dosen' => $user->isDosen() ? $user->nip : null,
                    'status' => 'pending',
                ]);

            // Fallback kompatibilitas untuk dashboard yang masih pakai contract lama.
            $errors = $response->json('errors', []);
            $needsLegacyPayload = $response->status() === 422
                && is_array($errors)
                && array_key_exists('file_content', $errors)
                && array_key_exists('file_name', $errors)
                && array_key_exists('file_mime_type', $errors)
                && array_key_exists('file_size', $errors);

            if ($needsLegacyPayload) {
                $fileContent = file_get_contents($file);
                $base64Content = base64_encode($fileContent);

                $response = Http::timeout(30)->post(DashboardHelper::getApiUrl('form-kesediaan/store'), [
                    'judul_kegiatan' => $request->judul_kegiatan,
                    'user_nim' => $identityNumber,
                    'nama_dosen' => $user->isDosen() ? $user->name : null,
                    'nip_dosen' => $user->isDosen() ? $user->nip : null,
                    'file_content' => $base64Content,
                    'file_name' => $fileName,
                    'file_mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize()
                ]);
            }

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Form Kesediaan berhasil dikirim!');
            } else {
                return redirect()->back()->with('error', 'Gagal mengirim form kesediaan: ' . $response->body());
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Cek status form kesediaan
     */
    public function status()
    {
        // Implementasi untuk cek status form kesediaan
        return view('form-kesediaan-status');
    }
}


