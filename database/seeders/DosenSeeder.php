<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun dosen
        User::create([
            'name' => 'Dr. Dosen Test',
            'email' => 'dosen@test.com',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => '198501012010012001',
            'nim' => null, // Dosen tidak punya NIM
        ]);

        // Update beberapa user yang sudah ada menjadi mahasiswa
        User::where('role', null)->update(['role' => 'mahasiswa']);
        
        // Pastikan user yang sudah ada memiliki role
        User::whereNull('role')->update(['role' => 'mahasiswa']);
    }
}

