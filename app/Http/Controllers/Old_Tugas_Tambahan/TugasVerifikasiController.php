<?php

namespace App\Http\Controllers\Tugas_Tambahan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TugasVerifikasiController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        if (request()->has('start_date') && request()->has('end_date')) {
            // Jika start_date dan end_date ada dalam request
            $rawStartDate = request('start_date');
            $rawEndDate = request('end_date');

            if ($rawStartDate && $rawEndDate) {
                $startDate = Carbon::parse($rawStartDate)->format('Y-m-d');
                $endDate = Carbon::parse($rawEndDate)->format('Y-m-d');
            }
        }

        $getTugasTambahanVerifikasi = DB::table('view_verifikasi_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 2)
            ->whereBetween('tmt_jabatan', [$startDate, $endDate])
            ->orderBy('kd_tugas_tambahan', 'desc')
            ->get();

            // dd($getMutasiVerifikasi);

        $totalTugasTambahanOnProcess = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 1)
            ->count();

        $totalTugasTambahanPending = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();

        return view('tugas-tambahan.verifikasi.index', [
            'getTugasTambahanVerifikasi' => $getTugasTambahanVerifikasi,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalTugasTambahanOnProcess' => $totalTugasTambahanOnProcess,
            'totalTugasTambahanPending' => $totalTugasTambahanPending
        ]);
    }
}
