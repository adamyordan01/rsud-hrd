<?php

use Endroid\QrCode\Logo\Logo;
use App\Models\SuratIzinPraktik;
use App\Models\SuratTandaRegistrasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\QRController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BsreController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SK\SKController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KaryawanLuarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MigrateUserController;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\Export\ExportControllerAlternative;
use App\Http\Controllers\Identitas\CvController;
use App\Http\Controllers\ChangeProfileController;
use App\Http\Controllers\MutasiPendingController;
use App\Http\Controllers\Laporan\PnsUsiaController;
use App\Http\Controllers\Laporan\AbsensiController;
use App\Http\Controllers\Laporan\JenjangPendidikanController as LaporanJenjangPendidikanController;
use App\Http\Controllers\MutasiOnProcessController;
use App\Http\Controllers\Settings\BahasaController;
use App\Http\Controllers\MutasiVerifikasiController;
use App\Http\Controllers\Riwayat\KeluargaController;
use App\Http\Controllers\Settings\JurusanController;
use App\Http\Controllers\Settings\RuanganController;
use App\Http\Controllers\Settings\UserManagementController;
use App\Http\Controllers\SuratIzinPraktikController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Laporan\StrukturalController;
use App\Http\Controllers\Laporan\KepalaRuanganController;
use App\Http\Controllers\Laporan\KoordinatorController;
use App\Http\Controllers\Laporan\RekapPegawaiController;
use App\Http\Controllers\Laporan\RekapRuanganController;
use App\Http\Controllers\Laporan\PerJenisTenagaController;
use App\Http\Controllers\Settings\PekerjaanController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Karyawan\JenisTenagaController;
use App\Http\Controllers\Riwayat\RiwayatKerjaController;
use App\Http\Controllers\Riwayat\RiwayatOrganisasiController;
use App\Http\Controllers\Riwayat\PenghargaanController;
use App\Http\Controllers\Riwayat\SeminarController;
use App\Http\Controllers\Riwayat\TugasController;
use App\Http\Controllers\Riwayat\CutiController;
use App\Http\Controllers\SuratTandaRegistrasiController;
use App\Http\Controllers\Riwayat\KemampuanBahasaController;
use App\Http\Controllers\Karyawan\KaryawanRuanganController;
use App\Http\Controllers\Karyawan\KaryawanGolonganController;
use App\Http\Controllers\Riwayat\RiwayatPendidikanController;
use App\Http\Controllers\Settings\HubunganKeluargaController;
use App\Http\Controllers\Settings\TenagaManagementController;
use App\Http\Controllers\Settings\WilayahController;
use App\Http\Controllers\Karyawan\KaryawanSuratIzinController;
use App\Http\Controllers\Settings\JenjangPendidikanController;
use App\Http\Controllers\Riwayat\BpjsKetenagakerjaanController;
use App\Http\Controllers\Tugas_Tambahan\TugasTambahanController;
use App\Http\Controllers\Laporan\DaftarUrutKepangakatanController;
use App\Http\Controllers\Laporan\JabatanFungsionalController;
use App\Http\Controllers\Laporan\SuratIzinController;
use App\Http\Controllers\Karyawan\JenjangPendidikanController as KaryawanJenjangPendidikanController;

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

Auth::loginUsingId('1629');
// Auth::loginUsingId('120');

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

// Route untuk mengakses foto dari disk hrd_files
Route::get('/photo/{type}/{id}/{filename}', [KaryawanController::class, 'showPhoto'])
    ->name('photo.show')
    ->middleware('auth'); // Sesuaikan dengan middleware yang Anda gunakan

