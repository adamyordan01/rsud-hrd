<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiPendingController extends Controller
{
    public function index()
    {
        $getMutasi = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            // ->where('kd_jenis_mutasi', 1)
            ->whereIn('kd_jenis_mutasi', [1, 3])
            // ->groupBy('kd_mutasi')
            ->get();
            // dd($getMutasi);

        // select count(KD_MUTASI) as hitung from HRD_R_MUTASI where KD_TAHAP_MUTASI = 1 and KD_JENIS_MUTASI = 1
        $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 1)
            ->where('kd_jenis_mutasi', 1)
            ->count();

        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            ->where('kd_jenis_mutasi', 1)
            ->count();

        return view('mutasi.mutasi-pending.index', [
            'getMutasi' => $getMutasi,
            'totalMutasiOnProcess' => $totalMutasiOnProcess,
            'totalMutasiPending' => $totalMutasiPending
        ]);
    }
}
