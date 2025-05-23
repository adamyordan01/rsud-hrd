<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Models\JenjangPendidikan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\DataTables\JenjangPendidikanDataTable;

class JenjangPendidikanController extends Controller
{
    public function index()
    {
        $pageTitle = 'Jenjang Pendidikan';

        if (request()->ajax()) {
            $query = JenjangPendidikan::query();

            return DataTables::of($query)
                ->addColumn('name', function ($jenjang) {
                    return $jenjang->jenjang_didik;
                })
                ->addColumn('nilaiIndex', function ($jenjang) {
                    return $jenjang->nilaiindex;
                })
                ->addColumn('action', function ($row) {
                    return view('settings.jenjang_pendidikan.datatables-column._actions', compact('row'));
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('jenjang_didik', $order);
                })
                ->orderColumn('nilaiIndex', function ($query, $order) {
                    $query->orderBy('nilaiindex', $order);
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('jenjang_didik', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.jenjang_pendidikan.index', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenjang_didik' => 'required|string|max:255',
            'nilaiIndex' => 'required|numeric|min:0|unique:hrd_jenjang_pendidikan,nilaiindex',
        ], [
            'jenjang_didik.required' => 'Nama jenjang pendidikan wajib diisi.',
            'jenjang_didik.max' => 'Nama jenjang pendidikan maksimal 255 karakter.',
            'nilaiIndex.required' => 'Nilai indeks wajib diisi.',
            'nilaiIndex.numeric' => 'Nilai indeks harus berupa angka.',
            'nilaiIndex.min' => 'Nilai indeks tidak boleh kurang dari 0.',
            'nilaiIndex.unique' => 'Nilai indeks sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam input data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Ambil kd_jenjang_didik terakhir dan tambahkan 1
        $lastKd = JenjangPendidikan::max('kd_jenjang_didik') ?? 0;
        $newKd = $lastKd + 1;

        JenjangPendidikan::create([
            'kd_jenjang_didik' => $newKd,
            'jenjang_didik' => $request->jenjang_didik,
            'nilaiindex' => $request->nilaiindex,
        ]);

        // Perbarui nilaiIndex untuk semua data agar berurutan
        $this->reorderNilaiIndex();

        return response()->json([
            'success' => true,
            'message' => 'Jenjang pendidikan berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function edit($id)
    {
        $jenjang = JenjangPendidikan::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'kd_jenjang_didik' => $jenjang->kd_jenjang_didik,
                'jenjang_didik' => $jenjang->jenjang_didik,
                'nilaiIndex' => $jenjang->nilaiindex,
            ],
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id)
    {
        $jenjang = JenjangPendidikan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'jenjang_didik' => 'required|string|max:255',
            'nilaiIndex' => 'required|numeric|min:0|unique:hrd_jenjang_pendidikan,nilaiindex,' . $id . ',kd_jenjang_didik',
        ], [
            'jenjang_didik.required' => 'Nama jenjang pendidikan wajib diisi.',
            'jenjang_didik.max' => 'Nama jenjang pendidikan maksimal 255 karakter.',
            'nilaiIndex.required' => 'Nilai indeks wajib diisi.',
            'nilaiIndex.numeric' => 'Nilai indeks harus berupa angka.',
            'nilaiIndex.min' => 'Nilai indeks tidak boleh kurang dari 0.',
            'nilaiIndex.unique' => 'Nilai indeks sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam input data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $jenjang->update([
            'jenjang_didik' => $request->jenjang_didik,
            'nilaiindex' => $request->nilaiindex,
        ]);

        // Perbarui nilaiIndex untuk semua data agar berurutan
        $this->reorderNilaiIndex();

        return response()->json([
            'success' => true,
            'message' => 'Jenjang pendidikan berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id)
    {
        $jenjang = JenjangPendidikan::findOrFail($id);
        $jenjang->delete();

        // Perbarui nilaiIndex untuk semua data agar berurutan
        $this->reorderNilaiIndex();

        return response()->json([
            'success' => true,
            'message' => 'Jenjang pendidikan berhasil dihapus.',
            'code' => 200,
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:hrd_jenjang_pendidikan,kd_jenjang_didik',
        ], [
            'order.required' => 'Urutan data wajib diisi.',
            'order.*.exists' => 'Kode jenjang pendidikan tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan dalam input urutan.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Perbarui nilaiIndex berdasarkan urutan
        foreach ($request->order as $index => $kd_jenjang_didik) {
            JenjangPendidikan::where('kd_jenjang_didik', $kd_jenjang_didik)
                ->update(['nilaiindex' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan jenjang pendidikan berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    protected function reorderNilaiIndex()
    {
        $jenjangs = JenjangPendidikan::orderBy('nilaiIndex', 'asc')->get();
        foreach ($jenjangs as $index => $jenjang) {
            $jenjang->update(['nilaiindex' => $index + 1]);
        }
    }
}