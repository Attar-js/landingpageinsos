<!-- Start Header Area -->
    <header class="rbt-header rbt-header-10 rbt-transparent-header">
        <div class="rbt-sticky-placeholder"></div>
        <div class="rbt-header-wrapper  header-not-transparent header-sticky">
            <div class="container">
                <div class="mainbar-row rbt-navigation-center align-items-center">
                    <div class="header-left rbt-header-content">
                        <div class="header-info">
                            <div class="logo">
                                <a href="{{ route('landing') }}">
                                    <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Institut Teknologi Kalimantan">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="rbt-main-navigation d-none d-xl-block">
                        <nav class="mainmenu-nav">
                            <ul class="mainmenu">
                                <li class="with-megamenu has-menu-child-item position-static">
                                    <a href="{{ route('landing') }}">Home</a>
                                </li>

                                @if(Auth::check() && Auth::user()->isMahasiswa())
                                <li class="with-megamenu has-menu-child-item position-static">
                                    <a href="{{ route('konversi') }}">Pendaftaran Konversi</a>
                                </li>
                                <li class="with-megamenu has-menu-child-item position-static">
                                    <a href="{{ route('laporanakhir') }}">Luaran</a>
                                </li>
                                <li class="with-megamenu has-menu-child-item position-static">
                                    <a href="{{ route('peer-review.upload') }}">Peer Review</a>
                                </li>
                                @else
                                @unless(Auth::check() && (Auth::user()->isDosen() || Auth::user()->hasRole('tim_penciri')))
                                <li class="has-dropdown has-menu-child-item">
                                    <a href="#">Konversi
                                        <i class="feather-chevron-down"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('konversi') }}">Upload Form Konversi/Kesesuaian CPMK</a>
                                        </li>
                                        <li><a href="{{ route('form-kesediaan.upload') }}">Upload Surat Kesediaan Dosen Pembimbing</a>
                                        </li>
                                        <li><a href="{{ route('status-verifikasi') }}">Status Verifikasi Tim MK Penciri</a></li>
                                        <li><a href="{{ route('nilai-cpmk.index') }}">Nilai CPMK</a></li>
                                    </ul>
                                </li>

                                <li class="has-dropdown has-menu-child-item">
                                    <a href="#">Kegiatan
                                        <i class="feather-chevron-down"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('proposalkegiatan') }}">Upload Proposal Kegiatan</a>
                                        </li>
                                        <li><a href="{{ route('laporanakhir') }}">Upload Laporan Akhir dan Luaran</a>
                                        </li>
                                        <li><a href="{{ route('peer-review.upload') }}">Upload Peer Review</a>
                                        </li>
                                    </ul>
                                </li>
                                @endunless
                                @endif

                                @if(Auth::check() && Auth::user()->isDosen())
                                <li class="with-megamenu has-menu-child-item position-static">
                                    <a href="{{ route('dosen.persetujuan-kelompok') }}">Pendaftaran</a>
                                </li>
                                <li class="has-dropdown has-menu-child-item">
                                    <a href="#">Penilaian dan Evaluasi
                                        <i class="feather-chevron-down"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('dosen.validasi-dokumen') }}">Verifikasi Dokumen</a></li>
                                        <li><a href="{{ route('dosen.mahasiswa-bimbingan') }}">
                                            Penilaian
                                        </a></li>
                                    </ul>
                                </li>
                                @endif

                                @if(Auth::check() && Auth::user()->hasRole('tim_penciri'))
                                <li class="has-dropdown has-menu-child-item">
                                    <a href="#">Dashboard Verifikasi
                                        <i class="feather-chevron-down"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('tim-penciri.kesediaan-proposal') }}">Kesediaan Dosen dan Proposal</a></li>
                                        <li><a href="{{ route('tim-penciri.rubrik-cpmk') }}">Rubrik Penilaian CPMK</a></li>
                                        <li><a href="{{ route('tim-penciri.laporan-luaran') }}">Laporan Akhir dan Luaran</a></li>
                                    </ul>
                                </li>
                                @endif

                                @if(Auth::check() && Auth::user()->isAdmin())
                                <li class="with-megamenu has-menu-child-item position-static">
                                    <a href="{{ \App\Helpers\DashboardHelper::getDashboardUrl() }}" target="_blank" rel="noopener">Panel Admin</a>
                                </li>
                                @endif

                                @unless(Auth::check() && Auth::user()->hasRole('tim_penciri'))
                                <li class="has-dropdown has-menu-child-item">
                                    <a href="#">Panduan
                                        <i class="feather-chevron-down"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li><a href="{{ route('faqTataCara') }}">FAQ dan Tata Cara</a>
                                        </li>
                                        <li><a href="{{ route('template.index') }}">Template Dokumen</a></li>
                                        <li><a href="{{ route('kontak.admin') }}">Kontak Admin Tim MK Penciri</a>
                                    </ul>
                                </li>
                                @endunless
                            </ul>
                        </nav>
                    </div>

                    <div class="header-right">
                        <!-- Navbar Icons -->
                        <ul class="quick-access">

                            @if(Auth::check())
                            <li class="account-access rbt-user-wrapper d-none d-xl-block">
                                <a href="#"><i class="feather-user"></i>{{ Auth::user()->isDosen() ? (Auth::user()->nip ?? 'Dosen') : (Auth::user()->nim ?? 'User') }}</a>
                                <div class="rbt-user-menu-list-wrapper">
                                    <div class="inner">
                                        <div class="rbt-admin-profile">
                                            <div class="admin-info">
                                                <span class="name">{{ Auth::user()->isDosen() ? (Auth::user()->nip ?? 'Dosen') : (Auth::user()->nim ?? 'User') }}</span>
                                            </div>
                                        </div>
                                        <hr class="mt--10 mb--10">
                                        <ul class="user-list-wrapper">
                                            <li>
                                                <a href="{{ route('profile.show') }}">
                                                    <i class="feather-user"></i>
                                                    <span>Profil</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('logout.get') }}">
                                                    <i class="feather-log-out"></i>
                                                    <span>Logout</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            @else
                            <li class="account-access rbt-user-wrapper d-none d-xl-block">
                                <a href="{{ route('login') }}"><i class="feather-user"></i>Login</a>
                            </li>
                            @endif

                            @if(Auth::check())
                            <li class="access-icon rbt-user-wrapper d-block d-xl-none">
                                <a class="rbt-round-btn" href="#"><i class="feather-user"></i></a>
                                <div class="rbt-user-menu-list-wrapper">
                                    <div class="inner">
                                        <div class="rbt-admin-profile">
                                            <div class="admin-info">
                                                <span class="name">{{ Auth::user()->isDosen() ? (Auth::user()->nip ?? 'Dosen') : (Auth::user()->nim ?? 'User') }}</span>
                                            </div>
                                        </div>
                                        <ul class="user-list-wrapper">
                                            <li>
                                                <a href="{{ route('profile.show') }}">
                                                    <i class="feather-user"></i>
                                                    <span>Profil</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('logout.get') }}">
                                                    <i class="feather-log-out"></i>
                                                    <span>Logout</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            @else
                            <li class="access-icon rbt-user-wrapper d-block d-xl-none">
                                <a class="rbt-round-btn" href="{{ route('login') }}"><i class="feather-user"></i></a>
                            </li>
                            @endif
                        </ul>

                        <!-- Start Mobile-Menu-Bar -->
                        <div class="mobile-menu-bar d-block d-xl-none">
                            <div class="hamberger">
                                <button class="hamberger-button rbt-round-btn mobile-menu-toggle">
                                    <i class="feather-menu"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Start Mobile-Menu-Bar -->

                    </div>
                </div>
            </div>
        </div>
    </header>
