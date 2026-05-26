@extends('layout.layout')

@php
     $footer='true';
     $topToBottom='true';
     $header='false';
@endphp

@section('content')
<x-header/>
<div style="height: 0px;"></div>

    <!-- Start Side Vav -->
    <x-sideVav/>
    <!-- End Side Vav -->

    <a class="close_side_menu" href="javascript:void(0);"></a>

    <!-- Start Banner Area -->
    <div class="rbt-banner-area rbt-banner-3 header-transperent-spacer">
        <div class="wrapper">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-7 order-2 order-lg-1">
                        <div class="banner-content ">
                            <div class="inner">
                                <h1 class="title" style="background: linear-gradient(90deg, #0038F0, #9C27B0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Inovasi Sosial</h1>
                                <p class="description" style="text-align: justify;">sebuah mata kuliah wajib bagi seluruh mahasiswa Institut Teknologi Kalimantan (ITK) yang dirancang untuk meningkatkan empati dan kepedulian mereka terhadap permasalahan yang ada di masyarakat. Melalui mata kuliah ini, mahasiswa diharapkan dapat memberikan solusi atas berbagai masalah sosial dengan menerapkan ilmu pengetahuan dan teknologi secara berkelompok dan lintas keilmuan. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 order-1 order-lg-2">
                        <div class="text-center">
                            <img src="{{ asset('assets/images/category/image/definisi.jpg') }}" alt="Definisi Inovasi Sosial" class="img-fluid" style="max-width: 100%; height: auto; border-radius: 15px; ">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="shape-wrapper">
            <div class="top-shape">
                <img src="{{ asset('assets/images/banner/top-shape.png') }}" alt="Banner Images">
            </div>
            <div class="marque-images edumarque"></div>
        </div>
    </div>
    <!-- End Banner Area -->

<div class="rbt-banner-area rbt-banner-3 header-transperent-spacer">
    <div class="wrapper">
        <div class="container">
            <div class="row g-5 align-items-center">
                <!-- Gambar Ilustrasi di kiri -->
                <div class="col-lg-6 order-1 order-lg-1 text-center">
                    <img src="{{ asset('assets/images/category/image/deskripsi.jpg') }}" alt="Deskripsi Gambar" class="img-fluid" style="max-width: 100%; height: auto;">
                </div>

                <!-- Konten Deskripsi di kanan -->
                <div class="col-lg-6 order-2 order-lg-2">
                    <div class="banner-content">
                        <div class="inner">
                            <!-- Judul -->
                            <h1 class="title" style="background: linear-gradient(90deg, #0038F0, #9C27B0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                Deskripsi
                            </h1>

                            <!-- Deskripsi -->
                            <p class="description" style="text-align: justify;">
                                Inovasi Sosial adalah mata kuliah wajib di ITK yang berfokus pada pengabdian kepada masyarakat. Mahasiswa didorong untuk mengidentifikasi potensi dan permasalahan di lapangan, merancang solusi yang berkelanjutan, serta berkolaborasi lintas disiplin bersama mitra lokal. Melalui kegiatan ini, mahasiswa tidak hanya mengasah kemampuan intelektual, tetapi juga membangun empati, kepemimpinan, dan tanggung jawab sosial.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Start Tahapan Konversi -->
