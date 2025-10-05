<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetActiveRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Jika belum ada active role di session, set default
            if (!Session::has('active_role')) {
                // Jika user hanya punya satu role, set sebagai active
                if ($user->roles->count() === 1) {
                    $singleRole = $user->roles->first();
                    Session::put('active_role', $singleRole->name);
                    Session::put('active_role_display', $this->getRoleDisplayName($singleRole->name));
                }
                // Jika user punya multiple roles, akan di-handle di role selector
            }
            
            // Verifikasi bahwa user masih memiliki active role
            $activeRole = Session::get('active_role');
            if ($activeRole && !$user->hasRole($activeRole)) {
                // Role sudah tidak valid, hapus dari session
                Session::forget(['active_role', 'active_role_display']);
            }
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