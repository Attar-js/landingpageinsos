@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
@php
    $statusLabels = [
        'waiting_supervisor_approval' => 'Menunggu Persetujuan Dosen',
        'waiting_leader_replacement' => 'Menunggu Ketua Pengganti',
        'active' => 'Aktif',
        'rejected' => 'Ditolak',
        'dropped' => 'Drop',
        'assigned' => 'Di-assign',
        'pending' => 'Menunggu',
    ];
@endphp
<style>
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .btn-primary {
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    #proposalDropzone,
    #proposalReuploadDropzone {
        transition: all 0.3s ease;
    }
    
    #proposalDropzone:hover,
    #proposalReuploadDropzone:hover {
        border-color: #0d6efd !important;
        background-color: #f8f9fa !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    /* Memastikan semua field anggota memiliki ukuran yang sama */
    .member-field {
        height: 50px !important;
        font-size: 16px !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        font-weight: 400 !important;
        padding: 12px 15px !important;
        line-height: 1.4 !important;
        box-sizing: border-box !important;
        width: 100% !important;
        min-width: 200px !important;
    }
    
    /* Override Bootstrap untuk select */
    .form-select.member-field {
        background-image: none !important;
        padding-right: 15px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
    }
    
    /* Menghilangkan dropdown arrow di semua browser */
    .form-select.member-field::-ms-expand {
        display: none !important;
    }
    
    /* Memastikan kolom memiliki lebar yang sama */
    .member-col {
        min-width: 0 !important;
        flex: 1 !important;
    }

    .dosen-item, .member-item {
        cursor: pointer;
        border-radius: 6px;
        padding-left: 8px;
        display: none;
    }

    .dosen-item.active, .member-item.active {
        background-color: #e9f2ff;
    }

    .dosen-item input, .member-item input {
        margin-right: 8px;
    }

    .search-select-wrapper {
        position: relative;
    }

    .search-result-list {
        position: absolute;
        width: 100%;
        top: calc(100% + 4px);
        left: 0;
        z-index: 20;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px;
        max-height: 240px;
        overflow-y: auto;
        display: none;
        font-size: 16px;
    }

    #memberSearch {
        min-height: 50px;
        line-height: 1.4;
        resize: none;
        overflow: hidden;
        white-space: pre-wrap;
        word-break: break-word;
        font-size: 16px;
    }

    #dosenSearch {
        font-size: 16px;
    }

    .dosen-item, .member-item {
        font-size: 16px;
    }

    .group-info-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.06);
    }

    .group-info-title {
        font-size: 1.8rem !important;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .group-info-item {
        font-size: 1.4rem !important;
        color: #495057;
        line-height: 1.6;
    }

    .group-info-label {
        font-weight: 600;
        color: #212529;
    }

    .group-member-table thead th {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-size: 1.3rem !important;
        padding: 16px 14px;
    }

    .group-member-table tbody td {
        font-size: 1.28rem !important;
        padding: 14px;
    }

    .status-pill {
        display: inline-block;
        padding: 7px 16px;
        border-radius: 999px;
        font-size: 1.15rem !important;
        font-weight: 600;
        text-transform: capitalize;
        background: #eef3ff;
        color: #3359b8;
    }

    .group-info-card {
        font-size: 1.3rem;
    }

    .drop-form-label {
        font-size: 1.15rem;
        font-weight: 700;
        color: #212529;
    }

    .drop-reason-input {
        height: 52px;
        border-radius: 10px;
        font-size: 1rem;
        border: 1px solid #dfe3e8;
    }

    .drop-btn {
        height: 52px;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        border: none;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: #fff;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.28);
    }

    .drop-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(220, 53, 69, 0.32);
        background: linear-gradient(135deg, #c82333 0%, #b71c2b 100%);
    }

    .proposal-status-card {
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 1.25rem;
        font-size: 1.15rem;
    }

    .proposal-status-card .status-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .proposal-status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .proposal-status-note {
        margin-top: 12px;
        padding: 12px 14px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.65);
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .proposal-reupload-box {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        background: #fafafa;
        margin-bottom: 1.25rem;
    }
</style>
<x-header/>
<div style="height: 140px;"></div>

<div class="container">
    <div class="card shadow mb-4" style="max-width: 1200px; margin: 0 auto;">
        <div class="section-title text-center py-4">
            <h4 class="fw-bold mb-0">Form Daftar Kelompok</h4>
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
            @if($myGroup)
                 <div class="alert alert-info">
                    Anda saat ini berada di kelompok <strong>{{ $myGroup->nama_kelompok }}</strong> dengan status
                    <strong>{{ $statusLabels[$myGroup->status] ?? $myGroup->status }}</strong>.
                 </div>
                 <div class="card mb-4 group-info-card">
                    <div class="card-body">
                        <div class="group-info-title">Detail Kelompok</div>
                        <div class="row g-3">
                            <div class="col-md-6 group-info-item"><span class="group-info-label">Nama Kelompok:</span> {{ $myGroup->nama_kelompok ?? '-' }}</div>
                            <div class="col-md-6 group-info-item"><span class="group-info-label">Status:</span> <span class="status-pill">{{ $statusLabels[$myGroup->status] ?? ($myGroup->status ?? '-') }}</span></div>
                            <div class="col-md-6 group-info-item"><span class="group-info-label">Ketua:</span> {{ $myGroup->groupLeader->name ?? '-' }} ({{ $myGroup->groupLeader->nim ?? '-' }})</div>
                            <div class="col-md-6 group-info-item"><span class="group-info-label">Dosen Pembimbing:</span> {{ $myGroup->dosen->name ?? 'Belum dipilih' }}{{ $myGroup->dosen ? ' ('.($myGroup->dosen->nip ?? $myGroup->dosen->email).')' : '' }}</div>
                            <div class="col-md-6 group-info-item"><span class="group-info-label">Judul Kegiatan:</span> {{ $myGroup->judul_kegiatan ?? '-' }}</div>
                            <div class="col-md-6 group-info-item"><span class="group-info-label">Nama Mitra:</span> {{ $myGroup->nama_mitra ?? '-' }}</div>
                            <div class="col-md-6 group-info-item"><span class="group-info-label">Lokasi Mitra:</span> {{ $myGroup->lokasi_mitra ?? '-' }}</div>
                            <div class="col-md-6 group-info-item"><span class="group-info-label">Catatan:</span> {{ $myGroup->catatan ?? '-' }}</div>
                            @if($proposalStatus ?? null)
                                <div class="col-md-6 group-info-item">
                                    <span class="group-info-label">Status Proposal:</span>
                                    <span class="status-pill" style="background: {{ $proposalStatus['ui_status'] === 'disetujui' ? '#d1fae5' : ($proposalStatus['ui_status'] === 'revisi' ? '#fee2e2' : '#ffedd5') }}; color: {{ $proposalStatus['ui_status'] === 'disetujui' ? '#047857' : ($proposalStatus['ui_status'] === 'revisi' ? '#b91c1c' : '#c2410c') }};">
                                        {{ $proposalStatus['status_label'] }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <hr>
                        <div class="group-info-title mb-2">Daftar Anggota</div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0 group-member-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>Peran</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($myGroup->members as $member)
                                        <tr>
                                            <td>{{ $member->mahasiswa->name ?? '-' }}</td>
                                            <td>{{ $member->mahasiswa->nim ?? '-' }}</td>
                                            <td>{{ $member->role === 'leader' ? 'Ketua' : ($member->role === 'member' ? 'Anggota' : $member->role) }}</td>
                                            <td>{{ $statusLabels[$member->status] ?? $member->status }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada anggota.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                 </div>

                @if($proposalStatus ?? null)
                    @if(!($proposalStatus['uploaded'] ?? false))
                        <div class="proposal-reupload-box">
                            <div class="group-info-title mb-2">Unggah Proposal</div>
                            <p class="text-muted mb-3">
                                Dosen pembimbing: <strong>{{ $proposalStatus['dosen_name'] }}</strong>
                            </p>
                            @if($proposalStatus['can_reupload'] ?? false)
                                <form action="{{ route('kkn.groups.proposal-reupload', $myGroup->id) }}" method="POST" enctype="multipart/form-data" id="proposalReuploadForm">
                                    @csrf
                                    <label class="form-label fw-bold">File Proposal (PDF, maks. 10 MB)</label>
                                    <div id="proposalReuploadDropzone" class="p-4 border-2 border-dashed border-secondary rounded bg-light text-center" style="cursor: pointer; min-height: 120px;">
                                        <input type="file" id="proposalReuploadFile" name="proposal_file" class="d-none" required accept="application/pdf">
                                        <div class="mb-2"><i class="fas fa-cloud-upload-alt fa-2x text-muted"></i></div>
                                        <div id="proposalReuploadText" class="text-muted">Klik atau seret file PDF proposal di sini</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary fw-bold mt-3">
                                        <i class="fas fa-upload me-1"></i> Kirim Proposal
                                    </button>
                                </form>
                            @elseif($proposalStatus['is_leader'] ?? false)
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    Unggah proposal tidak tersedia pada status kelompok saat ini.
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    Proposal belum diunggah. Hanya <strong>ketua kelompok</strong> yang dapat mengunggah setelah kelompok disetujui.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="proposal-status-card alert alert-{{ $proposalStatus['badge_class'] }}">
                            <div class="status-title">Validasi Proposal — {{ $proposalStatus['group_name'] }}</div>
                            <div class="mb-2 text-muted">Dosen pembimbing: <strong>{{ $proposalStatus['dosen_name'] }}</strong></div>
                            <span class="proposal-status-badge bg-{{ $proposalStatus['badge_class'] }} text-white">
                                @if($proposalStatus['ui_status'] === 'revisi')
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                @elseif($proposalStatus['ui_status'] === 'disetujui')
                                    <i class="fas fa-check-circle me-1"></i>
                                @else
                                    <i class="fas fa-clock me-1"></i>
                                @endif
                                {{ $proposalStatus['status_label'] }}
                            </span>

                            @if($proposalStatus['ui_status'] === 'revisi' && !empty($proposalStatus['review_note']))
                                <div class="proposal-status-note">
                                    <strong>Catatan dari dosen:</strong><br>
                                    {{ $proposalStatus['review_note'] }}
                                </div>
                            @elseif($proposalStatus['ui_status'] === 'revisi')
                                <div class="proposal-status-note">
                                    Dosen meminta revisi proposal. Ketua kelompok silakan unggah ulang file proposal di bawah.
                                </div>
                            @elseif($proposalStatus['ui_status'] === 'menunggu')
                                <div class="proposal-status-note">
                                    Proposal sedang ditinjau dosen pembimbing. Mohon tunggu hasil validasi.
                                </div>
                            @elseif($proposalStatus['ui_status'] === 'disetujui')
                                <div class="proposal-status-note">
                                    @if($myGroup->status === 'waiting_supervisor_approval')
                                        Proposal telah disetujui dosen. Kelompok menunggu persetujuan akhir dari dosen.
                                    @else
                                        Proposal telah disetujui dosen pembimbing.
                                    @endif
                                    @if($proposalStatus['reviewed_at'])
                                        <br><small>Disetujui pada {{ $proposalStatus['reviewed_at']->timezone(config('app.timezone'))->format('d M Y H:i') }}</small>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($proposalStatus['can_reupload'] ?? false)
                            <div class="proposal-reupload-box">
                                <div class="group-info-title mb-3">Unggah Ulang Proposal</div>
                                <form action="{{ route('kkn.groups.proposal-reupload', $myGroup->id) }}" method="POST" enctype="multipart/form-data" id="proposalReuploadForm">
                                    @csrf
                                    <label class="form-label fw-bold">File Proposal (PDF, maks. 10 MB)</label>
                                    <div id="proposalReuploadDropzone" class="p-4 border-2 border-dashed border-secondary rounded bg-light text-center" style="cursor: pointer; min-height: 100px;">
                                        <input type="file" id="proposalReuploadFile" name="proposal_file" class="d-none" required accept="application/pdf">
                                        <div id="proposalReuploadText" class="text-muted">Klik atau seret file PDF proposal di sini</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary fw-bold mt-3">
                                        <i class="fas fa-upload me-1"></i> Kirim Proposal Revisi
                                    </button>
                                </form>
                            </div>
                        @elseif(!($proposalStatus['is_leader'] ?? false) && ($proposalStatus['needs_revision'] ?? false))
                            <div class="alert alert-warning">
                                Hanya <strong>ketua kelompok</strong> yang dapat mengunggah ulang proposal.
                            </div>
                        @endif
                    @endif
                @endif

                 @if($myGroup->status === 'waiting_supervisor_approval')
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-1"></i>
                        Kelompok Anda menunggu persetujuan dosen pembimbing.
                        Setelah disetujui, ketua kelompok dapat mengisi <strong>deskripsi kegiatan</strong> pada Rubrik CPMK di halaman ini. Kolom skor diisi oleh Tim MK Penciri.
                    </div>
                 @elseif($myGroup->isSupervisorApproved())
                    @if($canFillCpmkRubric ?? false)
                        @include('components.cpmk-rubric-form', [
                            'group' => $myGroup,
                            'rubric' => $cpmkRubric ?? null,
                            'mode' => 'mahasiswa',
                        ])
                    @elseif($cpmkRubric ?? null)
                        @include('components.cpmk-rubric-form', [
                            'group' => $myGroup,
                            'rubric' => $cpmkRubric,
                            'mode' => 'readonly',
                        ])
                    @else
                        <div class="alert alert-info">
                            Rubrik Penilaian CPMK belum diisi. Ketua kelompok dapat mengisi setelah kelompok aktif.
                        </div>
                    @endif
                 @endif

                 <div class="mb-4">
                    <label class="form-label drop-form-label">Keluar dari Kelompok</label>
                    <form action="{{ route('kkn.groups.drop-self', $myGroup->id) }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-9">
                                <input type="text" class="form-control drop-reason-input" name="drop_reason" placeholder="Tulis alasan keluar kelompok" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn drop-btn w-100">Drop Diri</button>
                            </div>
                        </div>
                    </form>
                 </div>
            @else
            <form id="kknForm" action="{{ route('kkn.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_nim" value="{{ Auth::user()->nim ?? '10221051' }}">
                 
                <!-- Judul Kegiatan -->
                <div class="mb-4">
                    <label for="judul_kegiatan" class="form-label fw-bold">Judul Kegiatan</label>
                    <input type="text" class="form-control @error('judul_kegiatan') is-invalid @enderror" 
                    id="judul_kegiatan" name="judul_kegiatan" value="{{ old('judul_kegiatan') }}" 
                    placeholder="Masukkan judul kegiatan kelompok" required style="height: 50px; font-size: 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-weight: 400;">
                @error('judul_kegiatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>

                <div class="mb-4">
                    <label for="nama_kelompok" class="form-label fw-bold">Nama Kelompok</label>
                    <input type="text" class="form-control @error('nama_kelompok') is-invalid @enderror"
                    id="nama_kelompok" name="nama_kelompok" value="{{ old('nama_kelompok') }}"
                    placeholder="Masukkan nama kelompok" required style="height: 50px; font-size: 16px;">
                    @error('nama_kelompok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Pilih Dosen Pembimbing</label>
                    <div class="search-select-wrapper">
                        <input type="text" id="dosenSearch" class="form-control" placeholder="Cari dosen berdasarkan nama...">
                        <div id="dosenList" class="search-result-list">
                            @foreach($dosenList as $dosen)
                                <label class="py-1 dosen-item">
                                    <input type="radio" name="dosen_id" value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'checked' : '' }} required>
                                    {{ $dosen->name }} ({{ $dosen->nip ?? $dosen->email }})
                                </label>
                            @endforeach
                            <div id="dosenNoResult" class="text-muted small d-none">Tidak ada dosen yang cocok.</div>
                        </div>
                    </div>
                    @error('dosen_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Pilih Anggota Kelompok (2-10 Mahasiswa)</label>
                    <div class="search-select-wrapper">
                        <textarea id="memberSearch" class="form-control" placeholder="Cari mahasiswa berdasarkan nama..." rows="1"></textarea>
                        <div id="memberList" class="search-result-list">
                            @foreach($mahasiswaList as $mahasiswa)
                                <label class="py-1 member-item">
                                    <input type="checkbox"
                                           name="member_ids[]"
                                           value="{{ $mahasiswa->id }}"
                                           {{ in_array($mahasiswa->id, old('member_ids', [Auth::id()])) ? 'checked' : '' }}
                                           {{ $mahasiswa->id === Auth::id() ? 'data-self-member=1' : '' }}>
                                    {{ $mahasiswa->name }} ({{ $mahasiswa->nim ?? '-' }}){{ $mahasiswa->id === Auth::id() ? ' - Anda (Ketua)' : '' }}
                                </label>
                            @endforeach
                            <div id="memberNoResult" class="text-muted small d-none">Tidak ada mahasiswa yang cocok.</div>
                        </div>
                    </div>
                    <small class="text-muted">Mahasiswa login harus tetap dipilih karena otomatis menjadi ketua kelompok.</small>
                    @error('member_ids')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    @error('member_ids.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <!-- Mitra & Lokasi Mitra -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="mitra" class="form-label fw-bold">Nama Mitra</label>
                        <input type="text" class="form-control" id="mitra" name="mitra" 
                               placeholder="Masukkan nama mitra" style="height: 50px; font-size: 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-weight: 400;">
                    </div>
                    <div class="col-md-6">
                        <label for="lokasi_mitra" class="form-label fw-bold">Lokasi Mitra <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('lokasi_mitra') is-invalid @enderror" 
                               id="lokasi_mitra" name="lokasi_mitra" value="{{ old('lokasi_mitra') }}" 
                               placeholder="Masukkan lokasi mitra" required style="height: 50px; font-size: 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-weight: 400;">
                        @error('lokasi_mitra')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Upload Dokumen -->
                <div class="card shadow-sm mt-4 mb-4" style="max-width: 600px; margin: 0 auto;">
                    <div class="card-header text-center py-3">
                        <strong>Upload Dokumen Kelompok</strong>
                    </div>
                    <div class="card-body p-4">
                            <label class="form-label fw-bold">Upload Proposal <span class="text-muted fw-normal">(opsional)</span></label>
                            <p class="text-muted small mb-2">Proposal dapat diunggah nanti setelah kelompok disetujui dosen pembimbing.</p>
                            <div id="proposalDropzone" class="p-4 border-2 border-dashed border-secondary rounded bg-light text-center" style="cursor: pointer; min-height: 120px;">
                                <input type="file" id="proposalFile" name="proposal_file" class="form-control d-none" accept="application/pdf">
                                <div class="mb-2">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                </div>
                                <div id="proposalDropzoneText" class="text-muted">Seret dan lepas file PDF di sini atau klik untuk memilih file (opsional)</div>
                                <div class="invalid-feedback mt-2 d-none" id="proposalFileError">File proposal harus berformat PDF maksimal 10 MB.</div>
                            </div>
                            @error('proposal_file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold" style="min-width: 200px;">
                        <i class="fas fa-paper-plane me-2"></i>
                        Submit Form
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

<!-- Validasi Prodi dan Dropzone Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('kknForm');
    const proposalDropzone = document.getElementById('proposalDropzone');
    const proposalDropzoneText = document.getElementById('proposalDropzoneText');
    const proposalFileError = document.getElementById('proposalFileError');
    const proposalFileInput = document.getElementById('proposalFile');
    const dosenSearch = document.getElementById('dosenSearch');
    const memberSearch = document.getElementById('memberSearch');
    const autoResizeTextarea = (textarea) => {
        if (!textarea) return;
        textarea.style.height = 'auto';
        textarea.style.height = `${Math.max(textarea.scrollHeight, 50)}px`;
    };

    const memberCheckboxes = Array.from(document.querySelectorAll('input[name="member_ids[]"]'));
    const selfMemberCheckbox = document.querySelector('input[name="member_ids[]"][data-self-member="1"]');
    const dosenNoResult = document.getElementById('dosenNoResult');
    const memberNoResult = document.getElementById('memberNoResult');
    const dosenItems = Array.from(document.querySelectorAll('.dosen-item'));
    const memberItems = Array.from(document.querySelectorAll('.member-item'));
    const dosenList = document.getElementById('dosenList');
    const memberList = document.getElementById('memberList');

    if (!form || !proposalFileInput || !proposalDropzone || !proposalDropzoneText || !proposalFileError) {
        return;
    }

    const filterList = (searchInput, itemSelector, noResultElement, listContainer) => {
        if (!searchInput) return;
        const items = Array.from(document.querySelectorAll(itemSelector));

        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();

            if (keyword === '') {
                items.forEach((item) => {
                    item.style.display = 'none';
                });
                if (listContainer) listContainer.style.display = 'none';
                if (noResultElement) {
                    noResultElement.classList.add('d-none');
                }
                return;
            }

            if (listContainer) listContainer.style.display = 'block';

            let visibleCount = 0;
            items.forEach((item) => {
                const content = (item.textContent || '').toLowerCase();
                const isVisible = keyword === '' || content.includes(keyword);
                item.style.display = isVisible ? 'block' : 'none';
                if (isVisible) {
                    visibleCount++;
                }
            });

            if (noResultElement) {
                noResultElement.classList.toggle('d-none', visibleCount > 0);
            }
        });

        items.forEach((item) => {
            item.style.display = 'none';
        });
        if (listContainer) {
            listContainer.style.display = 'none';
        }
        if (noResultElement) {
            noResultElement.classList.add('d-none');
        }
    };

    filterList(dosenSearch, '.dosen-item', dosenNoResult, dosenList);
    filterList(memberSearch, '.member-item', memberNoResult, memberList);

    const updateDosenActiveState = () => {
        dosenItems.forEach((item) => {
            const radio = item.querySelector('input[type="radio"]');
            item.classList.toggle('active', !!radio && radio.checked);
        });

        const selectedRadio = document.querySelector('input[name="dosen_id"]:checked');
        if (selectedRadio) {
            const labelText = selectedRadio.closest('label')?.textContent?.trim() || '';
            dosenSearch.value = labelText;
        }
    };

    const updateMemberActiveState = () => {
        memberItems.forEach((item) => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            item.classList.toggle('active', !!checkbox && checkbox.checked);
        });

        const selectedNames = memberCheckboxes
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.closest('label')?.textContent?.trim())
            .filter(Boolean);

        memberSearch.value = selectedNames.join(', ');
        autoResizeTextarea(memberSearch);
    };

    dosenItems.forEach((item) => {
        item.addEventListener('click', function (event) {
            if (event.target.tagName.toLowerCase() !== 'input') {
                const radio = item.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            }
            updateDosenActiveState();
            if (dosenList) {
                dosenList.style.display = 'none';
            }
        });
    });

    memberItems.forEach((item) => {
        item.addEventListener('click', function (event) {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (!checkbox) return;

            if (event.target.tagName.toLowerCase() !== 'input') {
                checkbox.checked = !checkbox.checked;
            }

            if (checkbox.dataset.selfMember === '1' && !checkbox.checked) {
                checkbox.checked = true;
                alert('Akun Anda harus tetap dipilih sebagai ketua kelompok.');
            }

            updateMemberActiveState();
            if (memberList) {
                memberList.style.display = 'none';
            }
        });
    });

    dosenSearch.addEventListener('focus', function () {
        dosenSearch.value = '';
        if (dosenList) {
            dosenList.style.display = 'block';
        }
    });

    memberSearch.addEventListener('focus', function () {
        memberSearch.value = '';
        autoResizeTextarea(memberSearch);
        if (memberList) {
            memberList.style.display = 'block';
        }
    });

    memberSearch.addEventListener('input', function () {
        autoResizeTextarea(memberSearch);
    });

    document.addEventListener('click', function (event) {
        const dosenWrapper = dosenSearch.closest('.search-select-wrapper');
        const memberWrapper = memberSearch.closest('.search-select-wrapper');

        if (dosenWrapper && !dosenWrapper.contains(event.target)) {
            if (dosenList) dosenList.style.display = 'none';
            updateDosenActiveState();
        }

        if (memberWrapper && !memberWrapper.contains(event.target)) {
            if (memberList) memberList.style.display = 'none';
            updateMemberActiveState();
        }
    });

    if (selfMemberCheckbox) {
        selfMemberCheckbox.addEventListener('change', function () {
            if (!selfMemberCheckbox.checked) {
                selfMemberCheckbox.checked = true;
                alert('Akun Anda harus tetap dipilih sebagai ketua kelompok.');
            }
            updateMemberActiveState();
        });
    }

    updateDosenActiveState();
    updateMemberActiveState();
    autoResizeTextarea(memberSearch);

    proposalDropzone.addEventListener('click', () => proposalFileInput.click());

    proposalFileInput.addEventListener('change', () => {
        if (proposalFileInput.files.length > 0) {
            const file = proposalFileInput.files[0];
            if (file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024) {
                proposalDropzoneText.innerText = 'Hanya file PDF maksimal 10 MB yang diperbolehkan!';
                proposalDropzone.classList.add('border-danger');
                proposalFileError.classList.remove('d-none');
                proposalFileInput.value = '';
            } else {
                proposalDropzoneText.innerText = file.name;
                proposalDropzone.classList.remove('border-danger');
                proposalFileError.classList.add('d-none');
            }
        }
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
        proposalDropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            proposalDropzone.classList.add('bg-primary', 'text-white');
            proposalDropzoneText.innerText = 'Lepaskan file di sini';
        });
    });

    proposalDropzone.addEventListener('dragleave', () => {
        proposalDropzone.classList.remove('bg-primary', 'text-white');
        proposalDropzoneText.innerText = proposalFileInput.files.length > 0 ? proposalFileInput.files[0].name : 'Seret dan lepas file PDF di sini atau klik untuk memilih file';
    });

    proposalDropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        proposalDropzone.classList.remove('bg-primary', 'text-white');
        const file = e.dataTransfer.files?.[0];
        if (file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            proposalFileInput.files = dataTransfer.files;
            proposalFileInput.dispatchEvent(new Event('change'));
        }
    });

    form.addEventListener('submit', function (e) {
        if (proposalFileInput.files.length > 0) {
            const proposalFile = proposalFileInput.files[0];
            if (proposalFile.type !== 'application/pdf' || proposalFile.size > 10 * 1024 * 1024) {
                e.preventDefault();
                proposalDropzone.classList.add('border-danger');
                proposalFileError.classList.remove('d-none');
                proposalDropzoneText.innerText = 'File proposal harus berformat PDF maksimal 10 MB.';
            }
        }

        if (memberCheckboxes.length > 0) {
            const selectedCount = memberCheckboxes.filter((checkbox) => checkbox.checked).length;
            if (selectedCount < 2 || selectedCount > 10) {
                e.preventDefault();
                alert('Anggota kelompok harus berjumlah 2 sampai 10 mahasiswa.');
            }
        }
    });
});
</script>

