<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\DataTables\RoleDataTable;
use Illuminate\Support\Facades\Validator;
use App\Helpers\PermissionHelper;

class RoleController extends Controller
{
    public function index(RoleDataTable $dataTable)
    {
        // Hanya tampilkan HRD permissions untuk role management
        $permissions = Permission::where('name', 'like', 'hrd_%')
            ->orderBy('name')
            ->get();
        
        return $dataTable->render('roles.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|numeric',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
            'name.string' => 'Nama role harus berupa string.',
            'name.max' => 'Nama role maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa string.',
            'level.numeric' => 'Level harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Auto-add HRD prefix if not present
        $roleName = PermissionHelper::addRolePrefix($request->name);

        $role = Role::create([
            'name' => $roleName,
            'description' => $request->description,
            'level' => $request->level,
            'guard_name' => 'web',
        ]);

        // Filter permissions to only HRD permissions
        $hrdPermissions = collect($request->permissions ?? [])
            ->filter(function ($permission) {
                return str_starts_with($permission, 'hrd_');
            })
            ->toArray();

        $role->syncPermissions($hrdPermissions);

        // clear cache
        cache()->forget('spatie.permission.cache');

        return response()->json([
            'code' => 201,
            'status' => 'success',
            'message' => 'Role berhasil ditambahkan.',
            'data' => $role,
        ], 201);
    }

    // show akan saya gunakan untuk menampilkan role dan permission yang dimiliki oleh role tersebut, tampilkan informasi terkait jumlah user yang memiliki role tersebut dan total permission yang dimiliki oleh role tersebut
    public function show($id)
    {
        $role = Role::with('permissions')
            ->withCount(['permissions', 'users'])
            ->findOrFail($id);

        // Filter hanya HRD roles jika diperlukan
        if (!PermissionHelper::isHrdRole($role->name)) {
            return response()->json([
                'code' => 403,
                'status' => 'error',
                'message' => 'Access denied. This role is not part of HRD system.',
            ], 403);
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $role,
        ], 200);
    }

    // edit akan saya gunakan untuk mengedit role dan permission jika terdapat pivotnya kirim data dalam bentuk json karena saya akan menggunakan ajax untuk mengeditnya
    public function edit($id)
    {
        $role = Role::with('permissions')->find($id);
        // $permissions = Permission::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $role,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrfail($id);

        // Security check - hanya allow update HRD roles
        if (!PermissionHelper::isHrdRole($role->name)) {
            return response()->json([
                'code' => 403,
                'status' => 'error',
                'message' => 'Access denied. Cannot modify non-HRD roles.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'level' => 'nullable|numeric',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
            'name.string' => 'Nama role harus berupa string.',
            'name.max' => 'Nama role maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa string.',
            'level.numeric' => 'Level harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Ensure HRD prefix
        $roleName = PermissionHelper::addRolePrefix($request->name);

        $role->update([
            'name' => $roleName,
            'description' => $request->description,
            'level' => $request->level,
        ]);

        // Filter permissions to only HRD permissions
        $hrdPermissions = collect($request->permissions ?? [])
            ->filter(function ($permission) {
                return str_starts_with($permission, 'hrd_');
            })
            ->toArray();

        $role->syncPermissions($hrdPermissions);

        // clear cache
        cache()->forget('spatie.permission.cache');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Role berhasil diupdate.',
            'data' => $role,
        ], 200);
    }
    
}
