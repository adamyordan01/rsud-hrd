<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class PnsUsiaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $awal = $request->awal;
            $akhir = $request->akhir;
            
            if (empty($awal) || empty($akhir)) {
                return response()->json(['data' => []]);
            }
            
            // Query untuk mendapatkan data PNS berdasarkan rentang usia - tanpa orderBy untuk menghindari error SQL Server
            $query = DB::connection('sqlsrv')
                ->table('view_duk as v')
                ->selectRaw('v.*, DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) as umur')
                ->whereRaw("DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) >= ?", [$awal])
                ->whereRaw("DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) <= ?", [$akhir]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $row->nama . $gelarBelakang;
                    
                    $tglLahir = $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-';
                    $ttl = $row->tempat_lahir . ', ' . $tglLahir;
                    
                    return '<div class="fw-bold">' . $namaLengkap . '</div>' .
                        '<div class="text-muted fs-7">' . $ttl . '</div>' .
                        '<div class="text-muted fs-7">' . ($row->nip_baru ?? '-') . '</div>' .
                        '<div class="text-muted fs-7">' . ($row->no_karpeg ?? '-') . '</div>';
                })
                ->editColumn('jenis_kelamin', function($row) {
                    return $row->jenis == 'Pria' ? 'L' : 'P';
                })
                ->addColumn('usia', function($row) {
                    return '<div class="text-center fw-bold">' . $row->umur . '</div>';
                })
                ->addColumn('pangkat_cpns', function ($row) {
                    $pangkat = ($row->pangkat_masuk ?? '-') . ' / ' . ($row->kd_gol_masuk ?? '-');
                    return '<div class="text-center">' . $pangkat . '</div>';
                })
                ->addColumn('tmt_cpns', function ($row) {
                    $tmtCpns = $row->tmt_gol_masuk ? Carbon::parse($row->tmt_gol_masuk)->format('d-m-Y') : '-';
                    return '<div class="text-center">' . $tmtCpns . '</div>';
                })
                ->addColumn('pangkat_sekarang', function ($row) {
                    $pangkat = ($row->pangkat_sekarang ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-');
                    return '<div class="text-center">' . $pangkat . '</div>';
                })
                ->addColumn('tmt_gol_sekarang', function ($row) {
                    $tmtGol = $row->tmt_gol_sekarang ? Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                    return '<div class="text-center">' . $tmtGol . '</div>';
                })
                ->addColumn('masa_kerja_thn', function ($row) {
                    return '<div class="text-center">' . ($row->masa_kerja_thn ?? '0') . '</div>';
                })
                ->addColumn('masa_kerja_bulan', function ($row) {
                    return '<div class="text-center">' . ($row->masa_kerja_bulan ?? '0') . '</div>';
                })
                ->addColumn('eselon_info', function ($row) {
                    $eselon = $row->eselon ?? '-';
                    return '<div class="text-center">' . $eselon . '</div>';
                })
                ->addColumn('tmt_eselon', function ($row) {
                    $tmtEselon = $row->tmt_eselon ? Carbon::parse($row->tmt_eselon)->format('d-m-Y') : '-';
                    return '<div class="text-center">' . $tmtEselon . '</div>';
                })
                ->addColumn('pendidikan', function ($row) {
                    $pendidikan = $row->jenjang_didik ?? '-';
                    $jurusan = $row->jurusan ?? '-';
                    
                    return '<div class="text-center">' . $pendidikan . '</div>' .
                        '<div class="text-center text-muted fs-7">' . $jurusan . '</div>';
                })
                ->addColumn('tahun_lulus', function ($row) {
                    return '<div class="text-center">' . ($row->tahun_lulus ?? '-') . '</div>';
                })
                ->order(function ($query) {
                    // Terapkan ordering di sini setelah count selesai
                    $query->orderByRaw('DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) DESC');
                })
                ->rawColumns(['nama_lengkap', 'usia', 'pangkat_cpns', 'tmt_cpns', 'pangkat_sekarang', 'tmt_gol_sekarang', 'masa_kerja_thn', 'masa_kerja_bulan', 'eselon_info', 'tmt_eselon', 'pendidikan', 'tahun_lulus'])
                ->make(true);
        }

        return view('laporan.pns-usia.index');
    }

    public function print(Request $request)
    {
        $awal = $request->awal;
        $akhir = $request->akhir;
        
        if (empty($awal) || empty($akhir)) {
            return redirect()->back()->with('error', 'Rentang usia harus diisi');
        }

        // Query untuk mendapatkan data PNS berdasarkan rentang usia
        $data = DB::connection('sqlsrv')
            ->table('view_duk as v')
            ->selectRaw('v.*, DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) as umur')
            ->whereRaw("DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) >= ?", [$awal])
            ->whereRaw("DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) <= ?", [$akhir])
            ->orderByRaw('DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) DESC')
            ->get();

        // Get direktur info
        $direktur = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('kd_jabatan_struktural', 1)
            ->where('status_peg', 1)
            ->first();

        return view('laporan.pns-usia.print', compact('data', 'awal', 'akhir', 'direktur'));
    }

    public function checkData(Request $request)
    {
        $awal = $request->awal;
        $akhir = $request->akhir;
        
        if (empty($awal) || empty($akhir)) {
            return response()->json(['count' => 0]);
        }
        
        $count = DB::connection('sqlsrv')
            ->table('view_duk as v')
            ->whereRaw("DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) >= ?", [$awal])
            ->whereRaw("DATEDIFF(year, v.tgl_lahir, CAST(GETDATE() AS DATE)) <= ?", [$akhir])
            ->count();

        return response()->json(['count' => $count]);
    }
}