<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiVerifikasiController extends Controller
{
    public function index(Request $request)
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

        $getMutasiVerifikasi = DB::table('view_verifikasi')
            ->where('kd_tahap_mutasi', 2)
            ->whereBetween('tmt_jabatan', [$startDate, $endDate])
            ->orderBy('kd_mutasi', 'desc')
            ->get();

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

        return view('mutasi.mutasi-verifikasi.index', [
            'getMutasiVerifikasi' => $getMutasiVerifikasi,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalMutasiOnProcess' => $totalMutasiOnProcess,
            'totalMutasiPending' => $totalMutasiPending
        ]);
    }
}
