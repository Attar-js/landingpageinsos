<div class="popup-mobile-menu">
    <div class="inner-wrapper">
        <div class="inner-top">
            <div class="content">
                <div class="logo">
                    <div class="logo logo-dark">
                        <a href="{{ route('landing') }}">
                            <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Education Logo Images">
                        </a>
                    </div>

                    <div class="logo d-none logo-light">
                        <a href="{{ route('landing') }}">
                            <img src="{{ asset('assets/images/dark/logo/logo-light.png') }}" alt="Education Logo Images">
                        </a>
                    </div>
                </div>
                <div class="rbt-btn-close">
                    <button class="close-button rbt-round-btn"><i class="feather-x"></i></button>
                </div>
            </div>
            <p class="description">Inovasi Sosial - Institut Teknologi Kalimantan</p>
            <ul class="navbar-top-left rbt-information-list justify-content-start">
                <li>
                    <a href="mailto:inovasisosial@itk.ac.id"><i class="feather-mail"></i>inovasisosial@itk.ac.id</a>
                </li>
                <li>
                    <a href="#"><i class="feather-phone"></i>+62 541 673853</a>
                </li>
            </ul>
        </div>

        <nav class="mainmenu-nav">
            <ul class="mainmenu">
                <li>
                    <a href="{{ route('landing') }}">Home</a>
                </li>

                @if(Auth::check() && Auth::user()->isMahasiswa())
                <li>
                    <a href="{{ route('konversi') }}">Pendaftaran Konversi</a>
                </li>
                <li>
                    <a href="{{ route('laporanakhir') }}">Luaran</a>
                </li>
                <li>
                    <a href="{{ route('peer-review.upload') }}">Peer Review</a>
                </li>
                @else
                @unless(Auth::check() && (Auth::user()->isDosen() || Auth::user()->hasRole('tim_penciri')))
                <li class="has-dropdown has-menu-child-item">
                    <a href="#">Konversi <i class="feather-chevron-down"></i></a>
                    <ul class="submenu">
                        <li><a href="{{ route('konversi') }}">Upload Form Konversi/Kesesuaian CPMK</a></li>
                        <li><a href="{{ route('form-kesediaan.upload') }}">Upload Surat Kesediaan Dosen Pembimbing</a></li>
                        <li><a href="{{ route('status-verifikasi') }}">Status Verifikasi Tim MK Penciri</a></li>
                        <li><a href="{{ route('nilai-cpmk.index') }}">Nilai CPMK</a></li>
                    </ul>
                </li>

                <li class="has-dropdown has-menu-child-item">
                    <a href="#">Kegiatan <i class="feather-chevron-down"></i></a>
                    <ul class="submenu">
                        <li><a href="{{ route('proposalkegiatan') }}">Upload Proposal Kegiatan</a></li>
                        <li><a href="{{ route('laporanakhir') }}">Upload Laporan Akhir dan Luaran</a></li>
                        <li><a href="{{ route('peer-review.upload') }}">Upload Peer Review</a></li>
                    </ul>
                </li>
                @endunless
                @endif

                @if(Auth::check() && Auth::user()->isDosen())
                <li>
                    <a href="{{ route('dosen.persetujuan-kelompok') }}">Pendaftaran</a>
                </li>
                <li class="has-dropdown has-menu-child-item">
                    <a href="#">Penilaian dan Evaluasi <i class="feather-chevron-down"></i></a>
                    <ul class="submenu">
                        <li><a href="{{ route('dosen.validasi-dokumen') }}">Verifikasi Dokumen</a></li>
                        <li><a href="{{ route('dosen.mahasiswa-bimbingan') }}">Penilaian</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::check() && Auth::user()->hasRole('tim_penciri'))
                <li class="has-dropdown has-menu-child-item">
                    <a href="#">Dashboard Verifikasi <i class="feather-chevron-down"></i></a>
                    <ul class="submenu">
                        <li><a href="{{ route('tim-penciri.kesediaan-proposal') }}">Kesediaan Dosen dan Proposal</a></li>
                        <li><a href="{{ route('tim-penciri.rubrik-cpmk') }}">Rubrik Penilaian CPMK</a></li>
                        <li><a href="{{ route('tim-penciri.laporan-luaran') }}">Laporan Akhir dan Luaran</a></li>
                    </ul>
                </li>
                @endif

                @if(Auth::check() && Auth::user()->isAdmin())
                <li>
                    <a href="{{ \App\Helpers\DashboardHelper::getDashboardUrl() }}" target="_blank" rel="noopener">Panel Admin</a>
                </li>
                @endif

                @if(Auth::check())
                <li>
                    <a href="{{ route('profile.show') }}">Profil</a>
                </li>
                @endif

                @unless(Auth::check() && Auth::user()->hasRole('tim_penciri'))
                                 <li class="has-dropdown has-menu-child-item">
                     <a href="#">Panduan <i class="feather-chevron-down"></i></a>
                     <ul class="submenu">
                         <li><a href="{{ route('faqTataCara') }}">FAQ dan Tata Cara</a></li>
                         <li><a href="{{ route('template.index') }}">Template Dokumen</a></li>
                         <li><a href="{{ route('kontak.admin') }}">Kontak Admin Tim MK Penciri</a></li>
                     </ul>
                 </li>
                @endunless
            </ul>
        </nav>

        <div class="mobile-menu-bottom">
            @if(Auth::check())
            <div class="rbt-btn-wrapper mb--20">
                <div class="rbt-admin-profile">
                    <div class="admin-info">
                        <span class="name">{{ Auth::user()->isDosen() ? (Auth::user()->nip ?? 'Dosen') : (Auth::user()->nim ?? 'User') }}</span>
                    </div>
                </div>
                {{-- <a class="rbt-btn btn-border-gradient radius-round btn-sm hover-transform-none w-100 justify-content-center text-center mt--20" href="{{ route('logout.get') }}">
                    <span>Logout</span>
                </a>
            </div>
            @else
            <div class="rbt-btn-wrapper mb--20">
                <a class="rbt-btn btn-border-gradient radius-round btn-sm hover-transform-none w-100 justify-content-center text-center" href="{{ route('login') }}">
                    <span>Login</span>
                </a> --}}
            </div>
            @endif

            <div class="social-share-wrapper">
                <span class="rbt-short-title d-block">Find Us</span>
                <ul class="social-icon social-default transparent-with-border justify-content-start mt--20">
                    <li><a href="https://www.facebook.com/ITKalimantan">
                            <i class="feather-facebook"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://x.com/itk_official_">
                            <i class="feather-twitter"></i>
                        </a>
                    </li>
                    <li><a href="https://www.instagram.com/itk_official/">
                            <i class="feather-instagram"></i>
                        </a>
                    </li>
                    <li><a href="https://id.linkedin.com/school/institut-teknologi-kalimantan/">
                            <i class="feather-linkedin"></i>
                        </a>
                    </li>
                    <li><a href="https://www.youtube.com/@institutteknologikalimantan">
                            <i class="feather-youtube"></i>
                        </a>
                    </li>
                    <li>
                        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
                        <a href="https://www.tiktok.com/@itk_official">
                            <i class='fab fa-tiktok'></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle functionality
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const popupMobileMenu = document.querySelector('.popup-mobile-menu');
    const closeButton = document.querySelector('.close-button');
    const body = document.body;

    if (mobileMenuToggle && popupMobileMenu) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            popupMobileMenu.classList.add('menu-open');
            body.classList.add('popup-mobile-menu-wrapper-open');
        });
    }

    if (closeButton && popupMobileMenu) {
        closeButton.addEventListener('click', function(e) {
            e.preventDefault();
            popupMobileMenu.classList.remove('menu-open');
            body.classList.remove('popup-mobile-menu-wrapper-open');
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (popupMobileMenu && popupMobileMenu.classList.contains('menu-open')) {
            if (!popupMobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                popupMobileMenu.classList.remove('menu-open');
                body.classList.remove('popup-mobile-menu-wrapper-open');
            }
        }
    });

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && popupMobileMenu && popupMobileMenu.classList.contains('menu-open')) {
            popupMobileMenu.classList.remove('menu-open');
            body.classList.remove('popup-mobile-menu-wrapper-open');
        }
    });

    // Handle submenu toggles
    const dropdownItems = document.querySelectorAll('.has-dropdown');
    dropdownItems.forEach(item => {
        const link = item.querySelector('a');
        const submenu = item.querySelector('.submenu');
        
        if (link && submenu) {
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) { // Only on mobile
                    // Check if the link has a href that points to a route
                    const href = link.getAttribute('href');
                    if (href && href !== '#' && !href.startsWith('javascript:')) {
                        // If it's a real link, don't prevent default and don't toggle dropdown
                        return;
                    }
                    e.preventDefault();
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                    item.classList.toggle('active');
                }
            });
        }
    });
});
</script>

