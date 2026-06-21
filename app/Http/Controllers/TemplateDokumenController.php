<?php

namespace App\Http\Controllers;

use App\Services\TemplateDownloadLinkService;
use Illuminate\Http\RedirectResponse;

class TemplateDokumenController extends Controller
{
    public function index()
    {
        $templates = config('template_dokumen.templates', []);

        return view('pages.template-dokumen', compact('templates'));
    }

    public function download(string $key, TemplateDownloadLinkService $linkService): RedirectResponse
    {
        $template = collect(config('template_dokumen.templates', []))
            ->firstWhere('key', $key);

        if (!is_array($template)) {
            abort(404);
        }

        $downloadUrl = $template['download_url'] ?? '';

        if (!$linkService->isAvailable($downloadUrl)) {
            return redirect()
                ->back()
                ->with('error', 'Link unduh template tidak tersedia saat ini.');
        }

        return redirect()->away($downloadUrl);
    }
}
