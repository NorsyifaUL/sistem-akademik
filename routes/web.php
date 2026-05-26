<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;

// Admin Controllers
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\MapelController as AdminMapelController;
use App\Http\Controllers\Admin\JadwalController as AdminJadwalController;
use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\NilaiController; 

use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
/*
|--------------------------------------------------------------------------
| 1. HOMEPAGE
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('auth.login'); });

/*
|--------------------------------------------------------------------------
| 2. AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Logout Support
    Route::match(['get', 'post'], '/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    // Dashboard Redirector
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'guru') return redirect()->route('guru.dashboard');
        if ($user->role === 'siswa') return redirect()->route('siswa.dashboard');
        abort(403);
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMIN AREA
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // --- DASHBOARD & PROFIL ---
       Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/siswa/profil', [SiswaController::class, 'profil'])->name('siswa.profil');
        Route::get('/profil', [AdminController::class, 'profil'])->name('profil');
        Route::post('/profil/update', [AdminController::class, 'updateProfil'])->name('profil.update');
        
        // --- MASTER DATA (RESOURCE) ---
        Route::resource('guru', AdminGuruController::class);
        Route::resource('siswa', AdminSiswaController::class);
        Route::resource('mapel', AdminMapelController::class);
        Route::resource('jadwal', AdminJadwalController::class);
        Route::resource('kelas', KelasController::class);

        // --- PASSWORD MANAGEMENT ---
        Route::post('/guru/{guru}/reset-password', [AdminGuruController::class, 'resetPassword'])->name('guru.reset-password');
        Route::post('/siswa/{siswa}/reset-password', [AdminSiswaController::class, 'resetPassword'])->name('siswa.reset-password');

        // --- MANAJEMEN ABSENSI (MONITORING) ---
        // Rute statis diletakkan di atas rute dinamis agar tidak bentrok
        Route::get('/absensi/rekap', [AdminAbsensiController::class, 'rekap'])->name('absensi.rekap');
        Route::get('/absensi/cetak', [AdminAbsensiController::class, 'cetak'])->name('absensi.cetak');
        Route::get('/absensi/{id}/edit', [AdminAbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/absensi/{id}', [AdminAbsensiController::class, 'update'])->name('absensi.update');
        Route::resource('absensi', AdminAbsensiController::class)->only(['index']);
        // Pastikan berada di dalam prefix 'admin' dan name 'admin.'
Route::delete('/absensi/{id}', [App\Http\Controllers\Admin\AbsensiController::class, 'destroy'])->name('absensi.destroy');

        // --- MANAJEMEN NILAI ---
        Route::controller(NilaiController::class)->prefix('nilai')->name('nilai.')->group(function () {
            Route::get('/', 'index')->name('index');                
            Route::get('/detail/{id}', 'show')->name('show');       
            Route::get('/print', 'print')->name('print');           
            Route::get('/raport/{id}', 'cetakRaport')->name('raport'); 
            Route::get('/input/{jadwal_id}', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
        });

        // --- PENGATURAN SISTEM & RAPORT (GABUNGAN) ---
        // Mengelola tabel 'settings' (Tahun Ajaran, Semester, TTD Kepsek, dll)
        Route::controller(AdminSettingController::class)->prefix('settings')->name('settings.')->group(function () {
            Route::get('/', 'index')->name('index');         // Tampilan halaman pengaturan
            Route::put('/update', 'update')->name('update');  // Proses simpan perubahan (menggunakan PUT)
        });

        // --- NOTIFIKASI ---
        Route::controller(NotifikasiController::class)->prefix('notifikasi')->name('notifikasi.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    });
    /*
    |--------------------------------------------------------------------------
    | GURU AREA 
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
        
        // --- Dashboard & Profil ---
        Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
        Route::get('/profil', [GuruController::class, 'profil'])->name('profil');
        Route::post('/profil/update', [GuruController::class, 'updateProfil'])->name('profil.update');

        // --- 1. MENU JADWAL ---
        Route::get('/jadwal', [GuruController::class, 'indexJadwal'])->name('jadwal');
        Route::get('/nilai/siswa/{jadwalId}', [GuruController::class, 'lihatSiswa'])->name('jadwal.siswa');
        Route::get('/jadwal/legger/{id}', [GuruController::class, 'leggerJadwal'])->name('jadwal.legger');

        // --- 2. MENU LAPORAN NILAI ---
        Route::get('/lihat-nilai', [GuruController::class, 'lihatNilaiSiswa'])->name('lihat_nilai'); 
        Route::get('/nilai/input-form/{jadwalId}/{siswaId}', [GuruController::class, 'inputNilaiForm'])->name('nilai.input');
        
        Route::post('/nilai/store-kolektif', [GuruController::class, 'storeKolektif'])->name('nilai.store_kolektif');

        Route::post('/nilai/simpan/{jadwalId}/{siswaId}', [GuruController::class, 'simpanNilai'])->name('nilai.store');
        Route::delete('/nilai/destroy/{id}', [GuruController::class, 'destroyNilai'])->name('nilai.destroy');
        Route::post('/nilai/simpan-massal', [GuruController::class, 'simpanNilaiMassal'])->name('nilai.simpan_massal');
        Route::get('/rekap/{jadwal_id}', [GuruController::class, 'rekapNilai'])->name('rekap.index');
        Route::get('/nilai/cetak-pdf/{jadwal_id}', [GuruController::class, 'cetakPdf'])->name('nilai.cetak_pdf');

        // --- 3. ABSENSI SISWA ---
        Route::prefix('absensi')->name('absensi.')->group(function() {
            Route::get('/', [GuruAbsensiController::class, 'indexGuru'])->name('index'); 
            Route::get('/riwayat', [GuruAbsensiController::class, 'index'])->name('riwayat'); 
            Route::get('/form/{jadwalId}', [GuruAbsensiController::class, 'formAbsensi'])->name('form'); 
            Route::post('/simpan', [GuruAbsensiController::class, 'simpanAbsensi'])->name('simpan'); 
            Route::get('/rekap', [GuruAbsensiController::class, 'rekap'])->name('rekap'); 
            
            // --- ROUTE TAMBAHAN: KIRIM ULANG WA ---
            Route::post('/resend-wa/{id}', [GuruAbsensiController::class, 'resendWa'])->name('resend'); 
            
            Route::resource('manage', GuruAbsensiController::class)->except(['index', 'create', 'store', 'show']);
        });

        // --- 4. RAPORT, SIKAP, ESKUL ---
        Route::get('/raport', [GuruController::class, 'indexRaport'])->name('raport.index');
        Route::get('/raport/cetak/{id}', [GuruController::class, 'cetakRaport'])->name('raport.cetak');

        Route::get('/nilai-sikap/input/{id}', [GuruController::class, 'inputSikap'])->name('nilai_sikap_input');
        Route::get('/nilai-eskul/input/{id}', [GuruController::class, 'inputEskul'])->name('nilai_eskul_input');
        Route::post('/nilai-sikap/store', [GuruController::class, 'storeSikap'])->name('nilai.sikap.store');
        Route::post('/nilai-eskul/store', [GuruController::class, 'storeEskul'])->name('nilai.eskul.store');

    });   
    
   /*
    |--------------------------------------------------------------------------
    | SISWA AREA
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        
        // Menggunakan Route Controller untuk SiswaController
        Route::controller(SiswaController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/absensi', 'absensi')->name('absensi');
            Route::get('/nilai', 'nilai')->name('nilai');
            Route::get('/jadwal', 'jadwal')->name('jadwal');
            Route::get('/notifikasi', 'notifikasi')->name('notifikasi.index');
            
            // Route Profil khusus Siswa
            Route::get('/profil', 'profil')->name('profil');
            Route::post('/profil/update', 'updateProfil')->name('profil.update');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | SHARED AREA (Profile & Password)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {
        // Profile Default Laravel/Breeze
        Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
        });

        // Ganti Password
        Route::controller(App\Http\Controllers\Auth\ChangePasswordController::class)->group(function () {
            Route::get('/ganti-password', 'index')->name('password.form');
            Route::post('/ganti-password', 'update')->name('password.update');
        });
    }); 
});
require __DIR__.'/auth.php';