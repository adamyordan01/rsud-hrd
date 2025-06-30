<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use App\Helpers\PhotoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class StrukturalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            
            // Tentukan query berdasarkan bulan dan tahun - tanpa orderBy untuk menghindari error SQL Server
            if (empty($bulan) || empty($tahun) || ($bulan == date('m') && $tahun == date('Y'))) {
                // Data terkini
                $query = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan')
                    ->whereNotNull('kd_jabatan_struktural')
                    ->where('kd_jabatan_struktural', '!=', 0)
                    ->where('status_peg', '1')
                    ->where('kd_jab_1', '<=', 4);
            } else {
                // Data backup/historis
                $query = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan_backup')
                    ->whereNotNull('kd_jabatan_struktural')
                    ->where('kd_jabatan_struktural', '!=', 0)
                    ->where('status_peg', '1')
                    ->where('kd_jab_1', '<=', 4)
                    ->where('bulan_backup', $bulan)
                    ->where('tahun_backup', $tahun);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('foto', function ($row) {
                    // jika tidak ada foto_square, gunakan foto default
                    // $photo = PhotoHelper::getPhotoUrl($row, 'foto_square');
                    if (empty($row->foto_square)) {
                        $type = 'foto';
                    } else {
                        $type = 'foto_square';
                    }
                    
                    $photo = PhotoHelper::getPhotoUrl($row, $type);

                    $photo = '<div class="symbol symbol-45px"><img src="' . $photo . '" alt="' . $row->kd_karyawan . '"></div>';
                    

                    return $photo;
                })
                ->addColumn('nama_lengkap', function ($row) {
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $row->nama . $gelarBelakang;
                    
                    $tglLahir = $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-';
                    $ttl = $row->tempat_lahir . ', ' . $tglLahir;
                    
                    return '<div class="fw-bold">' . $namaLengkap . '</div>' .
                           '<div class="text-muted fs-7">' . $ttl . '</div>' .
                           '<div class="text-muted fs-7">' . ($row->nip_baru ?? '-') . '</div>' .
                           '<div class="text-muted fs-7">' . ($row->no_karpeg ?? '-') . ' / ' . ($row->kd_karyawan ?? '-') . '</div>';
                })
                ->editColumn('jenis_kelamin', function($row) {
                    if ($row->jenis_kelamin == 'Pria') {
                        return 'L';
                    } elseif ($row->jenis_kelamin == 'Wanita') {
                        return 'P';
                    } else {
                        return '?';
                    }
                })
                ->addColumn('pangkat_sekarang', function ($row) {
                    $pangkat = ($row->pangkat ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-');
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
                ->addColumn('jabatan_struktural', function ($row) {
                    return '<div class="text-center">' . ($row->jab_struk ?? '-') . '</div>';
                })
                ->addColumn('tmt_jabatan_struktural', function ($row) {
                    $tmtJabStruk = $row->tmt_jabatan_struktural ? Carbon::parse($row->tmt_jabatan_struktural)->format('d-m-Y') : '-';
                    return '<div class="text-center">' . $tmtJabStruk . '</div>';
                })
                ->order(function ($query) {
                    // Terapkan ordering di sini setelah count selesai
                    $query->orderBy('kd_jabatan_struktural', 'ASC');
                })
                ->rawColumns(['foto', 'nama_lengkap', 'pangkat_sekarang', 'tmt_gol_sekarang', 'masa_kerja_thn', 'masa_kerja_bulan', 'eselon_info', 'tmt_eselon', 'jabatan_struktural', 'tmt_jabatan_struktural'])
                ->make(true);
        }

        return view('laporan.struktural.index');
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
                ->whereNotNull('kd_jabatan_struktural')
                ->where('kd_jabatan_struktural', '!=', 0)
                ->where('status_peg', '1')
                ->where('kd_jab_1', '<=', 4)
                ->orderBy('kd_jabatan_struktural', 'ASC')
                ->get();
        } else {
            $data = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan_backup')
                ->whereNotNull('kd_jabatan_struktural')
                ->where('kd_jabatan_struktural', '!=', 0)
                ->where('status_peg', '1')
                ->where('kd_jab_1', '<=', 4)
                ->where('bulan_backup', $bulan)
                ->where('tahun_backup', $tahun)
                ->orderBy('kd_jabatan_struktural', 'ASC')
                ->get();
        }

        return view('laporan.struktural.print', compact('data', 'bulan', 'tahun', 'dataBulan'));
    }

    public function checkData(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        if ($bulan == date('m') && $tahun == date('Y')) {
            $count = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->whereNotNull('kd_jabatan_struktural')
                ->where('kd_jabatan_struktural', '!=', 0)
                ->where('status_peg', '1')
                ->where('kd_jab_1', '<=', 4)
                ->count();
        } else {
            $count = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan_backup')
                ->whereNotNull('kd_jabatan_struktural')
                ->where('kd_jabatan_struktural', '!=', 0)
                ->where('status_peg', '1')
                ->where('kd_jab_1', '<=', 4)
                ->where('bulan_backup', $bulan)
                ->where('tahun_backup', $tahun)
                ->count();
        }

        return response()->json(['count' => $count]);
    }
}
