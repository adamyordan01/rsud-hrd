<?php

namespace App\Traits;

use App\Helpers\PermissionHelper;

trait HasHrdRolesAndPermissions
{
    /**
     * Check if user has HRD role
     *
     * @param string $role
     * @return bool
     */
    public function hasHrdRole(string $role): bool
    {
        $prefixedRole = PermissionHelper::addRolePrefix($role);
        return $this->hasRole($prefixedRole);
    }

    /**
     * Check if user has HRD permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasHrdPermission(string $permission): bool
    {
        $prefixedPermission = PermissionHelper::addPrefix($permission);
        return $this->hasPermissionTo($prefixedPermission);
    }

    /**
     * Check if user has HRD permission based on active role only
     *
     * @param string $permission
     * @return bool
     */
    public function hasActiveRolePermission(string $permission): bool
    {
        // Get active role from session
        $activeRole = session('active_role');
        
        if (!$activeRole) {
            return false;
        }

        // Check if user still has this active role
        if (!$this->roles->contains('name', $activeRole)) {
            return false;
        }

        // Add HRD prefix if not already present
        $prefixedPermission = str_starts_with($permission, 'hrd_') ? $permission : 'hrd_' . $permission;
        
        // Find the specific role
        $role = $this->roles->firstWhere('name', $activeRole);
        
        if (!$role) {
            return false;
        }

        // Check if this specific role has the permission
        return $role->permissions->contains('name', $prefixedPermission);
    }

    /**
     * Assign HRD role to user
     *
     * @param string $role
     * @return $this
     */
    public function assignHrdRole(string $role): self
    {
        $prefixedRole = PermissionHelper::addRolePrefix($role);
        $this->assignRole($prefixedRole);
        return $this;
    }

    /**
     * Give HRD permission to user
     *
     * @param string $permission
     * @return $this
     */
    public function giveHrdPermission(string $permission): self
    {
        $prefixedPermission = PermissionHelper::addPrefix($permission);
        $this->givePermissionTo($prefixedPermission);
        return $this;
    }

    /**
     * Remove HRD role from user
     *
     * @param string $role
     * @return $this
     */
    public function removeHrdRole(string $role): self
    {
        $prefixedRole = PermissionHelper::addRolePrefix($role);
        $this->removeRole($prefixedRole);
        return $this;
    }

    /**
     * Get user's HRD roles (without prefix)
     *
     * @return array
     */
    public function getHrdRoles(): array
    {
        return $this->roles
            ->filter(function ($role) {
                return PermissionHelper::isHrdRole($role->name);
            })
            ->map(function ($role) {
                return PermissionHelper::removePrefix($role->name);
            })
            ->values()
            ->toArray();
    }

    /**
     * Get user's HRD permissions (without prefix)
     *
     * @return array
     */
    public function getHrdPermissions(): array
    {
        return $this->getAllPermissions()
            ->filter(function ($permission) {
                return PermissionHelper::isHrdPermission($permission->name);
            })
            ->map(function ($permission) {
                return PermissionHelper::removePrefix($permission->name);
            })
            ->values()
            ->toArray();
    }

    /**
     * Check if user has any HRD roles
     *
     * @return bool
     */
    public function hasAnyHrdRole(): bool
    {
        return $this->roles->filter(function ($role) {
            return PermissionHelper::isHrdRole($role->name);
        })->isNotEmpty();
    }

    /**
     * Check if user has HRD roles only (for redirect logic)
     *
     * @return bool
     */
    public function hasOnlyHrdRoles(): bool
    {
        $allRoles = $this->roles;
        $hrdRoles = $allRoles->filter(function ($role) {
            return PermissionHelper::isHrdRole($role->name);
        });

        return $allRoles->count() === $hrdRoles->count() && $hrdRoles->isNotEmpty();
    }
}
