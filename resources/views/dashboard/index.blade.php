<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerbang ITK</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #2c3e50 !important;
        }

        .nav-link {
            color: #7f8c8d !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #e74c3c !important;
        }

        .main-content {
            padding: 2rem 0;
        }

        .welcome-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            margin-bottom: 2rem;
        }

        .welcome-title {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .welcome-text {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ff6b6b, #4ecdc4, #45b7d1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .feature-icon i {
            font-size: 2rem;
            color: white;
        }

        .feature-title {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .feature-text {
            color: #7f8c8d;
        }

        .btn-custom {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: bold;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                Gerbang ITK
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('konversi') }}">
                            <i class="fas fa-exchange-alt me-1"></i>Form Konversi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('laporanakhir') }}">
                            <i class="fas fa-upload me-1"></i>Upload Laporan Akhir dan Luaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('status-verifikasi') }}">
                            <i class="fas fa-list me-1"></i>Status Luaran
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ $user->name ?? 'User' }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h1 class="welcome-title">
                    <i class="fas fa-home me-2"></i>
                    Selamat Datang di Gerbang ITK
                </h1>
                <p class="welcome-text">
                    Sistem Informasi Terintegrasi Institut Teknologi Kalimantan
                </p>
                <p class="welcome-text">
                    Halo, <strong>{{ $user->name ?? 'User' }}</strong>! Anda berhasil login ke sistem.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h4 class="feature-title">Form Konversi</h4>
                        <p class="feature-text">Upload dan konversi mata kuliah KKN Anda dengan mudah.</p>
                        <a href="{{ route('konversi') }}" class="btn btn-custom">
                            <i class="fas fa-arrow-right me-1"></i>Akses Form
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-upload"></i>
                        </div>
                        <h4 class="feature-title">Laporan Akhir dan Luaran</h4>
                        <p class="feature-text">Upload laporan akhir dan luaran kegiatan KKN dalam satu menu.</p>
                        <a href="{{ route('laporanakhir') }}" class="btn btn-custom">
                            <i class="fas fa-arrow-right me-1"></i>Upload Dokumen
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <h4 class="feature-title">Status Luaran</h4>
                        <p class="feature-text">Lihat status dan progress upload luaran Anda.</p>
                        <a href="{{ route('status-verifikasi') }}" class="btn btn-custom">
                            <i class="fas fa-arrow-right me-1"></i>Cek Status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
