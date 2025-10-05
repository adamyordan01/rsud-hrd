<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignHrdSuperadminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrd:assign-superadmin 
                            {kd_karyawan : Kode karyawan yang akan diberi role superadmin}
                            {--live : Use live database connection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign hrd_superadmin role to specific employee';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $kdKaryawan = $this->argument('kd_karyawan');
        $connection = $this->option('live') ? 'live' : 'sqlsrv';
        
        $this->info("=== ASSIGN HRD SUPERADMIN ROLE ===");
        $this->info("Kode Karyawan: {$kdKaryawan}");
        $this->info("Database: {$connection}");
        $this->newLine();

        try {
            // Cek apakah user ada di users table (gunakan connection yang konsisten)
            $usersConnection = $connection === 'live' ? 'live' : 'default';
            $user = DB::connection($usersConnection)
                ->table('users')
                ->where('kd_karyawan', $kdKaryawan)
                ->first();
            
            if (!$user) {
                $this->error("User dengan kd_karyawan {$kdKaryawan} tidak ditemukan di users table!");
                return 1;
            }

            $this->info("User ditemukan: {$user->name} (ID: {$user->id})");

            // Cek role hrd_superadmin
            $superadminRole = DB::connection($connection)->table('roles')
                ->where('name', 'hrd_superadmin')
                ->first();

            if (!$superadminRole) {
                $this->error("Role 'hrd_superadmin' tidak ditemukan!");
                return 1;
            }

            $this->info("Role hrd_superadmin ditemukan (ID: {$superadminRole->id})");

            // Cek apakah user sudah memiliki role ini
            $existingRole = DB::connection($connection)->table('model_has_roles')
                ->where('role_id', $superadminRole->id)
                ->where('model_type', 'App\Models\User')
                ->where('model_id', $user->id)
                ->first();

            if ($existingRole) {
                $this->warn("User sudah memiliki role hrd_superadmin!");
                return 0;
            }

            // Assign role
            DB::connection($connection)->table('model_has_roles')->insert([
                'role_id' => $superadminRole->id,
                'model_type' => 'App\Models\User',
                'model_id' => $user->id
            ]);

            $this->info("✅ Role hrd_superadmin berhasil di-assign ke {$user->name}!");
            
            // Show current roles
            $this->newLine();
            $this->info("Current roles untuk user ini:");
            $currentRoles = DB::connection($connection)->table('model_has_roles as mhr')
                ->join('roles as r', 'mhr.role_id', '=', 'r.id')
                ->where('mhr.model_type', 'App\Models\User')
                ->where('mhr.model_id', $user->id)
                ->select('r.name')
                ->get();

            foreach ($currentRoles as $role) {
                $this->info("  - {$role->name}");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
