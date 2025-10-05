<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Helpers\PermissionHelper;

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
        
        // Ambil active role dari session, jika ada
        $activeRole = Session::get('active_role');
        
        // Jika ada active role, gunakan itu untuk menentukan redirect
        if ($activeRole) {
            $cleanRole = str_replace('hrd_', '', $activeRole);
            
            // Jika active role adalah pegawai_biasa
            if ($cleanRole === 'pegawai_biasa') {
                // Jika mencoba mengakses area admin, redirect ke user dashboard
                if ($request->is('admin*')) {
                    return redirect('/user/dashboard');
                }
            } 
            // Jika active role bukan pegawai_biasa
            else {
                // Jika mencoba mengakses area user, redirect ke admin dashboard
                if ($request->is('user*')) {
                    return redirect('/admin/dashboard');
                }
            }
        }
        // Fallback ke logic lama jika tidak ada active role
        else {
            // Cek jika user hanya memiliki role pegawai_biasa HRD
            $hrdPegawaiBiasaRole = PermissionHelper::addRolePrefix('pegawai_biasa');
            $hasOnlyPegawaiBiasa = $user->roles->count() === 1 && 
                                   $user->roles->first()->name === $hrdPegawaiBiasaRole;
            
            if ($hasOnlyPegawaiBiasa) {
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
        }

        return $next($request);
    }
}
