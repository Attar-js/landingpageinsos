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

    .group-info-table td {
        border-bottom: 1px solid #dee2e6 !important;
    }

    .rubrik-deskripsi {
        font-size: 1.2rem;
        line-height: 1.65;
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
                                <li class="breadcrumb-item"><a href="{{ route('dosen.mahasiswa-bimbingan') }}">Mahasiswa Bimbingan</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Detail Kelompok</li>
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
                <!-- Group Information -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="fa fa-info-circle me-2"></i>
                                    Informasi Kelompok
                                </h5>
                                <p class="text-white-50 mb-0 mt-1">Detail informasi kelompok mahasiswa</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('dosen.mahasiswa-bimbingan') }}" class="btn btn-light btn-sm">
                                    <i class="fa fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table group-info-table">
                                    <tr>
                                        <td width="35%" class="py-2"><strong class="text-muted">Nama Kelompok:</strong></td>
                                        <td class="py-2"><span class="fw-bold text-dark">{{ $group['nama_kelompok'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="py-2"><strong class="text-muted">Judul Kegiatan:</strong></td>
                                        <td class="py-2"><span class="text-dark">{{ $group['judul_kegiatan'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="py-2"><strong class="text-muted">Nama Mitra:</strong></td>
                                        <td class="py-2"><span class="text-dark">{{ $group['nama_mitra'] ?? 'N/A' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="py-2"><strong class="text-muted">Lokasi Mitra:</strong></td>
                                        <td class="py-2"><span class="text-dark">{{ $group['lokasi_mitra'] ?? 'N/A' }}</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table group-info-table">
                                    <tr>
                                        <td class="py-2"><strong class="text-muted">Assigned At:</strong></td>
                                        <td class="py-2">
                                            <span class="text-dark">
                                                {{ $group['assigned_at'] ? \Carbon\Carbon::parse($group['assigned_at'])->format('d/m/Y H:i') : 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2"><strong class="text-muted">Dosen Pembimbing:</strong></td>
                                        <td class="py-2">
                                            <span class="text-dark">{{ $group['dosen_name'] }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-star me-2"></i>
                            Penilaian Kelompok
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('dosen.store-penilaian-kelompok', $group['id']) }}">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="bulkPenilaianTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Proposal Kegiatan (20%)</th>
                                            <th>Asistensi (10%)</th>
                                            <th>Peer Review Tim (15%)</th>
                                            <th>Laporan Akhir (20%)</th>
                                            <th>Presentasi Akhir (15%)</th>
                                            <th>Pembimbing Lapangan (20%)</th>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group['members'] as $member)
                                            <tr class="penilaian-row">
                                                <td>
                                                    {{ $member['name'] }}
                                                    @if(!($member['peer_review_complete'] ?? false))
                                                        <br><small class="text-warning">Peer review belum lengkap</small>
                                                    @endif
                                                </td>
                                                <td><code>{{ $member['nim'] }}</code></td>
                                                <td><input type="number" name="scores[{{ $member['nim'] }}][proposal_kegiatan]" class="form-control score-input proposal-input" min="0" max="100" step="0.01" value="{{ old('scores.'.$member['nim'].'.proposal_kegiatan', $member['proposal_kegiatan'] ?? '') }}" oninput="recalcRowTotalsInline()"></td>
                                                <td><input type="number" name="scores[{{ $member['nim'] }}][asistensi]" class="form-control score-input asistensi-input" min="0" max="100" step="0.01" value="{{ old('scores.'.$member['nim'].'.asistensi', $member['asistensi'] ?? '') }}" oninput="recalcRowTotalsInline()"></td>
                                                <td><input type="number" class="form-control peer-review-auto" min="0" max="100" step="0.01" value="{{ $member['peer_review'] ?? 0 }}" readonly><input type="hidden" name="scores[{{ $member['nim'] }}][peer_review]" value="{{ $member['peer_review'] ?? 0 }}"></td>
                                                <td><input type="number" name="scores[{{ $member['nim'] }}][laporan_akhir]" class="form-control score-input laporan-input" min="0" max="100" step="0.01" value="{{ old('scores.'.$member['nim'].'.laporan_akhir', $member['laporan_akhir'] ?? '') }}" oninput="recalcRowTotalsInline()"></td>
                                                <td><input type="number" name="scores[{{ $member['nim'] }}][presentasi_akhir]" class="form-control score-input presentasi-input" min="0" max="100" step="0.01" value="{{ old('scores.'.$member['nim'].'.presentasi_akhir', $member['presentasi_akhir'] ?? '') }}" oninput="recalcRowTotalsInline()"></td>
                                                <td><input type="number" name="scores[{{ $member['nim'] }}][pembimbing_lapangan]" class="form-control score-input pembimbing-input" min="0" max="100" step="0.01" value="{{ old('scores.'.$member['nim'].'.pembimbing_lapangan', $member['pembimbing_lapangan'] ?? '') }}" oninput="recalcRowTotalsInline()"></td>
                                                <td><span class="badge bg-info member-total">0.00</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary btn-lg px-4 py-2 fw-bold">
                                    <i class="fa fa-save me-1"></i>Simpan Penilaian Kelompok (Nilai Akhir)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                    function recalcRowTotalsInline() {
                        const table = document.getElementById('bulkPenilaianTable');
                        if (!table) return;
                        const rows = table.querySelectorAll('tbody tr.penilaian-row');
                        rows.forEach((row) => {
                            const toNum = (selector) => {
                                const el = row.querySelector(selector);
                                if (!el) return 0;
                                const parsed = parseFloat(String(el.value || '').replace(',', '.'));
                                return Number.isNaN(parsed) ? 0 : parsed;
                            };

                            const proposal = toNum('.proposal-input');
                            const asistensi = toNum('.asistensi-input');
                            const laporan = toNum('.laporan-input');
                            const presentasi = toNum('.presentasi-input');
                            const pembimbing = toNum('.pembimbing-input');
                            const peer = toNum('.peer-review-auto');

                            const total = (proposal * 0.20) + (asistensi * 0.10) + (peer * 0.15) + (laporan * 0.20) + (presentasi * 0.15) + (pembimbing * 0.20);
                            const badge = row.querySelector('.member-total');
                            if (badge) badge.textContent = total.toFixed(2);
                        });
                    }

                    document.addEventListener('DOMContentLoaded', recalcRowTotalsInline);
                </script>

                @if($cpmkRubric ?? null)
                    @include('components.cpmk-rubric-form', [
                        'group' => $groupModel,
                        'rubric' => $cpmkRubric,
                        'mode' => 'readonly',
                    ])
                @else
                    <div class="alert alert-info mt-4">
                        <i class="fa fa-info-circle me-1"></i>
                        Rubrik Penilaian CPMK MK Inovasi Sosial diisi oleh ketua kelompok di halaman
                        <strong>Form Daftar Kelompok</strong> setelah kelompok disetujui.
                    </div>
                @endif

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
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const table = document.getElementById('bulkPenilaianTable');
    if (!table) return;

    const parseScore = (value) => {
        if (value === undefined || value === null || value === '') return 0;
        const normalized = String(value).replace(',', '.').trim();
        const parsed = Number.parseFloat(normalized);
        return Number.isNaN(parsed) ? 0 : parsed;
    };

    const recalcRowTotals = () => {
        const rows = table.querySelectorAll('tbody tr.penilaian-row');
        rows.forEach((row) => {
            const proposal = parseScore(row.querySelector('.proposal-input')?.value);
            const asistensi = parseScore(row.querySelector('.asistensi-input')?.value);
            const laporan = parseScore(row.querySelector('.laporan-input')?.value);
            const presentasi = parseScore(row.querySelector('.presentasi-input')?.value);
            const pembimbing = parseScore(row.querySelector('.pembimbing-input')?.value);
            const peer = parseScore(row.querySelector('.peer-review-auto')?.value);

            const total = (proposal * 0.20) + (asistensi * 0.10) + (peer * 0.15) + (laporan * 0.20) + (presentasi * 0.15) + (pembimbing * 0.20);
            const badge = row.querySelector('.member-total');
            if (badge) badge.textContent = total.toFixed(2);
        });
    };

    table.addEventListener('input', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLInputElement)) return;
        if (!target.classList.contains('score-input')) return;

        const v = parseScore(target.value);
        if (v < 0) target.value = '0';
        if (v > 100) target.value = '100';
        recalcRowTotals();
    });

    recalcRowTotals();
    window.setTimeout(recalcRowTotals, 100);
});
</script>
@endsection 
