<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionHelper;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckRolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrd:check-roles-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check existing roles and permissions in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Checking existing roles and permissions...');
        $this->newLine();

        try {
            // Test database connection
            DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Check if permission tables exist
        $this->checkTables();
        
        // Check existing roles
        $this->checkExistingRoles();
        
        // Check existing permissions
        $this->checkExistingPermissions();
        
        // Check user assignments
        $this->checkUserAssignments();
        
        // Show recommendations
        $this->showRecommendations();

        return 0;
    }

    private function checkTables()
    {
        $this->info('📋 Checking permission tables...');
        
        $tables = ['roles', 'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions'];
        
        foreach ($tables as $table) {
            try {
                $exists = DB::getSchemaBuilder()->hasTable($table);
                if ($exists) {
                    $count = DB::table($table)->count();
                    $this->line("  ✅ {$table}: exists ({$count} records)");
                } else {
                    $this->line("  ❌ {$table}: not found");
                }
            } catch (\Exception $e) {
                $this->line("  ⚠️  {$table}: error checking - " . $e->getMessage());
            }
        }
        $this->newLine();
    }

    private function checkExistingRoles()
    {
        $this->info('👥 Existing Roles:');
        
        try {
            $roles = Role::all();
            
            if ($roles->isEmpty()) {
                $this->line('  No roles found');
            } else {
                $hrdRoles = [];
                $hrdRolesWithoutPrefix = [];
                $otherRoles = [];
                
                foreach ($roles as $role) {
                    if (PermissionHelper::isHrdRole($role->name)) {
                        $hrdRoles[] = $role;
                    } elseif (in_array($role->name, PermissionHelper::getHrdRoles())) {
                        $hrdRolesWithoutPrefix[] = $role;
                    } else {
                        $otherRoles[] = $role;
                    }
                }
                
                if (!empty($hrdRoles)) {
                    $this->line('  ✅ HRD Roles (already with prefix):');
                    foreach ($hrdRoles as $role) {
                        $userCount = $role->users->count();
                        $permCount = $role->permissions->count();
                        $this->line("    🏷️  {$role->name} (users: {$userCount}, permissions: {$permCount})");
                    }
                }
                
                if (!empty($hrdRolesWithoutPrefix)) {
                    $this->line('  🎯 HRD Roles (need prefix migration):');
                    foreach ($hrdRolesWithoutPrefix as $role) {
                        $userCount = $role->users->count();
                        $permCount = $role->permissions->count();
                        $this->line("    📝 {$role->name} → hrd_{$role->name} (users: {$userCount}, permissions: {$permCount})");
                    }
                }
                
                if (!empty($otherRoles)) {
                    $this->line('  ❓ Other Roles (unknown origin):');
                    foreach ($otherRoles as $role) {
                        $userCount = $role->users->count();
                        $permCount = $role->permissions->count();
                        $this->line("    ❓ {$role->name} (users: {$userCount}, permissions: {$permCount})");
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Error checking roles: ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function checkExistingPermissions()
    {
        $this->info('🔐 Existing Permissions:');
        
        try {
            $permissions = Permission::all();
            
            if ($permissions->isEmpty()) {
                $this->line('  No permissions found');
            } else {
                $hrdPermissions = [];
                $hrdPermissionsWithoutPrefix = [];
                $otherPermissions = [];
                
                foreach ($permissions as $permission) {
                    if (PermissionHelper::isHrdPermission($permission->name)) {
                        $hrdPermissions[] = $permission;
                    } elseif (in_array($permission->name, PermissionHelper::getHrdPermissions())) {
                        $hrdPermissionsWithoutPrefix[] = $permission;
                    } else {
                        $otherPermissions[] = $permission;
                    }
                }
                
                if (!empty($hrdPermissions)) {
                    $this->line('  ✅ HRD Permissions (already with prefix):');
                    foreach ($hrdPermissions as $permission) {
                        $roleCount = $permission->roles->count();
                        $userCount = $permission->users->count();
                        $this->line("    🔑 {$permission->name} (roles: {$roleCount}, direct users: {$userCount})");
                    }
                }
                
                if (!empty($hrdPermissionsWithoutPrefix)) {
                    $this->line('  🎯 HRD Permissions (need prefix migration):');
                    foreach ($hrdPermissionsWithoutPrefix as $permission) {
                        $roleCount = $permission->roles->count();
                        $userCount = $permission->users->count();
                        $this->line("    📝 {$permission->name} → hrd_{$permission->name} (roles: {$roleCount}, direct users: {$userCount})");
                    }
                }
                
                if (!empty($otherPermissions)) {
                    $this->line('  ❓ Other Permissions (unknown origin):');
                    foreach ($otherPermissions as $permission) {
                        $roleCount = $permission->roles->count();
                        $userCount = $permission->users->count();
                        $this->line("    ❓ {$permission->name} (roles: {$roleCount}, direct users: {$userCount})");
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Error checking permissions: ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function checkUserAssignments()
    {
        $this->info('👤 User Role Assignments:');
        
        try {
            $usersWithRoles = User::whereHas('roles')->with('roles')->get();
            
            if ($usersWithRoles->isEmpty()) {
                $this->line('  No users with roles found');
            } else {
                $this->line("  Found {$usersWithRoles->count()} users with roles:");
                
                foreach ($usersWithRoles->take(10) as $user) { // Show first 10 users
                    $roleNames = $user->roles->pluck('name')->toArray();
                    $hrdRoles = array_filter($roleNames, function($role) {
                        return PermissionHelper::isHrdRole($role);
                    });
                    $otherRoles = array_filter($roleNames, function($role) {
                        return !PermissionHelper::isHrdRole($role);
                    });
                    
                    $roleInfo = '';
                    if (!empty($hrdRoles)) {
                        $roleInfo .= 'HRD: ' . implode(', ', $hrdRoles);
                    }
                    if (!empty($otherRoles)) {
                        if ($roleInfo) $roleInfo .= ' | ';
                        $roleInfo .= 'Other: ' . implode(', ', $otherRoles);
                    }
                    
                    $this->line("    👤 {$user->name} ({$user->email}): {$roleInfo}");
                }
                
                if ($usersWithRoles->count() > 10) {
                    $this->line("    ... and " . ($usersWithRoles->count() - 10) . " more users");
                }
            }
        } catch (\Exception $e) {
            $this->error('Error checking user assignments: ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function showRecommendations()
    {
        $this->info('💡 Recommendations:');
        
        try {
            $roles = Role::all();
            $permissions = Permission::all();
            
            // Check for potential HRD roles without prefix
            $potentialHrdRoles = $roles->filter(function($role) {
                return !PermissionHelper::isHrdRole($role->name) && 
                       in_array($role->name, PermissionHelper::getHrdRoles());
            });
            
            // Check for potential HRD permissions without prefix
            $potentialHrdPermissions = $permissions->filter(function($permission) {
                return !PermissionHelper::isHrdPermission($permission->name) && 
                       in_array($permission->name, PermissionHelper::getHrdPermissions());
            });
            
            if ($potentialHrdRoles->isNotEmpty() || $potentialHrdPermissions->isNotEmpty()) {
                $this->line('  📋 Found roles/permissions that need prefix migration:');
                
                if ($potentialHrdRoles->isNotEmpty()) {
                    $this->line('    Roles to migrate: ' . $potentialHrdRoles->pluck('name')->implode(', '));
                }
                
                if ($potentialHrdPermissions->isNotEmpty()) {
                    $this->line('    Permissions to migrate: ' . $potentialHrdPermissions->pluck('name')->implode(', '));
                }
                
                $this->newLine();
                $this->line('  🚀 Next steps:');
                $this->line('    1. Run: php artisan db:seed --class=RolesAndPermissionsSeeder');
                $this->line('    2. Run: php artisan hrd:migrate-prefix');
                $this->line('    3. Test your application');
            } else {
                // Check if HRD prefixed roles/permissions exist
                $hrdRoles = $roles->filter(function($role) {
                    return PermissionHelper::isHrdRole($role->name);
                });
                
                $hrdPermissions = $permissions->filter(function($permission) {
                    return PermissionHelper::isHrdPermission($permission->name);
                });
                
                if ($hrdRoles->isEmpty() && $hrdPermissions->isEmpty()) {
                    $this->line('  🆕 No HRD roles/permissions found. Create them with:');
                    $this->line('    php artisan db:seed --class=RolesAndPermissionsSeeder');
                } else {
                    $this->line('  ✅ HRD prefix system is already in place!');
                    $this->line('  📝 Your application should be ready to use the new prefix system.');
                }
            }
            
        } catch (\Exception $e) {
            $this->error('Error generating recommendations: ' . $e->getMessage());
        }
    }
}
