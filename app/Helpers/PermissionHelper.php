<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * HRD Application prefix untuk roles dan permissions
     */
    const HRD_PREFIX = 'hrd_';

    /**
     * Add HRD prefix to permission name
     *
     * @param string $permission
     * @return string
     */
    public static function addPrefix(string $permission): string
    {
        if (strpos($permission, self::HRD_PREFIX) === 0) {
            return $permission; // Already has prefix
        }
        
        return self::HRD_PREFIX . $permission;
    }

    /**
     * Add HRD prefix to role name
     *
     * @param string $role
     * @return string
     */
    public static function addRolePrefix(string $role): string
    {
        if (strpos($role, self::HRD_PREFIX) === 0) {
            return $role; // Already has prefix
        }
        
        return self::HRD_PREFIX . $role;
    }

    /**
     * Remove HRD prefix from permission/role name
     *
     * @param string $name
     * @return string
     */
    public static function removePrefix(string $name): string
    {
        if (strpos($name, self::HRD_PREFIX) === 0) {
            return substr($name, strlen(self::HRD_PREFIX));
        }
        
        return $name;
    }

    /**
     * Get all HRD permissions (dari database existing)
     *
     * @return array
     */
    public static function getHrdPermissions(): array
    {
        return [
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
            // Tambahan permissions yang mungkin ada di aplikasi
            'create_karyawan',
            'edit_karyawan',
            'delete_karyawan',
            'create_sk_karyawan',
            'edit_sk_karyawan',
            'create_mutasi_karyawan',
            'edit_mutasi_karyawan',
            'view_laporan',
            'print_laporan',
            'export_data',
            'view_settings',
            'edit_settings',
        ];
    }

    /**
     * Get all HRD roles (dari database existing)
     *
     * @return array
     */
    public static function getHrdRoles(): array
    {
        return [
            'pegawai_biasa',
            'struktural', 
            'it_member',
            'it_head',
            'kepegawaian',
            'superadmin',
            'pegawai_viewer'
        ];
    }

    /**
     * Check if permission belongs to HRD app
     *
     * @param string $permission
     * @return bool
     */
    public static function isHrdPermission(string $permission): bool
    {
        return strpos($permission, self::HRD_PREFIX) === 0;
    }

    /**
     * Check if role belongs to HRD app
     *
     * @param string $role
     * @return bool
     */
    public static function isHrdRole(string $role): bool
    {
        return strpos($role, self::HRD_PREFIX) === 0;
    }
}
