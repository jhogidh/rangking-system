<?php

// Controller Bawaan

use App\Http\Controllers\LaporanGabunganController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// --- Controller-controller Admin ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NonAkademikController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\BordaController;
use App\Http\Controllers\InputNilaiController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\PenempatanKelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StatusInputController;
use App\Http\Controllers\WpController;
// --- Untuk Rute Tes ---
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        // Redirect sesuai role
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('proses.dashboard'); // Guru ke dashboard proses
        }
    }
    return view('layouts.landing.profile');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ====================================================
    // GRUP KHUSUS ADMIN (Middleware: role:admin)
    // ====================================================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- SPK ---
        Route::resource('kriteria', KriteriaController::class)->parameters(['kriteria' => 'kriteria']);
        Route::post('kriteria/hitung-roc', [KriteriaController::class, 'hitungBobot'])->name('kriteria.hitung-roc');
        Route::post('kriteria/update-prioritas', [KriteriaController::class, 'updatePrioritas'])->name('kriteria.update-prioritas');

        Route::get('hitung-manual', [ManualController::class, 'index'])->name('manual.index');
        Route::post('hitung-manual', [ManualController::class, 'calculate'])->name('manual.calculate');

        Route::get('hitung-borda', [BordaController::class, 'index'])->name('borda.index');
        Route::post('hitung-borda', [BordaController::class, 'calculate'])->name('borda.calculate');

        Route::get('hitung-wp', [WpController::class, 'index'])->name('wp.index');
        Route::post('hitung-wp', [WpController::class, 'calculate'])->name('wp.calculate');

        Route::get('analisis/pemeringkatan', [AnalisisController::class, 'showPemeringkatan'])->name('analisis.pemeringkatan');
        Route::get('analisis/pengujian', [AnalisisController::class, 'showPengujian'])->name('analisis.pengujian');
        Route::post('analisis/hitung-spearman', [AnalisisController::class, 'hitungSpearman'])->name('analisis.hitung.spearman');
        Route::get('analisis/gabungan', [LaporanGabunganController::class, 'index'])->name('analisis.gabungan');

        Route::get('status-input', [StatusInputController::class, 'index'])->name('status-input.index');
    });


    // ====================================================
    // GRUP KHUSUS GURU - Prefix: 'proses'
    // ====================================================
    // Kita gunakan prefix 'proses' agar URL-nya netral (misal: /proses/input-nilai)
    // Tidak perlu middleware role khusus, karena 'auth' sudah cukup (Admin & Guru sama-sama user login)

    Route::prefix('proses')->name('proses.')->group(function () {

        // Dashboard untuk Guru (Admin juga bisa akses tapi biasanya punya dashboard sendiri)
        Route::get('/dashboard', function () {
            return view('layouts.admin.contents.dashboard'); // Bisa pakai view dashboard yang sama atau beda
        })->name('dashboard');


        // --- Master Data ---
        Route::resource('siswa', SiswaController::class);
        Route::resource('kelas', KelasController::class);
        Route::get('kriteria', [KriteriaController::class, 'indexGuru'])->name('kriteriaguru.index');
        Route::resource('semester', SemesterController::class);
        Route::resource('akademik', AkademikController::class);
        Route::resource('nonakademik', NonAkademikController::class);

        // Penempatan Kelas
        Route::get('penempatan-kelas', [PenempatanKelasController::class, 'index'])->name('penempatan.index');
        Route::post('penempatan-kelas', [PenempatanKelasController::class, 'store'])->name('penempatan.store');
        Route::delete('penempatan-kelas/{id}', [PenempatanKelasController::class, 'destroy'])->name('penempatan.destroy');

        // Import Nilai
        Route::get('input-nilai', [InputNilaiController::class, 'index'])->name('input-nilai.index');
        Route::post('input-nilai', [InputNilaiController::class, 'store'])->name('input-nilai.store');
    });
});
