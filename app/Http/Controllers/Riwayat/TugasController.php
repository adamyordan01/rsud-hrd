<?php

namespace App\Http\Controllers\Riwayat;

use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\EmployeeProfileService;
use Yajra\DataTables\Facades\DataTables;

class TugasController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        return view('karyawan.tugas.index', $data);
    }

    public function getTugasData($id)
    {
        // Menggunakan view_tempat_kerja
        $tugas = DB::connection('sqlsrv')
            ->table('view_tempat_kerja')
            ->where('kd_karyawan', $id)
            ->orderBy('no_urut', 'desc')
            ->get();

        return DataTables::of($tugas)
            ->addIndexColumn()
            ->editColumn('periode', function ($row) {
                $tglMasuk = Carbon::parse($row->tgl_masuk)->translatedFormat('d M Y');
                $tglKeluar = $row->tgl_keluar ? Carbon::parse($row->tgl_keluar)->translatedFormat('d M Y') : 'Sekarang';
                return $tglMasuk . '<br><small class="text-muted">s/d ' . $tglKeluar . '</small>';
            })
            ->editColumn('unit_kerja_info', function ($row) {
                return $row->divisi . '<br><small class="text-muted">' . $row->unit_kerja . '</small>';
            })
            ->editColumn('sub_unit_ruangan', function ($row) {
                return $row->sub_unit_kerja . '<br><small class="text-muted">' . $row->ruangan . '</small>';
            })
            ->editColumn('jabatan_info', function ($row) {
                $jabatan = $row->jab_struk ? $row->jab_struk : '<em class="text-muted">Tidak ada</em>';
                return $jabatan . '<br><small class="text-muted">' . $row->jenis_tenaga . '</small>';
            })
            ->editColumn('detail_tenaga', function ($row) {
                $detail = $row->detail_jenis_tenaga;
                if ($row->sub_detail) {
                    $detail .= '<br><small class="text-muted">' . $row->sub_detail . '</small>';
                }
                return $detail;
            })
            ->editColumn('no_nota', function ($row) {
                return $row->no_nota ? $row->no_nota : '<em class="text-muted">-</em>';
            })
            ->editColumn('status', function ($row) {
                if ($row->tgl_keluar) {
                    return '<span class="badge badge-light-secondary">Selesai</span>';
                } else {
                    return '<span class="badge badge-light-success">Aktif</span>';
                }
            })
            ->rawColumns(['periode', 'unit_kerja_info', 'sub_unit_ruangan', 'jabatan_info', 'detail_tenaga', 'no_nota', 'status'])
            ->make(true);
    }
}
