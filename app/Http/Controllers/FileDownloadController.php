<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileDownloadController extends Controller
{
    /**
     * Download file berdasarkan jenis dokumen dan nama file
     */
    public function downloadFile(Request $request, $jenisDokumen, $fileName)
    {
        try {
            // Validasi user yang sedang login
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            // Decode URL untuk menangani spasi dalam nama file
            $fileName = urldecode($fileName);

            // Log untuk debugging
            \Log::info('FileDownloadController@downloadFile called', [
                'jenis_dokumen' => $jenisDokumen,
                'file_name' => $fileName,
                'user_nim' => $user->nim,
                'user_role' => $user->role
            ]);

            // Tentukan path file berdasarkan jenis dokumen
            $filePath = $this->getFilePath($jenisDokumen, $fileName, $user->nim);
            
            if (!$filePath || !file_exists($filePath)) {
                \Log::error('File not found for download', [
                    'jenis_dokumen' => $jenisDokumen,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_exists' => $filePath ? file_exists($filePath) : false
                ]);
                
                return redirect()->back()->with('error', 'File tidak ditemukan. File: ' . $fileName . ' (Jenis: ' . $jenisDokumen . ')');
            }

            \Log::info('File found for download', [
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'file_permissions' => substr(sprintf('%o', fileperms($filePath)), -4)
            ]);

            // Download file
            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            \Log::error('Error downloading file: ' . $e->getMessage(), [
                'jenis_dokumen' => $jenisDokumen,
                'file_name' => $fileName,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file.');
        }
    }

    /**
     * View file PDF di browser
     */
    public function viewFile(Request $request, $jenisDokumen, $fileName)
    {
        try {
            // Validasi user yang sedang login
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            // Decode URL untuk menangani spasi dalam nama file
            $fileName = urldecode($fileName);
            
            // Log untuk debugging
            \Log::info('FileDownloadController@viewFile called', [
                'jenis_dokumen' => $jenisDokumen,
                'file_name' => $fileName,
                'user_nim' => $user->nim,
                'user_role' => $user->role
            ]);
            
            // Tentukan path file berdasarkan jenis dokumen
            $filePath = $this->getFilePath($jenisDokumen, $fileName, $user->nim);
            
            // Log untuk debug
            \Log::info('File view attempt', [
                'jenis_dokumen' => $jenisDokumen,
                'file_name' => $fileName,
                'file_name_original' => $request->fileName,
                'user_nim' => $user->nim,
                'file_path' => $filePath,
                'file_exists' => $filePath ? file_exists($filePath) : false
            ]);
            
            if (!$filePath || !file_exists($filePath)) {
                \Log::error('File not found for view', [
                    'jenis_dokumen' => $jenisDokumen,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_exists' => $filePath ? file_exists($filePath) : false
                ]);
                
                return redirect()->back()->with('error', 'File tidak ditemukan. File: ' . $fileName . ' (Jenis: ' . $jenisDokumen . ')');
            }

            \Log::info('File found for view', [
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'file_permissions' => substr(sprintf('%o', fileperms($filePath)), -4)
            ]);

            // Tampilkan file PDF di browser
            $content = file_get_contents($filePath);
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error viewing file: ' . $e->getMessage(), [
                'jenis_dokumen' => $jenisDokumen,
                'file_name' => $fileName,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menampilkan file.');
        }
    }

    /**
     * Mendapatkan path file berdasarkan jenis dokumen
     */
    private function getFilePath($jenisDokumen, $fileName, $userNim)
    {
        // Log untuk debug
        \Log::info('getFilePath called', [
            'jenis_dokumen' => $jenisDokumen,
            'file_name' => $fileName,
            'user_nim' => $userNim
        ]);
        
        // Normalize jenis dokumen (handle both underscore and dash formats)
        $normalizedJenisDokumen = str_replace('_', '-', strtolower($jenisDokumen));
        
        // Coba cari file di project-akhir terlebih dahulu
        $localPath = $this->getLocalFilePath($normalizedJenisDokumen, $fileName);
        \Log::info('Local path check', [
            'local_path' => $localPath,
            'local_exists' => $localPath ? file_exists($localPath) : false
        ]);
        
        if ($localPath && file_exists($localPath)) {
            \Log::info('File found in local storage', ['path' => $localPath]);
            return $localPath;
        }
        
        // Jika tidak ada di local, coba dari dashboard
        $dashboardPath = $this->getDashboardFilePath($normalizedJenisDokumen, $fileName);
        \Log::info('Dashboard path check', [
            'dashboard_path' => $dashboardPath,
            'dashboard_exists' => $dashboardPath ? file_exists($dashboardPath) : false
        ]);
        
        if ($dashboardPath && file_exists($dashboardPath)) {
            \Log::info('File found in dashboard storage', ['path' => $dashboardPath]);
            return $dashboardPath;
        }
        
        // Fallback: coba dengan jenis dokumen asli (tanpa normalisasi)
        if ($normalizedJenisDokumen !== strtolower($jenisDokumen)) {
            $fallbackLocalPath = $this->getLocalFilePath(strtolower($jenisDokumen), $fileName);
            if ($fallbackLocalPath && file_exists($fallbackLocalPath)) {
                \Log::info('File found in local storage with fallback', ['path' => $fallbackLocalPath]);
                return $fallbackLocalPath;
            }
            
            $fallbackDashboardPath = $this->getDashboardFilePath(strtolower($jenisDokumen), $fileName);
            if ($fallbackDashboardPath && file_exists($fallbackDashboardPath)) {
                \Log::info('File found in dashboard storage with fallback', ['path' => $fallbackDashboardPath]);
                return $fallbackDashboardPath;
            }
        }
        
        // Fallback: coba cari file di semua direktori yang mungkin
        $allPossiblePaths = $this->getAllPossiblePaths($fileName);
        foreach ($allPossiblePaths as $path) {
            if (file_exists($path)) {
                \Log::info('File found in fallback search', ['path' => $path]);
                return $path;
            }
        }
        
        \Log::warning('File not found in any storage location', [
            'jenis_dokumen' => $jenisDokumen,
            'normalized_jenis_dokumen' => $normalizedJenisDokumen,
            'file_name' => $fileName,
            'searched_paths' => $allPossiblePaths
        ]);
        
        return null;
    }
    
    /**
     * Mendapatkan path file dari storage local project-akhir
     */
    private function getLocalFilePath($jenisDokumen, $fileName)
    {
        \Log::info('getLocalFilePath called', [
            'jenis_dokumen' => $jenisDokumen,
            'file_name' => $fileName
        ]);
        
        // Normalize jenis dokumen (handle both underscore and dash formats)
        $jenisDokumen = str_replace('_', '-', strtolower($jenisDokumen));
        
        $path = null;
        switch ($jenisDokumen) {
            case 'form-konversi':
                $path = storage_path('app/public/kkn-files/' . $fileName);
                break;
            
            case 'proposal':
                $path = storage_path('app/public/proposal/' . $fileName);
                break;
            
            case 'form-kesediaan':
                $path = storage_path('app/public/form-kesediaan/' . $fileName);
                break;
            
            case 'laporan-akhir':
                $path = storage_path('app/public/laporan-akhir/' . $fileName);
                break;
            
            case 'luaran':
                $path = storage_path('app/public/luaran/' . $fileName);
                break;
            
            case 'peer-review':
                $path = storage_path('app/public/peer-review/' . $fileName);
                break;
            
            default:
                $path = null;
        }
        
        \Log::info('getLocalFilePath result', [
            'jenis_dokumen' => $jenisDokumen,
            'file_name' => $fileName,
            'path' => $path,
            'exists' => $path ? file_exists($path) : false
        ]);
        
        return $path;
    }
    
    /**
     * Mendapatkan path file dari dashboard
     */
    private function getDashboardFilePath($jenisDokumen, $fileName)
    {
        // Path ke direktori dashboard
        $dashboardPath = base_path('../dashboard/storage/app/public/');
        
        // Normalize jenis dokumen (handle both underscore and dash formats)
        $jenisDokumen = str_replace('_', '-', strtolower($jenisDokumen));
        
        switch ($jenisDokumen) {
            case 'form-konversi':
                return $dashboardPath . 'kkn-files/' . $fileName;
            
            case 'proposal':
                return $dashboardPath . 'proposal-files/' . $fileName;
            
            case 'form-kesediaan':
                return $dashboardPath . 'form-kesediaan-files/' . $fileName;
            
            case 'laporan-akhir':
                return $dashboardPath . 'laporan-akhir-files/' . $fileName;
            
            case 'luaran':
                return $dashboardPath . 'luaran-files/' . $fileName;
            
            case 'peer-review':
                return $dashboardPath . 'peer-review-files/' . $fileName;
            
            default:
                return null;
        }
    }

    /**
     * Mendapatkan semua path yang mungkin untuk file
     */
    private function getAllPossiblePaths($fileName)
    {
        $paths = [];
        
        // Local storage paths
        $localBase = storage_path('app/public/');
        $localDirs = ['kkn-files', 'proposal', 'form-kesediaan', 'laporan-akhir', 'luaran', 'peer-review', 'proposal-files'];
        foreach ($localDirs as $dir) {
            $paths[] = $localBase . $dir . '/' . $fileName;
        }
        
        // Dashboard storage paths
        $dashboardBase = base_path('../dashboard/storage/app/public/');
        $dashboardDirs = ['kkn-files', 'proposal-files', 'form-kesediaan-files', 'laporan-akhir-files', 'luaran-files', 'peer-review-files'];
        foreach ($dashboardDirs as $dir) {
            $paths[] = $dashboardBase . $dir . '/' . $fileName;
        }
        
        return $paths;
    }

    /**
     * Download file untuk dosen (dengan validasi NIM mahasiswa)
     */
    public function downloadFileForDosen(Request $request, $jenisDokumen, $fileName, $studentNim)
    {
        try {
            // Validasi user yang sedang login adalah dosen
            $user = Auth::user();
            if (!$user || $user->role !== 'dosen') {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }

            // Decode URL untuk menangani spasi dalam nama file
            $fileName = urldecode($fileName);

            // Tentukan path file berdasarkan jenis dokumen
            $filePath = $this->getFilePath($jenisDokumen, $fileName, $studentNim);
            
            if (!$filePath || !file_exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan. File: ' . $fileName . ' (Jenis: ' . $jenisDokumen . ')');
            }

            // Download file
            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            \Log::error('Error downloading file for dosen: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file.');
        }
    }

    /**
     * View file untuk dosen
     */
    public function viewFileForDosen(Request $request, $jenisDokumen, $fileName, $studentNim)
    {
        try {
            // Validasi user yang sedang login adalah dosen
            $user = Auth::user();
            if (!$user || $user->role !== 'dosen') {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }

            // Decode URL untuk menangani spasi dalam nama file
            $fileName = urldecode($fileName);

            // Tentukan path file berdasarkan jenis dokumen
            $filePath = $this->getFilePath($jenisDokumen, $fileName, $studentNim);
            
            if (!$filePath || !file_exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan. File: ' . $fileName . ' (Jenis: ' . $jenisDokumen . ')');
            }

            // Tampilkan file PDF di browser
            $content = file_get_contents($filePath);
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error viewing file for dosen: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menampilkan file.');
        }
    }

}
