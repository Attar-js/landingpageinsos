<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class DashboardHelper
{
    public static function getDashboardUrl(string $path = ''): string
    {
        $baseUrl = Config::get('app.dashboard_url', 'http://localhost:8001');

        return $path === ''
            ? rtrim($baseUrl, '/')
            : rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    public static function getLandingUrl(string $path = ''): string
    {
        $baseUrl = Config::get('app.landing_url', Config::get('app.url', 'http://localhost:8000'));

        return $path === ''
            ? rtrim($baseUrl, '/')
            : rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    public static function getApiUrl(string $path = ''): string
    {
        return self::getDashboardUrl($path === '' ? 'api' : 'api/' . ltrim($path, '/'));
    }

    public static function getLandingApiUrl(string $path = ''): string
    {
        return self::getLandingUrl($path === '' ? 'api' : 'api/' . ltrim($path, '/'));
    }

    public static function getDashboardPageUrl(string $page): string
    {
        return self::getDashboardUrl($page);
    }

    public static function getPendaftarUrl(): string
    {
        return self::getDashboardUrl('special-pages/pendaftar');
    }

    public static function getProposalUrl(): string
    {
        return self::getDashboardUrl('special-pages/proposal');
    }

    public static function getLaporanAkhirUrl(): string
    {
        return self::getDashboardUrl('special-pages/laporan-akhir');
    }

    public static function getFormKesediaanUrl(): string
    {
        return self::getDashboardUrl('special-pages/form-kesediaan');
    }

    public static function getPeerReviewUrl(): string
    {
        return self::getDashboardUrl('special-pages/peer-review');
    }

    public static function getFileManagerUrl(): string
    {
        return self::getDashboardUrl('file-sync-manager');
    }

    public static function getNilaiAkhirUrl(): string
    {
        return self::getDashboardUrl('nilai-akhir');
    }

    public static function getNilaiAkhirReceiveUrl(): string
    {
        return self::getDashboardUrl('nilai-akhir/api/receive-nilai');
    }

    public static function getCurrentAppUrl(string $path = ''): string
    {
        return self::getLandingUrl($path);
    }

    public static function getFormUrl(): string
    {
        return self::getLandingUrl('kkn/form');
    }

    public static function getMahasiswaBimbinganUrl(): string
    {
        return self::getLandingUrl('dosen/mahasiswa-bimbingan');
    }

    public static function redirectToDashboard(string $path = '')
    {
        return redirect(self::getDashboardUrl($path));
    }

    public static function redirectToForm()
    {
        return redirect(self::getFormUrl());
    }

    public static function getConfig(): array
    {
        return [
            'current_app_url' => self::getCurrentAppUrl(),
            'landing_url' => self::getLandingUrl(),
            'dashboard_url' => self::getDashboardUrl(),
            'form_url' => self::getFormUrl(),
            'pendaftar_url' => self::getPendaftarUrl(),
            'file_manager_url' => self::getFileManagerUrl(),
            'nilai_akhir_url' => self::getNilaiAkhirUrl(),
        ];
    }
}
