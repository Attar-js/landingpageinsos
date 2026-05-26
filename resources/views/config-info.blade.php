<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Info - Project Akhir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4><i class="fas fa-cog me-2"></i>Project Akhir Configuration</h4>
                        <p class="mb-0">Configuration using APP_DASHBOARD_URL (Port 8000)</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-info-circle me-2"></i>Environment Variables</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>APP_DASHBOARD_URL:</strong></label>
                                            <input type="text" class="form-control" value="{{ config('app.dashboard_url') }}" readonly>
                                            <small class="text-muted">URL untuk dashboard dashboard (Port 8001)</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label"><strong>APP_LANDING_URL:</strong></label>
                                            <input type="text" class="form-control" value="{{ config('app.landing_url') }}" readonly>
                                            <small class="text-muted">URL untuk landing page project-akhir (Port 8000)</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label"><strong>APP_URL:</strong></label>
                                            <input type="text" class="form-control" value="{{ config('app.url') }}" readonly>
                                            <small class="text-muted">URL dasar project-akhir (Port 8000)</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label"><strong>APP_NAME:</strong></label>
                                            <input type="text" class="form-control" value="{{ config('app.name') }}" readonly>
                                            <small class="text-muted">Nama aplikasi</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-link me-2"></i>Application Links</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ \App\Helpers\DashboardHelper::getCurrentAppUrl() }}" class="btn btn-success" target="_blank">
                                                <i class="fas fa-home me-2"></i>Project Akhir Home (Port 8000)
                                            </a>
                                            
                                            <a href="{{ \App\Helpers\DashboardHelper::getFormUrl() }}" class="btn btn-primary" target="_blank">
                                                <i class="fas fa-file-alt me-2"></i>KKN Form (Port 8000)
                                            </a>
                                            
                                            <a href="{{ \App\Helpers\DashboardHelper::getDashboardUrl() }}" class="btn btn-warning" target="_blank">
                                                <i class="fas fa-tachometer-alt me-2"></i>Hope UI Dashboard (Port 8001)
                                            </a>
                                            
                                            <a href="{{ \App\Helpers\DashboardHelper::getPendaftarUrl() }}" class="btn btn-info" target="_blank">
                                                <i class="fas fa-users me-2"></i>Pendaftar Page (Port 8001)
                                            </a>
                                            
                                            <button class="btn btn-secondary" onclick="getConfig()">
                                                <i class="fas fa-cog me-2"></i>Get Config
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-code me-2"></i>Configuration Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="configDetails">
                                            <p class="text-muted">Click "Get Config" to see configuration details</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-book me-2"></i>Port Configuration</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Project Akhir (.env):</h6>
                                                <pre><code>APP_URL=http://localhost:8000
APP_DASHBOARD_URL=http://localhost:8001
APP_NAME="Project Akhir - KKN Form"</code></pre>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Hope UI (.env):</h6>
                                                <pre><code>APP_URL=http://localhost:8001
APP_DASHBOARD_URL=http://localhost:8001
APP_NAME="Hope UI Dashboard"</code></pre>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-info mt-3">
                                            <h6><i class="fas fa-info-circle me-2"></i>Port Configuration Summary:</h6>
                                            <ul class="mb-0">
                                                <li><strong>Project Akhir:</strong> Port 8000 (Form Input)</li>
                                                <li><strong>Hope UI:</strong> Port 8001 (Dashboard Admin)</li>
                                                <li><strong>Database:</strong> Shared (dashboardta)</li>
                                                <li><strong>Integration:</strong> API + Shared Storage</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function getConfig() {
            $('#configDetails').html(`
                <div class="text-center">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2">Loading configuration...</p>
                </div>
            `);
            
            $.get('/config/info')
                .done(function(data) {
                    let html = '<div class="alert alert-success">';
                    html += '<h6>Project Akhir Configuration:</h6>';
                    html += '<ul>';
                    html += '<li><strong>Current App URL:</strong> ' + data.current_app_url + '</li>';
                    html += '<li><strong>Dashboard URL:</strong> ' + data.dashboard_url + '</li>';
                    html += '<li><strong>Form URL:</strong> ' + data.form_url + '</li>';
                    html += '<li><strong>Pendaftar URL:</strong> ' + data.pendaftar_url + '</li>';
                    html += '<li><strong>File Manager URL:</strong> ' + data.file_manager_url + '</li>';
                    html += '<li><strong>App Name:</strong> ' + data.app_name + '</li>';
                    html += '<li><strong>Hope UI URL:</strong> ' + data.dashboard_url + '</li>';
                    html += '</ul>';
                    html += '</div>';
                    
                    html += '<div class="mt-3"><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
                    
                    $('#configDetails').html(html);
                })
                .fail(function(xhr) {
                    $('#configDetails').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Failed to get configuration: ${xhr.responseText}
                        </div>
                    `);
                });
        }
    </script>
</body>
</html> 

