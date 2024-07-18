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

        // $validator->sometimes('login', 'email', function ($input) {
        //     return !filter_var($input->login, FILTER_VALIDATE_EMAIL);
        // });

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
        $this->validatedLogin($request)->validate();
        
        $kd_karyawan = $request->input('login');
        $password = md5($request->input('password'));

        $field = filter_var($kd_karyawan, FILTER_VALIDATE_EMAIL) ? 'EMAIL' : 'KD_KARYAWAN';
        $user = User::where($field, $kd_karyawan)->first();
        // unset($user->PASSWORD);

        // dd($user->PASSWORD == $password ? 'true' : 'false');
        if ($user) {
            if ($password == $user->password) {
                Auth::login($user);
                
                // dd($user);
                $request->session()->regenerate();
                return redirect()->intended('/admin/dashboard');
            } else {
                return back()->withErrors([
                    'login' => 'Kode karyawan atau password salah',
                ]);
            }
        } else {
            return back()->withErrors([
                'login' => 'Kode karyawan atau password salah',
            ])->withInput();
        }
    }
}
