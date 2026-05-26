@extends('layout.layout')

@section('styles')
<style>
    .content-wrapper {
        padding-top: 120px !important;
        min-height: 100vh;
    }
    
    .breadcrumb-section {
        padding-top: 0 !important;
        margin-top: 0;
    }
    
    .section-b-space {
        padding-top: 2rem;
    }
    
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
                                <li class="breadcrumb-item active" aria-current="page">Mahasiswa Bimbingan</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
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
                                    <i class="fa fa-users me-2"></i>
                                    Mahasiswa Bimbingan
                                </h5>
                                <p class="text-white-50 mb-0 mt-1">Daftar kelompok mahasiswa yang dibimbing</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <span class="badge bg-light text-dark">
                                        <i class="fa fa-user me-1"></i>
                                        Dosen: {{ auth()->user()->name ?? 'N/A' }}
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
                            <table class="table table-hover" id="mahasiswaTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama Ketua</th>
                                        <th width="15%">NIM</th>
                                        <th width="25%">Judul Kegiatan</th>
                                        <th width="15%">Nama Mitra</th>
                                        <th width="15%">Lokasi Mitra</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($groups as $index => $group)
                                        @php
                                            $leader = collect($group['members'])->where('role', 'Ketua')->first();
                                            $leaderName = $leader ? $leader['name'] : 'N/A';
                                            $leaderNim = $leader ? $leader['nim'] : 'N/A';
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                                                        <i class="fa fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $leaderName }}</div>
                                                        @if($leader && $leader['role'] == 'Ketua')
                                                            <small class="text-primary">Ketua</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="text-primary">{{ $leaderNim }}</code>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1 text-dark">{{ $group['judul_kegiatan'] }}</h6>
                                                    <small class="text-muted">
                                                        <i class="fa fa-users me-1"></i>
                                                        {{ count($group['members']) }} anggota
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $group['nama_mitra'] ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $group['lokasi_mitra'] ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('dosen.detail-mahasiswa-bimbingan', $group['id']) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Detail Kelompok"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="text-muted">
                                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-users fa-2x text-muted"></i>
                                                    </div>
                                                    <h5 class="text-muted mb-2">Belum ada mahasiswa bimbingan</h5>
                                                    <p class="text-muted mb-0">Data akan muncul setelah ada kelompok yang diassign</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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

        // Search functionality
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#mahasiswaTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Initialize DataTable
        $('#mahasiswaTable').DataTable({
            "pageLength": 10,
            "order": [[0, "asc"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 6] }
            ]
        });
    });
</script>
@endsection 
