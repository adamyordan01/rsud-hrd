<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleSelectorController extends Controller
{
    /**
     * Show role selector page
     */
    public function index()
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Jika user tidak punya role, redirect ke login dengan error
        if ($user->roles->isEmpty()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akses Anda belum diatur. Silahkan hubungi admin untuk mendapatkan bantuan.');
        }

        // Jika user hanya punya satu role, langsung redirect
        if ($user->roles->count() === 1) {
            $singleRole = $user->roles->first();
            $destination = $this->setActiveRoleAndRedirect($singleRole->name);
            return redirect($destination);
        }

        // Ambil semua HRD roles yang dimiliki user
        $availableRoles = $user->getHrdRoles();
        
        // Transform roles untuk tampilan yang lebih user-friendly
        $roleOptions = [];
        foreach ($availableRoles as $role) {
            $roleOptions[] = [
                'name' => $role,
                'display_name' => $this->getRoleDisplayName($role),
                'description' => $this->getRoleDescription($role),
                'icon' => $this->getRoleIcon($role),
                'destination' => $this->getRoleDestination($role)
            ];
        }

        return view('auth.role-selector', compact('roleOptions'));
    }

    /**
     * Set active role and redirect to appropriate dashboard
     */
    public function selectRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        $user = Auth::user();
        $selectedRole = $request->input('role');

        // Verify user has this role
        if (!$user->hasHrdRole($selectedRole)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke role tersebut.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'redirect' => $this->setActiveRoleAndRedirect($selectedRole)
        ]);
    }

    /**
     * Switch active role (untuk role switcher di navbar)
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        $user = Auth::user();
        $newRole = $request->input('role');

        // Verify user has this role
        if (!$user->hasHrdRole($newRole)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke role tersebut.'
            ], 403);
        }

        // Set new active role
        Session::put('active_role', 'hrd_' . $newRole);
        Session::put('active_role_display', $this->getRoleDisplayName($newRole));

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diubah ke ' . $this->getRoleDisplayName($newRole),
            'redirect' => $this->getRoleDestination($newRole)
        ]);
    }

    /**
     * Set active role in session and return redirect URL
     */
    private function setActiveRoleAndRedirect($roleName)
    {
        // Remove hrd_ prefix for clean role name
        $cleanRole = str_replace('hrd_', '', $roleName);
        
        // Set active role in session
        Session::put('active_role', $roleName);
        Session::put('active_role_display', $this->getRoleDisplayName($cleanRole));

        // Determine redirect destination
        $destination = $this->getRoleDestination($cleanRole);
        
        return $destination;  // Return URL string, not redirect object
    }

    /**
     * Get user-friendly display name for role
     */
    private function getRoleDisplayName($role)
    {
        $displayNames = [
            'pegawai_biasa' => 'Pegawai',
            'struktural' => 'Pejabat Struktural',
            'it_member' => 'Staff IT',
            'it_head' => 'Kepala IT',
            'kepegawaian' => 'Staff Kepegawaian',
            'superadmin' => 'Super Administrator',
            'pegawai_viewer' => 'Viewer Pegawai'
        ];

        return $displayNames[$role] ?? ucwords(str_replace('_', ' ', $role));
    }

    /**
     * Get description for role
     */
    private function getRoleDescription($role)
    {
        $descriptions = [
            'pegawai_biasa' => 'Akses profil personal, slip gaji, dan informasi kepegawaian pribadi',
            'struktural' => 'Akses manajemen pegawai dan laporan struktural',
            'it_member' => 'Akses sistem dan dukungan teknis',
            'it_head' => 'Akses penuh sistem dan manajemen IT',
            'kepegawaian' => 'Akses manajemen data kepegawaian dan administrasi',
            'superadmin' => 'Akses penuh ke semua fitur sistem',
            'pegawai_viewer' => 'Akses read-only ke data pegawai'
        ];

        return $descriptions[$role] ?? 'Akses khusus untuk ' . $this->getRoleDisplayName($role);
    }

    /**
     * Get icon for role
     */
    private function getRoleIcon($role)
    {
        $icons = [
            'pegawai_biasa' => 'fas fa-user',
            'struktural' => 'fas fa-users-cog',
            'it_member' => 'fas fa-laptop-code',
            'it_head' => 'fas fa-crown',
            'kepegawaian' => 'fas fa-clipboard-list',
            'superadmin' => 'fas fa-shield-alt',
            'pegawai_viewer' => 'fas fa-eye'
        ];

        return $icons[$role] ?? 'fas fa-user-tag';
    }

    /**
     * Get destination URL for role
     */
    private function getRoleDestination($role)
    {
        // Pegawai biasa goes to user dashboard, others go to admin dashboard
        if ($role === 'pegawai_biasa') {
            return route('user.dashboard');
        }

        return route('admin.dashboard.index');
    }
}