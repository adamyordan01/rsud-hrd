<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        return view('auth.new-password', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8'
        ], [
            'token.required' => 'Tautan reset password tidak valid atau sudah kedaluwarsa. Silakan coba kirim ulang permintaan reset password.',
            'email.required' => 'Tautan reset password tidak valid. Silakan gunakan tautan yang dikirim ke email Anda.',
            'email.email' => 'Tautan reset password tidak valid. Pastikan Anda menggunakan tautan dari email yang dikirim kepada Anda.',
            'password.required' => 'Silakan masukkan password baru Anda.',
            'password.confirmed' => 'Konfirmasi password tidak cocok. Pastikan kedua password sama.',
            'password.min' => 'Password harus terdiri dari minimal 8 karakter.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => __($status),
                'redirect' => route('login')
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => __($status)
        ], 422);
    }
}
