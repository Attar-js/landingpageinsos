<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Login Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Status Login Check</h3>
                    </div>
                    <div class="card-body">
                        @if(Auth::check())
                            <div class="alert alert-success">
                                <h4>✅ Anda SUDAH LOGIN!</h4>
                                <p><strong>User ID:</strong> {{ Auth::id() }}</p>
                                <p><strong>NIM:</strong> {{ Auth::user()->nim }}</p>
                                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                                <p><strong>Nama:</strong> {{ Auth::user()->first_name . ' ' . Auth::user()->last_name ?? Auth::user()->name }}</p>
                                <p><strong>Role:</strong> {{ Auth::user()->role }}</p>
                                <p><strong>Status:</strong> {{ Auth::user()->status }}</p>
                                <p><strong>Login Time:</strong> {{ now() }}</p>
                                <p><strong>Remember Token:</strong> {{ Auth::user()->remember_token ? 'Set' : 'Not Set' }}</p>
                                <p><strong>Session ID:</strong> {{ session()->getId() }}</p>
                                <p><strong>Session Lifetime:</strong> {{ config('session.lifetime') }} minutes</p>
                                <p><strong>Expire on Close:</strong> {{ config('session.expire_on_close') ? 'Yes' : 'No' }}</p>
                            </div>
                            
                            <div class="mt-3">
                                <h5>Actions:</h5>
                                <a href="{{ route('landing') }}" class="btn btn-primary">Go to Landing Page</a>
                                <a href="{{ route('logout.get') }}" class="btn btn-danger">Logout</a>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <h4>❌ Anda BELUM LOGIN!</h4>
                                <p>Silakan login terlebih dahulu untuk mengakses halaman OnlineSchool.</p>
                            </div>
                            
                            <div class="mt-3">
                                <h5>Actions:</h5>
                                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            <h5>Debug Info:</h5>
                            <ul>
                                <li><strong>Session ID:</strong> {{ session()->getId() }}</li>
                                <li><strong>Current URL:</strong> {{ request()->url() }}</li>
                                <li><strong>Route Name:</strong> {{ request()->route()->getName() ?? 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
