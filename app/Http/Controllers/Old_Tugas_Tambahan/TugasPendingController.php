<?php

namespace App\Http\Controllers\Tugas_Tambahan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TugasPendingController extends Controller
{
    public function index()
    {
        $getTugasTambahan = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->groupBy('kd_tugas_tambahan')
            ->get();

        $totalTugasTambahanOnProcess = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 1)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();

        $totalTugasTambahanPending = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();

        return view('tugas-tambahan.pending.index', [
            'getTugasTambahan' => $getTugasTambahan,
            'totalTugasTambahanOnProcess' => $totalTugasTambahanOnProcess,
            'totalTugasTambahanPending' => $totalTugasTambahanPending
        ]);
    }
}
