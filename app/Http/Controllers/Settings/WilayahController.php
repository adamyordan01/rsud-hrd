<?php

namespace App\Http\Controllers\Settings;

use App\Models\Propinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WilayahController extends Controller
{
    public function index()
    {
        $pageTitle = 'Master Wilayah';
        
        // Load minimal data - only provinsi for initial display
        // Everything else will be loaded via AJAX on demand
        try {
            $provinsi = Propinsi::select('kd_propinsi', 'propinsi')
                ->orderBy('kd_propinsi')
                ->get();

            return view('settings.wilayah.index', compact('pageTitle', 'provinsi'));
            
        } catch (\Exception $e) {
            Log::error('Error loading wilayah data: ' . $e->getMessage());
            $provinsi = collect(); // Empty collection as fallback
            session()->flash('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
            return view('settings.wilayah.index', compact('pageTitle', 'provinsi'));
        }
    }

    // New method: Load kabupaten on demand via AJAX
    public function loadKabupaten($provinsiId)
    {
        try {
            $kabupaten = Kabupaten::select('kd_kabupaten', 'kd_propinsi', 'kabupaten')
                ->where('kd_propinsi', $provinsiId)
                ->orderBy('kd_kabupaten')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kabupaten
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // New method: Load kecamatan on demand via AJAX
    public function loadKecamatan($kabupatenId)
    {
        try {
            $kecamatan = Kecamatan::select('kd_kecamatan', 'kd_kabupaten', 'kecamatan')
                ->where('kd_kabupaten', $kabupatenId)
                ->orderBy('kd_kecamatan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kecamatan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // New method: Load kelurahan on demand via AJAX
    public function loadKelurahan($kecamatanId)
    {
        try {
            $kelurahan = Kelurahan::select('kd_kelurahan', 'kd_kecamatan', 'kelurahan')
                ->where('kd_kecamatan', $kecamatanId)
                ->orderBy('kd_kelurahan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kelurahan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // API untuk cascade dropdown
    public function getKabupaten($provinsiId)
    {
        $kabupaten = Kabupaten::where('kd_propinsi', $provinsiId)
            ->orderBy('kd_kabupaten')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kabupaten,
        ]);
    }

    public function getKecamatan($kabupatenId)
    {
        $kecamatan = Kecamatan::where('kd_kabupaten', $kabupatenId)
            ->orderBy('kd_kecamatan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kecamatan,
        ]);
    }

    public function getKelurahan($kecamatanId)
    {
        $kelurahan = Kelurahan::where('kd_kecamatan', $kecamatanId)
            ->orderBy('kd_kelurahan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kelurahan,
        ]);
    }

    // CRUD untuk Provinsi (Level 1)
    public function storeProvinsi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'propinsi' => 'required|string|max:30|unique:propinsi,propinsi',
        ], [
            'propinsi.required' => 'Nama provinsi harus diisi.',
            'propinsi.max' => 'Nama provinsi maksimal 30 karakter.',
            'propinsi.unique' => 'Nama provinsi sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get next ID
            $result = DB::select("SELECT ISNULL(MAX(kd_propinsi), 0) as max_id FROM propinsi");
            $lastKd = $result[0]->max_id ?? 0;
            $newKd = $lastKd + 1;

            // Escape nama provinsi untuk SQL injection
            $namaProvinsi = str_replace("'", "''", trim($request->propinsi));
            $kdProvinsi = (int) $newKd;

            // Log untuk debugging
            Log::info('Attempting to insert provinsi:', [
                'kd_propinsi' => $kdProvinsi,
                'propinsi' => $namaProvinsi,
            ]);

            // Disable triggers untuk operasi insert
            DB::unprepared("ALTER TABLE propinsi DISABLE TRIGGER ALL");

            // Use unprepared insert untuk bypass trigger
            $sql = "INSERT INTO propinsi (kd_propinsi, propinsi) VALUES ({$kdProvinsi}, N'{$namaProvinsi}')";
            DB::unprepared($sql);

            // Re-enable triggers
            DB::unprepared("ALTER TABLE propinsi ENABLE TRIGGER ALL");

            // Get inserted data
            $provinsiData = DB::select("SELECT * FROM propinsi WHERE kd_propinsi = {$kdProvinsi}");
            
            if (empty($provinsiData)) {
                throw new \Exception('Provinsi tidak ditemukan setelah insert');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil disimpan.',
                'data' => $provinsiData[0],
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            // Make sure to re-enable triggers even if insert fails
            try {
                DB::unprepared("ALTER TABLE propinsi ENABLE TRIGGER ALL");
            } catch (\Exception $triggerEx) {
                Log::error('Failed to re-enable triggers after insert error: ' . $triggerEx->getMessage());
            }

            Log::error('Error storing provinsi: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan provinsi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editProvinsi($id)
    {
        $provinsi = Propinsi::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $provinsi,
            'code' => 200,
        ], 200);
    }

    public function updateProvinsi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'propinsi' => 'required|string|max:30|unique:propinsi,propinsi,' . $id . ',kd_propinsi',
        ], [
            'propinsi.required' => 'Nama provinsi harus diisi.',
            'propinsi.max' => 'Nama provinsi maksimal 30 karakter.',
            'propinsi.unique' => 'Nama provinsi sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Escape nama provinsi untuk SQL injection
            $namaProvinsi = str_replace("'", "''", trim($request->propinsi));
            $kdProvinsi = (int) $id;

            // Log untuk debugging
            Log::info('Attempting to update provinsi:', [
                'kd_propinsi' => $kdProvinsi,
                'propinsi' => $namaProvinsi,
            ]);

            // Disable triggers untuk operasi update
            DB::unprepared("ALTER TABLE propinsi DISABLE TRIGGER ALL");

            // Use unprepared update untuk bypass trigger
            $sql = "UPDATE propinsi SET propinsi = N'{$namaProvinsi}' WHERE kd_propinsi = {$kdProvinsi}";
            DB::unprepared($sql);

            // Re-enable triggers
            DB::unprepared("ALTER TABLE propinsi ENABLE TRIGGER ALL");

            // Get updated data
            $provinsiData = DB::select("SELECT * FROM propinsi WHERE kd_propinsi = {$kdProvinsi}");
            
            if (empty($provinsiData)) {
                throw new \Exception('Provinsi tidak ditemukan setelah update');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil diperbarui.',
                'data' => $provinsiData[0],
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            // Make sure to re-enable triggers even if update fails
            try {
                DB::unprepared("ALTER TABLE propinsi ENABLE TRIGGER ALL");
            } catch (\Exception $triggerEx) {
                Log::error('Failed to re-enable triggers after update error: ' . $triggerEx->getMessage());
            }

            Log::error('Error updating provinsi: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui provinsi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroyProvinsi($id)
    {
        try {
            DB::beginTransaction();

            Log::info('Starting provinsi delete operation for ID: ' . $id);

            // Step 1: Get all kabupaten IDs for this provinsi
            $kabupatenIds = DB::connection('sqlsrv')->table('kabupaten')
                ->where('kd_propinsi', $id)
                ->pluck('kd_kabupaten')
                ->toArray();

            if (!empty($kabupatenIds)) {
                // Step 2: Get all kecamatan IDs for these kabupaten
                $kecamatanIds = DB::connection('sqlsrv')->table('kecamatan')
                    ->whereIn('kd_kabupaten', $kabupatenIds)
                    ->pluck('kd_kecamatan')
                    ->toArray();

                if (!empty($kecamatanIds)) {
                    // Step 3: Delete kelurahan using raw SQL with disable trigger approach
                    try {
                        DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan DISABLE TRIGGER ALL");
                        
                        // Delete kelurahan in batches to avoid large IN clause
                        $chunks = array_chunk($kecamatanIds, 50);
                        foreach ($chunks as $chunk) {
                            $kecamatanIdsStr = implode(',', $chunk);
                            DB::connection('sqlsrv')->unprepared("DELETE FROM kelurahan WHERE kd_kecamatan IN ({$kecamatanIdsStr})");
                        }

                        DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
                    } catch (\Exception $kelurahanEx) {
                        // Re-enable triggers even if delete fails
                        try {
                            DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
                        } catch (\Exception $triggerEx) {
                            Log::error('Failed to re-enable kelurahan triggers: ' . $triggerEx->getMessage());
                        }
                        throw $kelurahanEx;
                    }

                    // Step 4: Delete kecamatan using raw SQL with disable trigger approach
                    try {
                        DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan DISABLE TRIGGER ALL");
                        
                        $kecamatanIdsStr = implode(',', $kecamatanIds);
                        DB::connection('sqlsrv')->unprepared("DELETE FROM kecamatan WHERE kd_kecamatan IN ({$kecamatanIdsStr})");

                        DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");
                    } catch (\Exception $kecamatanEx) {
                        // Re-enable triggers even if delete fails
                        try {
                            DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");
                        } catch (\Exception $triggerEx) {
                            Log::error('Failed to re-enable kecamatan triggers: ' . $triggerEx->getMessage());
                        }
                        throw $kecamatanEx;
                    }
                }

                // Step 5: Delete kabupaten using raw SQL with disable trigger approach
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten DISABLE TRIGGER ALL");
                    
                    $kabupatenIdsStr = implode(',', $kabupatenIds);
                    DB::connection('sqlsrv')->unprepared("DELETE FROM kabupaten WHERE kd_kabupaten IN ({$kabupatenIdsStr})");

                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten ENABLE TRIGGER ALL");
                } catch (\Exception $kabupatenEx) {
                    // Re-enable triggers even if delete fails
                    try {
                        DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten ENABLE TRIGGER ALL");
                    } catch (\Exception $triggerEx) {
                        Log::error('Failed to re-enable kabupaten triggers: ' . $triggerEx->getMessage());
                    }
                    throw $kabupatenEx;
                }
            }

            // Step 6: Delete provinsi using raw SQL with disable trigger approach
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE propinsi DISABLE TRIGGER ALL");
                
                DB::connection('sqlsrv')->unprepared("DELETE FROM propinsi WHERE kd_propinsi = {$id}");

                DB::connection('sqlsrv')->unprepared("ALTER TABLE propinsi ENABLE TRIGGER ALL");
            } catch (\Exception $provinsiEx) {
                // Re-enable triggers even if delete fails
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE propinsi ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable propinsi triggers: ' . $triggerEx->getMessage());
                }
                throw $provinsiEx;
            }

            DB::commit();

            Log::info('Provinsi delete operation completed successfully for ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting provinsi: ' . $e->getMessage());
            Log::error('Provinsi ID: ' . $id);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus provinsi: ' . $e->getMessage(),
            ], 500);
        }
    }

    // CRUD untuk Kabupaten (Level 2)
    public function storeKabupaten(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_propinsi' => 'required|exists:propinsi,kd_propinsi',
            'kabupaten' => [
                'required',
                'string',
                'max:30',
                Rule::unique('kabupaten')->where(function ($query) use ($request) {
                    return $query->where('kd_propinsi', $request->kd_propinsi);
                }),
            ],
        ], [
            'kd_propinsi.required' => 'Provinsi harus dipilih.',
            'kd_propinsi.exists' => 'Provinsi tidak ditemukan.',
            'kabupaten.required' => 'Nama kabupaten harus diisi.',
            'kabupaten.max' => 'Nama kabupaten maksimal 30 karakter.',
            'kabupaten.unique' => 'Nama kabupaten sudah ada di provinsi ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get next ID menggunakan raw SQL untuk avoid trigger issues
            $result = DB::connection('sqlsrv')->select("SELECT ISNULL(MAX(kd_kabupaten), 0) as max_id FROM kabupaten");
            $lastKd = $result[0]->max_id ?? 0;
            $newKd = $lastKd + 1;

            // Prepare data dengan escape untuk SQL injection
            $kdKabupaten = (int) $newKd;
            $kdProvinsi = (int) $request->kd_propinsi;
            $namaKabupaten = str_replace("'", "''", trim($request->kabupaten));

            Log::info('Attempting to insert kabupaten', [
                'kd_kabupaten' => $kdKabupaten,
                'kd_propinsi' => $kdProvinsi,
                'kabupaten' => $namaKabupaten,
            ]);

            // Disable triggers untuk insert
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten DISABLE TRIGGER ALL");

                // Insert menggunakan raw SQL untuk bypass trigger
                $sql = "INSERT INTO kabupaten (kd_kabupaten, kd_propinsi, kabupaten) VALUES ({$kdKabupaten}, {$kdProvinsi}, N'{$namaKabupaten}')";
                DB::connection('sqlsrv')->unprepared($sql);

                // Re-enable triggers
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten ENABLE TRIGGER ALL");

                // Verify insert dan ambil data
                $kabupatenData = DB::connection('sqlsrv')->select("SELECT * FROM kabupaten WHERE kd_kabupaten = {$kdKabupaten}");
                
                if (empty($kabupatenData)) {
                    throw new \Exception('Data kabupaten tidak ditemukan setelah insert');
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Kabupaten berhasil disimpan.',
                    'data' => $kabupatenData[0],
                ]);

            } catch (\Exception $insertEx) {
                // Re-enable triggers jika ada error
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kabupaten triggers: ' . $triggerEx->getMessage());
                }
                throw $insertEx;
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing kabupaten: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan kabupaten: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editKabupaten($provinsiId, $kabupatenId)
    {
        $kabupaten = Kabupaten::where('kd_propinsi', $provinsiId)
            ->where('kd_kabupaten', $kabupatenId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $kabupaten,
            'code' => 200,
        ], 200);
    }

    public function updateKabupaten(Request $request, $provinsiId, $kabupatenId)
    {
        $validator = Validator::make($request->all(), [
            'kabupaten' => 'required|string|max:30|unique:kabupaten,kabupaten,' . $kabupatenId . ',kd_kabupaten',
        ], [
            'kabupaten.required' => 'Nama kabupaten harus diisi.',
            'kabupaten.max' => 'Nama kabupaten maksimal 30 karakter.',
            'kabupaten.unique' => 'Nama kabupaten sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Prepare data dengan escape untuk SQL
            $namaKabupaten = str_replace("'", "''", trim($request->kabupaten));

            Log::info('Attempting to update kabupaten', [
                'kd_provinsi' => $provinsiId,
                'kd_kabupaten' => $kabupatenId,
                'kabupaten' => $namaKabupaten,
            ]);

            // Disable triggers untuk operasi update
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten DISABLE TRIGGER ALL");

                // Use unprepared update untuk bypass trigger
                $sql = "UPDATE kabupaten SET kabupaten = N'{$namaKabupaten}' WHERE kd_propinsi = {$provinsiId} AND kd_kabupaten = {$kabupatenId}";
                DB::connection('sqlsrv')->unprepared($sql);

                // Re-enable triggers
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten ENABLE TRIGGER ALL");

                // Get updated data
                $kabupatenData = DB::connection('sqlsrv')->select("SELECT * FROM kabupaten WHERE kd_propinsi = {$provinsiId} AND kd_kabupaten = {$kabupatenId}");
                
                if (empty($kabupatenData)) {
                    throw new \Exception('Kabupaten tidak ditemukan setelah update');
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Kabupaten berhasil diperbarui.',
                    'data' => $kabupatenData[0],
                ]);

            } catch (\Exception $updateEx) {
                // Re-enable triggers jika ada error
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kabupaten triggers after update error: ' . $triggerEx->getMessage());
                }
                throw $updateEx;
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating kabupaten: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui kabupaten: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroyKabupaten($provinsiId, $kabupatenId)
    {
        try {
            DB::beginTransaction();

            Log::info('Starting kabupaten delete operation', [
                'provinsi_id' => $provinsiId,
                'kabupaten_id' => $kabupatenId
            ]);

            // Step 1: Get all kecamatan IDs for this kabupaten
            $kecamatanIds = DB::connection('sqlsrv')->table('kecamatan')
                ->where('kd_kabupaten', $kabupatenId)
                ->pluck('kd_kecamatan')
                ->toArray();

            if (!empty($kecamatanIds)) {
                // Step 2: Delete kelurahan using raw SQL with disable trigger approach
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan DISABLE TRIGGER ALL");
                    
                    // Delete kelurahan in batches
                    $chunks = array_chunk($kecamatanIds, 50);
                    foreach ($chunks as $chunk) {
                        $kecamatanIdsStr = implode(',', $chunk);
                        DB::connection('sqlsrv')->unprepared("DELETE FROM kelurahan WHERE kd_kecamatan IN ({$kecamatanIdsStr})");
                    }

                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
                } catch (\Exception $kelurahanEx) {
                    // Re-enable triggers even if delete fails
                    try {
                        DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
                    } catch (\Exception $triggerEx) {
                        Log::error('Failed to re-enable kelurahan triggers: ' . $triggerEx->getMessage());
                    }
                    throw $kelurahanEx;
                }

                // Step 3: Delete kecamatan using raw SQL with disable trigger approach
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan DISABLE TRIGGER ALL");
                    
                    $kecamatanIdsStr = implode(',', $kecamatanIds);
                    DB::connection('sqlsrv')->unprepared("DELETE FROM kecamatan WHERE kd_kecamatan IN ({$kecamatanIdsStr})");

                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");
                } catch (\Exception $kecamatanEx) {
                    // Re-enable triggers even if delete fails
                    try {
                        DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");
                    } catch (\Exception $triggerEx) {
                        Log::error('Failed to re-enable kecamatan triggers: ' . $triggerEx->getMessage());
                    }
                    throw $kecamatanEx;
                }
            }

            // Step 4: Delete kabupaten using raw SQL with disable trigger approach
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten DISABLE TRIGGER ALL");
                
                DB::connection('sqlsrv')->unprepared("DELETE FROM kabupaten WHERE kd_propinsi = {$provinsiId} AND kd_kabupaten = {$kabupatenId}");

                DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten ENABLE TRIGGER ALL");
            } catch (\Exception $kabupatenEx) {
                // Re-enable triggers even if delete fails
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kabupaten ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kabupaten triggers: ' . $triggerEx->getMessage());
                }
                throw $kabupatenEx;
            }

            DB::commit();

            Log::info('Kabupaten delete operation completed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Kabupaten berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting kabupaten: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kabupaten: ' . $e->getMessage(),
            ], 500);
        }
    }

    // CRUD untuk Kecamatan (Level 3)
    public function storeKecamatan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_kabupaten' => 'required|exists:kabupaten,kd_kabupaten',
            'kecamatan' => [
                'required',
                'string',
                'max:30',
                Rule::unique('kecamatan')->where(function ($query) use ($request) {
                    return $query->where('kd_kabupaten', $request->kd_kabupaten);
                }),
            ],
        ], [
            'kd_kabupaten.required' => 'Kabupaten harus dipilih.',
            'kd_kabupaten.exists' => 'Kabupaten tidak ditemukan.',
            'kecamatan.required' => 'Nama kecamatan harus diisi.',
            'kecamatan.max' => 'Nama kecamatan maksimal 30 karakter.',
            'kecamatan.unique' => 'Nama kecamatan sudah ada di kabupaten ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get next ID menggunakan raw SQL untuk avoid trigger issues
            $result = DB::connection('sqlsrv')->select("SELECT ISNULL(MAX(kd_kecamatan), 0) as max_id FROM kecamatan");
            $lastKd = $result[0]->max_id ?? 0;
            $newKd = $lastKd + 1;

            // Prepare data dengan escape untuk SQL injection
            $kdKecamatan = (int) $newKd;
            $kdKabupaten = (int) $request->kd_kabupaten;
            $namaKecamatan = str_replace("'", "''", trim($request->kecamatan));

            Log::info('Attempting to insert kecamatan', [
                'kd_kecamatan' => $kdKecamatan,
                'kd_kabupaten' => $kdKabupaten,
                'kecamatan' => $namaKecamatan,
            ]);

            // Disable triggers untuk insert
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan DISABLE TRIGGER ALL");

                // Insert menggunakan raw SQL untuk bypass trigger
                $sql = "INSERT INTO kecamatan (kd_kecamatan, kd_kabupaten, kecamatan) VALUES ({$kdKecamatan}, {$kdKabupaten}, N'{$namaKecamatan}')";
                DB::connection('sqlsrv')->unprepared($sql);

                // Re-enable triggers
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");

                // Verify insert dan ambil data
                $kecamatanData = DB::connection('sqlsrv')->select("SELECT * FROM kecamatan WHERE kd_kecamatan = {$kdKecamatan}");
                
                if (empty($kecamatanData)) {
                    throw new \Exception('Data kecamatan tidak ditemukan setelah insert');
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Kecamatan berhasil disimpan.',
                    'data' => $kecamatanData[0],
                ]);

            } catch (\Exception $insertEx) {
                // Re-enable triggers jika ada error
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kecamatan triggers: ' . $triggerEx->getMessage());
                }
                throw $insertEx;
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing kecamatan: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan kecamatan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editKecamatan($kabupatenId, $kecamatanId)
    {
        $kecamatan = Kecamatan::where('kd_kabupaten', $kabupatenId)
            ->where('kd_kecamatan', $kecamatanId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $kecamatan,
            'code' => 200,
        ], 200);
    }

    public function updateKecamatan(Request $request, $kabupatenId, $kecamatanId)
    {
        $validator = Validator::make($request->all(), [
            'kecamatan' => [
                'required',
                'string',
                'max:30',
                Rule::unique('kecamatan')->ignore($kecamatanId, 'kd_kecamatan')->where(function ($query) use ($kabupatenId) {
                    return $query->where('kd_kabupaten', $kabupatenId);
                }),
            ],
        ], [
            'kecamatan.required' => 'Nama kecamatan harus diisi.',
            'kecamatan.max' => 'Nama kecamatan maksimal 30 karakter.',
            'kecamatan.unique' => 'Nama kecamatan sudah ada di kabupaten ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Validate kecamatan exists
            $existingKecamatan = DB::connection('sqlsrv')->select("SELECT * FROM kecamatan WHERE kd_kabupaten = ? AND kd_kecamatan = ?", [$kabupatenId, $kecamatanId]);
            
            if (empty($existingKecamatan)) {
                throw new \Exception('Kecamatan tidak ditemukan');
            }

            // Prepare data dengan escape untuk SQL injection
            $namaKecamatan = str_replace("'", "''", trim($request->kecamatan));
            $kdKabupaten = (int) $kabupatenId;
            $kdKecamatan = (int) $kecamatanId;

            Log::info('Attempting to update kecamatan', [
                'kd_kabupaten' => $kdKabupaten,
                'kd_kecamatan' => $kdKecamatan,
                'kecamatan' => $namaKecamatan,
            ]);

            // Disable triggers untuk update
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan DISABLE TRIGGER ALL");

                // Update menggunakan raw SQL untuk bypass trigger
                $sql = "UPDATE kecamatan SET kecamatan = N'{$namaKecamatan}' WHERE kd_kabupaten = {$kdKabupaten} AND kd_kecamatan = {$kdKecamatan}";
                DB::connection('sqlsrv')->unprepared($sql);

                // Re-enable triggers
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");

                // Verify update dan ambil data terbaru
                $updatedKecamatan = DB::connection('sqlsrv')->select("SELECT * FROM kecamatan WHERE kd_kabupaten = {$kdKabupaten} AND kd_kecamatan = {$kdKecamatan}");
                
                if (empty($updatedKecamatan)) {
                    throw new \Exception('Data kecamatan tidak ditemukan setelah update');
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Kecamatan berhasil diperbarui.',
                    'data' => $updatedKecamatan[0],
                ]);

            } catch (\Exception $updateEx) {
                // Re-enable triggers jika ada error
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kecamatan triggers: ' . $triggerEx->getMessage());
                }
                throw $updateEx;
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating kecamatan: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate kecamatan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroyKecamatan($kabupatenId, $kecamatanId)
    {
        try {
            DB::beginTransaction();

            Log::info('Starting kecamatan delete operation', [
                'kabupaten_id' => $kabupatenId,
                'kecamatan_id' => $kecamatanId
            ]);

            // Step 1: Delete kelurahan using raw SQL with disable trigger approach
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan DISABLE TRIGGER ALL");
                
                // Delete kelurahan for this kecamatan
                DB::connection('sqlsrv')->unprepared("DELETE FROM kelurahan WHERE kd_kecamatan = {$kecamatanId}");

                DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
            } catch (\Exception $kelurahanEx) {
                // Re-enable triggers even if delete fails
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kelurahan triggers: ' . $triggerEx->getMessage());
                }
                throw $kelurahanEx;
            }

            // Step 2: Delete kecamatan using raw SQL with disable trigger approach
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan DISABLE TRIGGER ALL");
                
                DB::connection('sqlsrv')->unprepared("DELETE FROM kecamatan WHERE kd_kabupaten = {$kabupatenId} AND kd_kecamatan = {$kecamatanId}");

                DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");
            } catch (\Exception $kecamatanEx) {
                // Re-enable triggers even if delete fails
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kecamatan ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kecamatan triggers: ' . $triggerEx->getMessage());
                }
                throw $kecamatanEx;
            }

            DB::commit();

            Log::info('Kecamatan delete operation completed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Kecamatan berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting kecamatan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kecamatan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // CRUD untuk Kelurahan (Level 4)
    public function storeKelurahan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_kecamatan' => 'required|exists:kecamatan,kd_kecamatan',
            'kelurahan' => [
                'required',
                'string',
                'max:80',
                Rule::unique('kelurahan')->where(function ($query) use ($request) {
                    return $query->where('kd_kecamatan', $request->kd_kecamatan);
                }),
            ],
            'kode_pos' => 'nullable|string|max:5',
            'aktif' => 'required|in:0,1',
        ], [
            'kd_kecamatan.required' => 'Kecamatan harus dipilih.',
            'kd_kecamatan.exists' => 'Kecamatan tidak ditemukan.',
            'kelurahan.required' => 'Nama kelurahan harus diisi.',
            'kelurahan.max' => 'Nama kelurahan maksimal 80 karakter.',
            'kelurahan.unique' => 'Nama kelurahan sudah ada di kecamatan ini.',
            'kode_pos.max' => 'Kode pos maksimal 5 karakter.',
            'aktif.required' => 'Status aktif harus dipilih.',
            'aktif.in' => 'Status aktif harus berupa 0 atau 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get next ID menggunakan raw SQL untuk avoid trigger issues
            $result = DB::connection('sqlsrv')->select("SELECT ISNULL(MAX(kd_kelurahan), 0) as max_id FROM kelurahan");
            $lastKd = $result[0]->max_id ?? 0;
            $newKd = $lastKd + 1;

            // Prepare data dengan escape untuk SQL injection
            $kdKelurahan = (int) $newKd;
            $kdKecamatan = (int) $request->kd_kecamatan;
            $namaKelurahan = str_replace("'", "''", trim($request->kelurahan));
            $kodePos = $request->kode_pos ? str_replace("'", "''", trim($request->kode_pos)) : '';
            $aktif = (int) ($request->aktif ?? 1);

            Log::info('Attempting to insert kelurahan', [
                'kd_kelurahan' => $kdKelurahan,
                'kd_kecamatan' => $kdKecamatan,
                'kelurahan' => $namaKelurahan,
                'kode_pos' => $kodePos,
                'aktif' => $aktif,
            ]);

            // Disable triggers untuk insert
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan DISABLE TRIGGER ALL");

                // Insert menggunakan raw SQL untuk bypass trigger
                $sql = "INSERT INTO kelurahan (kd_kelurahan, kd_kecamatan, kelurahan, kode_pos, aktif) VALUES ({$kdKelurahan}, {$kdKecamatan}, N'{$namaKelurahan}', N'{$kodePos}', {$aktif})";
                DB::connection('sqlsrv')->unprepared($sql);

                // Re-enable triggers
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");

                // Verify insert dan ambil data
                $kelurahanData = DB::connection('sqlsrv')->select("SELECT * FROM kelurahan WHERE kd_kelurahan = {$kdKelurahan}");
                
                if (empty($kelurahanData)) {
                    throw new \Exception('Data kelurahan tidak ditemukan setelah insert');
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Kelurahan berhasil disimpan.',
                    'data' => $kelurahanData[0],
                ]);

            } catch (\Exception $insertEx) {
                // Re-enable triggers jika ada error
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kelurahan triggers: ' . $triggerEx->getMessage());
                }
                throw $insertEx;
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing kelurahan: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan kelurahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editKelurahan($kecamatanId, $kelurahanId)
    {
        try {
            // Use raw SQL dengan koneksi sqlsrv untuk bypass trigger issues pada SELECT
            $kelurahanData = DB::connection('sqlsrv')->select("SELECT * FROM kelurahan WHERE kd_kecamatan = ? AND kd_kelurahan = ?", [$kecamatanId, $kelurahanId]);
            
            if (empty($kelurahanData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelurahan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $kelurahanData[0],
                'code' => 200,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching kelurahan for edit: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kelurahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateKelurahan(Request $request, $kecamatanId, $kelurahanId)
    {
        $validator = Validator::make($request->all(), [
            'kelurahan' => [
                'required',
                'string',
                'max:80',
                Rule::unique('kelurahan')->ignore($kelurahanId, 'kd_kelurahan')->where(function ($query) use ($kecamatanId) {
                    return $query->where('kd_kecamatan', $kecamatanId);
                }),
            ],
            'kode_pos' => 'nullable|string|max:5',
            'aktif' => 'required|in:0,1',
        ], [
            'kelurahan.required' => 'Nama kelurahan harus diisi.',
            'kelurahan.max' => 'Nama kelurahan maksimal 80 karakter.',
            'kelurahan.unique' => 'Nama kelurahan sudah ada di kecamatan ini.',
            'kode_pos.max' => 'Kode pos maksimal 5 karakter.',
            'aktif.required' => 'Status aktif harus dipilih.',
            'aktif.in' => 'Status aktif harus berupa 0 atau 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Validate kelurahan exists
            $existingKelurahan = DB::connection('sqlsrv')->select("SELECT * FROM kelurahan WHERE kd_kecamatan = ? AND kd_kelurahan = ?", [$kecamatanId, $kelurahanId]);
            
            if (empty($existingKelurahan)) {
                throw new \Exception('Kelurahan tidak ditemukan');
            }

            // Prepare data dengan escape untuk SQL injection
            $namaKelurahan = str_replace("'", "''", trim($request->kelurahan));
            $kodePos = $request->kode_pos ? str_replace("'", "''", trim($request->kode_pos)) : '';
            $aktif = (int) $request->aktif;
            $kdKecamatan = (int) $kecamatanId;
            $kdKelurahan = (int) $kelurahanId;

            Log::info('Attempting to update kelurahan', [
                'kd_kecamatan' => $kdKecamatan,
                'kd_kelurahan' => $kdKelurahan,
                'kelurahan' => $namaKelurahan,
                'kode_pos' => $kodePos,
                'aktif' => $aktif,
            ]);

            // Disable triggers untuk update
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan DISABLE TRIGGER ALL");

                // Update menggunakan raw SQL untuk bypass trigger
                $sql = "UPDATE kelurahan SET kelurahan = N'{$namaKelurahan}', kode_pos = N'{$kodePos}', aktif = {$aktif} WHERE kd_kecamatan = {$kdKecamatan} AND kd_kelurahan = {$kdKelurahan}";
                DB::connection('sqlsrv')->unprepared($sql);

                // Re-enable triggers
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");

                // Verify update dan ambil data terbaru
                $updatedKelurahan = DB::connection('sqlsrv')->select("SELECT * FROM kelurahan WHERE kd_kecamatan = {$kdKecamatan} AND kd_kelurahan = {$kdKelurahan}");
                
                if (empty($updatedKelurahan)) {
                    throw new \Exception('Data kelurahan tidak ditemukan setelah update');
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Kelurahan berhasil diperbarui.',
                    'data' => $updatedKelurahan[0],
                ]);

            } catch (\Exception $updateEx) {
                // Re-enable triggers jika ada error
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kelurahan triggers: ' . $triggerEx->getMessage());
                }
                throw $updateEx;
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating kelurahan: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate kelurahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroyKelurahan($kecamatanId, $kelurahanId)
    {
        try {
            DB::beginTransaction();

            // Convert to int untuk safety
            $kdKecamatan = (int) $kecamatanId;
            $kdKelurahan = (int) $kelurahanId;

            Log::info('Starting kelurahan delete operation', [
                'kd_kecamatan' => $kdKecamatan,
                'kd_kelurahan' => $kdKelurahan,
            ]);

            // Disable triggers untuk delete
            try {
                DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan DISABLE TRIGGER ALL");
                
                // Delete kelurahan using raw SQL
                DB::connection('sqlsrv')->unprepared("DELETE FROM kelurahan WHERE kd_kecamatan = {$kdKecamatan} AND kd_kelurahan = {$kdKelurahan}");

                DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
            } catch (\Exception $deleteEx) {
                // Re-enable triggers even if delete fails
                try {
                    DB::connection('sqlsrv')->unprepared("ALTER TABLE kelurahan ENABLE TRIGGER ALL");
                } catch (\Exception $triggerEx) {
                    Log::error('Failed to re-enable kelurahan triggers: ' . $triggerEx->getMessage());
                }
                throw $deleteEx;
            }

            DB::commit();

            Log::info('Kelurahan delete operation completed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Kelurahan berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting kelurahan: ' . $e->getMessage());
            Log::error('Parameters: ', ['kd_kecamatan' => $kecamatanId, 'kd_kelurahan' => $kelurahanId]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kelurahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
