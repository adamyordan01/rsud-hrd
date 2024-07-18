<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
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
        $password = md5($request->input('password'));

        $field = filter_var($kd_karyawan, FILTER_VALIDATE_EMAIL) ? 'EMAIL' : 'KD_KARYAWAN';
        $user = User::where($field, $kd_karyawan)->first();

        if ($user && $password == $user->password) {
            Auth::login($user);
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan login',
                'redirect' => '/admin/dashboard'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => ['login' => 'Kode karyawan atau password salah']
            ]);
        }
    }
}