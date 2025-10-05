<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VerifyActiveRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip verification for login/logout routes
        if ($request->routeIs(['login', 'logout', 'role-selector.*'])) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $activeRole = Session::get('active_role');

        // If no active role in session, redirect to role selector
        if (!$activeRole) {
            // If user has multiple roles, redirect to role selector
            if ($user->roles->count() > 1) {
                return redirect()->route('role-selector.index');
            }
            
            // If user has single role, set it as active and continue
            if ($user->roles->count() === 1) {
                $singleRole = $user->roles->first();
                Session::put('active_role', $singleRole->name);
                Session::put('active_role_display', $this->getRoleDisplayName($singleRole->name));
                return $next($request);
            }
            
            // If user has no roles, logout and redirect to login
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akses Anda belum diatur. Silahkan hubungi admin untuk mendapatkan bantuan.');
        }

        // Verify that user still has the active role
        $cleanRole = str_replace('hrd_', '', $activeRole);
        if (!$user->hasHrdRole($cleanRole)) {
            // Clear invalid role from session
            Session::forget(['active_role', 'active_role_display']);
            
            // If user still has other roles, redirect to role selector
            if ($user->roles->count() > 0) {
                return redirect()->route('role-selector.index')
                    ->with('warning', 'Role Anda telah berubah. Silakan pilih role yang valid.');
            }
            
            // If user has no roles, logout
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akses Anda telah dicabut. Silahkan hubungi admin untuk mendapatkan bantuan.');
        }

        // Verify user account is still active
        if ($user->is_active != 1) {
            Auth::logout();
            Session::flush();
            return redirect()->route('login')->with('error', 'Akun Anda sudah tidak aktif.');
        }

        return $next($request);
    }

    /**
     * Get user-friendly display name for role
     */
    private function getRoleDisplayName($roleName)
    {
        $cleanRole = str_replace('hrd_', '', $roleName);
        
        $displayNames = [
            'pegawai_biasa' => 'Pegawai',
            'struktural' => 'Pejabat Struktural',
            'it_member' => 'Staff IT',
            'it_head' => 'Kepala IT',
            'kepegawaian' => 'Staff Kepegawaian',
            'superadmin' => 'Super Administrator',
            'pegawai_viewer' => 'Viewer Pegawai'
        ];

        return $displayNames[$cleanRole] ?? ucwords(str_replace('_', ' ', $cleanRole));
    }
}