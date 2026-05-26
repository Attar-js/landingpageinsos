<?php

namespace App\Http\Controllers;

class TemplateDokumenController extends Controller
{
    public function index()
    {
        $templates = config('template_dokumen.templates', []);

        return view('pages.template-dokumen', compact('templates'));
    }
}
