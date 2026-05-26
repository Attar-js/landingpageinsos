@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
<style>
    .rubrik-table thead th { background: #eef2ff; font-size: .85rem; }
    .status-badge { font-size: .78rem; font-weight: 600; padding: 4px 10px; border-radius: 999px; }
    .status-lengkap { background: #d1e7dd; color: #0f5132; }
    .status-partial { background: #fff3cd; color: #664d03; }
    .status-kosong { background: #f8d7da; color: #842029; }
</style>

<x-header />
<div style="height: 140px;"></div>

<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-1">Rubrik Penilaian CPMK</h4>
                    <p class="text-muted mb-0">Isi kolom skor untuk kelompok yang sudah mengisi deskripsi kegiatan.</p>
                </div>
                <a href="{{ route('tim-penciri.dashboard') }}" class="btn btn-secondary">Kembali</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered rubrik-table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelompok</th>
                            <th>Ketua</th>
                            <th>Judul Kegiatan</th>
                            <th>Dosen Pembimbing</th>
                            <th>Deskripsi</th>
                            <th>Skor</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                            @php
                                $rubric = $group->cpmkRubric;
                                $hasDeskripsi = $rubric?->hasDeskripsi();
                                $hasSkor = $rubric?->hasSkor();
                                if ($hasDeskripsi && $hasSkor) {
                                    $statusClass = 'status-lengkap';
                                    $statusLabel = 'Lengkap';
                                } elseif ($hasDeskripsi) {
                                    $statusClass = 'status-partial';
                                    $statusLabel = 'Menunggu skor';
                                } else {
                                    $statusClass = 'status-kosong';
                                    $statusLabel = 'Belum deskripsi';
                                }
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $group->nama_kelompok ?? '-' }}</td>
                                <td>{{ $group->groupLeader->name ?? '-' }}<br><small class="text-muted">{{ $group->groupLeader->nim ?? '' }}</small></td>
                                <td>{{ $group->judul_kegiatan ?? '-' }}</td>
                                <td>{{ $group->dosen->name ?? '-' }}</td>
                                <td>
                                    @if($hasDeskripsi)
                                        <span class="status-badge status-lengkap">Sudah diisi</span>
                                    @else
                                        <span class="status-badge status-kosong">Belum</span>
                                    @endif
                                </td>
                                <td>
                                    @if($hasSkor)
                                        <span class="status-badge status-lengkap">{{ number_format($rubric->rubrik_total, 2) }}</span>
                                    @else
                                        <span class="status-badge status-partial">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('tim-penciri.rubrik-cpmk.edit', $group->id) }}" class="btn btn-sm btn-primary">
                                        {{ $hasDeskripsi ? 'Isi / Ubah Skor' : 'Lihat' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada kelompok aktif yang disetujui dosen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
