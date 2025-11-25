<?php

// Controller Bawaan

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
use App\Http\Controllers\PerangkinganController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\WpController;
// --- Untuk Rute Tes ---
use Illuminate\Http\Request;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Rute Publik (Landing Page)
Route::get('/', function () {
    return view('layouts.landing.profile'); // (File landing page kamu)
});


// 2. Rute Autentikasi (login, logout, register, dll)
require __DIR__ . '/auth.php';

// 3. Grup Rute Admin (YANG SUDAH LOGIN)
Route::middleware(['auth', 'verified'])->group(function () {


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {


        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- Master Data (Alur A) ---
        Route::resource('siswa', SiswaController::class);
        Route::resource('kelas', KelasController::class);
        // Menu 1: Perhitungan ROC (Bobot)
        Route::resource('kriteria', KriteriaController::class)->parameters([
            'kriteria' => 'kriteria' // Memaksa parameter tetap bernama 'kriteria', bukan 'kriterium'
        ]);
        Route::post('kriteria/hitung-bobot', [KriteriaController::class, 'hitungBobot'])->name('kriteria.hitung');
        Route::resource('tahun-ajaran', TahunAjaranController::class);
        Route::resource('semester', SemesterController::class);
        Route::resource('akademik', AkademikController::class);
        Route::resource('nonakademik', NonAkademikController::class);

        // --- Proses (Input Data - Alur B) ---
        Route::get('penempatan-kelas', [PenempatanKelasController::class, 'index'])->name('penempatan.index');
        Route::post('penempatan-kelas', [PenempatanKelasController::class, 'store'])->name('penempatan.store');
        Route::delete('penempatan-kelas/{id}', [PenempatanKelasController::class, 'destroy'])->name('penempatan.destroy'); // (Rute destroy yang sebelumnya hilang)

        Route::get('input-nilai', [InputNilaiController::class, 'index'])->name('input-nilai.index');
        Route::post('input-nilai', [InputNilaiController::class, 'store'])->name('input-nilai.store');

        // --- Perankingan & Laporan (Alur C - 4 Menu Baru) ---

        // --- RUTE LAMA (DIHAPUS) ---
        // Route::get('perankingan', [PerangkinganController::class, 'index'])->name('perankingan.index');
        // Route::post('perankingan/hitung', [PerangkinganController::class, 'hitungDanAnalisis'])->name('perankingan.hitung');
        // Route::get('analisis/hasil/{id_semester}', [AnalisisController::class, 'show'])
        //     ->name('analisis.show');
        // -----------------------------


        // --- RUTE BARU (SESUAI 4 MENU) ---

        // Menu 2: Hitung Borda (Beserta Step)
        Route::get('hitung-borda', [BordaController::class, 'index'])->name('borda.index');
        Route::post('hitung-borda', [BordaController::class, 'calculate'])->name('borda.calculate');

        // Menu 3: Hitung WP (Beserta Step)
        Route::get('hitung-wp', [WpController::class, 'index'])->name('wp.index');
        Route::post('hitung-wp', [WpController::class, 'calculate'])->name('wp.calculate');

        // Menu 4: Hitung Manual (BARU)
        Route::get('hitung-manual', [ManualController::class, 'index'])->name('manual.index');
        Route::post('hitung-manual', [ManualController::class, 'calculate'])->name('manual.calculate');

        // Menu 4: Perangkingan (Analisis Total)
        // (Menampilkan perbandingan ranking)
        Route::get('analisis/pemeringkatan', [AnalisisController::class, 'showPemeringkatan'])->name('analisis.pemeringkatan');
        // (Tombol untuk hitung ulang 'Manual')
        Route::post('analisis/hitung-manual', [AnalisisController::class, 'hitungManual'])->name('analisis.hitung.manual');

        // Menu 5: Pengujian (Statistik)
        // (Menampilkan statistik Akurasi & Kecepatan)
        Route::get('analisis/pengujian', [AnalisisController::class, 'showPengujian'])->name('analisis.pengujian');
        // (Tombol untuk hitung ulang 'Spearman')
        Route::post('analisis/hitung-spearman', [AnalisisController::class, 'hitungSpearman'])->name('analisis.hitung.spearman');
    });
});


// 4. Rute Tes (Hanya jalan di local)
if (app()->isLocal()) {
    // Rute /tes-ranking ini sudah TIDAK RELEVAN lagi karena logikanya sudah dipecah.
    // Disarankan untuk dihapus atau di-update agar memanggil salah satu metode hitung baru.
}
