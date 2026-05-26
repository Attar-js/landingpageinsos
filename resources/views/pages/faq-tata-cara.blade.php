@extends('layout.layout')

@php
    $footer = 'true';
@endphp

@section('title', 'FAQ & Tata Cara Mata Kuliah Inovasi Sosial')

@section('content')
<div class="container py-4" style="padding-top: 50px !important;">
    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="text-center fw-bold text-dark mb-0" style="font-size: 2rem;">FAQ & Tata Cara Mata Kuliah Inovasi Sosial</h1>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="row mb-4 justify-content-center">
        <div class="col-12 col-md-11 col-lg-9">
            <div id="faq-section" class="faq-section position-relative mx-auto" style="background: linear-gradient(135deg, #007bff, #0056b3); border-radius: 24px; padding: 32px 24px 36px 24px; min-height: unset; box-shadow: 0 4px 24px rgba(0,0,0,0.07); max-width: 900px;">
                <!-- Decorative X Patterns -->
                <div class="position-absolute" style="top: 16px; left: 16px; font-size: 1.1rem;">
                    <div class="text-white opacity-50 lh-1">
                        <div>XXXX</div>
                        <div>XXX</div>
                        <div>XX</div>
                    </div>
                </div>
                <div class="position-absolute" style="bottom: 16px; right: 16px; font-size: 1.1rem;">
                    <div class="text-white opacity-50 lh-1">
                        <div>XX</div>
                        <div>XXX</div>
                        <div>XXXX</div>
                    </div>
                </div>

                <!-- FAQ Title -->
                <div class="text-center mb-3">
                    <h2 class="text-white fw-bold mb-0" style="font-size: 1.5rem; letter-spacing: 0.5px;">FAQ - Mata Kuliah Inovasi Sosial</h2>
                </div>

                <!-- FAQ Items -->
                <div class="faq-items">
                    <div class="accordion" id="faqAccordion">
                        @php $faqFont = 'font-size: 1.1rem;'; @endphp
                        
                        <!-- FAQ Item 1 -->
                        <div class="accordion-item mb-2" style="background: rgba(255,255,255,0.97); border-radius: 12px; border: none;">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1" style="background: transparent; border: none; color: #333; {{ $faqFont }}">
                                    Apa itu Mata Kuliah Inovasi Sosial?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="{{ $faqFont }}">
                                    Mata kuliah Inovasi Sosial adalah mata kuliah penciri ITK yang wajib diambil mahasiswa mulai semester 5, bertujuan mengasah empati sosial, kolaborasi lintas prodi, dan penerapan ilmu untuk pengabdian masyarakat dengan metode Project Based Learning.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="accordion-item mb-2" style="background: rgba(255,255,255,0.97); border-radius: 12px; border: none;">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2" style="background: transparent; border: none; color: #333; {{ $faqFont }}">
                                    Apa saja syarat mengikuti konversi mata kuliah Inovasi Sosial?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="{{ $faqFont }}">
                                    <ol>
                                        <li>Minimal berada di semester 5</li>
                                        <li>Sudah lulus TPB</li>
                                        <li>Telah melaksanakan kegiatan pengabdian masyarakat</li>
                                        <li>Memiliki tim dengan minimal 2 prodi berbeda</li>
                                        <li>Menunjuk dosen pembimbing dan memiliki surat persetujuan</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="accordion-item mb-2" style="background: rgba(255,255,255,0.97); border-radius: 12px; border: none;">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3" style="background: transparent; border: none; color: #333; {{ $faqFont }}">
                                    Bagaimana cara mendaftarkan kegiatan untuk dikonversi?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="{{ $faqFont }}">
                                    Mahasiswa harus mengisi form konversi dan form kesesuaian CPMK di sistem, serta mengunggah dokumen pendukung seperti proposal, laporan akhir, logbook, dan luaran kegiatan (artikel dan video). Semua dokumen harus disetujui oleh tim MK Penciri.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div class="accordion-item mb-2" style="background: rgba(255,255,255,0.97); border-radius: 12px; border: none;">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4" style="background: transparent; border: none; color: #333; {{ $faqFont }}">
                                    Apa saja dokumen yang harus diunggah?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="{{ $faqFont }}">
                                    Mahasiswa harus menyelesaikan kegiatan terlebih dahulu, baru kemudian dapat mendaftar konversi. Konversi hanya bisa dilakukan setelah seluruh dokumen dan bukti kegiatan dilengkapi.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 5 -->
                        <div class="accordion-item mb-2" style="background: rgba(255,255,255,0.97); border-radius: 12px; border: none;">
                            <h2 class="accordion-header" id="faq5">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5" style="background: transparent; border: none; color: #333; {{ $faqFont }}">
                                    Bagaimana format tim mahasiswa?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="{{ $faqFont }}">
                                    <ol>
                                        <li>Ketua dan anggota berasal dari minimal 2 program studi berbeda</li>
                                        <li>Jumlah anggota: minimal 9, maksimal 10</li>
                                        <li>Salah satu anggota menjadi ketua tim dan disetujui oleh tim MK Penciri untuk diberi akses sistem</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 6 -->
                        <div class="accordion-item mb-2" style="background: rgba(255,255,255,0.97); border-radius: 12px; border: none;">
                            <h2 class="accordion-header" id="faq6">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6" style="background: transparent; border: none; color: #333; {{ $faqFont }}">
                                    Siapa yang memberi nilai kegiatan mahasiswa?
                                </button>
                            </h2>
                            <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="faq6" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="{{ $faqFont }}">
                                    Nilai diberikan oleh dosen pembimbing dan pembimbing lapangan. Penilaian mencakup proposal, laporan, dan luaran kegiatan. Nilai akhir kemudian direkap dan divalidasi oleh Tim MK Penciri untuk diunggah ke Gerbang ITK.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 7 -->
                        <div class="accordion-item mb-2" style="background: rgba(255,255,255,0.97); border-radius: 12px; border: none;">
                            <h2 class="accordion-header" id="faq7">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7" style="background: transparent; border: none; color: #333; {{ $faqFont }}">
                                    Apa luaran wajib dari MK Inovasi Sosial?
                                </button>
                            </h2>
                            <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="faq7" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="{{ $faqFont }}">
                                    <ol>
                                        <li>Video aftermovie kegiatan (diunggah ke YouTube)</li>
                                        <li>Artikel deskriptif kegiatan (diunggah ke laman publikasi ITK)</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 8 -->
                        <div class="accordion-item mb-2" style="background: rgba(255,255,255,0.97); border-radius: 12px; border: none;">
                            <h2 class="accordion-header" id="faq8">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8" style="background: transparent; border: none; color: #333; {{ $faqFont }}">
                                    Bagaimana jika kegiatan belum sepenuhnya selesai?
                                </button>
                            </h2>
                            <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="faq8" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="{{ $faqFont }}">
                                    Mahasiswa harus menyelesaikan kegiatan terlebih dahulu, baru kemudian dapat mendaftar konversi. Konversi hanya bisa dilakukan setelah seluruh dokumen dan bukti kegiatan dilengkapi.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tata Cara Section -->
    <div class="row mb-4 justify-content-center">
        <div class="col-12 col-md-11 col-lg-9">
            <div id="tata-cara-section" class="tata-cara-section mx-auto" style="background: #fff; border-radius: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); padding: 28px 20px 22px 20px; max-width: 900px;">
                <h2 class="text-center fw-bold text-dark mb-4" style="font-size: 1.8rem;">Tata Cara - Mata Kuliah Inovasi Sosial</h2>
                <div class="timeline-container position-relative" style="padding-left: 18px;">
                    <!-- Timeline Line -->
                    <div class="position-absolute" style="left: 18px; top: 0; bottom: 0; width: 2px; background: #e9ecef; border-left: 2px dashed #dee2e6;"></div>
                    <!-- Steps -->
                    @php $stepFont = 'font-size: 1.15rem;'; $badgeSize = 'width: 42px; height: 42px; font-size: 1.2rem; font-weight: 900;'; @endphp
                    <div class="timeline-step mb-3 position-relative">
                        <div class="d-flex align-items-start">
                            <div class="timeline-badge me-3" style="background: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #fff; z-index: 2; {{ $badgeSize }}">
                                1
                            </div>
                            <div class="timeline-content flex-grow-1" style="{{ $stepFont }}">
                                <span class="fw-bold">Pengajuan Formulir dan Proposal</span>
                                <ul class="list-unstyled mb-1 mt-1">
                                    <li style="font-size:1em;">• Mahasiswa mengajukan form konversi dan proposal kegiatan MK Inovasi Sosial kepada Tim MK Penciri.</li>
                                    <li style="font-size:1em;">• Proposal harus ditandatangani oleh dosen pembimbing.</li>
                                    <li style="font-size:1em;">• Jika kegiatan berasal dari program kompetisi (misal: KKN, PKM, dll), mahasiswa boleh menggunakan format proposal dari penyelenggara.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-step mb-3 position-relative">
                        <div class="d-flex align-items-start">
                            <div class="timeline-badge me-3" style="background: #e83e8c; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #fff; z-index: 2; {{ $badgeSize }}">
                                2
                            </div>
                            <div class="timeline-content flex-grow-1" style="{{ $stepFont }}">
                                <span class="fw-bold">Persetujuan oleh Tim MK Penciri</span>
                                <ul class="list-unstyled mb-1 mt-1">
                                    <li style="font-size:1em;">• Tim MK Penciri menilai kesesuaian kegiatan terhadap CPMK MK Inovasi Sosial (minimal 70% kesesuaian).</li>
                                    <li style="font-size:1em;">• Jika disetujui, mahasiswa mendapat izin konversi kegiatan ke MK Inovasi Sosial.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-step mb-3 position-relative">
                        <div class="d-flex align-items-start">
                            <div class="timeline-badge me-3" style="background: #e83e8c; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #fff; z-index: 2; {{ $badgeSize }}">
                                3
                            </div>
                            <div class="timeline-content flex-grow-1" style="border: 1.5px dashed #007bff; border-radius: 10px; padding: 10px 12px; background: rgba(0,123,255,0.04); {{ $stepFont }}">
                                <span class="fw-bold">Pelaksanaan Kegiatan</span>
                                <ul class="list-unstyled mb-1 mt-1">
                                    <li style="font-size:1em;">• Mahasiswa yang disetujui menjalankan kegiatan pengabdian sesuai proposal.</li>
                                    <li style="font-size:1em;">• Mahasiswa juga menyusun:</li>
                                    <li class="ms-3" style="font-size:1em;">• Laporan kemajuan,</li>
                                    <li class="ms-3" style="font-size:1em;">• Laporan akhir,</li>
                                    <li class="ms-3" style="font-size:1em;">• Logbook kegiatan,</li>
                                    <li class="ms-3" style="font-size:1em;">• Luaran (video aftermovie dan artikel berita).</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-step mb-3 position-relative">
                        <div class="d-flex align-items-start">
                            <div class="timeline-badge me-3" style="background: #17a2b8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #fff; z-index: 2; {{ $badgeSize }}">
                                4
                            </div>
                            <div class="timeline-content flex-grow-1" style="{{ $stepFont }}">
                                <span class="fw-bold">Evaluasi oleh Dosen Pembimbing dan Pembimbing Lapangan</span>
                                <ul class="list-unstyled mb-1 mt-1">
                                    <li style="font-size:1em;">• Setelah kegiatan selesai, Dosen pembimbing dan pembimbing lapangan memberikan evaluasi terhadap kegiatan mahasiswa.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-step mb-3 position-relative">
                        <div class="d-flex align-items-start">
                            <div class="timeline-badge me-3" style="background: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #fff; z-index: 2; {{ $badgeSize }}">
                                5
                            </div>
                            <div class="timeline-content flex-grow-1" style="{{ $stepFont }}">
                                <span class="fw-bold">Penilaian Akhir</span>
                                <ul class="list-unstyled mb-1 mt-1">
                                    <li style="font-size:1em;">• Dosen pembimbing dan pembimbing lapangan memberikan nilai berdasarkan rubrik yang tersedia.</li>
                                    <li style="font-size:1em;">• Penilaian mencakup: proposal, laporan akhir, asistensi, expo, peer review tim, dan penilaian pembimbing lapangan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-step mb-2 position-relative">
                        <div class="d-flex align-items-start">
                            <div class="timeline-badge me-3" style="background: #e83e8c; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #fff; z-index: 2; {{ $badgeSize }}">
                                6
                            </div>
                            <div class="timeline-content flex-grow-1" style="{{ $stepFont }}">
                                <span class="fw-bold">Pengisian Nilai ke Sistem ITK</span>
                                <ul class="list-unstyled mb-1 mt-1">
                                    <li style="font-size:1em;">• Tim MK Penciri menerima semua hasil penilaian, lalu menginput nilai akhir ke Gerbang ITK.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mb-3">
        <div class="col-12 text-center">
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
                <a href="{{ route('download.panduan') }}"  class="btn btn-primary px-4 py-3" style="border-radius: 8px; font-size: 1.2rem; font-weight: 600;">
                    <i class="fas fa-file-alt me-2"></i>
                    Unduh Panduan
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Ensure proper spacing from navbar */
body {
    padding-top: 40px;
}

