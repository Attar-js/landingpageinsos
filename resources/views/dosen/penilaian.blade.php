@extends('layout.layout')

@php
    $header = 'false';
@endphp

@section('content')
    <x-header/>
    <div style="height: 140px;"></div>
    
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Main Title with Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('dosen.mahasiswa-bimbingan') }}" class="btn-back-header">
                        <i class="fas fa-arrow-left"></i> Kembali ke Mahasiswa Bimbingan
                    </a>
                </div>
                <div>
                    <h2 class="fw-bold text-dark mb-0">Detail Mahasiswa</h2>
                </div>
                <div style="width: 200px;"></div> <!-- Spacer for centering -->
            </div>

            <!-- Student Identity Section -->
            <div class="student-identity-box mb-4">
                <h4 class="identity-title">Identitas Mahasiswa</h4>
                <div class="identity-content">
                    <div class="identity-grid">
                        <div class="identity-item">
                            <span class="identity-label">Nama:</span>
                            <span class="identity-value">{{ $mahasiswaData['nama'] ?? 'Muhammad Fachrurrozi Attar' }}</span>
                        </div>
                        <div class="identity-item">
                            <span class="identity-label">NIM:</span>
                            <span class="identity-value">{{ $mahasiswaData['nim'] ?? '10221051' }}</span>
                        </div>
                        <div class="identity-item">
                            <span class="identity-label">Program Studi:</span>
                            <span class="identity-value">{{ $mahasiswaData['program_studi'] ?? 'Sistem Informasi' }}</span>
                        </div>
                        <div class="identity-item">
                            <span class="identity-label">Judul Kegiatan:</span>
                            <span class="identity-value">{{ $mahasiswaData['judul_kegiatan'] ?? 'Pengembangan Pasar Sepinggan' }}</span>
                        </div>
                        <div class="identity-item">
                            <span class="identity-label">Mitra:</span>
                            <span class="identity-value">{{ $mahasiswaData['nama_mitra'] ?? 'sdnjkad' }}</span>
                        </div>
                        <div class="identity-item">
                            <span class="identity-label">Lokasi Mitra:</span>
                            <span class="identity-value">{{ $mahasiswaData['lokasi_mitra'] ?? 'snd ansd mna' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Form -->
            <div class="assessment-card">
                <h4 class="assessment-title">Penilaian Mahasiswa oleh Dosen Pembimbing</h4>
                
                <form id="dosenPenilaianForm" action="{{ route('dosen.store-penilaian', $mahasiswaData['nim'] ?? '10221051') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mahasiswa_nim" value="{{ $mahasiswaData['nim'] ?? '10221051' }}">
                    <input type="hidden" name="dosen_id" value="{{ Auth::id() }}">
                    
                    @php
                        $peerValue = old('peer_review', $autoPeerReview ?? ($penilaian->peer_review ?? ''));
                    @endphp
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle rubric-table">
                            <thead class="table-light">
                                <tr>
                                    <th>CPMK MK Inovasi Sosial</th>
                                    <th>Deskripsi Kegiatan</th>
                                    <th class="text-center">Bobot</th>
                                    <th class="text-center">Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mahasiswa mampu menentukan program sebagai solusi pemecahan masalah di lokasi pengabdian masyarakat (P5)</td>
                                    <td><textarea name="deskripsi_p5" class="form-control" rows="2" placeholder="Deskripsi kegiatan...">{{ old('deskripsi_p5') }}</textarea></td>
                                    <td class="text-center fw-semibold">35%</td>
                                    <td><input type="number" name="cpmk_p5" class="form-control criteria-input" value="{{ old('cpmk_p5', $penilaian->proposal_kegiatan ?? '') }}" min="0" max="100" step="0.01" required></td>
                                </tr>
                                <tr>
                                    <td>Mahasiswa mampu mengimplementasikan ilmu pengetahuan dan teknologi dalam kerjasama tim (C3)</td>
                                    <td><textarea name="deskripsi_c3" class="form-control" rows="2" placeholder="Deskripsi kegiatan...">{{ old('deskripsi_c3') }}</textarea></td>
                                    <td class="text-center fw-semibold">30%</td>
                                    <td><input type="number" name="cpmk_c3" class="form-control criteria-input" value="{{ old('cpmk_c3', $penilaian->presentasi_akhir ?? '') }}" min="0" max="100" step="0.01" required></td>
                                </tr>
                                <tr>
                                    <td>Mahasiswa mampu melaporkan hasil kegiatan pengabdian kepada masyarakat (A2)</td>
                                    <td><textarea name="deskripsi_a2" class="form-control" rows="2" placeholder="Deskripsi kegiatan...">{{ old('deskripsi_a2') }}</textarea></td>
                                    <td class="text-center fw-semibold">35%</td>
                                    <td><input type="number" name="cpmk_a2" class="form-control criteria-input" value="{{ old('cpmk_a2', $penilaian->laporan_akhir ?? '') }}" min="0" max="100" step="0.01" required></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="fw-bold">Total Nilai Rubrik Dosen</td>
                                    <td class="text-center fw-bold">100%</td>
                                    <td class="text-center fw-bold" id="rubrik-total-score">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="cpmk-section">
                        <h5 class="cpmk-title">Peer Review Tim (Otomatis)</h5>
                        <p class="cpmk-weight">Bobot terhadap nilai akhir: 15%</p>
                        <div class="criteria-list">
                            <div class="criteria-item">
                                <span class="criteria-text">Nilai Peer Review</span>
                                <input type="number" name="peer_review" class="criteria-input" value="{{ $peerValue }}" min="0" max="100" placeholder="Otomatis dari peer review" readonly>
                            </div>
                        </div>
                        <small class="text-muted">Nilai peer review diisi otomatis dari rata-rata penilaian anggota kelompok.</small>
                        <div class="mt-2">
                            <span id="peerReviewStatusBadge" class="badge {{ ($peerReviewComplete ?? false) ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ($peerReviewComplete ?? false) ? 'Peer review lengkap' : 'Peer review belum lengkap' }}
                            </span>
                        </div>
                    </div>

                    <!-- Total Assessment -->
                    <div class="total-assessment">
                        <span class="total-label">Total Penilaian</span>
                        <span class="total-score" id="total-score">0.0</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn-submit" id="submitPenilaianBtn" {{ ($peerReviewComplete ?? false) ? '' : 'disabled' }}>
                            Submit →
                        </button>
                        <button type="reset" class="btn-reset">
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
/* Student Identity Box - Larger Font Sizes */
.student-identity-box {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 8px;
    padding: 20px;
    color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 100%;
}

.identity-title {
    font-size: 1.6rem;
    font-weight: bold;
    margin-bottom: 15px;
    color: white;
}

.identity-content {
    color: white;
}

.identity-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

.identity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.identity-item:last-child {
    border-bottom: none;
}

.identity-label {
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    min-width: 140px;
}

.identity-value {
    font-weight: 600;
    color: white;
    font-size: 1.1rem;
    text-align: right;
    flex: 1;
    margin-left: 15px;
}

/* Assessment Card - Larger Font Sizes */
.assessment-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.assessment-title {
    font-size: 1.6rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
    text-align: left;
}

/* CPMK Sections - Larger Font Sizes */
.cpmk-section {
    margin-bottom: 25px;
}

.cpmk-title {
    font-size: 1.4rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 4px;
}

.cpmk-weight {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 15px;
}

.cpmk-divider {
    border: none;
    height: 1px;
    background-color: #e9ecef;
    margin: 25px 0;
}

/* Criteria List - Larger Font Sizes */
.criteria-list {
    margin-left: 15px;
}

.criteria-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px 0;
}

.criteria-text {
    flex: 1;
    color: #333;
    font-size: 1.1rem;
    line-height: 1.5;
    margin-right: 15px;
}

.criteria-input {
    width: 90px;
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-align: center;
    font-size: 1.1rem;
    background: white;
}

.criteria-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

/* Total Assessment - Larger Font Sizes */
.total-assessment {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 18px 30px;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 25px 0;
    font-weight: bold;
}

.total-label {
    font-size: 1.4rem;
}

.total-score {
    font-size: 1.8rem;
    font-weight: bold;
}

/* Action Buttons - Larger Font Sizes */
.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 15px;
}

