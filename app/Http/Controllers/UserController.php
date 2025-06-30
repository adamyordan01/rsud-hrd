<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        $roles = Role::all();
        return $dataTable->render('user.index', compact('roles'));
    }

    public function show($id)
    {
        $user = User::with('karyawan')->findOrFail($id);

        $nama_lengkap = trim(($user->karyawan->gelar_depan ?? '') . ' ' . $user->karyawan->nama . '' . ($user->karyawan->gelar_belakang ?? ''));
        $user->karyawan->nama_lengkap = $nama_lengkap;
        
        // Pastikan data roles benar-benar array string sederhana
        $roleNames = $user->roles->pluck('name')->all(); // gunakan all() sebagai alternatif toArray() yang lebih aman
        
        // Buat array data user manual untuk memastikan struktur yang diinginkan
        $userData = [
            'id' => $user->id,
            'name' => $user->karyawan->nama_lengkap,
            'email' => $user->email,
            'kd_karyawan' => $user->kd_karyawan,
            'karyawan' => $user->karyawan, // relasi karyawan
            'roles' => $roleNames, // array string sederhana
            // tambahkan field lain yang diperlukan
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
        $user->syncRoles($request->roles); // Gunakan syncRoles untuk mendukung banyak role

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Role berhasil diberikan kepada user.',
            'data' => $user,
        ]);
    }
}