<style>
/* Mobile menu styles */
.popup-mobile-menu {
    position: fixed;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100vh;
    background: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
}

.popup-mobile-menu.menu-open {
    left: 0;
    opacity: 1;
    visibility: visible;
}

.popup-mobile-menu .inner-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    width: 320px;
    height: 100%;
    background: #fff;
    overflow-y: auto;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.popup-mobile-menu.menu-open .inner-wrapper {
    transform: translateX(0);
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .popup-mobile-menu .inner-wrapper {
        width: 100%;
    }
    
    .mainmenu-nav .mainmenu {
        padding: 0;
    }
    
    .mainmenu-nav .mainmenu li {
        border-bottom: 1px solid #eee;
    }
    
    .mainmenu-nav .mainmenu li a {
        padding: 15px 20px;
        display: block;
        color: #333;
        text-decoration: none;
    }
    
    .mainmenu-nav .submenu {
        display: none;
        background: #f8f9fa;
        padding-left: 20px;
    }
    
    .mainmenu-nav .submenu li a {
        padding: 10px 20px;
        font-size: 0.9em;
    }
    
    .has-dropdown.active .submenu {
        display: block;
    }
}

/* Ensure body doesn't scroll when menu is open */
body.popup-mobile-menu-wrapper-open {
    overflow: hidden;
}
</style>

