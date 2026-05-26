@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
    <x-header/>
    <div style="height: 140px;"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                            <h4 class="fw-bold text-dark mt-3">Konfirmasi Hapus Pengumpulan</h4>
                            <p class="text-muted">Anda yakin ingin menghapus pengumpulan dokumen ini?</p>
                        </div>

                        <div class="alert alert-warning">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-info-circle me-2"></i>Informasi Penting
                            </h6>
                            <ul class="mb-0">
                                <li>Dokumen yang dihapus tidak dapat dipulihkan</li>
                                <li>File akan dihapus dari sistem</li>
                                <li>Data akan dihapus dari database</li>
                                <li>Anda dapat upload ulang di form utama setelah penghapusan</li>
                            </ul>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2">Detail Dokumen:</h6>
                                <p class="mb-1"><strong>Jenis Dokumen:</strong> {{ $jenis }}</p>
                                <p class="mb-1"><strong>ID:</strong> {{ $id }}</p>
                                <p class="mb-0"><strong>Status:</strong> <span class="badge bg-danger">Ditolak</span></p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('status-verifikasi') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <form action="{{ route('hapusPengumpulan.confirm', ['id' => $id, 'jenis' => $jenis]) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengumpulan ini?')">
                                    <i class="fas fa-trash me-1"></i>Hapus Pengumpulan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 
