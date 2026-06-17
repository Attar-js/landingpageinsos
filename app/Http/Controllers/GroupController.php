<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\KknPendaftar;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * API untuk mendapatkan status verifikasi kelompok
     *
     * @OA\Get(
     *     path="/api/groups/status-verifikasi",
     *     summary="Daftar status verifikasi semua kelompok",
     *     description="Mengembalikan daftar kelompok beserta anggota dan persentase progress verifikasi dokumen.",
     *     tags={"Kelompok"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data kelompok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nama_kelompok", type="string", example="Kelompok A"),
     *                 @OA\Property(property="judul_kegiatan", type="string", example="Pemberdayaan Masyarakat Pesisir"),
     *                 @OA\Property(property="lokasi_kkn", type="string", example="Desa Maju"),
     *                 @OA\Property(property="nama_mitra", type="string", example="Kampung Blaton"),
     *                 @OA\Property(property="lokasi_mitra", type="string", example="Jl. Murjani"),
     *                 @OA\Property(
     *                     property="members",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="name", type="string", example="Budi Santoso"),
     *                         @OA\Property(property="nim", type="string", example="2021001")
     *                     )
     *                 ),
     *                 @OA\Property(property="progress_verifikasi", type="number", format="float", example=66.67),
     *                 @OA\Property(property="status", type="string", example="active")
     *             )
     *         )
     *     )
     * )
     */
    public function getStatusVerifikasi()
    {
        $groups = Group::with(['members.mahasiswa', 'leader.mahasiswa'])->get();
        
        $result = $groups->map(function ($group) {
            // Hitung progress verifikasi
            $progress = $this->calculateVerificationProgress($group);
            
            // Ambil data anggota
            $members = $group->members->map(function ($member) {
                return [
                    'name' => $member->mahasiswa->name,
                    'nim' => $member->mahasiswa->nim
                ];
            })->toArray();
            
            return [
                'id' => $group->id,
                'nama_kelompok' => $group->nama_kelompok,
                'judul_kegiatan' => $group->judul_kegiatan,
                'lokasi_kkn' => $group->lokasi_kkn,
                'nama_mitra' => $group->nama_mitra,
                'lokasi_mitra' => $group->lokasi_mitra,
                'members' => $members,
                'progress_verifikasi' => $progress,
                'status' => $group->status
            ];
        });
        
        return response()->json($result);
    }

    /**
     * Hitung progress verifikasi berdasarkan dokumen
     */
    private function calculateVerificationProgress($group)
    {
        $totalDokumen = 6; // Form Konversi, Form Kesediaan, Proposal, Laporan Akhir, Peer Review, Luaran
        $diterimaCount = 0;
        
        // Ambil NIM ketua kelompok
        $leader = $group->leader;
        if (!$leader || !$leader->mahasiswa) {
            return 0;
        }
        
        $nimKetua = $leader->mahasiswa->nim;
        
        // Cek Form Konversi (dari database lokal)
        $pendaftar = KknPendaftar::where('user_nim', $nimKetua)->first();
        if ($pendaftar && ($pendaftar->status_verifikasi == 'diterima' || $pendaftar->status == 'approved')) {
            $diterimaCount++;
        }
        
        // Cek dokumen dari dashboard
        $hopeUiTables = ['proposal', 'form_kesediaan', 'laporan_akhir', 'luaran', 'peer_review'];
        
        foreach ($hopeUiTables as $table) {
            $data = DB::connection('dashboard')->table($table)
                ->where('user_nim', $nimKetua)
                ->first();
            
            if ($data && ($data->status == 'approved' || $data->status == 'diterima')) {
                $diterimaCount++;
            }
        }
        
        return ($diterimaCount / $totalDokumen) * 100;
    }
}


