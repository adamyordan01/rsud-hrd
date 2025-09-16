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
