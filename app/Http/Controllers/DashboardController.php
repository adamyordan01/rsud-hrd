<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawaiPns = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 1)
            ->where('status_peg', 1)
            ->first();

        $pegawaiHonor = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 2)
            ->where('status_peg', 1)
            ->first();

        $pppk = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 7)
            ->where('status_peg', 1)
            ->first();

        $pegawaiKontrakBlud = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 3)
            ->where('status_peg', 1)
            ->where('kd_jenis_peg', 2)
            ->first();

        $pegawaiKontrakPemko = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 3)
            ->where('status_peg', 1)
            ->where('kd_jenis_peg', 1)
            ->first();

        $pegawaiPartTime = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 4)
            ->where('status_peg', 1)
            ->first();

        $pegawaiLuar = DB::table('hrd_karyawan_luar')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 5)
            ->where('status_peg', 1)
            ->first();

        $pegawaiLuar = collect($pegawaiLuar);

        $pegawaiThl = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 6)
            ->where('status_peg', 1)
            ->first();

        $totalPegawai = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('status_peg', 1)
            ->first();

        $pegawai = [
            'pns' => $pegawaiPns,
            'pppk' => $pppk,
            'honor' => $pegawaiHonor,
            'kontrak_blud' => $pegawaiKontrakBlud,
            'kontrak_pemko' => $pegawaiKontrakPemko,
            'part_time' => $pegawaiPartTime,
            'luar' => $pegawaiLuar,
            'thl' => $pegawaiThl,
            'total' => $totalPegawai,
        ];

        // dd($pegawai);
        // dd($pegawai['luar']['jumlah']);
        
        return view('dashboard.index', compact('pegawai'));
    }
}
