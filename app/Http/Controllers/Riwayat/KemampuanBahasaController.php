<?php

namespace App\Http\Controllers\Riwayat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bahasa;
use App\Models\KemampuanBahasa;
use App\Models\TingkatBahasa;
use App\Services\EmployeeProfileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KemampuanBahasaController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }

    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);

        // $bahasa = Bahasa::orderBy('kd_bahasa', 'asc')
        //     ->get()
        //     ->map(function ($item) {
        //         return [
        //             'id' => $item->kd_bahasa,
        //             'text' => $item->bahasa,
        //         ];
        //     });
        // $bahasa = Bahasa::orderBy('bahasa', 'asc')->get();

        // $data = [
        //     'bahasa' => $bahasa,
        //     'pageTitle' => 'Kemampuan Bahasa',
        // ];

        $data['bahasa'] = Bahasa::orderBy('bahasa', 'asc')
            ->get();
        $data['tingkat_bahasa'] = TingkatBahasa::orderBy('tingkat_bahasa', 'asc')
            ->get();

        $data['pageTitle'] = 'Kemampuan Bahasa';

        return view('karyawan.kemampuan_bahasa.index', $data);
    }

    public function getData($id)
    {
        $query = KemampuanBahasa::with(['karyawan', 'bahasa', 'tingkatBahasa'])
            ->where('kd_karyawan', $id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($item) {
                return view('karyawan.kemampuan_bahasa._actions', [
                    'item' => $item,
                ]);
            })
            ->editColumn('bahasa', function ($item) {
                return $item->bahasa->bahasa ?? '-';
            })
            ->editColumn('tingkat_bahasa', function ($item) {
                return $item->tingkatBahasa->tingkat_bahasa ?? '-';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request, $id)
    {
        $validated = $this->validatedRequest($request);

        try {
            return DB::connection('sqlsrv')->transaction(function () use ($request, $id, $validated) {
                // ambil urut_bahasa terakhir
                $urutBahasa = KemampuanBahasa::where('kd_karyawan', $id)
                    ->max('urut_bahasa') ?? 0;

                // Insert data
                $inserted = DB::connection('sqlsrv')
                    ->table('hrd_r_bahasa')
                    ->insert([
                        'kd_karyawan' => $id,
                        'kd_bahasa' => $validated['bahasa'],
                        'kd_tingkat_bahasa' => $validated['tingkat_bahasa'],
                        'urut_bahasa' => $urutBahasa + 1,
                    ]);
                
                if (!$inserted) {
                    return $this->jsonResponse(false, 'Gagal menyimpan data kemampuan bahasa.', null, 500);
                }

                return $this->jsonResponse(true, 'Data kemampuan bahasa berhasil disimpan.', null, 200);
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(), null, 500);
        }
    }

    public function edit($id, $urut)
    {
        $bahasa = KemampuanBahasa::with(['bahasa', 'tingkatBahasa'])
            ->where('kd_karyawan', $id)
            ->where('urut_bahasa', $urut)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Data kemampuan bahasa berhasil ditemukan.',
            'data' => $bahasa,
        ], 200);
    }

    public function update(Request $request, $id, $urut)
    {
        $validated = $this->validatedRequest($request);

        try {
            return DB::connection('sqlsrv')->transaction(function () use ($request, $id, $urut, $validated) {
                // Update data
                $updated = DB::connection('sqlsrv')
                    ->table('hrd_r_bahasa')
                    ->where('kd_karyawan', $id)
                    ->where('urut_bahasa', $urut)
                    ->update([
                        'kd_bahasa' => $validated['bahasa'],
                        'kd_tingkat_bahasa' => $validated['tingkat_bahasa'],
                    ]);

                if (!$updated) {
                    return $this->jsonResponse(false, 'Gagal memperbarui data kemampuan bahasa.', null, 500);
                }

                return $this->jsonResponse(true, 'Data kemampuan bahasa berhasil diperbarui.', null, 200);
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage(), null, 500);
        }
    }

    public function destroy($id, $urut)
    {
        try {
            return DB::connection('sqlsrv')->transaction(function () use ($id, $urut) {
                // Hapus data
                $deleted = DB::connection('sqlsrv')
                    ->table('hrd_r_bahasa')
                    ->where('kd_karyawan', $id)
                    ->where('urut_bahasa', $urut)
                    ->delete();

                if (!$deleted) {
                    return $this->jsonResponse(false, 'Gagal menghapus data kemampuan bahasa.', null, 500);
                }

                return $this->jsonResponse(true, 'Data kemampuan bahasa berhasil dihapus.', null, 200);
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(), null, 500);
        }
    }

    protected function validatedRequest(Request $request, array $additionalRules = [])
    {
        $rules = array_merge([
            'bahasa' => 'required|exists:hrd_bahasa,kd_bahasa',
            'tingkat_bahasa' => 'required|exists:hrd_tingkat_bahasa,kd_tingkat_bahasa',
        ], $additionalRules);

        $messages = [
            'bahasa.required' => 'Bahasa harus diisi.',
            'bahasa.exists' => 'Bahasa yang dipilih tidak valid.',
            'tingkat_bahasa.required' => 'Tingkat bahasa harus diisi.',
            'tingkat_bahasa.exists' => 'Tingkat bahasa yang dipilih tidak valid.',
        ];

        return Validator::make($request->all(), $rules, $messages)->validate();
    }

    protected function jsonResponse($success, $message, $data = null, $code = null)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'code' => $success ? ($code ?? 200) : ($code ?? 500),
        ], $success ? ($code ?? 200) : ($code ?? 500));
    }
}
