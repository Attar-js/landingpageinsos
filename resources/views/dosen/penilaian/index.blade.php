@extends('layouts.app')

@section('title', 'Penilaian Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Penilaian Mahasiswa</h4>
                <a href="{{ route('dosen.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Mahasiswa</h4>
                </div>
                <div class="card-body">
                    @if($mahasiswa->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>Email</th>
                                        <th>Status Penilaian</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mahasiswa as $index => $mhs)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $mhs->name }}</td>
                                        <td>{{ $mhs->nim ?? '-' }}</td>
                                        <td>{{ $mhs->email }}</td>
                                        <td>
                                            @if($mhs->penilaianAsMahasiswa->count() > 0)
                                                <span class="badge bg-success">Sudah Dinilai</span>
                                            @else
                                                <span class="badge bg-warning">Belum Dinilai</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($mhs->penilaianAsMahasiswa->count() > 0)
                                                <span class="fw-bold">{{ $mhs->penilaianAsMahasiswa->first()->nilai_akhir }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('dosen.penilaian.show', $mhs->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> 
                                                {{ $mhs->penilaianAsMahasiswa->count() > 0 ? 'Edit Nilai' : 'Beri Nilai' }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Belum ada mahasiswa</h5>
                            <p class="text-muted">Tidak ada mahasiswa yang terdaftar dalam sistem.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
