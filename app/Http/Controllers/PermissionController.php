<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\DataTables\PermissionDataTable;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index(PermissionDataTable $dataTable)
    {
        return $dataTable->render('permissions.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama permission wajib diisi.',
            'name.unique' => 'Nama permission sudah digunakan.',
            'name.string' => 'Nama permission harus berupa string.',
            'name.max' => 'Nama permission maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa string.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $slug = str_replace('-', '.', Str::slug($request->name));

        $permission = Permission::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'guard_name' => 'web',
        ]);

        // clear cache
        cache()->forget('spatie.permission.cache');

        return response()->json([
            'code' => 201,
            'status' => 'success',
            'message' => 'Permission berhasil ditambahkan.',
            'data' => $permission,
        ], 201);
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $permission,
        ]);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama permission wajib diisi.',
            'name.unique' => 'Nama permission sudah digunakan.',
            'name.string' => 'Nama permission harus berupa string.',
            'name.max' => 'Nama permission maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa string.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $slug = str_replace('-', '.', Str::slug($request->name)); // Ubah tanda hubung menjadi tanda titik

        $permission->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        // clear cache
        cache()->forget('spatie.permission.cache');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Permission berhasil diperbarui.',
            'data' => $permission,
        ]);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Permission berhasil dihapus.',
        ]);
    }

    public function show($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $permission,
        ]);
    }
}
