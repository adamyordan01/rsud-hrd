<?php

namespace App\Http\Controllers\Karyawan;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class JenisTenagaController extends Controller
{
    public function index(Request $request, $jenisTenaga)
    {
        $titleBreadcrumb = $this->getJenisTenagaTitle($jenisTenaga);

        if ($request->ajax()) {
            $karyawan = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select([
                    'hrd_karyawan.kd_karyawan', 
                    'hrd_karyawan.gelar_depan', 
                    'hrd_karyawan.nama', 
                    'hrd_karyawan.gelar_belakang', 
                    'hrd_karyawan.nip_baru', 
                    'hrd_karyawan.tempat_lahir', 
                    'hrd_karyawan.tgl_lahir', 
                    'hrd_karyawan.no_karpeg', 
                    'hrd_karyawan.kd_jenis_kelamin', 
                    'hrd_karyawan.tmt_gol_sekarang', 
                    'hrd_karyawan.kd_gol_sekarang', 
                    'hrd_karyawan.masa_kerja_thn', 
                    'hrd_karyawan.masa_kerja_bulan', 
                    'hrd_karyawan.tmt_eselon', 
                    'hrd_karyawan.foto', 
                    'hrd_karyawan.tahun_lulus',
                    'hrd_karyawan.rek_bni_syariah',
                    'hrd_karyawan.kd_status_kerja',
                    'hrd_eselon.eselon', 
                    'hrd_golongan.pangkat', 
                    'hrd_ruangan.ruangan', 
                    'hrd_jenjang_pendidikan.jenjang_didik', 
                    'hrd_jurusan.jurusan', 
                    'hrd_status_kerja.status_kerja', 
                    'hrd_jenis_tenaga_sub_detail.sub_detail',
                    'hrd_karyawan.foto_square',
                ])
                ->leftJoin('sex', 'hrd_karyawan.kd_jenis_kelamin', 'sex.kode')
                ->leftJoin('hrd_eselon', 'hrd_karyawan.kd_eselon', 'hrd_eselon.kd_eselon')
                ->leftJoin('hrd_golongan', 'hrd_karyawan.kd_gol_sekarang', 'hrd_golongan.kd_gol')
                ->leftJoin('hrd_jenjang_pendidikan', 'hrd_karyawan.kd_pendidikan_terakhir', 'hrd_jenjang_pendidikan.kd_jenjang_didik')
                ->leftJoin('hrd_jurusan', 'hrd_karyawan.kd_jurusan', 'hrd_jurusan.kd_jurusan')
                ->leftJoin('hrd_status_kerja', 'hrd_karyawan.kd_status_kerja', 'hrd_status_kerja.kd_status_kerja')
                ->leftJoin('hrd_ruangan', 'hrd_karyawan.kd_ruangan', 'hrd_ruangan.kd_ruangan')
                ->leftJoin('hrd_jenis_tenaga_sub_detail', function ($join) {
                    $join->on('hrd_karyawan.kd_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_jenis_tenaga')
                        ->on('hrd_karyawan.kd_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_detail')
                        ->on('hrd_karyawan.kd_sub_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_sub_detail');
                })
                ->where('hrd_karyawan.status_peg', '1')
                ->where('hrd_karyawan.kd_jenis_tenaga', $jenisTenaga);

            // Filter berdasarkan status kerja
            $statusKerjaFilter = [];
            if ($request->has('pns') && $request->pns == '1') $statusKerjaFilter[] = '1';
            if ($request->has('honor') && $request->honor == '2') $statusKerjaFilter[] = '2';
            if ($request->has('kontrak') && $request->kontrak == '3') $statusKerjaFilter[] = '3';
            if ($request->has('partime') && $request->partime == '4') $statusKerjaFilter[] = '4';
            if ($request->has('pppk') && $request->pppk == '7') $statusKerjaFilter[] = '7';

            if (!empty($statusKerjaFilter)) {
                $karyawan->whereIn('hrd_karyawan.kd_status_kerja', $statusKerjaFilter);
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
                    $eselon = $row->eselon;
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
                    return view('karyawan.jenis_tenaga.columns._actions', ['karyawan' => $row]);
                })
                ->rawColumns(['id_pegawai', 'nama_lengkap', 'jenis_kelamin', 'golongan', 'eselon', 'pendidikan', 'sub_detail', 'action'])
                ->toJson();
        }

