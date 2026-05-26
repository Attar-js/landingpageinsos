@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
<x-header/>
<div style="height: 140px;"></div>

<div class="container">
    <div class="card shadow mb-4" style="max-width: 1200px; margin: 0 auto;">
        <div class="section-title text-center py-4">
            <h4 class="fw-bold mb-0">Form Peer Review Kelompok</h4>
        </div>
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($group))
                <div class="alert alert-info">
                    <strong>Kelompok:</strong> {{ $group->nama_kelompok ?? '-' }} |
                    <strong>Judul:</strong> {{ $group->judul_kegiatan ?? '-' }}
                </div>
            @endif

            @if(isset($windowMessage))
                <div class="alert {{ ($isPeerReviewOpen ?? false) ? 'alert-success' : 'alert-warning' }}">
                    {{ $windowMessage }}
                </div>
            @endif

            @if(isset($reviewTargets) && $reviewTargets->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Progres Penilaian</span>
                            <span class="fw-bold">{{ $reviewedCount ?? 0 }}/{{ $totalTargets ?? 0 }} anggota</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-primary"
                                 role="progressbar"
                                 style="width: {{ $progressPercent ?? 0 }}%;"
                                 aria-valuenow="{{ $progressPercent ?? 0 }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            {{ $progressPercent ?? 0 }}% selesai. Isi semua baris agar penilaian lengkap.
                        </small>
                    </div>
                </div>

                <form id="peerReviewForm" action="{{ route('peer-review.store') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Mahasiswa</th>
                                    <th>NIM</th>
                                    <th>Kontribusi terhadap Kegiatan (25%)</th>
                                    <th>Tanggung Jawab (25%)</th>
                                    <th>Kerjasama dalam Tim (25%)</th>
                                    <th>Inisiatif dan Motivasi (25%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviewTargets as $member)
                                    @php $saved = $existingReviews[$member->mahasiswa_id] ?? null; @endphp
                                    <tr>
                                        <td>{{ $member->mahasiswa->name ?? '-' }}</td>
                                        <td>{{ $member->mahasiswa->nim ?? '-' }}</td>
                                        <td><input type="number" class="form-control score-input" name="reviews[{{ $member->mahasiswa_id }}][kontribusi_kegiatan]" min="0" max="100" step="0.01" value="{{ old('reviews.'.$member->mahasiswa_id.'.kontribusi_kegiatan', $saved->kontribusi_kegiatan ?? '') }}" required></td>
                                        <td><input type="number" class="form-control score-input" name="reviews[{{ $member->mahasiswa_id }}][tanggung_jawab]" min="0" max="100" step="0.01" value="{{ old('reviews.'.$member->mahasiswa_id.'.tanggung_jawab', $saved->tanggung_jawab ?? '') }}" required></td>
                                        <td><input type="number" class="form-control score-input" name="reviews[{{ $member->mahasiswa_id }}][kerjasama_tim]" min="0" max="100" step="0.01" value="{{ old('reviews.'.$member->mahasiswa_id.'.kerjasama_tim', $saved->kerjasama_tim ?? '') }}" required></td>
                                        <td><input type="number" class="form-control score-input" name="reviews[{{ $member->mahasiswa_id }}][inisiatif_motivasi]" min="0" max="100" step="0.01" value="{{ old('reviews.'.$member->mahasiswa_id.'.inisiatif_motivasi', $saved->inisiatif_motivasi ?? '') }}" required></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold" {{ ($isPeerReviewOpen ?? true) ? '' : 'disabled' }}>
                            <i class="fas fa-paper-plane me-2"></i>
                            Simpan Peer Review
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    Belum ada anggota lain yang bisa dinilai pada kelompok Anda.
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.score-input');
    inputs.forEach((input) => {
        input.addEventListener('input', function () {
            const value = parseFloat(this.value);
            if (!Number.isNaN(value)) {
                if (value < 0) this.value = 0;
                if (value > 100) this.value = 100;
            }
        });
    });
});
</script>
@endsection
