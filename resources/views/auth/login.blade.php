<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerbang ITK - Login</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('{{ asset("assets/images/category/image/login.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: backgroundZoom 20s ease-in-out infinite;
        }

        @keyframes backgroundZoom {
            0%, 100% { background-size: 100% auto; }
            50% { background-size: 110% auto; }
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.4) 100%);
            z-index: 1;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 900px;
            width: 90%;
            margin: 2rem;
            display: flex;
            min-height: 600px;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .illustration-panel {
            background: url('{{ asset("assets/images/category/image/logincard.jpg") }}') no-repeat center center;
            background-size: cover;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            padding: 2rem 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.1);
        }

        .illustration-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .overlay-content {
            position: relative;
            z-index: 2;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 0;
            padding: 0;
        }



        .form-panel {
            flex: 1;
            background: white;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            text-align: center;
            margin-bottom: 2rem;
        }

        .main-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 0;
        }

        .divider {
            width: 50px;
            height: 3px;
            background: #3498db;
            margin: 1rem auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-group {
            position: relative;
        }

        .form-control {
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            padding: 0.75rem 1rem 0.75rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            width: 100%;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            background-color: white;
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            z-index: 3;
        }

        .btn-login {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: bold;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #2980b9 0%, #1f5f8b 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            color: white;
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
            color: #7f8c8d;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
            }
            
            .illustration-panel {
                padding: 1.5rem 1rem;
                min-height: 300px;
                background-size: cover;
            }
            
            .form-panel {
                padding: 2rem 1.5rem;
            }
            
            .overlay-content h2 {
                font-size: 1.5rem !important;
            }
            
            .overlay-content img {
                width: 40px !important;
                height: 40px !important;
            }
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            border: 2px solid #3498db;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #3498db;
            border-color: #3498db;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            outline: none;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #2c3e50;
            cursor: pointer;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }


    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel - Illustration -->
        <div class="illustration-panel">
            <div class="overlay-content">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Institut Teknologi Kalimantan" style="height: 70px; width: auto; object-fit: contain;">
                <div>
                    <h2 style="font-size: 2rem; font-weight: bold; margin-bottom: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); line-height: 1.2;">INOVASI SOSIAL</h2>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="form-panel">
            <div class="form-title">
                <h1 class="main-title">Silakan melakukan Log In:</h1>
                <div class="divider"></div>
                <p class="subtitle">Login dengan User dan Password Gerbang</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}" 
                               placeholder="Masukkan Username"
                               required 
                               autofocus>
                    </div>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Masukkan Password"
                               required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember" 
                               name="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            <i class="fas fa-clock me-1"></i>Remember Me
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    Log In
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Footer -->
            <div class="footer">
                Copyright © 2025 Institut Teknologi Kalimantan
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
