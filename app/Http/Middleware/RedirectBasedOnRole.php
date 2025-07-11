<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next)
    {
        // Jika belum login, lanjutkan request normal
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Cek jika user tidak memiliki role
        if ($user->roles->isEmpty()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akses Anda belum diatur. Silahkan hubungi admin untuk mendapatkan bantuan.');
        }
        
        // Cek jika user hanya memiliki role pegawai_biasa
        if ($user->hasRole('pegawai_biasa') && $user->roles->count() === 1) {
            // Jika mencoba mengakses area admin, redirect ke user dashboard
            if ($request->is('admin*')) {
                return redirect('/user/dashboard');
            }
        } 
        // Jika user memiliki role lain selain atau di samping pegawai_biasa
        else {
            // Jika mencoba mengakses area user, redirect ke admin dashboard
            if ($request->is('user*')) {
                return redirect('/admin/dashboard');
            }
        }

        return $next($request);
    }
}
