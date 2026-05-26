@extends('layout.layout')

@section('content')
<style>
    .template-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1rem 3rem;
    }

    .template-page-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-top: 0.5rem;
    }

    .template-page-header h1 {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 800;
        margin-bottom: 0.75rem;
        background: linear-gradient(90deg, #0038F0, #9C27B0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .template-page-header p {
        color: #6b7280;
        font-size: 1.4rem;
        margin: 0;
    }

    .template-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }

    @media (max-width: 768px) {
        .template-grid {
            grid-template-columns: 1fr;
        }
    }

    .template-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.5rem 1.6rem;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .template-card:hover {
        border-color: #c7d2fe;
        box-shadow: 0 6px 18px rgba(59, 130, 246, 0.1);
    }

    .template-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.65rem;
        flex-shrink: 0;
    }

    .template-card-body {
        flex: 1;
        min-width: 0;
    }

    .template-card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        margin: 0 0 0.35rem;
        line-height: 1.35;
    }

    .template-card-meta {
        font-size: 1.15rem;
        color: #9ca3af;
        margin: 0;
    }

    .template-card-actions {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .template-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 84px;
        padding: 0.65rem 1.2rem;
        border-radius: 10px;
        font-size: 1.15rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .template-btn-lihat {
        background: #f3f4f6;
        color: #374151;
        border-color: #e5e7eb;
    }

    .template-btn-lihat:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .template-btn-unduh {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .template-btn-unduh:hover {
        background: #dbeafe;
        color: #1e40af;
    }

    @media (max-width: 576px) {
        .template-card {
            flex-wrap: wrap;
        }

        .template-card-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<x-header/>
<div style="height: 140px;"></div>

<div class="template-page">
    <div class="template-page-header">
        <h1>Template Dokumen</h1>
        <p>Download template dokumen untuk kegiatan Inovasi Sosial</p>
    </div>

    <div class="template-grid">
        @foreach($templates as $template)
            <div class="template-card" id="template-{{ $template['key'] }}">
                <div class="template-card-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="template-card-body">
                    <h3 class="template-card-title">{{ $template['title'] }}</h3>
                    <p class="template-card-meta">{{ $template['meta'] }}</p>
                </div>
                <div class="template-card-actions">
                    <a href="{{ $template['view_url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="template-btn template-btn-lihat">
                        Lihat
                    </a>
                    <a href="{{ $template['download_url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="template-btn template-btn-unduh">
                        Unduh
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
