<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.reset-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // $request->validate(['email' => 'required|email']);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => __($status)
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => __($status)
        ], 422);
    }
}
