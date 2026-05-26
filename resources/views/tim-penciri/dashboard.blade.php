@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
<x-header/>
<div style="height: 140px;"></div>

<div class="container penciri-wrap">
    <div class="card shadow main-card mb-4">
        <div class="main-head">
            <div class="main-title">Dashboard Tim MK Penciri</div>
            <div>Pilih halaman verifikasi sesuai jenis dokumen.</div>
        </div>
        <div class="card-body p-4">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errorMessage)
                <div class="alert alert-warning">{{ $errorMessage }}</div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="summary-card bg-kesediaan p-3 shadow-sm">
                        <div class="label">Persetujuan Dosen Menunggu</div>
                        <div class="value">{{ $counts['form_kesediaan_pending'] }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-proposal p-3 shadow-sm">
                        <div class="label">Proposal Menunggu</div>
                        <div class="value">{{ $counts['proposal_pending'] }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-laporan p-3 shadow-sm">
                        <div class="label">Laporan Akhir Menunggu</div>
                        <div class="value">{{ $counts['laporan_pending'] }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card bg-luaran p-3 shadow-sm">
                        <div class="label">Luaran Menunggu</div>
                        <div class="value">{{ $counts['luaran_pending'] }}</div>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="{{ route('tim-penciri.kesediaan-proposal') }}" class="btn btn-outline-primary w-100 py-3 fw-semibold">
                        Halaman Kesediaan Dosen dan Proposal
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('tim-penciri.laporan-luaran') }}" class="btn btn-outline-success w-100 py-3 fw-semibold">
                        Halaman Laporan Akhir dan Luaran
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

