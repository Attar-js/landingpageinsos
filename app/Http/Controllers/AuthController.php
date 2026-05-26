<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\DashboardHelper;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke landing page
        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }
        
        return view('auth.login');
    }

    /**
     * Menangani proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Log untuk debugging remember me
        \Log::info('Login attempt', [
            'username' => $request->username,
            'remember_me' => $request->has('remember'),
            'remember_value' => $request->input('remember')
        ]);

        // Coba login dengan NIM
        $user = User::where('nim', $request->username)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            // Gunakan remember me jika checkbox dicentang
            $remember = $request->has('remember');
            Auth::login($user, $remember);
            $request->session()->regenerate();
            
            \Log::info('Login successful with NIM', [
                'user_id' => $user->id,
                'user_nim' => $user->nim,
                'remember_me' => $remember,
                'remember_token' => $user->remember_token
            ]);
            
            return $this->redirectAfterLogin()
                ->with('success', 'Selamat datang! Anda berhasil login.');
        }

        // Jika gagal dengan NIM, coba dengan email
        $user = User::where('email', $request->username)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            // Gunakan remember me jika checkbox dicentang
            $remember = $request->has('remember');
            Auth::login($user, $remember);
            $request->session()->regenerate();
            
            \Log::info('Login successful with email', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'remember_me' => $remember,
                'remember_token' => $user->remember_token
            ]);
            
            return $this->redirectAfterLogin()
                ->with('success', 'Selamat datang! Anda berhasil login.');
        }

        \Log::warning('Login failed', [
            'username' => $request->username,
            'ip_address' => $request->ip()
        ]);

        return back()->withErrors([
            'username' => 'NIM atau password salah.',
        ])->withInput($request->only('username'));
    }

    /**
     * Menangani proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Redirect user after login based on role.
     */
    protected function redirectAfterLogin()
    {
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->away(DashboardHelper::getDashboardUrl());
        }

        return redirect()->intended(route('landing'));
    }

    /**
     * Menampilkan halaman forgot password
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Menangani request forgot password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // TODO: Implementasi reset password
        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }
} 
