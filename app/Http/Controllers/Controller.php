<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API Sistem Penilaian KKN (project-akhir)",
 *     version="1.0.0",
 *     description="Dokumentasi API untuk sistem penilaian KKN. Menyediakan endpoint untuk status verifikasi kelompok, daftar dosen, dan penugasan dosen ke kelompok."
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Server pengembangan lokal"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     description="Masukkan token autentikasi Sanctum di sini."
 * )
 */
abstract class Controller
{
    //
}

