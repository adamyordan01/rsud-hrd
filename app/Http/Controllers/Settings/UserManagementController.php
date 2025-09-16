<?php

namespace App\Http\Controllers\Settings;

use App\Models\Karyawan;
use App\Models\WebAkses;
use App\Models\WebAplikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrd_view_user_management');
    }

    public function index()
    {
        $pageTitle = 'User Management';

        if (request()->ajax()) {
            $filterAkses = request()->get('filter_akses', 1); // Default HRD (kd_akses = 1)
            
            // Get users with access using raw query similar to original PHP
            $query = DB::table('web_akses as wa')
                ->join('view_tampil_karyawan as vtk', 'wa.kd_karyawan', '=', 'vtk.kd_karyawan')
                ->join('web_aplikasi as wap', 'wa.kd_akses', '=', 'wap.kd_akses')
                ->select(
                    'wa.kd_karyawan',
                    'wa.kd_akses',
                    DB::raw("CONCAT(COALESCE(vtk.gelar_depan, ''), ' ', vtk.nama, COALESCE(CONCAT(' ', vtk.gelar_belakang), '')) as nama_lengkap"),
                    'vtk.nip_baru as nip',
                    'wap.akses'
                );

            // Apply filter if specified
            if ($filterAkses && $filterAkses != 'all') {
                $query->where('wa.kd_akses', $filterAkses);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('employee_info', function ($row) {
                    return view('settings.user-management.datatables-column._employee_info', compact('row'));
                })
                ->addColumn('access_level', function ($row) {
                    return '<span class="badge badge-light-primary">' . $row->akses . '</span>';
                })
                ->addColumn('status', function ($row) {
                    return '<span class="badge badge-light-success">Aktif</span>';
                })
                ->addColumn('action', function ($row) {
                    return view('settings.user-management.datatables-column._actions', compact('row'));
                })
                ->filterColumn('employee_info', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('vtk.nama', 'like', "%{$keyword}%")
                          ->orWhere('vtk.nip_baru', 'like', "%{$keyword}%")
                          ->orWhere('vtk.gelar_depan', 'like', "%{$keyword}%")
                          ->orWhere('vtk.gelar_belakang', 'like', "%{$keyword}%")
                          ->orWhere('wa.kd_karyawan', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('employee_info', function ($query, $order) {
                    $query->orderBy('vtk.nama', $order);
                })
                ->orderColumn('access_level', function ($query, $order) {
                    $query->orderBy('wap.akses', $order);
                })
                ->rawColumns(['employee_info', 'access_level', 'status', 'action'])
                ->make(true);
        }

        // Get access levels for dropdown
        $accessLevels = WebAplikasi::getAccessLevels();
        
        // Get employees for dropdown (excluding those with kd_karyawan = '001723' special case)
        $employees = DB::table('view_tampil_karyawan')
            ->select(
                'kd_karyawan', 
                DB::raw("CONCAT(COALESCE(gelar_depan, ''), ' ', nama, COALESCE(CONCAT(' ', gelar_belakang), '')) as nama_lengkap"),
                'nip_baru as nip'
            )
            ->whereNotIn('kd_karyawan', function ($query) {
                $query->select('kd_karyawan')->from('web_akses');
            })
            ->get()
            ->sortBy('nama_lengkap');

        return view('settings.user-management.index', compact('pageTitle', 'accessLevels', 'employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_karyawan' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // Check if combination of kd_karyawan + kd_akses already exists
                    $exists = DB::table('web_akses')
                        ->where('kd_karyawan', $value)
                        ->where('kd_akses', $request->kd_akses)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Karyawan sudah memiliki akses untuk aplikasi ini.');
                    }
                }
            ],
            'kd_akses' => 'required|integer|exists:web_aplikasi,kd_akses',
        ], [
            'kd_karyawan.required' => 'Karyawan harus dipilih.',
            'kd_akses.required' => 'Level akses harus dipilih.',
            'kd_akses.exists' => 'Level akses tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        try {
            WebAkses::create([
                'kd_karyawan' => $request->kd_karyawan,
                'kd_akses' => $request->kd_akses,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan ke sistem.',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    public function show($kdKaryawan)
    {
        try {
            $userAccess = DB::table('web_akses as wa')
                ->join('view_tampil_karyawan as vtk', 'wa.kd_karyawan', '=', 'vtk.kd_karyawan')
                ->join('web_aplikasi as wap', 'wa.kd_akses', '=', 'wap.kd_akses')
                ->select(
                    'wa.kd_karyawan',
                    'wa.kd_akses',
                    DB::raw("CONCAT(COALESCE(vtk.gelar_depan, ''), ' ', vtk.nama, COALESCE(CONCAT(' ', vtk.gelar_belakang), '')) as nama_lengkap"),
                    'vtk.nip_baru as nip',
                    'wap.akses'
                )
                ->where('wa.kd_karyawan', $kdKaryawan)
                ->first();

            if (!$userAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data user tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $userAccess,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    public function edit($kdKaryawan)
    {
        try {
            $userAccess = DB::table('web_akses as wa')
                ->join('view_tampil_karyawan as vtk', 'wa.kd_karyawan', '=', 'vtk.kd_karyawan')
                ->join('web_aplikasi as wap', 'wa.kd_akses', '=', 'wap.kd_akses')
                ->select(
                    'wa.kd_karyawan',
                    'wa.kd_akses',
                    DB::raw("CONCAT(COALESCE(vtk.gelar_depan, ''), ' ', vtk.nama, COALESCE(CONCAT(' ', vtk.gelar_belakang), '')) as nama_lengkap"),
                    'vtk.nip_baru as nip',
                    'wap.akses'
                )
                ->where('wa.kd_karyawan', $kdKaryawan)
                ->first();

            if (!$userAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $userAccess,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    public function update(Request $request, $kdKaryawan)
    {
        $validator = Validator::make($request->all(), [
            'kd_akses' => [
                'required',
                'integer',
                'exists:web_aplikasi,kd_akses',
                function ($attribute, $value, $fail) use ($request, $kdKaryawan) {
                    // Get current kd_akses for this specific record (from request if available)
                    $currentKdAkses = $request->get('current_kd_akses');
                    
                    // If we're changing to a different access level, check if combination already exists
                    if ($currentKdAkses && $currentKdAkses != $value) {
                        $exists = DB::table('web_akses')
                            ->where('kd_karyawan', $kdKaryawan)
                            ->where('kd_akses', $value)
                            ->exists();
                        
                        if ($exists) {
                            $fail('Karyawan sudah memiliki akses untuk aplikasi ini.');
                        }
                    }
                }
            ],
        ], [
            'kd_akses.required' => 'Level akses harus dipilih.',
            'kd_akses.exists' => 'Level akses tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        try {
            // Get current kd_akses from request for precise update
            $currentKdAkses = $request->get('current_kd_akses');
            
            if (!$currentKdAkses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data akses saat ini tidak ditemukan.',
                    'code' => 400,
                ], 400);
            }

            // Update using both kd_karyawan and current kd_akses for precision
            $updated = DB::table('web_akses')
                ->where('kd_karyawan', $kdKaryawan)
                ->where('kd_akses', $currentKdAkses)
                ->update(['kd_akses' => $request->kd_akses]);
            
            if ($updated === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Level akses berhasil diperbarui.',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    public function destroy(Request $request, $kdKaryawan)
    {
        try {
            // Check special case for kd_karyawan = '001723' (Laila Amalia)
            if ($kdKaryawan === '001723') {
                return response()->json([
                    'success' => false,
                    'message' => 'User ini tidak dapat dihapus dari sistem.',
                    'code' => 403,
                ], 403);
            }

            // Get kd_akses from request for more precise deletion
            $kdAkses = $request->get('kd_akses');
            
            // Build the deletion query
            $deleteQuery = DB::table('web_akses')
                ->where('kd_karyawan', $kdKaryawan);
            
            // If kd_akses is provided, add it to the where clause for precise deletion
            if ($kdAkses) {
                $deleteQuery->where('kd_akses', $kdAkses);
            }
            
            $deleted = $deleteQuery->delete();
            
            if ($deleted === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.',
                    'code' => 404,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus dari sistem.',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Get available employees for dropdown based on access level
     */
    public function getAvailableEmployeesByAccess(Request $request)
    {
        try {
            $kdAkses = $request->get('kd_akses');
            
            if (!$kdAkses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Level akses harus dipilih terlebih dahulu.',
                    'code' => 400,
                ], 400);
            }

            $employees = DB::table('view_tampil_karyawan')
                ->select(
                    'kd_karyawan', 
                    DB::raw("CONCAT(COALESCE(gelar_depan, ''), ' ', nama, COALESCE(CONCAT(' ', gelar_belakang), '')) as nama_lengkap"),
                    'nip_baru as nip'
                )
                ->whereNotIn('kd_karyawan', function ($query) use ($kdAkses) {
                    $query->select('kd_karyawan')
                          ->from('web_akses')
                          ->where('kd_akses', $kdAkses);
                })
                ->get()
                ->map(function ($employee) {
                    $nip = $employee->nip ? " - {$employee->nip}" : '';
                    return [
                        'kd_karyawan' => $employee->kd_karyawan,
                        'display_text' => "{$employee->kd_karyawan} - " . trim($employee->nama_lengkap) . $nip,
                        'nama_lengkap' => trim($employee->nama_lengkap),
                        'nip' => $employee->nip
                    ];
                })
                ->sortBy('display_text')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $employees,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data karyawan: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }
}
