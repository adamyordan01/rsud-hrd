<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionHelper;
use App\Models\User;

class MigrateToHrdPrefix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrd:migrate-prefix {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing HRD roles and permissions to use hrd_ prefix';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Define HRD roles and permissions (without prefix)
        $hrdRoles = PermissionHelper::getHrdRoles();
        $hrdPermissions = PermissionHelper::getHrdPermissions();

        $this->info('📋 Starting HRD prefix migration...');
        $this->newLine();

        // Show current state
        $this->showCurrentState($hrdRoles, $hrdPermissions);

        if (!$isDryRun) {
            if (!$this->confirm('Do you want to proceed with the migration?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        // Migrate permissions
        $this->migratePermissions($hrdPermissions, $isDryRun);
        
        // Migrate roles
        $this->migrateRoles($hrdRoles, $isDryRun);
        
        // Update user assignments
        $this->updateUserAssignments($hrdRoles, $hrdPermissions, $isDryRun);

        $this->newLine();
        $this->info('✅ Migration completed successfully!');
        
        if ($isDryRun) {
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        }

        return 0;
    }

    private function showCurrentState($hrdRoles, $hrdPermissions)
    {
        $this->info('🔍 Current state analysis:');
        
        // Check existing roles
        $existingRoles = Role::whereIn('name', $hrdRoles)->get();
        $existingPrefixedRoles = Role::whereIn('name', array_map(function($role) {
            return PermissionHelper::addRolePrefix($role);
        }, $hrdRoles))->get();

        $this->table(['Type', 'Name', 'Status'], 
            collect($hrdRoles)->map(function($role) use ($existingRoles, $existingPrefixedRoles) {
                $exists = $existingRoles->where('name', $role)->first();
                $prefixedExists = $existingPrefixedRoles->where('name', PermissionHelper::addRolePrefix($role))->first();
                
                $status = 'Not found';
                if ($exists && $prefixedExists) {
                    $status = 'Both exist';
                } elseif ($exists) {
                    $status = 'Needs migration';
                } elseif ($prefixedExists) {
                    $status = 'Already migrated';
                }
                
                return ['Role', $role, $status];
            })->toArray()
        );

        // Check existing permissions
        $existingPermissions = Permission::whereIn('name', $hrdPermissions)->get();
        $existingPrefixedPermissions = Permission::whereIn('name', array_map(function($permission) {
            return PermissionHelper::addPrefix($permission);
        }, $hrdPermissions))->get();

        $this->table(['Type', 'Name', 'Status'], 
            collect($hrdPermissions)->map(function($permission) use ($existingPermissions, $existingPrefixedPermissions) {
                $exists = $existingPermissions->where('name', $permission)->first();
                $prefixedExists = $existingPrefixedPermissions->where('name', PermissionHelper::addPrefix($permission))->first();
                
                $status = 'Not found';
                if ($exists && $prefixedExists) {
                    $status = 'Both exist';
                } elseif ($exists) {
                    $status = 'Needs migration';
                } elseif ($prefixedExists) {
                    $status = 'Already migrated';
                }
                
                return ['Permission', $permission, $status];
            })->toArray()
        );

        $this->newLine();
    }

    private function migratePermissions($hrdPermissions, $isDryRun)
    {
        $this->info('🔄 Migrating permissions...');
        
        foreach ($hrdPermissions as $permission) {
            $originalPermission = Permission::where('name', $permission)->first();
            $prefixedName = PermissionHelper::addPrefix($permission);
            $prefixedPermission = Permission::where('name', $prefixedName)->first();

            if ($originalPermission && !$prefixedPermission) {
                if (!$isDryRun) {
                    // Create new prefixed permission
                    $newPermission = Permission::create([
                        'name' => $prefixedName,
                        'guard_name' => $originalPermission->guard_name
                    ]);
                    
                    // Copy role assignments
                    foreach ($originalPermission->roles as $role) {
                        $newPermission->assignRole($role);
                    }
                    
                    // Copy direct user assignments
                    foreach ($originalPermission->users as $user) {
                        $user->givePermissionTo($newPermission);
                    }
                }
                $this->line("  ✓ Created: {$prefixedName}");
            } elseif ($originalPermission && $prefixedPermission) {
                $this->line("  ⚠ Both exist: {$permission} and {$prefixedName}");
            } elseif (!$originalPermission && $prefixedPermission) {
                $this->line("  ✓ Already migrated: {$prefixedName}");
            } else {
                $this->line("  ⚠ Not found: {$permission}");
            }
        }
    }

    private function migrateRoles($hrdRoles, $isDryRun)
    {
        $this->info('🔄 Migrating roles...');
        
        foreach ($hrdRoles as $role) {
            $originalRole = Role::where('name', $role)->first();
            $prefixedName = PermissionHelper::addRolePrefix($role);
            $prefixedRole = Role::where('name', $prefixedName)->first();

            if ($originalRole && !$prefixedRole) {
                if (!$isDryRun) {
                    // Create new prefixed role
                    $newRole = Role::create([
                        'name' => $prefixedName,
                        'guard_name' => $originalRole->guard_name
                    ]);
                    
                    // Copy permissions (only HRD ones with prefix)
                    foreach ($originalRole->permissions as $permission) {
                        if (in_array($permission->name, PermissionHelper::getHrdPermissions())) {
                            $prefixedPermissionName = PermissionHelper::addPrefix($permission->name);
                            $prefixedPermission = Permission::where('name', $prefixedPermissionName)->first();
                            if ($prefixedPermission) {
                                $newRole->givePermissionTo($prefixedPermission);
                            }
                        }
                    }
                    
                    // Copy user assignments
                    foreach ($originalRole->users as $user) {
                        $user->assignRole($newRole);
                    }
                }
                $this->line("  ✓ Created: {$prefixedName}");
            } elseif ($originalRole && $prefixedRole) {
                $this->line("  ⚠ Both exist: {$role} and {$prefixedName}");
            } elseif (!$originalRole && $prefixedRole) {
                $this->line("  ✓ Already migrated: {$prefixedName}");
            } else {
                $this->line("  ⚠ Not found: {$role}");
            }
        }
    }

    private function updateUserAssignments($hrdRoles, $hrdPermissions, $isDryRun)
    {
        $this->info('🔄 Updating user assignments...');
        
        $usersWithHrdRoles = User::role($hrdRoles)->get();
        $affectedUsersCount = $usersWithHrdRoles->count();
        
        $this->line("  📊 Found {$affectedUsersCount} users with HRD roles");
        
        if (!$isDryRun && $affectedUsersCount > 0) {
            $this->line("  ✓ User assignments will be automatically handled by role migration");
        }
    }
}
