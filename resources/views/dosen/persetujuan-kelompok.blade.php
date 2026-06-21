@extends('layout.layout')

@section('content')
<style>
    .pengajuan-page {
        max-width: 1320px;
        margin: 0 auto;
        font-size: 1.15rem;
    }

    .pengajuan-page .alert {
        font-size: 1.15rem;
    }

    .page-header-block {
        text-align: center;
        margin-bottom: 1.75rem;
    }

    .page-title {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 10px;
        font-size: 3rem;
        text-align: center;
    }

    .page-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 1.45rem;
        text-align: center;
    }

    .summary-card {
        border-radius: 12px;
        color: #fff;
        padding: 18px 20px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        min-height: 100px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .summary-card h6 {
        font-size: 1.2rem;
        margin-bottom: 8px;
        font-weight: 600;
        opacity: 0.95;
        text-align: center;
        width: 100%;
    }

    .summary-card .num {
        font-size: 2.6rem;
        font-weight: 800;
        line-height: 1;
        text-align: center;
        width: 100%;
    }

    .summary-teal { background: linear-gradient(135deg, #17a2b8, #20c997); }
    .summary-orange { background: linear-gradient(135deg, #f39c12, #f1c40f); color: #1f2937; }
    .summary-green { background: linear-gradient(135deg, #28a745, #20c997); }
    .summary-red { background: linear-gradient(135deg, #dc3545, #e83e8c); }

    .master-detail {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 991px) {
        .master-detail {
            grid-template-columns: 1fr;
        }
    }

    .group-list-panel {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: calc(100vh - 320px);
        overflow-y: auto;
        padding-right: 4px;
    }

    .group-list-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        padding: 16px 18px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .group-list-card:hover {
        border-color: #93c5fd;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12);
    }

    .group-list-card.active {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.15);
    }

    .group-list-card .card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
        padding-right: 88px;
        line-height: 1.35;
    }

    .group-list-card .card-meta {
        font-size: 1.12rem;
        color: #6b7280;
        margin-bottom: 4px;
        line-height: 1.4;
    }

    .status-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 999px;
        text-transform: lowercase;
    }

    .status-menunggu { background: #ffedd5; color: #c2410c; }
    .status-diterima { background: #d1fae5; color: #047857; }
    .status-ditolak { background: #fee2e2; color: #b91c1c; }

    .detail-panel {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        min-height: 520px;
    }

    .detail-header {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        padding: 18px 22px;
    }

    .detail-header h5,
    .detail-header p {
        color: #fff !important;
    }

    .detail-header h5 {
        font-size: 1.65rem;
        font-weight: 700;
        margin: 0 0 6px;
    }

    .detail-header p {
        margin: 0;
        font-size: 1.15rem;
        opacity: 0.95;
    }

    .detail-body {
        padding: 24px 26px;
    }

    .detail-body .section-heading {
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 24px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .info-grid { grid-template-columns: 1fr; }
    }

    .info-label {
        font-size: 1.05rem;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 1.28rem;
        font-weight: 600;
        color: #111827;
        line-height: 1.4;
    }

    .member-table thead th {
        background: #f3f4f6;
        font-size: 1.12rem;
        font-weight: 700;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        padding: 14px 16px;
    }

    .member-table td {
        font-size: 1.2rem;
        vertical-align: middle;
        padding: 12px 16px;
    }

    .doc-item-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        background: #fafafa;
        display: flex;
        flex-direction: column;
        margin-top: 20px;
        max-width: 480px;
    }

    .doc-item-card.approved { border-color: #a7f3d0; background: #f0fdf4; }
    .doc-item-card.rejected { border-color: #fecaca; background: #fff7f7; }

    .doc-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 12px;
    }

    .doc-item-label {
        font-size: 1.15rem;
        font-weight: 700;
        color: #374151;
    }

    .doc-status-pill {
        font-size: 0.9rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .doc-status-pill.menunggu { background: #ffedd5; color: #c2410c; }
    .doc-status-pill.disetujui { background: #d1fae5; color: #047857; }
    .doc-status-pill.revisi { background: #fee2e2; color: #b91c1c; }

    .doc-file-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .doc-file-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #fee2e2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .doc-file-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
        word-break: break-word;
    }

    .doc-file-meta {
        font-size: 1rem;
        color: #6b7280;
    }

    .doc-action-btn {
        margin-top: 14px;
        width: 100%;
        padding: 11px 16px;
        border-radius: 10px;
        font-size: 1.05rem;
        font-weight: 600;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        color: #374151;
        text-align: center;
        text-decoration: none;
        display: block;
    }

    .doc-action-btn:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .doc-action-btn.disabled {
        opacity: 0.55;
        cursor: not-allowed;
        pointer-events: none;
    }

    .doc-file-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 14px;
    }

    .doc-file-actions .doc-action-btn {
        margin-top: 0;
    }

    .doc-review-footer {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .doc-note-input {
        font-size: 0.95rem;
        border-radius: 8px;
        min-height: 40px;
        width: 100%;
    }

    .doc-review-panel {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .doc-review-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .doc-review-form {
        margin: 0;
        display: flex;
        width: 100%;
    }

    .doc-review-actions {
        display: flex;
        gap: 8px;
    }

    .doc-review-actions form { margin: 0; flex: 1; }

    .btn-revisi-sm,
    .btn-setuju-sm {
        width: 100%;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 10px 12px;
        border-radius: 8px;
        border: none;
    }

    .btn-revisi-sm { background: #dc3545; color: #fff; }
    .btn-revisi-sm:hover { background: #c82333; color: #fff; }
    .btn-setuju-sm { background: #28a745; color: #fff; }
    .btn-setuju-sm:hover { background: #218838; color: #fff; }

    .doc-approved-label {
        text-align: center;
        font-weight: 700;
        color: #047857;
        font-size: 1rem;
        padding: 10px 0;
    }

    .doc-revision-note {
        font-size: 0.95rem;
        color: #9a3412;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 8px;
        padding: 8px 10px;
    }

    .action-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .btn-tolak {
        background: #dc3545;
        border: none;
        color: #fff;
        font-weight: 700;
        font-size: 1.15rem;
        padding: 14px 32px;
        border-radius: 10px;
        min-width: 150px;
    }

    .btn-tolak:hover { background: #c82333; color: #fff; }

    .btn-setuju {
        background: #28a745;
        border: none;
        color: #fff;
        font-weight: 700;
        font-size: 1.15rem;
        padding: 14px 32px;
        border-radius: 10px;
        min-width: 150px;
    }

    .btn-setuju:hover { background: #218838; color: #fff; }

    .manage-section {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px dashed #e5e7eb;
        font-size: 1.15rem;
    }

    .manage-section h6 {
        font-size: 1.35rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 14px;
    }

    .manage-section .form-select,
    .manage-section .form-control {
        font-size: 1.1rem;
        min-height: 46px;
    }

    .manage-section .btn {
        font-size: 1.05rem;
        padding: 10px 18px;
    }

    .manage-section .member-drop-label {
        font-size: 1.12rem;
    }

    .student-search-item {
        font-size: 1.1rem !important;
    }

    .empty-detail {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        color: #9ca3af;
        text-align: center;
        padding: 40px;
        font-size: 1.25rem;
    }

    .group-list-panel .empty-list-text {
        font-size: 1.15rem;
    }

    #approvalConfirmModal .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
    }

    #approvalConfirmModal .modal-body,
    #approvalConfirmModal .form-check-label {
        font-size: 1.15rem;
    }

    #approvalConfirmModal .modal-footer .btn {
        font-size: 1.1rem;
        padding: 10px 22px;
    }

    .reject-inline {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .reject-inline .form-control {
        max-width: 280px;
        border-radius: 8px;
    }
</style>

<x-header/>
<div style="height: 140px;"></div>

<div class="container mb-5 pengajuan-page">
    <div class="page-header-block">
        <h2 class="page-title">Pengajuan Kelompok</h2>
        <p class="page-subtitle">Monitoring pengajuan dan pengelolaan anggota kelompok.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="summary-card summary-teal">
                <h6>Total Kelompok</h6>
                <div class="num">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card summary-orange">
                <h6>Menunggu Persetujuan</h6>
                <div class="num">{{ $stats['menunggu'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card summary-green">
                <h6>Total Kelompok Diterima</h6>
                <div class="num">{{ $stats['diterima'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card summary-red">
                <h6>Total Kelompok Ditolak</h6>
                <div class="num">{{ $stats['ditolak'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="master-detail">
        {{-- Sidebar daftar kelompok --}}
        <div class="group-list-panel">
            @forelse($sidebarItems as $item)
                <a href="{{ route('dosen.persetujuan-kelompok', ['group' => $item['group_id']]) }}"
                   class="group-list-card {{ ($selectedItem['group_id'] ?? null) === $item['group_id'] ? 'active' : '' }}">
                    <span class="status-badge status-{{ $item['status_key'] }}">{{ $item['status_label'] }}</span>
                    <div class="card-title">{{ $item['nama_kelompok'] }}</div>
                    <div class="card-meta">Diajukan oleh {{ $item['pengaju'] }}</div>
                    <div class="card-meta">
                        {{ $item['nama_mitra'] ?? '-' }}
                        @if($item['lokasi_mitra'])
                            &bull; {{ $item['lokasi_mitra'] }}
                        @endif
                    </div>
                </a>
            @empty
                <div class="text-muted text-center py-4 empty-list-text">Belum ada pengajuan kelompok.</div>
            @endforelse
        </div>

        {{-- Panel detail --}}
        <div class="detail-panel">
            @if($selectedItem)
                <div class="detail-header">
                    <h5>Detail Pengajuan Mahasiswa</h5>
                    <p>Daftar anggota kelompok mahasiswa yang mengajukan diri.</p>
                </div>
                <div class="detail-body">
                    <div class="info-grid">
                        <div>
                            <div class="info-label">Judul Kegiatan</div>
                            <div class="info-value">{{ $selectedItem['judul_kegiatan'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Nama Kelompok</div>
                            <div class="info-value">{{ $selectedItem['nama_kelompok'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Nama Mitra</div>
                            <div class="info-value">{{ $selectedItem['nama_mitra'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="info-label">Lokasi Mitra</div>
                            <div class="info-value">{{ $selectedItem['lokasi_mitra'] ?? '-' }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 section-heading">Anggota Kelompok</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered member-table mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIM</th>
                                    <th>Peran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($selectedItem['members'] as $member)
                                    <tr>
                                        <td>{{ $member['name'] }}</td>
                                        <td>{{ $member['nim'] }}</td>
                                        <td>{{ $member['role'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada anggota.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($selectedItem['proposal_card'] ?? null)
                        @include('dosen.partials.doc-validasi-card', [
                            'doc' => $selectedItem['proposal_card'],
                            'groupId' => $selectedItem['group_id'],
                            'approveRoute' => route('dosen.persetujuan-kelompok.proposal.approve', $selectedItem['group_id']),
                            'rejectRoute' => route('dosen.persetujuan-kelompok.proposal.reject', $selectedItem['group_id']),
                        ])
                    @endif

                    @if($selectedItem['show_group_approval_actions'] ?? false)
                        <div class="action-footer">
                            <form action="{{ route('dosen.persetujuan-kelompok.reject', $selectedItem['request_id']) }}" method="POST" class="reject-inline m-0">
                                @csrf
                                <input type="hidden" name="note" value="">
                                <button type="submit" class="btn btn-tolak" onclick="return confirm('Yakin ingin menolak pengajuan kelompok ini?');">
                                    Tolak Kelompok
                                </button>
                            </form>
                            <form id="approveForm-{{ $selectedItem['request_id'] }}" action="{{ route('dosen.persetujuan-kelompok.approve', $selectedItem['request_id']) }}" method="POST" class="m-0">
                                @csrf
                                <button type="button"
                                        class="btn btn-setuju open-approve-modal-btn {{ ($selectedItem['can_approve'] ?? false) ? '' : 'disabled' }}"
                                        data-request-id="{{ $selectedItem['request_id'] }}"
                                        data-group-name="{{ $selectedItem['nama_kelompok'] }}"
                                        @if(!($selectedItem['can_approve'] ?? false)) disabled @endif>
                                    Setujui Kelompok
                                </button>
                            </form>
                        </div>
                    @endif

                    @php
                        $group = $selectedItem['group'];
                        $activeMembers = $group->members->where('status', 'active');
                        $droppedMembers = $group->members->where('status', 'dropped');
                        $currentLeader = $activeMembers->firstWhere('role', 'leader');
                    @endphp

                    @if(($selectedItem['status_key'] ?? '') === 'diterima')
                        <div class="manage-section">
                            <h6><i class="fas fa-users-cog me-1"></i> Kelola Anggota Kelompok</h6>

                            @if(!$currentLeader && $activeMembers->count() > 0)
                                <div class="alert alert-warning py-3 mb-3" style="font-size: 1.1rem;">Belum ada ketua aktif.</div>
                                <form action="{{ route('dosen.groups.assign-leader', $group->id) }}" method="POST" class="d-flex gap-2 flex-wrap mb-3">
                                    @csrf
                                    <select name="mahasiswa_id" class="form-select" style="max-width: 380px;" required>
                                        <option value="">Pilih ketua pengganti</option>
                                        @foreach($activeMembers as $m)
                                            <option value="{{ $m->mahasiswa_id }}">{{ $m->mahasiswa->name ?? '-' }} ({{ $m->mahasiswa->nim ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-warning fw-bold">Tetapkan Ketua</button>
                                </form>
                            @endif

                            @if($droppedMembers->isNotEmpty())
                                <p class="fw-semibold text-muted mb-2 member-drop-label">Anggota Drop</p>
                                @foreach($droppedMembers as $member)
                                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                        <span class="member-drop-label">{{ $member->mahasiswa->name ?? '-' }} ({{ $member->mahasiswa->nim ?? '-' }})</span>
                                        <form action="{{ route('dosen.groups.re-add', $group->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="mahasiswa_id" value="{{ $member->mahasiswa_id }}">
                                            <button type="submit" class="btn btn-primary">Tambah Kembali</button>
                                        </form>
                                    </div>
                                @endforeach
                            @endif

                            <p class="fw-semibold text-muted mb-2 mt-3 member-drop-label">Tambah Anggota Baru</p>
                            <form action="{{ route('dosen.groups.re-add', $group->id) }}" method="POST" class="d-flex gap-2 flex-wrap align-items-start add-member-form">
                                @csrf
                                <input type="hidden" name="mahasiswa_id" class="selected-student-id" required>
                                <div style="min-width: 300px; position: relative;">
                                    <input type="text" class="form-control student-search-input" placeholder="Cari mahasiswa (nama/NIM)..." autocomplete="off">
                                    <div class="student-search-results border rounded bg-white mt-1 p-2 d-none" style="max-height: 160px; overflow-y: auto; position: absolute; width: 100%; z-index: 20;">
                                        @foreach($availableStudents as $student)
                                            <button type="button" class="btn btn-link text-start text-decoration-none w-100 p-1 student-search-item" data-id="{{ $student->id }}" data-label="{{ $student->name }} ({{ $student->nim ?? '-' }})">
                                                {{ $student->name }} ({{ $student->nim ?? '-' }})
                                            </button>
                                        @endforeach
                                        <div class="student-search-empty text-muted small d-none">Tidak ada hasil.</div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success fw-bold">Tambah Anggota</button>
                            </form>

                            @if(in_array(Auth::user()->role, ['dosen', 'admin']))
                                <form action="{{ route('groups.delete', $group->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Yakin ingin menghapus kelompok ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">Hapus Kelompok</button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div class="empty-detail">
                    <div>
                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                        <p class="mb-0">Pilih kelompok di sebelah kiri untuk melihat detail pengajuan.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="approvalConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan Dosen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-2">
                    Silakan baca dokumen kesediaan dosen sebelum menyetujui kelompok
                    <strong id="approvalTargetGroupName">-</strong>.
                </p>
                <div style="height: 70vh; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                    <iframe src="{{ route('dosen.persetujuan-kelompok.kesediaan-preview') }}" width="100%" height="100%" style="border:0;" title="Dokumen Kesediaan"></iframe>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="approvalAgreementCheck">
                    <label class="form-check-label" for="approvalAgreementCheck">
                        Saya telah membaca dokumen kesediaan dosen dan menyetujui kelompok ini.
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="finalApproveBtn" class="btn btn-success" disabled>Ya, Setujui Kelompok</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.add-member-form').forEach((form) => {
        const searchInput = form.querySelector('.student-search-input');
        const resultsBox = form.querySelector('.student-search-results');
        const hiddenId = form.querySelector('.selected-student-id');
        const items = Array.from(form.querySelectorAll('.student-search-item'));
        const emptyState = form.querySelector('.student-search-empty');
        if (!searchInput || !hiddenId) return;

        const filterItems = () => {
            const keyword = searchInput.value.toLowerCase().trim();
            let visible = 0;
            items.forEach((item) => {
                const show = keyword !== '' && (item.dataset.label || '').toLowerCase().includes(keyword);
                item.classList.toggle('d-none', !show);
                if (show) visible++;
            });
            resultsBox?.classList.toggle('d-none', keyword === '');
            emptyState?.classList.toggle('d-none', visible > 0 || keyword === '');
        };

        searchInput.addEventListener('input', () => { hiddenId.value = ''; filterItems(); });
        searchInput.addEventListener('focus', filterItems);
        items.forEach((item) => {
            item.addEventListener('click', () => {
                hiddenId.value = item.dataset.id;
                searchInput.value = item.dataset.label;
                resultsBox?.classList.add('d-none');
            });
        });
        form.addEventListener('submit', (e) => {
            if (!hiddenId.value) { e.preventDefault(); alert('Pilih mahasiswa dari hasil pencarian.'); }
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.add-member-form')) {
            document.querySelectorAll('.student-search-results').forEach((b) => b.classList.add('d-none'));
        }
    });

    const modalEl = document.getElementById('approvalConfirmModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const agreementCheck = document.getElementById('approvalAgreementCheck');
    const finalApproveBtn = document.getElementById('finalApproveBtn');
    const targetNameEl = document.getElementById('approvalTargetGroupName');
    let targetFormId = null;

    document.querySelectorAll('.open-approve-modal-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            targetFormId = 'approveForm-' + btn.dataset.requestId;
            if (targetNameEl) targetNameEl.textContent = btn.dataset.groupName || '-';
            if (agreementCheck) agreementCheck.checked = false;
            if (finalApproveBtn) finalApproveBtn.disabled = true;
            modal?.show();
        });
    });

    agreementCheck?.addEventListener('change', () => {
        if (finalApproveBtn) finalApproveBtn.disabled = !agreementCheck.checked;
    });

    finalApproveBtn?.addEventListener('click', () => {
        if (!targetFormId || !agreementCheck?.checked) return;
        document.getElementById(targetFormId)?.submit();
    });
});
</script>
@endsection
