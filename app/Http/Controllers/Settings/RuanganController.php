<?php

namespace App\Http\Controllers\Settings;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RuanganController extends Controller
{
    public function index()
    {
        $pageTitle = 'Ruangan';

        if (request()->ajax()) {
            $query = Ruangan::query();

            return DataTables::of($query)
                ->addColumn('name', function ($row) {
                    return $row->ruangan;
                })
                ->addColumn('unit', function ($row) {
                    return $row->kd_unit;
                })
                ->addColumn('index_ruangan', function ($row) {
                    return $row->index_ruangan;
                })
                ->addColumn('status', function ($row) {
                    return view('settings.ruangan.datatables-column._status', compact('row'));
                })
                ->addColumn('action', function ($row) {
                    return view('settings.ruangan.datatables-column._actions', compact('row'));
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('ruangan', $order);
                })
                ->orderColumn('unit', function ($query, $order) {
                    $query->orderBy('kd_unit', $order);
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('ruangan', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.ruangan.index', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruangan' => 'required|string|max:255',
            'status_aktif' => 'required|boolean',
            'kd_unit' => 'nullable|string|max:5',
            'index_ruangan' => 'nullable|numeric|min:0|unique:hrd_ruangan,index_ruangan',
        ], [
            'ruangan.required' => 'Nama ruangan wajib diisi.',
            'ruangan.max' => 'Nama ruangan maksimal 255 karakter.',
            'status_aktif.required' => 'Status aktif wajib diisi.',
            'status_aktif.boolean' => 'Status aktif harus berupa true atau false.',
            'kd_unit.max' => 'Kode unit maksimal 5 karakter.',
            'index_ruangan.numeric' => 'Index ruangan harus berupa angka.',
            'index_ruangan.min' => 'Index ruangan tidak boleh kurang dari 0.',
            'index_ruangan.unique' => 'Index ruangan sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // ambil kd_ruangan terakhir dan tambahkan 1
        $lastKd = Ruangan::max('kd_ruangan') ?? 0;
        $newKd = $lastKd + 1;

        Ruangan::create([
            'kd_ruangan' => $newKd,
            'status_aktif' => $request->status_aktif,
            'ruangan' => $request->ruangan,
            'kd_unit' => $request->kd_unit,
            'index_ruangan' => $request->index_ruangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data ruangan berhasil disimpan.',
            'code' => 200,
        ], 200);
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ruangan,
            'code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ruangan' => 'required|string|max:255',
            'status_aktif' => 'required|boolean',
            'kd_unit' => 'nullable|string|max:5',
            'index_ruangan' => 'nullable|numeric|min:0|unique:hrd_ruangan,index_ruangan,' . $id . ',kd_ruangan',
        ], [
            'ruangan.required' => 'Nama ruangan wajib diisi.',
            'ruangan.max' => 'Nama ruangan maksimal 255 karakter.',
            'status_aktif.required' => 'Status aktif wajib diisi.',
            'status_aktif.boolean' => 'Status aktif harus berupa true atau false.',
            'kd_unit.max' => 'Kode unit maksimal 5 karakter.',
            'index_ruangan.numeric' => 'Index ruangan harus berupa angka.',
            'index_ruangan.min' => 'Index ruangan tidak boleh kurang dari 0.',
            'index_ruangan.unique' => 'Index ruangan sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update([
            'status_aktif' => $request->status_aktif,
            'ruangan' => $request->ruangan,
            'kd_unit' => $request->kd_unit,
            'index_ruangan' => $request->index_ruangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data ruangan berhasil diperbarui.',
            'code' => 200,
        ], 200);
    }
}
