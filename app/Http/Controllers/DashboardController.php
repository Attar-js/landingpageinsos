<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware auth akan diterapkan di route
    }

    /**
     * Menampilkan dashboard utama
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('dashboard.index', compact('user'));
    }

    /**
     * Menampilkan halaman utama setelah login
     */
    public function home()
    {
        $user = Auth::user();
        
        return view('dashboard.home', compact('user'));
    }

    /**
     * Menampilkan halaman landing page
     */
    public function onlineSchool()
    {
        $user = Auth::user();
        
        return view('home.landingpage', compact('user'));
    }
}

