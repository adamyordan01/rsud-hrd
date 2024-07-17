<?php

use Endroid\QrCode\Logo\Logo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRController;
use App\Http\Controllers\BsreController;
use App\Http\Controllers\SK\SKController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MutasiPendingController;
use App\Http\Controllers\MutasiOnProcessController;
use App\Http\Controllers\MutasiVerifikasiController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/qr', function () {
    return view('qr-index');
});

// Auth::loginUsingId('000662');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// login
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login/process', [LoginController::class, 'login'])->name('login.process');

Route::get('/check-status-bsre', [BsreController::class, 'checkStatus'])->name('check-status-bsre');
Route::get('/sign-pdf', [BsreController::class, 'signPdf'])->name('sign-pdf');

// logout
Route::post('/logout', LogoutController::class)->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::name('dashboard.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('index');
    });

    Route::name('karyawan.')->group(function () {
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('index');
        Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('create');
        Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('store');
        Route::get('karyawan/jurusan/{id}', [KaryawanController::class, 'getJurusan'])->name('jurusan');
        Route::get('karyawan/edit/{id}', [KaryawanController::class, 'edit'])->name('edit');
        Route::patch('karyawan/update/{id}', [KaryawanController::class, 'update'])->name('update');
    });

    Route::name('sk-kontrak.')->group(function () {
        Route::get('/sk-kontrak',[SKController::class, 'index'])->name('index');
        Route::post('/sk-kontrak/store', [SKController::class, 'store'])->name('store');
        Route::post('/sk-kontrak/first-verification', [SKController::class, 'firstVerification'])->name('first-verification');
        Route::post('/sk-kontrak/second-verification', [SKController::class, 'secondVerification'])->name('second-verification');
        Route::post('/sk-kontrak/third-verification', [SKController::class, 'thirdVerification'])->name('third-verification');
        Route::post('/sk-kontrak/fourth-verification', [SKController::class, 'fourthVerification'])->name('fourth-verification');
        Route::post('/sk-kontrak/finalisasi', [SKController::class, 'finalisasi'])->name('finalisasi');
        Route::get('/sk-kontrak/get-karyawan', [SKController::class, 'getKaryawan'])->name('get-karyawan');
        Route::get('/sk-kontrak/rincian-karyawan', [SKController::class, 'rincianKaryawan'])->name('rincian-karyawan');
        Route::get('/sk-kontrak/verifikasi-karyawan', [SKController::class, 'verifikasiKaryawan'])->name('verifikasi-karyawan');
    });

    Route::name('mutasi.')->group(function () {
        Route::get('/mutasi', [MutasiController::class, 'index'])->name('index');
        Route::post('/mutasi/store', [MutasiController::class, 'store'])->name('store');
        Route::post('/mutasi/store-mutasi-nota', [MutasiController::class, 'storeMutasiNota'])->name('store-mutasi-nota');
        Route::delete('/mutasi/delete-mutasi-nota/{id}', [MutasiController::class, 'deleteMutasiNota'])->name('delete-mutasi-nota');
        Route::get('/mutasi/check-pegawai/{id}', [MutasiController::class, 'checkPegawai'])->name('check-pegawai');
        Route::get('/mutasi/list-mutasi-nota/{id}', [MutasiController::class, 'listMutasiNota'])->name('list-mutasi-nota');
        Route::get('/mutasi/get-unit-kerja/{id}', [MutasiController::class, 'getUnitKerja'])->name('get-unit-kerja');
        Route::get('/mutasi/get-sub-unit-kerja/{id}/{divisi}', [MutasiController::class, 'getSubUnitKerja'])->name('get-sub-unit-kerja');
    });

    Route::name('mutasi-on-process.')->group(function () {
        Route::get('/mutasi-on-process', [MutasiOnProcessController::class, 'index'])->name('index');
        Route::post('/mutasi-on-process/first-verification', [MutasiOnProcessController::class, 'firstVerification'])->name('first-verification');
        Route::post('/mutasi-on-process/second-verification', [MutasiOnProcessController::class, 'secondVerification'])->name('second-verification');
        Route::post('/mutasi-on-process/third-verification', [MutasiOnProcessController::class, 'thirdVerification'])->name('third-verification');
        Route::post('/mutasi-on-process/fourth-verification', [MutasiOnProcessController::class, 'fourthVerification'])->name('fourth-verification');
        Route::post('/mutasi-on-process/finalisasi', [MutasiOnProcessController::class, 'finalisasi'])->name('finalisasi');
        Route::get('/mutasi-on-process/rincian', [MutasiOnProcessController::class, 'rincian'])->name('rincian');
    });

    Route::name('mutasi-pending.')->group(function () {
        Route::get('/mutasi-pending', [MutasiPendingController::class, 'index'])->name('index');
    });

    Route::name('mutasi-verifikasi.')->group(function () {
        Route::get('/mutasi-verifikasi', [MutasiVerifikasiController::class, 'index'])->name('index');
    });
});


Route::get('/lokasi/kabupaten/{id}', [LokasiController::class, 'getKabupaten'])->name('lokasi.kabupaten');
Route::get('/lokasi/kecamatan/{id}', [LokasiController::class, 'getKecamatan'])->name('lokasi.kecamatan');
Route::get('/lokasi/kelurahan/{id}', [LokasiController::class, 'getKelurahan'])->name('lokasi.kelurahan');

// buat route untuk melakukan php artisan storage:link
Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created';
});
