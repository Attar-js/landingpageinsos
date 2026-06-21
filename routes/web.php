<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TimPenciriController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\LaporanAkhirController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

// Debug Route - Check Login Status
Route::get('/check-login', function() {
    return view('auth.check-login');
})->name('check.login');

// Dashboard Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'home'])->name('home');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Landing page route (Public)
Route::get('/landing', [DashboardController::class, 'onlineSchool'])->name('landing');

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});


//  student-dashboard
Route::prefix('dashboard')->group(function () {
    
    // Nilai CPMK Routes (Mahasiswa)
    Route::group(['prefix' => 'nilai-cpmk'], function() {
        Route::get('/', [App\Http\Controllers\NilaiCpmkController::class, 'index'])->name('nilai-cpmk.index');
        Route::get('download/{nilaiCpmk}', [App\Http\Controllers\NilaiCpmkController::class, 'download'])->name('nilai-cpmk.download');
        Route::get('view/{nilaiCpmk}', [App\Http\Controllers\NilaiCpmkController::class, 'view'])->name('nilai-cpmk.view');
        Route::get('api/my-grades', [App\Http\Controllers\NilaiCpmkController::class, 'getMyGrades'])->name('nilai-cpmk.api.my-grades');
    });
});





// FAQ and Pages Routes
Route::middleware('auth')->prefix('pages')->group(function () {
    Route::get('/faq-tata-cara', [FaqController::class, 'faqTataCara'])->name('faqTataCara');
    Route::get('/proposal-kegiatan', [ProposalController::class, 'index'])->name('proposalkegiatan');
    Route::get('/laporan-akhir', [LaporanAkhirController::class, 'index'])->name('laporanakhir');
});


// KKN Routes
Route::middleware('auth')->prefix('kkn')->group(function () {
    Route::get('/form', [App\Http\Controllers\KknController::class, 'showForm'])->name('konversi');
    Route::post('/store', [App\Http\Controllers\KknController::class, 'store'])->name('kkn.store');
    Route::post('/groups/{group}/drop-self', [App\Http\Controllers\KknController::class, 'dropSelf'])->name('kkn.groups.drop-self');
    Route::post('/groups/{group}/cpmk-rubrik', [App\Http\Controllers\KknController::class, 'storeCpmkRubrik'])->name('kkn.groups.cpmk-rubrik');
    Route::post('/groups/{group}/proposal-reupload', [App\Http\Controllers\KknController::class, 'reuploadProposal'])->name('kkn.groups.proposal-reupload');
    Route::get('/status', [App\Http\Controllers\KknController::class, 'status'])->name('kkn.status');
    Route::get('/detail/{id}', [App\Http\Controllers\KknController::class, 'detail'])->name('kkn.detail');
});

// Legacy redirects (modul lama digabung ke halaman terpusat)
Route::middleware('auth')->group(function () {
    Route::get('/luaran/upload', fn () => redirect('/pages/laporan-akhir'))->name('luaran.upload');
    Route::get('/luaran/status', fn () => redirect('/status-verifikasi'))->name('luaran.status');
    Route::get('/proposal/form', fn () => redirect('/pages/proposal-kegiatan'))->name('proposal.form');
    Route::get('/laporan-akhir/form', fn () => redirect('/pages/laporan-akhir'))->name('laporan-akhir.form');
    Route::get('/pages/form-kesediaan', fn () => redirect('/form-kesediaan'))->name('form-kesediaan');
});

// Proposal Routes
Route::middleware('auth')->prefix('proposal')->group(function () {
    Route::post('/store', [ProposalController::class, 'store'])->name('proposal.store');
});

// Laporan Akhir Routes
Route::middleware('auth')->prefix('laporan-akhir')->group(function () {
    Route::post('/store', [LaporanAkhirController::class, 'store'])->name('laporan-akhir.store');
});

// Peer Review Routes
Route::middleware('auth')->group(function () {
    Route::get('/peer-review', [App\Http\Controllers\PeerReviewController::class, 'showForm'])->name('peer-review.upload');
    Route::post('/peer-review/store', [App\Http\Controllers\PeerReviewController::class, 'store'])->name('peer-review.store');
    Route::get('/peer-review/status', [App\Http\Controllers\PeerReviewController::class, 'status'])->name('peer-review.status');
});

// Configuration Routes
Route::get('/config/info', function() {
    return response()->json(\App\Helpers\DashboardHelper::getConfig());
})->name('config.info');

Route::get('/config-info', function() {
    return view('config-info');
})->name('config.info.page');

