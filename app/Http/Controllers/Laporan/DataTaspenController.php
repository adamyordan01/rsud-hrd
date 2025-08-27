<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DataTaspenController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Query utama dari view_tampil_karyawan untuk ASN (PNS) saja
            // Select hanya kolom yang diperlukan untuk performa lebih baik
            $query = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->select([
                    'nama', 'gelar_depan', 'gelar_belakang', 
                    'nip_baru', 'no_ktp', 'no_hp'
                ])
                ->where('status_peg', '1') // Pegawai aktif
                ->where('kd_status_kerja', 1); // 1=PNS/ASN

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $row->nama . $gelarBelakang;
                    
                    return '<div class="fw-bold">' . $namaLengkap . '</div>';
                })
                ->addColumn('nip', function ($row) {
                    return '<div class="text-center">' . ($row->nip_baru ?? '-') . '</div>';
                })
                ->addColumn('no_ktp', function ($row) {
                    return '<div class="text-center">' . ($row->no_ktp ?? '-') . '</div>';
                })
                ->addColumn('no_hp', function ($row) {
                    return '<div class="text-center">' . ($row->no_hp ?? '-') . '</div>';
                })
                ->addColumn('keterangan', function ($row) {
                    return '<div class="text-center">-</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value']) {
                        $query->where(function($q) use ($search) {
                            $q->whereRaw("LOWER(CONCAT(ISNULL(gelar_depan,''), ' ', nama, ' ', ISNULL(gelar_belakang,''))) LIKE ?", ['%' . strtolower($search) . '%'])
                              ->orWhere('nama', 'LIKE', '%' . $search . '%')
                              ->orWhere('nip_baru', 'LIKE', '%' . $search . '%')
                              ->orWhere('no_ktp', 'LIKE', '%' . $search . '%')
                              ->orWhere('no_hp', 'LIKE', '%' . $search . '%');
                        });
                    }
                })
                ->order(function ($query) {
                    // Order berdasarkan nama
                    $query->orderBy('nama', 'ASC');
                })
                ->rawColumns(['nama_lengkap', 'nip', 'no_ktp', 'no_hp', 'keterangan'])
                ->make(true);
        }

        return view('laporan.data-taspen.index');
    }
    
    public function print(Request $request)
    {
        // Query utama untuk ASN (PNS) saja
        $query = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('status_peg', '1')
            ->where('kd_status_kerja', 1); // 1=PNS/ASN
        
        // Urutkan data berdasarkan nama
        $data = $query->orderBy('nama', 'ASC')->get();
        
        // Ambil data direktur untuk tanda tangan
        $direktur = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('kd_jabatan_struktural', 1)
            ->where('status_peg', '1')
            ->first();

        return view('laporan.data-taspen.print', compact('data', 'direktur'));
    }
}