.btn-submit {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.btn-reset {
    background: #f8f9fa;
    color: #333;
    border: 1px solid #ddd;
    padding: 14px 30px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.btn-reset:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.btn-back {
    background: #6c757d;
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.1rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-back:hover {
    background: #5a6268;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    text-decoration: none;
}

.btn-back-header {
    background: #6c757d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-back-header:hover {
    background: #5a6268;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    text-decoration: none;
}

/* Responsive Design - Larger Font Sizes */
@media (max-width: 768px) {
    .identity-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    
    .identity-label {
        min-width: auto;
        font-size: 1rem;
    }
    
    .identity-value {
        text-align: left;
        margin-left: 0;
        font-size: 1rem;
    }
    
    .criteria-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .criteria-text {
        font-size: 1rem;
    }
    
    .criteria-input {
        width: 100%;
        max-width: 130px;
        font-size: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-submit, .btn-reset, .btn-back {
        width: 100%;
        font-size: 1rem;
    }
    
    .assessment-title {
        font-size: 1.4rem;
    }
    
    .cpmk-title {
        font-size: 1.2rem;
    }
    
    .total-label {
        font-size: 1.2rem;
    }
    
    .total-score {
        font-size: 1.5rem;
    }
}

/* Container - Improved Proportions */
.container {
    max-width: 800px;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript loaded successfully');
    
    // Function to calculate total score
    function calculateTotalScore() {
        console.log('Calculating total score...');
        let cpmkP5 = parseFloat(document.querySelector('input[name="cpmk_p5"]').value) || 0;
        let cpmkC3 = parseFloat(document.querySelector('input[name="cpmk_c3"]').value) || 0;
        let cpmkA2 = parseFloat(document.querySelector('input[name="cpmk_a2"]').value) || 0;
        let peerReview = parseFloat(document.querySelector('input[name="peer_review"]').value) || 0;

        let rubrikTotal = (cpmkP5 * 0.35) + (cpmkC3 * 0.30) + (cpmkA2 * 0.35);
        let total = (rubrikTotal * 0.85) + (peerReview * 0.15);

        const rubrikTotalElement = document.getElementById('rubrik-total-score');
        if (rubrikTotalElement) {
            rubrikTotalElement.textContent = rubrikTotal.toFixed(2);
        }
        
        // Update total score
        let totalElement = document.getElementById('total-score');
        if (totalElement) {
            totalElement.textContent = total.toFixed(2);
            console.log(`Total Score: ${total.toFixed(2)}`);
        } else {
            console.error('Total score element not found!');
        }
    }
    
    // Add event listeners to all input fields
    document.querySelectorAll('.criteria-input').forEach(function(input) {
        console.log('Adding event listener to:', input.name);
        
        // Calculate on input change
        input.addEventListener('input', function() {
            console.log('Input changed:', this.name, 'Value:', this.value);
            let value = parseInt(this.value);
            if (value < 0) this.value = 0;
            if (value > 100) this.value = 100;
            calculateTotalScore();
        });
        
        // Add input validation on blur
        input.addEventListener('blur', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 0) {
                this.value = 0;
            } else if (value > 100) {
                this.value = 100;
            }
            calculateTotalScore();
        });
    });
    
    // Initial calculation
    console.log('Performing initial calculation...');
    calculateTotalScore();

    const peerReviewInput = document.querySelector('input[name="peer_review"]');
    const peerReviewStatusBadge = document.getElementById('peerReviewStatusBadge');
    const submitPenilaianBtn = document.getElementById('submitPenilaianBtn');
    const peerScoreUrl = "{{ route('dosen.penilaian.peer-review-score', $mahasiswaData['nim'] ?? '10221051') }}";

    const refreshPeerReviewScore = async () => {
        try {
            const response = await fetch(peerScoreUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            if (!response.ok) return;

            const data = await response.json();
            const score = data.peer_review;
            const complete = !!data.peer_review_complete;

            if (peerReviewInput && score !== null && score !== undefined) {
                peerReviewInput.value = Number(score).toFixed(2);
                calculateTotalScore();
            }

            if (peerReviewStatusBadge) {
                peerReviewStatusBadge.className = complete ? 'badge bg-success' : 'badge bg-warning text-dark';
                peerReviewStatusBadge.textContent = complete ? 'Peer review lengkap' : 'Peer review belum lengkap';
            }

            if (submitPenilaianBtn) {
                submitPenilaianBtn.disabled = !complete;
                submitPenilaianBtn.title = complete ? '' : 'Tunggu sampai seluruh anggota saling menilai.';
            }
        } catch (error) {
            console.warn('Gagal refresh peer review score:', error);
        }
    };

    refreshPeerReviewScore();
    setInterval(refreshPeerReviewScore, 10000);
    
    // Debug: Log all input fields
    console.log('Found input fields:');
    document.querySelectorAll('.criteria-input').forEach(function(input) {
        console.log('-', input.name, ':', input.value);
    });
});
</script> 
