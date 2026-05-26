@extends('layout.layout')

@section('content')
<style>
    .validasi-page {
        max-width: 1100px;
        margin: 0 auto;
        font-size: 1.1rem;
    }

    .page-header-block {
        text-align: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2.75rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 12px;
    }

    .page-subtitle {
        font-size: 1.25rem;
        color: #6b7280;
        max-width: 820px;
        margin: 0 auto;
        line-height: 1.55;
    }

    .group-validasi-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #fff;
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    }

    .group-validasi-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid #f3f4f6;
        flex-wrap: wrap;
    }

    .group-validasi-header h3 {
        font-size: 1.45rem;
        font-weight: 700;
        color: #111827;
        margin: 0 0 6px;
    }

    .group-validasi-header p {
        margin: 0;
        font-size: 1.1rem;
        color: #6b7280;
    }

    .validation-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 1rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .validation-badge.menunggu {
        background: #ffedd5;
        color: #c2410c;
    }

    .validation-badge.disetujui {
        background: #d1fae5;
        color: #047857;
    }

    .validation-badge.revisi {
        background: #fee2e2;
        color: #b91c1c;
    }

    .proposal-section {
        padding: 0 24px 8px;
    }

    .proposal-section .doc-item-card {
        max-width: 420px;
    }

    .docs-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        padding: 20px 24px;
    }

    @media (max-width: 991px) {
        .docs-grid {
            grid-template-columns: 1fr;
        }
    }

    .doc-item-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        background: #fafafa;
        display: flex;
        flex-direction: column;
        min-height: 260px;
    }

    .doc-item-card.approved {
        border-color: #a7f3d0;
        background: #f0fdf4;
    }

    .doc-item-card.rejected {
        border-color: #fecaca;
        background: #fff7f7;
    }

    .doc-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 12px;
    }

    .doc-item-label {
        font-size: 1.05rem;
        font-weight: 700;
        color: #374151;
    }

    .doc-status-pill {
        font-size: 0.85rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .doc-status-pill.menunggu {
        background: #ffedd5;
        color: #c2410c;
    }

    .doc-status-pill.disetujui {
        background: #d1fae5;
        color: #047857;
    }

    .doc-status-pill.revisi {
        background: #fee2e2;
        color: #b91c1c;
    }

    .doc-file-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        flex: 1;
    }

    .doc-file-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #dbeafe;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .doc-file-icon.video {
        background: #fce7f3;
        color: #db2777;
    }

    .doc-file-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
        word-break: break-word;
    }

    .doc-file-meta {
        font-size: 1rem;
        color: #6b7280;
        word-break: break-all;
    }

    .doc-action-btn {
        margin-top: 14px;
        width: 100%;
        padding: 11px 16px;
        border-radius: 10px;
        font-size: 1.05rem;
        font-weight: 600;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        color: #374151;
        text-align: center;
        text-decoration: none;
        display: block;
        transition: all 0.2s ease;
    }

    .doc-action-btn:hover:not(.disabled) {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .doc-action-btn.disabled {
        opacity: 0.55;
        cursor: not-allowed;
        pointer-events: none;
    }

    .doc-review-footer {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .doc-note-input {
        font-size: 0.95rem;
        border-radius: 8px;
        min-height: 40px;
    }

    .doc-review-actions {
        display: flex;
        gap: 8px;
    }

    .doc-review-actions form {
        margin: 0;
        flex: 1;
    }

    .btn-revisi-sm,
    .btn-setuju-sm {
        width: 100%;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 10px 12px;
        border-radius: 8px;
        border: none;
    }

    .btn-revisi-sm {
        background: #dc3545;
        color: #fff;
    }

    .btn-revisi-sm:hover {
        background: #c82333;
        color: #fff;
    }

    .btn-setuju-sm {
        background: #28a745;
        color: #fff;
    }

    .btn-setuju-sm:hover:not(:disabled) {
        background: #218838;
        color: #fff;
    }

    .btn-setuju-sm:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .doc-file-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 14px;
    }

    .btn-hapus-dokumen {
        width: 100%;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #fecaca;
        background: #fff;
        color: #b91c1c;
    }

    .btn-hapus-dokumen:hover {
        background: #fee2e2;
        color: #991b1b;
    }

    .doc-approved-label {
        text-align: center;
        font-weight: 700;
        color: #047857;
        font-size: 1rem;
        padding: 10px 0;
    }

    .doc-revision-note {
        font-size: 0.95rem;
        color: #9a3412;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 8px;
        padding: 8px 10px;
    }

    .group-summary-footer {
        border-top: 1px solid #e5e7eb;
        padding: 14px 24px;
        background: #f9fafb;
        font-size: 1rem;
        color: #6b7280;
        text-align: center;
    }

    .empty-state {
        text-align: center;
        padding: 60px 24px;
        color: #9ca3af;
        font-size: 1.2rem;
    }
</style>

<x-header/>
<div style="height: 140px;"></div>

<div class="container mb-5 validasi-page">
    <div class="page-header-block">
        <h2 class="page-title">Validasi Dokumen Mahasiswa</h2>
        <p class="page-subtitle">
            Setujui atau minta revisi proposal dan luaran per dokumen. Mahasiswa melihat status di Form Daftar Kelompok dan halaman upload luaran.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="font-size: 1.1rem;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" style="font-size: 1.1rem;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($groupCards as $card)
        <div class="group-validasi-card">
            <div class="group-validasi-header">
                <div>
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['subtitle'] }}</p>
                </div>
                <span class="validation-badge {{ $card['status_key'] }}">
                    @if($card['status_key'] === 'disetujui')
                        <i class="fas fa-check-circle"></i>
                    @elseif($card['status_key'] === 'menunggu')
                        <i class="fas fa-clock"></i>
                    @else
                        <i class="fas fa-exclamation-circle"></i>
                    @endif
                    {{ $card['status_label'] }}
                </span>
            </div>

            @if($card['proposal'] ?? null)
                <div class="proposal-section">
                    @include('dosen.partials.doc-validasi-card', [
                        'doc' => $card['proposal'],
                        'groupId' => $card['group_id'],
                        'approveRoute' => route('dosen.validasi-dokumen.proposal.approve', $card['group_id']),
                        'rejectRoute' => route('dosen.validasi-dokumen.proposal.reject', $card['group_id']),
                        'showDelete' => false,
                    ])
                </div>
            @endif

            <div class="docs-grid">
                @foreach(\App\Services\GroupDocumentStatusService::DOC_KEYS as $docKey)
                    @include('dosen.partials.doc-validasi-card', [
                        'doc' => $card['documents'][$docKey],
                        'groupId' => $card['group_id'],
                        'approveRoute' => route('dosen.validasi-dokumen.dokumen.approve', [$card['group_id'], $docKey]),
                        'rejectRoute' => route('dosen.validasi-dokumen.dokumen.reject', [$card['group_id'], $docKey]),
                        'deleteRoute' => route('dosen.validasi-dokumen.dokumen.delete', [$card['group_id'], $docKey]),
                        'showDelete' => true,
                    ])
                @endforeach
            </div>

            @if($card['is_fully_approved'] ?? false)
                <div class="group-summary-footer">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    Semua dokumen luaran kelompok ini telah disetujui.
                </div>
            @endif
        </div>
    @empty
        <div class="group-validasi-card">
            <div class="empty-state">
                <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-50"></i>
                Belum ada kelompok aktif di bawah bimbingan Anda.
            </div>
        </div>
    @endforelse
</div>
@endsection
