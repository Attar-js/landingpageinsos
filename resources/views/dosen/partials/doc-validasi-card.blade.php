@php
    $doc = $doc ?? [];
    $groupId = $groupId ?? null;
    $approveRoute = $approveRoute ?? null;
    $rejectRoute = $rejectRoute ?? null;
    $showDelete = $showDelete ?? false;
    $docKey = $doc['key'] ?? 'doc';
    $statusClass = $doc['status_class'] ?? 'menunggu';
    $canReview = ($doc['can_review'] ?? false);
@endphp
<div class="doc-item-card {{ $statusClass === 'disetujui' ? 'approved' : ($statusClass === 'revisi' ? 'rejected' : '') }}">
    <div class="doc-item-header">
        <div class="doc-item-label">{{ $doc['label'] ?? 'Dokumen' }}</div>
        <span class="doc-status-pill {{ $statusClass }}">{{ $doc['status_label'] ?? 'Menunggu' }}</span>
    </div>

    <div class="doc-file-row">
        <div class="doc-file-icon {{ ($doc['action_type'] ?? '') === 'view' ? 'video' : '' }}">
            <i class="fas {{ ($doc['action_type'] ?? '') === 'view' ? 'fa-play' : 'fa-file-alt' }}"></i>
        </div>
        <div>
            <div class="doc-file-title">{{ $doc['title'] ?? '-' }}</div>
            <div class="doc-file-meta">{{ $doc['meta'] ?? '-' }}</div>
        </div>
    </div>

    <div class="doc-file-actions">
        @if($doc['uploaded'] && ($doc['view_url'] || $doc['download_url']))
            @if(($doc['action_type'] ?? '') === 'view')
                <a href="{{ $doc['view_url'] }}" target="_blank" rel="noopener" class="doc-action-btn">
                    {{ $doc['action_label'] ?? 'Lihat' }}
                </a>
            @else
                @if($doc['view_url'])
                    <a href="{{ $doc['view_url'] }}" target="_blank" rel="noopener" class="doc-action-btn">
                        Lihat
                    </a>
                @endif
                @if($doc['download_url'])
                    <a href="{{ $doc['download_url'] }}" class="doc-action-btn">
                        {{ $doc['action_label'] ?? 'Unduh' }}
                    </a>
                @endif
            @endif
            @if($showDelete && ($deleteRoute ?? null))
                <form action="{{ $deleteRoute }}" method="POST" class="m-0 doc-delete-form">
                    @csrf
                    <button type="submit"
                            class="btn btn-hapus-dokumen"
                            onclick="return confirm('Hapus {{ $doc['label'] }}? Mahasiswa akan diminta mengunggah ulang.');">
                        <i class="fas fa-trash-alt me-1"></i>
                        {{ ($doc['action_type'] ?? '') === 'view' ? 'Hapus Tautan' : 'Hapus Dokumen' }}
                    </button>
                </form>
            @endif
        @else
            <span class="doc-action-btn disabled">{{ $doc['action_label'] ?? 'Belum tersedia' }}</span>
        @endif
    </div>

    @if(!empty($doc['review_note']) && ($doc['needs_revision'] ?? false))
        <div class="doc-revision-note">
            <strong>Catatan:</strong> {{ $doc['review_note'] }}
        </div>
    @endif

    <div class="doc-review-footer">
        @if($canReview && $approveRoute && $rejectRoute)
            @php $rejectFormId = 'reject-' . $groupId . '-' . $docKey; @endphp
            <div class="doc-review-panel">
                <input type="text"
                       name="note"
                       form="{{ $rejectFormId }}"
                       class="form-control doc-note-input"
                       placeholder="Catatan revisi (opsional)">
                <div class="doc-review-buttons">
                    <form id="{{ $rejectFormId }}" action="{{ $rejectRoute }}" method="POST" class="doc-review-form">
                        @csrf
                        <button type="submit"
                                class="btn btn-revisi-sm"
                                onclick="return confirm('Minta revisi untuk {{ $doc['label'] }}?');">
                            Revisi
                        </button>
                    </form>
                    <form action="{{ $approveRoute }}" method="POST" class="doc-review-form">
                        @csrf
                        <button type="submit" class="btn btn-setuju-sm">
                            Setujui
                        </button>
                    </form>
                </div>
            </div>
        @elseif($doc['is_approved'] ?? false)
            <div class="doc-approved-label">
                <i class="fas fa-check me-1"></i> Disetujui
            </div>
        @else
            <span class="doc-action-btn disabled">Belum dapat divalidasi</span>
        @endif
    </div>
</div>
