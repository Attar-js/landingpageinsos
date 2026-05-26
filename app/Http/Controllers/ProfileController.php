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
}
