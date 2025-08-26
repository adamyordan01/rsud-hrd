<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class JenjangPendidikanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jenjang = $request->jenjang;
            $status = $request->status; // 1=PNS, 2=Honor, 3=Kontrak, 4=Part Time, 7=PPPK
            $jurusan = $request->jurusan;
            
            // Query utama dari view_tampil_karyawan
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('status_peg', '1'); // Pegawai aktif

            // Filter berdasarkan jenjang pendidikan
            if (!empty($jenjang)) {
                $query->where('jenjang_didik', $jenjang);
            }

            // Filter berdasarkan status pegawai
            if (!empty($status)) {
                // Handle multiple status selection
                $statusArray = is_array($status) ? $status : [$status];
                $statusArray = array_filter($statusArray); // Remove empty values
                
                if (!empty($statusArray)) {
                    $query->whereIn('kd_status_kerja', $statusArray);
                }
            }

            // Filter berdasarkan jurusan
            if (!empty($jurusan)) {
                $query->where('jurusan', $jurusan);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $row->nama . $gelarBelakang;
                    
                    $tglLahir = $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-';
                    $ttl = $row->tempat_lahir . ', ' . $tglLahir;
                    
                    // Tampilkan NIP atau ID Pegawai berdasarkan status
                    $identitas = '';
                    if ($row->kd_status_kerja == 1 || $row->kd_status_kerja == 7) {
                        // PNS - tampilkan NIP dan No KARPEG
                        $identitas = ($row->nip_baru ?? '-') . '<br>' . ($row->no_karpeg ?? '-') . ' / ' . ($row->kd_karyawan ?? '-');
                    } else {
                        // Non-PNS - hanya tampilkan ID Pegawai
                        $identitas = ($row->kd_karyawan ?? '-');
                    }
                    
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
                ->addColumn('pangkat_golongan', function ($row) use ($request) {
                    $status = $request->status;
                    $statusArray = is_array($status) ? $status : [$status];
                    
                    // Check if current employee's status is PNS or PPPK
                    if ($row->kd_status_kerja == 1 || $row->kd_status_kerja == 7) {
                        $pangkat = ($row->pangkat ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-');
                        return '<div class="text-center">' . $pangkat . '</div>';
                    } else {
                        return '<div class="text-center">-</div>';
                    }
                })
                ->addColumn('tmt_pangkat', function ($row) use ($request) {
                    $status = $request->status;
                    $statusArray = is_array($status) ? $status : [$status];
                    
                    // Check if current employee's status is PNS or PPPK
                    if ($row->kd_status_kerja == 1 || $row->kd_status_kerja == 7) {
                        $tmtGol = $row->tmt_gol_sekarang ? Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                        return '<div class="text-center">' . $tmtGol . '</div>';
                    } else {
                        return '<div class="text-center">-</div>';
                    }
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
                ->addColumn('masa_kerja_thn', function ($row) use ($request) {
                    $status = $request->status;
                    $statusArray = is_array($status) ? $status : [$status];
                    
                    // Check if current employee's status is PNS or PPPK
                    if ($row->kd_status_kerja == 1 || $row->kd_status_kerja == 7) {
                        $tahun = $row->masa_kerja_thn ?? '0';
                        return '<div class="text-center">' . $tahun . '</div>';
                    } else {
                        return '<div class="text-center">-</div>';
                    }
                })
                ->addColumn('masa_kerja_bln', function ($row) use ($request) {
                    $status = $request->status;
                    $statusArray = is_array($status) ? $status : [$status];
                    
                    // Check if current employee's status is PNS or PPPK
                    if ($row->kd_status_kerja == 1 || $row->kd_status_kerja == 7) {
                        $bulan = $row->masa_kerja_bulan ?? '0';
                        return '<div class="text-center">' . $bulan . '</div>';
                    } else {
                        return '<div class="text-center">-</div>';
                    }
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

        // Ambil data jenjang pendidikan untuk dropdown
        $jenjangPendidikan = DB::connection('sqlsrv')
            ->table('hrd_jenjang_pendidikan')
            ->orderBy('nilaiIndex', 'desc')
            ->get();

        return view('laporan.jenjang-pendidikan.index', compact('jenjangPendidikan'));
    }
    
    public function getJurusan(Request $request)
    {
        try {
            $jenjang = $request->jenjang;
            
            if (empty($jenjang)) {
                return response()->json([]);
            }
            
            // Ambil semua jurusan yang ada untuk jenjang tersebut dari data aktual pegawai
            $jurusan = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('status_peg', '1')
                ->where('jenjang_didik', $jenjang)
                ->whereNotNull('jurusan')
                ->where('jurusan', '!=', '')
                ->distinct()
                ->select('jurusan')
                ->orderBy('jurusan', 'ASC')
                ->get();

            return response()->json($jurusan);
        } catch (\Exception $e) {
            Log::error('Error in JenjangPendidikanController@getJurusan: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data jurusan'], 500);
        }
    }
    
    public function print(Request $request)
    {
        $jenjang = $request->jenjang;
        $jurusan = $request->jurusan;
        $status = $request->status;
        
        // Query utama
        $query = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('status_peg', '1');
        
        // Filter berdasarkan jenjang pendidikan
        if (!empty($jenjang)) {
            $query->where('jenjang_didik', $jenjang);
        }
        
        // Filter berdasarkan jurusan
        if (!empty($jurusan)) {
            $query->where('jurusan', $jurusan);
        }
        
        // Filter berdasarkan status kerja
        if (!empty($status)) {
            $statusArray = is_array($status) ? $status : explode(',', $status);
            $statusArray = array_filter($statusArray); // Remove empty values
            if (!empty($statusArray)) {
                $query->whereIn('kd_status_kerja', $statusArray);
            }
        }
        
        // Urutkan data
        $data = $query->orderBy('nama', 'ASC')->get();
        
        // Tentukan nama jenjang untuk header
        $jenjangNama = !empty($jenjang) ? $jenjang : 'Semua Jenjang';
        
        // Tentukan nama jurusan untuk header
        $jurusanNama = !empty($jurusan) ? $jurusan : 'Semua Jurusan';
        
        // Tentukan nama status untuk header
        $statusNama = 'Semua Pegawai';
        if (!empty($status)) {
            $statusArray = is_array($status) ? $status : explode(',', $status);
            $statusArray = array_filter($statusArray);
            
            if (!empty($statusArray)) {
                $statusData = [
                    '1' => 'PNS',
                    '2' => 'Honor', 
                    '3' => 'Kontrak',
                    '4' => 'Part Time',
                    '7' => 'PPPK'
                ];
                
                $namaStatusList = [];
                foreach ($statusArray as $s) {
                    if (isset($statusData[$s])) {
                        $namaStatusList[] = $statusData[$s];
                    }
                }
                
                if (!empty($namaStatusList)) {
                    $statusNama = implode(', ', $namaStatusList);
                }
            }
        }

        return view('laporan.jenjang-pendidikan.print', compact('data', 'jenjangNama', 'jurusanNama', 'statusNama'));
    }
    
    public function checkData(Request $request)
    {
        $jenjang = $request->jenjang;
        $jurusan = $request->jurusan;
        $statusKerja = $request->status_kerja;
        
        $query = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('status_peg', '1');
        
        // Filter berdasarkan jenjang pendidikan
        if (!empty($jenjang)) {
            $query->where('kd_pendidikan_terakhir', $jenjang);
        }
        
        // Filter berdasarkan jurusan
        if (!empty($jurusan) && $jurusan != '000') {
            $query->where('kd_jurusan', $jurusan);
        }
        
        // Filter berdasarkan status kerja
        if (!empty($statusKerja) && is_array($statusKerja)) {
            $query->whereIn('kd_status_kerja', $statusKerja);
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }
}
