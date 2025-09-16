<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\PermissionHelper;

class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        // Hanya tampilkan HRD roles untuk user management
        $roles = Role::where('name', 'like', 'hrd_%')
            ->orderBy('level')
            ->get();
            
        return $dataTable->render('user.index', compact('roles'));
    }

    public function show($id)
    {
        $user = User::with('karyawan')->findOrFail($id);

        $nama_lengkap = trim(($user->karyawan->gelar_depan ?? '') . ' ' . $user->karyawan->nama . '' . ($user->karyawan->gelar_belakang ?? ''));
        $user->karyawan->nama_lengkap = $nama_lengkap;
        
        // Pastikan data roles benar-benar array string sederhana - filter hanya HRD roles
        $roleNames = $user->roles
            ->filter(function ($role) {
                return str_starts_with($role->name, 'hrd_');
            })
            ->pluck('name')
            ->all();
        
        // Buat array data user manual untuk memastikan struktur yang diinginkan
        $userData = [
            'id' => $user->id,
            'name' => $user->karyawan->nama_lengkap,
            'email' => $user->email,
            'kd_karyawan' => $user->kd_karyawan,
            'karyawan' => $user->karyawan, // relasi karyawan
            'roles' => $roleNames, // array string sederhana - hanya HRD roles
            'hrd_roles' => $user->getHrdRoles(), // menggunakan method dari trait
            'hrd_permissions' => $user->getHrdPermissions(), // list HRD permissions
        ];

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $userData,
        ]);
    }

    public function assignRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::findOrFail($id);
        
        // Filter hanya HRD roles yang diizinkan
        $hrdRoles = collect($request->roles)
            ->filter(function ($role) {
                return str_starts_with($role, 'hrd_');
            })
            ->toArray();

        if (empty($hrdRoles)) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'No valid HRD roles provided.',
            ], 422);
        }

        // Hapus semua HRD roles yang ada, kemudian assign yang baru
        $user->roles()->whereIn('name', $user->getHrdRoles())->detach();
        $user->assignRole($hrdRoles);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'HRD Role berhasil diberikan kepada user.',
            'data' => [
                'user' => $user,
                'assigned_roles' => $hrdRoles,
            ],
        ]);
    }
}
