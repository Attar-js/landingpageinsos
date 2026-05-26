@php
    $rubric = $rubric ?? null;
    $mode = $mode ?? 'readonly';
    $canEditDeskripsi = $mode === 'mahasiswa';
    $canEditSkor = $mode === 'tim_penciri';
    $isReadonly = $mode === 'readonly';

    $formAction = match ($mode) {
        'mahasiswa' => route('kkn.groups.cpmk-rubrik', $group->id),
        'tim_penciri' => route('tim-penciri.groups.cpmk-rubrik-skor', $group->id),
        default => '#',
    };

    $rubrikTotal = $rubric
        ? round(
            ((float) ($rubric->skor_p5 ?? 0) * 0.35)
            + ((float) ($rubric->skor_c3 ?? 0) * 0.30)
            + ((float) ($rubric->skor_a2 ?? 0) * 0.35),
            2
        )
        : 0;

    $deskripsiHint = match ($mode) {
        'mahasiswa' => 'Ketua kelompok mengisi kolom deskripsi kegiatan. Kolom skor diisi oleh Tim MK Penciri.',
        'tim_penciri' => 'Isi kolom skor berdasarkan deskripsi kegiatan yang telah diisi mahasiswa.',
        default => 'Rubrik Penilaian CPMK MK Inovasi Sosial.',
    };

    $submitLabel = match ($mode) {
        'mahasiswa' => 'Simpan Deskripsi Kegiatan',
        'tim_penciri' => 'Simpan Skor Rubrik CPMK',
        default => 'Simpan',
    };
@endphp

