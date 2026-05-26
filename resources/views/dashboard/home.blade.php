@extends('layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Selamat Datang di Sistem KKN ITK</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <h2 class="text-primary mb-4">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Sistem Informasi KKN Institut Teknologi Kalimantan
                                </h2>
                                <p class="text-muted mb-4">
                                    Selamat datang, <strong>{{ $user->name ?? 'User' }}</strong>! 
                                    Anda berhasil login ke sistem KKN ITK.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-exchange-alt fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Form Konversi</h5>
                                    <p class="card-text">Upload dan konversi mata kuliah KKN Anda dengan mudah.</p>
                                    <a href="{{ route('konversi') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right me-1"></i>Akses Form
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-upload fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Laporan Akhir dan Luaran</h5>
                                    <p class="card-text">Upload laporan akhir dan luaran kegiatan KKN dalam satu menu.</p>
                                    <a href="{{ route('laporanakhir') }}" class="btn btn-success">
                                        <i class="fas fa-arrow-right me-1"></i>Upload Dokumen
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-list fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title">Status Luaran</h5>
                                    <p class="card-text">Lihat status dan progress upload luaran Anda.</p>
                                    <a href="{{ route('status-verifikasi') }}" class="btn btn-info">
                                        <i class="fas fa-arrow-right me-1"></i>Cek Status
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-file-alt fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="card-title">Form Kesediaan</h5>
                                    <p class="card-text">Form kesediaan dosen pembimbing KKN.</p>
                                    <a href="{{ route('form-kesediaan.upload') }}" class="btn btn-warning">
                                        <i class="fas fa-arrow-right me-1"></i>Form Kesediaan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-chart-line fa-3x text-danger"></i>
                                    </div>
                                    <h5 class="card-title">Proposal Kegiatan</h5>
                                    <p class="card-text">Upload dan kelola proposal kegiatan KKN.</p>
                                    <a href="{{ route('proposalkegiatan') }}" class="btn btn-danger">
                                        <i class="fas fa-arrow-right me-1"></i>Proposal
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-book fa-3x text-secondary"></i>
                                    </div>
                                    <h5 class="card-title">Laporan Akhir</h5>
                                    <p class="card-text">Upload dan kelola laporan akhir KKN.</p>
                                    <a href="{{ route('laporanakhir') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-right me-1"></i>Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Informasi Sistem
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success me-2"></i>Sistem terintegrasi dengan dashboard</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Upload file dengan aman</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Tracking status real-time</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success me-2"></i>Notifikasi otomatis</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Backup data otomatis</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Support multi-user</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

