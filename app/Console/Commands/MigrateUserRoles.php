<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use App\Helpers\PermissionHelper;
use App\Models\User;

class MigrateUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrd:migrate-user-roles {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate users from old HRD roles to prefixed HRD roles';

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

        $this->info('👥 Migrating user role assignments...');
        $this->newLine();

        $hrdRoles = PermissionHelper::getHrdRoles();
        $usersProcessed = 0;
        $rolesAssigned = 0;

        foreach ($hrdRoles as $roleName) {
            // Cari role lama
            $oldRole = Role::where('name', $roleName)->first();
            if (!$oldRole) {
                $this->line("  ⚠️  Old role not found: {$roleName}");
                continue;
            }

            // Cari role baru dengan prefix
            $newRoleName = PermissionHelper::addRolePrefix($roleName);
            $newRole = Role::where('name', $newRoleName)->first();
            if (!$newRole) {
                $this->line("  ❌ New role not found: {$newRoleName}");
                continue;
            }

            // Ambil users yang memiliki role lama
            $users = $oldRole->users;
            
            if ($users->isEmpty()) {
                $this->line("  ℹ️  No users found for role: {$roleName}");
                continue;
            }

            $this->line("  🔄 Processing role: {$roleName} → {$newRoleName}");
            
            foreach ($users as $user) {
                if (!$isDryRun) {
                    // Assign role baru
                    $user->assignRole($newRole);
                    
                    // Remove role lama
                    $user->removeRole($oldRole);
                }
                
                $this->line("    👤 {$user->name} ({$user->email})");
                $usersProcessed++;
                $rolesAssigned++;
            }
        }

        $this->newLine();
        
        if ($isDryRun) {
            $this->info("📊 Summary (DRY RUN):");
            $this->line("  Users to be processed: {$usersProcessed}");
            $this->line("  Role assignments to be made: {$rolesAssigned}");
            $this->newLine();
            $this->warn("This was a dry run. Run without --dry-run to apply changes.");
        } else {
            $this->info("✅ Migration completed:");
            $this->line("  Users processed: {$usersProcessed}");
            $this->line("  Role assignments made: {$rolesAssigned}");
            $this->newLine();
            $this->info("Users now have prefixed HRD roles!");
        }

        return 0;
    }
}
