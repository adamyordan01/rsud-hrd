<?php

namespace App\Http\Controllers\Riwayat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HubunganKeluarga;
use App\Models\JenjangPendidikan;
use App\Models\Keluarga;
use App\Models\Pekerjaan;
use App\Services\EmployeeProfileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KeluargaController extends Controller
{
    
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }
    
    public function index($id)
    {
        $data = $this->employeeProfileService->getEmployeeProfile($id);

        $pekerjaan = Pekerjaan::get();
        $pendidikan = JenjangPendidikan::get();
        $hubungan = HubunganKeluarga::get();

        $data['pekerjaan'] = $pekerjaan;
        $data['pendidikan'] = $pendidikan;
        $data['hubungan'] = $hubungan;
        
        return view('karyawan.keluarga.index', $data);
    }

    public function store(Request $request, $id)
    {
        $validated = $this->validateRequest($request);

        try {
            return DB::connection('sqlsrv')->transaction(function () use ($request, $id, $validated) {
                // Ambil urut_keluarga
                $urutKeluarga = DB::connection('sqlsrv')
                    ->table('hrd_r_keluarga')
                    ->where('kd_karyawan', $id)
                    ->max('urut_klrg') ?? 0;

                // Insert data
                $inserted = DB::connection('sqlsrv')
                    ->table('hrd_r_keluarga')
                    ->insert([
                        'kd_karyawan' => $id,
                        'urut_klrg' => $urutKeluarga + 1,
                        'kd_pekerjaan' => $validated['pekerjaan'],
                        'kd_jenjang_didik' => $validated['pendidikan'],
                        'kd_hub_klrg' => $validated['hubungan'],
                        'jk' => $validated['sex'],
                        'nama' => strtoupper($validated['nama']),
                        'tempat_lahir' => strtoupper($validated['tempat_lahir']),
                        'tgl_lahir' => $validated['tgl_lahir'],
                        'created_by' => auth()->user()->kd_karyawan,
                        'created_at' => now(),
                        'updated_by' => auth()->user()->kd_karyawan,
                        'updated_at' => now(),
                    ]);

                if (!$inserted) {
                    return $this->jsonResponse(false, 'Gagal menyimpan data keluarga.', null, 500);
                }

                return $this->jsonResponse(true, 'Data keluarga berhasil disimpan.', null, 200);
            });
        } catch (\Exception $th) {
            return $this->jsonResponse(false, 'Terjadi kesalahan: ' . $th->getMessage(), null, 500);
        }
    }

    public function getData($id)
    {
        $query = Keluarga::where('kd_karyawan', $id)
            ->with(['hubungan', 'pendidikan', 'pekerjaan']);

        return DataTables::of($query)
            ->addIndexColumn()
            // nama buat menjadi Adam Yordan <br> Ayah
            ->addColumn('name', function ($row) {
                return view('karyawan.keluarga._name', compact('row'));
            })
            ->addColumn('pekerjaan', function ($row) {
                return $row->pekerjaan->pekerjaan ?? '-';
            })
            ->addColumn('pendidikan', function ($row) {
                return $row->pendidikan->jenjang_didik ?? '-';
            })
            ->addColumn('hubungan', function ($row) {
                return $row->hubungan->hub_klrg ?? '-';
            })
            ->addColumn('urut', function ($row) {
                return $row->urut_klrg;
            })
            ->addColumn('tempat_tanggal_lahir', function ($row) {
                return $row->tempat_lahir . ', ' . $row->tgl_lahir->format('d-m-Y');
            })
            ->addColumn('jenis_kelamin', function ($row) {
                return $row->jk == 1 ? 'Laki-laki' : 'Perempuan';
            })
            ->addColumn('action', function ($row) {
                return view('karyawan.keluarga._actions', compact('row'));
            })
            ->orderColumn('urut', function ($query, $order) {
                $query->orderBy('urut_klrg', $order);
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('nama', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action', 'name', 'tempat_tanggal_lahir', 'jenis_kelamin'])
            ->make(true);
    }

    public function edit($id, $urut)
    {
        $keluarga = Keluarga::where('kd_karyawan', $id)
            ->where('urut_klrg', $urut)
            ->firstOrFail();
            
        return response()->json([
            'success' => true,
            'data' => [
                'kd_karyawan' => $keluarga->kd_karyawan,
                'nama' => $keluarga->nama,
                'urut_klrg' => $keluarga->urut_klrg,
                'pekerjaan' => $keluarga->kd_pekerjaan,
                'pendidikan' => $keluarga->kd_jenjang_didik,
                'hubungan' => $keluarga->kd_hub_klrg,
                'tempat_lahir' => $keluarga->tempat_lahir,
                'tgl_lahir' => $keluarga->tgl_lahir->format('Y-m-d'),
                'sex' => $keluarga->jk,
            ],
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id, $urut)
    {
        $validated = $this->validateRequest($request);

        try {
            return DB::connection('sqlsrv')->transaction(function () use ($request, $id, $urut, $validated) {
                // Update data
                $updated = DB::connection('sqlsrv')
                    ->table('hrd_r_keluarga')
                    ->where('kd_karyawan', $id)
                    ->where('urut_klrg', $urut)
                    ->update([
                        'kd_pekerjaan' => $validated['pekerjaan'],
                        'kd_jenjang_didik' => $validated['pendidikan'],
                        'kd_hub_klrg' => $validated['hubungan'],
                        'jk' => $validated['sex'],
                        'nama' => strtoupper($validated['nama']),
                        'tempat_lahir' => strtoupper($validated['tempat_lahir']),
                        'tgl_lahir' => $validated['tgl_lahir'],
                        'updated_by' => auth()->user()->kd_karyawan,
                        'updated_at' => now(),
                    ]);

                if (!$updated) {
                    return $this->jsonResponse(false, 'Gagal memperbarui data keluarga.', null, 500);
                }

                return $this->jsonResponse(true, 'Data keluarga berhasil diperbarui.', null, 200);
            });
        } catch (\Exception $th) {
            return $this->jsonResponse(false, 'Terjadi kesalahan: ' . $th->getMessage(), null, 500);
        }
    }

    public function destroy($id, $urut)
    {
        try {
            return DB::connection('sqlsrv')->transaction(function () use ($id, $urut) {
                \Log::info('Menghapus keluarga', ['kd_karyawan' => $id, 'urut_klrg' => $urut]);

                // Verifikasi data sebelum hapus
                $exists = DB::connection('sqlsrv')
                    ->table('hrd_r_keluarga')
                    ->where('kd_karyawan', $id)
                    ->where('urut_klrg', $urut)
                    ->exists();

                if (!$exists) {
                    throw new \Exception('Data keluarga tidak ditemukan.');
                }

                $deleted = DB::connection('sqlsrv')
                    ->table('hrd_r_keluarga')
                    ->where('kd_karyawan', $id)
                    ->where('urut_klrg', $urut)
                    ->delete();

                if (!$deleted) {
                    throw new \Exception('Gagal menghapus data keluarga.');
                }

                // Reorder urut_klrg setelah penghapusan
                $this->reorderUrutKeluarga($id);

                return $this->jsonResponse(true, 'Data keluarga berhasil dihapus.');
            });
        } catch (\Exception $e) {
            \Log::error('Error menghapus keluarga', ['error' => $e->getMessage(), 'kd_karyawan' => $id, 'urut_klrg' => $urut]);
            return $this->jsonResponse(false, 'Terjadi kesalahan: ' . $e->getMessage(), null, 500);
        }
    }

    public function reorder(Request $request, $id)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|min:1',
        ]);

        try {
            return DB::connection('sqlsrv')->transaction(function () use ($id, $validated) {
                \Log::info('Reordering keluarga', ['kd_karyawan' => $id, 'order' => $validated['order']]);

                // Ambil semua data keluarga untuk karyawan ini
                $keluargaList = DB::connection('sqlsrv')
                    ->table('hrd_r_keluarga')
                    ->where('kd_karyawan', $id)
                    ->orderBy('urut_klrg')
                    ->get(['urut_klrg', 'nama', 'created_at']);

                if ($keluargaList->count() !== count($validated['order'])) {
                    throw new \Exception('Jumlah data tidak sesuai dengan urutan yang diberikan.');
                }

                // Update urutan berdasarkan array yang diterima
                foreach ($validated['order'] as $newIndex => $oldUrut) {
                    $updated = DB::connection('sqlsrv')
                        ->table('hrd_r_keluarga')
                        ->where('kd_karyawan', $id)
                        ->where('urut_klrg', $oldUrut)
                        ->update([
                            'urut_klrg' => 9999 + $newIndex, // Temporary value
                            'updated_by' => auth()->user()->kd_karyawan,
                            'updated_at' => now(),
                        ]);

                    if (!$updated) {
                        throw new \Exception("Gagal memperbarui urut_klrg: $oldUrut");
                    }
                }

                // Update ke nilai final
                foreach ($validated['order'] as $newIndex => $oldUrut) {
                    DB::connection('sqlsrv')
                        ->table('hrd_r_keluarga')
                        ->where('kd_karyawan', $id)
                        ->where('urut_klrg', 9999 + $newIndex)
                        ->update(['urut_klrg' => $newIndex + 1]);
                }

                return $this->jsonResponse(true, 'Urutan keluarga berhasil diperbarui.');
            });
        } catch (\Exception $e) {
            \Log::error('Error reordering keluarga', ['error' => $e->getMessage(), 'kd_karyawan' => $id]);
            return $this->jsonResponse(false, 'Terjadi kesalahan: ' . $e->getMessage(), null, 500);
        }
    }

    protected function reorderUrutKeluarga($id)
    {
        \Log::info('Mengurutkan ulang keluarga', ['kd_karyawan' => $id]);

        // Ambil data dengan kolom unik untuk menghindari duplikasi
        $keluarga = DB::connection('sqlsrv')
            ->table('hrd_r_keluarga')
            ->where('kd_karyawan', $id)
            ->orderBy('urut_klrg')
            ->orderBy('created_at') // Tiebreaker untuk stabilitas
            ->get();

        foreach ($keluarga as $index => $item) {
            $updated = DB::connection('sqlsrv')
                ->table('hrd_r_keluarga')
                ->where('kd_karyawan', $id)
                ->where('nama', $item->nama) // Gunakan nama sebagai pengenal unik
                ->where('created_at', $item->created_at)
                ->update(['urut_klrg' => $index + 1]);

            if (!$updated) {
                \Log::warning('Gagal mengurutkan ulang', ['kd_karyawan' => $id, 'item' => $item]);
            }
        }
    }

    protected function validateRequest(Request $request, array $additionalRules = [])
    {
        $rules = array_merge([
            'pekerjaan' => 'required|exists:hrd_pekerjaan,kd_pekerjaan',
            'pendidikan' => 'required|exists:hrd_jenjang_pendidikan,kd_jenjang_didik',
            'hubungan' => 'required|exists:hrd_hub_keluarga,kd_hub_klrg',
            'sex' => 'required|in:0,1', // 0: Laki-laki, 1: Perempuan
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:100',
            'tgl_lahir' => 'required|date_format:Y-m-d',
        ], $additionalRules);

        $messages = [
            'pekerjaan.required' => 'Pekerjaan harus diisi.',
            'pendidikan.required' => 'Pendidikan harus diisi.',
            'hubungan.required' => 'Hubungan harus diisi.',
            'sex.required' => 'Jenis kelamin harus diisi.',
            'nama.required' => 'Nama harus diisi.',
            'tempat_lahir.required' => 'Tempat lahir harus diisi.',
            'tempat_lahir.max' => 'Tempat lahir tidak boleh lebih dari 100 karakter.',
            'tgl_lahir.required' => 'Tanggal lahir harus diisi.',
            'tgl_lahir.date_format' => 'Format tanggal lahir harus YYYY-MM-DD.',
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
