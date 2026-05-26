@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
    <x-header/>
    <div style="height: 140px;"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <!-- Main Title -->
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-dark">Status Pendaftaran KKN</h2>
                    <p class="text-muted">Monitoring status pendaftaran KKN Tim MK Penciri</p>
                </div>

                <!-- Notifications -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Status Pendaftaran Table -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-dark mb-3">
                            <i class="fas fa-list me-2"></i>Daftar Pendaftaran KKN
                        </h4>
                        
                        @if($pendaftar->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" width="5%">No</th>
                                            <th scope="col" width="20%">Judul Kegiatan</th>
                                            <th scope="col" width="15%">Mitra</th>
                                            <th scope="col" width="15%">Lokasi Mitra</th>
                                            <th scope="col" width="10%">NIM Ketua</th>
                                            <th scope="col" width="10%">Status</th>
                                            <th scope="col" width="10%">Tanggal Daftar</th>
                                            <th scope="col" width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendaftar as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $item->judul_kegiatan }}</strong>
                                            </td>
                                            <td>{{ $item->mitra }}</td>
                                            <td>{{ $item->lokasi_mitra }}</td>
                                            <td>{{ $item->user_nim }}</td>
                                            <td>
                                                @if($item->status == 'approved')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Diterima
                                                    </span>
                                                @elseif($item->status == 'rejected')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Ditolak
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Menunggu
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $item->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('kkn.detail', $item->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada pendaftaran KKN</h5>
                                <p class="text-muted">Data pendaftaran akan muncul di sini setelah ada yang mendaftar.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85em;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .badge {
        font-size: 0.75em;
        padding: 0.5em 0.75em;
        border-radius: 20px;
    }
    
    .btn {
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8em;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary {
        border: 2px solid #007bff;
        color: #007bff;
    }
    
    .btn-outline-primary:hover {
        background: #007bff;
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        color: white;
    }
    
    .alert {
        border-radius: 15px;
        border: none;
        font-weight: 600;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .fw-bold {
        font-weight: 700 !important;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    @media print {
        .btn {
            display: none !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
    </style>
@endsection 