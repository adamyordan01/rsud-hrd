<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected $redirectTo = '/admin/dashboard';

    protected function validatedLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ], [
            'login.required' => 'Kode karyawan atau email tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
        ]);

        $validator = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? $validator->sometimes('login', 'email', function ($input) {
                return !filter_var($input->login, FILTER_VALIDATE_EMAIL);
            })
            : $validator->sometimes('login', 'exists:HRD_KARYAWAN,KD_KARYAWAN', function ($input) {
                return filter_var($input->login, FILTER_VALIDATE_EMAIL);
            }
        );

        return $validator;
    }

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = $this->validatedLogin($request);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $kd_karyawan = $request->input('login');
        $password = $request->input('password');

        $field = filter_var($kd_karyawan, FILTER_VALIDATE_EMAIL) ? 'email' : 'kd_karyawan';
        $user = User::with('karyawan')->where($field, $kd_karyawan)->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->is_active == 1) {
                // Cek apakah user memiliki role
                if ($user->roles->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['login' => 'Akses Anda belum diatur. Silahkan hubungi admin untuk mendapatkan bantuan.']
                    ]);
                }
                
                Auth::login($user);
                $request->session()->regenerate();
                
                // Tentukan redirect berdasarkan role
                $redirectPath = $user->hasRole('pegawai_biasa') && $user->roles->count() === 1 
                    ? route('user.dashboard.index')
                    : route('admin.dashboard.index');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil melakukan login',
                    'redirect' => $redirectPath
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'errors' => ['login' => 'Akun anda sudah tidak aktif']
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'errors' => ['login' => 'Kode karyawan atau password salah']
            ]);
        }
    }

    public function old_login(Request $request)
    {
        $validator = $this->validatedLogin($request);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $kd_karyawan = $request->input('login');
        $password = $request->input('password');

        $field = filter_var($kd_karyawan, FILTER_VALIDATE_EMAIL) ? 'email' : 'kd_karyawan';
        // $user = User::where($field, $kd_karyawan)->first();
        $user = User::with('karyawan')->where($field, $kd_karyawan)->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->is_active == 1) {
                Auth::login($user);
                $request->session()->regenerate();
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil melakukan login',
                    // 'redirect' => session('url.intended', $this->redirectTo)
                    'redirect' => $this->redirectTo
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'errors' => ['login' => 'Akun anda sudah tidak aktif']
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'errors' => ['login' => 'Kode karyawan atau password salah']
            ]);
        }
    }
}