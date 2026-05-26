@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
    <x-header/>
    <div style="height: 140px;"></div>

    <div class="container">
    <div class="card shadow" style="max-width: 550px; margin: 0 auto;">
        <div class="section-title text-center py-3">
            <h4 class="titleform">Form Proposal Kegiatan</h4>
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

        @if($proposalStatus ?? null)
            <div class="alert alert-{{ $proposalStatus['badge_class'] }} mx-4">
                <strong>Status Proposal — {{ $proposalStatus['group_name'] }}</strong><br>
                {{ $proposalStatus['status_label'] }}
                @if(($proposalStatus['ui_status'] ?? '') === 'revisi' && !empty($proposalStatus['review_note']))
                    <div class="mt-2 p-2 rounded" style="background: rgba(255,255,255,0.6);">
                        <strong>Catatan dosen:</strong> {{ $proposalStatus['review_note'] }}
                    </div>
                @elseif(($proposalStatus['ui_status'] ?? '') === 'revisi')
                    <div class="mt-2">Silakan perbaiki dan unggah ulang proposal melalui form di bawah.</div>
                @endif
            </div>
        @endif

        @if(!($proposalStatus ?? null) || ($proposalStatus['can_reupload'] ?? false))
        <form id="kknForm" action="{{ route('proposal.store') }}" method="POST" enctype="multipart/form-data" novalidate class="px-4 pb-4">
            @csrf
            <input type="hidden" name="user_nim" value="{{ Auth::user()->nim ?? '' }}">
            
            <!-- Judul Kegiatan -->
            <div class="mb-4">
                <label for="judul_kegiatan" class="form-label fw-bold">Judul Kegiatan</label>
                <input type="text" 
                       class="form-control" 
                       id="judul_kegiatan" 
                       name="judul_kegiatan" 
                       placeholder="Masukkan judul kegiatan"
                       required>
                <small class="text-muted">Masukkan judul kegiatan yang akan dilaksanakan</small>
            </div>

            <!-- Dropzone -->
            <div id="dropzone" class="mb-4 p-4 border border-2 border-secondary rounded bg-light text-center"
                 style="cursor: pointer;">
                <label for="validatedCustomFile" class="form-label fw-bold mb-3 d-block">Upload Proposal Kegiatan</label>
                
                <input type="file" id="validatedCustomFile" name="file"
                       class="form-control d-none" required>
                
                <div id="dropzoneText" class="text-muted">Seret dan lepas file di sini atau klik untuk memilih</div>
                <div class="invalid-feedback mt-2" id="fileError">File wajib diunggah.</div>
            </div>

            <!-- Submit Button -->
            <div class="mt-3">
                <button type="submit" class="rbt-btn btn-md hover-icon-reverse radius-square w-100">
                    <span class="icon-reverse-wrapper">
                        <span class="btn-text">Submit</span>
                        <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                        <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                    </span>
                </button>
            </div>
        </form>
        @elseif($proposalStatus ?? null)
            <div class="px-4 pb-4 text-center text-muted">
                Form upload tidak tersedia pada status saat ini.
            </div>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('kknForm');
                if (!form) return;

                const dropzone = document.getElementById('dropzone');
                const fileInput = document.getElementById('validatedCustomFile');
                const dropzoneText = document.getElementById('dropzoneText');
                const fileError = document.getElementById('fileError');

                dropzone.addEventListener('click', () => fileInput.click());

                fileInput.addEventListener('change', () => {
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        // Validasi ekstensi dan ukuran
                        if (file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024) {
                            dropzoneText.innerText = 'Hanya file PDF maksimal 10 MB yang diperbolehkan!';
                            dropzone.classList.add('border-danger');
                            fileError.style.display = 'block';
                            fileInput.value = '';
                            return;
                        }
                        dropzoneText.innerText = file.name;
                        dropzone.classList.remove('border-danger');
                        fileError.style.display = 'none';
                    }
                });

                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('bg-primary', 'text-white');
                    dropzoneText.innerText = 'Lepaskan file di sini';
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('bg-primary', 'text-white');
                    dropzoneText.innerText = fileInput.files.length > 0
                        ? fileInput.files[0].name
                        : 'Seret dan lepas file di sini atau klik untuk memilih';
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('bg-primary', 'text-white');
                    fileInput.files = e.dataTransfer.files;
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        // Validasi ekstensi dan ukuran
                        if (file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024) {
                            dropzoneText.innerText = 'Hanya file PDF maksimal 10 MB yang diperbolehkan!';
                            dropzone.classList.add('border-danger');
                            fileError.style.display = 'block';
                            fileInput.value = '';
                            return;
                        }
                        dropzoneText.innerText = file.name;
                        dropzone.classList.remove('border-danger');
                        fileError.style.display = 'none';
                    }
                });

                form.addEventListener('submit', function (e) {
                    // Validasi judul kegiatan
                    const judulKegiatan = document.getElementById('judul_kegiatan').value.trim();
                    if (!judulKegiatan) {
                        e.preventDefault();
                        alert('Judul kegiatan harus diisi!');
                        document.getElementById('judul_kegiatan').focus();
                        return;
                    }
                    
                    // Validasi file
                    if (fileInput.files.length === 0) {
                        e.preventDefault();
                        dropzone.classList.add('border-danger');
                        fileError.style.display = 'block';
                        dropzoneText.innerText = 'File wajib diunggah!';
                        return;
                    }
                    const file = fileInput.files[0];
                    if (file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024) {
                        e.preventDefault();
                        dropzone.classList.add('border-danger');
                        fileError.style.display = 'block';
                        dropzoneText.innerText = 'Hanya file PDF maksimal 10 MB yang diperbolehkan!';
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

.rbt-btn {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.rbt-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    color: white;
}
</style>

@endsection

