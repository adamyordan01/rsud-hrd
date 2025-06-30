<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ChangeProfileController extends Controller
{
    public function changePassword(Request $request)
    {
        // current_password, new_password, new_password_confirmation
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Password lama tidak cocok.',
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
