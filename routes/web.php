<?php

use Endroid\QrCode\Logo\Logo;
use App\Models\SuratIzinPraktik;
use App\Models\SuratTandaRegistrasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BsreController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SK\SKController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MigrateUserController;
use App\Http\Controllers\Identitas\CvController;
use App\Http\Controllers\ChangeProfileController;
use App\Http\Controllers\MutasiPendingController;
use App\Http\Controllers\MutasiOnProcessController;
use App\Http\Controllers\MutasiVerifikasiController;
use App\Http\Controllers\Settings\RuanganController;
use App\Http\Controllers\SuratIzinPraktikController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Riwayat\BpjsKetenagakerjaanController;
use App\Http\Controllers\Riwayat\KeluargaController;
use App\Http\Controllers\Riwayat\KemampuanBahasaController;
use App\Http\Controllers\Settings\BahasaController;
use App\Http\Controllers\Settings\HubunganKeluargaController;
use App\Http\Controllers\SuratTandaRegistrasiController;
use App\Http\Controllers\Settings\JenjangPendidikanController;
use App\Http\Controllers\Settings\JurusanController;
use App\Http\Controllers\Settings\PekerjaanController;
use App\Http\Controllers\Tugas_Tambahan\TugasTambahanController;

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

// fungsi untuk clear config
Route::get('/config-cache', function () {
    Artisan::call('config:clear');
    return 'Config cache is cleared';
});

// buat fungsi untuk clear cache
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return 'Cache is cleared';
});

// buat fungsi untuk optimize clear
Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    return 'Optimize clear';
});

