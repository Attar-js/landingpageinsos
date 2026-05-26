<?php

namespace App\Http\Controllers;

use App\Models\NilaiCpmk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiCpmkController extends Controller
{
    /**
     * Display a listing of the resource for logged in student.
     */
    public function index()
    {
        $user = Auth::user();
        $nim = $user->nim ?? $user->username ?? null;
        
        if (!$nim) {
            return redirect()->back()->withErrors(['error' => 'NIM tidak ditemukan']);
        }

        $nilaiCpmk = NilaiCpmk::where('nim_mahasiswa', $nim)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pages.nilai-cpmk.index', compact('nilaiCpmk'));
    }

    /**
     * Download file PDF
     */
    public function download(NilaiCpmk $nilaiCpmk)
    {
        $user = Auth::user();
        $nim = $user->nim ?? $user->username ?? null;
        
        // Check if user is authorized to download this file
        if ($nilaiCpmk->nim_mahasiswa !== $nim) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak memiliki akses ke file ini']);
        }

        if (!$nilaiCpmk->file_content) {
            return back()->withErrors(['error' => 'File tidak ditemukan']);
        }

        return response($nilaiCpmk->file_content)
            ->header('Content-Type', $nilaiCpmk->file_mime_type ?? 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $nilaiCpmk->file_name . '"');
    }

    /**
     * View file PDF in browser
     */
    public function view(NilaiCpmk $nilaiCpmk)
    {
        $user = Auth::user();
        $nim = $user->nim ?? $user->username ?? null;
        
        // Check if user is authorized to view this file
        if ($nilaiCpmk->nim_mahasiswa !== $nim) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak memiliki akses ke file ini']);
        }

        if (!$nilaiCpmk->file_content) {
            return back()->withErrors(['error' => 'File tidak ditemukan']);
        }

        return response($nilaiCpmk->file_content)
            ->header('Content-Type', $nilaiCpmk->file_mime_type ?? 'application/pdf');
    }

    /**
     * Get student's CPMK grades (API endpoint for integration)
     */
    public function getMyGrades()
    {
        $user = Auth::user();
        $nim = $user->nim ?? $user->username ?? null;
        
        if (!$nim) {
            return response()->json(['error' => 'NIM tidak ditemukan'], 400);
        }

        $nilaiCpmk = NilaiCpmk::where('nim_mahasiswa', $nim)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'judul_kegiatan', 'file_name', 'status', 'uploaded_at', 'catatan']);

        return response()->json([
            'success' => true,
            'data' => $nilaiCpmk
        ]);
    }
} 
