<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // Carbon default locale
        Carbon::setLocale('id');

        // parsing jumlah mutasi yang harus diverifikasi oleh tiap pemegang jabatan
        // ambil kd_jabatan dari user yang login
        View::composer('layouts.backend-modules.sidebar', function ($view) {
            $user = Auth::user();

            if ($user) {
                $jabatan = $user->kd_jabatan_struktural ?? 0;

                $totalMutasiOnProcess = 0;
                $totalSk = 0;
                $totalTugasTambahan = 0;

                if ($jabatan == 19) {
                    $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
                        ->select('kd_mutasi')
                        ->where('kd_tahap_mutasi', 1)
                        ->where('kd_jenis_mutasi', 1)
                        ->whereNull('verif_1')
                        ->count();

                    $totalSk = DB::table('hrd_sk_pegawai_kontrak')
                        ->select('kd_index')
                        ->whereNull('verif_1')
                        ->count();

                    $totalTugasTambahan = DB::table('hrd_tugas_tambahan')
                        ->select('kd_tugas_tambahan')
                        ->where('kd_tahap_tugas_tambahan', 1)
                        ->whereNull('verif_1')
                        ->count();
                } elseif ($jabatan == 7) {
                    // ketika verif_1 tidak null, verif_2 null, kd_karyawan_verif_2 null
                    $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
                        ->select('kd_mutasi')
                        ->where('kd_tahap_mutasi', 1)
                        ->where('kd_jenis_mutasi', 1)
                        ->whereNotNull('verif_1')
                        ->whereNull('verif_2')
                        ->count();

                    // ketika verif_1 tidak null, verif_2 null, kd_karyawan_verif_2 null
                    $totalSk = DB::table('hrd_sk_pegawai_kontrak')
                        ->select('kd_index')
                        ->whereNotNull('verif_1')
                        ->whereNull('verif_2')
                        ->whereNull('kd_karyawan_verif_2')
                        ->count();

                    $totalTugasTambahan = DB::table('hrd_tugas_tambahan')
                        ->select('kd_tugas_tambahan')
                        ->where('kd_tahap_tugas_tambahan', 1)
                        ->whereNotNull('verif_1')
                        ->whereNull('verif_2')
                        ->whereNull('kd_karyawan_verif_2')
                        ->count();
                } elseif ($jabatan == 3 || $jabatan == 6) {
                    // ketika verif_2 tidak null, verif_3 null, kd_karyawan_verif_3 null
                    $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
                        ->select('kd_mutasi')
                        ->where('kd_tahap_mutasi', 1)
                        ->where('kd_jenis_mutasi', 1)
                        ->whereNotNull('verif_2')
                        ->whereNull('verif_3')
                        ->whereNull('kd_karyawan_verif_3')
                        ->count();

                    // ketika verif_2 tidak null, verif_3 null, kd_karyawan_verif_3 null
                    $totalSk = DB::table('hrd_sk_pegawai_kontrak')
                        ->select('kd_index')
                        ->whereNotNull('verif_2')
                        ->whereNull('verif_3')
                        ->whereNull('kd_karyawan_verif_3')
                        ->count();

                    $totalTugasTambahan = DB::table('hrd_tugas_tambahan')
                        ->select('kd_tugas_tambahan')
                        ->where('kd_tahap_tugas_tambahan', 1)
                        ->whereNotNull('verif_2')
                        ->whereNull('verif_3')
                        ->whereNull('kd_karyawan_verif_3')
                        ->count();
                } elseif ($jabatan == 1) {
                    // ketika verif_3 tidak null, verif_4 null, kd_karyawan_verif_4 null
                    $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
                        ->select('kd_mutasi')
                        ->where('kd_tahap_mutasi', 1)
                        ->where('kd_jenis_mutasi', 1)
                        ->whereNotNull('verif_3')
                        ->whereNull('verif_4')
                        ->whereNull('kd_karyawan_verif_4')
                        ->count();

                    $totalSk = DB::table('hrd_sk_pegawai_kontrak')
                        ->select('kd_index')
                        ->whereNotNull('verif_3')
                        ->whereNull('verif_4')
                        ->whereNull('kd_karyawan_verif_4')
                        ->count();

                    $totalTugasTambahan = DB::table('hrd_tugas_tambahan')
                        ->select('kd_tugas_tambahan')
                        ->where('kd_tahap_tugas_tambahan', 1)
                        ->whereNotNull('verif_3')
                        ->whereNull('verif_4')
                        ->whereNull('kd_karyawan_verif_4')
                        ->count();
                } else {
                    // Mengirimkan nilai default jika jabatan tidak ditemukan
                    $view->with('totalMutasiOnProcess', 0);
                    $view->with('totalSk', 0);
                    $view->with('totalTugasTambahan', 0);
                }

                // Mengirimkan data ke partial sidebar
                $view->with('totalMutasiOnProcess', $totalMutasiOnProcess);
                $view->with('totalSk', $totalSk);
                $view->with('totalTugasTambahan', $totalTugasTambahan);
            } else {
                // Mengirimkan nilai default jika tidak ada pengguna yang login
                $view->with('totalMutasiOnProcess', 0);
                $view->with('totalSk', 0);
                $view->with('totalTugasTambahan', 0);
            }
        });

        // Directive untuk mengecek apakah user memiliki permission tertentu
        Blade::if('hasPermission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermissionTo($permission);
        });
        
        // Directive untuk mengecek apakah user memiliki role tertentu
        Blade::if('hasRole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
    }
}