Route::middleware('auth')->group(function () {
    Route::get('/form-kesediaan', [App\Http\Controllers\FormKesediaanController::class, 'showForm'])->name('form-kesediaan.upload');
    Route::post('/form-kesediaan/store', [App\Http\Controllers\FormKesediaanController::class, 'store'])->name('form-kesediaan.store');
    Route::get('/form-kesediaan/status', [App\Http\Controllers\FormKesediaanController::class, 'status'])->name('form-kesediaan.status');

    Route::get('/status-verifikasi', [App\Http\Controllers\StatusVerifikasiController::class, 'index'])->name('status-verifikasi');
    
    // Hapus Pengumpulan Routes
    Route::get('/hapus-pengumpulan/{id}/{jenis}', [App\Http\Controllers\UploadUlangController::class, 'hapusPengumpulan'])->name('hapusPengumpulan');
    Route::post('/hapus-pengumpulan/{id}/{jenis}/confirm', [App\Http\Controllers\UploadUlangController::class, 'konfirmasiHapus'])->name('hapusPengumpulan.confirm');
    
    // Upload Ulang Routes
    Route::get('/upload-ulang/{id}/{jenis}', [App\Http\Controllers\UploadUlangController::class, 'showUploadUlang'])->name('uploadUlang');
    Route::post('/upload-ulang/form-konversi', [App\Http\Controllers\UploadUlangController::class, 'uploadUlangFormKonversi'])->name('uploadUlang.formKonversi');
    Route::post('/upload-ulang/{jenis}', [App\Http\Controllers\UploadUlangController::class, 'uploadUlangDokumen'])->name('uploadUlang.dokumen');
    Route::post('/groups/{group}/delete', [App\Http\Controllers\DosenController::class, 'destroyGroup'])->name('groups.delete');
    
    // Dosen Routes
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/mahasiswa-bimbingan', [App\Http\Controllers\DosenController::class, 'mahasiswaBimbingan'])->name('mahasiswa-bimbingan');
        Route::get('/mahasiswa-bimbingan/{group_id}', [App\Http\Controllers\DosenController::class, 'detailMahasiswaBimbingan'])->name('detail-mahasiswa-bimbingan');
        Route::post('/mahasiswa-bimbingan/{group_id}/penilaian-kelompok', [App\Http\Controllers\DosenController::class, 'storePenilaianKelompok'])->name('store-penilaian-kelompok');
        Route::get('/penilaian/{mahasiswa_id}', [App\Http\Controllers\DosenController::class, 'showPenilaian'])->name('penilaian');
        Route::get('/penilaian/{mahasiswa_id}/peer-review-score', [App\Http\Controllers\DosenController::class, 'peerReviewScore'])->name('penilaian.peer-review-score');
        Route::post('/penilaian/{mahasiswa_id}', [App\Http\Controllers\DosenController::class, 'storePenilaian'])->name('store-penilaian');
        Route::get('/validasi-dokumen', [App\Http\Controllers\DosenController::class, 'validasiDokumen'])->name('validasi-dokumen');
        Route::post('/validasi-dokumen/{group}/dokumen/{docType}/approve', [App\Http\Controllers\DosenController::class, 'approveDocumentItem'])->name('validasi-dokumen.dokumen.approve');
        Route::post('/validasi-dokumen/{group}/dokumen/{docType}/reject', [App\Http\Controllers\DosenController::class, 'rejectDocumentItem'])->name('validasi-dokumen.dokumen.reject');
        Route::post('/validasi-dokumen/{group}/dokumen/{docType}/delete', [App\Http\Controllers\DosenController::class, 'deleteDocumentItem'])->name('validasi-dokumen.dokumen.delete');
        Route::get('/validasi-dokumen/{group}/laporan', [App\Http\Controllers\DosenController::class, 'downloadGroupLaporanAkhir'])->name('validasi-dokumen.laporan');
        Route::get('/validasi-dokumen/{group}/artikel', [App\Http\Controllers\DosenController::class, 'downloadGroupArtikel'])->name('validasi-dokumen.artikel');
        Route::get('/validasi-dokumen/{group}/proposal', [App\Http\Controllers\DosenController::class, 'previewValidasiProposal'])->name('validasi-dokumen.proposal');
        Route::post('/validasi-dokumen/{group}/proposal/approve', [App\Http\Controllers\DosenController::class, 'approveValidasiProposal'])->name('validasi-dokumen.proposal.approve');
        Route::post('/validasi-dokumen/{group}/proposal/reject', [App\Http\Controllers\DosenController::class, 'rejectValidasiProposal'])->name('validasi-dokumen.proposal.reject');
        Route::get('/persetujuan-kelompok', [App\Http\Controllers\DosenController::class, 'persetujuanKelompok'])->name('persetujuan-kelompok');
        Route::get('/persetujuan-kelompok/kesediaan-preview', [App\Http\Controllers\DosenController::class, 'previewKesediaanPdf'])->name('persetujuan-kelompok.kesediaan-preview');
        Route::get('/persetujuan-kelompok/{group}/proposal', [App\Http\Controllers\DosenController::class, 'previewGroupProposal'])->name('persetujuan-kelompok.proposal');
        Route::get('/persetujuan-kelompok/{group}/proposal/download', [App\Http\Controllers\DosenController::class, 'downloadGroupProposal'])->name('persetujuan-kelompok.proposal-download');
        Route::post('/persetujuan-kelompok/{group}/proposal/approve', [App\Http\Controllers\DosenController::class, 'approveGroupProposal'])->name('persetujuan-kelompok.proposal.approve');
        Route::post('/persetujuan-kelompok/{group}/proposal/reject', [App\Http\Controllers\DosenController::class, 'rejectGroupProposal'])->name('persetujuan-kelompok.proposal.reject');
        Route::post('/persetujuan-kelompok/{request}/approve', [App\Http\Controllers\DosenController::class, 'approveSupervisorRequest'])->name('persetujuan-kelompok.approve');
        Route::post('/persetujuan-kelompok/{request}/reject', [App\Http\Controllers\DosenController::class, 'rejectSupervisorRequest'])->name('persetujuan-kelompok.reject');
        Route::post('/groups/{group}/re-add', [App\Http\Controllers\DosenController::class, 'reAddDroppedMember'])->name('groups.re-add');
        Route::post('/groups/{group}/assign-leader', [App\Http\Controllers\DosenController::class, 'assignLeader'])->name('groups.assign-leader');
    });

    Route::middleware('role:tim_penciri')->prefix('tim-penciri')->name('tim-penciri.')->group(function () {
        Route::get('/dashboard', [TimPenciriController::class, 'dashboard'])->name('dashboard');
        Route::get('/kesediaan-proposal', [TimPenciriController::class, 'kesediaanProposal'])->name('kesediaan-proposal');
        Route::get('/kesediaan', [TimPenciriController::class, 'kesediaan'])->name('kesediaan');
        Route::get('/proposal', [TimPenciriController::class, 'proposal'])->name('proposal');
        Route::get('/laporan-luaran', [TimPenciriController::class, 'laporanLuaran'])->name('laporan-luaran');
        Route::post('/verify', [TimPenciriController::class, 'verify'])->name('verify');
        Route::get('/detail/{id}', [TimPenciriController::class, 'detail'])->name('detail');
        Route::post('/destroy/{id}', [TimPenciriController::class, 'destroy'])->name('destroy');
        Route::get('/rubrik-cpmk', [TimPenciriController::class, 'rubrikCpmk'])->name('rubrik-cpmk');
        Route::get('/rubrik-cpmk/{group}', [TimPenciriController::class, 'rubrikCpmkEdit'])->name('rubrik-cpmk.edit');
        Route::post('/rubrik-cpmk/{group}/skor', [TimPenciriController::class, 'storeCpmkRubrikSkor'])->name('groups.cpmk-rubrik-skor');
    });
    
    // Template Routes
    Route::get('/template', [App\Http\Controllers\TemplateDokumenController::class, 'index'])->name('template.index');
    Route::get('/template/download/{key}', [App\Http\Controllers\TemplateDokumenController::class, 'download'])->name('template.download');

    Route::get('/template/proposal', fn () => redirect(url('/template#template-proposal')))->name('template.proposal');
    Route::get('/template/laporan', fn () => redirect(url('/template#template-laporan')))->name('template.laporan');
    Route::get('/template/logbook', fn () => redirect(url('/template#template-logbook')))->name('template.logbook');
    Route::get('/template/cpmk', fn () => redirect(url('/template#template-cpmk')))->name('template.cpmk');
    
    // Kontak Admin Route
    Route::get('/kontak-admin', function() {
        return view('kontak.admin');
    })->name('kontak.admin');
    
    // Download Panduan Route
    Route::get('/download-panduan', function() {
        $filePath = public_path('assets/documents/panduan-inovasi-sosial.pdf');
        
        if (file_exists($filePath)) {
            return response()->download($filePath, 'Panduan-Inovasi-Sosial-ITK.pdf');
        } else {
            return redirect()->back()->with('error', 'File panduan tidak ditemukan.');
        }
    })->name('download.panduan');
    
    // File Download Routes
    Route::get('/file/download/{jenisDokumen}/{fileName}', [App\Http\Controllers\FileDownloadController::class, 'downloadFile'])->name('file.download');
    Route::get('/file/view/{jenisDokumen}/{fileName}', [App\Http\Controllers\FileDownloadController::class, 'viewFile'])->name('file.view');
    
    // File Download Routes untuk Dosen
    Route::get('/file/download/{jenisDokumen}/{fileName}/{studentNim}', [App\Http\Controllers\FileDownloadController::class, 'downloadFileForDosen'])->name('file.download.dosen');
    Route::get('/file/view/{jenisDokumen}/{fileName}/{studentNim}', [App\Http\Controllers\FileDownloadController::class, 'viewFileForDosen'])->name('file.view.dosen');
});
