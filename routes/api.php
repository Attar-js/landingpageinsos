<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\DosenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API untuk tim penciri di dashboard
Route::get('/groups/status-verifikasi', [GroupController::class, 'getStatusVerifikasi']);
Route::get('/dosen/list', [DosenController::class, 'getDosenList']);
Route::post('/dosen/assign-group', [DosenController::class, 'receiveAssignment']); 
