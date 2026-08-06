<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BidangRealisasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\RencanaKinerjaController;
use App\Http\Controllers\UndanganController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export', [RekapController::class, 'export'])->name('rekap.export');

    // Menggantikan 12 file bidang lama - satu controller diparameterisasi oleh {bidang}.
    // Contoh URL: /bidang/kepegawaian, /bidang/kepegawaian/input
    Route::prefix('bidang/{bidang}')->name('bidang.')->group(function () {
        Route::get('/', [BidangRealisasiController::class, 'index'])->name('index');
        Route::get('/input', [BidangRealisasiController::class, 'edit'])->name('edit');
        Route::post('/input', [BidangRealisasiController::class, 'update'])->name('update');
    });

    Route::post('/undangan/{undangan}/hadiri', [UndanganController::class, 'hadiri'])->name('undangan.hadiri');

    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push/subscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    Route::post('/notifikasi/tandai-dibaca', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Hanya Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/kegiatan', [RencanaKinerjaController::class, 'index'])->name('rencana-kinerja.index');
        Route::post('/kegiatan', [RencanaKinerjaController::class, 'store'])->name('rencana-kinerja.store');
        Route::put('/kegiatan/{rencanaKinerja}', [RencanaKinerjaController::class, 'update'])->name('rencana-kinerja.update');
        Route::delete('/kegiatan/{rencanaKinerja}', [RencanaKinerjaController::class, 'destroy'])->name('rencana-kinerja.destroy');

        Route::get('/undangan', [UndanganController::class, 'index'])->name('undangan.index');
        Route::post('/undangan', [UndanganController::class, 'store'])->name('undangan.store');
        Route::put('/undangan/{undangan}', [UndanganController::class, 'update'])->name('undangan.update');
        Route::delete('/undangan/{undangan}', [UndanganController::class, 'destroy'])->name('undangan.destroy');
    });
});
