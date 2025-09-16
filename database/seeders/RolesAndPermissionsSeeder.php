<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define HRD application prefix
        $prefix = 'hrd_';

        // Get existing permissions dari database (semua milik HRD)
        $existingPermissions = [
            'view_any_data',
            'view_own_data', 
            'create_data',
            'edit_data',
            'delete_data',
            'view_user_management',
            'view_roles',
            'view_permissions',
            'view_users',
            'view_karyawan',
            'view_sk_karyawan',
            'view_mutasi_karyawan',
            'view_tugas_tambahan',
            'view_mutasi_on_process',
            'view_mutasi_pending',
            'view_mutasi_verifikasi',
            'view_dashboard',
            'view_jenis_tenaga',
            'view_karyawan_jenjang_pendidikan',
            'view_karyawan_golongan',
            'view_karyawan_ruangan',
            'view_settings',
            'view_export',
            'view_laporan'
        ];

        // Create HRD Permissions dengan prefix
        foreach ($existingPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $prefix . $permission,
                'guard_name' => 'web'
            ], [
                'slug' => str_replace('_', '.', $prefix . $permission),
                'description' => 'HRD - ' . str_replace('_', ' ', ucfirst($permission))
            ]);
        }

        // Define roles dengan permissions yang sesuai existing data
        $roles = [
            'pegawai_biasa' => [
                'name' => $prefix . 'pegawai_biasa',
                'description' => 'HRD - Pegawai Biasa Role',
                'level' => 1,
                'permissions' => [
                    $prefix . 'view_own_data'
                ]
            ],
            'struktural' => [
                'name' => $prefix . 'struktural',
                'description' => 'HRD - Struktural Role', 
                'level' => 2,
                'permissions' => [
                    $prefix . 'view_own_data'
                ]
            ],
            'it_member' => [
                'name' => $prefix . 'it_member',
                'description' => 'HRD - IT Member Role',
                'level' => 3,
                'permissions' => [
                    $prefix . 'view_any_data',
                    $prefix . 'view_own_data',
                    $prefix . 'view_dashboard',
                    $prefix . 'view_jenis_tenaga',
                    $prefix . 'view_karyawan_jenjang_pendidikan',
                    $prefix . 'view_karyawan_golongan',
                    $prefix . 'view_karyawan_ruangan',
                    $prefix . 'view_settings'
                ]
            ],
            'it_head' => [
                'name' => $prefix . 'it_head',
                'description' => 'HRD - IT Head Role',
                'level' => 4,
                'permissions' => [
                    $prefix . 'view_any_data',
                    $prefix . 'view_own_data',
                    $prefix . 'create_data',
                    $prefix . 'edit_data',
                    $prefix . 'delete_data',
                    $prefix . 'view_user_management',
                    $prefix . 'view_roles', 
                    $prefix . 'view_permissions',
                    $prefix . 'view_users',
                    $prefix . 'view_dashboard',
                    $prefix . 'view_jenis_tenaga',
                    $prefix . 'view_karyawan_jenjang_pendidikan',
                    $prefix . 'view_karyawan_golongan',
                    $prefix . 'view_karyawan_ruangan',
                    $prefix . 'view_settings'
                ]
            ],
            'kepegawaian' => [
                'name' => $prefix . 'kepegawaian',
                'description' => 'HRD - Kepegawaian Role',
                'level' => 3,
                'permissions' => [
                    $prefix . 'view_dashboard',
                    $prefix . 'view_karyawan',
                    $prefix . 'view_jenis_tenaga',
                    $prefix . 'view_karyawan_jenjang_pendidikan',
                    $prefix . 'view_karyawan_golongan',
                    $prefix . 'view_karyawan_ruangan',
                    $prefix . 'view_sk_karyawan',
                    $prefix . 'view_mutasi_karyawan',
                    $prefix . 'view_tugas_tambahan',
                    $prefix . 'view_mutasi_on_process',
                    $prefix . 'view_export',
                    $prefix . 'view_laporan'
                ]
            ],
            'superadmin' => [
                'name' => $prefix . 'superadmin',
                'description' => 'HRD - Super Admin Role',
                'level' => 5,
                'permissions' => array_map(function($p) use ($prefix) { return $prefix . $p; }, $existingPermissions)
            ],
            'pegawai_viewer' => [
                'name' => $prefix . 'pegawai_viewer',
                'description' => 'HRD - Pegawai Viewer Role',
                'level' => 1,
                'permissions' => [
                    $prefix . 'view_own_data'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleData['name'],
                'guard_name' => 'web'
            ], [
                'description' => $roleData['description'],
                'level' => $roleData['level']
            ]);

            if (!empty($roleData['permissions'])) {
                $role->syncPermissions($roleData['permissions']);
            }
        }

        echo "HRD Roles and Permissions dengan prefix '{$prefix}' berhasil dibuat!\n";
        echo "Total roles: " . count($roles) . "\n";
        echo "Total permissions: " . count($existingPermissions) . "\n";
    }
}
