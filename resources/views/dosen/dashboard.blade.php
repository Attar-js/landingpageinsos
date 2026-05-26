@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Dashboard Dosen</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-2">{{ $totalMahasiswa }}</h4>
                            <p class="text-muted mb-0">Total Mahasiswa</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary rounded fs-3">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-2">{{ $mahasiswaSudahDinilai }}</h4>
                            <p class="text-muted mb-0">Sudah Dinilai</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success rounded fs-3">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-2">{{ $mahasiswaBelumDinilai }}</h4>
                            <p class="text-muted mb-0">Belum Dinilai</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning rounded fs-3">
                                    <i class="fas fa-clock"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-2">{{ $mahasiswa->count() > 0 ? round(($mahasiswaSudahDinilai / $mahasiswa->count()) * 100, 1) : 0 }}%</h4>
                            <p class="text-muted mb-0">Progress Penilaian</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-info rounded fs-3">
                                    <i class="fas fa-percentage"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Mahasiswa yang Belum Dinilai</h4>
                </div>
                <div class="card-body">
                    @if($mahasiswaBelumDinilaiList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>Email</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mahasiswaBelumDinilaiList as $index => $mhs)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $mhs->name }}</td>
                                        <td>{{ $mhs->nim ?? '-' }}</td>
                                        <td>{{ $mhs->email }}</td>
                                        <td>
                                            <a href="{{ route('dosen.penilaian.show', $mhs->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Nilai
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Semua mahasiswa sudah dinilai!</h5>
                            <p class="text-muted">Tidak ada mahasiswa yang belum dinilai.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Aksi Cepat</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('dosen.penilaian') }}" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-list"></i> Lihat Semua Penilaian
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('status-verifikasi') }}" class="btn btn-info btn-lg w-100 mb-3">
                                <i class="fas fa-eye"></i> Lihat Status Verifikasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
