<?php

namespace App\Http\Controllers\Karyawan;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Helpers\PhotoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class KaryawanRuanganController extends Controller
{
    public function index(Request $request, $kdRuangan = null)
    {
        if ($request->ajax()) {
            $karyawan = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('status_peg', 1);

            // Filter berdasarkan ruangan
            if ($kdRuangan) {
                // Jika ada ruangan yang dipilih, tampilkan karyawan dari ruangan tersebut
                $karyawan->where('kd_ruangan', $kdRuangan);
            } else {
                $karyawan->where(function($query) {
                    $query->whereNull('kd_ruangan')
                        ->orWhere('kd_ruangan', 0);
                });
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
                    
                    $photo = PhotoHelper::getPhotoUrl($row, 'foto_square');

                    $photo = '<div class="symbol symbol-45px"><img src="' . $photo . '" alt="' . $row->kd_karyawan . '"></div>';
                    

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
                    if ($row->jenis_kelamin == 'Wanita') return 'P';
                    if ($row->jenis_kelamin == 'Pria') return 'L';
                    return '?';
                })
                ->addColumn('status_kerja', function($row) {
                    return $row->status_kerja ?? '-';
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
                    return $row->jenjang_didik . '<br>Lulus thn. ' . $row->tahun_lulus;
                })
                ->addColumn('sub_detail', function ($row) {
                    $jenisTenaga = 'Tenaga ' . ($row->sub_detail ?? 'Belum Ditentukan');
                    $ruangan = $row->ruangan ?? 'Belum Ada Ruangan';
                    return strtoupper($jenisTenaga) . '<br>PADA ' . $ruangan;
                })
                ->addColumn('action', function ($row) {
                    return view('karyawan.ruangan.columns._actions', ['karyawan' => $row]);
                })
                ->rawColumns(['id_pegawai', 'nama_lengkap', 'jenis_kelamin', 'golongan', 'eselon', 'pendidikan', 'sub_detail', 'action'])
                ->toJson();
        }

        // Get current ruangan info
        $ruanganInfo = null;
        if ($kdRuangan) {
            $ruanganInfo = DB::connection('sqlsrv')
                ->table('hrd_ruangan')
                ->where('kd_ruangan', $kdRuangan)
                ->first();
        }

        // Ambil semua ruangan untuk dropdown
        $ruanganList = $this->getRuanganList();

        // Get total karyawan count
        $totalKaryawan = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('status_peg', '1')
            ->count();

        return view('karyawan.ruangan.index', compact(
            'kdRuangan', 
            'ruanganInfo', 
            'ruanganList', 
            'totalKaryawan'
        ));
    }

    public function printSesuaiJabatan(Request $request, $kdRuangan)
    {
        return $this->printRuangan($request, $kdRuangan, 'jabatan');
    }

    public function printSesuaiRekBNI(Request $request, $kdRuangan)
    {
        return $this->printRuangan($request, $kdRuangan, 'rek_bni');
    }

    public function printDataPegawai(Request $request, $kdRuangan)
    {
        return $this->printRuangan($request, $kdRuangan, 'data_pegawai');
    }

    private function printRuangan(Request $request, $kdRuangan, $jenisPrint)
    {
        // Build query
        $query = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('status_peg', '1')
            ->where('kd_ruangan', $kdRuangan);

        // Filter berdasarkan status kerja
        $statusKerjaFilter = [];
        if ($request->has('data1') && !empty($request->data1) && $request->data1 != 'a') $statusKerjaFilter[] = $request->data1;
        if ($request->has('data2') && !empty($request->data2)) $statusKerjaFilter[] = $request->data2;
        if ($request->has('data3') && !empty($request->data3)) $statusKerjaFilter[] = $request->data3;
        if ($request->has('data4') && !empty($request->data4)) $statusKerjaFilter[] = $request->data4;
        if ($request->has('data5') && !empty($request->data5)) $statusKerjaFilter[] = $request->data5;

        if (!empty($statusKerjaFilter)) {
            $query->whereIn('kd_status_kerja', $statusKerjaFilter);
        }

        // Order berdasarkan jenis print
        switch ($jenisPrint) {
            case 'jabatan':
                $query->orderBy('kd_status_kerja')
                      ->orderBy('nilaiIndex', 'desc')
                      ->orderBy('tahun_lulus');
                break;
            case 'rek_bni':
                $query->orderBy('rek_bni_syariah');
                break;
            default:
                $query->orderBy('nama');
                break;
        }

        $karyawanData = $query->get();
        $totalKaryawan = $karyawanData->count();

        // Get ruangan info
        $ruanganInfo = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->where('kd_ruangan', $kdRuangan)
            ->first();

        $judulLaporan = '';
        switch ($jenisPrint) {
            case 'jabatan':
                $judulLaporan = 'LAPORAN PEGAWAI SESUAI JABATAN - ' . ($ruanganInfo->ruangan ?? '');
                break;
            case 'rek_bni':
                $judulLaporan = 'LAPORAN PEGAWAI SESUAI REK BNI - ' . ($ruanganInfo->ruangan ?? '');
                break;
            default:
                $judulLaporan = 'LAPORAN DATA PEGAWAI - ' . ($ruanganInfo->ruangan ?? '');
                break;
        }

        return view('karyawan.ruangan.print', compact(
            'karyawanData', 
            'totalKaryawan', 
            'judulLaporan',
            'ruanganInfo'
        ));
    }

    public function exportExcel(Request $request, $kdRuangan)
    {
        // TODO: Implement Excel export
        // Untuk sementara redirect ke print
        return $this->printDataPegawai($request, $kdRuangan);
    }

    public function exportToFP(Request $request, $kdRuangan)
    {
        // TODO: Implement FP export  
        // Untuk sementara redirect ke print
        return $this->printDataPegawai($request, $kdRuangan);
    }

    private function getRuanganList()
    {
        return DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->select('kd_ruangan', 'ruangan')
            ->where('kd_ruangan', '!=', 0)
            ->where('status_aktif', 1)
            ->orderBy('ruangan')
            ->get();
    }
}
