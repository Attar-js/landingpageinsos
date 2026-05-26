<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create user with NIM 10221051
        User::create([
            'nim' => '10221051',
            'username' => '10221051',
            'first_name' => 'Mahasiswa',
            'last_name' => 'ITK',
            'email' => '10221051@itk.ac.id',
            'password' => Hash::make('Mamoru123'),
            'user_type' => 'user',
            'role' => 'mahasiswa',
            'status' => 'active',
        ]);
    }
}

