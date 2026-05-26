<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\KknPendaftar;
use App\Models\Luaran;
use App\Models\Proposal;
use App\Models\LaporanAkhir;
use App\Models\PeerReview;
use App\Models\FormKesediaan;
use Illuminate\Support\Facades\DB;

class UploadUlangController extends Controller
{
    /**
     * Hapus pengumpulan dokumen yang ditolak
     */
    public function hapusPengumpulan($id, $jenis)
    {
        try {
            $userNim = Auth::user()->nim ?? '10221051';
            
            // Tentukan table name berdasarkan jenis dokumen
            $tableNames = [
                'Form Konversi' => 'kkn_pendaftars', // Local table
                'Proposal Kegiatan' => 'proposal',
                'Surat Pernyataan Kesediaan Dosen Pembimbing' => 'form_kesediaan',
                'Laporan Akhir' => 'laporan_akhir',
                'Luaran' => 'luaran',
                'Peer Review' => 'peer_review'
            ];
            
            $tableName = $tableNames[$jenis] ?? null;
            
            if (!$tableName) {
                return redirect()->route('status-verifikasi')
                    ->with('error', 'Jenis dokumen tidak valid');
            }
            
            // Handle Form Konversi (local database)
            if ($jenis === 'Form Konversi') {
                $pendaftar = KknPendaftar::where('user_nim', $userNim)->first();
                
                if ($pendaftar) {
                    // Hapus file dari storage
                    if ($pendaftar->file_name) {
                        $filePath = storage_path('app/public/kkn-files/' . $pendaftar->file_name);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                    
                    // Hapus anggota
                    $pendaftar->anggota()->delete();
                    
                    // Hapus pendaftar
                    $pendaftar->delete();
                    
                    return redirect()->route('status-verifikasi')
                        ->with('success', 'Pengumpulan Form Konversi berhasil dihapus. Silakan upload ulang di form utama.');
                }
            } else {
                // Handle dokumen lain (dashboard database)
                $hopeUiData = null;
                
                try {
                    $hopeUiData = DB::connection('dashboard')->table($tableName)
                        ->where('user_nim', $userNim)
                        ->first();
                } catch (\Exception $connectionError) {
                    \Log::error("Database connection error for {$tableName}: " . $connectionError->getMessage());
                    
                    // Fallback: coba dengan connection default
                    try {
                        $hopeUiData = DB::table($tableName)
                            ->where('user_nim', $userNim)
                            ->first();
                    } catch (\Exception $fallbackError) {
                        \Log::error("Fallback connection also failed for {$tableName}: " . $fallbackError->getMessage());
                    }
                }
                
                if ($hopeUiData) {
                    // Hapus file dari storage dashboard dengan pengecekan yang lebih aman
                    if (isset($hopeUiData->file_name) && !empty($hopeUiData->file_name)) {
                        $filePath = storage_path('app/public/' . $tableName . '-files/' . $hopeUiData->file_name);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        
                        // Coba path alternatif
                        $alternativePath = storage_path('app/public/' . $hopeUiData->file_name);
                        if (file_exists($alternativePath)) {
                            unlink($alternativePath);
                        }
                    }
                    
                    // Hapus data dari database dashboard
                    try {
                        DB::connection('dashboard')->table($tableName)
                            ->where('user_nim', $userNim)
                            ->delete();
                    } catch (\Exception $deleteError) {
                        \Log::error("Error deleting from dashboard database: " . $deleteError->getMessage());
                        
                        // Fallback: coba dengan connection default
                        try {
                            DB::table($tableName)
                                ->where('user_nim', $userNim)
                                ->delete();
                        } catch (\Exception $fallbackDeleteError) {
                            \Log::error("Fallback delete also failed: " . $fallbackDeleteError->getMessage());
                        }
                    }
                    
                    return redirect()->route('status-verifikasi')
                        ->with('success', 'Pengumpulan ' . $jenis . ' berhasil dihapus. Silakan upload ulang di form utama.');
                }
            }
            
            return redirect()->route('status-verifikasi')
                ->with('error', 'Data tidak ditemukan');
            
        } catch (\Exception $e) {
            \Log::error('Error hapus pengumpulan: ' . $e->getMessage());
            return redirect()->route('status-verifikasi')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Konfirmasi hapus pengumpulan
     */
    public function konfirmasiHapus($id, $jenis)
    {
        return view('pages.konfirmasi-hapus', compact('id', 'jenis'));
    }
    
    /**
     * Menampilkan form upload ulang berdasarkan jenis dokumen
     */
    public function showUploadUlang($id, $jenis)
    {
        $userNim = Auth::user()->nim ?? '10221051';
        
        // Tentukan route berdasarkan jenis dokumen
        $uploadRoutes = [
            'Form Konversi' => 'formkonversi',
            'Proposal Kegiatan' => 'proposal',
            'Surat Pernyataan Kesediaan Dosen Pembimbing' => 'formkesediaan',
            'Laporan Akhir' => 'laporanakhir',
            'Luaran' => 'luaran',
            'Peer Review' => 'peerreview'
        ];
        
        $routeName = $uploadRoutes[$jenis] ?? 'dashboard';
        
        return redirect()->route($routeName)->with('upload_ulang', true);
    }
    
    /**
     * Handle upload ulang untuk Form Konversi
     */
    public function uploadUlangFormKonversi(Request $request)
    {
        $request->validate([
            'judul_kegiatan' => 'required|string|max:255',
            'mitra' => 'required|string|max:255',
            'lokasi_mitra' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'anggota.*.nama' => 'required|string|max:255',
            'anggota.*.nim' => 'required|string|max:20',
            'anggota.*.program_studi' => 'required|string|max:100'
        ]);
        
        try {
            $userNim = Auth::user()->nim ?? '10221051';
            
            // Update data KKN Pendaftar
            $pendaftar = KknPendaftar::where('user_nim', $userNim)->first();
            
            if ($pendaftar) {
                // Update file
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/kkn-files', $fileName);
                    
                    $pendaftar->update([
                        'judul_kegiatan' => $request->judul_kegiatan,
                        'mitra' => $request->mitra,
                        'lokasi_mitra' => $request->lokasi_mitra,
                        'file_name' => $fileName,
                        'status_verifikasi' => 'pending', // Reset status ke pending
                        'catatan_verifikasi' => null // Clear catatan lama
                    ]);
                }
                
                // Update anggota
                $pendaftar->anggota()->delete(); // Hapus anggota lama
                foreach ($request->anggota as $anggotaData) {
                    $pendaftar->anggota()->create([
                        'nama' => $anggotaData['nama'],
                        'nim' => $anggotaData['nim'],
                        'program_studi' => $anggotaData['program_studi']
                    ]);
                }
                
                return redirect()->route('status-verifikasi')
                    ->with('success', 'Dokumen Form Konversi berhasil diupload ulang!');
            }
            
            return redirect()->back()->with('error', 'Data tidak ditemukan');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle upload ulang untuk dokumen lain (Proposal, Luaran, dll)
     */
    public function uploadUlangDokumen(Request $request, $jenis)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'judul_kegiatan' => 'required|string|max:255',
            'user_nim' => 'required|string|max:20'
        ]);
        
        try {
            $dashboardUrl = \App\Helpers\DashboardHelper::getDashboardUrl();
            
            // Upload file ke dashboard
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($request->file('file')), $request->file('file')->getClientOriginalName(), [
                    'Content-Type' => $request->file('file')->getMimeType()
                ])
                ->post($dashboardUrl . '/api/' . $this->getApiEndpoint($jenis) . '/store-from-external', [
                    'judul_kegiatan' => $request->judul_kegiatan,
                    'user_nim' => $request->user_nim,
                    'status' => 'pending' // Reset status ke pending
                ]);
            
            if ($response->successful()) {
                return redirect()->route('status-verifikasi')
                    ->with('success', 'Dokumen ' . $jenis . ' berhasil diupload ulang!');
            } else {
                return redirect()->back()->with('error', 'Gagal mengupload ulang: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Mendapatkan endpoint API berdasarkan jenis dokumen
     */
    private function getApiEndpoint($jenis)
    {
        $endpoints = [
            'Proposal Kegiatan' => 'proposal',
            'Surat Pernyataan Kesediaan Dosen Pembimbing' => 'form-kesediaan',
            'Laporan Akhir' => 'laporan-akhir',
            'Luaran' => 'luaran',
            'Peer Review' => 'peer-review'
        ];
        
        return $endpoints[$jenis] ?? 'proposal';
    }
} 