<section class="py-5" style="background: url('{{ asset('assets/images/banner/h-banner.jpg') }}') center center / cover no-repeat;">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold text-primary">Tahapan Konversi</h3>
        </div>

        <!-- Row 1: 3 Cards -->
        <div class="row justify-content-center text-center mb-4">
            <div class="col-md-4 mb-4">
                <div class="bg-white bg-opacity-75 shadow rounded p-4 h-100">
                    <div class="mb-3">
                        <div class="rounded-circle bg-warning text-white fw-bold mx-auto d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">1</div>
                    </div>
                    <h5 class="fw-bold">Mengajukan Proposal & Form Konversi</h5>
                    <p class="mb-0">Mahasiswa mengajukan proposal dan form konversi mata kuliah yang telah ditandatangani oleh dosen pembimbing paling lambat satu minggu sebelum perkuliahan dimulai.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="bg-white bg-opacity-75 shadow rounded p-4 h-100">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white fw-bold mx-auto d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">2</div>
                    </div>
                    <h5 class="fw-bold">Persetujuan Konversi</h5>
                    <p class="mb-0">Tim MK Penciri memberikan persetujuan Form Konversi Mata Kuliah kepada mahasiswa. Kegiatan yang dinyatakan dapat dikonversi harus memiliki kesesuaian minimal 70%.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="bg-white bg-opacity-75 shadow rounded p-4 h-100">
                    <div class="mb-3">
                        <div class="rounded-circle bg-danger text-white fw-bold mx-auto d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">3</div>
                    </div>
                    <h5 class="fw-bold">Pelaksanaan Kegiatan & Penyusunan Laporan</h5>
                    <p class="mb-0">Mahasiswa yang telah memperoleh persetujuan konversi melaksanakan kegiatan, menyusun laporan kemajuan, dan laporan akhir kegiatan sesuai dengan Template Laporan Akhir.</p>
                </div>
            </div>
        </div>

        <!-- Row 2: 2 Cards with Same Width as Row 1 -->
        <div class="row justify-content-center text-center">
            <div class="col-md-4 mb-4">
                <div class="bg-white bg-opacity-75 shadow rounded p-4 h-100">
                    <div class="mb-3">
                        <div class="rounded-circle bg-success text-white fw-bold mx-auto d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">4</div>
                    </div>
                    <h5 class="fw-bold">Evaluasi Kegiatan</h5>
                    <p class="mb-0">Mahasiswa melakukan evaluasi kegiatan kepada dosen pembimbing dan menyerahkan laporan kegiatan serta presentasi. Mahasiswa mendapatkan nilai dari dosen pembimbing dan pembimbing lapangan.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="bg-white bg-opacity-75 shadow rounded p-4 h-100">
                    <div class="mb-3">
                        <div class="rounded-circle bg-info text-white fw-bold mx-auto d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">5</div>
                    </div>
                    <h5 class="fw-bold">Penilaian Hasil Kegiatan</h5>
                    <p class="mb-0">Tim MK Penciri mendapatkan hasil penilaian dari dosen pembimbing dan pembimbing lapangan yang kemudian dilakukan pengisian nilai ke Gerbang ITK untuk mata kuliah Inovasi Sosial</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Tahapan Konversi -->

        

    <!-- Background Optional -->
    <div class="shape-wrapper">
        <div class="marque-images edumarque"></div>
    </div>
