<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat beberapa akun mahasiswa untuk testing
        $mahasiswaList = [
            [
                'name' => 'Mahasiswa 10221051',
                'email' => '10221051@test.com',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '10221051',
                'nip' => null,
            ],
            [
                'name' => 'Mahasiswa Test 1',
                'email' => 'mahasiswa1@test.com',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '2021001',
                'nip' => null,
            ],
            [
                'name' => 'Mahasiswa Test 2',
                'email' => 'mahasiswa2@test.com',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '2021002',
                'nip' => null,
            ],
            [
                'name' => 'Mahasiswa Test 3',
                'email' => 'mahasiswa3@test.com',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '2021003',
                'nip' => null,
            ],
        ];

        foreach ($mahasiswaList as $mahasiswa) {
            // Cek apakah user sudah ada berdasarkan email
            $existingUser = User::where('email', $mahasiswa['email'])->first();
            if (!$existingUser) {
                User::create($mahasiswa);
            }
        }

        echo "Berhasil membuat/mengecek akun mahasiswa untuk testing.\n";
        echo "Email: 10221051@test.com, mahasiswa1@test.com, mahasiswa2@test.com, mahasiswa3@test.com\n";
        echo "Password: password\n";
    }
}

