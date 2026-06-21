<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TemplateDownloadLinkService
{
    public function isAvailable(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        try {
            $response = Http::timeout(10)->get($url);

            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}
