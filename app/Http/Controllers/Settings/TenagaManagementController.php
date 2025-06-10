<?php

namespace App\Http\Controllers\Settings;

use App\Models\JenisTenaga;
use Illuminate\Http\Request;
use App\Models\JenisTenagaDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\JenisTenagaSubDetail;
use Illuminate\Support\Facades\Validator;

class TenagaManagementController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manajemen Jenis Tenaga Kerja';
        
        try {
            // Step 1: Load jenis tenaga dengan details
            $jenisTenaga = JenisTenaga::with([
                'details' => function($query) {
                    $query->orderBy('kd_detail');
                }
            ])
            ->orderBy('kd_jenis_tenaga')
            ->get();

            // Step 2: Load semua sub details sekaligus untuk efficiency
            $allSubDetails = JenisTenagaSubDetail::orderBy('kd_jenis_tenaga')
                                                ->orderBy('kd_detail')
                                                ->orderBy('kd_sub_detail')
                                                ->get()
                                                ->groupBy(['kd_jenis_tenaga', 'kd_detail']);

            // Step 3: Attach sub details ke setiap detail
            foreach($jenisTenaga as $jenis) {
                foreach($jenis->details as $detail) {
                    // Get sub details untuk detail ini
                    $subDetails = collect($allSubDetails[$jenis->kd_jenis_tenaga][$detail->kd_detail] ?? []);
                    
                    // Set sub details menggunakan accessor
                    $detail->subDetails = $subDetails;
                }
            }

            // Debug logging
            // Log::info('=== TENAGA MANAGEMENT DEBUG ===');
            // Log::info('Total Jenis Tenaga: ' . $jenisTenaga->count());
            
            // foreach($jenisTenaga as $jenis) {
            //     $totalSubDetails = $jenis->details->sum(function($detail) {
            //         return $detail->subDetails->count();
            //     });
                
            //     // Log::info("Jenis: {$jenis->jenis_tenaga}");
            //     // Log::info("- Details: {$jenis->details->count()}");
            //     // Log::info("- Total SubDetails: {$totalSubDetails}");
                
            //     foreach($jenis->details as $detail) {
            //         Log::info("  -- Detail: {$detail->detail_jenis_tenaga} (SubDetails: {$detail->subDetails->count()})");
            //     }
            // }

        } catch (\Exception $e) {
            Log::error('Error loading tenaga data: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $jenisTenaga = collect(); // Empty collection sebagai fallback
            session()->flash('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }

        return view('settings.tenaga_management.index', compact('pageTitle', 'jenisTenaga'));
    }

    // API untuk cascade dropdown
    public function getDetails($jenisId)
    {
        $details = JenisTenagaDetail::where('kd_jenis_tenaga', $jenisId)
            ->orderBy('kd_detail')
            ->get();

        return response()->json([
                'success' => true,
                'data' => $details,
            ]);
    }

    public function getSubDetails($jenisId, $detailId)
    {
        $subDetails = JenisTenagaSubDetail::where('kd_jenis_tenaga', $jenisId)
            ->where('kd_detail', $detailId)
            ->orderBy('kd_sub_detail')
            ->get();

        return response()->json([
                'success' => true,
                'data' => $subDetails,
            ]);
    }

    // CRUD untuk Jenis Tenaga (Level 1 )
    public function storeJenisTenaga(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_tenaga' => 'required|string|max:255|unique:hrd_jenis_tenaga,jenis_tenaga',
        ], [
            'jenis_tenaga.required' => 'Jenis tenaga kerja harus diisi.',
            'jenis_tenaga.unique' => 'Jenis tenaga kerja sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lastKd = JenisTenaga::max('kd_jenis_tenaga') ?? 0;
        $newKd = $lastKd + 1;

        // $jenisTenaga = JenisTenaga::create([
        //     'kd_jenis_tenaga' => $newKd,
        //     'jenis_tenaga' => $request->jenis_tenaga,
        // ]);

        // create jenis tenaga using DB
        $jenisTenaga = DB::table('hrd_jenis_tenaga')->insertGetId([
            'kd_jenis_tenaga' => $newKd,
            'jenis_tenaga' => $request->jenis_tenaga,
        ]);

        // Get the newly created jenis tenaga
        $jenisTenaga = JenisTenaga::find($jenisTenaga);
        if (!$jenisTenaga) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tenaga kerja gagal disimpan.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jenis tenaga kerja berhasil disimpan.',
            'data' => $jenisTenaga,
        ]);
    }

    public function updateJenisTenaga(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_tenaga' => 'required|string|max:255|unique:hrd_jenis_tenaga,jenis_tenaga,' . $id . ',kd_jenis_tenaga',
        ], [
            'jenis_tenaga.required' => 'Jenis tenaga kerja harus diisi.',
            'jenis_tenaga.unique' => 'Jenis tenaga kerja sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $jenisTenaga = JenisTenaga::findOrFail($id);

        // $jenisTenaga->update([
        //     'jenis_tenaga' => $request->jenis_tenaga,
        // ]);

        // Update jenis tenaga using DB
        $updated = DB::table('hrd_jenis_tenaga')
            ->where('kd_jenis_tenaga', $id)
            ->update(['jenis_tenaga' => $request->jenis_tenaga]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tenaga kerja tidak ditemukan atau gagal diperbarui.',
            ], 404);
        }

        // Get the updated jenis tenaga
        $jenisTenaga = JenisTenaga::find($id);
        if (!$jenisTenaga) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tenaga kerja tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jenis tenaga kerja berhasil diperbarui.',
            'data' => $jenisTenaga,
        ]);
    }

    public function destroyJenisTenaga($id)
    {
        try {
            DB::beginTransaction();

            // Delete sub details first
            JenisTenagaSubDetail::where('kd_jenis_tenaga', $id)->delete();

            // Delete details
            JenisTenagaDetail::where('kd_jenis_tenaga', $id)->delete();

            // Finally delete jenis tenaga
            JenisTenaga::where('kd_jenis_tenaga', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jenis tenaga kerja berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus jenis tenaga kerja: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // CRUD untuk Jenis Tenaga Detail (Level 2)
    public function storeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_jenis_tenaga' => 'required|exists:hrd_jenis_tenaga,kd_jenis_tenaga',
            'detail_jenis_tenaga' => 'required|string|max:255|unique:hrd_jenis_tenaga_detail,detail_jenis_tenaga',
        ], [
            'kd_jenis_tenaga.required' => 'Jenis tenaga kerja harus dipilih.',
            'kd_jenis_tenaga.exists' => 'Jenis tenaga kerja tidak ditemukan.',
            'detail_jenis_tenaga.required' => 'Detail jenis tenaga kerja harus diisi.',
            'detail_jenis_tenaga.unique' => 'Detail jenis tenaga kerja sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lastKd = JenisTenagaDetail::where('kd_jenis_tenaga', $request->kd_jenis_tenaga)
            ->max('kd_detail') ?? 0;
        $newKd = $lastKd + 1;

        $detail = JenisTenagaDetail::create([
            'kd_detail' => $newKd,
            'kd_jenis_tenaga' => $request->kd_jenis_tenaga,
            'detail_jenis_tenaga' => $request->detail_jenis_tenaga,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail jenis tenaga kerja berhasil disimpan.',
            'data' => $detail,
        ]);
    }

    public function updateDetail(Request $request, $jenisId, $detailId)
    {
        $validator = Validator::make($request->all(), [
            'detail_jenis_tenaga' => 'required|string|max:255|unique:hrd_jenis_tenaga_detail,detail_jenis_tenaga,' . $detailId . ',kd_detail,kd_jenis_tenaga,' . $jenisId,
        ], [
            'detail_jenis_tenaga.required' => 'Detail jenis tenaga kerja harus diisi.',
            'detail_jenis_tenaga.unique' => 'Detail jenis tenaga kerja sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // $detail = JenisTenagaDetail::where('kd_jenis_tenaga', $jenisId)
        //     ->where('kd_detail', $detailId)
        //     ->firstOrFail();

        // $detail->update([
        //     'detail_jenis_tenaga' => $request->detail_jenis_tenaga,
        // ]);

        // get data menggunakan DB
        $detail = DB::table('hrd_jenis_tenaga_detail')
            ->where('kd_jenis_tenaga', $jenisId)
            ->where('kd_detail', $detailId)
            ->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Detail jenis tenaga kerja tidak ditemukan.',
            ], 404);
        }

        // Update detail using DB
        DB::table('hrd_jenis_tenaga_detail')
            ->where('kd_jenis_tenaga', $jenisId)
            ->where('kd_detail', $detailId)
            ->update([
                'detail_jenis_tenaga' => $request->detail_jenis_tenaga,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail jenis tenaga kerja berhasil diperbarui.',
            'data' => $detail,
        ]);
    }

    public function destroyDetail($jenisId, $detailId)
    {
        try {
            DB::beginTransaction();

            // Delete sub details first
            // JenisTenagaSubDetail::where('kd_jenis_tenaga', $jenisId)
            //     ->where('kd_detail', $detailId)
            //     ->delete();

            // Delete sub details using DB
            DB::table('hrd_jenis_tenaga_sub_detail')
                ->where('kd_jenis_tenaga', $jenisId)
                ->where('kd_detail', $detailId)
                ->delete();

            // Delete detail
            // JenisTenagaDetail::where('kd_jenis_tenaga', $jenisId)
            //     ->where('kd_detail', $detailId)
            //     ->delete();

            // Delete detail using DB
            DB::table('hrd_jenis_tenaga_detail')
                ->where('kd_jenis_tenaga', $jenisId)
                ->where('kd_detail', $detailId)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detail jenis tenaga kerja berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus detail jenis tenaga kerja: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    // CRUD untuk Jenis Tenaga Sub Detail (Level 3)
    public function storeSubDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kd_jenis_tenaga' => 'required|exists:hrd_jenis_tenaga,kd_jenis_tenaga',
            'kd_detail' => 'required|exists:hrd_jenis_tenaga_detail,kd_detail,kd_jenis_tenaga,' . $request->kd_jenis_tenaga,
            'sub_detail' => 'required|string|max:255',
            'kd_sdmk' => 'nullable|string|max:50',
            'kelompok_spesialis' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:10',
        ], [
            'kd_jenis_tenaga.required' => 'Jenis tenaga kerja harus dipilih.',
            'kd_jenis_tenaga.exists' => 'Jenis tenaga kerja tidak ditemukan.',
            'kd_detail.required' => 'Detail jenis tenaga kerja harus dipilih.',
            'kd_detail.exists' => 'Detail jenis tenaga kerja tidak ditemukan.',
            'sub_detail.required' => 'Sub detail jenis tenaga kerja harus diisi.',
            'sub_detail.max' => 'Sub detail jenis tenaga kerja tidak boleh lebih dari 255 karakter.',
            'kd_sdmk.max' => 'Kode SDMK tidak boleh lebih dari 50 karakter.',
            'kelompok_spesialis.max' => 'Kelompok spesialis tidak boleh lebih dari 50 karakter.',
            'status.max' => 'Status tidak boleh lebih dari 10 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lastKd = JenisTenagaSubDetail::where('kd_jenis_tenaga', $request->kd_jenis_tenaga)
            ->where('kd_detail', $request->kd_detail)
            ->max('kd_sub_detail') ?? 0;

        $newKd = $lastKd + 1;

        $subDetail = JenisTenagaSubDetail::create([
            'kd_jenis_tenaga' => $request->kd_jenis_tenaga,
            'kd_detail' => $request->kd_detail,
            'kd_sub_detail' => $newKd,
            'sub_detail' => $request->sub_detail,
            'kd_sdmk' => $request->kd_sdmk,
            'kelompok_spesialis' => $request->kelompok_spesialis,
            'status' => $request->status ?? '1',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub detail jenis tenaga kerja berhasil disimpan.',
            'data' => $subDetail,
        ]);
    }

    public function updateSubDetail(Request $request, $jenisId, $detailId, $subDetailId)
    {
        // dd("Jenis ID: $jenisId, Detail ID: $detailId, Sub Detail ID: $subDetailId");
        $validator = Validator::make($request->all(), [
            'sub_detail' => 'required|string|max:255',
            'kd_sdmk' => 'nullable|string|max:50',
            'kelompok_spesialis' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:10',
        ], [
            'sub_detail.required' => 'Sub detail jenis tenaga kerja harus diisi.',
            'sub_detail.max' => 'Sub detail jenis tenaga kerja tidak boleh lebih dari 255 karakter.',
            'kd_sdmk.max' => 'Kode SDMK tidak boleh lebih dari 50 karakter.',
            'kelompok_spesialis.max' => 'Kelompok spesialis tidak boleh lebih dari 50 karakter.',
            'status.max' => 'Status tidak boleh lebih dari 10 karakter.',
        ]);
        // dd($request->all());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // $subDetail = JenisTenagaSubDetail::where('kd_jenis_tenaga', $jenisId)
        //     ->where('kd_detail', $detailId)
        //     ->where('kd_sub_detail', $subDetailId)
        //     ->firstOrFail();

        // $subDetail->update([
        //     'sub_detail' => $request->sub_detail,
        //     'kd_sdmk' => $request->kd_sdmk,
        //     'kelompok_spesialis' => $request->kelompok_spesialis,
        //     'status' => $request->status ?? '1',
        // ]);

        // get data menggunakan DB
        $subDetail = DB::table('hrd_jenis_tenaga_sub_detail')
            ->where('kd_jenis_tenaga', $jenisId)
            ->where('kd_detail', $detailId)
            ->where('kd_sub_detail', $subDetailId)
            ->first();

        if (!$subDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Sub detail jenis tenaga kerja tidak ditemukan.',
            ], 404);
        }

        // Update sub detail using DB
        DB::table('hrd_jenis_tenaga_sub_detail')
            ->where('kd_jenis_tenaga', $jenisId)
            ->where('kd_detail', $detailId)
            ->where('kd_sub_detail', $subDetailId)
            ->update([
                'sub_detail' => $request->sub_detail,
                'kd_sdmk' => $request->kd_sdmk,
                'kelompok_spesialis' => $request->kelompok_spesialis,
                'status' => $request->status ?? '1',
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub detail jenis tenaga kerja berhasil diperbarui.',
            // 'data' => $subDetail,
        ]);
    }

    public function destroySubDetail($jenisId, $detailId, $subDetailId)
    {
        try {
            // $subDetail = JenisTenagaSubDetail::where('kd_jenis_tenaga', $jenisId)
            //     ->where('kd_detail', $detailId)
            //     ->where('kd_sub_detail', $subDetailId)
            //     ->firstOrFail();

            // $subDetail->delete();

            // Delete sub detail using DB
            $deleted = DB::table('hrd_jenis_tenaga_sub_detail')
                ->where('kd_jenis_tenaga', $jenisId)
                ->where('kd_detail', $detailId)
                ->where('kd_sub_detail', $subDetailId)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub detail jenis tenaga kerja tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sub detail jenis tenaga kerja berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus sub detail jenis tenaga kerja: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