<div class="card shadow-sm border-0 mt-4 cpmk-rubric-card">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fa fa-table me-2"></i>
            Rubrik Penilaian CPMK MK Inovasi Sosial
        </h5>
        <small class="text-muted d-block mt-1">{{ $deskripsiHint }}</small>
    </div>
    <div class="card-body">
        @if($isReadonly)
            <div class="alert alert-secondary mb-3">Tampilan rubrik.</div>
        @endif

        @if($mode === 'tim_penciri' && empty($rubric?->deskripsi_p5) && empty($rubric?->deskripsi_c3) && empty($rubric?->deskripsi_a2))
            <div class="alert alert-warning mb-3">
                Mahasiswa belum mengisi deskripsi kegiatan. Skor dapat disimpan setelah deskripsi tersedia.
            </div>
        @endif

        <form
            id="cpmkRubricForm"
            action="{{ $formAction }}"
            method="POST"
            @if($isReadonly) class="pe-none" @endif
        >
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-3">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45%;">CPMK MK Inovasi Sosial</th>
                            <th style="width: 35%;">Deskripsi Kegiatan</th>
                            <th class="text-center" style="width: 10%;">Bobot</th>
                            <th class="text-center" style="width: 10%;">Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mahasiswa mampu menentukan program sebagai solusi pemecahan masalah di lokasi pengabdian masyarakat (P5)</td>
                            <td>
                                <textarea
                                    name="deskripsi_p5"
                                    class="form-control rubrik-deskripsi"
                                    rows="3"
                                    placeholder="Deskripsi kegiatan untuk P5..."
                                    @if(!$canEditDeskripsi) readonly @endif
                                    @if($canEditDeskripsi) required @endif
                                >{{ old('deskripsi_p5', $rubric->deskripsi_p5 ?? '') }}</textarea>
                            </td>
                            <td class="text-center fw-semibold">35%</td>
                            <td>
                                @if($canEditSkor)
                                    <input type="number" name="skor_p5" class="form-control text-center cpmk-skor-input" min="0" max="100" step="0.01" placeholder="0-100" value="{{ old('skor_p5', $rubric->skor_p5 ?? '') }}" required>
                                @else
                                    <input type="text" class="form-control text-center bg-light" value="{{ isset($rubric->skor_p5) ? number_format($rubric->skor_p5, 2) : '' }}" placeholder="Tim MK Penciri" readonly>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Mahasiswa mampu mengimplementasikan ilmu pengetahuan dan teknologi dalam kerja sama tim (C3)</td>
                            <td>
                                <textarea
                                    name="deskripsi_c3"
                                    class="form-control rubrik-deskripsi"
                                    rows="3"
                                    placeholder="Deskripsi kegiatan untuk C3..."
                                    @if(!$canEditDeskripsi) readonly @endif
                                    @if($canEditDeskripsi) required @endif
                                >{{ old('deskripsi_c3', $rubric->deskripsi_c3 ?? '') }}</textarea>
                            </td>
                            <td class="text-center fw-semibold">30%</td>
                            <td>
                                @if($canEditSkor)
                                    <input type="number" name="skor_c3" class="form-control text-center cpmk-skor-input" min="0" max="100" step="0.01" placeholder="0-100" value="{{ old('skor_c3', $rubric->skor_c3 ?? '') }}" required>
                                @else
                                    <input type="text" class="form-control text-center bg-light" value="{{ isset($rubric->skor_c3) ? number_format($rubric->skor_c3, 2) : '' }}" placeholder="Tim MK Penciri" readonly>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Mahasiswa mampu melaporkan hasil kegiatan pengabdian kepada masyarakat (A2)</td>
                            <td>
                                <textarea
                                    name="deskripsi_a2"
                                    class="form-control rubrik-deskripsi"
                                    rows="3"
                                    placeholder="Deskripsi kegiatan untuk A2..."
                                    @if(!$canEditDeskripsi) readonly @endif
                                    @if($canEditDeskripsi) required @endif
                                >{{ old('deskripsi_a2', $rubric->deskripsi_a2 ?? '') }}</textarea>
                            </td>
                            <td class="text-center fw-semibold">35%</td>
                            <td>
                                @if($canEditSkor)
                                    <input type="number" name="skor_a2" class="form-control text-center cpmk-skor-input" min="0" max="100" step="0.01" placeholder="0-100" value="{{ old('skor_a2', $rubric->skor_a2 ?? '') }}" required>
                                @else
                                    <input type="text" class="form-control text-center bg-light" value="{{ isset($rubric->skor_a2) ? number_format($rubric->skor_a2, 2) : '' }}" placeholder="Tim MK Penciri" readonly>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="fw-bold">Total Nilai Rubrik</td>
                            <td class="text-center fw-bold">100%</td>
                            <td class="text-center fw-bold" id="cpmkRubrikTotal">{{ number_format($rubrikTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($canEditSkor)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan/Komentar Tim MK Penciri:</label>
                    <textarea name="catatan" class="form-control" rows="4" placeholder="Catatan penilaian dari Tim MK Penciri...">{{ old('catatan', $rubric->catatan ?? '') }}</textarea>
                </div>
            @elseif(!empty($rubric?->catatan))
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Tim MK Penciri:</label>
                    <div class="form-control bg-light" style="min-height: 80px;">{{ $rubric->catatan }}</div>
                </div>
            @endif

            @if($canEditDeskripsi || $canEditSkor)
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4 py-2 fw-bold">
                        <i class="fa fa-save me-1"></i>{{ $submitLabel }}
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

@if($canEditSkor)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('#cpmkRubricForm .cpmk-skor-input');
    const totalEl = document.getElementById('cpmkRubrikTotal');
    if (!inputs.length || !totalEl) return;

    const recalc = () => {
        const p5 = parseFloat(document.querySelector('#cpmkRubricForm [name="skor_p5"]')?.value) || 0;
        const c3 = parseFloat(document.querySelector('#cpmkRubricForm [name="skor_c3"]')?.value) || 0;
        const a2 = parseFloat(document.querySelector('#cpmkRubricForm [name="skor_a2"]')?.value) || 0;
        totalEl.textContent = ((p5 * 0.35) + (c3 * 0.30) + (a2 * 0.35)).toFixed(2);
    };

    inputs.forEach((input) => input.addEventListener('input', recalc));
    recalc();
});
</script>
@endif
