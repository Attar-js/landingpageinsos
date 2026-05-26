@extends('layouts.app')

@section('title', 'Penilaian Mahasiswa - ' . $mahasiswaData['nama'])

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Penilaian Mahasiswa</h4>
                <a href="{{ route('dosen.mahasiswa-bimbingan') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Mahasiswa Bimbingan
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informasi Mahasiswa -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Informasi Mahasiswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto">
                            <span class="avatar-title bg-primary rounded-circle fs-1">
                                {{ strtoupper(substr($mahasiswaData['nama'], 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $mahasiswaData['nama'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>NIM:</strong></td>
                            <td>{{ $mahasiswaData['nim'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Program Studi:</strong></td>
                            <td>{{ $mahasiswaData['program_studi'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Judul Kegiatan:</strong></td>
                            <td>{{ $mahasiswaData['judul_kegiatan'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nama Mitra:</strong></td>
                            <td>{{ $mahasiswaData['nama_mitra'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Lokasi Mitra:</strong></td>
                            <td>{{ $mahasiswaData['lokasi_mitra'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Peran:</strong></td>
                            <td>{{ $mahasiswaData['peran'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status Penilaian:</strong></td>
                            <td>
                                @if($penilaian)
                                    <span class="badge bg-success">Sudah Dinilai</span>
                                @else
                                    <span class="badge bg-warning">Belum Dinilai</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Penilaian -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star"></i> Form Penilaian Komponen KKN
                    </h5>
                </div>
                <div class="card-body">
                    <form id="penilaianForm" method="POST" action="{{ route('dosen.store-penilaian') }}">
                        @csrf
                        <input type="hidden" name="mahasiswa_nim" value="{{ $mahasiswaData['nim'] }}">
                        <input type="hidden" name="dosen_id" value="{{ auth()->user()->id }}">

                        <div class="row">
                            <!-- Proposal Kegiatan (20%) -->
                            <div class="col-md-6 mb-3">
                                <label for="proposal_kegiatan" class="form-label">
                                    <strong>Proposal Kegiatan (20%)</strong>
                                </label>
                                <input type="number" 
                                       class="form-control @error('proposal_kegiatan') is-invalid @enderror" 
                                       id="proposal_kegiatan" 
                                       name="proposal_kegiatan" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       value="{{ $penilaian->proposal_kegiatan ?? old('proposal_kegiatan') }}" 
                                       required>
                                @error('proposal_kegiatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kualitas proposal kegiatan yang diajukan mahasiswa</small>
                            </div>

                            <!-- Peer Review (15%) -->
                            <div class="col-md-6 mb-3">
                                <label for="peer_review" class="form-label">
                                    <strong>Peer Review (15%)</strong>
                                </label>
                                <input type="number" 
                                       class="form-control @error('peer_review') is-invalid @enderror" 
                                       id="peer_review" 
                                       name="peer_review" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       value="{{ $penilaian->peer_review ?? old('peer_review') }}" 
                                       required>
                                @error('peer_review')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kemampuan memberikan dan menerima feedback dari rekan</small>
                            </div>

                            <!-- Laporan Akhir (20%) -->
                            <div class="col-md-6 mb-3">
                                <label for="laporan_akhir" class="form-label">
                                    <strong>Laporan Akhir (20%)</strong>
                                </label>
                                <input type="number" 
                                       class="form-control @error('laporan_akhir') is-invalid @enderror" 
                                       id="laporan_akhir" 
                                       name="laporan_akhir" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       value="{{ $penilaian->laporan_akhir ?? old('laporan_akhir') }}" 
                                       required>
                                @error('laporan_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kualitas laporan akhir kegiatan yang disusun</small>
                            </div>

                            <!-- Presentasi Akhir (15%) -->
                            <div class="col-md-6 mb-3">
                                <label for="presentasi_akhir" class="form-label">
                                    <strong>Presentasi Akhir (15%)</strong>
                                </label>
                                <input type="number" 
                                       class="form-control @error('presentasi_akhir') is-invalid @enderror" 
                                       id="presentasi_akhir" 
                                       name="presentasi_akhir" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       value="{{ $penilaian->presentasi_akhir ?? old('presentasi_akhir') }}" 
                                       required>
                                @error('presentasi_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kemampuan presentasi dan komunikasi hasil kegiatan</small>
                            </div>

                            <!-- Preview Nilai Akhir -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <strong>Nilai Akhir (Preview)</strong>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="nilai_akhir_preview" 
                                           readonly 
                                           value="{{ $penilaian->nilai_akhir ?? '0.00' }}">
                                    <span class="input-group-text">
                                        <span id="grade_preview" class="badge bg-secondary">-</span>
                                    </span>
                                </div>
                                <small class="text-muted">Nilai akhir akan dihitung otomatis berdasarkan bobot komponen</small>
                            </div>

                            <!-- Detail Kontribusi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <strong>Detail Kontribusi</strong>
                                </label>
                                <div class="card border-info">
                                    <div class="card-body p-2">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <small class="text-muted d-block">Proposal</small>
                                                <span id="proposal_contribution" class="badge bg-info">0.00</span>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-muted d-block">Peer Review</small>
                                                <span id="peer_contribution" class="badge bg-info">0.00</span>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-muted d-block">Laporan</small>
                                                <span id="laporan_contribution" class="badge bg-info">0.00</span>
                                            </div>
                                            <div class="col-3">
                                                <small class="text-muted d-block">Presentasi</small>
                                                <span id="presentasi_contribution" class="badge bg-info">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Kontribusi setiap komponen terhadap nilai akhir</small>
                            </div>

                            <!-- Grade Description -->
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            <strong>Grade: </strong><span id="grade_description">-</span>
                                            <br>
                                            <small class="text-muted">
                                                <strong>Bobot Total:</strong> 70% (30% dapat ditambahkan sesuai kebutuhan)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-3">
                            <label for="catatan" class="form-label">
                                <strong>Catatan Penilaian (Opsional)</strong>
                            </label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" 
                                      name="catatan" 
                                      rows="3" 
                                      placeholder="Berikan catatan atau feedback untuk mahasiswa...">{{ $penilaian->catatan ?? old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="history.back()">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Simpan Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Function to calculate final grade
    function calculateFinalGrade() {
        // Get input values with validation
        const proposalKegiatan = parseFloat($('#proposal_kegiatan').val()) || 0;
        const peerReview = parseFloat($('#peer_review').val()) || 0;
        const laporanAkhir = parseFloat($('#laporan_akhir').val()) || 0;
        const presentasiAkhir = parseFloat($('#presentasi_akhir').val()) || 0;
        
        // Validate input ranges
        const inputs = [
            { value: proposalKegiatan, name: 'Proposal Kegiatan', element: '#proposal_kegiatan' },
            { value: peerReview, name: 'Peer Review', element: '#peer_review' },
            { value: laporanAkhir, name: 'Laporan Akhir', element: '#laporan_akhir' },
            { value: presentasiAkhir, name: 'Presentasi Akhir', element: '#presentasi_akhir' }
        ];
        
        let isValid = true;
        inputs.forEach(input => {
            if (input.value < 0 || input.value > 100) {
                $(input.element).addClass('is-invalid');
                isValid = false;
            } else {
                $(input.element).removeClass('is-invalid');
            }
        });
        
        // Calculate weighted average
        const bobotProposal = 0.20;    // 20%
        const bobotPeerReview = 0.15;  // 15%
        const bobotLaporan = 0.20;     // 20%
        const bobotPresentasi = 0.15;  // 15%
        const totalBobot = bobotProposal + bobotPeerReview + bobotLaporan + bobotPresentasi; // 70%
        
        const nilaiAkhir = (proposalKegiatan * bobotProposal) + 
                          (peerReview * bobotPeerReview) + 
                          (laporanAkhir * bobotLaporan) + 
                          (presentasiAkhir * bobotPresentasi);
        
        // Update preview with proper formatting
        $('#nilai_akhir_preview').val(nilaiAkhir.toFixed(2));
        
        // Calculate individual contributions for display
        const proposalContribution = (proposalKegiatan * bobotProposal).toFixed(2);
        const peerContribution = (peerReview * bobotPeerReview).toFixed(2);
        const laporanContribution = (laporanAkhir * bobotLaporan).toFixed(2);
        const presentasiContribution = (presentasiAkhir * bobotPresentasi).toFixed(2);
        
        // Update contribution display if elements exist
        if ($('#proposal_contribution').length) {
            $('#proposal_contribution').text(proposalContribution);
        }
        if ($('#peer_contribution').length) {
            $('#peer_contribution').text(peerContribution);
        }
        if ($('#laporan_contribution').length) {
            $('#laporan_contribution').text(laporanContribution);
        }
        if ($('#presentasi_contribution').length) {
            $('#presentasi_contribution').text(presentasiContribution);
        }
        
        // Determine grade with improved logic
        let grade = '-';
        let badgeClass = 'bg-secondary';
        let gradeDescription = '';
        
        if (nilaiAkhir >= 85) {
            grade = 'A';
            badgeClass = 'bg-success';
            gradeDescription = 'Sangat Baik';
        } else if (nilaiAkhir >= 80) {
            grade = 'A-';
            badgeClass = 'bg-success';
            gradeDescription = 'Sangat Baik';
        } else if (nilaiAkhir >= 75) {
            grade = 'B+';
            badgeClass = 'bg-primary';
            gradeDescription = 'Baik';
        } else if (nilaiAkhir >= 70) {
            grade = 'B';
            badgeClass = 'bg-primary';
            gradeDescription = 'Baik';
        } else if (nilaiAkhir >= 65) {
            grade = 'B-';
            badgeClass = 'bg-warning';
            gradeDescription = 'Cukup';
        } else if (nilaiAkhir >= 60) {
            grade = 'C+';
            badgeClass = 'bg-warning';
            gradeDescription = 'Cukup';
        } else if (nilaiAkhir >= 55) {
            grade = 'C';
            badgeClass = 'bg-warning';
            gradeDescription = 'Cukup';
        } else if (nilaiAkhir >= 50) {
            grade = 'C-';
            badgeClass = 'bg-danger';
            gradeDescription = 'Kurang';
        } else if (nilaiAkhir >= 45) {
            grade = 'D';
            badgeClass = 'bg-danger';
            gradeDescription = 'Kurang';
        } else {
            grade = 'E';
            badgeClass = 'bg-danger';
            gradeDescription = 'Sangat Kurang';
        }
        
        // Update grade display
        $('#grade_preview').text(grade).removeClass().addClass('badge ' + badgeClass);
        
        // Update grade description if element exists
        if ($('#grade_description').length) {
            $('#grade_description').text(gradeDescription).removeClass().addClass('text-' + badgeClass.replace('bg-', ''));
        }
        
        // Show/hide submit button based on validity
        if (isValid && nilaiAkhir > 0) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
        
        // Add visual feedback for total calculation
        if (nilaiAkhir > 0) {
            $('#nilai_akhir_preview').addClass('border-success');
        } else {
            $('#nilai_akhir_preview').removeClass('border-success');
        }
    }
    
    // Calculate grade on input change with debouncing
    let calculationTimeout;
    $('#proposal_kegiatan, #peer_review, #laporan_akhir, #presentasi_akhir').on('input', function() {
        clearTimeout(calculationTimeout);
        calculationTimeout = setTimeout(calculateFinalGrade, 100);
    });
    
    // Add input validation on blur
    $('#proposal_kegiatan, #peer_review, #laporan_akhir, #presentasi_akhir').on('blur', function() {
        const value = parseFloat($(this).val());
        if (value < 0 || value > 100) {
            $(this).addClass('is-invalid');
            // Show tooltip or alert
            if (value < 0) {
                $(this).attr('title', 'Nilai tidak boleh kurang dari 0');
            } else if (value > 100) {
                $(this).attr('title', 'Nilai tidak boleh lebih dari 100');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).removeAttr('title');
        }
    });
    
    // Form submission with enhanced validation
    $('#penilaianForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate all inputs before submission
        const proposalKegiatan = parseFloat($('#proposal_kegiatan').val()) || 0;
        const peerReview = parseFloat($('#peer_review').val()) || 0;
        const laporanAkhir = parseFloat($('#laporan_akhir').val()) || 0;
        const presentasiAkhir = parseFloat($('#presentasi_akhir').val()) || 0;
        
        if (proposalKegiatan < 0 || proposalKegiatan > 100 ||
            peerReview < 0 || peerReview > 100 ||
            laporanAkhir < 0 || laporanAkhir > 100 ||
            presentasiAkhir < 0 || presentasiAkhir > 100) {
            
            Swal.fire({
                icon: 'error',
                title: 'Nilai Tidak Valid!',
                text: 'Semua nilai harus berada dalam rentang 0-100',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        // Show confirmation dialog with calculated values
        const nilaiAkhir = (proposalKegiatan * 0.20) + (peerReview * 0.15) + (laporanAkhir * 0.20) + (presentasiAkhir * 0.15);
        
        Swal.fire({
            title: 'Konfirmasi Penilaian',
            html: `
                <div class="text-start">
                    <p><strong>Nilai yang akan disimpan:</strong></p>
                    <ul>
                        <li>Proposal Kegiatan: ${proposalKegiatan} (${(proposalKegiatan * 0.20).toFixed(2)} poin)</li>
                        <li>Peer Review: ${peerReview} (${(peerReview * 0.15).toFixed(2)} poin)</li>
                        <li>Laporan Akhir: ${laporanAkhir} (${(laporanAkhir * 0.20).toFixed(2)} poin)</li>
                        <li>Presentasi Akhir: ${presentasiAkhir} (${(presentasiAkhir * 0.15).toFixed(2)} poin)</li>
                    </ul>
                    <p><strong>Nilai Akhir: ${nilaiAkhir.toFixed(2)}</strong></p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Nilai berhasil disimpan dan dashboard terupdate secara real-time!',
                            showConfirmButton: true,
                            confirmButtonText: 'Lihat Dashboard'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open dashboard in new tab
                                window.open('{{ \App\Helpers\DashboardHelper::getNilaiAkhirUrl() }}', '_blank');
                            }
                        });
                        
                        // Re-enable button
                        submitBtn.prop('disabled', false).html(originalText);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        let errorMessage = 'Terjadi kesalahan saat menyimpan nilai.';
                        
                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('\n');
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonText: 'OK'
                        });
                        
                        // Re-enable button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            } else {
                // Re-enable button if cancelled
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Initialize calculation on page load
    calculateFinalGrade();
    
    // Add keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+Enter to submit form
        if (e.ctrlKey && e.keyCode === 13) {
            $('#penilaianForm').submit();
        }
        // Escape to go back
        if (e.keyCode === 27) {
            history.back();
        }
    });
});
</script>
@endsection 
