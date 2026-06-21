<?php

namespace App\Http\Controllers;

use App\Models\KknAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $programStudi = $user->program_studi ?? null;
        if (empty($programStudi) && !empty($user->nim)) {
            $programStudi = KknAnggota::where('nim', $user->nim)
                ->value('program_studi');
        }

        return view('pages.profile', [
            'user' => $user,
            'programStudi' => $programStudi,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'program_studi' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:30',
        ], [
            'name.required' => 'Nama wajib diisi.',
        ]);

        $payload = [
            'name' => trim($validated['name']),
            'phone_number' => !empty($validated['phone_number']) ? trim($validated['phone_number']) : null,
        ];

        if (Schema::hasColumn('users', 'program_studi')) {
            $payload['program_studi'] = !empty($validated['program_studi']) ? trim($validated['program_studi']) : null;
        }

        $user->update($payload);

        if (!Schema::hasColumn('users', 'program_studi') && !empty($user->nim)) {
            KknAnggota::where('nim', $user->nim)->update([
                'program_studi' => !empty($validated['program_studi']) ? trim($validated['program_studi']) : null,
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('profile.show')->with('password_success', 'Password berhasil diperbarui.');
    }
}
