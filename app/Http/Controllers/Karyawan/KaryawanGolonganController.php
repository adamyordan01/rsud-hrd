<?php

namespace App\Http\Controllers\Karyawan;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class KaryawanGolonganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $karyawan = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('status_peg', '1');

            // Filter berdasarkan golongan
            if ($request->has('golongan') && !empty($request->golongan)) {
                $karyawan->where('kd_gol_sekarang', $request->golongan);
            }

            return DataTables::of($karyawan)
                ->addColumn('id_pegawai', function ($row) {
                    $kd_karyawan = '<span class="fw-bold text-dark" style="font-size: 12px;">' . $row->kd_karyawan . '</span>';
                    
                    $photo = $row->foto_square 
                        ? '<div class="symbol symbol-45px"><img src="' . url(str_replace('public', 'storage', $row->foto_square)) . '" alt=""></div>'
                        : ($row->foto && (Str::startsWith($row->foto, 'rsud_') || $row->foto === 'user.png') 
                            ? '<div class="symbol symbol-45px"><img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $row->foto . '" alt=""></div>'
                            : '<div class="symbol symbol-45px"><img src="https://ui-avatars.com/api/?name=' . $row->nama . '&color=7F9CF5&background=EBF4FF" alt=""></div>'
                        );

                    return $kd_karyawan . '<br>' . $photo;
                })
                ->addColumn('nama_lengkap', function ($row) {
                    $nama = $row->nama;
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $nama . $gelarBelakang;
                    $namaBold = '<span style="font-size: 12px; font-weight: bold;">' . $namaLengkap . '</span>';
                    $tanggal_lahir = Carbon::parse($row->tgl_lahir)->format('d-m-Y');
                    $ttl = $row->tempat_lahir . ', ' . $tanggal_lahir;
                    return $namaBold . '<br>' . $ttl . '<br>' . $row->nip_baru . '<br>' . $row->no_karpeg;
                })
                ->editColumn('jenis_kelamin', function($row) {
                    if($row->jenis_kelamin == 'Wanita') {
                        return 'P';
                    } else if($row->jenis_kelamin == 'Pria') {
                        return 'L';
                    } else {
                        return '?';
                    }
                })
                ->addColumn('golongan', function ($row) {
                    $pangkat = $row->pangkat . ' / ' . $row->kd_gol_sekarang;
                    $tmtGol = $row->tmt_gol_sekarang ? Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                    return $pangkat . '<br>' . $tmtGol;
                })
                ->addColumn('masa_kerja', function ($row) {
                    return '<div style="text-align:center;">' . ($row->masa_kerja_thn ?? '-') . '</div>';
                })
                ->addColumn('masa_kerja_bulan', function ($row) {
                    return '<div style="text-align:center;">' . ($row->masa_kerja_bulan ?? '-') . '</div>';
                })
                ->addColumn('eselon', function ($row) {
                    $eselon = $row->eselon ?? '-';
                    $tmtEselon = $row->tmt_eselon ? Carbon::parse($row->tmt_eselon)->format('d-m-Y') : '';
                    return $eselon . '<br>' . $tmtEselon;
                })
                ->addColumn('pendidikan', function ($row) {
                    return $row->jenjang_didik . '<br>' . $row->jurusan . '<br>Lulus Thn. ' . $row->tahun_lulus;
                })
                ->addColumn('sub_detail', function ($row) {
                    $eselon = $row->eselon;
                    $jenisTenaga = ($eselon == '-' || $eselon == null || $eselon == '') 
                        ? 'Tenaga ' . $row->sub_detail 
                        : 'TENAGA MANAJEMEN';
                    return strtoupper($jenisTenaga) . '<br>Pada ' . $row->ruangan;
                })
                ->addColumn('status_kerja', function($row) {
                    return $row->status_kerja ?? '-';
                })
                ->addColumn('rek_bsi', function($row) {
                    return $row->rek_bni_syariah ?? '-';
                })
                ->addColumn('kelengkapan_data', function ($row) {
                    // Hitung persentase kelengkapan data seperti di kode asli
                    $dataArray = (array) $row;
                    $jumlah = 0; 
                    $isi = 0;
                    
                    foreach ($dataArray as $value) {
                        $jumlah++;
                        if ($value != null && $value != '-') {
                            $isi++;
                        }
                    }
                    
                    $jumlah2 = $jumlah / 2;
                    $isi2 = $isi / 2;
                    
                    $statusKerja = $row->kd_status_kerja;
                    if($statusKerja == 1) {
                        $jumlah2 = $jumlah2 - 12;
                    } else {
                        $jumlah2 = $jumlah2 - 36;
                    }
                    
                    $persen = ($isi2 * 100) / $jumlah2;
                    if($persen >= 100) {
                        $persen = 100;
                    }
                    
                    return 'Terisi ' . ceil($persen) . '%';
                })
                ->addColumn('action', function ($row) {
                    return view('karyawan.golongan.columns._actions', ['karyawan' => $row]);
                })
                ->rawColumns(['id_pegawai', 'nama_lengkap', 'jenis_kelamin', 'golongan', 'masa_kerja', 'masa_kerja_bulan', 'eselon', 'pendidikan', 'sub_detail', 'action'])
                ->toJson();
        }

        // Ambil data golongan untuk dropdown
        $golongan = $this->getGolongan();

        return view('karyawan.golongan.index', compact('golongan'));
    }

    public function printGolongan(Request $request)
    {
        $golongan = $request->golongan;
        
        // Build query menggunakan VIEW_TAMPIL_KARYAWAN seperti di kode asli
        $query = DB::connection('sqlsrv')
            ->table('VIEW_TAMPIL_KARYAWAN')
            ->where('status_peg', '1')
            ->where('kd_gol_sekarang', $golongan);

        $karyawanData = $query->get();
        $totalKaryawan = $karyawanData->count();

        // Get golongan name
        $golonganName = DB::connection('sqlsrv')
            ->table('HRD_GOLONGAN')
            ->where('KD_GOL', $golongan)
            ->first();

        return view('karyawan.golongan.print', compact(
            'karyawanData', 
            'totalKaryawan', 
            'golonganName'
        ));
    }

    private function getGolongan()
    {
        return DB::connection('sqlsrv')
            ->table('HRD_GOLONGAN')
            ->select('KD_GOL', 'PANGKAT')
            ->orderBy('KD_GOL', 'desc')
            ->get();
    }
}
