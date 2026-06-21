@extends('layout.layout')

@php
    $header = 'false';
    $roleLabels = [
        'mahasiswa' => 'Mahasiswa',
        'dosen' => 'Dosen',
        'admin' => 'Admin',
        'tim_penciri' => 'Tim MK Penciri',
    ];
    $roleLabel = $roleLabels[$user->role ?? ''] ?? ucfirst($user->role ?? '-');
    $identifierLabel = ($user->role ?? '') === 'dosen' ? 'NIP' : 'NIM';
    $identifierValue = ($user->role ?? '') === 'dosen'
        ? ($user->nip ?? '-')
        : ($user->nim ?? '-');
    $initials = collect(preg_split('/\s+/', trim($user->name ?? 'U')))
        ->filter()
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

@section('content')
<style>
    .profile-page {
        padding-bottom: 48px;
    }

    .profile-page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 24px;
    }

    .profile-summary-card,
    .profile-section-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .profile-summary-card {
        padding: 28px;
        margin-bottom: 24px;
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #dbeafe;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .profile-role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: capitalize;
    }

    .profile-meta-label {
        font-size: 0.78rem;
        color: #6b7280;
        margin-bottom: 2px;
    }

    .profile-meta-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        word-break: break-word;
    }

    .profile-section-card {
        padding: 24px;
        height: 100%;
    }

    .profile-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 20px;
    }

    .profile-info-label {
        font-size: 0.82rem;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .profile-info-value {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        color: #111827;
        min-height: 44px;
        display: flex;
        align-items: center;
    }

    .profile-info-value input.form-control {
        background: transparent;
        border: 0;
        padding: 0;
        box-shadow: none;
        min-height: auto;
    }

    .profile-info-value input.form-control:focus {
        box-shadow: none;
    }

    .profile-edit-btn,
    .profile-action-btn,
    .profile-password-btn {
        min-height: 42px;
        border-radius: 10px;
        font-size: 0.92rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 18px;
    }

    .profile-edit-btn {
        min-width: 120px;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.18);
    }

    .profile-password-btn {
        min-width: 160px;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.18);
    }

    .password-field-wrap {
        position: relative;
    }

    .password-field-wrap .form-control {
        padding-right: 44px;
        border-radius: 10px;
        min-height: 44px;
    }

    .password-toggle-btn {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #6b7280;
        padding: 0;
        line-height: 1;
        cursor: pointer;
    }

    .password-toggle-btn:hover {
        color: #2563eb;
    }
</style>

<x-header/>
<div style="height: 140px;"></div>

<div class="container profile-page">
    <h1 class="profile-page-title">Profil Saya</h1>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if(session('password_success'))
        <div class="alert alert-success mb-3">{{ session('password_success') }}</div>
    @endif

    <div class="profile-summary-card">
        <div class="row g-4 align-items-center">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="profile-avatar">{{ $initials ?: 'U' }}</div>
                    <div>
                        <h2 class="h5 fw-bold mb-2">{{ $user->name ?? '-' }}</h2>
                        <span class="profile-role-badge">{{ $roleLabel }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="profile-meta-label">{{ $identifierLabel }}</div>
                        <div class="profile-meta-value">{{ $identifierValue }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="profile-meta-label">Email</div>
                        <div class="profile-meta-value">{{ $user->email ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="profile-meta-label">Nomor Telepon</div>
                        <div class="profile-meta-value">{{ $user->phone_number ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="profile-section-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="profile-section-title mb-0">Informasi Akun</h3>
                    <button type="button" class="btn btn-primary profile-edit-btn" id="editProfileBtn">Edit Profil</button>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="profile-info-label">Nama Lengkap</div>
                            <div class="profile-info-value">
                                <input type="text" class="form-control editable-field @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name ?? '') }}" readonly>
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-label">Hak Akses</div>
                            <div class="profile-info-value">{{ $roleLabel }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-label">Email</div>
                            <div class="profile-info-value">{{ $user->email ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-label">{{ $identifierLabel }}</div>
                            <div class="profile-info-value">{{ $identifierValue }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-label">Program Studi</div>
                            <div class="profile-info-value">
                                <input type="text" class="form-control editable-field @error('program_studi') is-invalid @enderror" name="program_studi" value="{{ old('program_studi', $programStudi ?? '') }}" readonly>
                            </div>
                            @error('program_studi')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="profile-info-label">Nomor Telepon</div>
                            <div class="profile-info-value">
                                <input type="text" class="form-control editable-field @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number', $user->phone_number ?? '') }}" readonly>
                            </div>
                            @error('phone_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 d-none" id="profileActionButtons">
                        <button type="submit" class="btn btn-success profile-action-btn">Simpan Perubahan</button>
                        <button type="button" class="btn btn-outline-secondary profile-action-btn" id="cancelEditBtn">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="profile-section-card" id="keamananAkun">
                <h3 class="profile-section-title">Keamanan Akun</h3>

                <form action="{{ route('profile.password.update') }}" method="POST" id="passwordForm">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                        <div class="password-field-wrap">
                            <input type="password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password"
                                   name="current_password"
                                   placeholder="Masukkan password lama"
                                   autocomplete="current-password">
                            <button type="button" class="password-toggle-btn" data-target="current_password" aria-label="Tampilkan password">
                                <i class="feather-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Password Baru</label>
                            <div class="password-field-wrap">
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Minimal 8 karakter"
                                       autocomplete="new-password">
                                <button type="button" class="password-toggle-btn" data-target="password" aria-label="Tampilkan password">
                                    <i class="feather-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
                            <div class="password-field-wrap">
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Ulangi password baru"
                                       autocomplete="new-password">
                                <button type="button" class="password-toggle-btn" data-target="password_confirmation" aria-label="Tampilkan password">
                                    <i class="feather-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary profile-password-btn">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editBtn = document.getElementById('editProfileBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const actionButtons = document.getElementById('profileActionButtons');
    const editableFields = document.querySelectorAll('.editable-field');
    const initialValues = new Map();

    editableFields.forEach((field) => {
        initialValues.set(field, field.value);
    });

    const setEditMode = (enabled) => {
        editableFields.forEach((field) => {
            field.readOnly = !enabled;
        });
        actionButtons.classList.toggle('d-none', !enabled);
        editBtn.classList.toggle('d-none', enabled);
    };

    editBtn.addEventListener('click', function () {
        setEditMode(true);
    });

    cancelBtn.addEventListener('click', function () {
        editableFields.forEach((field) => {
            field.value = initialValues.get(field) ?? '';
        });
        setEditMode(false);
    });

    document.querySelectorAll('.password-toggle-btn').forEach((button) => {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.target);
            if (!input) {
                return;
            }

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';

            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.toggle('feather-eye', !isHidden);
                icon.classList.toggle('feather-eye-off', isHidden);
            }
        });
    });

    @if($errors->has('current_password') || $errors->has('password'))
        document.getElementById('keamananAkun')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    @endif
});
</script>
@endsection
