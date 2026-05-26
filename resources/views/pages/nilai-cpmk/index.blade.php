@extends('layout.layout')

@section('styles')
<style>
    /* Tambahkan padding-top untuk menghindari tabrakan dengan header navbar */
    .content-wrapper {
        padding-top: 120px !important; /* Sesuaikan dengan tinggi header navbar */
        min-height: 100vh;
    }
    
    .breadcrumb-section {
        padding-top: 0 !important;
        margin-top: 0;
    }
    
    /* Pastikan konten tidak tertutup header */
    .section-b-space {
        padding-top: 2rem;
    }
    
    /* Responsive padding untuk mobile */
    @media (max-width: 768px) {
        .content-wrapper {
            padding-top: 100px !important;
        }
    }
</style>
@endsection

@section('content')

<!-- breadcrumb start -->
<div class="content-wrapper" style="padding-top: 100px;">
    <section class="breadcrumb-section">
        <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nilai CPMK</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb end -->

<!-- section start -->
<section class="section-b-space">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="fa fa-file-pdf me-2"></i>
                                    File Nilai CPMK Saya
                                </h5>
                                <p class="text-white-50 mb-0 mt-1">Download file nilai CPMK yang diupload tim penciri</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <span class="badge bg-light text-dark">
                                        <i class="fa fa-user me-1"></i>
                                        NIM: {{ auth()->user()->nim ?? auth()->user()->username ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover" id="nilaiCpmkTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="35%">Judul Kegiatan</th>
                                        <th width="25%">File</th>
                                        <th width="15%">Uploaded By</th>
                                        <th width="15%">Tanggal Upload</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nilaiCpmk as $index => $item)
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                            </td>
                                                                                         <td>
                                                 <div class="d-flex align-items-center">
                                                     <div class="flex-grow-1">
                                                         <h6 class="mb-1 text-dark">{{ $item->judul_kegiatan }}</h6>
                                                         @if($item->catatan)
                                                             <small class="text-muted">{{ $item->catatan }}</small>
                                                         @endif
                                                     </div>
                                                 </div>
                                             </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                                        <i class="fa fa-file-pdf text-danger"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $item->file_name }}</div>
                                                        <small class="text-muted">
                                                            <i class="fa fa-hdd me-1"></i>
                                                            {{ $item->formatted_file_size }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-info bg-opacity-10 p-2 rounded me-2">
                                                        <i class="fa fa-user text-info"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $item->uploaded_by }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold text-dark">{{ $item->uploaded_at->format('d/m/Y') }}</div>
                                                    <small class="text-muted">{{ $item->uploaded_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                                                                         <td>
                                                 <div class="d-flex gap-1 justify-content-center">
                                                     <a href="{{ route('nilai-cpmk.download', $item->id) }}" 
                                                        class="btn btn-sm btn-success" 
                                                        title="Download PDF"
                                                        data-bs-toggle="tooltip">
                                                         <i class="fa fa-download"></i>
                                                     </a>
                                                     <a href="{{ route('nilai-cpmk.view', $item->id) }}" 
                                                        class="btn btn-sm btn-primary" 
                                                        title="View PDF" 
                                                        target="_blank"
                                                        data-bs-toggle="tooltip">
                                                         <i class="fa fa-external-link-alt"></i>
                                                     </a>
                                                 </div>
                                             </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted">
                                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-inbox fa-2x text-muted"></i>
                                                    </div>
                                                    <h5 class="text-muted mb-2">Belum ada file nilai CPMK</h5>
                                                    <p class="text-muted mb-0">Tim penciri belum mengupload file nilai CPMK untuk Anda</p>
                                                    <small class="text-muted">
                                                        File akan muncul di sini setelah tim penciri mengupload nilai CPMK Anda
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($nilaiCpmk->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $nilaiCpmk->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- section end -->
</div>

@endsection

@section('script')
<script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/datatable/datatables/datatable.custom.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize DataTable
        $('#nilaiCpmkTable').DataTable({
            "pageLength": 10,
            "order": [[4, "desc"]], // Sort by upload date
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 5] } // Disable sorting for No and Action columns
            ]
        });
    });
</script>
@endsection 
