<?php

namespace App\Http\Controllers\Settings;

use App\Models\Bahasa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BahasaController extends Controller
{
    public function index()
    {
        $pageTitle = 'Bahasa';

        if (request()->ajax()) {
            $query = Bahasa::query();

            return DataTables::of($query)
                ->addColumn('name', function ($row) {
                    return $row->bahasa;
                })
                ->addColumn('action', function ($row) {
                    return view('settings.bahasa.datatables-column._actions', compact('row'));
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('bahasa', $order);
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('bahasa', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.bahasa.index', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bahasa' => 'required|string|max:255|unique:hrd_bahasa,bahasa',
        ], [
            'bahasa.required' => 'Nama bahasa wajib diisi.',
            'bahasa.string' => 'Nama bahasa harus berupa teks.',
            'bahasa.max' => 'Nama bahasa tidak boleh lebih dari 255 karakter.',
            'bahasa.unique' => 'Nama bahasa sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $lastKd = Bahasa::max('kd_bahasa') ?? 0;
        $newKd = $lastKd + 1;

        Bahasa::create([
            'kd_bahasa' => $newKd,
            'bahasa' => strtoupper($request->bahasa),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data bahasa berhasil disimpan.',
            'code' => 200,
        ], 200);
    }

    public function edit($id)
    {
        $bahasa = Bahasa::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $bahasa,
            'code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $bahasa = Bahasa::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bahasa' => 'required|string|max:255|unique:hrd_bahasa,bahasa,' . $bahasa->kd_bahasa . ',kd_bahasa',
        ], [
            'bahasa.required' => 'Nama bahasa wajib diisi.',
            'bahasa.string' => 'Nama bahasa harus berupa teks.',
            'bahasa.max' => 'Nama bahasa tidak boleh lebih dari 255 karakter.',
            'bahasa.unique' => 'Nama bahasa sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $bahasa->update([
            'bahasa' => strtoupper($request->bahasa),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data bahasa berhasil diperbarui.',
            'code' => 200,
        ], 200);
    }
}
