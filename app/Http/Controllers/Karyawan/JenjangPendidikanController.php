<?php

namespace App\Http\Controllers\Karyawan;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class JenjangPendidikanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $karyawan = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('status_peg', '1');

            // Filter berdasarkan jenjang pendidikan
            if ($request->has('jenjang') && !empty($request->jenjang)) {
                $karyawan->where('kd_pendidikan_terakhir', $request->jenjang);
            }

            // Filter berdasarkan jurusan
            if ($request->has('jurusan') && !empty($request->jurusan) && $request->jurusan != '000') {
                $karyawan->where('kd_jurusan', $request->jurusan);
            }

            // Filter berdasarkan status kerja
            $statusKerjaFilter = [];
            if ($request->has('pns') && $request->pns == '1') $statusKerjaFilter[] = '1';
            if ($request->has('honor') && $request->honor == '2') $statusKerjaFilter[] = '2';
            if ($request->has('kontrak') && $request->kontrak == '3') $statusKerjaFilter[] = '3';
            if ($request->has('partime') && $request->partime == '4') $statusKerjaFilter[] = '4';
            if ($request->has('pppk') && $request->pppk == '7') $statusKerjaFilter[] = '7';

            if (!empty($statusKerjaFilter)) {
                $karyawan->whereIn('kd_status_kerja', $statusKerjaFilter);
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
                ->editColumn('jenis_kelamin', fn($row) => $row->kd_jenis_kelamin == '1' ? 'L' : 'P')
                ->addColumn('status_kerja', function($row) {
                    switch($row->kd_status_kerja) {
                        case 1: return 'PNS';
                        case 2: return 'HONOR';
                        case 3: return 'KONTRAK';
                        case 4: return 'PT';
                        case 6: return 'THL';
                        case 7: return 'PPPK';
                        default: return '-';
                    }
                })
                ->addColumn('golongan', function ($row) {
                    $pangkat = $row->pangkat . ' / ' . $row->kd_gol_sekarang;
                    $gol = ($row->kd_gol_sekarang == '0' || $row->kd_gol_sekarang == null) 
                        ? '-' 
                        : Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y');
                    return $pangkat . '<br>' . $gol;
                })
                ->addColumn('eselon', function ($row) {
                    $eselon = $row->eselon ?? '-';
                    $tmtEselon = $row->tmt_eselon ? Carbon::parse($row->tmt_eselon)->format('d-m-Y') : '';
                    return $eselon . '<br>' . $tmtEselon;
                })
                ->addColumn('pendidikan', function ($row) {
                    return $row->jenjang_didik . '<br>' . $row->jurusan . '<br>Lulus thn. ' . $row->tahun_lulus;
                })
                ->addColumn('sub_detail', function ($row) {
                    $eselon = $row->eselon;
                    $jenisTenaga = ($eselon == '-' || $eselon == null) 
                        ? 'Tenaga ' . $row->sub_detail 
                        : 'Tenaga Manajemen';
                    return strtoupper($jenisTenaga) . '<br>PADA ' . $row->ruangan;
                })
                ->addColumn('action', function ($row) {
                    return view('karyawan.jenjang_pendidikan.columns._actions', ['karyawan' => $row]);
                })
                ->rawColumns(['id_pegawai', 'nama_lengkap', 'jenis_kelamin', 'golongan', 'eselon', 'pendidikan', 'sub_detail', 'action'])
                ->toJson();
        }

        // Ambil data jenjang pendidikan untuk dropdown
        $jenjangPendidikan = $this->getJenjangPendidikan();

        return view('karyawan.jenjang_pendidikan.index', compact('jenjangPendidikan'));
    }

    public function printJenjang(Request $request)
    {
        $jenjang = $request->jenjang;
        $jurusan = $request->jurusan;
        
        // Build query menggunakan VIEW_TAMPIL_KARYAWAN seperti di kode asli
        $query = DB::connection('sqlsrv')
            ->table('VIEW_TAMPIL_KARYAWAN')
            ->where('STATUS_PEG', '1')
            ->where('KD_PENDIDIKAN_TERAKHIR', $jenjang);

        // Filter berdasarkan jurusan jika dipilih
        if (!empty($jurusan) && $jurusan != '000') {
            $query->where('KD_JURUSAN', $jurusan);
        }

        // Filter berdasarkan status kerja
        $statusKerjaFilter = [];
        if ($request->has('data1') && !empty($request->data1) && $request->data1 != 'a') $statusKerjaFilter[] = $request->data1;
        if ($request->has('data2') && !empty($request->data2)) $statusKerjaFilter[] = $request->data2;
        if ($request->has('data3') && !empty($request->data3)) $statusKerjaFilter[] = $request->data3;
        if ($request->has('data4') && !empty($request->data4)) $statusKerjaFilter[] = $request->data4;
        if ($request->has('data5') && !empty($request->data5)) $statusKerjaFilter[] = $request->data5;

        if (!empty($statusKerjaFilter)) {
            $query->whereIn('KD_STATUS_KERJA', $statusKerjaFilter);
        }

        $karyawanData = $query->get();
        $totalKaryawan = $karyawanData->count();

        // Get jenjang name
        $jenjangName = DB::connection('sqlsrv')
            ->table('HRD_JENJANG_PENDIDIKAN')
            ->where('KD_JENJANG_DIDIK', $jenjang)
            ->value('JENJANG_DIDIK');

        // Get jurusan name if selected
        $jurusanName = '';
        if (!empty($jurusan) && $jurusan != '000') {
            $jurusanName = DB::connection('sqlsrv')
                ->table('VIEW_TAMPIL_KARYAWAN')
                ->where('KD_JURUSAN', $jurusan)
                ->value('JURUSAN');
        }

        return view('karyawan.jenjang_pendidikan.print', compact(
            'karyawanData', 
            'totalKaryawan', 
            'jenjangName', 
            'jurusanName'
        ));
    }

    public function getJurusan(Request $request)
    {
        $jenjang = $request->jenjang;
        
        $jurusan = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->select('kd_jurusan', 'jurusan')
            ->where('kd_pendidikan_terakhir', $jenjang)
            ->whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->groupBy('kd_jurusan', 'jurusan')
            ->orderBy('jurusan', 'asc')
            ->get();

        return response()->json($jurusan);
    }

    private function getJenjangPendidikan()
    {
        return DB::connection('sqlsrv')
            ->table('hrd_jenjang_pendidikan')
            ->select('kd_jenjang_didik', 'jenjang_didik')
            ->orderBy('nilaiIndex', 'asc')
            ->get();
    }
}
