<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class GolonganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $golongan = $request->golongan;
            
            // Query utama dari view_tampil_karyawan untuk PNS dan PPPK saja
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('status_peg', '1') // Pegawai aktif
                ->whereIn('kd_status_kerja', [1, 7]); // 1=PNS, 7=PPPK

            // Filter berdasarkan golongan
            if (!empty($golongan)) {
                $query->where('kd_gol_sekarang', $golongan);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $row->nama . $gelarBelakang;
                    
                    $tglLahir = $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-';
                    $ttl = $row->tempat_lahir . ', ' . $tglLahir;
                    
                    // Tampilkan NIP dan No KARPEG untuk PNS/PPPK
                    $identitas = ($row->nip_baru ?? '-') . '<br>' . ($row->no_karpeg ?? '-') . ' / ' . ($row->kd_karyawan ?? '-');
                    
                    return '<div class="fw-bold">' . $namaLengkap . '</div>' .
                           '<div class="text-muted fs-7">' . $ttl . '</div>' .
                           '<div class="text-muted fs-7">' . $identitas . '</div>';
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
                ->addColumn('pangkat_golongan', function ($row) {
                    $pangkat = ($row->pangkat ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-');
                    return '<div class="text-center">' . $pangkat . '</div>';
                })
                ->addColumn('tmt_pangkat', function ($row) {
                    $tmtGol = $row->tmt_gol_sekarang ? Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                    return '<div class="text-center">' . $tmtGol . '</div>';
                })
                ->addColumn('nik_askes', function ($row) {
                    $nik = $row->no_ktp ? '<u>' . $row->no_ktp . '</u>' : '-';
                    $askes = $row->no_askes ? '<br>' . $row->no_askes : '';
                    return '<div class="text-left">' . $nik . $askes . '</div>';
                })
                ->addColumn('jenjang_pendidikan', function ($row) {
                    return '<div class="text-center">' . ($row->jenjang_didik ?? '-') . '</div>';
                })
                ->addColumn('program_studi', function ($row) {
                    return '<div class="text-center">' . ($row->jurusan ?? '-') . '</div>';
                })
                ->addColumn('tahun_lulus', function ($row) {
                    return '<div class="text-center">' . ($row->tahun_lulus ?? '-') . '</div>';
                })
                ->addColumn('jenis_tenaga', function ($row) {
                    return '<div class="text-center">' . ($row->jenis_tenaga ?? '-') . '</div>';
                })
                ->addColumn('sub_jenis_ruangan', function ($row) {
                    $subDetail = $row->sub_detail ? 'Tenaga ' . $row->sub_detail : '-';
                    $ruangan = $row->ruangan ? '<br>Pada ' . $row->ruangan : '';
                    return '<div class="text-center" style="font-size: 8pt; text-transform: uppercase;">' . $subDetail . $ruangan . '</div>';
                })
                ->addColumn('masa_kerja_thn', function ($row) {
                    $tahun = $row->masa_kerja_thn ?? '0';
                    return '<div class="text-center">' . $tahun . '</div>';
                })
                ->addColumn('masa_kerja_bln', function ($row) {
                    $bulan = $row->masa_kerja_bulan ?? '0';
                    return '<div class="text-center">' . $bulan . '</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value']) {
                        $query->where(function($q) use ($search) {
                            $q->whereRaw("LOWER(CONCAT(ISNULL(gelar_depan,''), ' ', nama, ' ', ISNULL(gelar_belakang,''))) LIKE ?", ['%' . strtolower($search) . '%'])
                              ->orWhere('nama', 'LIKE', '%' . $search . '%')
                              ->orWhere('nip_baru', 'LIKE', '%' . $search . '%')
                              ->orWhere('no_karpeg', 'LIKE', '%' . $search . '%')
                              ->orWhere('kd_karyawan', 'LIKE', '%' . $search . '%')
                              ->orWhere('ruangan', 'LIKE', '%' . $search . '%')
                              ->orWhere('jurusan', 'LIKE', '%' . $search . '%');
                        });
                    }
                })
                ->order(function ($query) {
                    // Order berdasarkan nama
                    $query->orderBy('nama', 'ASC');
                })
                ->rawColumns(['nama_lengkap', 'nik_askes', 'pangkat_golongan', 'tmt_pangkat', 'masa_kerja_thn', 'masa_kerja_bln', 'jenjang_pendidikan', 'program_studi', 'tahun_lulus', 'jenis_tenaga', 'sub_jenis_ruangan'])
                ->make(true);
        }

        // Ambil data golongan untuk dropdown
        $golonganList = DB::connection('sqlsrv')
            ->table('hrd_golongan')
            ->orderBy('kd_gol', 'desc')
            ->get();

        return view('laporan.golongan.index', compact('golonganList'));
    }
    
    public function print(Request $request)
    {
        $golongan = $request->golongan;
        
        // Query utama untuk PNS dan PPPK saja
        $query = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('status_peg', '1')
            ->whereIn('kd_status_kerja', [1, 7]); // 1=PNS, 7=PPPK
        
        // Filter berdasarkan golongan
        if (!empty($golongan)) {
            $query->where('kd_gol_sekarang', $golongan);
        }
        
        // Urutkan data
        $data = $query->orderBy('nama', 'ASC')->get();
        
        // Tentukan nama golongan untuk header
        $golonganNama = 'Semua Golongan';
        if (!empty($golongan)) {
            $golonganData = DB::connection('sqlsrv')
                ->table('hrd_golongan')
                ->where('kd_gol', $golongan)
                ->first();
            $golonganNama = $golonganData ? $golonganData->kd_gol . ' - ' . $golonganData->pangkat : $golongan;
        }

        return view('laporan.golongan.print', compact('data', 'golonganNama'));
    }
}
