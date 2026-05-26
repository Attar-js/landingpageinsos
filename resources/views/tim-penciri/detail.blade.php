@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
<x-header/>
<div style="height: 140px;"></div>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Detail Verifikasi</h4>
                <a href="{{ route('tim-penciri.dashboard') }}" class="btn btn-secondary">Kembali</a>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3"><strong>ID</strong><div>{{ $item->id }}</div></div>
                <div class="col-md-3"><strong>Jenis</strong><div>{{ $table }}</div></div>
                <div class="col-md-3"><strong>User NIM/NIP</strong><div>{{ $item->user_nim ?? '-' }}</div></div>
                <div class="col-md-3"><strong>Status</strong><div>{{ $item->status ?? '-' }}</div></div>
                <div class="col-12"><strong>Judul Kegiatan</strong><div>{{ $item->judul_kegiatan ?? '-' }}</div></div>
                <div class="col-12"><strong>Catatan</strong><div>{{ $item->catatan ?? '-' }}</div></div>
            </div>

            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h5 class="fw-semibold">Isi Kelompok</h5>
                    @if($group)
                        <div class="mb-3">
                            <div><strong>Nama Kelompok:</strong> {{ $group->nama_kelompok ?? '-' }}</div>
                            <div><strong>Dosen Pembimbing:</strong> {{ $group->dosen_id ?? '-' }}</div>
                            <div><strong>Mitra:</strong> {{ $group->nama_mitra ?? '-' }}</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>Peran</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $member)
                                        <tr>
                                            <td>{{ $member->name }}</td>
                                            <td>{{ $member->nim }}</td>
                                            <td>{{ $member->role === 'leader' ? 'Ketua' : 'Anggota' }}</td>
                                            <td>{{ $member->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">Data kelompok tidak ditemukan untuk dokumen ini.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

