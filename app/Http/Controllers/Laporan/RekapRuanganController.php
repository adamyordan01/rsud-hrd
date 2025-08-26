<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use App\Helpers\PhotoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class RekapRuanganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $ruangan = $request->ruangan; // *, x, atau kode ruangan spesifik
            
            // Tentukan query berdasarkan bulan dan tahun
            if (empty($bulan) || empty($tahun) || ($bulan == date('m') && $tahun == date('Y'))) {
                // Data terkini
                $query = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan')
                    ->where('status_peg', '1'); // Pegawai aktif
            } else {
                // Data backup/historis
                $query = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan_backup')
                    ->where('status_peg', '1') // Pegawai aktif
                    ->where('bulan_backup', $bulan)
                    ->where('tahun_backup', $tahun);
            }

            // Filter berdasarkan ruangan
            if (!empty($ruangan)) {
                if ($ruangan == '*') {
                    // Semua ruangan - tampilkan yang memiliki ruangan
                    $query->whereNotNull('ruangan');
                } elseif ($ruangan == 'x') {
                    // Tidak ada ruangan - tampilkan yang ruangannya null
                    $query->whereNull('ruangan');
                } else {
                    // Ruangan spesifik
                    $query->where('kd_ruangan', $ruangan);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $row->nama . $gelarBelakang;
                    
                    $tglLahir = $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-';
                    $ttl = $row->tempat_lahir . ', ' . $tglLahir;
                    
                    // Tampilkan NIP dan No KARPEG untuk PNS
                    $identitas = '';
                    if ($row->kd_status_kerja == 1 || $row->kd_status_kerja == 7) {
                        // PNS dan PPPK - tampilkan NIP dan No KARPEG
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
                ->addColumn('status_kerja', function ($row) {
                    return '<div class="text-center">' . ($row->status_kerja ?? '-') . '</div>';
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
                              ->orWhere('jenis_tenaga', 'LIKE', '%' . $search . '%')
                              ->orWhere('no_ktp', 'LIKE', '%' . $search . '%')
                              ->orWhere('no_askes', 'LIKE', '%' . $search . '%')
                              ->orWhere('jenjang_didik', 'LIKE', '%' . $search . '%')
                              ->orWhere('jurusan', 'LIKE', '%' . $search . '%')
                              ->orWhere('status_kerja', 'LIKE', '%' . $search . '%')
                              ->orWhere('sub_detail', 'LIKE', '%' . $search . '%');
                        });
                    }
                })
                ->order(function ($query) use ($request) {
                    $ruangan = $request->ruangan;
                    if ($ruangan == '*') {
                        // Untuk semua ruangan, urutkan berdasarkan ruangan, status kerja, dll
                        $query->orderBy('ruangan', 'ASC')
                              ->orderBy('kd_status_kerja', 'ASC')
                              ->orderBy('nilaiIndex', 'DESC')
                              ->orderBy('tahun_lulus', 'ASC')
                              ->orderBy('nama', 'ASC');
                    } else {
                        // Untuk ruangan spesifik atau tanpa ruangan
                        $query->orderBy('kd_status_kerja', 'ASC')
                              ->orderBy('nilaiIndex', 'DESC')
                              ->orderBy('tahun_lulus', 'ASC')
                              ->orderBy('nama', 'ASC');
                    }
                })
                ->rawColumns(['nama_lengkap', 'nik_askes', 'jenjang_pendidikan', 'program_studi', 'tahun_lulus', 'jenis_tenaga', 'sub_jenis_ruangan', 'status_kerja'])
                ->make(true);
        }

        // Ambil data ruangan untuk dropdown
        $ruangans = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->orderBy('ruangan')
            ->get();

        return view('laporan.rekap-ruangan.index', compact('ruangans'));
    }
    
    public function print(Request $request)
    {
        $bulan = $request->bln;
        $tahun = $request->thn;
        $ruangan = $request->ruang;
        
        // Array nama bulan
        $dataBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Tentukan query berdasarkan bulan dan tahun
        if ($bulan == date('m') && $tahun == date('Y')) {
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('status_peg', '1');
        } else {
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan_backup')
                ->where('status_peg', '1')
                ->where('bulan_backup', $bulan)
                ->where('tahun_backup', $tahun);
        }

        // Filter berdasarkan ruangan
        if (!empty($ruangan)) {
            if ($ruangan == '*') {
                $query->whereNotNull('ruangan');
            } elseif ($ruangan == 'x') {
                $query->whereNull('ruangan');
            } else {
                $query->where('kd_ruangan', $ruangan);
            }
        }

        // Tentukan ordering berdasarkan parameter ruangan
        if ($ruangan == '*') {
            // Untuk semua ruangan
            $data = $query->orderBy('ruangan', 'ASC')
                         ->orderBy('kd_status_kerja', 'ASC')
                         ->orderBy('nilaiIndex', 'DESC')
                         ->orderBy('tahun_lulus', 'ASC')
                         ->orderBy('nama', 'ASC')
                         ->get();
        } else {
            // Untuk ruangan spesifik atau tanpa ruangan
            $data = $query->orderBy('kd_status_kerja', 'ASC')
                         ->orderBy('nilaiIndex', 'DESC')
                         ->orderBy('tahun_lulus', 'ASC')
                         ->orderBy('nama', 'ASC')
                         ->get();
        }

        // Ambil nama ruangan untuk header
        $namaRuangan = '';
        if ($ruangan == '*') {
            $namaRuangan = 'SEMUA RUANGAN';
        } elseif ($ruangan == 'x') {
            $namaRuangan = 'TIDAK ADA RUANGAN';
        } else {
            $ruanganData = DB::connection('sqlsrv')
                ->table('hrd_ruangan')
                ->where('kd_ruangan', $ruangan)
                ->first();
            $namaRuangan = $ruanganData ? $ruanganData->ruangan : 'RUANGAN TIDAK DITEMUKAN';
        }

        return view('laporan.rekap-ruangan.print', compact('data', 'bulan', 'tahun', 'ruangan', 'dataBulan', 'namaRuangan'));
    }
    
    public function checkData(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $ruangan = $request->ruangan;
        
        if ($bulan == date('m') && $tahun == date('Y')) {
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('status_peg', '1');
        } else {
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan_backup')
                ->where('status_peg', '1')
                ->where('bulan_backup', $bulan)
                ->where('tahun_backup', $tahun);
        }

        // Filter berdasarkan ruangan
        if (!empty($ruangan)) {
            if ($ruangan == '*') {
                $query->whereNotNull('ruangan');
            } elseif ($ruangan == 'x') {
                $query->whereNull('ruangan');
            } else {
                $query->where('kd_ruangan', $ruangan);
            }
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }
}
