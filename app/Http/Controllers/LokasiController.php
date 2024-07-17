<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LokasiController extends Controller
{
    public function getKabupaten($id)
    {
        $kabupaten = DB::connection('sqlsrv')
            ->table('kabupaten')
            ->select('kd_kabupaten', 'kd_propinsi', 'kabupaten')
            ->where('kd_propinsi', $id)
            ->orderBy('kd_kabupaten', 'asc')
            ->get()
        ;

        return response()->json($kabupaten);
    }

    public function getKecamatan($id)
    {
        $kecamatan = DB::connection('sqlsrv')
            ->table('kecamatan')
            ->select('kd_kecamatan', 'kd_kabupaten', 'kecamatan')
            ->where('kd_kabupaten', $id)
            ->orderBy('kd_kecamatan', 'asc')
            ->get()
        ;

        return response()->json($kecamatan);
    }

    public function getKelurahan($id)
    {
        $kelurahan = DB::connection('sqlsrv')
            ->table('kelurahan')
            ->select('kd_kelurahan', 'kd_kecamatan', 'kelurahan', 'kode_pos')
            ->where('kd_kecamatan', $id)
            ->where('aktif', '1')
            ->orderBy('kd_kelurahan', 'asc')
            ->get()
        ;

        return response()->json($kelurahan);
    }
}
