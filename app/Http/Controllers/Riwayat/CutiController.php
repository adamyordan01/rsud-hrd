<?php

namespace App\Http\Controllers\Riwayat;

use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\EmployeeProfileService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CutiController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        return view('karyawan.cuti.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kd_cuti' => 'required|integer|min:1',
            'pejabat' => 'required|string|max:50',
            'no_sk' => 'required|string|max:50',
            'tgl_sk' => 'required|date',
            'lama_hari' => 'required|integer|min:1',
            'tgl_mulai' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_mulai',
            'ket' => 'nullable|string',
        ], [
            'kd_cuti.required' => 'Kolom Jenis Cuti wajib diisi.',
            'kd_cuti.integer' => 'Kolom Jenis Cuti harus berupa angka.',
            'kd_cuti.min' => 'Kolom Jenis Cuti harus lebih dari 0.',
            'pejabat.required' => 'Kolom Pejabat Pemberi wajib diisi.',
            'pejabat.string' => 'Kolom Pejabat Pemberi harus berupa teks.',
            'pejabat.max' => 'Kolom Pejabat Pemberi maksimal 50 karakter.',
            'no_sk.required' => 'Kolom No. SK wajib diisi.',
            'no_sk.string' => 'Kolom No. SK harus berupa teks.',
            'no_sk.max' => 'Kolom No. SK maksimal 50 karakter.',
            'tgl_sk.required' => 'Kolom Tanggal SK wajib diisi.',
            'tgl_sk.date' => 'Kolom Tanggal SK harus berupa tanggal.',
            'lama_hari.required' => 'Kolom Lama Hari wajib diisi.',
            'lama_hari.integer' => 'Kolom Lama Hari harus berupa angka.',
            'lama_hari.min' => 'Kolom Lama Hari harus lebih dari 0.',
            'tgl_mulai.required' => 'Kolom Tanggal Mulai wajib diisi.',
            'tgl_mulai.date' => 'Kolom Tanggal Mulai harus berupa tanggal.',
            'tgl_akhir.required' => 'Kolom Tanggal Akhir wajib diisi.',
            'tgl_akhir.date' => 'Kolom Tanggal Akhir harus berupa tanggal.',
            'tgl_akhir.after_or_equal' => 'Tanggal Akhir harus sama dengan atau setelah Tanggal Mulai.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Get urut_cuti berikutnya
        $urutCuti = DB::connection('sqlsrv')
            ->table('hrd_r_cuti')
            ->where('kd_karyawan', $id)
            ->max('urut_cuti') + 1;

        // Insert manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_cuti')
            ->insert([
                'kd_karyawan' => $id,
                'urut_cuti' => $urutCuti,
                'kd_cuti' => $request->kd_cuti,
                'pejabat' => $request->pejabat,
                'no_sk' => $request->no_sk,
                'tgl_sk' => $request->tgl_sk,
                'lama_hari' => $request->lama_hari,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_akhir' => $request->tgl_akhir,
                'ket' => $request->ket,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat cuti berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function getCutiData($id)
    {
        // Menggunakan Collection untuk menghindari masalah ORDER BY di SQL Server
        $cuti = DB::connection('sqlsrv')
            ->table('hrd_r_cuti')
            ->where('kd_karyawan', $id)
            ->get()
            ->sortByDesc('tgl_sk')
            ->sortByDesc('urut_cuti');

        return DataTables::of($cuti)
            ->addIndexColumn()
            ->editColumn('periode_cuti', function ($row) {
                $tglMulai = Carbon::parse($row->tgl_mulai)->translatedFormat('d M Y');
                $tglAkhir = Carbon::parse($row->tgl_akhir)->translatedFormat('d M Y');
                $lamaHari = $row->lama_hari;
                
                return $tglMulai . ' - ' . $tglAkhir . '<br><small class="text-muted">(' . $lamaHari . ' hari)</small>';
            })
            ->editColumn('pejabat_sk', function ($row) {
                $tglSk = Carbon::parse($row->tgl_sk)->translatedFormat('d M Y');
                return $row->pejabat . '<br><small class="text-muted">SK: ' . $row->no_sk . ' (' . $tglSk . ')</small>';
            })
            ->editColumn('jenis_cuti', function ($row) {
                // Mapping jenis cuti berdasarkan kd_cuti
                $jenisCutiMap = [
                    1 => 'Cuti Tahunan',
                    2 => 'Cuti Besar',
                    3 => 'Cuti Sakit',
                    4 => 'Cuti Melahirkan',
                    5 => 'Cuti Karena Alasan Penting',
                    6 => 'Cuti di Luar Tanggungan Negara',
                ];
                
                $jenisCuti = $jenisCutiMap[(int)$row->kd_cuti] ?? 'Jenis Cuti Lain (' . $row->kd_cuti . ')';
                
                return '<span class="badge badge-light-primary">' . $jenisCuti . '</span>';
            })
            ->editColumn('ket', function ($row) {
                if ($row->ket) {
                    $ketShort = strlen($row->ket) > 50 ? substr($row->ket, 0, 50) . '...' : $row->ket;
                    return '<span title="' . e($row->ket) . '">' . e($ketShort) . '</span>';
                }
                return '-';
            })
            ->addColumn('action', function ($row) {
                return view('karyawan.cuti._actions', ['cuti' => $row]);
            })
            ->rawColumns(['periode_cuti', 'pejabat_sk', 'jenis_cuti', 'ket', 'action'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $cuti = DB::connection('sqlsrv')
            ->table('hrd_r_cuti')
            ->where('kd_karyawan', $id)
            ->where('urut_cuti', $urut)
            ->first();

        if (!$cuti) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $data = [
            'kd_karyawan' => $cuti->kd_karyawan,
            'urut_cuti' => $cuti->urut_cuti,
            'kd_cuti' => $cuti->kd_cuti,
            'pejabat' => $cuti->pejabat,
            'no_sk' => $cuti->no_sk,
            'tgl_sk' => Carbon::parse($cuti->tgl_sk)->format('Y-m-d'),
            'lama_hari' => $cuti->lama_hari,
            'tgl_mulai' => Carbon::parse($cuti->tgl_mulai)->format('Y-m-d'),
            'tgl_akhir' => Carbon::parse($cuti->tgl_akhir)->format('Y-m-d'),
            'ket' => $cuti->ket,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $cuti = DB::connection('sqlsrv')
            ->table('hrd_r_cuti')
            ->where('kd_karyawan', $id)
            ->where('urut_cuti', $urut)
            ->first();

        if (!$cuti) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kd_cuti' => 'required|integer|min:1',
            'pejabat' => 'required|string|max:50',
            'no_sk' => 'required|string|max:50',
            'tgl_sk' => 'required|date',
            'lama_hari' => 'required|integer|min:1',
            'tgl_mulai' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_mulai',
            'ket' => 'nullable|string',
        ], [
            'kd_cuti.required' => 'Kolom Jenis Cuti wajib diisi.',
            'kd_cuti.integer' => 'Kolom Jenis Cuti harus berupa angka.',
            'kd_cuti.min' => 'Kolom Jenis Cuti harus lebih dari 0.',
            'pejabat.required' => 'Kolom Pejabat Pemberi wajib diisi.',
            'pejabat.string' => 'Kolom Pejabat Pemberi harus berupa teks.',
            'pejabat.max' => 'Kolom Pejabat Pemberi maksimal 50 karakter.',
            'no_sk.required' => 'Kolom No. SK wajib diisi.',
            'no_sk.string' => 'Kolom No. SK harus berupa teks.',
            'no_sk.max' => 'Kolom No. SK maksimal 50 karakter.',
            'tgl_sk.required' => 'Kolom Tanggal SK wajib diisi.',
            'tgl_sk.date' => 'Kolom Tanggal SK harus berupa tanggal.',
            'lama_hari.required' => 'Kolom Lama Hari wajib diisi.',
            'lama_hari.integer' => 'Kolom Lama Hari harus berupa angka.',
            'lama_hari.min' => 'Kolom Lama Hari harus lebih dari 0.',
            'tgl_mulai.required' => 'Kolom Tanggal Mulai wajib diisi.',
            'tgl_mulai.date' => 'Kolom Tanggal Mulai harus berupa tanggal.',
            'tgl_akhir.required' => 'Kolom Tanggal Akhir wajib diisi.',
            'tgl_akhir.date' => 'Kolom Tanggal Akhir harus berupa tanggal.',
            'tgl_akhir.after_or_equal' => 'Tanggal Akhir harus sama dengan atau setelah Tanggal Mulai.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Update manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_cuti')
            ->where('kd_karyawan', $id)
            ->where('urut_cuti', $urut)
            ->update([
                'kd_cuti' => $request->kd_cuti,
                'pejabat' => $request->pejabat,
                'no_sk' => $request->no_sk,
                'tgl_sk' => $request->tgl_sk,
                'lama_hari' => $request->lama_hari,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_akhir' => $request->tgl_akhir,
                'ket' => $request->ket,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat cuti berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id, $urut)
    {
        try {
            $cuti = DB::connection('sqlsrv')
                ->table('hrd_r_cuti')
                ->where('kd_karyawan', $id)
                ->where('urut_cuti', $urut)
                ->first();

            if (!$cuti) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            // Hapus data dari database
            DB::connection('sqlsrv')
                ->table('hrd_r_cuti')
                ->where('kd_karyawan', $id)
                ->where('urut_cuti', $urut)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat cuti berhasil dihapus.',
                'code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'code' => 500,
            ], 500);
        }
    }
}
