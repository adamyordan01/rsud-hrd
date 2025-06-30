<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DaftarUrutKepangakatanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            
            // Tentukan query berdasarkan bulan dan tahun
            if (empty($bulan) || empty($tahun) || ($bulan == date('m') && $tahun == date('Y'))) {
                // Data terkini - menggunakan query builder tanpa orderBy untuk count
                $query = DB::connection('sqlsrv')->table('view_duk');
            } else {
                // Data backup/historis - menggunakan query builder tanpa orderBy untuk count
                $query = DB::connection('sqlsrv')
                    ->table('view_duk_backup')
                    ->where('bulan_backup', $bulan)
                    ->where('tahun_backup', $tahun);
            }

            return DataTables::of($query)
                // ->addColumn('nomor', function ($row) {
                //     static $no = 0;
                //     return ++$no;
                // })
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
                ->addColumn('pangkat_cpns', function ($row) {
                    $pangkat = ($row->pangkat_masuk ?? '-') . ' / ' . ($row->kd_gol_masuk ?? '-');
                    $tmtGolMasuk = $row->tmt_gol_masuk ? Carbon::parse($row->tmt_gol_masuk)->format('d-m-Y') : '-';
                    
                    return '<div class="text-center">' . $pangkat . '</div>';
                })
                ->addColumn('tmt_cpns', function ($row) {
                    $tmtCpns = $row->tmt_gol_masuk ? Carbon::parse($row->tmt_gol_masuk)->format('d-m-Y') : '-';
                    return '<div class="text-center">' . $tmtCpns . '</div>';
                })
                ->addColumn('pangkat_sekarang', function ($row) {
                    $pangkat = ($row->pangkat_sekarang ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-');
                    $tmtGol = $row->tmt_gol_sekarang ? Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                    
                    return '<div class="text-center">' . $pangkat . '</div>';
                })
                ->addColumn('tmt_gol_sekarang', function ($row) {
                    $tmtGol = $row->tmt_gol_sekarang ? Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                    return '<div class="text-center">' . $tmtGol . '</div>';
                })
                ->addColumn('masa_kerja', function ($row) {
                    return '<div class="text-center">' . ($row->masa_kerja_thn ?? '0') . ' Thn</div>' .
                           '<div class="text-center">' . ($row->masa_kerja_bulan ?? '0') . ' Bln</div>';
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
                    $tahunLulus = $row->tahun_lulus ?? '-';
                    
                    return '<div class="text-center">' . $pendidikan . '</div>' .
                           '<div class="text-center text-muted fs-7">' . $jurusan . '</div>';
                })
                ->addColumn('tahun_lulus', function ($row) {
                    return '<div class="text-center">' . ($row->tahun_lulus ?? '-') . '</div>';
                })
                ->order(function ($query) use ($bulan, $tahun) {
                    // Terapkan ordering di sini setelah count selesai
                    if ($bulan == date('m') && $tahun == date('Y')) {
                        $query->orderBy('kd_gol_sekarang', 'DESC')
                              ->orderBy('eselon', 'DESC')
                              ->orderBy('masa_kerja_thn', 'DESC')
                              ->orderBy('masa_kerja_bulan', 'DESC')
                              ->orderBy('nilaiindex', 'DESC');
                    } else {
                        $query->orderBy('kd_gol_sekarang', 'DESC')
                              ->orderBy('eselon', 'DESC')
                              ->orderBy('masa_kerja_thn', 'DESC')
                              ->orderBy('masa_kerja_bulan', 'DESC')
                              ->orderBy('nilaiindex', 'DESC');
                    }
                })
                ->rawColumns(['nama_lengkap', 'pangkat_cpns', 'tmt_cpns', 'pangkat_sekarang', 'tmt_gol_sekarang', 'masa_kerja', 'masa_kerja_thn', 'masa_kerja_bulan', 'eselon_info', 'tmt_eselon', 'pendidikan', 'tahun_lulus'])
                ->make(true);
        }

        return view('laporan.duk.index');
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
                ->table('view_duk')
                ->orderBy('kd_gol_sekarang', 'DESC')
                ->orderBy('eselon', 'DESC')
                ->orderBy('masa_kerja_thn', 'DESC')
                ->orderBy('masa_kerja_bulan', 'DESC')
                ->orderBy('nilaiindex', 'DESC')
                ->get();
        } else {
            $data = DB::connection('sqlsrv')
                ->table('view_duk_backup')
                ->where('bulan_backup', $bulan)
                ->where('tahun_backup', $tahun)
                ->orderBy('kd_gol_sekarang', 'DESC')
                ->orderBy('eselon', 'DESC')
                ->orderBy('masa_kerja_thn', 'DESC')
                ->orderBy('masa_kerja_bulan', 'DESC')
                ->orderBy('nilaiindex', 'DESC')
                ->get();
        }

        // Get direktur info
        $direktur = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('kd_jabatan_struktural', 1)
            ->where('status_peg', 1)
            ->first();

        return view('laporan.duk.print', compact('data', 'bulan', 'tahun', 'dataBulan', 'direktur'));
    }

    public function checkData(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        if ($bulan == date('m') && $tahun == date('Y')) {
            $count = DB::connection('sqlsrv')->table('view_duk')->count();
        } else {
            $count = DB::connection('sqlsrv')
                ->table('view_duk_backup')
                ->where('bulan_backup', $bulan)
                ->where('tahun_backup', $tahun)
                ->count();
        }

        return response()->json(['count' => $count]);
    }
}
