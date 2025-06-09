<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Models\HubunganKeluarga;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class HubunganKeluargaController extends Controller
{
    public function index()
    {
        $pageTitle = 'Hubungan Keluarga';

        if (request()->ajax()) {
            $query = HubunganKeluarga::query();

            return DataTables::of($query)
                ->addColumn('name', function ($row) {
                    return $row->hub_klrg;
                })
                ->addColumn('action', function ($row) {
                    return view('settings.hubungan_keluarga.datatables-column._actions', compact('row'));
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('hub_klrg', $order);
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('hub_klrg', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('settings.hubungan_keluarga.index', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hubungan' => 'required|string|max:255|unique:hrd_hub_keluarga,hub_klrg',
        ], [
            'hubungan.required' => 'Hubungan keluarga wajib diisi.',
            'hubungan.max' => 'Hubungan keluarga maksimal 255 karakter.',
            'hubungan.unique' => 'Hubungan keluarga sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $lastKdhubungan = HubunganKeluarga::max('kd_hub_klrg') ?? 0;
        $newKdHubungan = $lastKdhubungan + 1;

        HubunganKeluarga::create([
            'kd_hub_klrg' => $newKdHubungan,
            'hub_klrg' => $request->hubungan,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Hubungan Keluarga berhasil ditambahkan.',
            'code' => 200,
        ]);
    }

    public function edit($id)
    {
        $hubungan = HubunganKeluarga::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $hubungan,
            'code' => 200,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'hubungan' => 'required|string|max:255|unique:hrd_hub_keluarga,hub_klrg,' . $id . ',kd_hub_klrg',
        ], [
            'hubungan.required' => 'Hubungan keluarga wajib diisi.',
            'hubungan.max' => 'Hubungan keluarga maksimal 255 karakter.',
            'hubungan.unique' => 'Hubungan keluarga sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        $hubungan = HubunganKeluarga::findOrFail($id);
        $hubungan->update([
            'hub_klrg' => $request->hubungan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hubungan Keluarga berhasil diperbarui.',
            'code' => 200,
        ]);
    }
}