</div>

    <div class="rbt-categories-area bg-color-white rbt-section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <span class="subtitle bg-primary-opacity">Inovasi Sosial</span>
                        <h2 class="title">Luaran Kegiatan</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center text-center py-2">
                <!-- Start Category Box Layout  -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="rbt-cat-box rbt-cat-box-1 variation-2 text-center">
                        <div class="inner">
                            <div class="thumbnail">
                                <a href="#">
                                    <img src="{{ asset('assets/images/category/image/artikel.jpg') }}" alt="Category Images">
                                </a>
                            </div>
                            <div class="icons">
                                <img src="{{ asset('assets/images/category/web-design.png') }}" alt="Icons Images">
                            </div>
                            <div class="content">
                                <h5 class="title"><a href="#">Artikel</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Category Box Layout  -->
                <!-- Start Category Box Layout  -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="rbt-cat-box rbt-cat-box-1 variation-2 text-center">
                        <div class="inner">
                            <div class="thumbnail">
                                <a href="#">
                                    <img src="{{ asset('assets/images/category/image/aftermovie.jpg') }}" alt="Category Images">
                                </a>
                            </div>
                            <div class="icons">
                                <img src="{{ asset('assets/images/category/aftermovie.png') }}" alt="Icons Images">
                            </div>
                            <div class="content">
                                <h5 class="title"><a href="#">After Movie</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Category Box Layout  -->
                <!-- Start Category Box Layout  -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="rbt-cat-box rbt-cat-box-1 variation-2 text-center">
                        <div class="inner">
                            <div class="thumbnail">
                                <a href="#">
                                    <img src="{{ asset('assets/images/category/image/jurnal.jpg') }}" alt="Category Images">
                                </a>
                            </div>
                            <div class="icons">
                                <img src="{{ asset('assets/images/category/journal.png') }}" alt="Icons Images">
                            </div>
                            <div class="content">
                                <h5 class="title"><a href="#">Jurnal</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="service-wrapper bg-color-white rbt-section-gap">
        <div class="container">
            <div class="row mb--60">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <span class="subtitle bg-pink-opacity">Inovasi Sosial</span>
                        <h2 class="title">Penilaian Kegiatan</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center text-center">
                <div class="col-lg-12">
                    <div class="row justify-content-center text-center">
                        <!-- Start Single Card  -->
                        <div class="col-xl-3 col-md-6 col-sm-6 col-12 mt--30">
                            <div class="rbt-flipbox">
                                <div class="rbt-flipbox-wrap rbt-service rbt-service-1 card-bg-1">
                                    <div class="rbt-flipbox-front rbt-flipbox-face inner">
                                        <div class="icon">
                                            <img src="{{ asset('assets/images/icons/card-icon-1.png') }}" alt="card-icon">
                                        </div>
                                        <div class="content">
                                            <h5 class="title"><a href="#">Proposal Kegiatan</a></h5>
                                            <p>20%</p>
                                            <a class="rbt-btn-link stretched-link" href="#">Learn More<i class="feather-arrow-right"></i></a>
                                        </div>
                                    </div>
                                    <div class="rbt-flipbox-back rbt-flipbox-face inner">
                                        <ul class="rbt-list-style-3 color-white">
                                            <li><i class="feather-info"></i> Dosen pembimbing melakukan penilaian pada proposal kegiatan berdasarkan Rubrik Penilaian Proposal yang terdapat pada Lampiran Form Penilaian Proposal dan Lampiran Rubrik Penilaian Proposal.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->

                        <!-- Start Single Card  -->
                        <div class="col-xl-3 col-md-6 col-sm-6 col-12 mt--30">
                            <div class="rbt-flipbox">
                                <div class="rbt-flipbox-wrap rbt-service rbt-service-1 card-bg-2">
                                    <div class="rbt-flipbox-front rbt-flipbox-face inner">
                                        <div class="icon">
                                            <img src="{{ asset('assets/images/icons/card-icon-2.png') }}" alt="card-icon">
                                        </div>
                                        <div class="content">
                                            <h5 class="title"><a href="#">Asistensi</a></h5>
                                            <p>10%</p>
                                            <a class="rbt-btn-link stretched-link" href="#">Learn More<i class="feather-arrow-right"></i></a>
                                        </div>
                                    </div>

                                    <div class="rbt-flipbox-back rbt-flipbox-face inner">
                                        <ul class="rbt-list-style-3 color-white">
                                            <li><i class="feather-info"></i>Dosen Pembimbing melakukan penilaian asistensi berdasarkan Lembar Asistensi Dosen Pembimbing. Dosen Pembimbing dapat menggunakan Form Penilaian Asistensi dan Lampiran Rubrik Penilaian Asistensi sebagai acuan.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->

                        <!-- Start Single Card  -->
                        <div class="col-xl-3 col-md-6 col-sm-6 col-12 mt--30">
                            <div class="rbt-flipbox">
                                <div class="rbt-flipbox-wrap rbt-service rbt-service-1 card-bg-3">
                                    <div class="rbt-flipbox-front rbt-flipbox-face inner">
                                        <div class="icon">
                                            <img src="{{ asset('assets/images/icons/card-icon-3.png') }}" alt="card-icon">
                                        </div>
                                        <div class="content">
                                            <h5 class="title"><a href="#">Peer Review Tim</a></h5>
                                            <p>15%</p>
                                            <a class="rbt-btn-link stretched-link" href="#">Learn More<i class="feather-arrow-right"></i></a>
                                        </div>
                                    </div>

                                    <div class="rbt-flipbox-back rbt-flipbox-face inner">
                                        <ul class="rbt-list-style-3 color-white">
                                            <li><i class="feather-info"></i>Peer Review Tim merupakan nilai yang diberikan anggota tim untuk menilai peforma anggota lainnya. Anggota tim dapat menggunakan Rubrik Penilaian Peer Review Tim untuk menilai. Nilai Peer Review Tim dikumpulkan menggunakan Lampiran Form Penilaian Peer Review Tim.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->

                        <!-- Start Single Card  -->
                        <div class="col-xl-3 col-md-6 col-sm-6 col-12 mt--30">
                            <div class="rbt-flipbox">
                                <div class="rbt-flipbox-wrap rbt-service rbt-service-1 card-bg-4">
                                    <div class="rbt-flipbox-front rbt-flipbox-face inner">
                                        <div class="icon">
                                            <img src="{{ asset('assets/images/icons/card-icon-4.png') }}" alt="card-icon">
                                        </div>
                                        <div class="content">
                                            <h5 class="title"><a href="#">Laporan Akhir</a></h5>
                                            <p>20%</p>
                                            <a class="rbt-btn-link stretched-link" href="#">Learn More<i class="feather-arrow-right"></i></a>
                                        </div>
                                    </div>

                                    <div class="rbt-flipbox-back rbt-flipbox-face inner">
                                        <ul class="rbt-list-style-3 color-white">
                                            <li><i class="feather-info"></i>Dosen Pembimbing melakukan penilaian terhadap Laporan Akhir menggunakan Lampiran Form Penilaian Laporan Akhir, bedasarkan Lampiran Rubrik Penilaian Laporan Akhir.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->

                        <!-- Start Single Card  -->
                        <div class="col-xl-3 col-md-6 col-sm-6 col-12 mt--30">
                            <div class="rbt-flipbox">
                                <div class="rbt-flipbox-wrap rbt-service rbt-service-1 card-bg-1">
                                    <div class="rbt-flipbox-front rbt-flipbox-face inner">
                                        <div class="icon">
                                            <img src="{{ asset('assets/images/icons/card-icon-1.png') }}" alt="card-icon">
                                        </div>
                                        <div class="content">
                                            <h5 class="title"><a href="#">Presentasi Akhir (Expo)</a></h5>
                                            <p>20%</p>
                                            <a class="rbt-btn-link stretched-link" href="#">Learn More<i class="feather-arrow-right"></i></a>
                                        </div>
                                    </div>
                                    <div class="rbt-flipbox-back rbt-flipbox-face inner">
                                        <ul class="rbt-list-style-3 color-white">
                                            <li><i class="feather-info"></i>Penilaian Expo dapat dilakukan pada saat pelaksanaan Expo menggunakan Form Penilaian Expo dan Rubrik Penilaian Expo.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->
                        <!-- Start Single Card  -->
                        <div class="col-xl-3 col-md-6 col-sm-6 col-12 mt--30">
                            <div class="rbt-flipbox">
                                <div class="rbt-flipbox-wrap rbt-service rbt-service-1 card-bg-2">
                                    <div class="rbt-flipbox-front rbt-flipbox-face inner">
                                        <div class="icon">
                                            <img src="{{ asset('assets/images/icons/card-icon-2.png') }}" alt="card-icon">
                                        </div>
                                        <div class="content">
                                            <h5 class="title"><a href="#">Pembimbing Lapangan</a></h5>
                                            <p>10%</p>
                                            <a class="rbt-btn-link stretched-link" href="#">Learn More<i class="feather-arrow-right"></i></a>
                                        </div>
                                    </div>

                                    <div class="rbt-flipbox-back rbt-flipbox-face inner">
                                        <ul class="rbt-list-style-3 color-white">
                                            <li><i class="feather-info"></i>Pembimbing lapangan melakukan penilaian terhadap kegiatan yang telah dilaksanakan, berdasarkan Rubrik Penilaian Pembimbing Lapangan. </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Card  -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