.container {
    margin-top: 10px;
}

.accordion-button:not(.collapsed) {
    background-color: transparent !important;
    color: #333 !important;
    box-shadow: none !important;
}
.accordion-button:focus {
    box-shadow: none !important;
    border-color: transparent !important;
}
.accordion-button::after {
    display: none !important;
}
@media (max-width: 768px) {
    body {
        padding-top: 35px;
    }
    .container {
        margin-top: 8px;
    }
    .faq-section, .tata-cara-section {
        padding: 12px 4px 16px 4px !important;
        border-radius: 12px !important;
    }
    .timeline-container {
        padding-left: 6px !important;
    }
    .timeline-badge {
        width: 28px !important;
        height: 28px !important;
        font-size: 0.9rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordionItems = document.querySelectorAll('.accordion-item');
    accordionItems.forEach(item => {
        const button = item.querySelector('.accordion-button');
        button.addEventListener('click', function() {
            accordionItems.forEach(otherItem => {
                if (otherItem !== item) {
                    const otherCollapse = otherItem.querySelector('.accordion-collapse');
                    const otherButton = otherItem.querySelector('.accordion-button');
                    otherCollapse.classList.remove('show');
                    otherButton.classList.add('collapsed');
                }
            });
        });
    });
});
</script>

@endsection 
