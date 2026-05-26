<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\KknPendaftar;
use App\Models\Luaran;
use App\Models\Proposal;
use App\Models\LaporanAkhir;
use App\Models\PeerReview;
use App\Models\FormKesediaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatusVerifikasiController extends Controller
{
    /**
     * Menampilkan halaman status verifikasi
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $isDosen = $user->role === 'dosen';
            
            // Debug log untuk memeriksa role
            \Log::info('StatusVerifikasiController - User Role Check', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'is_dosen' => $isDosen
            ]);
            
            // Jika dosen, ambil daftar kelompok yang dibimbing terlebih dahulu
            $kelompokList = [];
            if ($isDosen) {
                $kelompokList = $this->getKelompokList($user->id);
                
                // Debug log untuk memeriksa kelompok list
                \Log::info('StatusVerifikasiController - Kelompok List', [
                    'dosen_id' => $user->id,
                    'kelompok_count' => count($kelompokList),
                    'kelompok_list' => $kelompokList
                ]);
                
                // Jika dosen, cek apakah ada parameter nim yang dipilih
                if ($request->has('nim')) {
                    $selectedNim = $request->nim;
                    
                    // Validasi bahwa NIM yang dipilih adalah dari kelompok yang dibimbing
                    $validNim = collect($kelompokList)->pluck('nim')->contains($selectedNim);
                    if (!$validNim) {
                        return redirect()->route('status-verifikasi')->with('error', 'Kelompok yang dipilih tidak valid atau tidak dibimbing oleh Anda.');
                    }
                } else {
                    // Jika tidak ada NIM yang dipilih, gunakan NIM pertama dari kelompok yang dibimbing
                    if (!empty($kelompokList)) {
                        $selectedNim = $kelompokList[0]['nim'];
                    } else {
                        // Jika tidak ada kelompok yang dibimbing, tampilkan pesan
                        return view('pages.status-verifikasi', [
                            'mahasiswa' => null,
                            'dokumen' => [],
                            'isDosen' => true,
                            'kelompokList' => [],
                            'selectedNim' => null,
                            'noGroupsAssigned' => true
                        ]);
                    }
                }
            } else {
                // Untuk mahasiswa, gunakan NIM mereka sendiri
                $selectedNim = $user->nim ?? '10221051';
            }
            
            // Data dokumen yang diupload
            $dokumen = [];
            
            // 1. Form Konversi (KKN Pendaftar) - dari database lokal
            $dokumen[] = $this->getLocalDocumentStatus($selectedNim, 'Form Konversi');
            
            // 2. Proposal Kegiatan - dari database dashboard langsung
            $dokumen[] = $this->getHopeUiDocumentStatus('proposal', $selectedNim, 'Proposal Kegiatan');
            
            // 3. Surat Pernyataan Kesediaan Dosen Pembimbing - dari database dashboard langsung
            $dokumen[] = $this->getHopeUiDocumentStatus('form_kesediaan', $selectedNim, 'Surat Pernyataan Kesediaan Dosen Pembimbing');
            
            // 4. Laporan Akhir - dari database dashboard langsung
            $dokumen[] = $this->getHopeUiDocumentStatus('laporan_akhir', $selectedNim, 'Laporan Akhir');
            
            // 5. Luaran - dari database dashboard langsung
            $dokumen[] = $this->getHopeUiDocumentStatus('luaran', $selectedNim, 'Luaran');
            
            // 6. Peer Review - dari database dashboard langsung
            $dokumen[] = $this->getHopeUiDocumentStatus('peer_review', $selectedNim, 'Peer Review');
            
            // Ambil data identitas mahasiswa dari form konversi
            $mahasiswa = $this->getMahasiswaData($selectedNim);
            
            return view('pages.status-verifikasi', compact('mahasiswa', 'dokumen', 'isDosen', 'kelompokList', 'selectedNim'));
            
        } catch (\Exception $e) {
            \Log::error('Error in StatusVerifikasiController: ' . $e->getMessage());
            
            // Jika terjadi error, gunakan data default
            $mahasiswa = [
                'nama' => 'Data belum diupload',
                'nim' => Auth::user()->nim ?? '-',
                'program_studi' => '-',
                'judul_kegiatan' => '-',
                'mitra' => '-',
                'lokasi_mitra' => '-',
                'anggota' => []
            ];
            
            $dokumen = [
                [
                    'id' => null,
                    'jenis' => 'Form Konversi',
                    'status_upload' => 'Belum Upload',
                    'status_verifikasi' => 'Menunggu',
                    'catatan' => '-',
                    'file_name' => '-',
                    'upload_date' => '-'
                ],
                [
                    'id' => null,
                    'jenis' => 'Proposal Kegiatan',
                    'status_upload' => 'Belum Upload',
                    'status_verifikasi' => 'Menunggu',
                    'catatan' => '-',
                    'file_name' => '-',
                    'upload_date' => '-'
                ],
                [
                    'id' => null,
                    'jenis' => 'Surat Pernyataan Kesediaan Dosen Pembimbing',
                    'status_upload' => 'Belum Upload',
                    'status_verifikasi' => 'Menunggu',
                    'catatan' => '-',
                    'file_name' => '-',
                    'upload_date' => '-'
                ],
                [
                    'id' => null,
                    'jenis' => 'Laporan Akhir',
                    'status_upload' => 'Belum Upload',
                    'status_verifikasi' => 'Menunggu',
                    'catatan' => '-',
                    'file_name' => '-',
                    'upload_date' => '-'
                ],
                [
                    'id' => null,
                    'jenis' => 'Luaran',
                    'status_upload' => 'Belum Upload',
                    'status_verifikasi' => 'Menunggu',
                    'catatan' => '-',
                    'file_name' => '-',
                    'upload_date' => '-'
                ],
                [
                    'id' => null,
                    'jenis' => 'Peer Review',
                    'status_upload' => 'Belum Upload',
                    'status_verifikasi' => 'Menunggu',
                    'catatan' => '-',
                    'file_name' => '-',
                    'upload_date' => '-'
                ]
            ];
            
            $isDosen = Auth::user()->role === 'dosen';
            $kelompokList = [];
            $selectedNim = Auth::user()->nim ?? '10221051';
            
            return view('pages.status-verifikasi', compact('mahasiswa', 'dokumen', 'isDosen', 'kelompokList', 'selectedNim'));
        }
    }
    
    /**
     * Mendapatkan status dokumen dari database lokal (Form Konversi)
     */
    private function getLocalDocumentStatus($userNim, $jenisDokumen)
    {
        try {
            $pendaftar = \App\Models\KknPendaftar::where('user_nim', $userNim)->first();
            
            if ($pendaftar) {
                return [
                    'id' => $pendaftar->id,
                    'jenis' => $jenisDokumen,
                    'jenis_dokumen_key' => 'form-konversi',
                    'status_upload' => 'Terkirim',
                    'status_verifikasi' => $this->mapStatus($pendaftar->status_verifikasi ?? $pendaftar->status ?? 'pending'),
                    'catatan' => $pendaftar->catatan_verifikasi ?? '-',
                    'file_name' => $pendaftar->file_name ?? '-',
                    'upload_date' => $pendaftar->created_at ? $pendaftar->created_at->format('Y-m-d H:i:s') : '-'
                ];
            }
            
            // Jika data tidak ditemukan, berarti belum upload
            return [
                'id' => null,
                'jenis' => $jenisDokumen,
                'jenis_dokumen_key' => 'form-konversi',
                'status_upload' => 'Belum Upload',
                'status_verifikasi' => 'Menunggu', // Ubah dari '-' ke 'Menunggu'
                'catatan' => '-',
                'file_name' => '-',
                'upload_date' => '-'
            ];
            
        } catch (\Exception $e) {
            \Log::error("Error getting local document status: " . $e->getMessage());
            
            return [
                'id' => null,
                'jenis' => $jenisDokumen,
                'jenis_dokumen_key' => 'form-konversi',
                'status_upload' => 'Belum Upload',
                'status_verifikasi' => 'Menunggu', // Ubah dari '-' ke 'Menunggu'
                'catatan' => '-',
                'file_name' => '-',
                'upload_date' => '-'
            ];
        }
    }
    
    /**
     * Mendapatkan status dokumen dari database dashboard langsung
     */
    private function getHopeUiDocumentStatus($tableName, $userNim, $jenisDokumen)
    {
        try {
            // Koneksi ke database dashboard dengan error handling yang lebih baik
            $hopeUiData = null;
            
            try {
                $hopeUiData = \DB::connection('dashboard')->table($tableName)
                    ->where('user_nim', $userNim)
                    ->first();
            } catch (\Exception $connectionError) {
                \Log::error("Database connection error for {$tableName}: " . $connectionError->getMessage());
                
                // Fallback: coba dengan connection default
                try {
                    $hopeUiData = \DB::table($tableName)
                        ->where('user_nim', $userNim)
                        ->first();
                } catch (\Exception $fallbackError) {
                    \Log::error("Fallback connection also failed for {$tableName}: " . $fallbackError->getMessage());
                }
            }
            
            if ($hopeUiData) {
                // Ambil status dari database, default ke 'pending' jika null
                $statusFromDB = $hopeUiData->status ?? 'pending';
                
                // Ambil file_name dengan pengecekan yang lebih aman
                $fileName = null;
                if (isset($hopeUiData->file_name) && !empty($hopeUiData->file_name)) {
                    $fileName = $hopeUiData->file_name;
                } elseif (isset($hopeUiData->artikel_file_name) && !empty($hopeUiData->artikel_file_name)) {
                    $fileName = $hopeUiData->artikel_file_name;
                } else {
                    $fileName = '-';
                }
                
                return [
                    'id' => $hopeUiData->id ?? null, // Tambahkan ID dari dashboard
                    'jenis' => $jenisDokumen,
                    'jenis_dokumen_key' => $tableName,
                    'status_upload' => 'Terkirim',
                    'status_verifikasi' => $this->mapStatus($statusFromDB),
                    'catatan' => $hopeUiData->catatan ?? '-',
                    'file_name' => $fileName,
                    'upload_date' => isset($hopeUiData->created_at) ? date('Y-m-d H:i:s', strtotime($hopeUiData->created_at)) : '-'
                ];
            }
            
            // Jika data tidak ditemukan, berarti belum upload
            return [
                'id' => null, // Tambahkan ID dari dashboard
                'jenis' => $jenisDokumen,
                'jenis_dokumen_key' => $tableName,
                'status_upload' => 'Belum Upload',
                'status_verifikasi' => 'Menunggu', // Ubah dari '-' ke 'Menunggu'
                'catatan' => '-',
                'file_name' => '-',
                'upload_date' => '-'
            ];
            
        } catch (\Exception $e) {
            \Log::error("Error getting dashboard document status for {$jenisDokumen}: " . $e->getMessage());
            
            // Return default data jika terjadi error
            return [
                'id' => null,
                'jenis' => $jenisDokumen,
                'jenis_dokumen_key' => $tableName,
                'status_upload' => 'Belum Upload',
                'status_verifikasi' => 'Menunggu',
                'catatan' => 'Terjadi kesalahan koneksi database',
                'file_name' => '-',
                'upload_date' => '-'
            ];
        }
    }
    
    /**
     * Mendapatkan data mahasiswa dari database lokal
     */
    private function getMahasiswaData($userNim)
    {
        try {
            $pendaftar = \App\Models\KknPendaftar::where('user_nim', $userNim)->first();
            
            if ($pendaftar) {
                $anggota = \App\Models\KknAnggota::where('kkn_pendaftar_id', $pendaftar->id)->get();
                $anggotaNames = $anggota->pluck('nama')->toArray();
                
                return [
                    'nama' => $anggota->first()->nama ?? 'Data tidak tersedia',
                    'nim' => $userNim,
                    'program_studi' => $anggota->first()->program_studi ?? 'Data tidak tersedia',
                    'judul_kegiatan' => $pendaftar->judul_kegiatan ?? 'Data tidak tersedia',
                    'mitra' => $pendaftar->mitra ?? 'Data tidak tersedia',
                    'lokasi_mitra' => $pendaftar->lokasi_mitra ?? 'Data tidak tersedia',
                    'anggota' => $anggotaNames
                ];
            }
            
            return [
                'nama' => 'Data belum diupload',
                'nim' => $userNim,
                'program_studi' => '-',
                'judul_kegiatan' => '-',
                'mitra' => '-',
                'lokasi_mitra' => '-',
                'anggota' => []
            ];
            
        } catch (\Exception $e) {
            \Log::error("Error getting mahasiswa data: " . $e->getMessage());
            
            return [
                'nama' => 'Data belum diupload',
                'nim' => $userNim,
                'program_studi' => '-',
                'judul_kegiatan' => '-',
                'mitra' => '-',
                'lokasi_mitra' => '-',
                'anggota' => []
            ];
        }
    }
    
    /**
     * Memetakan status dari database ke tampilan Indonesia
     */
    private function mapStatus($status)
    {
        // Debug: Log status yang diterima
        \Log::info('Mapping status: ' . $status);
        
        switch (strtolower($status)) {
            case 'approved':
            case 'diterima':
            case 'accepted':
            case 'verified':
            case 'disetujui':
                return 'Diterima';
            case 'rejected':
            case 'ditolak':
            case 'declined':
            case 'unverified':
                return 'Ditolak';
            case 'pending':
            case 'menunggu':
            case 'waiting':
            case 'belum diverifikasi':
            default:
                return 'Menunggu';
        }
    }
    
    /**
     * Mendapatkan daftar kelompok yang dibimbing oleh dosen
     */
    private function getKelompokList($dosenId)
    {
        try {
            // Untuk sementara, gunakan dosen_id: 20 karena itu yang ada di database dashboard
            // TODO: Perlu sinkronisasi dosen_id antara project-akhir dan dashboard
            $dashboardDosenId = 20;
            
            // Ambil assignment dosen dari database dashboard
            $assignments = DB::connection('dashboard')
                ->table('group_assignments')
                ->where('dosen_id', $dashboardDosenId)
                ->get();
            
            \Log::info("Found " . $assignments->count() . " assignments for dosen_id: " . $dashboardDosenId);
            
            $kelompokList = [];
            
            foreach ($assignments as $assignment) {
                // Ambil data pendaftar
                $pendaftar = DB::connection('dashboard')
                    ->table('kkn_pendaftar')
                    ->where('id', $assignment->group_id)
                    ->first();
                
                if ($pendaftar) {
                    // Ambil anggota pertama sebagai ketua (karena tidak ada kolom is_ketua)
                    $ketua = DB::connection('dashboard')
                        ->table('kkn_anggota')
                        ->where('kkn_pendaftar_id', $pendaftar->id)
                        ->first();
                    
                    if ($ketua) {
                        $kelompokList[] = [
                            'nim' => $ketua->nim,
                            'nama' => $ketua->nama,
                            'judul_kegiatan' => $pendaftar->judul_kegiatan ?? 'Judul belum diisi',
                            'mitra' => $pendaftar->mitra ?? 'Mitra belum diisi'
                        ];
                        
                        \Log::info("Added kelompok: " . $ketua->nama . " (" . $ketua->nim . ")");
                    }
                }
            }
            
            \Log::info("Total kelompok found: " . count($kelompokList));
            return $kelompokList;
            
        } catch (\Exception $e) {
            \Log::error("Error getting kelompok list: " . $e->getMessage());
            return [];
        }
    }
}


