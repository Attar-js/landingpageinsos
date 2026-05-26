@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
<style>
    .verify-card { border: 1px solid #e5e7eb; border-radius: 10px; }
    .verify-title { font-size: 1.3rem; font-weight: 700; color: #1f2937; }
    .verify-table thead th {
        background: #eef2ff;
        color: #374151;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        white-space: nowrap;
        vertical-align: middle;
    }
    .verify-table td { font-size: .87rem; vertical-align: middle; }
    .status-pill {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 700;
    }
    .status-pending { background: #fff3cd; color: #664d03; }
    .status-approved { background: #d1e7dd; color: #0f5132; }
    .status-rejected { background: #f8d7da; color: #842029; }
    .action-group .btn { margin: 2px; font-size: .75rem; padding: .28rem .5rem; }
</style>
<x-header/>
<div style="height: 140px;"></div>
<div class="container">
    <div class="card shadow-sm mb-4 verify-card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="verify-title mb-0">Data Verifikasi Kesediaan Dosen Pembimbing</h4>
                <a href="{{ route('tim-penciri.dashboard') }}" class="btn btn-secondary">Kembali</a>
            </div>
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            <div class="table-responsive">
                <table class="table table-bordered verify-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NIM/NIP</th>
                            <th>JUDUL KEGIATAN</th>
                            <th>MITRA</th>
                            <th>LOKASI MITRA</th>
                            <th>JUMLAH ANGGOTA</th>
                            <th>TANGGAL DAFTAR</th>
                            <th>STATUS</th>
                            <th>FILE</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formKesediaan as $row)
                            @php
                                $status = strtolower($row->status ?? 'pending');
                                $statusClass = $status === 'approved' ? 'status-approved' : ($status === 'rejected' ? 'status-rejected' : 'status-pending');
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->user_nim ?? '-' }}</td>
                                <td>{{ $row->judul_kegiatan ?? '-' }}</td>
                                <td>{{ $row->mitra ?? '-' }}</td>
                                <td>{{ $row->lokasi_mitra ?? '-' }}</td>
                                <td>{{ $row->jumlah_anggota ?? '-' }}</td>
                                <td>{{ isset($row->created_at) ? \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') : '-' }}</td>
                                <td><span class="status-pill {{ $statusClass }}">{{ $status }}</span></td>
                                <td>
                                    @if(!empty($row->file_path))
                                        <a href="{{ asset('storage/' . $row->file_path) }}" target="_blank" class="btn btn-info btn-sm">Lihat</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center action-group">
                                    <a class="btn btn-sm btn-primary" href="{{ route('tim-penciri.detail', ['id' => $row->id, 'source' => 'local', 'table' => 'form_kesediaan']) }}">Detail</a>
                                    <form method="POST" action="{{ route('tim-penciri.verify') }}" class="d-inline">@csrf
                                        <input type="hidden" name="source" value="local"><input type="hidden" name="table" value="form_kesediaan"><input type="hidden" name="id" value="{{ $row->id }}"><input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-success" type="submit">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('tim-penciri.verify') }}" class="d-inline">@csrf
                                        <input type="hidden" name="source" value="local"><input type="hidden" name="table" value="form_kesediaan"><input type="hidden" name="id" value="{{ $row->id }}"><input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-sm btn-danger" type="submit">Tolak</button>
                                    </form>
                                    <form method="POST" action="{{ route('tim-penciri.destroy', $row->id) }}" class="d-inline">@csrf
                                        <input type="hidden" name="source" value="local"><input type="hidden" name="table" value="form_kesediaan">
                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-3">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

