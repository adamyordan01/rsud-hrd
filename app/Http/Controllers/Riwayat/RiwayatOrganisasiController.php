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

class RiwayatOrganisasiController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        return view('karyawan.riwayat-organisasi.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'pejabat' => 'required|string|max:50',
            'no_sk' => 'required|string|max:50',
            'tgl_sk' => 'required|date',
            'organisasi' => 'required|string|max:100',
            'jabatan' => 'required|string|max:100',
            'ket' => 'nullable|string',
        ], [
            'pejabat.required' => 'Kolom Pejabat wajib diisi.',
            'pejabat.string' => 'Kolom Pejabat harus berupa teks.',
            'pejabat.max' => 'Kolom Pejabat maksimal 50 karakter.',
            'no_sk.required' => 'Kolom No. SK wajib diisi.',
            'no_sk.string' => 'Kolom No. SK harus berupa teks.',
            'no_sk.max' => 'Kolom No. SK maksimal 50 karakter.',
            'tgl_sk.required' => 'Kolom Tanggal SK wajib diisi.',
            'tgl_sk.date' => 'Kolom Tanggal SK harus berupa tanggal.',
            'organisasi.required' => 'Kolom Organisasi wajib diisi.',
            'organisasi.string' => 'Kolom Organisasi harus berupa teks.',
            'organisasi.max' => 'Kolom Organisasi maksimal 100 karakter.',
            'jabatan.required' => 'Kolom Jabatan wajib diisi.',
            'jabatan.string' => 'Kolom Jabatan harus berupa teks.',
            'jabatan.max' => 'Kolom Jabatan maksimal 100 karakter.',
            'ket.string' => 'Kolom Keterangan harus berupa teks.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Get urut_org berikutnya
        $urutOrg = DB::connection('sqlsrv')
            ->table('hrd_r_organisasi')
            ->where('kd_karyawan', $id)
            ->max('urut_org') + 1;

        // Insert manual menggunakan query builder
        DB::connection('sqlsrv')
            ->table('hrd_r_organisasi')
            ->insert([
                'kd_karyawan' => $id,
                'urut_org' => $urutOrg,
                'pejabat' => $request->pejabat,
                'no_sk' => $request->no_sk,
                'tgl_sk' => $request->tgl_sk,
                'organisasi' => $request->organisasi,
                'jabatan' => $request->jabatan,
                'ket' => $request->ket,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Organisasi berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function getRiwayatOrganisasiData($id)
    {
        // Menggunakan Collection untuk menghindari masalah ORDER BY di SQL Server
        $riwayatOrganisasi = DB::connection('sqlsrv')
            ->table('hrd_r_organisasi')
            ->where('kd_karyawan', $id)
            ->get()
            ->sortByDesc('tgl_sk')
            ->sortByDesc('urut_org');

        return DataTables::of($riwayatOrganisasi)
            ->addIndexColumn()
            ->editColumn('tgl_sk', function ($row) {
                return Carbon::parse($row->tgl_sk)->translatedFormat('d M Y');
            })
            ->editColumn('pejabat_organisasi', function ($row) {
                return $row->pejabat . '<br><small class="text-muted">' . $row->organisasi . '</small>';
            })
            ->editColumn('sk_jabatan', function ($row) {
                return $row->no_sk . '<br><small class="text-muted">Jabatan: ' . $row->jabatan . '</small>';
            })
            ->editColumn('ket', function ($row) {
                return $row->ket ? e($row->ket) : '-';
            })
            ->addColumn('action', function ($row) {
                return view('karyawan.riwayat-organisasi._actions', ['riwayatOrganisasi' => $row]);
            })
            ->rawColumns(['pejabat_organisasi', 'sk_jabatan', 'action'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $riwayatOrganisasi = DB::connection('sqlsrv')
            ->table('hrd_r_organisasi')
            ->where('kd_karyawan', $id)
            ->where('urut_org', $urut)
            ->first();

        if (!$riwayatOrganisasi) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $data = [
            'kd_karyawan' => $riwayatOrganisasi->kd_karyawan,
            'urut_org' => $riwayatOrganisasi->urut_org,
            'pejabat' => $riwayatOrganisasi->pejabat,
            'no_sk' => $riwayatOrganisasi->no_sk,
            'tgl_sk' => Carbon::parse($riwayatOrganisasi->tgl_sk)->format('Y-m-d'),
            'organisasi' => $riwayatOrganisasi->organisasi,
            'jabatan' => $riwayatOrganisasi->jabatan,
            'ket' => $riwayatOrganisasi->ket,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $riwayatOrganisasi = DB::connection('sqlsrv')
            ->table('hrd_r_organisasi')
            ->where('kd_karyawan', $id)
            ->where('urut_org', $urut)
            ->first();

        if (!$riwayatOrganisasi) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'pejabat' => 'required|string|max:50',
            'no_sk' => 'required|string|max:50',
            'tgl_sk' => 'required|date',
            'organisasi' => 'required|string|max:100',
            'jabatan' => 'required|string|max:100',
            'ket' => 'nullable|string',
        ], [
            'pejabat.required' => 'Kolom Pejabat wajib diisi.',
            'pejabat.string' => 'Kolom Pejabat harus berupa teks.',
            'pejabat.max' => 'Kolom Pejabat maksimal 50 karakter.',
            'no_sk.required' => 'Kolom No. SK wajib diisi.',
            'no_sk.string' => 'Kolom No. SK harus berupa teks.',
            'no_sk.max' => 'Kolom No. SK maksimal 50 karakter.',
            'tgl_sk.required' => 'Kolom Tanggal SK wajib diisi.',
            'tgl_sk.date' => 'Kolom Tanggal SK harus berupa tanggal.',
            'organisasi.required' => 'Kolom Organisasi wajib diisi.',
            'organisasi.string' => 'Kolom Organisasi harus berupa teks.',
            'organisasi.max' => 'Kolom Organisasi maksimal 100 karakter.',
            'jabatan.required' => 'Kolom Jabatan wajib diisi.',
            'jabatan.string' => 'Kolom Jabatan harus berupa teks.',
            'jabatan.max' => 'Kolom Jabatan maksimal 100 karakter.',
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
            ->table('hrd_r_organisasi')
            ->where('kd_karyawan', $id)
            ->where('urut_org', $urut)
            ->update([
                'pejabat' => $request->pejabat,
                'no_sk' => $request->no_sk,
                'tgl_sk' => $request->tgl_sk,
                'organisasi' => $request->organisasi,
                'jabatan' => $request->jabatan,
                'ket' => $request->ket,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Organisasi berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id, $urut)
    {
        try {
            $riwayatOrganisasi = DB::connection('sqlsrv')
                ->table('hrd_r_organisasi')
                ->where('kd_karyawan', $id)
                ->where('urut_org', $urut)
                ->first();

            if (!$riwayatOrganisasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            // Hapus data dari database
            DB::connection('sqlsrv')
                ->table('hrd_r_organisasi')
                ->where('kd_karyawan', $id)
                ->where('urut_org', $urut)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat Organisasi berhasil dihapus.',
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
