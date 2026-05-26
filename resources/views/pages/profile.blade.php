@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
<style>
    .profile-edit-btn {
        min-height: 44px;
        min-width: 150px;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 0.98rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.24);
    }

    .profile-action-btn {
        min-height: 42px;
        min-width: 150px;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
<x-header/>
<div style="height: 140px;"></div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success" style="max-width: 700px; margin: 0 auto 16px;">
            {{ session('success') }}
        </div>
    @endif
    <div class="card shadow" style="max-width: 700px; margin: 0 auto 40px;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Profil Pengguna</h4>
                <button type="button" class="btn btn-primary profile-edit-btn" id="editProfileBtn">Edit Profil</button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                @csrf
                <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama</label>
                    <input type="text" class="form-control editable-field @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name ?? '') }}" readonly>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ ($user->role ?? '') === 'dosen' ? 'NIP' : 'NIM' }}</label>
                    <input type="text" class="form-control" value="{{ ($user->role ?? '') === 'dosen' ? ($user->nip ?? '-') : ($user->nim ?? '-') }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Program Studi</label>
                    <input type="text" class="form-control editable-field" name="program_studi" value="{{ old('program_studi', $programStudi ?? '') }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nomor Telepon</label>
                    <input type="text" class="form-control editable-field" name="phone_number" value="{{ old('phone_number', $user->phone_number ?? '') }}" readonly>
                </div>
                </div>

                <div class="d-flex gap-2 mt-4 d-none" id="profileActionButtons">
                    <button type="submit" class="btn btn-success profile-action-btn">Simpan Perubahan</button>
                    <button type="button" class="btn btn-outline-secondary profile-action-btn" id="cancelEditBtn">Batal</button>
                </div>
            </form>
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
});
</script>
@endsection