        // Ambil detail jenis tenaga untuk dropdown
        $detailJenisTenaga = $this->getDetailJenisTenaga($jenisTenaga);

        return view('karyawan.jenis_tenaga.index', compact('titleBreadcrumb', 'jenisTenaga', 'detailJenisTenaga'));
    }

    private function getDetailJenisTenaga($kdJenisTenaga)
    {
        $details = DB::table('hrd_jenis_tenaga_detail')
            ->where('kd_jenis_tenaga', $kdJenisTenaga)
            ->orderBy('detail_jenis_tenaga')
            ->get();

        foreach ($details as $detail) {
            $detail->statistik = DB::table('hrd_karyawan')
                ->selectRaw('sum(case when kd_status_kerja = 1 then 1 else 0 end) as pns')
                ->selectRaw('sum(case when kd_status_kerja = 2 then 1 else 0 end) as honor')
                ->selectRaw('sum(case when kd_status_kerja = 3 then 1 else 0 end) as kontrak')
                ->selectRaw('sum(case when kd_status_kerja = 4 then 1 else 0 end) as partime')
                ->selectRaw('sum(case when kd_status_kerja = 6 then 1 else 0 end) as thl')
                ->selectRaw('sum(case when kd_status_kerja = 7 then 1 else 0 end) as pppk')
                ->where('kd_detail_jenis_tenaga', $detail->kd_detail)
                ->where('kd_jenis_tenaga', $kdJenisTenaga)
                ->where('status_peg', 1)
                ->first();
        }

        return $details;
    }

    private function getJenisTenagaTitle($jenisTenaga)
    {
        switch ($jenisTenaga) {
            case 1:
                return 'Tenaga Medis';
            case 2:
                return 'Perawat dan Bidan';
            case 3:
                return 'Penunjang Medis';
            case 4:
                return 'Non-Kesehatan';
            default:
                return 'Pegawai';
        }
    }

    public function detailJenis(Request $request, $kdDetail, $jenisTenaga)
    {
        $titleBreadcrumb = $this->getJenisTenagaTitle($jenisTenaga);
        
        // Ambil nama detail jenis tenaga
        $detailJenisTenaga = DB::table('hrd_jenis_tenaga_detail')
            ->where('kd_detail', $kdDetail)
            ->where('kd_jenis_tenaga', $jenisTenaga)
            ->first();

        if ($request->ajax()) {
            $karyawan = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select([
                    'hrd_karyawan.kd_karyawan', 
                    'hrd_karyawan.gelar_depan', 
                    'hrd_karyawan.nama', 
                    'hrd_karyawan.gelar_belakang', 
                    'hrd_karyawan.nip_baru', 
                    'hrd_karyawan.tempat_lahir', 
                    'hrd_karyawan.tgl_lahir', 
                    'hrd_karyawan.no_karpeg', 
                    'hrd_karyawan.kd_jenis_kelamin', 
                    'hrd_karyawan.tmt_gol_sekarang', 
                    'hrd_karyawan.kd_gol_sekarang', 
                    'hrd_karyawan.masa_kerja_thn', 
                    'hrd_karyawan.masa_kerja_bulan', 
                    'hrd_karyawan.tmt_eselon', 
                    'hrd_karyawan.foto', 
                    'hrd_karyawan.tahun_lulus',
                    'hrd_karyawan.rek_bni_syariah',
                    'hrd_karyawan.kd_status_kerja',
                    'hrd_eselon.eselon', 
                    'hrd_golongan.pangkat', 
                    'hrd_ruangan.ruangan', 
                    'hrd_jenjang_pendidikan.jenjang_didik', 
                    'hrd_jurusan.jurusan', 
                    'hrd_status_kerja.status_kerja', 
                    'hrd_jenis_tenaga_sub_detail.sub_detail',
                    'hrd_karyawan.foto_square',
                ])
                ->leftJoin('sex', 'hrd_karyawan.kd_jenis_kelamin', 'sex.kode')
                ->leftJoin('hrd_eselon', 'hrd_karyawan.kd_eselon', 'hrd_eselon.kd_eselon')
                ->leftJoin('hrd_golongan', 'hrd_karyawan.kd_gol_sekarang', 'hrd_golongan.kd_gol')
                ->leftJoin('hrd_jenjang_pendidikan', 'hrd_karyawan.kd_pendidikan_terakhir', 'hrd_jenjang_pendidikan.kd_jenjang_didik')
                ->leftJoin('hrd_jurusan', 'hrd_karyawan.kd_jurusan', 'hrd_jurusan.kd_jurusan')
                ->leftJoin('hrd_status_kerja', 'hrd_karyawan.kd_status_kerja', 'hrd_status_kerja.kd_status_kerja')
                ->leftJoin('hrd_ruangan', 'hrd_karyawan.kd_ruangan', 'hrd_ruangan.kd_ruangan')
                ->leftJoin('hrd_jenis_tenaga_sub_detail', function ($join) {
                    $join->on('hrd_karyawan.kd_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_jenis_tenaga')
                        ->on('hrd_karyawan.kd_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_detail')
                        ->on('hrd_karyawan.kd_sub_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_sub_detail');
                })
                ->where('hrd_karyawan.status_peg', '1')
                ->where('hrd_karyawan.kd_jenis_tenaga', $jenisTenaga)
                ->where('hrd_karyawan.kd_detail_jenis_tenaga', $kdDetail);

            // TAMBAHAN: Filter berdasarkan status kerja
            $statusKerjaFilter = [];
            if ($request->has('pns') && $request->pns == '1') $statusKerjaFilter[] = '1';
            if ($request->has('honor') && $request->honor == '2') $statusKerjaFilter[] = '2';
            if ($request->has('kontrak') && $request->kontrak == '3') $statusKerjaFilter[] = '3';
            if ($request->has('partime') && $request->partime == '4') $statusKerjaFilter[] = '4';
            if ($request->has('pppk') && $request->pppk == '7') $statusKerjaFilter[] = '7';

            // Jika ada filter status kerja yang dipilih, terapkan filter
            if (!empty($statusKerjaFilter)) {
                $karyawan->whereIn('hrd_karyawan.kd_status_kerja', $statusKerjaFilter);
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
                    $eselon = $row->eselon;
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
                    return view('karyawan.jenis_tenaga.columns._actions', ['karyawan' => $row]);
                })
                ->rawColumns(['id_pegawai', 'nama_lengkap', 'jenis_kelamin', 'golongan', 'eselon', 'pendidikan', 'sub_detail', 'action'])
                ->toJson();
        }

        $allDetailJenisTenaga  = $this->getDetailJenisTenaga($jenisTenaga);

        return view('karyawan.jenis_tenaga.detail', compact('titleBreadcrumb', 'jenisTenaga', 'kdDetail', 'detailJenisTenaga', 'allDetailJenisTenaga'));
    }

    /**
     * Print laporan pegawai berdasarkan jenis tenaga dan filter status kerja
     */
    public function printPegawai(Request $request, $jenisTenaga)
    {
        // Ambil parameter filter status kerja dari request
        $data1 = $request->get('data1'); // PNS
        $data2 = $request->get('data2'); // HONOR
        $data3 = $request->get('data3'); // KONTRAK
        $data4 = $request->get('data4'); // PT
        $data7 = $request->get('data7'); // PPPK
        $detail = $request->get('detail'); // KD_DETAIL_JENIS_TENAGA (opsional)
        $sub = $request->get('sub'); // KD_SUB_DETAIL_JENIS_TENAGA (opsional)

        // Inisialisasi filter status kerja - PERBAIKAN: filter null values
        $statusKerjaFilter = [];
        
        // Hanya tambahkan ke array jika nilai tidak null, tidak kosong, dan bukan string 'null'
        if ($data1 && $data1 !== 'a' && $data1 !== 'null') $statusKerjaFilter[] = (int)$data1;
        if ($data2 && $data2 !== 'null') $statusKerjaFilter[] = (int)$data2;
        if ($data3 && $data3 !== 'null') $statusKerjaFilter[] = (int)$data3;
        if ($data4 && $data4 !== 'null') $statusKerjaFilter[] = (int)$data4;
        if ($data7 && $data7 !== 'null') $statusKerjaFilter[] = (int)$data7;

        // Jika tidak ada filter yang dipilih, gunakan default semua status kerja
        if (empty($statusKerjaFilter) && $data1 !== 'a') {
            $statusKerjaFilter = [1, 2, 3, 4, 7]; // Default: PNS, HONOR, KONTRAK, PT, PPPK
        } elseif ($data1 === 'a') {
            $statusKerjaFilter = []; // Tidak ada data yang dipilih
        }

        // Inisialisasi collection kosong
        $karyawanData = collect();

        // PERBAIKAN: Hanya jalankan query jika ada filter yang valid
        if (!empty($statusKerjaFilter)) {
            // Query builder
            $query = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select([
                    'hrd_karyawan.kd_karyawan',
                    'hrd_karyawan.gelar_depan',
                    'hrd_karyawan.nama',
                    'hrd_karyawan.gelar_belakang',
                    'hrd_karyawan.nip_baru',
                    'hrd_karyawan.tempat_lahir',
                    'hrd_karyawan.tgl_lahir',
                    'hrd_karyawan.no_karpeg',
                    'hrd_karyawan.no_ktp',
                    'hrd_karyawan.no_askes',
                    'hrd_karyawan.kd_jenis_kelamin',
                    'hrd_karyawan.tmt_gol_sekarang',
                    'hrd_karyawan.kd_gol_sekarang',
                    'hrd_karyawan.masa_kerja_thn',
                    'hrd_karyawan.masa_kerja_bulan',
                    'hrd_karyawan.tahun_lulus',
                    'hrd_karyawan.kd_status_kerja',
                    'sex.jenis',
                    'hrd_golongan.pangkat',
                    'hrd_ruangan.ruangan',
                    'hrd_jenjang_pendidikan.jenjang_didik',
                    'hrd_jurusan.jurusan',
                    'hrd_status_kerja.status_kerja',
                    'hrd_jenis_tenaga.jenis_tenaga',
                    'hrd_jenis_tenaga_sub_detail.sub_detail'
                ])
                ->leftJoin('sex', 'hrd_karyawan.kd_jenis_kelamin', 'sex.kode')
                ->leftJoin('hrd_golongan', 'hrd_karyawan.kd_gol_sekarang', 'hrd_golongan.kd_gol')
                ->leftJoin('hrd_jenjang_pendidikan', 'hrd_karyawan.kd_pendidikan_terakhir', 'hrd_jenjang_pendidikan.kd_jenjang_didik')
                ->leftJoin('hrd_jurusan', 'hrd_karyawan.kd_jurusan', 'hrd_jurusan.kd_jurusan')
                ->leftJoin('hrd_status_kerja', 'hrd_karyawan.kd_status_kerja', 'hrd_status_kerja.kd_status_kerja')
                ->leftJoin('hrd_ruangan', 'hrd_karyawan.kd_ruangan', 'hrd_ruangan.kd_ruangan')
                ->leftJoin('hrd_jenis_tenaga', 'hrd_karyawan.kd_jenis_tenaga', 'hrd_jenis_tenaga.kd_jenis_tenaga')
                ->leftJoin('hrd_jenis_tenaga_sub_detail', function ($join) {
                    $join->on('hrd_karyawan.kd_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_jenis_tenaga')
                        ->on('hrd_karyawan.kd_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_detail')
                        ->on('hrd_karyawan.kd_sub_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_sub_detail');
                })
                ->where('hrd_karyawan.status_peg', '1')
                ->where('hrd_karyawan.kd_jenis_tenaga', $jenisTenaga);

            // Filter berdasarkan detail dan sub detail jika ada
            if ($detail && $detail !== 'null') {
                $query->where('hrd_karyawan.kd_detail_jenis_tenaga', $detail);
            }
            if ($sub && $sub !== 'null') {
                $query->where('hrd_karyawan.kd_sub_detail_jenis_tenaga', $sub);
            }

            // Filter berdasarkan status kerja - hanya jika array tidak kosong
            $query->whereIn('hrd_karyawan.kd_status_kerja', $statusKerjaFilter);

            // Eksekusi query
            $karyawanData = $query->orderBy('hrd_karyawan.kd_status_kerja')
                ->orderBy('hrd_ruangan.ruangan')
                ->orderBy('hrd_karyawan.tahun_lulus')
                ->orderBy('hrd_karyawan.nama')
                ->get();
        }

        // Tentukan judul laporan
        $judulLaporan = $this->getJudulLaporan($jenisTenaga, $detail, $sub);

        return view('karyawan.jenis_tenaga.print.pegawai', compact(
            'karyawanData',
            'judulLaporan',
            'jenisTenaga',
            'detail',
            'sub'
        ));
    }

    /**
     * Print laporan pegawai spesialis (khusus untuk jenis tenaga medis)
     */
    public function printPegawaiSpesialis(Request $request, $jenisTenaga)
    {
        // Validasi hanya untuk jenis tenaga medis (kode 1)
        if ($jenisTenaga != 1) {
            abort(404, 'Laporan spesialis hanya tersedia untuk tenaga medis');
        }

        // Ambil parameter filter status kerja dari request
        $data1 = $request->get('data1'); // PNS
        $data2 = $request->get('data2'); // HONOR
        $data3 = $request->get('data3'); // KONTRAK
        $data4 = $request->get('data4'); // PT
        $data7 = $request->get('data7'); // PPPK

        // Inisialisasi filter status kerja - PERBAIKAN: filter null values
        $statusKerjaFilter = [];
        
        // Hanya tambahkan ke array jika nilai tidak null dan tidak kosong
        if ($data1 && $data1 !== 'a' && $data1 !== 'null') $statusKerjaFilter[] = (int)$data1;
        if ($data2 && $data2 !== 'null') $statusKerjaFilter[] = (int)$data2;
        if ($data3 && $data3 !== 'null') $statusKerjaFilter[] = (int)$data3;
        if ($data4 && $data4 !== 'null') $statusKerjaFilter[] = (int)$data4;
        if ($data7 && $data7 !== 'null') $statusKerjaFilter[] = (int)$data7;

        // Jika tidak ada filter yang dipilih, gunakan default semua status kerja
        if (empty($statusKerjaFilter) && $data1 !== 'a') {
            $statusKerjaFilter = [1, 2, 3, 4, 7];
        } elseif ($data1 === 'a') {
            $statusKerjaFilter = [];
        }

        $karyawanMayor = collect();
        $karyawanMinor = collect();

        // PERBAIKAN: Hanya jalankan query jika ada filter yang valid
        if (!empty($statusKerjaFilter)) {
            // Query untuk MAYOR (LEFT(KELOMPOK_SPESIALIS, 1) = 1)
            $karyawanMayor = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->select([
                    'kd_karyawan',
                    'gelar_depan',
                    'nama',
                    'gelar_belakang',
                    'nip_baru',
                    'tempat_lahir',
                    'tgl_lahir',
                    'no_karpeg',
                    'no_ktp',
                    'no_askes',
                    'kd_jenis_kelamin',
                    'tmt_gol_sekarang',
                    'kd_gol_sekarang',
                    'masa_kerja_thn',
                    'masa_kerja_bulan',
                    'tahun_lulus',
                    'kd_status_kerja',
                    'kelompok_spesialis',
                    'jenis_kelamin',
                    'pangkat',
                    'ruangan',
                    'jenjang_didik',
                    'jurusan',
                    'status_kerja',
                    'jenis_tenaga',
                    'sub_detail'
                ])
                ->where('status_peg', '1')
                ->where('kd_jenis_tenaga', '1')
                ->whereIn('kd_status_kerja', $statusKerjaFilter)
                ->whereRaw('LEFT(kelompok_spesialis, 1) = 1')
                ->orderBy('kelompok_spesialis')
                ->orderBy('kd_status_kerja')
                ->orderBy('kd_sub_detail_jenis_tenaga')
                ->orderBy('nama')
                ->get();

            // Query untuk MINOR (LEFT(KELOMPOK_SPESIALIS, 1) = 2)
            $karyawanMinor = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->select([
                    'kd_karyawan',
                    'gelar_depan',
                    'nama',
                    'gelar_belakang',
                    'nip_baru',
                    'tempat_lahir',
                    'tgl_lahir',
                    'no_karpeg',
                    'no_ktp',
                    'no_askes',
                    'kd_jenis_kelamin',
                    'tmt_gol_sekarang',
                    'kd_gol_sekarang',
                    'masa_kerja_thn',
                    'masa_kerja_bulan',
                    'tahun_lulus',
                    'kd_status_kerja',
                    'kelompok_spesialis',
                    'jenis_kelamin',
                    'pangkat',
                    'ruangan',
                    'jenjang_didik',
                    'jurusan',
                    'status_kerja',
                    'jenis_tenaga',
                    'sub_detail'
                ])
                ->where('status_peg', '1')
                ->where('kd_jenis_tenaga', '1')
                ->whereIn('kd_status_kerja', $statusKerjaFilter)
                ->whereRaw('LEFT(kelompok_spesialis, 1) = 2')
                ->orderBy('kelompok_spesialis')
                ->orderBy('kd_sub_detail_jenis_tenaga')
                ->orderBy('kd_status_kerja')
                ->orderBy('nama')
                ->get();
        }

        return view('karyawan.jenis_tenaga.print.pegawai_spesialis', compact(
            'karyawanMayor',
            'karyawanMinor',
            'jenisTenaga'
        ));
    }

    /**
     * Helper method untuk mendapatkan judul laporan
     */
    private function getJudulLaporan($jenisTenaga, $detail = null, $sub = null)
    {
        if ($detail && $sub) {
            // Kasus: Jenis, Detail, dan Sub Detail
            $subDetail = DB::table('hrd_jenis_tenaga_sub_detail')
                ->where('kd_jenis_tenaga', $jenisTenaga)
                ->where('kd_detail', $detail)
                ->where('kd_sub_detail', $sub)
                ->first();
            return 'DATA PEGAWAI ' . ($subDetail->sub_detail ?? '');
        } elseif ($detail && !$sub) {
            // Kasus: Jenis dan Detail (tanpa Sub Detail)
            $detailJenis = DB::table('hrd_jenis_tenaga_detail')
                ->where('kd_jenis_tenaga', $jenisTenaga)
                ->where('kd_detail', $detail)
                ->first();
            return 'DATA PEGAWAI ' . ($detailJenis->detail_jenis_tenaga ?? '');
        } else {
            // Kasus: Hanya Jenis
            switch ($jenisTenaga) {
                case 1:
                    return 'DATA PEGAWAI MEDIS';
                case 2:
                    return 'DATA PEGAWAI PARAMEDIS';
                case 3:
                    return 'DATA PEGAWAI PENUNJANG MEDIS';
                case 4:
                    return 'DATA PEGAWAI NON-KESEHATAN';
                default:
                    return 'DATA PEGAWAI';
            }
        }
    }
}
