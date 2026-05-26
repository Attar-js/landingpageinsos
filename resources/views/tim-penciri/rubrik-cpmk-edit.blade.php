@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
<x-header />
<div style="height: 140px;"></div>

<div class="container" style="max-width: 1200px;">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-1">{{ $group->nama_kelompok }}</h4>
                    <p class="text-muted mb-0">{{ $group->judul_kegiatan }}</p>
                </div>
                <a href="{{ route('tim-penciri.rubrik-cpmk') }}" class="btn btn-secondary">Kembali ke Daftar</a>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4"><strong>Ketua:</strong> {{ $group->groupLeader->name ?? '-' }} ({{ $group->groupLeader->nim ?? '-' }})</div>
                <div class="col-md-4"><strong>Dosen Pembimbing:</strong> {{ $group->dosen->name ?? '-' }}</div>
                <div class="col-md-4"><strong>Mitra:</strong> {{ $group->nama_mitra ?? '-' }}</div>
            </div>
        </div>
    </div>

    @include('components.cpmk-rubric-form', [
        'group' => $group,
        'rubric' => $rubric,
        'mode' => 'tim_penciri',
    ])
</div>
@endsection
