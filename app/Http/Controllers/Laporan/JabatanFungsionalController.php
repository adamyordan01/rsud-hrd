<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use App\Helpers\PhotoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class JabatanFungsionalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            
            // Tentukan query berdasarkan bulan dan tahun
            if (empty($bulan) || empty($tahun) || ($bulan == date('m') && $tahun == date('Y'))) {
                // Data terkini
                $query = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan')
                    ->whereNotNull('kd_jabfung')
                    ->where('kd_jabfung', '!=', 0)
                    ->where('status_peg', '1')
                    ->where('kd_status_kerja', '1')
                    ->where('kd_jab_1', '>=', 5);
            } else {
                // Data backup/historis
                $query = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan_backup')
                    ->whereNotNull('kd_jabfung')
                    ->where('kd_jabfung', '!=', 0)
                    ->where('status_peg', '1')
                    ->where('kd_status_kerja', '1')
                    ->where('kd_jab_1', '>=', 5)
                    ->where('bulan_backup', $bulan)
                    ->where('tahun_backup', $tahun);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    return $gelarDepan . $row->nama . $gelarBelakang;
                })
                ->addColumn('nip', function ($row) {
                    return $row->nip_baru ?? ($row->no_karpeg ?? $row->kd_karyawan);
                })
                ->editColumn('jenis_kelamin', function($row) {
                    return $row->jenis_kelamin == 'Pria' ? 'L' : ($row->jenis_kelamin == 'Wanita' ? 'P' : '-');
                })
                ->addColumn('tempat_lahir', function ($row) {
                    return $row->tempat_lahir ?? '-';
                })
                ->addColumn('tanggal_lahir', function ($row) {
                    return $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('Y-m-d') : null;
                })
                ->addColumn('pangkat', function ($row) {
                    return $row->pangkat ?? '-';
                })
                ->addColumn('golongan', function ($row) {
                    return $row->kd_gol_sekarang ?? '-';
                })
                ->addColumn('tmt_golongan', function ($row) {
                    return $row->tmt_gol_sekarang ? Carbon::parse($row->tmt_gol_sekarang)->format('Y-m-d') : null;
                })
                ->addColumn('jab_fung', function ($row) {
                    return $row->jab_fung ?? '-';
                })
                ->addColumn('pendidikan', function ($row) {
                    return $row->jenjang_didik ?? '-';
                })
                ->addColumn('jurusan', function ($row) {
                    return $row->jurusan ?? '-';
                })
                ->addColumn('ruangan', function ($row) {
                    return $row->ruangan ?? '-';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value']) {
                        $query->where(function($q) use ($search) {
                            $q->where('nama', 'LIKE', '%' . $search . '%')
                              ->orWhere('nip_baru', 'LIKE', '%' . $search . '%')
                              ->orWhere('no_karpeg', 'LIKE', '%' . $search . '%')
                              ->orWhere('kd_karyawan', 'LIKE', '%' . $search . '%')
                              ->orWhere('tempat_lahir', 'LIKE', '%' . $search . '%')
                              ->orWhere('pangkat', 'LIKE', '%' . $search . '%')
                              ->orWhere('jab_fung', 'LIKE', '%' . $search . '%')
                              ->orWhere('jenjang_didik', 'LIKE', '%' . $search . '%')
                              ->orWhere('jurusan', 'LIKE', '%' . $search . '%')
                              ->orWhere('ruangan', 'LIKE', '%' . $search . '%');
                        });
                    }
                })
                ->order(function ($query) {
                    $query->orderBy('ruangan', 'ASC')
                          ->orderBy('jab_fung', 'ASC');
                })
                ->make(true);
        }

        return view('laporan.jabatan-fungsional.index');
    }
    
    public function print(Request $request)
    {
        $bulan = $request->bln;
        $tahun = $request->thn;
        
        // Array nama bulan
        $dataBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Tentukan query berdasarkan bulan dan tahun
        if ($bulan == date('m') && $tahun == date('Y')) {
            $data = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->whereNotNull('kd_jabfung')
                ->where('kd_jabfung', '!=', 0)
                ->where('status_peg', '1')
                ->where('kd_status_kerja', '1')
                ->where('kd_jab_1', '>=', 5)
                ->orderBy('ruangan', 'ASC')
                ->orderBy('jab_fung', 'ASC')
                ->get();
        } else {
            $data = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan_backup')
                ->whereNotNull('kd_jabfung')
                ->where('kd_jabfung', '!=', 0)
                ->where('status_peg', '1')
                ->where('kd_status_kerja', '1')
                ->where('kd_jab_1', '>=', 5)
                ->where('bulan_backup', $bulan)
                ->where('tahun_backup', $tahun)
                ->orderBy('ruangan', 'ASC')
                ->orderBy('jab_fung', 'ASC')
                ->get();
        }

        return view('laporan.jabatan-fungsional.print', compact('data', 'bulan', 'tahun', 'dataBulan'));
    }
    
    public function checkData(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        try {
            if ($bulan == date('m') && $tahun == date('Y')) {
                $count = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan')
                    ->whereNotNull('kd_jabfung')
                    ->where('kd_jabfung', '!=', 0)
                    ->where('status_peg', '1')
                    ->where('kd_status_kerja', '1')
                    ->where('kd_jab_1', '>=', 5)
                    ->count();
            } else {
                $count = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan_backup')
                    ->whereNotNull('kd_jabfung')
                    ->where('kd_jabfung', '!=', 0)
                    ->where('status_peg', '1')
                    ->where('kd_status_kerja', '1')
                    ->where('kd_jab_1', '>=', 5)
                    ->where('bulan_backup', $bulan)
                    ->where('tahun_backup', $tahun)
                    ->count();
            }

            return response()->json([
                'status' => 'success',
                'data_count' => $count,
                'message' => $count > 0 ? "Ditemukan {$count} data jabatan fungsional" : 'Tidak ada data ditemukan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
