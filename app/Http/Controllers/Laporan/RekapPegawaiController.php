<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use App\Helpers\PhotoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class RekapPegawaiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $status = $request->status; // 1=PNS, 2=Non-PNS, 7=PPPK
            
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

            // Filter berdasarkan status pegawai
            if (!empty($status)) {
                if ($status == '1') {
                    // PNS
                    $query->where('kd_status_kerja', 1);
                } elseif ($status == '2') {
                    // Honor
                    $query->where('kd_status_kerja', 2);
                } elseif ($status == '3') {
                    // Kontrak
                    $query->where('kd_status_kerja', 3);
                } elseif ($status == '4') {
                    // Part Time
                    $query->where('kd_status_kerja', 4);
                } elseif ($status == '7') {
                    // PPPK
                    $query->where('kd_status_kerja', 7);
                }
                // Jika status kosong, tampilkan semua pegawai aktif
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('foto', function ($row) {
                    // Gunakan foto_square jika ada, jika tidak gunakan foto default
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
                    if ($status == '1' || $status == '7') {
                        // PNS dan PPPK - tampilkan pangkat dan golongan
                        $pangkat = ($row->pangkat ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-');
                        return '<div class="text-center">' . $pangkat . '</div>';
                    } else {
                        // Honor, Kontrak, Part Time - tidak ada pangkat
                        return '<div class="text-center">-</div>';
                    }
                })
                ->addColumn('tmt_pangkat', function ($row) use ($request) {
                    $status = $request->status;
                    if ($status == '1' || $status == '7') {
                        // PNS dan PPPK
                        $tmtGol = $row->tmt_gol_sekarang ? Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                        return '<div class="text-center">' . $tmtGol . '</div>';
                    } else {
                        // Honor, Kontrak, Part Time
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
                    if ($status == '1' || $status == '7') {
                        // PNS dan PPPK - tampilkan tahun masa kerja
                        $tahun = $row->masa_kerja_thn ?? '0';
                        return '<div class="text-center">' . $tahun . '</div>';
                    } else {
                        // Honor, Kontrak, Part Time - tidak ada masa kerja
                        return '<div class="text-center">-</div>';
                    }
                })
                ->addColumn('masa_kerja_bln', function ($row) use ($request) {
                    $status = $request->status;
                    if ($status == '1' || $status == '7') {
                        // PNS dan PPPK - tampilkan bulan masa kerja
                        $bulan = $row->masa_kerja_bulan ?? '0';
                        return '<div class="text-center">' . $bulan . '</div>';
                    } else {
                        // Honor, Kontrak, Part Time - tidak ada masa kerja
                        return '<div class="text-center">-</div>';
                    }
                })
                ->addColumn('keterangan', function ($row) {
                    $ket = $row->keterangan ?? '-';
                    return '<div class="text-center">' . $ket . '</div>';
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
                              ->orWhere('pendidikan_terakhir', 'LIKE', '%' . $search . '%')
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

        return view('laporan.rekap-pegawai.index');
    }
    
    public function print(Request $request)
    {
        $bulan = $request->bln;
        $tahun = $request->thn;
        $status = $request->status;
        
        // Array nama bulan
        $dataBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Array nama status
        $statusNama = [
            '' => 'Semua Pegawai',
            '1' => 'PNS',
            '2' => 'Honor', 
            '3' => 'Kontrak',
            '4' => 'Part Time',
            '7' => 'PPPK'
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

        // Filter berdasarkan status pegawai
        if (!empty($status)) {
            if ($status == '1') {
                $query->where('kd_status_kerja', 1);
            } elseif ($status == '2') {
                // Honor
                $query->where('kd_status_kerja', 2);
            } elseif ($status == '3') {
                // Kontrak
                $query->where('kd_status_kerja', 3);
            } elseif ($status == '4') {
                // Part Time
                $query->where('kd_status_kerja', 4);
            } elseif ($status == '7') {
                // PPPK
                $query->where('kd_status_kerja', 7);
            }
        }

        $data = $query->orderBy('ruangan', 'ASC')
                     ->orderBy('nilaiIndex', 'DESC')
                     ->orderBy('tahun_lulus', 'ASC')
                     ->orderBy('nama', 'ASC')
                     ->get();

        return view('laporan.rekap-pegawai.print', compact('data', 'bulan', 'tahun', 'status', 'dataBulan', 'statusNama'));
    }
    
    public function checkData(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $status = $request->status;
        
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

        // Filter berdasarkan status pegawai
        if (!empty($status)) {
            if ($status == '1') {
                $query->where('kd_status_kerja', 1);
            } elseif ($status == '2') {
                // Honor
                $query->where('kd_status_kerja', 2);
            } elseif ($status == '3') {
                // Kontrak
                $query->where('kd_status_kerja', 3);
            } elseif ($status == '4') {
                // Part Time
                $query->where('kd_status_kerja', 4);
            } elseif ($status == '7') {
                // PPPK
                $query->where('kd_status_kerja', 7);
            }
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }
}
