<?php

namespace App\Http\Controllers\Settings;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class JurusanController extends Controller
{
    public function index()
    {
        $pageTitle = 'Jurusan';

        if (request()->ajax()) {
            $query = Jurusan::query();

            return DataTables::of($query)
                ->addColumn('name', function ($row) {
                    return $row->jurusan;
                })
                ->addColumn('group', function ($row) {
                    return $row->grup_jurusan;
                })
                ->addColumn('action', function ($row) {
                    return view('settings.jurusan.datatables-column._actions', compact('row'));
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('jurusan', $order);
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('jurusan', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.jurusan.index', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jurusan' => 'required|string|max:255|unique:hrd_jurusan,jurusan',
            'grup' => 'required|in:1,2',
        ], [
            'jurusan.required' => 'Nama jurusan wajib diisi.',
            'jurusan.unique' => 'Nama jurusan sudah ada.',
            'grup.required' => 'Grup jurusan wajib diisi.',
            'grup.in' => 'Grup jurusan harus 1 atau 2.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // ambil kd_jurusan terakhir dan tambahkan 1
        $lastKd = Jurusan::max('kd_jurusan') ?? 0;
        $newKd = $lastKd + 1;

        Jurusan::create([
            'kd_jurusan' => $newKd,
            'jurusan' => $request->jurusan,
            'grup_jurusan' => $request->grup,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data jurusan berhasil disimpan.',
            'code' => 200,
        ], 200);
    }

    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);

        // ubah grup_jurusan menjadi grup
        $jurusan->grup = $jurusan->grup_jurusan;

        return response()->json([
            'success' => true,
            'data' => $jurusan,
            'code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jurusan' => 'required|string|max:255|unique:hrd_jurusan,jurusan,' . $id . ',kd_jurusan',
            'grup' => 'required|in:1,2',
        ], [
            'jurusan.required' => 'Nama jurusan wajib diisi.',
            'jurusan.unique' => 'Nama jurusan sudah ada.',
            'grup.required' => 'Grup jurusan wajib diisi.',
            'grup.in' => 'Grup jurusan harus 1 atau 2.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $jurusan = Jurusan::findOrFail($id);
        $jurusan->update([
            'jurusan' => $request->jurusan,
            'grup_jurusan' => $request->grup,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data jurusan berhasil diperbarui.',
            'code' => 200,
        ], 200);
    }
}
