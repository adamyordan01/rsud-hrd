<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignHrdPegawaiBiasaToExisting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrd:assign-pegawai-biasa-existing 
                            {--live : Use live database connection}
                            {--dry-run : Show what would be assigned without actually doing it}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign hrd_pegawai_biasa role to all existing users without HRD roles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $connection = $this->option('live') ? 'live' : 'sqlsrv';
        $usersConnection = $connection === 'live' ? 'live' : 'default';
        $isDryRun = $this->option('dry-run');
        
        $this->info('=== ASSIGN HRD_PEGAWAI_BIASA TO EXISTING USERS ===');
        $this->info("Database connection: {$connection}");
        $this->info("Users table: {$usersConnection}");
        $this->info("Mode: " . ($isDryRun ? 'DRY RUN' : 'LIVE'));
        $this->newLine();

        try {
            // Get hrd_pegawai_biasa role
            $hrdPegawaiBiasaRole = DB::connection($connection)
                ->table('roles')
                ->where('name', 'hrd_pegawai_biasa')
                ->first();

            if (!$hrdPegawaiBiasaRole) {
                $this->error('Role "hrd_pegawai_biasa" tidak ditemukan!');
                return 1;
            }

            $this->info("Role hrd_pegawai_biasa ditemukan (ID: {$hrdPegawaiBiasaRole->id})");

            // Get all users
            $allUsers = DB::connection($usersConnection)
                ->table('users')
                ->select('id', 'name', 'kd_karyawan')
                ->orderBy('id')
                ->get();

            $this->info("Total users ditemukan: {$allUsers->count()}");

            // Get users yang sudah punya role HRD (hrd_*)
            $usersWithHrdRoles = DB::connection($connection)
                ->table('model_has_roles as mhr')
                ->join('roles as r', 'mhr.role_id', '=', 'r.id')
                ->where('mhr.model_type', 'App\Models\User')
                ->where('r.name', 'like', 'hrd_%')
                ->pluck('mhr.model_id')
                ->unique()
                ->toArray();

            $this->info("Users dengan role HRD: " . count($usersWithHrdRoles));

            // Filter users yang belum punya role HRD
            $usersNeedRole = $allUsers->whereNotIn('id', $usersWithHrdRoles);
            $this->info("Users yang perlu role hrd_pegawai_biasa: {$usersNeedRole->count()}");
            $this->newLine();

            if ($usersNeedRole->count() === 0) {
                $this->info('✅ Semua user sudah memiliki role HRD.');
                return 0;
            }

            // Show preview
            $this->info('Preview users yang akan diberi role:');
            $headers = ['User ID', 'Kode Karyawan', 'Nama', 'Current HRD Roles'];
            $previewData = [];
            
            foreach ($usersNeedRole->take(10) as $user) {
                $currentRoles = DB::connection($connection)
                    ->table('model_has_roles as mhr')
                    ->join('roles as r', 'mhr.role_id', '=', 'r.id')
                    ->where('mhr.model_type', 'App\Models\User')
                    ->where('mhr.model_id', $user->id)
                    ->where('r.name', 'like', 'hrd_%')
                    ->pluck('r.name')
                    ->implode(', ');

                $previewData[] = [
                    $user->id,
                    $user->kd_karyawan ?: 'NULL',
                    $user->name,
                    $currentRoles ?: 'NONE'
                ];
            }
            
            $this->table($headers, $previewData);
            
            if ($usersNeedRole->count() > 10) {
                $this->info("... dan " . ($usersNeedRole->count() - 10) . " user lainnya");
            }
            $this->newLine();

            if ($isDryRun) {
                $this->info('=== DRY RUN - TIDAK ADA DATA YANG DISIMPAN ===');
                return 0;
            }

            // Confirmation
            if (!$this->option('force')) {
                if (!$this->confirm("Assign role hrd_pegawai_biasa ke {$usersNeedRole->count()} users?")) {
                    $this->info('Assignment dibatalkan.');
                    return 0;
                }
            }

            // Start assignment
            $this->info('Memulai assignment...');
            $progressBar = $this->output->createProgressBar($usersNeedRole->count());
            $progressBar->start();

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($usersNeedRole as $user) {
                try {
                    // Check if already has this specific role
                    $existingRole = DB::connection($connection)
                        ->table('model_has_roles')
                        ->where('role_id', $hrdPegawaiBiasaRole->id)
                        ->where('model_type', 'App\Models\User')
                        ->where('model_id', $user->id)
                        ->first();

                    if (!$existingRole) {
                        DB::connection($connection)->table('model_has_roles')->insert([
                            'role_id' => $hrdPegawaiBiasaRole->id,
                            'model_type' => 'App\Models\User',
                            'model_id' => $user->id
                        ]);
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'error' => $e->getMessage()
                    ];
                }
                
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Show results
            $this->info('=== HASIL ASSIGNMENT ===');
            $this->info("✅ Berhasil: {$successCount}");
            
            if ($errorCount > 0) {
                $this->error("❌ Gagal: {$errorCount}");
                $this->newLine();
                foreach ($errors as $error) {
                    $this->error("- User ID {$error['user_id']} ({$error['name']}): {$error['error']}");
                }
            }

            $this->newLine();
            $this->info('✅ Assignment hrd_pegawai_biasa selesai!');

            return 0;

        } catch (\Exception $e) {
            $this->error('Error during assignment: ' . $e->getMessage());
            return 1;
        }
    }
}
