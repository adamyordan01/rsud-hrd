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

class PenghargaanController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        return view('karyawan.penghargaan.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'pejabat' => 'required|string|max:50',
            'tgl_sk' => 'required|date',
            'no_sk' => 'nullable|string|max:50',
            'bentuk' => 'required|string|max:100',
            'event' => 'required|string|max:100',
            'ket' => 'nullable|string',
        ], [
            'pejabat.required' => 'Kolom Pejabat wajib diisi.',
            'pejabat.string' => 'Kolom Pejabat harus berupa teks.',
            'pejabat.max' => 'Kolom Pejabat maksimal 50 karakter.',
            'tgl_sk.required' => 'Kolom Tanggal SK wajib diisi.',
            'tgl_sk.date' => 'Kolom Tanggal SK harus berupa tanggal.',
            'no_sk.string' => 'Kolom No. SK harus berupa teks.',
            'no_sk.max' => 'Kolom No. SK maksimal 50 karakter.',
            'bentuk.required' => 'Kolom Bentuk Penghargaan wajib diisi.',
            'bentuk.string' => 'Kolom Bentuk Penghargaan harus berupa teks.',
            'bentuk.max' => 'Kolom Bentuk Penghargaan maksimal 100 karakter.',
            'event.required' => 'Kolom Event/Nama Penghargaan wajib diisi.',
            'event.string' => 'Kolom Event/Nama Penghargaan harus berupa teks.',
            'event.max' => 'Kolom Event/Nama Penghargaan maksimal 100 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Get urut_peng berikutnya
        $urutPeng = DB::connection('sqlsrv')
            ->table('hrd_r_penghargaan')
            ->where('kd_karyawan', $id)
            ->max('urut_peng') + 1;

        // Insert manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_penghargaan')
            ->insert([
                'kd_karyawan' => $id,
                'urut_peng' => $urutPeng,
                'pejabat' => $request->pejabat,
                'tgl_sk' => $request->tgl_sk,
                'no_sk' => $request->no_sk,
                'bentuk' => $request->bentuk,
                'event' => $request->event,
                'ket' => $request->ket,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Penghargaan berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function getPenghargaanData($id)
    {
        // Menggunakan Collection untuk menghindari masalah ORDER BY di SQL Server
        $penghargaan = DB::connection('sqlsrv')
            ->table('hrd_r_penghargaan')
            ->where('kd_karyawan', $id)
            ->get()
            ->sortByDesc('tgl_sk')
            ->sortByDesc('urut_peng');

        return DataTables::of($penghargaan)
            ->addIndexColumn()
            ->editColumn('tgl_sk', function ($row) {
                return Carbon::parse($row->tgl_sk)->translatedFormat('d M Y');
            })
            ->editColumn('pejabat_bentuk', function ($row) {
                return $row->pejabat . '<br><small class="text-muted">' . $row->bentuk . '</small>';
            })
            ->editColumn('sk_event', function ($row) {
                $noSk = $row->no_sk ? $row->no_sk : '<em class="text-muted">No. SK tidak ada</em>';
                return $noSk . '<br><small class="text-muted">' . $row->event . '</small>';
            })
            ->editColumn('ket', function ($row) {
                if ($row->ket) {
                    $ketShort = strlen($row->ket) > 50 ? substr($row->ket, 0, 50) . '...' : $row->ket;
                    return '<span title="' . e($row->ket) . '">' . e($ketShort) . '</span>';
                }
                return '-';
            })
            ->addColumn('action', function ($row) {
                return view('karyawan.penghargaan._actions', ['penghargaan' => $row]);
            })
            ->rawColumns(['pejabat_bentuk', 'sk_event', 'ket', 'action'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $penghargaan = DB::connection('sqlsrv')
            ->table('hrd_r_penghargaan')
            ->where('kd_karyawan', $id)
            ->where('urut_peng', $urut)
            ->first();

        if (!$penghargaan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $data = [
            'kd_karyawan' => $penghargaan->kd_karyawan,
            'urut_peng' => $penghargaan->urut_peng,
            'pejabat' => $penghargaan->pejabat,
            'tgl_sk' => Carbon::parse($penghargaan->tgl_sk)->format('Y-m-d'),
            'no_sk' => $penghargaan->no_sk,
            'bentuk' => $penghargaan->bentuk,
            'event' => $penghargaan->event,
            'ket' => $penghargaan->ket,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $penghargaan = DB::connection('sqlsrv')
            ->table('hrd_r_penghargaan')
            ->where('kd_karyawan', $id)
            ->where('urut_peng', $urut)
            ->first();

        if (!$penghargaan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'pejabat' => 'required|string|max:50',
            'tgl_sk' => 'required|date',
            'no_sk' => 'nullable|string|max:50',
            'bentuk' => 'required|string|max:100',
            'event' => 'required|string|max:100',
            'ket' => 'nullable|string',
        ], [
            'pejabat.required' => 'Kolom Pejabat wajib diisi.',
            'pejabat.string' => 'Kolom Pejabat harus berupa teks.',
            'pejabat.max' => 'Kolom Pejabat maksimal 50 karakter.',
            'tgl_sk.required' => 'Kolom Tanggal SK wajib diisi.',
            'tgl_sk.date' => 'Kolom Tanggal SK harus berupa tanggal.',
            'no_sk.string' => 'Kolom No. SK harus berupa teks.',
            'no_sk.max' => 'Kolom No. SK maksimal 50 karakter.',
            'bentuk.required' => 'Kolom Bentuk Penghargaan wajib diisi.',
            'bentuk.string' => 'Kolom Bentuk Penghargaan harus berupa teks.',
            'bentuk.max' => 'Kolom Bentuk Penghargaan maksimal 100 karakter.',
            'event.required' => 'Kolom Event/Nama Penghargaan wajib diisi.',
            'event.string' => 'Kolom Event/Nama Penghargaan harus berupa teks.',
            'event.max' => 'Kolom Event/Nama Penghargaan maksimal 100 karakter.',
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
            ->table('hrd_r_penghargaan')
            ->where('kd_karyawan', $id)
            ->where('urut_peng', $urut)
            ->update([
                'pejabat' => $request->pejabat,
                'tgl_sk' => $request->tgl_sk,
                'no_sk' => $request->no_sk,
                'bentuk' => $request->bentuk,
                'event' => $request->event,
                'ket' => $request->ket,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Penghargaan berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id, $urut)
    {
        try {
            $penghargaan = DB::connection('sqlsrv')
                ->table('hrd_r_penghargaan')
                ->where('kd_karyawan', $id)
                ->where('urut_peng', $urut)
                ->first();

            if (!$penghargaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            // Hapus data dari database
            DB::connection('sqlsrv')
                ->table('hrd_r_penghargaan')
                ->where('kd_karyawan', $id)
                ->where('urut_peng', $urut)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Penghargaan berhasil dihapus.',
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
