<?php

use Endroid\QrCode\Logo\Logo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BsreController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SK\SKController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MigrateUserController;
use App\Http\Controllers\MutasiPendingController;
use App\Http\Controllers\MutasiOnProcessController;
use App\Http\Controllers\MutasiVerifikasiController;
use App\Http\Controllers\Tugas_Tambahan\TugasController;
use App\Http\Controllers\Tugas_Tambahan\TugasPendingController;
use App\Http\Controllers\Tugas_Tambahan\TugasOnProcessController;
use App\Http\Controllers\Tugas_Tambahan\TugasVerifikasiController;

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
// Route::get('/qr', function () {
//     return view('qr-index');
// });

// Auth::loginUsingId('000662');

// Route::get('/migrate-users', MigrateUserController::class);

Route::get('/', function () {
    // admin dashboard
    return redirect()->route('admin.dashboard.index');
});

Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// bungkus login dengan middleware guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login/process', [LoginController::class, 'login'])->name('login.process');
});

Route::get('/check-status-bsre', [BsreController::class, 'checkStatus'])->name('check-status-bsre');
Route::get('/sign-pdf', [BsreController::class, 'signPdf'])->name('sign-pdf');

// logout
Route::post('/logout', LogoutController::class)->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::name('dashboard.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('index');
    });

    // bungkus route role dan permission didalam user-management agar saya dapat mengatur menu yang akan di tampilkan
    Route::name('user-management.')->group(function () {
        Route::name('roles.')->group(function () {
            Route::get('/user-management/roles', [RoleController::class, 'index'])->name('index');
            Route::post('/user-management/roles/store', [RoleController::class, 'store'])->name('store');
            Route::get('/user-management/roles/show/{id}', [RoleController::class, 'show'])->name('show');
            Route::get('/user-management/roles/edit/{id}', [RoleController::class, 'edit'])->name('edit');
            Route::patch('/user-management/roles/update/{id}', [RoleController::class, 'update'])->name('update');
        });

        Route::name('permissions.')->group(function () {
            Route::get('/user-management/permissions', [PermissionController::class, 'index'])->name('index');
            Route::post('/user-management/permissions/store', [PermissionController::class, 'store'])->name('store');
        });
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
        Route::get('/sk-kontrak/print-perjanjian-kerja/{urut}/{tahun}', [SKController::class, 'printPerjanjianKerja'])->name('print-perjanjian-kerja');
    });

    Route::name('mutasi.')->group(function () {
        Route::get('/mutasi', [MutasiController::class, 'index'])->name('index');
        Route::get('/mutasi/tugas-tambahan', [MutasiController::class, 'tugasTambahan'])->name('tugas-tambahan');
        Route::post('/mutasi/store', [MutasiController::class, 'store'])->name('store');
        Route::post('/mutasi/store-mutasi-nota', [MutasiController::class, 'storeMutasiNota'])->name('store-mutasi-nota');
        Route::get('/mutasi/{id}/{jenis_mutasi}/edit-mutasi-nota-on-pending', [MutasiController::class, 'editMutasiOnPending'])->name('edit-mutasi-nota-on-pending');
        Route::patch('/mutasi/{id}/{jenis_mutasi}/update-mutasi-nota-on-pending', [MutasiController::class, 'updateMutasiOnPending'])->name('update-mutasi-nota-on-pending');
        Route::get('/mutasi/{id}/{jenis_mutasi}/edit-mutasi-nota-on-process', [MutasiController::class, 'editMutasiOnProcess'])->name('edit-mutasi-nota-on-process');
        Route::patch('/mutasi/{id}/{jenis_mutasi}/update-mutasi-nota-on-process', [MutasiController::class, 'updateMutasiOnProcess'])->name('update-mutasi-nota-on-process');
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
        Route::get('/mutasi-on-process/get-log-mutasi', [MutasiOnProcessController::class, 'getLogMutasi'])->name('get-log-mutasi');

        // public function printDraftSk($kd_karyawan, $kd_mutasi, $kd_jenis_mutasi)
        Route::get('/mutasi-on-process/print-draft-sk/{kd_karyawan}/{kd_mutasi}/{kd_jenis_mutasi}', [MutasiOnProcessController::class, 'printDraftSk'])->name('print-draft-sk');
    });

    Route::name('mutasi-pending.')->group(function () {
        Route::get('/mutasi-pending', [MutasiPendingController::class, 'index'])->name('index');
    });

    Route::name('mutasi-verifikasi.')->group(function () {
        Route::get('/mutasi-verifikasi', [MutasiVerifikasiController::class, 'index'])->name('index');
    });

    // tugas tambahan
    Route::name('old-tugas-tambahan.')->group(function () {
        Route::get('/tugas-tambahan', [TugasController::class, 'index'])->name('index');
        Route::post('/tugas-tambahan/store', [TugasController::class, 'store'])->name('store');
        Route::post('/tugas-tambahan/store-tugas-tambahan', [TugasController::class, 'storeTugasTambahan'])->name('store-tugas-tambahan');
        Route::get('/tugas-tambahan/{id}/edit-tugas-tambahan-on-pending', [TugasController::class, 'editTugasOnPending'])->name('edit-tugas-tambahan-on-pending');
        Route::patch('/tugas-tambahan/{id}/update-tugas-tambahan-on-pending', [TugasController::class, 'updateTugasOnPending'])->name('update-tugas-tambahan-on-pending');
        Route::get('/tugas-tambahan/{id}/edit-tugas-tambahan-on-process', [TugasController::class, 'editTugasOnProcess'])->name('edit-tugas-tambahan-on-process');
        Route::patch('/tugas-tambahan/{id}/update-tugas-tambahan-on-process', [TugasController::class, 'updateTugasOnProcess'])->name('update-tugas-tambahan-on-process');
        Route::delete('/tugas-tambahan/delete-tugas-tambahan/{id}', [TugasController::class, 'deleteTugasTambahan'])->name('delete-tugas-tambahan');
        Route::get('/tugas-tambahan/check-pegawai/{id}', [TugasController::class, 'checkPegawai'])->name('check-pegawai');
        Route::get('/tugas-tambahan/list-tugas-tambahan/{id}', [TugasController::class, 'listTugasTambahan'])->name('list-tugas-tambahan');
        Route::get('/tugas-tambahan/get-unit-kerja/{id}', [TugasController::class, 'getUnitKerja'])->name('get-unit-kerja');
        Route::get('/tugas-tambahan/get-sub-unit-kerja/{id}/{divisi}', [TugasController::class, 'getSubUnitKerja'])->name('get-sub-unit-kerja');
    });

    // tugas tambahan on process
    Route::name('old-tugas-tambahan-on-process.')->group(function () {
        Route::get('/tugas-tambahan-on-process', [TugasOnProcessController::class, 'index'])->name('index');
        Route::post('/tugas-tambahan-on-process/first-verification', [TugasOnProcessController::class, 'firstVerification'])->name('first-verification');
        Route::post('/tugas-tambahan-on-process/second-verification', [TugasOnProcessController::class, 'secondVerification'])->name('second-verification');
        Route::post('/tugas-tambahan-on-process/third-verification', [TugasOnProcessController::class, 'thirdVerification'])->name('third-verification');
        Route::post('/tugas-tambahan-on-process/fourth-verification', [TugasOnProcessController::class, 'fourthVerification'])->name('fourth-verification');
        Route::post('/tugas-tambahan-on-process/finalisasi', [TugasOnProcessController::class, 'finalisasi'])->name('finalisasi');
        Route::get('/tugas-tambahan-on-process/rincian', [TugasOnProcessController::class, 'rincian'])->name('rincian');
        Route::get('/tugas-tambahan-on-process/get-log-tugas-tambahan', [TugasOnProcessController::class, 'getLogTugasTambahan'])->name('get-log-tugas-tambahan');

        // public function printDraftSk($kd_karyawan, $kd_tugas_tambahan)
        Route::get('/tugas-tambahan-on-process/print-draft-sk/{kd_karyawan}/{kd_tugas_tambahan}', [TugasOnProcessController::class, 'printDraftSk'])->name('print-draft-sk');
    });

    // tugas tambahan pending
    Route::name('old-tugas-tambahan-pending.')->group(function () {
        Route::get('/tugas-tambahan-pending', [TugasPendingController::class, 'index'])->name('index');
    });

    // verfikasi
    Route::name('tugas-tambahan-verifikasi.')->group(function () {
        Route::get('/tugas-tambahan-verifikasi', [TugasVerifikasiController::class, 'index'])->name('index');
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