@if(($proposalStatus['can_reupload'] ?? false))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropzone = document.getElementById('proposalReuploadDropzone');
    const fileInput = document.getElementById('proposalReuploadFile');
    const dropzoneText = document.getElementById('proposalReuploadText');
    const reuploadForm = document.getElementById('proposalReuploadForm');
    if (!dropzone || !fileInput || !dropzoneText) return;

    const defaultText = 'Klik atau seret file PDF proposal di sini';

    const setFileOnInput = (file) => {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;
    };

    const validateAndShowFile = (file) => {
        if (!file) return false;
        if (file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024) {
            dropzone.classList.add('border-danger');
            dropzoneText.textContent = 'Hanya file PDF maksimal 10 MB yang diperbolehkan.';
            fileInput.value = '';
            return false;
        }
        dropzone.classList.remove('border-danger');
        dropzoneText.textContent = file.name;
        return true;
    };

    dropzone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            validateAndShowFile(fileInput.files[0]);
        }
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('bg-primary', 'text-white');
            dropzoneText.textContent = 'Lepaskan file di sini';
        });
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('bg-primary', 'text-white');
        dropzoneText.textContent = fileInput.files.length > 0
            ? fileInput.files[0].name
            : defaultText;
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('bg-primary', 'text-white');

        const file = e.dataTransfer.files?.[0];
        if (!file) {
            dropzoneText.textContent = defaultText;
            return;
        }

        if (validateAndShowFile(file)) {
            setFileOnInput(file);
        }
    });

    if (reuploadForm) {
        reuploadForm.addEventListener('submit', (e) => {
            if (fileInput.files.length === 0) {
                e.preventDefault();
                dropzone.classList.add('border-danger');
                dropzoneText.textContent = 'Pilih atau seret file proposal terlebih dahulu.';
                return;
            }
            const file = fileInput.files[0];
            if (file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024) {
                e.preventDefault();
                dropzone.classList.add('border-danger');
                dropzoneText.textContent = 'File proposal harus berformat PDF maksimal 10 MB.';
            }
        });
    }
});
</script>
@endif
@endsection

