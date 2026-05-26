@extends('layout.layout')

@section('content')
<div class="rbt-banner-area rbt-banner-3 header-transperent-spacer">
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-content text-center">
                        <div class="inner">
                            <h1 class="title" style="background: linear-gradient(90deg, #0038F0, #9C27B0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Kontak Admin</h1>
                            <p class="description">Hubungi Tim MK Penciri untuk bantuan dan informasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="rbt-section-gap bg-color-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title text-center">
                    <h2 class="title">Tim MK Penciri</h2>
                    <p class="description">Tim Mata Kuliah Penciri Institut Teknologi Kalimantan</p>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="rbt-card box-card-style-1">
                    <div class="inner">
                        <div class="content">
                            <h4 class="title">Informasi Kontak</h4>
                            <div class="contact-info">
                                <div class="contact-item">
                                    <i class="feather-mail"></i>
                                    <div class="info">
                                        <h6>Email</h6>
                                        <p>inovasisosial@itk.ac.id</p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="feather-phone"></i>
                                    <div class="info">
                                        <h6>Telepon</h6>
                                        <p>+62 541 673853</p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="feather-map-pin"></i>
                                    <div class="info">
                                        <h6>Alamat</h6>
                                        <p>Institut Teknologi Kalimantan<br>Jl. Soekarno-Hatta Km. 15, Balikpapan, Kalimantan Timur</p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="feather-clock"></i>
                                    <div class="info">
                                        <h6>Jam Kerja</h6>
                                        <p>Senin - Jumat: 08:00 - 16:00 WITA</p>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- <div class="rbt-btn-group mt--30">
                                <a class="rbt-btn btn-gradient hover-icon-reverse" href="mailto:inovasisosial@itk.ac.id">
                                    <span class="icon-reverse-wrapper">
                                        <span class="btn-text">Kirim Email</span>
                                        <span class="btn-icon"><i class="feather-mail"></i></span>
                                        <span class="btn-icon"><i class="feather-mail"></i></span>
                                    </span>
                                </a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-info {
    margin-top: 30px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.contact-item i {
    font-size: 20px;
    color: #0038F0;
    margin-right: 15px;
    margin-top: 5px;
}

.contact-item .info h6 {
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.contact-item .info p {
    margin: 0;
    color: #666;
    line-height: 1.5;
}
</style>
@endsection 
