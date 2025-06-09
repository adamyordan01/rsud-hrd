<?php

namespace App\Http\Controllers\Settings;

use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PekerjaanController extends Controller
{
    public function index()
    {
        $pageTitle = 'Pekerjaan';

        if (request()->ajax()) {
            $query = Pekerjaan::query();

            return DataTables::of($query)
                ->addColumn('name', function ($row) {
                    return $row->pekerjaan;
                })
                ->addColumn('action', function ($row) {
                    return view('settings.pekerjaan.datatables-column._actions', compact('row'));
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('pekerjaan', $order);
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('pekerjaan', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.pekerjaan.index', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pekerjaan' => 'required|string|max:255|unique:hrd_pekerjaan,pekerjaan',
        ], [
            'pekerjaan.required' => 'Nama pekerjaan wajib diisi.',
            'pekerjaan.max' => 'Nama pekerjaan maksimal 255 karakter.',
            'pekerjaan.unique' => 'Nama pekerjaan sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $lastKdPekerjaan = Pekerjaan::max('kd_pekerjaan') ?? 0;
        $newKdPekerjaan = $lastKdPekerjaan + 1;

        Pekerjaan::create([
            'kd_pekerjaan' => $newKdPekerjaan,
            'pekerjaan' => $request->pekerjaan,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Pekerjaan berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function edit($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $pekerjaan,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pekerjaan' => 'required|string|max:255|unique:hrd_pekerjaan,pekerjaan,' . $pekerjaan->kd_pekerjaan . ',kd_pekerjaan',
        ], [
            'pekerjaan.required' => 'Nama pekerjaan wajib diisi.',
            'pekerjaan.max' => 'Nama pekerjaan maksimal 255 karakter.',
            'pekerjaan.unique' => 'Nama pekerjaan sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'errors' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $pekerjaan->update([
            'pekerjaan' => $request->pekerjaan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan berhasil diperbarui.',
            'code' => 200,
        ]);
    }

    public function destroy($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $pekerjaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan berhasil dihapus.',
            'code' => 200,
        ]);
    }
}