// Route untuk mengakses dokumen SK dari disk hrd_files
Route::get('/sk-document/{year}/{filename}', [SKController::class, 'showSkDocument'])
    ->name('sk.document.show')
    ->middleware('auth');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['permission:hrd_view_dashboard'])
        ->name('dashboard.')
        ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('index');
    });

    Route::middleware(['permission:hrd_view_jenis_tenaga'])
        ->prefix('jenis-tenaga')
        ->name('jenis-tenaga.')
        ->group(function () {
        Route::get('/{jenisTenaga}', [JenisTenagaController::class, 'index'])->name('index');
        Route::get('/detail/{kdDetail}/{jenisTenaga}', [JenisTenagaController::class, 'detailJenis'])->name('detail');

        Route::get('/print-pegawai/{jenisTenaga}', [JenisTenagaController::class, 'printPegawai'])->name('print-pegawai');
        Route::get('/print-pegawai-spesialis/{jenisTenaga}', [JenisTenagaController::class, 'printPegawaiSpesialis'])->name('print-pegawai-spesialis');
    });

    Route::middleware(['permission:hrd_view_karyawan_jenjang_pendidikan'])
        ->prefix('karyawan-jenjang-pendidikan')
        ->name('karyawan-jenjang-pendidikan.')
        ->group(function () {
        Route::get('/', [KaryawanJenjangPendidikanController::class, 'index'])->name('index');
        Route::get('/get-jurusan', [KaryawanJenjangPendidikanController::class, 'getJurusan'])->name('get-jurusan');
        Route::get('/print', [KaryawanJenjangPendidikanController::class, 'printJenjang'])->name('print');
    });

    Route::middleware(['permission:hrd_view_karyawan_golongan'])
        ->prefix('karyawan-golongan')
        ->name('karyawan-golongan.')
        ->group(function () {
        Route::get('/', [KaryawanGolonganController::class, 'index'])->name('index');
        Route::get('/print', [KaryawanGolonganController::class, 'printGolongan'])->name('print');
    });

    Route::middleware(['permission:hrd_view_karyawan_ruangan'])
        ->prefix('karyawan-ruangan')
        ->name('karyawan-ruangan.')
        ->group(function () {
        // Route utama - handle both dengan ruangan dan tanpa ruangan
        Route::get('/{kdRuangan?}', [KaryawanRuanganController::class, 'index'])
            ->where('kdRuangan', '[0-9]+')
            ->name('index');
        
        // Print routes - hanya untuk ruangan yang valid (bukan 0)
        Route::get('/print-jabatan/{kdRuangan}', [KaryawanRuanganController::class, 'printSesuaiJabatan'])
            ->where('kdRuangan', '[1-9][0-9]*')
            ->name('print-jabatan');
        Route::get('/print-rek-bni/{kdRuangan}', [KaryawanRuanganController::class, 'printSesuaiRekBNI'])
            ->where('kdRuangan', '[1-9][0-9]*')
            ->name('print-rek-bni');
        Route::get('/print-data/{kdRuangan}', [KaryawanRuanganController::class, 'printDataPegawai'])
            ->where('kdRuangan', '[1-9][0-9]*')
            ->name('print-data');
        
        // Export routes - hanya untuk ruangan yang valid (bukan 0)
        Route::get('/export-excel/{kdRuangan}', [KaryawanRuanganController::class, 'exportExcel'])
            ->where('kdRuangan', '[1-9][0-9]*')
            ->name('export-excel');
        Route::get('/export-fp/{kdRuangan}', [KaryawanRuanganController::class, 'exportToFP'])
            ->where('kdRuangan', '[1-9][0-9]*')
            ->name('export-fp');
    });

    // bungkus route role dan permission didalam user-management agar saya dapat mengatur menu yang akan di tampilkan
    Route::middleware(['permission:hrd_view_user_management'])
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

    // Settings User Management - Native HRD System Integration
    Route::middleware(['permission:hrd_view_user_management'])
        ->prefix('settings')
        ->name('settings.')
        ->group(function () {
        Route::prefix('user-management')->name('user-management.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::delete('/{kdKaryawan}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::get('/employees-by-access', [UserManagementController::class, 'getAvailableEmployeesByAccess'])->name('employees-by-access');
        });
    });

    // Settings/JenjangPendidikan
    Route::middleware(['permission:hrd_view_settings'])
        ->prefix('settings/jenjang-pendidikan')
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

        // Hubungan Keluarga
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
            Route::delete('/jurusan/{id}', [JurusanController::class, 'destroy'])->name('destroy');
        });

        // Bahasa
        Route::name('bahasa.')->group(function () {
            Route::get('/bahasa', [BahasaController::class, 'index'])->name('index');
            Route::post('/bahasa/store', [BahasaController::class, 'store'])->name('store');
            Route::get('/bahasa/{id}/edit', [BahasaController::class, 'edit'])->name('edit');
            Route::patch('/bahasa/{id}', [BahasaController::class, 'update'])->name('update');
        });

        // Tenaga Management
        Route::prefix('tenaga-management')
            ->name('tenaga-management.')
            ->group(function () {
                Route::get('/', [TenagaManagementController::class, 'index'])->name('index');

                // API Routes untuk cascade dropdown
                Route::get('api/details/{jenisId}', [TenagaManagementController::class, 'getDetails'])->name('api.details');
                Route::get('api/sub-details/{detailId}', [TenagaManagementController::class, 'getSubDetails'])->name('api.sub-details');

                // Jenis Tenaga (Level 1) CRUD
                Route::post('jenis-tenaga', [TenagaManagementController::class, 'storeJenisTenaga'])->name('jenis-tenaga.store');
                Route::patch('jenis-tenaga/{id}', [TenagaManagementController::class, 'updateJenisTenaga'])->name('jenis-tenaga.update');
                Route::delete('jenis-tenaga/{id}', [TenagaManagementController::class, 'destroyJenisTenaga'])->name('jenis-tenaga.destroy');

                // Jenis Tenaga Detail (Level 2) CRUD
                Route::post('detail', [TenagaManagementController::class, 'storeDetail'])->name('detail.store');
                Route::patch('detail/{jenisId}/{detailId}', [TenagaManagementController::class, 'updateDetail'])->name('detail.update');
                Route::delete('detail/{jenisId}/{detailId}', [TenagaManagementController::class, 'destroyDetail'])->name('detail.destroy');

                // Jenis Tenaga Sub Detail (Level 3) CRUD
                Route::post('sub-detail', [TenagaManagementController::class, 'storeSubDetail'])->name('sub-detail.store');
                Route::patch('sub-detail/{jenisId}/{detailId}/{subDetailId}', [TenagaManagementController::class, 'updateSubDetail'])->name('sub-detail.update');
                Route::delete('sub-detail/{jenisId}/{detailId}/{subDetailId}', [TenagaManagementController::class, 'destroySubDetail'])->name('sub-detail.destroy');
            });
    });

    // Master Wilayah - dipindahkan ke group terpisah
    Route::middleware(['permission:hrd_view_settings'])
        ->prefix('settings')
        ->name('settings.')
        ->group(function () {
        Route::prefix('wilayah')
            ->name('wilayah.')
            ->group(function () {
                Route::get('/', [WilayahController::class, 'index'])->name('index');

                // API Routes untuk cascade dropdown (existing)
                Route::get('api/kabupaten/{provinsiId}', [WilayahController::class, 'getKabupaten'])->name('api.kabupaten');
                Route::get('api/kecamatan/{kabupatenId}', [WilayahController::class, 'getKecamatan'])->name('api.kecamatan');
                Route::get('api/kelurahan/{kecamatanId}', [WilayahController::class, 'getKelurahan'])->name('api.kelurahan');

                // New API Routes untuk lazy loading
                Route::get('load/kabupaten/{provinsiId}', [WilayahController::class, 'loadKabupaten'])->name('load.kabupaten');
                Route::get('load/kecamatan/{kabupatenId}', [WilayahController::class, 'loadKecamatan'])->name('load.kecamatan');
                Route::get('load/kelurahan/{kecamatanId}', [WilayahController::class, 'loadKelurahan'])->name('load.kelurahan');

                // Provinsi (Level 1) CRUD
                Route::post('provinsi', [WilayahController::class, 'storeProvinsi'])->name('provinsi.store');
                Route::get('provinsi/{id}/edit', [WilayahController::class, 'editProvinsi'])->name('provinsi.edit');
                Route::patch('provinsi/{id}', [WilayahController::class, 'updateProvinsi'])->name('provinsi.update');
                Route::delete('provinsi/{id}', [WilayahController::class, 'destroyProvinsi'])->name('provinsi.destroy');

                // Kabupaten (Level 2) CRUD
                Route::post('kabupaten', [WilayahController::class, 'storeKabupaten'])->name('kabupaten.store');
                Route::get('kabupaten/{provinsiId}/{kabupatenId}/edit', [WilayahController::class, 'editKabupaten'])->name('kabupaten.edit');
                Route::patch('kabupaten/{provinsiId}/{kabupatenId}', [WilayahController::class, 'updateKabupaten'])->name('kabupaten.update');
                Route::delete('kabupaten/{provinsiId}/{kabupatenId}', [WilayahController::class, 'destroyKabupaten'])->name('kabupaten.destroy');

                // Kecamatan (Level 3) CRUD
                Route::post('kecamatan', [WilayahController::class, 'storeKecamatan'])->name('kecamatan.store');
                Route::get('kecamatan/{kabupatenId}/{kecamatanId}/edit', [WilayahController::class, 'editKecamatan'])->name('kecamatan.edit');
                Route::patch('kecamatan/{kabupatenId}/{kecamatanId}', [WilayahController::class, 'updateKecamatan'])->name('kecamatan.update');
                Route::delete('kecamatan/{kabupatenId}/{kecamatanId}', [WilayahController::class, 'destroyKecamatan'])->name('kecamatan.destroy');

                // Kelurahan (Level 4) CRUD
                Route::post('kelurahan', [WilayahController::class, 'storeKelurahan'])->name('kelurahan.store');
                Route::get('kelurahan/{kecamatanId}/{kelurahanId}/edit', [WilayahController::class, 'editKelurahan'])->name('kelurahan.edit');
                Route::patch('kelurahan/{kecamatanId}/{kelurahanId}', [WilayahController::class, 'updateKelurahan'])->name('kelurahan.update');
                Route::delete('kelurahan/{kecamatanId}/{kelurahanId}', [WilayahController::class, 'destroyKelurahan'])->name('kelurahan.destroy');
            });
    });

    Route::middleware(['permission:hrd_view_karyawan'])
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
        Route::get('karyawan/str/get-str-data/{id}', [SuratTandaRegistrasiController::class, 'getStrData'])->name('str.get-str-data');
        Route::post('karyawan/str/store/{id}', [SuratTandaRegistrasiController::class, 'store'])->name('str.store');
        Route::get('karyawan/str/edit/{id}/{urut}', [SuratTandaRegistrasiController::class, 'edit'])->name('str.edit');
        Route::patch('karyawan/str/update/{id}/{urut}', [SuratTandaRegistrasiController::class, 'update'])->name('str.update');
        // Route baru untuk download file dengan keamanan
        Route::get('karyawan/str/download/{id}/{urut}', [SuratTandaRegistrasiController::class, 'downloadFile'])->name('str.download');
        Route::delete('karyawan/str/delete/{id}/{urut}', [SuratTandaRegistrasiController::class, 'destroy'])->name('str.destroy');

        // SIP
        Route::get('karyawan/sip/{id}', [SuratIzinPraktikController::class, 'index'])->name('sip.index');
        Route::get('karyawan/sip/get-sip-data/{id}', [SuratIzinPraktikController::class, 'getSipData'])->name('sip.get-sip-data');
        Route::post('karyawan/sip/store/{id}', [SuratIzinPraktikController::class, 'store'])->name('sip.store');
        Route::get('karyawan/sip/edit/{id}/{urut}', [SuratIzinPraktikController::class, 'edit'])->name('sip.edit');
        Route::patch('karyawan/sip/update/{id}/{urut}', [SuratIzinPraktikController::class, 'update'])->name('sip.update');
        // Route baru untuk download file dengan keamanan
        Route::get('karyawan/sip/download/{id}/{urut}', [SuratIzinPraktikController::class, 'downloadFile'])->name('sip.download');
        Route::delete('karyawan/sip/delete/{id}/{urut}', [SuratIzinPraktikController::class, 'destroy'])->name('sip.destroy');

        // Riwayat Pendidikan
        Route::group(['prefix' => 'karyawan/pendidikan', 'as' => 'pendidikan.'], function () {
            Route::get('/{id}', [RiwayatPendidikanController::class, 'index'])->name('index');
            Route::get('/get-pendidikan-data/{id}', [RiwayatPendidikanController::class, 'getPendidikanData'])->name('get-pendidikan-data');
            Route::post('/store/{id}', [RiwayatPendidikanController::class, 'store'])->name('store');
            Route::get('/edit/{id}/{urut}', [RiwayatPendidikanController::class, 'edit'])->name('edit');
            Route::patch('/update/{id}/{urut}', [RiwayatPendidikanController::class, 'update'])->name('update');
            Route::delete('/delete/{id}/{urut}', [RiwayatPendidikanController::class, 'destroy'])->name('destroy');
        });

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

        // Surat Izin
        Route::group(['prefix' => 'karyawan/surat-izin', 'as' => 'surat-izin.'], function () {
            Route::get('/', [KaryawanSuratIzinController::class, 'index'])->name('index');
            Route::post('/store', [KaryawanSuratIzinController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [KaryawanSuratIzinController::class, 'edit'])->name('edit');
            Route::put('/{id}', [KaryawanSuratIzinController::class, 'update'])->name('update');
            Route::delete('/{id}', [KaryawanSuratIzinController::class, 'destroy'])->name('destroy');
            Route::post('/get-kategori', [KaryawanSuratIzinController::class, 'getKategori'])->name('get-kategori');
            Route::get('/{id}/print', [KaryawanSuratIzinController::class, 'print'])->name('print');
        });

        // Riwayat Kerja
        Route::group(['prefix' => 'karyawan/riwayat-kerja', 'as' => 'riwayat-kerja.'], function () {
            Route::get('/{id}', [RiwayatKerjaController::class, 'index'])->name('index');
            Route::get('/get-data/{id}', [RiwayatKerjaController::class, 'getRiwayatKerjaData'])->name('get-data');
            Route::post('/store/{id}', [RiwayatKerjaController::class, 'store'])->name('store');
            Route::get('/edit/{id}/{urut}', [RiwayatKerjaController::class, 'edit'])->name('edit');
            Route::patch('/update/{id}/{urut}', [RiwayatKerjaController::class, 'update'])->name('update');
            Route::delete('/delete/{id}/{urut}', [RiwayatKerjaController::class, 'destroy'])->name('destroy');
            // download file
            Route::get('/download/{id}/{urut}', [RiwayatKerjaController::class, 'downloadFile'])->name('download');
        });

        // Riwayat Organisasi
        Route::group(['prefix' => 'karyawan/riwayat-organisasi', 'as' => 'riwayat-organisasi.'], function () {
            Route::get('/{id}', [RiwayatOrganisasiController::class, 'index'])->name('index');
            Route::get('/get-data/{id}', [RiwayatOrganisasiController::class, 'getRiwayatOrganisasiData'])->name('get-data');
            Route::post('/store/{id}', [RiwayatOrganisasiController::class, 'store'])->name('store');
            Route::get('/edit/{id}/{urut}', [RiwayatOrganisasiController::class, 'edit'])->name('edit');
            Route::patch('/update/{id}/{urut}', [RiwayatOrganisasiController::class, 'update'])->name('update');
            Route::delete('/delete/{id}/{urut}', [RiwayatOrganisasiController::class, 'destroy'])->name('destroy');
        });

        // Penghargaan
        Route::group(['prefix' => 'karyawan/penghargaan', 'as' => 'penghargaan.'], function () {
            Route::get('/{id}', [PenghargaanController::class, 'index'])->name('index');
            Route::get('/get-data/{id}', [PenghargaanController::class, 'getPenghargaanData'])->name('data');
            Route::post('/store/{id}', [PenghargaanController::class, 'store'])->name('store');
            Route::get('/edit/{id}/{urut}', [PenghargaanController::class, 'edit'])->name('edit');
            Route::put('/update/{id}/{urut}', [PenghargaanController::class, 'update'])->name('update');
            Route::delete('/delete/{id}/{urut}', [PenghargaanController::class, 'destroy'])->name('destroy');
        });

        // Seminar
        Route::group(['prefix' => 'karyawan/seminar', 'as' => 'seminar.'], function () {
            Route::get('/{id}', [SeminarController::class, 'index'])->name('index');
            Route::get('/get-data/{id}', [SeminarController::class, 'getSeminarData'])->name('data');
            Route::post('/store/{id}', [SeminarController::class, 'store'])->name('store');
            Route::get('/edit/{id}/{urut}', [SeminarController::class, 'edit'])->name('edit');
            Route::put('/update/{id}/{urut}', [SeminarController::class, 'update'])->name('update');
            Route::delete('/delete/{id}/{urut}', [SeminarController::class, 'destroy'])->name('destroy');
        });

        // Tugas (Read-only)
        Route::group(['prefix' => 'karyawan/tugas', 'as' => 'tugas.'], function () {
            Route::get('/{id}', [TugasController::class, 'index'])->name('index');
            Route::get('/get-data/{id}', [TugasController::class, 'getTugasData'])->name('data');
        });

        // Cuti
        Route::group(['prefix' => 'karyawan/cuti', 'as' => 'cuti.'], function () {
            Route::get('/{id}', [CutiController::class, 'index'])->name('index');
            Route::get('/get-data/{id}', [CutiController::class, 'getCutiData'])->name('data');
            Route::post('/store/{id}', [CutiController::class, 'store'])->name('store');
            Route::get('/edit/{id}/{urut}', [CutiController::class, 'edit'])->name('edit');
            Route::put('/update/{id}/{urut}', [CutiController::class, 'update'])->name('update');
            Route::delete('/delete/{id}/{urut}', [CutiController::class, 'destroy'])->name('destroy');
        });
    });

    // Karyawan Luar Routes
    Route::middleware(['permission:hrd_view_karyawan'])
        ->name('karyawan-luar.')
        ->group(function () {
        Route::get('/karyawan-luar', [KaryawanLuarController::class, 'index'])->name('index');
        Route::get('/karyawan-luar/create', [KaryawanLuarController::class, 'create'])->name('create');
        Route::post('/karyawan-luar/store', [KaryawanLuarController::class, 'store'])->name('store');
        Route::get('/karyawan-luar/show/{id}', [KaryawanLuarController::class, 'show'])->name('show');
        Route::get('/karyawan-luar/edit/{id}', [KaryawanLuarController::class, 'edit'])->name('edit');
        Route::patch('/karyawan-luar/update/{id}', [KaryawanLuarController::class, 'update'])->name('update');
    });

    // Karyawan Belum Lengkap Routes
    Route::middleware(['permission:hrd_view_karyawan'])
        ->name('karyawan-belum-lengkap.')
        ->group(function () {
        Route::get('/karyawan-belum-lengkap', [App\Http\Controllers\KaryawanBelumLengkapController::class, 'index'])->name('index');
    });

    // Pegawai Tidak Aktif Routes
    Route::middleware(['permission:hrd_view_karyawan'])
        ->name('pegawai-tidak-aktif.')
        ->group(function () {
        Route::get('/pegawai-tidak-aktif', [App\Http\Controllers\PegawaiTidakAktifController::class, 'index'])->name('index');
        Route::get('/pegawai-pensiun', [App\Http\Controllers\PegawaiTidakAktifController::class, 'pensiun'])->name('pensiun');
        Route::get('/pegawai-keluar', [App\Http\Controllers\PegawaiTidakAktifController::class, 'keluar'])->name('keluar');
        Route::get('/pegawai-tugas-belajar', [App\Http\Controllers\PegawaiTidakAktifController::class, 'tugasBelajar'])->name('tugas-belajar');
        Route::get('/pegawai-meninggal', [App\Http\Controllers\PegawaiTidakAktifController::class, 'meninggal'])->name('meninggal');
        Route::get('/cetak-pegawai-tidak-aktif/{status}', [App\Http\Controllers\PegawaiTidakAktifController::class, 'cetakLaporan'])->name('cetak');
    });

    Route::middleware(['permission:hrd_view_sk_karyawan'])
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
        Route::get('/sk-kontrak/print-perjanjian-kerja/{urut}/{tahun}/testing', function($urut, $tahun) {
            return app(SKController::class)->printPerjanjianKerja($urut, $tahun);
        })->name('print-perjanjian-kerja-testing');
        Route::delete('/sk-kontrak/cleanup-temporary-files', [SKController::class, 'cleanupTemporaryFiles'])->name('cleanup-temporary-files');
    });

    Route::middleware(['permission:hrd_view_mutasi_karyawan'])
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

    Route::middleware(['permission:hrd_view_mutasi_on_process'])
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
        
        // Route untuk download dokumen mutasi yang sudah ditandatangani
        Route::get('/mutasi-on-process/download-document/{id_dokumen}', [MutasiOnProcessController::class, 'downloadMutasiDocument'])->name('download-document');
        
        // Route untuk cleanup file temporary mutasi (admin only)
        Route::delete('/mutasi-on-process/cleanup-temporary-files', [MutasiOnProcessController::class, 'cleanupMutasiTemporaryFiles'])->name('cleanup-temporary-files');
    });

    Route::middleware(['permission:hrd_view_mutasi_pending'])
        ->name('mutasi-pending.')
        ->group(function () {
        Route::get('/mutasi-pending', [MutasiPendingController::class, 'index'])->name('index');
    });

    Route::middleware(['permission:hrd_view_mutasi_verifikasi'])
        ->name('mutasi-verifikasi.')
        ->group(function () {
        Route::get('/mutasi-verifikasi', [MutasiVerifikasiController::class, 'index'])->name('index');
    });
    
    Route::middleware(['permission:hrd_view_tugas_tambahan'])
        ->name('tugas-tambahan.')
        ->group(function () {
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

    // Export Data
    Route::group(['prefix' => 'export', 'as' => 'export.', 'middleware' => ['permission:hrd_view_export', 'export']], function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::get('/debug', [ExportController::class, 'debug'])->name('debug');
        
        // Export berdasarkan status kerja
        Route::get('/total-aktif', [ExportController::class, 'totalAktif'])->name('total-aktif');
        
        // Export Pegawai Aktif dengan maatwebsite/excel (NEW - LENGKAP)
        Route::get('/pegawai-aktif-maatwebsite', [ExportController::class, 'exportPegawaiAktifMaatwebsite'])->name('pegawai-aktif-maatwebsite');
        
        Route::get('/duk', [ExportController::class, 'exportDuk'])->name('duk');
        Route::get('/honor', [ExportController::class, 'exportHonor'])->name('honor');
        Route::get('/kontrak-blud', [ExportController::class, 'exportKontrakBlud'])->name('kontrak-blud');
        Route::get('/kontrak-pemko', [ExportController::class, 'exportKontrakPemko'])->name('kontrak-pemko');
        Route::get('/part-time', [ExportController::class, 'exportPartTimeMaatwebsite'])->name('part-time');
        Route::get('/pppk', [ExportController::class, 'exportPppkMaatwebsite'])->name('pppk');
        Route::get('/thl', [ExportController::class, 'exportThlMaatwebsite'])->name('thl');
        
        // Export berdasarkan jenis tenaga
        Route::get('/tenaga-medis', [ExportController::class, 'exportTenagaMedis'])->name('tenaga-medis');
        Route::get('/perawat-bidan', [ExportController::class, 'exportPerawatBidan'])->name('perawat-bidan');
        Route::get('/penunjang-medis', [ExportController::class, 'exportPenunjangMedis'])->name('penunjang-medis');
        Route::get('/non-kesehatan', [ExportController::class, 'exportNonKesehatan'])->name('non-kesehatan');
        
        // Export berdasarkan status pegawai
        Route::get('/pegawai-keluar', [ExportController::class, 'exportPegawaiKeluar'])->name('pegawai-keluar');
        Route::get('/pegawai-pensiun', [ExportController::class, 'exportPegawaiPensiun'])->name('pegawai-pensiun');
        Route::get('/pegawai-tubel', [ExportController::class, 'exportPegawaiTubel'])->name('pegawai-tubel');
        
        // Export untuk keperluan bank
        Route::get('/bni-syariah-kontrak', [ExportController::class, 'exportBniSyariahKontrak'])->name('bni-syariah-kontrak');
        Route::get('/bni-syariah-pns', [ExportController::class, 'exportBniSyariahPns'])->name('bni-syariah-pns');
        
        // Performance testing routes
        Route::get('/performance-test', [\App\Http\Controllers\Export\ExportControllerAlternative::class, 'performanceTest'])->name('performance-test');
        Route::get('/test/html-fast', [\App\Http\Controllers\Export\ExportControllerAlternative::class, 'exportHTMLFast'])->name('test.html-fast');
        Route::get('/test/maatwebsite', [\App\Http\Controllers\Export\ExportControllerAlternative::class, 'exportMaatwebsite'])->name('test.maatwebsite');
        Route::get('/test/phpspreadsheet', [\App\Http\Controllers\Export\ExportControllerAlternative::class, 'exportPhpSpreadsheet'])->name('test.phpspreadsheet');
    });

    // Laporan
    Route::middleware(['permission:hrd_view_laporan'])
        ->prefix('laporan')
        ->name('laporan.')
        ->group(function () {
        Route::group(['prefix' => 'duk', 'as' => 'duk.'], function () {
            Route::get('/', [DaftarUrutKepangakatanController::class, 'index'])->name('index');
            Route::get('/print', [DaftarUrutKepangakatanController::class, 'print'])->name('print');
            Route::get('/check-data', [DaftarUrutKepangakatanController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'pns-usia', 'as' => 'pns-usia.'], function () {
            Route::get('/', [PnsUsiaController::class, 'index'])->name('index');
            Route::get('/print', [PnsUsiaController::class, 'print'])->name('print');
            Route::get('/check-data', [PnsUsiaController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'struktural', 'as' => 'struktural.'], function () {
            Route::get('/', [StrukturalController::class, 'index'])->name('index');
            Route::get('/print', [StrukturalController::class, 'print'])->name('print');
            Route::get('/check-data', [StrukturalController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'kepala-ruangan', 'as' => 'kepala-ruangan.'], function () {
            Route::get('/', [KepalaRuanganController::class, 'index'])->name('index');
            Route::get('/print', [KepalaRuanganController::class, 'print'])->name('print');
            Route::get('/check-data', [KepalaRuanganController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'koordinator', 'as' => 'koordinator.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\KoordinatorController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\KoordinatorController::class, 'print'])->name('print');
            Route::get('/check-data', [\App\Http\Controllers\Laporan\KoordinatorController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'rekap-pegawai', 'as' => 'rekap-pegawai.'], function () {
            Route::get('/', [RekapPegawaiController::class, 'index'])->name('index');
            Route::get('/print', [RekapPegawaiController::class, 'print'])->name('print');
            Route::get('/check-data', [RekapPegawaiController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'rekap-ruangan', 'as' => 'rekap-ruangan.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\RekapRuanganController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\RekapRuanganController::class, 'print'])->name('print');
            Route::get('/check-data', [\App\Http\Controllers\Laporan\RekapRuanganController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'golongan', 'as' => 'golongan.'], function () {
            Route::get('/', [App\Http\Controllers\Laporan\GolonganController::class, 'index'])->name('index');
            Route::get('/print', [App\Http\Controllers\Laporan\GolonganController::class, 'print'])->name('print');
        });

        Route::group(['prefix' => 'jenjang-pendidikan', 'as' => 'jenjang-pendidikan.'], function () {
            Route::get('/', [LaporanJenjangPendidikanController::class, 'index'])->name('index');
            Route::get('/get-jurusan', [LaporanJenjangPendidikanController::class, 'getJurusan'])->name('get-jurusan');
            Route::get('/print', [LaporanJenjangPendidikanController::class, 'print'])->name('print');
            Route::get('/check-data', [LaporanJenjangPendidikanController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'per-pendidikan', 'as' => 'per-pendidikan.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\PerPendidikanController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\PerPendidikanController::class, 'print'])->name('print');
        });

        Route::group(['prefix' => 'per-pendidikan-ruangan', 'as' => 'per-pendidikan-ruangan.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\PerPendidikanRuanganController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\PerPendidikanRuanganController::class, 'print'])->name('print');
        });

        Route::group(['prefix' => 'per-jenis-tenaga', 'as' => 'per-jenis-tenaga.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\PerJenisTenagaController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\PerJenisTenagaController::class, 'print'])->name('print');
        });

        Route::group(['prefix' => 'per-jenis-tenaga-ruangan', 'as' => 'per-jenis-tenaga-ruangan.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\PerJenisTenagaRuanganController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\PerJenisTenagaRuanganController::class, 'print'])->name('print');
        });

        Route::group(['prefix' => 'data-taspen', 'as' => 'data-taspen.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\DataTaspenController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\DataTaspenController::class, 'print'])->name('print');
        });

        Route::group(['prefix' => 'satya-lencana', 'as' => 'satya-lencana.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\SatyaLencanaController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\SatyaLencanaController::class, 'print'])->name('print');
        });

        Route::group(['prefix' => 'jabatan-fungsional', 'as' => 'jabatan-fungsional.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\JabatanFungsionalController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\JabatanFungsionalController::class, 'print'])->name('print');
            Route::get('/check-data', [\App\Http\Controllers\Laporan\JabatanFungsionalController::class, 'checkData'])->name('check-data');
        });

        Route::group(['prefix' => 'surat-izin', 'as' => 'surat-izin.'], function () {
            Route::get('/', [\App\Http\Controllers\Laporan\SuratIzinController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Laporan\SuratIzinController::class, 'print'])->name('print');
            Route::get('/pdf', [\App\Http\Controllers\Laporan\SuratIzinController::class, 'pdf'])->name('pdf');
            Route::get('/check-data', [\App\Http\Controllers\Laporan\SuratIzinController::class, 'checkData'])->name('check-data');
            Route::get('/get-ruangan', [\App\Http\Controllers\Laporan\SuratIzinController::class, 'getRuangan'])->name('get-ruangan');
            Route::get('/get-kategori-izin', [\App\Http\Controllers\Laporan\SuratIzinController::class, 'getKategoriIzin'])->name('get-kategori-izin');
        });

        Route::group(['prefix' => 'absensi', 'as' => 'absensi.'], function () {
            Route::get('/', [AbsensiController::class, 'index'])->name('index');
            Route::get('/preview', [AbsensiController::class, 'preview'])->name('preview');
            Route::get('/print', [AbsensiController::class, 'print'])->name('print');
            Route::get('/check-data', [AbsensiController::class, 'checkData'])->name('check-data');
        });
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