// buat fungsi untuk composer dump autoload
Route::get('/composer-dump-autoload', function () {
    Artisan::call('composer dump-autoload');
    return 'Composer dump autoload';
});

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

    // Forgot password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('forgot-password');
    Route::post('/forgot-password/send-email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forgot-password.send-email');

    // Reset password
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Route untuk menampilkan detail karyawan yang di scan dari barcode kartu identitas KaryawanController::class showPersonal
    Route::get('/show-personal/{id}', [KaryawanController::class, 'showPersonal'])->name('show-personal');
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
    Route::middleware(['permission:view_user_management'])
        ->name('user-management.')
        ->group(function () {
        Route::name('roles.')->group(function () {
            Route::get('/user-management/roles', [RoleController::class, 'index'])->name('index');
            Route::post('/user-management/roles/store', [RoleController::class, 'store'])->name('store');
            Route::get('/user-management/roles/show/{id}', [RoleController::class, 'show'])->name('show');
            Route::get('/user-management/roles/edit/{id}', [RoleController::class, 'edit'])->name('edit');
            Route::put('/user-management/roles/update/{id}', [RoleController::class, 'update'])->name('update');
        });

        Route::name('permissions.')->group(function () {
            Route::get('/user-management/permissions', [PermissionController::class, 'index'])->name('index');
            Route::post('/user-management/permissions/store', [PermissionController::class, 'store'])->name('store');
            Route::get('/user-management/permissions/show/{id}', [PermissionController::class, 'show'])->name('show');
            Route::get('/user-management/permissions/edit/{id}', [PermissionController::class, 'edit'])->name('edit');
            Route::put('/user-management/permissions/update/{id}', [PermissionController::class, 'update'])->name('update');
        });

        Route::name('users.')->group(function () {
            Route::get('/user-management/users', [UserController::class, 'index'])->name('index');
            Route::get('/user-management/users/show/{id}', [UserController::class, 'show'])->name('show');
            Route::post('/user-management/users/assign-role/{id}', [UserController::class, 'assignRole'])->name('assign-role');
        });
    });

    // Settings/JenjangPendidikan
    Route::prefix('settings/jenjang-pendidikan')
        ->name('settings.')
        ->group(function () {
        Route::name('jenjang-pendidikan.')->group(function () {
            Route::get('/', [JenjangPendidikanController::class, 'index'])->name('index');
            Route::post('/store', [JenjangPendidikanController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [JenjangPendidikanController::class, 'edit'])->name('edit');
            Route::patch('/{id}', [JenjangPendidikanController::class, 'update'])->name('update');
            Route::delete('/{id}', [JenjangPendidikanController::class, 'destroy'])->name('destroy');
            Route::post('update-order', [JenjangPendidikanController::class, 'updateOrder'])->name('update-order');
        });

        // Ruangan
        Route::name('ruangan.')->group(function () {
            Route::get('/ruangan', [RuanganController::class, 'index'])->name('index');
            Route::post('/ruangan/store', [RuanganController::class, 'store'])->name('store');
            Route::get('/ruangan/{id}/edit', [RuanganController::class, 'edit'])->name('edit');
            Route::patch('/ruangan/{id}', [RuanganController::class, 'update'])->name('update');
        });

        // Pekerjaan
        Route::name('pekerjaan.')->group(function () {
            Route::get('/pekerjaan', [PekerjaanController::class, 'index'])->name('index');
            Route::post('/pekerjaan/store', [PekerjaanController::class, 'store'])->name('store');
            Route::get('/pekerjaan/{id}/edit', [PekerjaanController::class, 'edit'])->name('edit');
            Route::patch('/pekerjaan/{id}', [PekerjaanController::class, 'update'])->name('update');
        });

        // Pekerjaan
        Route::name('hubungan_keluarga.')->group(function () {
            Route::get('/hubungan_keluarga', [HubunganKeluargaController::class, 'index'])->name('index');
            Route::post('/hubungan_keluarga/store', [HubunganKeluargaController::class, 'store'])->name('store');
            Route::get('/hubungan_keluarga/{id}/edit', [HubunganKeluargaController::class, 'edit'])->name('edit');
            Route::patch('/hubungan_keluarga/{id}', [HubunganKeluargaController::class, 'update'])->name('update');
        });

        // Jurusan
        Route::name('jurusan.')->group(function () {
            Route::get('/jurusan', [JurusanController::class, 'index'])->name('index');
            Route::post('/jurusan/store', [JurusanController::class, 'store'])->name('store');
            Route::get('/jurusan/{id}/edit', [JurusanController::class, 'edit'])->name('edit');
            Route::patch('/jurusan/{id}', [JurusanController::class, 'update'])->name('update');
        });

        // Bahasa
        Route::name('bahasa.')->group(function () {
            Route::get('/bahasa', [BahasaController::class, 'index'])->name('index');
            Route::post('/bahasa/store', [BahasaController::class, 'store'])->name('store');
            Route::get('/bahasa/{id}/edit', [BahasaController::class, 'edit'])->name('edit');
            Route::patch('/bahasa/{id}', [BahasaController::class, 'update'])->name('update');
        });
    });

    Route::middleware(['permission:view_karyawan'])
        ->name('karyawan.')
        ->group(function () {
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('index');
        Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('create');
        Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('store');
        Route::get('/karyawan/show/{id}', [KaryawanController::class, 'show'])->name('show');
        Route::get('karyawan/jurusan/{id}', [KaryawanController::class, 'getJurusan'])->name('jurusan');
        Route::get('karyawan/edit/{id}', [KaryawanController::class, 'edit'])->name('edit');
        Route::patch('karyawan/update/{id}', [KaryawanController::class, 'update'])->name('update');

        // printIdCard
        Route::get('karyawan/identitas/print-id-card/{id}', [KaryawanController::class, 'printIdCard'])->name('print-id-card');
        Route::post('karyawan/generate-print-token', [KaryawanController::class, 'generatePrintToken'])->name('generate-print-token');
        Route::post('karyawan/upload-photo/{id}', [KaryawanController::class, 'uploadPhoto'])->name('upload-photo');

        // cv karyawan/identitas/cv/{id}
        Route::get('karyawan/identitas/cv/{id}', [CvController::class, 'show'])->name('cv');
        // print cv
        Route::get('karyawan/identitas/print-cv/{id}', [CvController::class, 'print'])->name('print-cv');

        // STR
        Route::get('karyawan/str/{id}', [SuratTandaRegistrasiController::class, 'index'])->name('str.index');
        // public function getStrData($id)
        Route::get('karyawan/str/get-str-data/{id}', [SuratTandaRegistrasiController::class, 'getStrData'])->name('str.get-str-data');
        Route::post('karyawan/str/store/{id}', [SuratTandaRegistrasiController::class, 'store'])->name('str.store');
        Route::get('karyawan/str/edit/{id}/{urut}', [SuratTandaRegistrasiController::class, 'edit'])->name('str.edit');
        Route::patch('karyawan/str/update/{id}/{urut}', [SuratTandaRegistrasiController::class, 'update'])->name('str.update');

        // SIP
        Route::get('karyawan/sip/{id}', [SuratIzinPraktikController::class, 'index'])->name('sip.index');
        Route::get('karyawan/sip/get-sip-data/{id}', [SuratIzinPraktikController::class, 'getSipData'])->name('sip.get-sip-data');
        Route::post('karyawan/sip/store/{id}', [SuratIzinPraktikController::class, 'store'])->name('sip.store');
        Route::get('karyawan/sip/edit/{id}/{urut}', [SuratIzinPraktikController::class, 'edit'])->name('sip.edit');
        Route::patch('karyawan/sip/update/{id}/{urut}', [SuratIzinPraktikController::class, 'update'])->name('sip.update');

        // BPJS Ketenagakerjaan
        Route::get('karyawan/bpjs-ketenagakerjaan/{id}', [BpjsKetenagakerjaanController::class, 'index'])->name('bpjs-ketenagakerjaan.index');
        Route::get('karyawan/bpjs-ketenagakerjaan/get-data/{id}', [BpjsKetenagakerjaanController::class, 'getData'])->name('bpjs-ketenagakerjaan.get-data');
        Route::post('karyawan/bpjs-ketenagakerjaan/store/{id}', [BpjsKetenagakerjaanController::class, 'store'])->name('bpjs-ketenagakerjaan.store');
        Route::get('karyawan/bpjs-ketenagakerjaan/edit/{id}/{urut}', [BpjsKetenagakerjaanController::class, 'edit'])->name('bpjs-ketenagakerjaan.edit');
        Route::patch('karyawan/bpjs-ketenagakerjaan/update/{id}/{urut}', [BpjsKetenagakerjaanController::class, 'update'])->name('bpjs-ketenagakerjaan.update');

        // Keluarga
        Route::get('karyawan/keluarga/{id}', [KeluargaController::class, 'index'])->name('keluarga.index');
        Route::get('karyawan/keluarga/get-data/{id}', [KeluargaController::class, 'getData'])->name('keluarga.get-data');
        Route::post('karyawan/keluarga/store/{id}', [KeluargaController::class, 'store'])->name('keluarga.store');
        Route::post('karyawan/keluarga/reorder/{id}', [KeluargaController::class, 'reorder'])->name('keluarga.reorder');
        Route::get('karyawan/keluarga/edit/{id}/{urut}', [KeluargaController::class, 'edit'])->name('keluarga.edit');
        Route::patch('karyawan/keluarga/update/{id}/{urut}', [KeluargaController::class, 'update'])->name('keluarga.update');
        Route::delete('karyawan/keluarga/delete/{id}/{urut}', [KeluargaController::class, 'destroy'])->name('keluarga.destroy');

        // Kemampuan Bahasa
        Route::get('karyawan/kemampuan-bahasa/{id}', [KemampuanBahasaController::class, 'index'])->name('kemampuan-bahasa.index');
        Route::get('karyawan/kemampuan-bahasa/get-data/{id}', [KemampuanBahasaController::class, 'getData'])->name('kemampuan-bahasa.get-data');
        Route::post('karyawan/kemampuan-bahasa/store/{id}', [KemampuanBahasaController::class, 'store'])->name('kemampuan-bahasa.store');
        Route::get('karyawan/kemampuan-bahasa/edit/{id}/{urut}', [KemampuanBahasaController::class, 'edit'])->name('kemampuan-bahasa.edit');
        Route::patch('karyawan/kemampuan-bahasa/update/{id}/{urut}', [KemampuanBahasaController::class, 'update'])->name('kemampuan-bahasa.update');
        Route::delete('karyawan/kemampuan-bahasa/delete/{id}/{urut}', [KemampuanBahasaController::class, 'destroy'])->name('kemampuan-bahasa.destroy');
    });

    Route::middleware(['permission:view_sk_karyawan'])
        ->name('sk-kontrak.')
        ->group(function () {
        Route::get('/sk-kontrak',[SKController::class, 'index'])->name('index');
        Route::get('/sk-kontrak/datatable', [SKController::class, 'datatable'])->name('datatable');
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

    Route::middleware(['permission:view_mutasi_karyawan'])
        ->name('mutasi.')
        ->group(function () {
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

    Route::middleware(['permission:view_mutasi_on_process'])
        ->name('mutasi-on-process.')
        ->group(function () {
        Route::get('/mutasi-on-process', [MutasiOnProcessController::class, 'index'])->name('index');
        // datatable
        Route::get('/mutasi-on-process/datatable', [MutasiOnProcessController::class, 'datatable'])->name('datatable');
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

    Route::middleware(['permission:view_mutasi_pending'])
        ->name('mutasi-pending.')
        ->group(function () {
        Route::get('/mutasi-pending', [MutasiPendingController::class, 'index'])->name('index');
    });

    Route::middleware(['permission:view_mutasi_verifikasi'])
        ->name('mutasi-verifikasi.')
        ->group(function () {
        Route::get('/mutasi-verifikasi', [MutasiVerifikasiController::class, 'index'])->name('index');
    });
    
    Route::name('tugas-tambahan.')->group(function () {
        Route::get('/tugas-tambahan', [TugasTambahanController::class, 'index'])->name('index');
        // Route::get('/tugas-tambahan/create', [TugasController::class, 'create'])->name('create');
        Route::post('/tugas-tambahan/store', [TugasTambahanController::class, 'store'])->name('store');
        // Route::get('/tugas-tambahan/edit/{id}', [TugasController::class, 'edit'])->name('edit');
        // Route::patch('/tugas-tambahan/update/{id}', [TugasController::class, 'update'])->name('update');
        Route::post('/tugas-tambahan/first-verification', [TugasTambahanController::class, 'firstVerifivation'])->name('first-verification');
        Route::post('/tugas-tambahan/second-verification', [TugasTambahanController::class, 'secondVerification'])->name('second-verification');
        Route::post('/tugas-tambahan/third-verification', [TugasTambahanController::class, 'thirdVerification'])->name('third-verification');
        Route::post('/tugas-tambahan/fourth-verification', [TugasTambahanController::class, 'fourthVerification'])->name('fourth-verification');
        Route::post('/tugas-tambahan/finalisasi', [TugasTambahanController::class, 'finalisasi'])->name('finalisasi');
        // batalFinalisasi
        Route::post('/tugas-tambahan/batal-finalisasi', [TugasTambahanController::class, 'batalFinalisasi'])->name('batal-finalisasi');
        Route::get('/tugas-tambahan/rincian', [TugasTambahanController::class, 'rincian'])->name('rincian');
        Route::get('/tugas-tambahan/get-karyawan', [TugasTambahanController::class, 'getKaryawan'])->name('get-karyawan');
        // print draft nota
        Route::get('/tugas-tambahan/print-draft-nota/{kd_karyawan}/{kd_tugas_tambahan}', [TugasTambahanController::class, 'printDraftNota'])->name('print-draft-nota');
    });
});

// changePassword
Route::post('/change-password', [ChangeProfileController::class, 'changePassword'])
    ->name('change-password')
    ->middleware('auth');


// Route milik user
// User routes dengan middleware auth dan role.redirect
Route::middleware(['auth', 'role.redirect'])->prefix('user')->name('user.')->group(function () {
    Route::name('dashboard.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('index');
    });
    
    // Tambahkan route khusus user di sini
});


Route::get('/lokasi/kabupaten/{id}', [LokasiController::class, 'getKabupaten'])->name('lokasi.kabupaten');
Route::get('/lokasi/kecamatan/{id}', [LokasiController::class, 'getKecamatan'])->name('lokasi.kecamatan');
Route::get('/lokasi/kelurahan/{id}', [LokasiController::class, 'getKelurahan'])->name('lokasi.kelurahan');

// buat route untuk melakukan php artisan storage:link
Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created';
});
