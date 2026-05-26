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
                    <h2 class="fw-bold text-dark">Status Verifikasi Tim MK Penciri</h2>
                    <p class="text-muted">Monitoring status upload dan verifikasi dokumen KKN</p>
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

                <!-- Pesan untuk dosen yang tidak memiliki kelompok -->
                @if(isset($noGroupsAssigned) && $noGroupsAssigned)
                    <div class="card shadow-sm">
                        <div class="card-body text-center p-5">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum Ada Kelompok yang Di-assign</h4>
                            <p class="text-muted">Anda belum memiliki kelompok yang di-assign untuk dibimbing. Silakan hubungi administrator untuk mendapatkan assignment kelompok.</p>
                        </div>
                    </div>
                @else
                    <!-- Dropdown Pilihan Kelompok untuk Dosen -->
                    @if(isset($isDosen) && $isDosen && isset($kelompokList) && !empty($kelompokList))
                        <div class="card shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-3">
                                    <i class="fas fa-users me-2"></i>Pilih Kelompok yang Ingin Dilihat
                                </h5>
                                <form method="GET" action="{{ route('status-verifikasi') }}" class="row align-items-end">
                                    <div class="col-md-8">
                                        <label for="nim" class="form-label fw-bold">Pilih Kelompok:</label>
                                        <select name="nim" id="nim" class="form-select" onchange="this.form.submit()">
                                            <option value="">-- Pilih Kelompok --</option>
                                            @foreach($kelompokList as $kelompok)
                                                <option value="{{ $kelompok['nim'] }}" 
                                                    {{ $selectedNim == $kelompok['nim'] ? 'selected' : '' }}>
                                                    {{ $kelompok['nama'] }} ({{ $kelompok['nim'] }}) - {{ $kelompok['judul_kegiatan'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Section 1: Identitas Mahasiswa -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h4 class="fw-bold text-dark mb-3">
                                <i class="fas fa-user-graduate me-2"></i>
                                @if($isDosen)
                                    Identitas Kelompok yang Dipilih
                                @else
                                    Identitas Mahasiswa
                                @endif
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Nama:</strong> {{ $mahasiswa['nama'] }}</p>
                                    <p class="mb-2"><strong>NIM:</strong> {{ $mahasiswa['nim'] }}</p>
                                    <p class="mb-2"><strong>Program Studi:</strong> {{ $mahasiswa['program_studi'] }}</p>
                                    <p class="mb-2"><strong>Judul Kegiatan:</strong> {{ $mahasiswa['judul_kegiatan'] }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Mitra:</strong> {{ $mahasiswa['mitra'] }}</p>
                                    <p class="mb-2"><strong>Lokasi Mitra:</strong> {{ $mahasiswa['lokasi_mitra'] }}</p>
                                    <p class="mb-2"><strong>Anggota:</strong></p>
                                    @if(!empty($mahasiswa['anggota']))
                                        <ul class="list-unstyled ms-3">
                                            @foreach($mahasiswa['anggota'] as $anggota)
                                                <li class="mb-1">• {{ trim($anggota) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted ms-3">Data anggota belum tersedia</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Dokumen Yang di Unggah -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h4 class="fw-bold text-dark mb-3">
                                <i class="fas fa-file-alt me-2"></i>Dokumen Yang di Unggah
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" width="5%">No</th>
                                            <th scope="col" width="20%">Jenis Dokumen</th>
                                            <th scope="col" width="12%">Status Upload</th>
                                            <th scope="col" width="12%">Status Verifikasi</th>
                                            <th scope="col" width="18%">Nama File</th>
                                            <th scope="col" width="8%">Tanggal Upload</th>
                                            <th scope="col" width="8%">Catatan</th>
                                            <th scope="col" width="12%">Hapus Pengumpulan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dokumen as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $item['jenis'] }}</strong>
                                            </td>
                                            <td>
                                                @if($item['status_upload'] == 'Terkirim')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Terkirim
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-times me-1"></i>Belum Upload
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['status_verifikasi'] == 'Diterima' || $item['status_verifikasi'] == 'approved')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Diterima
                                                    </span>
                                                @elseif($item['status_verifikasi'] == 'Ditolak' || $item['status_verifikasi'] == 'rejected')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Ditolak
                                                    </span>
                                                @elseif($item['status_verifikasi'] == 'Menunggu' || $item['status_verifikasi'] == 'Pending' || $item['status_verifikasi'] == 'pending' || $item['status_verifikasi'] == 'waiting')
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Menunggu
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['file_name'] && $item['file_name'] != '-')
                                                    <div class="d-flex flex-column">
                                                        <small class="text-primary mb-1" title="Klik untuk melihat opsi file">{{ $item['file_name'] }}</small>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            @if(isset($isDosen) && $isDosen)
                                                                <a href="{{ route('file.view.dosen', ['jenisDokumen' => $item['jenis_dokumen_key'], 'fileName' => $item['file_name'], 'studentNim' => $mahasiswa['nim']]) }}" 
                                                                   class="btn btn-outline-info btn-sm" 
                                                                   target="_blank" 
                                                                   title="Lihat File">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('file.download.dosen', ['jenisDokumen' => $item['jenis_dokumen_key'], 'fileName' => $item['file_name'], 'studentNim' => $mahasiswa['nim']]) }}" 
                                                                   class="btn btn-outline-primary btn-sm" 
                                                                   title="Download File">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('file.view', ['jenisDokumen' => $item['jenis_dokumen_key'], 'fileName' => $item['file_name']]) }}" 
                                                                   class="btn btn-outline-info btn-sm" 
                                                                   target="_blank" 
                                                                   title="Lihat File">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('file.download', ['jenisDokumen' => $item['jenis_dokumen_key'], 'fileName' => $item['file_name']]) }}" 
                                                                   class="btn btn-outline-primary btn-sm" 
                                                                   title="Download File">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['upload_date'] && $item['upload_date'] != '-')
                                                    <small>{{ \Carbon\Carbon::parse($item['upload_date'])->format('d/m/Y') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['catatan'] && $item['catatan'] != '-')
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            onclick="showCatatan('{{ addslashes($item['jenis']) }}', '{{ addslashes($item['catatan']) }}')">
                                                        <i class="fas fa-comment"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['status_verifikasi'] == 'Ditolak' || $item['status_verifikasi'] == 'rejected')
                                                    <a href="{{ route('hapusPengumpulan', ['id' => $item['id'] ?? 0, 'jenis' => $item['jenis']]) }}" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash me-1"></i>Hapus Pengumpulan
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-check-circle me-2"></i>Diterima
                                    </h5>
                                    <h3 class="mb-0">{{ count(array_filter($dokumen, function($item) { return $item['status_verifikasi'] == 'Diterima' || $item['status_verifikasi'] == 'approved'; })) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-clock me-2"></i>Menunggu
                                    </h5>
                                    <h3 class="mb-0">{{ count(array_filter($dokumen, function($item) { return $item['status_verifikasi'] == 'Menunggu' || $item['status_verifikasi'] == 'Pending' || $item['status_verifikasi'] == 'pending' || $item['status_verifikasi'] == 'waiting'; })) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-times-circle me-2"></i>Ditolak
                                    </h5>
                                    <h3 class="mb-0">{{ count(array_filter($dokumen, function($item) { return $item['status_verifikasi'] == 'Ditolak' || $item['status_verifikasi'] == 'rejected'; })) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-upload me-2"></i>Total Upload
                                    </h5>
                                    <h3 class="mb-0">{{ count(array_filter($dokumen, function($item) { return $item['status_upload'] == 'Terkirim'; })) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-3">
                                <i class="fas fa-chart-line me-2"></i>Progress Verifikasi
                            </h5>
                            @php
                                $totalDokumen = count($dokumen);
                                $diterima = count(array_filter($dokumen, function($item) { return $item['status_verifikasi'] == 'Diterima' || $item['status_verifikasi'] == 'approved'; }));
                                $pending = count(array_filter($dokumen, function($item) { return $item['status_verifikasi'] == 'Pending' || $item['status_verifikasi'] == 'pending'; }));
                                $ditolak = count(array_filter($dokumen, function($item) { return $item['status_verifikasi'] == 'Ditolak' || $item['status_verifikasi'] == 'rejected'; }));
                                $progress = $totalDokumen > 0 ? round(($diterima / $totalDokumen) * 100) : 0;
                            @endphp
                            <div class="progress mb-3" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($diterima / $totalDokumen) * 100 }}%" aria-valuenow="{{ $diterima }}" aria-valuemin="0" aria-valuemax="{{ $totalDokumen }}">
                                    {{ $diterima }} Diterima
                                </div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($pending / $totalDokumen) * 100 }}%" aria-valuenow="{{ $pending }}" aria-valuemin="0" aria-valuemax="{{ $totalDokumen }}">
                                    {{ $pending }} Pending
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($ditolak / $totalDokumen) * 100 }}%" aria-valuenow="{{ $ditolak }}" aria-valuemin="0" aria-valuemax="{{ $totalDokumen }}">
                                    {{ $ditolak }} Ditolak
                                </div>
                            </div>
                            <div class="text-center">
                                <h6 class="text-muted">Progress Verifikasi: {{ $progress }}%</h6>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Catatan -->
    <div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="catatanModalLabel">Catatan Verifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Dokumen:</strong> <span id="dokumenNama"></span></p>
                    <p><strong>Catatan:</strong></p>
                    <div class="alert alert-info" id="catatanText"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi File -->
    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Konfirmasi File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama File:</strong> <span id="fileName"></span></p>
                    <p><strong>Jenis Dokumen:</strong> <span id="documentType"></span></p>
                    <p class="text-muted">Pilih aksi yang ingin Anda lakukan:</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" id="viewFileBtn">
                        <i class="fas fa-eye me-2"></i>Lihat File
                    </button>
                    <button type="button" class="btn btn-primary" id="downloadFileBtn">
                        <i class="fas fa-download me-2"></i>Download File
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <style>
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        border: none;
        border-radius: 15px;
        background-color: white;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
        border-color: #dee2e6;
    }
    
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.75em;
        border-radius: 20px;
    }
    
    .bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }
    
    .bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    }
    
    .bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
    }
    
    .bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    }
    
    .bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%) !important;
    }
    
    .btn {
        border-radius: 10px;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #e91e63 0%, #f06292 100%);
        border: none;
        color: white;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #c2185b 0%, #e91e63 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        color: white;
    }
    
    .btn-outline-primary {
        border: 2px solid #e91e63;
        color: #e91e63;
    }
    
    .btn-outline-primary:hover {
        background: #e91e63;
        border-color: #e91e63;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        color: white;
    }
    
    .btn-outline-info {
        border: 2px solid #17a2b8;
        color: #17a2b8;
    }
    
    .btn-outline-info:hover {
        background: #17a2b8;
        border-color: #17a2b8;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        color: white;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.2rem;
    }
    
    .btn-group-sm .btn i {
        font-size: 0.7rem;
    }
    
    .d-flex.flex-column {
        gap: 0.25rem;
    }
    
    .text-primary {
        cursor: pointer;
        transition: color 0.3s ease;
        text-decoration: none;
    }
    
    .text-primary:hover {
        color: #0056b3 !important;
        text-decoration: underline;
    }
    
    .text-primary:active {
        color: #004085 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .text-primary {
        color: #007bff !important;
    }
    
    .fw-bold {
        font-weight: 700 !important;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    .list-unstyled li {
        color: #495057;
        font-size: 0.9em;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .progress {
        border-radius: 10px;
        background-color: #e9ecef;
    }
    
    .progress-bar {
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9em;
    }
    
    /* SweetAlert2 Custom Styles */
    .swal2-popup-custom {
        border-radius: 15px !important;
    }
    
    .swal2-popup-custom .swal2-title {
        color: #495057 !important;
        font-weight: 600 !important;
    }
    
    .swal2-popup-custom .swal2-html-container {
        text-align: left !important;
        color: #6c757d !important;
        line-height: 1.6 !important;
    }
    
    .swal2-popup-custom .swal2-confirm {
        background-color: #e91e63 !important;
        border: none !important;
        border-radius: 10px !important;
        padding: 10px 30px !important;
        font-weight: 500 !important;
    }
    
    .swal2-popup-custom .swal2-confirm:hover {
        background-color: #c2185b !important;
    }
    
    @media print {
        .btn, .progress {
            display: none !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
    </style>

    <script>
    function showCatatan(jenis, catatan) {
        // Clean and escape the content
        const cleanJenis = jenis.replace(/['"]/g, '');
        const cleanCatatan = catatan.replace(/['"]/g, '').replace(/\n/g, '<br>');
        
        Swal.fire({
            title: 'Catatan untuk ' + cleanJenis,
            html: cleanCatatan,
            icon: 'info',
            confirmButtonText: 'Tutup',
            width: '600px',
            customClass: {
                popup: 'swal2-popup-custom'
            }
        });
    }
    
    function showFileModal(fileName, documentType, viewUrl, downloadUrl) {
        document.getElementById('fileName').textContent = fileName;
        document.getElementById('documentType').textContent = documentType;
        
        // Set event listeners untuk tombol
        document.getElementById('viewFileBtn').onclick = function() {
            window.open(viewUrl, '_blank');
            bootstrap.Modal.getInstance(document.getElementById('fileModal')).hide();
        };
        
        document.getElementById('downloadFileBtn').onclick = function() {
            window.location.href = downloadUrl;
            bootstrap.Modal.getInstance(document.getElementById('fileModal')).hide();
        };
        
        var modal = new bootstrap.Modal(document.getElementById('fileModal'));
        modal.show();
    }
    
    // Event listener untuk nama file yang diklik
    document.addEventListener('DOMContentLoaded', function() {
        const fileNames = document.querySelectorAll('.text-primary');
        fileNames.forEach(function(fileName) {
            fileName.addEventListener('click', function() {
                const fileNameText = this.textContent.trim();
                const row = this.closest('tr');
                const documentType = row.querySelector('td:nth-child(2) strong').textContent;
                
                // Ambil URL dari tombol yang ada di baris yang sama
                const viewBtn = row.querySelector('.btn-outline-info');
                const downloadBtn = row.querySelector('.btn-outline-primary');
                
                if (viewBtn && downloadBtn) {
                    const viewUrl = viewBtn.href;
                    const downloadUrl = downloadBtn.href;
                    
                    showFileModal(fileNameText, documentType, viewUrl, downloadUrl);
                }
            });
        });
    });

    </script>
@endsection 
