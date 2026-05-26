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
            <h4 class="titleform">Form Kesediaan</h4>
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

        <form id="formKesediaanForm" action="{{ route('form-kesediaan.store') }}" method="POST" enctype="multipart/form-data" novalidate class="px-4 pb-4">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold">Nama</label>
                <input type="text" class="form-control" value="{{ $user->name ?? '-' }}" readonly>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">{{ ($user->role ?? '') === 'dosen' ? 'NIP' : 'NIM' }}</label>
                <input type="text" class="form-control" value="{{ ($user->role ?? '') === 'dosen' ? ($user->nip ?? '-') : ($user->nim ?? '-') }}" readonly>
                <small class="text-muted">Data identitas diambil otomatis dari akun Anda.</small>
            </div>
            
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
            <div id="dropzone" class="mb-4 p-4 border-2 border-secondary rounded bg-light text-center"
                 style="cursor: pointer;">
                <label for="validatedCustomFile" class="form-label fw-bold mb-3 d-block">Upload Form Kesediaan</label>
                
                <input type="file" id="validatedCustomFile" name="file"
                       class="form-control d-none" required>
                
                <div id="dropzoneText" class="text-muted">Seret dan lepas file di sini atau klik untuk memilih</div>
                <div class="invalid-feedback mt-2" id="fileError">File wajib diunggah.</div>
            </div>

            <!-- Submit Button -->
            <div class="mt-3">
                <button type="button" id="openAgreementModalBtn" class="rbt-btn btn-md hover-icon-reverse radius-square w-100">
                    <span class="icon-reverse-wrapper">
                        <span class="btn-text">Submit</span>
                        <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                        <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                    </span>
                </button>
            </div>
        </form>

        <div class="modal fade" id="agreementModal" tabindex="-1" aria-labelledby="agreementModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agreementModalLabel">Ketentuan Form Kesediaan Dosen Pembimbing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2 text-muted">
                            Silakan baca dokumen ketentuan terlebih dahulu sebelum mengirim form.
                        </p>
                        <div class="border rounded" style="height: 70vh;">
                            <iframe
                                id="agreementFileFrame"
                                src="{{ asset('assets/documents/form-kesediaan-dosen.pdf') }}"
                                title="Dokumen ketentuan form kesediaan dosen"
                                width="100%"
                                height="100%"
                                style="border: 0;">
                            </iframe>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Jika file belum tersedia, silakan letakkan file PDF ketentuan di
                            <code>public/assets/documents/form-kesediaan-dosen.pdf</code>.
                        </small>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="1" id="agreementReadCheck">
                            <label class="form-check-label" for="agreementReadCheck">
                                Saya telah membaca ketentuan dan setuju untuk melanjutkan submit.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="confirmSubmitBtn" class="btn btn-primary" disabled>Lanjut Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dropzone = document.getElementById('dropzone');
                const fileInput = document.getElementById('validatedCustomFile');
                const dropzoneText = document.getElementById('dropzoneText');
                const fileError = document.getElementById('fileError');
                const form = document.getElementById('formKesediaanForm');
                const openAgreementModalBtn = document.getElementById('openAgreementModalBtn');
                const agreementModalEl = document.getElementById('agreementModal');
                const agreementReadCheck = document.getElementById('agreementReadCheck');
                const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
                const agreementModal = agreementModalEl ? new bootstrap.Modal(agreementModalEl) : null;

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
                        
                        // Reset error state
                        dropzone.classList.remove('border-danger');
                        fileError.style.display = 'none';
                        
                        // Update dropzone text
                        dropzoneText.innerText = `File dipilih: ${file.name}`;
                        dropzone.classList.add('border-success');
                    }
                });

                // Drag and drop functionality
                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('border-primary');
                });

                dropzone.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-primary');
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-primary');
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        fileInput.dispatchEvent(new Event('change'));
                    }
                });

                const validateForm = () => {
                    const judulKegiatan = document.getElementById('judul_kegiatan').value.trim();
                    const file = fileInput.files[0];
                    
                    let isValid = true;
                    
                    // Validasi judul kegiatan
                    if (!judulKegiatan) {
                        document.getElementById('judul_kegiatan').classList.add('is-invalid');
                        isValid = false;
                    } else {
                        document.getElementById('judul_kegiatan').classList.remove('is-invalid');
                    }
                    
                    // Validasi file
                    if (!file) {
                        dropzone.classList.add('border-danger');
                        fileError.style.display = 'block';
                        isValid = false;
                    } else if (file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024) {
                        dropzoneText.innerText = 'Hanya file PDF maksimal 10 MB yang diperbolehkan!';
                        dropzone.classList.add('border-danger');
                        fileError.style.display = 'block';
                        isValid = false;
                    } else {
                        dropzone.classList.remove('border-danger');
                        fileError.style.display = 'none';
                    }
                    
                    if (!isValid) {
                        return false;
                    }

                    return true;
                };

                // Open agreement modal before final submit
                openAgreementModalBtn.addEventListener('click', function () {
                    if (!validateForm()) {
                        return;
                    }

                    agreementReadCheck.checked = false;
                    confirmSubmitBtn.disabled = true;
                    agreementModal.show();
                });

                agreementReadCheck.addEventListener('change', function () {
                    confirmSubmitBtn.disabled = !agreementReadCheck.checked;
                });

                confirmSubmitBtn.addEventListener('click', function () {
                    if (!agreementReadCheck.checked) {
                        return;
                    }

                    agreementModal.hide();
                    form.submit();
                });
            });
        </script>
    </div>
    </div>
@endsection 
