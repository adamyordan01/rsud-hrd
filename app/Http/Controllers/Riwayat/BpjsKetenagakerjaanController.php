<?php

namespace App\Http\Controllers\Riwayat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BpjsKetenagakerjaan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\EmployeeProfileService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BpjsKetenagakerjaanController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);
        
        return view('karyawan.bpjs_ketenagakerjaan.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validated = $this->validateRequest($request, ['foto_kartu' => 'required|file|mimes:jpg,jpeg,png|max:2048']);

        try {
            return DB::connection('sqlsrv')->transaction(function () use ($request, $id, $validated) {
                // Simpan file sementara
                $file = $request->file('foto_kartu');
                $fileName = 'bpjs-ketenagakerjaan-' . $id . '-' . time() . '.' . $file->getClientOriginalExtension();
                $tempPath = $file->storeAs('temp', $fileName, 'hrd_files');

                // Ambil urut_kartu
                $urutBpjs = DB::connection('sqlsrv')
                    ->table('hrd_r_bpjs_ketenagakerjaan')
                    ->where('kd_karyawan', $id)
                    ->max('urut_kartu') ?? 0;

                // Insert data
                $inserted = DB::connection('sqlsrv')
                    ->table('hrd_r_bpjs_ketenagakerjaan')
                    ->insert([
                        'kd_karyawan' => $id,
                        'no_kartu' => $validated['no_kartu'],
                        'urut_kartu' => $urutBpjs + 1,
                        'foto_kartu' => 'bpjs-ketenagakerjaan/' . $fileName,
                        'status' => $validated['status'],
                    ]);

                if (!$inserted) {
                    // Hapus file sementara jika insert gagal
                    Storage::disk('hrd_files')->delete($tempPath);
                    throw new \Exception('Gagal menyimpan data BPJS Ketenagakerjaan');
                }

                // Pindahkan file dari temp ke permanen
                $permanentPath = 'bpjs-ketenagakerjaan/' . $fileName;
                Storage::disk('hrd_files')->move($tempPath, $permanentPath);

                return $this->jsonResponse(true, 'Data BPJS Ketenagakerjaan berhasil disimpan', [
                    'kd_karyawan' => $id,
                    'no_kartu' => $validated['no_kartu'],
                    'urut_kartu' => $urutBpjs + 1,
                    'foto_kartu' => $permanentPath,
                    'url_file' => Storage::disk('hrd_files')->url($permanentPath),
                    'status' => $validated['status'],
                ]);
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getData($id)
    {
        $query = BpjsKetenagakerjaan::where('kd_karyawan', $id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return view('karyawan.bpjs_ketenagakerjaan._status', compact('row'));
            })
            ->addColumn('file', function ($row) {
                return view('karyawan.bpjs_ketenagakerjaan._file', compact('row'));
            })
            ->addColumn('action', function ($row) {
                return view('karyawan.bpjs_ketenagakerjaan._actions', compact('row'));
            })
            ->rawColumns(['status', 'action', 'file'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $bpjs = DB::connection('sqlsrv')
            ->table('hrd_r_bpjs_ketenagakerjaan')
            ->where('kd_karyawan', $id)
            ->where('urut_kartu', $urut)
            ->first();

        if (!$bpjs) {
            return response()->json([
                'success' => false,
                'message' => 'Data BPJS Ketenagakerjaan tidak ditemukan',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kd_karyawan' => $bpjs->kd_karyawan,
                'no_kartu' => $bpjs->no_kartu,
                'urut_kartu' => $bpjs->urut_kartu,
                'foto_kartu' => $bpjs->foto_kartu,
                'url_file' => $bpjs->foto_kartu ? Storage::disk('hrd_files')->url($bpjs->foto_kartu) : null,
                'status' => $bpjs->status,
            ],
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $validated = $this->validateRequest($request, ['foto_kartu' => 'nullable|file|mimes:jpg,jpeg,png|max:2048']);

        try {
            return DB::connection('sqlsrv')->transaction(function () use ($request, $id, $urut, $validated) {
                $bpjs = DB::connection('sqlsrv')
                    ->table('hrd_r_bpjs_ketenagakerjaan')
                    ->where('kd_karyawan', $id)
                    ->where('urut_kartu', $urut)
                    ->first();

                if (!$bpjs) {
                    throw new \Exception('Data BPJS Ketenagakerjaan tidak ditemukan', 404);
                }

                $filePath = $bpjs->foto_kartu;
                $oldFilePath = $filePath;

                if ($request->hasFile('foto_kartu')) {
                    // Simpan file baru sementara
                    $file = $request->file('foto_kartu');
                    $fileName = 'bpjs-ketenagakerjaan-' . $id . '-' . time() . '.' . $file->getClientOriginalExtension();
                    $tempPath = $file->storeAs('temp', $fileName, 'hrd_files');
                    $filePath = 'bpjs-ketenagakerjaan/' . $fileName;
                }

                // Update data
                $updated = DB::connection('sqlsrv')
                    ->table('hrd_r_bpjs_ketenagakerjaan')
                    ->where('kd_karyawan', $id)
                    ->where('urut_kartu', $urut)
                    ->update([
                        'no_kartu' => $validated['no_kartu'],
                        'foto_kartu' => $filePath,
                        'status' => $validated['status'],
                    ]);

                if (!$updated && $request->hasFile('foto_kartu')) {
                    // Hapus file sementara jika update gagal
                    Storage::disk('hrd_files')->delete($tempPath);
                    throw new \Exception('Gagal memperbarui data BPJS Ketenagakerjaan');
                }

                // Jika ada file baru, pindahkan ke permanen dan hapus file lama
                if ($request->hasFile('foto_kartu')) {
                    Storage::disk('hrd_files')->move($tempPath, $filePath);
                    if ($oldFilePath) {
                        Storage::disk('hrd_files')->delete($oldFilePath);
                    }
                }

                return $this->jsonResponse(true, 'Data BPJS Ketenagakerjaan berhasil diperbarui', [
                    'kd_karyawan' => $id,
                    'no_kartu' => $validated['no_kartu'],
                    'urut_kartu' => $urut,
                    'foto_kartu' => $filePath,
                    'url_file' => $filePath ? Storage::disk('hrd_files')->url($filePath) : null,
                    'status' => $validated['status'],
                ]);
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    protected function validateRequest(Request $request, array $additionalRules = [])
    {
        $rules = array_merge([
            'no_kartu' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ], $additionalRules);

        $messages = [
            'no_kartu.required' => 'Nomor Kartu tidak boleh kosong',
            'no_kartu.string' => 'Nomor Kartu harus berupa string',
            'no_kartu.max' => 'Nomor Kartu tidak boleh lebih dari 255 karakter',
            'foto_kartu.required' => 'Foto Kartu tidak boleh kosong',
            'foto_kartu.file' => 'Foto Kartu harus berupa file',
            'foto_kartu.mimes' => 'Foto Kartu harus berupa jpg, jpeg, atau png',
            'foto_kartu.max' => 'Ukuran Foto Kartu tidak boleh lebih dari 2MB',
            'status.required' => 'Status tidak boleh kosong',
            'status.in' => 'Status harus 0 (Tidak Aktif) atau 1 (Aktif)',
        ];

        return Validator::make($request->all(), $rules, $messages)->validate();
    }

    protected function storeFile($file, $id)
    {
        $fileName = 'bpjs-ketenagakerjaan-' . $id . '-' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('bpjs-ketenagakerjaan', $fileName, 'hrd_files');
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
