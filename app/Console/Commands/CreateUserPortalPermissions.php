<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class CreateUserPortalPermissions extends Command
{
    protected $signature = 'hrd:create-user-permissions';
    protected $description = 'Create permissions for user portal and assign to hrd_pegawai_biasa role';

    public function handle()
    {
        $this->info('Creating User Portal Permissions...');

        // Define all permissions needed for user portal
        $permissions = [
            // Dashboard
            'hrd_view_user_dashboard' => 'HRD - Akses Dashboard Pegawai',
            
            // Profile Management
            'hrd_view_own_profile' => 'HRD - Melihat Profil Pribadi',
            'hrd_edit_own_profile' => 'HRD - Edit Profil Pribadi',
            'hrd_upload_own_photo' => 'HRD - Upload Foto Profil',
            
            // Data Kepegawaian
            'hrd_view_own_sk' => 'HRD - Melihat SK Pribadi',
            'hrd_download_own_sk' => 'HRD - Download SK Pribadi',
            'hrd_view_own_mutasi' => 'HRD - Melihat Riwayat Mutasi',
            'hrd_download_own_mutasi' => 'HRD - Download Dokumen Mutasi',
            'hrd_view_own_izin' => 'HRD - Melihat Surat Izin',
            'hrd_download_own_izin' => 'HRD - Download Surat Izin',
            
            // Sertifikasi & Pelatihan
            'hrd_view_own_str' => 'HRD - Melihat STR Pribadi',
            'hrd_download_own_str' => 'HRD - Download STR',
            'hrd_view_own_sip' => 'HRD - Melihat SIP Pribadi',
            'hrd_download_own_sip' => 'HRD - Download SIP',
            'hrd_view_own_sertifikat' => 'HRD - Melihat Sertifikat Pelatihan',
            'hrd_download_own_sertifikat' => 'HRD - Download Sertifikat',
        ];

        $created = 0;
        $existing = 0;

        // Create permissions
        foreach ($permissions as $name => $description) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ], [
                'slug' => str_replace('_', '.', $name),
                'description' => $description
            ]);

            if ($permission->wasRecentlyCreated) {
                $this->line("✅ Created: {$name}");
                $created++;
            } else {
                $this->line("ℹ️  Exists: {$name}");
                $existing++;
            }
        }

        $this->info("\nPermissions Summary:");
        $this->line("Created: {$created}");
        $this->line("Already exists: {$existing}");

        // Assign to hrd_pegawai_biasa role
        $role = Role::where('name', 'hrd_pegawai_biasa')->first();
        
        if ($role) {
            $this->info("\nAssigning permissions to hrd_pegawai_biasa role...");
            
            // Get all permission names to assign
            $allPermissions = array_merge(['hrd_view_own_data'], array_keys($permissions));
            
            // Sync permissions
            $role->syncPermissions($allPermissions);
            
            $this->info("✅ Assigned " . count($allPermissions) . " permissions to hrd_pegawai_biasa");
            
            // Show assigned permissions
            $this->line("\nAssigned permissions:");
            foreach ($allPermissions as $perm) {
                $this->line("  - {$perm}");
            }
        } else {
            $this->error("❌ Role hrd_pegawai_biasa not found!");
        }

        // Clear cache
        cache()->forget('spatie.permission.cache');
        $this->info("\n✅ Permission cache cleared");
        
        $this->info("\n🎉 User Portal Permissions setup completed!");
    }
}