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

    /**
     * Show create role page
     */
    public function create()
    {
        // Hanya tampilkan HRD permissions
        $permissions = Permission::where('name', 'like', 'hrd_%')
            ->orderBy('name')
            ->get();
        
        // Group permissions by category
        $groupedPermissions = $this->groupPermissionsByCategory($permissions);
        
        return view('roles.create', compact('permissions', 'groupedPermissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|numeric',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
            'name.string' => 'Nama role harus berupa string.',
            'name.max' => 'Nama role maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa string.',
            'level.numeric' => 'Level harus berupa angka.',
        ]);

        if ($validator->fails()) {
            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
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
        $permissionIds = $request->permissions ?? [];
        
        if (!empty($permissionIds)) {
            // Get permissions by IDs and filter only HRD permissions
            $hrdPermissions = Permission::whereIn('id', $permissionIds)
                ->where('name', 'like', 'hrd_%')
                ->pluck('name')
                ->toArray();
        } else {
            $hrdPermissions = [];
        }

        $role->syncPermissions($hrdPermissions);

        // clear cache
        cache()->forget('spatie.permission.cache');

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Role berhasil ditambahkan.',
                'data' => $role,
            ], 201);
        } else {
            return redirect()->route('admin.user-management.roles.index')
                ->with('success', 'Role ' . str_replace('hrd_', '', $role->name) . ' berhasil ditambahkan.');
        }
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
        
        // Security check - hanya allow edit HRD roles
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
            'data' => [
                'role' => $role
            ],
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

        // Ensure HRD prefix untuk nama yang akan dicek
        $roleName = PermissionHelper::addRolePrefix($request->name);

        // Validasi dengan mengecek nama dengan prefix
        $validator = Validator::make([
            'name' => $roleName, // Gunakan nama dengan prefix untuk validasi
            'description' => $request->description,
            'level' => $request->level,
        ], [
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

        $role->update([
            'name' => $roleName,
            'description' => $request->description,
            'level' => $request->level,
        ]);

        // Filter permissions to only HRD permissions
        $permissionIds = $request->permissions ?? [];
        
        if (!empty($permissionIds)) {
            // Get permissions by IDs and filter only HRD permissions
            $hrdPermissions = Permission::whereIn('id', $permissionIds)
                ->where('name', 'like', 'hrd_%')
                ->pluck('name')
                ->toArray();
        } else {
            $hrdPermissions = [];
        }

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

    /**
     * Show edit permissions page for a role
     */
    public function editPermissions($id)
    {
        $role = Role::findOrFail($id);
        
        // Hanya tampilkan HRD permissions
        $permissions = Permission::where('name', 'like', 'hrd_%')
            ->orderBy('name')
            ->get();
        
        // Group permissions by category
        $permissionGroups = $this->groupPermissionsByCategory($permissions);
        
        // Get current role permissions
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit-permissions', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $permissionIds = $request->permissions ?? [];
        
        if (!empty($permissionIds)) {
            // Get permissions by IDs and filter only HRD permissions
            $hrdPermissions = Permission::whereIn('id', $permissionIds)
                ->where('name', 'like', 'hrd_%')
                ->pluck('name')
                ->toArray();
        } else {
            $hrdPermissions = [];
        }

        $role->syncPermissions($hrdPermissions);

        // clear cache
        cache()->forget('spatie.permission.cache');

        return redirect()->route('admin.user-management.roles.index')
            ->with('success', 'Permissions untuk role ' . $role->name . ' berhasil diupdate.');
    }

    /**
     * Group permissions by category for better organization
     */
    private function groupPermissionsByCategory($permissions)
    {
        $groups = [];
        
        foreach ($permissions as $permission) {
            $name = str_replace('hrd_', '', $permission->name);
            
            // Determine category based on permission name
            if (str_contains($name, 'dashboard')) {
                $category = 'Dashboard';
            } elseif (str_contains($name, 'karyawan')) {
                $category = 'Karyawan Management';
            } elseif (str_contains($name, 'user_management') || str_contains($name, 'roles') || str_contains($name, 'permissions') || str_contains($name, 'users')) {
                $category = 'User Management';
            } elseif (str_contains($name, 'sk_karyawan') || str_contains($name, 'sk')) {
                $category = 'SK Management';
            } elseif (str_contains($name, 'mutasi')) {
                $category = 'Mutasi Management';
            } elseif (str_contains($name, 'tugas_tambahan')) {
                $category = 'Tugas Tambahan';
            } elseif (str_contains($name, 'export')) {
                $category = 'Export Data';
            } elseif (str_contains($name, 'laporan')) {
                $category = 'Laporan';
            } elseif (str_contains($name, 'settings')) {
                $category = 'Settings';
            } else {
                $category = 'General';
            }
            
            if (!isset($groups[$category])) {
                $groups[$category] = [];
            }
            
            $groups[$category][] = $permission;
        }
        
        // Sort groups alphabetically
        ksort($groups);
        
        return $groups;
    }
    
}
