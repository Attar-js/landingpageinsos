@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
<style>
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .btn-primary {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    #dropzone, #luaranDropzone {
        transition: all 0.3s ease;
    }

    #dropzone:hover, #luaranDropzone:hover {
        border-color: #0d6efd !important;
        background-color: #f8f9fa !important;
    }

    .upload-status-card {
        border-radius: 12px;
        padding: 18px 20px;
        margin: 0 1rem 1.25rem;
        font-size: 1.1rem;
    }

    .upload-status-card .status-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .upload-status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 1rem;
    }

    .upload-status-note {
        margin-top: 12px;
        padding: 12px 14px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.65);
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .doc-status-list {
        margin-top: 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .doc-status-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.06);
        font-size: 1rem;
    }

    .doc-status-row .pill {
        font-size: 0.9rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .doc-status-row .pill.menunggu { background: #ffedd5; color: #c2410c; }
    .doc-status-row .pill.disetujui { background: #d1fae5; color: #047857; }
    .doc-status-row .pill.revisi { background: #fee2e2; color: #b91c1c; }

    .field-revision-hint {
        font-size: 0.95rem;
        color: #b45309;
        margin-top: 6px;
    }
</style>
    <x-header/>
    <div style="height: 140px;"></div>

    <div class="container">
    <div class="card shadow" style="max-width: 800px; margin: 0 auto;">
        <div class="section-title text-center py-3">
            <h4 class="titleform">Form Laporan Akhir dan Luaran</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($uploadStatus ?? null)
            <div class="upload-status-card alert alert-{{ $uploadStatus['badge_class'] }} mb-0">
                <div class="status-title">Status Validasi Dosen — {{ $uploadStatus['group_name'] }}</div>
                <div class="mb-2 text-muted">Dosen pembimbing: <strong>{{ $uploadStatus['dosen_name'] }}</strong></div>
                <span class="upload-status-badge bg-{{ $uploadStatus['badge_class'] }} text-white">
                    @if($uploadStatus['ui_status'] === 'revisi')
                        <i class="fas fa-exclamation-circle me-1"></i>
                    @elseif($uploadStatus['ui_status'] === 'disetujui')
                        <i class="fas fa-check-circle me-1"></i>
                    @elseif($uploadStatus['ui_status'] === 'menunggu')
                        <i class="fas fa-clock me-1"></i>
                    @else
                        <i class="fas fa-upload me-1"></i>
                    @endif
                    {{ $uploadStatus['status_label'] }}
                </span>

                @if(!empty($uploadStatus['documents']))
                    <div class="doc-status-list">
                        @foreach($uploadStatus['documents'] as $doc)
                            <div class="doc-status-row">
                                <span><strong>{{ $doc['label'] }}</strong></span>
                                <span class="pill {{ $doc['status_class'] ?? 'menunggu' }}">{{ $doc['status_label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($uploadStatus['ui_status'] === 'revisi' && !empty($uploadStatus['revision_notes']))
                    <div class="upload-status-note">
                        <strong>Catatan revisi dari dosen:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($uploadStatus['revision_notes'] as $note)
                                <li><strong>{{ $note['label'] }}:</strong> {{ $note['note'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @elseif($uploadStatus['ui_status'] === 'revisi')
                    <div class="upload-status-note">
                        Dosen meminta revisi pada satu atau lebih dokumen. Form di bawah hanya menampilkan field yang perlu diperbaiki.
                    </div>
                @elseif($uploadStatus['ui_status'] === 'menunggu')
                    <div class="upload-status-note">
                        Dokumen Anda sedang ditinjau dosen pembimbing. Mohon tunggu hasil validasi.
                    </div>
                @elseif($uploadStatus['ui_status'] === 'disetujui')
                    <div class="upload-status-note">
                        Seluruh dokumen luaran telah disetujui dosen pembimbing.
                        @if($uploadStatus['reviewed_at'])
                            <br><small>Disetujui pada {{ $uploadStatus['reviewed_at']->timezone(config('app.timezone'))->format('d M Y H:i') }}</small>
                        @endif
                    </div>
                @elseif($uploadStatus['ui_status'] === 'kelompok_pending')
                    <div class="upload-status-note">
                        Upload laporan akhir dan luaran dapat dilakukan setelah dosen pembimbing menyetujui pendaftaran kelompok Anda.
                    </div>
                @endif
            </div>
        @else
            <div class="alert alert-warning mx-4">
                Anda belum terdaftar sebagai anggota aktif pada kelompok manapun.
                Silakan daftar kelompok terlebih dahulu melalui menu <strong>Form Daftar Kelompok</strong>.
            </div>
        @endif

        @php
            $formDocs = $uploadStatus['documents'] ?? [];
            $laporanDoc = $formDocs[\App\Models\GroupDocumentReview::DOC_LAPORAN] ?? [];
            $artikelDoc = $formDocs[\App\Models\GroupDocumentReview::DOC_ARTIKEL] ?? [];
            $videoDoc = $formDocs[\App\Models\GroupDocumentReview::DOC_VIDEO] ?? [];

            $showLaporan = ($laporanDoc['can_edit'] ?? false)
                || (!($laporanDoc['uploaded'] ?? false) && filled($laporanDoc['review_note'] ?? null));
            $showArtikel = ($artikelDoc['can_edit'] ?? false)
                || (!($artikelDoc['uploaded'] ?? false) && filled($artikelDoc['review_note'] ?? null));
            $showVideo = ($videoDoc['can_edit'] ?? false)
                || (!($videoDoc['uploaded'] ?? false) && filled($videoDoc['review_note'] ?? null));
            $showLuaranFields = $showArtikel || $showVideo;
            $isRevisionUpload = $uploadStatus['is_revision_upload'] ?? false;
            $groupJudul = $uploadStatus['judul_kegiatan'] ?? '';
        @endphp

        @if(($uploadStatus['can_submit'] ?? false) && ($uploadStatus ?? null))
        <form id="kknForm" action="{{ route('laporan-akhir.store') }}" method="POST" enctype="multipart/form-data" novalidate class="px-4 pb-4"
              data-needs-laporan="{{ $showLaporan ? '1' : '0' }}"
              data-needs-artikel="{{ $showArtikel ? '1' : '0' }}"
              data-needs-video="{{ $showVideo ? '1' : '0' }}"
              data-is-revision="{{ $isRevisionUpload ? '1' : '0' }}">
            @csrf
            <input type="hidden" name="user_nim" value="{{ Auth::user()->nim ?? '' }}">
            <input type="hidden" name="judul_kegiatan" value="{{ old('judul_kegiatan', $groupJudul) }}">

            @if(!$isRevisionUpload)
                <div class="mb-4">
                    <label for="judul_kegiatan" class="form-label fw-bold">Judul Kegiatan</label>
                    <input type="text"
                           class="form-control"
                           id="judul_kegiatan"
                           name="judul_kegiatan"
                           value="{{ old('judul_kegiatan', $groupJudul) }}"
                           placeholder="Masukkan judul kegiatan KKN"
                           required>
                    <small class="text-muted">Masukkan judul kegiatan KKN yang telah dilaksanakan</small>
                </div>
            @endif

            @if($showVideo)
            <div class="mb-4">
                <label for="video_aftermovie" class="form-label fw-bold">Video Aftermovie</label>
                @if(!empty($formDocs[\App\Models\GroupDocumentReview::DOC_VIDEO]['review_note']))
                    <div class="field-revision-hint">{{ $formDocs[\App\Models\GroupDocumentReview::DOC_VIDEO]['review_note'] }}</div>
                @endif
                <input type="url"
                       class="form-control @error('video_aftermovie') is-invalid @enderror"
                       id="video_aftermovie"
                       name="video_aftermovie"
                       value="{{ old('video_aftermovie', $formDocs[\App\Models\GroupDocumentReview::DOC_VIDEO]['existing_video_url'] ?? '') }}"
                       placeholder="Tautan Video Aftermovie"
                       required>
                @error('video_aftermovie')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif

            @if($showArtikel)
            <div class="mb-4">
                <label for="artikel_link" class="form-label fw-bold">Tautan Artikel (laman ITK)</label>
                @if(!empty($formDocs[\App\Models\GroupDocumentReview::DOC_ARTIKEL]['review_note']))
                    <div class="field-revision-hint">{{ $formDocs[\App\Models\GroupDocumentReview::DOC_ARTIKEL]['review_note'] }}</div>
                @endif
                <input type="url"
                       class="form-control @error('artikel_link') is-invalid @enderror"
                       id="artikel_link"
                       name="artikel_link"
                       value="{{ old('artikel_link', $formDocs[\App\Models\GroupDocumentReview::DOC_ARTIKEL]['existing_artikel_link'] ?? '') }}"
                       placeholder="Tautan Artikel di laman ITK"
                       required>
                @error('artikel_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif

            @if($showLaporan || $showArtikel)
            <div class="card shadow-sm mt-4 mb-4" style="max-width: 680px; margin: 0 auto;">
                <div class="card-header text-center py-3">
                    <strong>{{ $showLaporan && $showArtikel ? 'Upload Dokumen Akhir' : ($showLaporan ? 'Upload Laporan Akhir' : 'Upload File Artikel') }}</strong>
                </div>
                <div class="card-body p-4">
                    @if($showLaporan)
                    <label for="validatedCustomFile" class="form-label fw-bold">Upload Laporan Akhir</label>
                    @if(!empty($formDocs[\App\Models\GroupDocumentReview::DOC_LAPORAN]['review_note']))
                        <div class="field-revision-hint mb-2">{{ $formDocs[\App\Models\GroupDocumentReview::DOC_LAPORAN]['review_note'] }}</div>
                    @endif
                    <div id="dropzone" class="mb-4 p-4 border-2 border-dashed border-secondary rounded bg-light text-center" style="cursor: pointer;">
                        <input type="file" id="validatedCustomFile" name="file" class="form-control d-none" required>
                        <div class="mb-2">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                        </div>
                        <div id="dropzoneText" class="text-muted">Seret dan lepas file PDF di sini atau klik untuk memilih file</div>
                        <div class="invalid-feedback mt-2 d-none" id="fileError">File laporan akhir wajib diunggah.</div>
                    </div>
                    @endif

                    @if($showArtikel)
                    <label for="artikelFileInput" class="form-label fw-bold">Upload File Artikel</label>
                    <div id="luaranDropzone" class="p-4 border-2 border-dashed border-secondary rounded bg-light text-center" style="cursor: pointer;">
                        <input type="file" id="artikelFileInput" name="artikel_file" class="form-control d-none" required>
                        <div class="mb-2">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                        </div>
                        <div id="luaranDropzoneText" class="text-muted">Seret dan lepas file PDF/DOC/DOCX di sini atau klik untuk memilih file</div>
                        <div class="invalid-feedback mt-2 d-none" id="luaranFileError">File artikel wajib diunggah.</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Submit Button -->
            <div class="mt-3">
                <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold w-100">
                    <i class="fas fa-paper-plane me-2"></i>
                    Submit Form
                </button>
            </div>
        </form>
        @elseif(($uploadStatus ?? null) && !($uploadStatus['can_submit'] ?? false))
            <div class="px-4 pb-4 text-center text-muted" style="font-size: 1.1rem;">
                Form upload tidak tersedia pada status saat ini.
            </div>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('kknForm');
                if (!form) return;

                const needsLaporan = form.dataset.needsLaporan === '1';
                const needsArtikel = form.dataset.needsArtikel === '1';
                const needsVideo = form.dataset.needsVideo === '1';
                const isRevision = form.dataset.isRevision === '1';

                const dropzone = document.getElementById('dropzone');
                const fileInput = document.getElementById('validatedCustomFile');
                const dropzoneText = document.getElementById('dropzoneText');
                const fileError = document.getElementById('fileError');
                const luaranDropzone = document.getElementById('luaranDropzone');
                const luaranFileInput = document.getElementById('artikelFileInput');
                const luaranDropzoneText = document.getElementById('luaranDropzoneText');
                const luaranFileError = document.getElementById('luaranFileError');
                const allowedLuaranTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                const maxSize = 10 * 1024 * 1024;

                if (dropzone && fileInput) dropzone.addEventListener('click', () => fileInput.click());

                if (fileInput) fileInput.addEventListener('change', () => {
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        // Validasi ekstensi dan ukuran
                        if (file.type !== 'application/pdf' || file.size > maxSize) {
                            dropzoneText.innerText = 'Hanya file PDF maksimal 10 MB yang diperbolehkan!';
                            dropzone.classList.add('border-danger');
                            fileError.classList.remove('d-none');
                            fileInput.value = '';
                            return;
                        }
                        dropzoneText.innerText = file.name;
                        dropzone.classList.remove('border-danger');
                        fileError.classList.add('d-none');
                    }
                });

                if (dropzone) dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('bg-primary', 'text-white');
                    dropzoneText.innerText = 'Lepaskan file di sini';
                });

                if (dropzone) dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('bg-primary', 'text-white');
                    dropzoneText.innerText = fileInput.files.length > 0
                        ? fileInput.files[0].name
                        : 'Seret dan lepas file di sini atau klik untuk memilih';
                });

                if (dropzone) dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('bg-primary', 'text-white');
                    fileInput.files = e.dataTransfer.files;
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        // Validasi ekstensi dan ukuran
                        if (file.type !== 'application/pdf' || file.size > maxSize) {
                            dropzoneText.innerText = 'Hanya file PDF maksimal 10 MB yang diperbolehkan!';
                            dropzone.classList.add('border-danger');
                            fileError.classList.remove('d-none');
                            fileInput.value = '';
                            return;
                        }
                        dropzoneText.innerText = file.name;
                        dropzone.classList.remove('border-danger');
                        fileError.classList.add('d-none');
                    }
                });

                if (luaranDropzone && luaranFileInput) luaranDropzone.addEventListener('click', () => luaranFileInput.click());

                if (luaranFileInput) luaranFileInput.addEventListener('change', () => {
                    if (luaranFileInput.files.length > 0) {
                        const file = luaranFileInput.files[0];
                        if (!allowedLuaranTypes.includes(file.type) || file.size > maxSize) {
                            luaranDropzoneText.innerText = 'Hanya file PDF/DOC/DOCX maksimal 10 MB yang diperbolehkan!';
                            luaranDropzone.classList.add('border-danger');
                            luaranFileError.classList.remove('d-none');
                            luaranFileInput.value = '';
                            return;
                        }
                        luaranDropzoneText.innerText = file.name;
                        luaranDropzone.classList.remove('border-danger');
                        luaranFileError.classList.add('d-none');
                    }
                });

                if (luaranDropzone) luaranDropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    luaranDropzone.classList.add('bg-primary', 'text-white');
                    luaranDropzoneText.innerText = 'Lepaskan file di sini';
                });

                if (luaranDropzone) luaranDropzone.addEventListener('dragleave', () => {
                    luaranDropzone.classList.remove('bg-primary', 'text-white');
                    luaranDropzoneText.innerText = luaranFileInput.files.length > 0
                        ? luaranFileInput.files[0].name
                        : 'Seret dan lepas file PDF/DOC/DOCX di sini atau klik untuk memilih';
                });

                if (luaranDropzone) luaranDropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    luaranDropzone.classList.remove('bg-primary', 'text-white');
                    luaranFileInput.files = e.dataTransfer.files;
                    luaranFileInput.dispatchEvent(new Event('change'));
                });

                form.addEventListener('submit', function (e) {
                    if (!isRevision) {
                        const judulInput = document.getElementById('judul_kegiatan');
                        const judulKegiatan = judulInput ? judulInput.value.trim() : '';
                        if (!judulKegiatan) {
                            e.preventDefault();
                            alert('Judul kegiatan harus diisi!');
                            judulInput?.focus();
                            return;
                        }
                    }

                    if (needsLaporan && fileInput) {
                        if (fileInput.files.length === 0) {
                            e.preventDefault();
                            dropzone.classList.add('border-danger');
                            fileError.classList.remove('d-none');
                            dropzoneText.innerText = 'File wajib diunggah!';
                            return;
                        }
                        const file = fileInput.files[0];
                        if (file.type !== 'application/pdf' || file.size > maxSize) {
                            e.preventDefault();
                            dropzone.classList.add('border-danger');
                            fileError.classList.remove('d-none');
                            dropzoneText.innerText = 'Hanya file PDF maksimal 10 MB yang diperbolehkan!';
                            return;
                        }
                    }

                    if (needsArtikel && luaranFileInput) {
                        if (luaranFileInput.files.length === 0) {
                            e.preventDefault();
                            luaranDropzone.classList.add('border-danger');
                            luaranFileError.classList.remove('d-none');
                            luaranDropzoneText.innerText = 'File artikel wajib diunggah!';
                            return;
                        }
                        const luaranFile = luaranFileInput.files[0];
                        if (!allowedLuaranTypes.includes(luaranFile.type) || luaranFile.size > maxSize) {
                            e.preventDefault();
                            luaranDropzone.classList.add('border-danger');
                            luaranFileError.classList.remove('d-none');
                            luaranDropzoneText.innerText = 'Hanya file PDF/DOC/DOCX maksimal 10 MB yang diperbolehkan!';
                        }
                    }

                    if (needsVideo) {
                        const videoInput = document.getElementById('video_aftermovie');
                        if (!videoInput || !videoInput.value.trim()) {
                            e.preventDefault();
                            alert('Tautan video aftermovie wajib diisi!');
                        }
                    }
                });
            });
        </script>
    </div>
</div>

<style>
.form-label {
    color: #495057;
    font-weight: 600;
}

.form-control {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.75rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.section-title h4 {
    color: #2c3e50;
    font-weight: 700;
}

.border-dashed {
    border-style: dashed !important;
}
</style>

@endsection

