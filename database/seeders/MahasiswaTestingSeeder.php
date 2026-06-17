<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaTestingSeeder extends Seeder
{
    /**
     * Akun mahasiswa untuk keperluan testing.
     * Login menggunakan NIM + password.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $mahasiswaList = [
            ['name' => 'Nur Fadillah',   'nim' => '10221019'],
            ['name' => 'Adji Muhammad',  'nim' => '10221012'],
            ['name' => 'Maria Siregar',  'nim' => '10221004'],
            ['name' => 'Taufik Ilham',   'nim' => '10221081'],
            ['name' => 'Hardi Wira',     'nim' => '10221049'],
            ['name' => 'Qudus',          'nim' => '10221048'],
            ['name' => 'Faiq Athari',    'nim' => '10221052'],
            ['name' => 'Nandha Aulia',   'nim' => '10221071'],
            ['name' => 'Muhammad Aulia', 'nim' => '10221055'],
            ['name' => 'Sheva Aryo',     'nim' => '10221088'],
        ];

        foreach ($mahasiswaList as $mahasiswa) {
            $nameParts = explode(' ', trim($mahasiswa['name']), 2);

            User::updateOrCreate(
                ['nim' => $mahasiswa['nim']],
                [
                    'name'         => $mahasiswa['name'],
                    'first_name'   => $nameParts[0] ?? $mahasiswa['name'],
                    'last_name'    => $nameParts[1] ?? null,
                    'username'     => $mahasiswa['nim'],
                    'email'        => $mahasiswa['nim'] . '@student.test',
                    'password'     => $password,
                    'role'         => 'mahasiswa',
                    'user_type'    => 'mahasiswa',
                    'status'       => 'active',
                ]
            );
        }

        $this->command->info('Berhasil membuat ' . count($mahasiswaList) . ' akun mahasiswa untuk testing.');
        $this->command->info('Login: NIM masing-masing | Password: password');
    }
}
