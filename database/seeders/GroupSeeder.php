<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil dosen untuk assignment
        $dosen = User::where('role', 'dosen')->first();
        
        // Data kelompok sesuai dengan gambar
        $groups = [
            [
                'nama_kelompok' => 'Kelompok A',
                'judul_kegiatan' => 'Edukasi Digital',
                'lokasi_kkn' => 'Jl. Kemayoran',
                'nama_mitra' => 'SD Al-Azhar',
                'lokasi_mitra' => 'Jl. Kemayoran',
                'deskripsi_kegiatan' => 'Program edukasi digital untuk siswa SD',
                'dosen_id' => $dosen ? $dosen->id : null,
                'status' => 'assigned',
                'progress_verifikasi' => 100,
                'assigned_at' => now(),
                'members' => [
                    [
                        'name' => 'Ni Putu Dian Pramesti',
                        'nim' => '10221061',
                        'email' => '10221061@test.com',
                        'role' => 'leader'
                    ]
                ]
            ],
            [
                'nama_kelompok' => 'Kelompok B',
                'judul_kegiatan' => 'Peningkatan Gizi Balita',
                'lokasi_kkn' => 'Jl. Menuju Syurga',
                'nama_mitra' => 'Posyandu Kita',
                'lokasi_mitra' => 'Jl. Menuju Syurga',
                'deskripsi_kegiatan' => 'Program peningkatan gizi balita di posyandu',
                'dosen_id' => $dosen ? $dosen->id : null,
                'status' => 'assigned',
                'progress_verifikasi' => 100,
                'assigned_at' => now(),
                'members' => [
                    [
                        'name' => 'Shello Juliano Julius',
                        'nim' => '10221041',
                        'email' => '10221041@test.com',
                        'role' => 'leader'
                    ]
                ]
            ],
            [
                'nama_kelompok' => 'Kelompok C',
                'judul_kegiatan' => 'Inovasi Organik Sampah',
                'lokasi_kkn' => 'Jl. Arah Pulang',
                'nama_mitra' => 'Desa Manaaja',
                'lokasi_mitra' => 'Jl. Arah Pulang',
                'deskripsi_kegiatan' => 'Program inovasi pengolahan sampah organik',
                'dosen_id' => $dosen ? $dosen->id : null,
                'status' => 'assigned',
                'progress_verifikasi' => 100,
                'assigned_at' => now(),
                'members' => [
                    [
                        'name' => 'Gilang Setiawan',
                        'nim' => '10221059',
                        'email' => '10221059@test.com',
                        'role' => 'leader'
                    ]
                ]
            ],
            [
                'nama_kelompok' => 'Kelompok D',
                'judul_kegiatan' => 'Pemberdayaan Masyarakat Pesisir',
                'lokasi_kkn' => 'Jl. Murjani',
                'nama_mitra' => 'Kampung Biatan',
                'lokasi_mitra' => 'Jl. Murjani',
                'deskripsi_kegiatan' => 'Program pemberdayaan masyarakat pesisir',
                'dosen_id' => $dosen ? $dosen->id : null,
                'status' => 'assigned',
                'progress_verifikasi' => 100,
                'assigned_at' => now(),
                'members' => [
                    [
                        'name' => 'Desti Irawati',
                        'nim' => '11221033',
                        'email' => '11221033@test.com',
                        'role' => 'leader'
                    ]
                ]
            ]
        ];

        foreach ($groups as $groupData) {
            // Buat atau ambil mahasiswa
            $members = $groupData['members'];
            unset($groupData['members']);
            
            // Buat kelompok
            $group = Group::create($groupData);
            
            // Buat anggota kelompok
            foreach ($members as $memberData) {
                // Buat atau ambil user mahasiswa
                $mahasiswa = User::updateOrCreate(
                    ['email' => $memberData['email']],
                    [
                        'name' => $memberData['name'],
                        'nim' => $memberData['nim'],
                        'email' => $memberData['email'],
                        'password' => bcrypt('password'),
                        'role' => 'mahasiswa'
                    ]
                );
                
                // Buat group member
                GroupMember::create([
                    'group_id' => $group->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'role' => $memberData['role'],
                    'status' => 'active'
                ]);
            }
        }
    }
}

