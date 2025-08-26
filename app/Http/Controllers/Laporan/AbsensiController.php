<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AbsensiController extends Controller
{
    public function index()
    {
        // Ambil data ruangan untuk dropdown
        $ruangans = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->orderBy('ruangan')
            ->get();

        return view('laporan.absensi.index', compact('ruangans'));
    }
    
    public function preview(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kdRuangan = $request->ruangan;
        
        // Validasi input
        if (empty($bulan) || empty($tahun) || empty($kdRuangan)) {
            return response()->json(['error' => 'Parameter tidak lengkap'], 400);
        }
        
        // Ambil data ruangan
        $ruanganData = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->where('kd_ruangan', $kdRuangan)
            ->first();
        
        if (!$ruanganData) {
            return response()->json(['error' => 'Ruangan tidak ditemukan'], 404);
        }
        
        // Query pegawai sesuai ruangan
        $query = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('kd_ruangan', $kdRuangan)
            ->where('status_peg', 1) // Pegawai aktif
            ->orderBy('kd_jab_1', 'ASC')
            ->orderBy('sub_detail', 'ASC') 
            ->orderBy('kd_status_kerja', 'ASC')
            ->orderBy('nilaiIndex', 'DESC')
            ->orderBy('tahun_lulus', 'ASC')
            ->orderByRaw('YEAR(tgl_lahir) ASC')
            ->orderBy('nama', 'ASC');
        
        $dataPegawai = $query->get();
        
        // Array nama bulan
        $dataBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        return view('laporan.absensi.preview', compact('dataPegawai', 'ruanganData', 'bulan', 'tahun', 'dataBulan'));
    }
    
    public function print(Request $request)
    {
        $bulan = $request->bln;
        $tahun = $request->thn;
        $kdRuangan = $request->ruang;
        $kdFilter = $request->kd; // RM, IGD, atau kosong
        
        // Ambil data ruangan
        $ruanganData = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->where('kd_ruangan', $kdRuangan)
            ->first();
        
        // Query pegawai berdasarkan filter
        if ($kdFilter == 'RM') {
            // RM: Exclude tenaga medis tertentu
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('kd_ruangan', $kdRuangan)
                ->whereNotIn('kd_detail_jenis_tenaga', [3, 8, 11])
                ->where('status_peg', 1);
        } elseif ($kdFilter == 'IGD') {
            // IGD: Hanya tenaga medis spesifik
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('kd_ruangan', $kdRuangan)
                ->whereIn('kd_detail_jenis_tenaga', [3, 8, 11])
                ->whereIn('kd_sub_detail_jenis_tenaga', [1, 3])
                ->where('status_peg', 1);
        } else {
            // Normal: Semua pegawai aktif di ruangan
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('kd_ruangan', $kdRuangan)
                ->where('status_peg', 1);
        }
        
        $dataPegawai = $query->orderBy('kd_jab_1', 'ASC')
                           ->orderBy('sub_detail', 'ASC')
                           ->orderBy('kd_status_kerja', 'ASC')
                           ->orderBy('nilaiIndex', 'DESC')
                           ->orderBy('tahun_lulus', 'ASC')
                           ->orderByRaw('YEAR(tgl_lahir) ASC')
                           ->orderBy('nama', 'ASC')
                           ->get();
        
        // Array nama bulan
        $dataBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        return view('laporan.absensi.print', compact('dataPegawai', 'ruanganData', 'bulan', 'tahun', 'dataBulan', 'kdFilter'));
    }
    
    public function checkData(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kdRuangan = $request->ruangan;
        
        $count = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('kd_ruangan', $kdRuangan)
            ->where('status_peg', 1)
            ->count();
        
        return response()->json(['count' => $count]);
    }
}
