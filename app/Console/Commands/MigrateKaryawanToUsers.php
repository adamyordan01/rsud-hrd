<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MigrateKaryawanToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'karyawan:migrate-to-users 
                            {--live : Use live database connection}
                            {--dry-run : Show what would be migrated without actually doing it}
                            {--force : Skip confirmation prompt}
                            {--model-type=App\Models\User : Model type to use for roles (default: App\Models\User)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate active employees from hrd_karyawan to users table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== MIGRASI DATA KARYAWAN KE USERS TABLE ===');
        $this->newLine();

        // Determine which database connection to use
        $connection = $this->option('live') ? 'live' : 'sqlsrv';
        $isDryRun = $this->option('dry-run');
        $modelType = $this->option('model-type');
        
        $this->info("Menggunakan koneksi database: {$connection}");
        $this->info("Mode: " . ($isDryRun ? 'DRY RUN (tidak akan menyimpan data)' : 'LIVE (akan menyimpan data)'));
        $this->info("Model Type untuk roles: {$modelType}");
        $this->newLine();

        try {
            // Get active employees from hrd_karyawan
            $karyawans = DB::connection($connection)
                ->table('hrd_karyawan')
                ->select([
                    'kd_karyawan',
                    'gelar_depan',
                    'nama',
                    'gelar_belakang',
                    'email',
                    'tgl_lahir',
                    'status_peg'
                ])
                ->where('status_peg', 1)
                ->orderBy('kd_karyawan')
                ->get();

            $totalKaryawan = $karyawans->count();
            $this->info("Ditemukan {$totalKaryawan} karyawan aktif untuk dimigrasi");
            $this->newLine();

            if ($totalKaryawan === 0) {
                $this->warn('Tidak ada data karyawan aktif yang ditemukan.');
                return 0;
            }

            // Show preview of first 5 records
            $this->info('Preview 5 data pertama:');
            $headers = ['Kode', 'Nama Lengkap', 'Email', 'Password Pattern', 'Roles'];
            $previewData = [];
            
            foreach ($karyawans->take(5) as $karyawan) {
                $namaLengkap = $this->buildFullName($karyawan);
                $passwordPattern = $this->generatePasswordPattern($karyawan);
                $email = $this->sanitizeEmail($karyawan->email);
                
                // Determine roles
                $roles = 'hrd_pegawai_biasa';
                if ($karyawan->kd_karyawan === '001635') {
                    $roles .= ', hrd_superadmin';
                }
                
                $previewData[] = [
                    $karyawan->kd_karyawan,
                    $namaLengkap,
                    $email ?: 'NULL',
                    $passwordPattern,
                    $roles
                ];
            }
            
            $this->table($headers, $previewData);
            $this->newLine();

            // Check for existing users (gunakan connection yang sama)
            $existingUsers = DB::connection($connection === 'live' ? 'live' : 'default')
                ->table('users')
                ->whereIn('kd_karyawan', $karyawans->pluck('kd_karyawan'))
                ->pluck('kd_karyawan')
                ->toArray();

            $newRecords = $karyawans->whereNotIn('kd_karyawan', $existingUsers);
            $duplicateCount = count($existingUsers);
            $newCount = $newRecords->count();

            $this->info("Status analisis:");
            $this->info("- Total karyawan aktif: {$totalKaryawan}");
            $this->info("- Sudah ada di users table: {$duplicateCount}");
            $this->info("- Yang akan ditambahkan: {$newCount}");
            $this->newLine();

            if ($duplicateCount > 0) {
                $this->warn("Ditemukan {$duplicateCount} karyawan yang sudah ada di users table (akan dilewati)");
                $this->newLine();
            }

            if ($newCount === 0) {
                $this->info('Semua karyawan aktif sudah ada di users table.');
                return 0;
            }

            // Confirmation (skip if force option is used or dry-run)
            if (!$this->option('force') && !$isDryRun) {
                if (!$this->confirm("Apakah Anda yakin ingin melanjutkan migrasi {$newCount} karyawan?")) {
                    $this->info('Migrasi dibatalkan.');
                    return 0;
                }
            }

            if ($isDryRun) {
                $this->info('=== DRY RUN - TIDAK ADA DATA YANG DISIMPAN ===');
                $this->showMigrationSummary($newRecords);
                return 0;
            }

            // Get role IDs
            $hrdPegawaiBiasaRoleId = DB::connection($connection)->table('roles')
                ->where('name', 'hrd_pegawai_biasa')
                ->value('id');
            
            $hrdSuperadminRoleId = DB::connection($connection)->table('roles')
                ->where('name', 'hrd_superadmin')
                ->value('id');

            if (!$hrdPegawaiBiasaRoleId) {
                $this->error('Role "hrd_pegawai_biasa" tidak ditemukan!');
                return 1;
            }

            if (!$hrdSuperadminRoleId) {
                $this->error('Role "hrd_superadmin" tidak ditemukan!');
                return 1;
            }

            $this->info("Role ID - hrd_pegawai_biasa: {$hrdPegawaiBiasaRoleId}");
            $this->info("Role ID - hrd_superadmin: {$hrdSuperadminRoleId}");
            $this->newLine();

            // Start migration
            $this->info('Memulai proses migrasi...');
            $progressBar = $this->output->createProgressBar($newCount);
            $progressBar->start();

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($newRecords as $karyawan) {
                try {
                    $userData = $this->prepareUserData($karyawan);
                    
                    // Insert user (gunakan connection yang sama dengan source data)
                    $userId = DB::connection($connection === 'live' ? 'live' : 'default')
                        ->table('users')
                        ->insertGetId($userData);
                    
                    // Assign default role (hrd_pegawai_biasa) to all users
                    // Menggunakan model_type khusus untuk HRD agar tidak konflik dengan aplikasi lain
                    DB::table('model_has_roles')->insert([
                        'role_id' => $hrdPegawaiBiasaRoleId,
                        'model_type' => $modelType,
                        'model_id' => $userId
                    ]);
                    
                    // Assign hrd_superadmin role to specific user (001635)
                    if ($karyawan->kd_karyawan === '001635') {
                        DB::table('model_has_roles')->insert([
                            'role_id' => $hrdSuperadminRoleId,
                            'model_type' => $modelType,
                            'model_id' => $userId
                        ]);
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'kd_karyawan' => $karyawan->kd_karyawan,
                        'nama' => $this->buildFullName($karyawan),
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error("Error migrating karyawan {$karyawan->kd_karyawan}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Show results
            $this->info('=== HASIL MIGRASI ===');
            $this->info("✅ Berhasil: {$successCount}");
            
            if ($errorCount > 0) {
                $this->error("❌ Gagal: {$errorCount}");
                $this->newLine();
                $this->error('Detail error:');
                foreach ($errors as $error) {
                    $this->error("- {$error['kd_karyawan']} ({$error['nama']}): {$error['error']}");
                }
            }

            $this->newLine();
            $this->info('=== INFORMASI PASSWORD ===');
            $this->info('Password pattern yang digunakan: [NamaDepan][DDMMYYYY]');
            $this->info('Contoh: Jika nama "Ahmad Rizki" lahir 15/08/1990, password = "Ahmad15081990"');
            $this->info('');
            $this->info('Untuk karyawan tanpa tanggal lahir, password = "rsud2024"');
            $this->newLine();
            
            $this->info('=== INFORMASI ROLES ===');
            $this->info('Setiap user yang dimigrasi akan mendapat role:');
            $this->info('- hrd_pegawai_biasa (semua user)');
            $this->info('- hrd_superadmin (khusus kd_karyawan: 001635)');
            $this->info("Model Type: {$modelType}");
            $this->newLine();
            
            $this->info('KEAMANAN: Roles menggunakan prefix hrd_ untuk menghindari');
            $this->info('konflik dengan aplikasi lain. Tabel users terpisah per aplikasi.');
            $this->newLine();

            Log::info("Karyawan migration completed", [
                'total_processed' => $newCount,
                'successful' => $successCount,
                'failed' => $errorCount,
                'connection' => $connection
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Error during migration: ' . $e->getMessage());
            Log::error('Karyawan migration failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Build full name from karyawan data
     */
    private function buildFullName($karyawan)
    {
        $gelarDepan = trim($karyawan->gelar_depan ?? '');
        $nama = trim($karyawan->nama);
        $gelarBelakang = trim($karyawan->gelar_belakang ?? '');
        
        // Handle gelar depan
        if (!empty($gelarDepan)) {
            // Ensure gelar_depan ends with appropriate punctuation if needed
            if (!preg_match('/[.,]$/', $gelarDepan)) {
                $gelarDepan = $gelarDepan . '.';
            }
        }
        
        // Handle gelar belakang - clean up format
        if (!empty($gelarBelakang)) {
            // Remove any leading/trailing spaces or commas
            $gelarBelakang = trim($gelarBelakang, ' ,');
            
            // Add proper comma prefix if not already present
            if (!empty($gelarBelakang)) {
                $gelarBelakang = ', ' . $gelarBelakang;
            }
        }
        
        // Build full name
        $parts = [];
        
        if (!empty($gelarDepan)) {
            $parts[] = $gelarDepan;
        }
        
        $parts[] = $nama;
        
        if (!empty($gelarBelakang)) {
            // For gelar belakang, we append directly to avoid extra space
            $fullName = implode(' ', $parts) . $gelarBelakang;
        } else {
            $fullName = implode(' ', $parts);
        }
        
        return trim($fullName);
    }

    /**
     * Sanitize email field
     */
    private function sanitizeEmail($email)
    {
        $email = trim($email ?? '');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        
        return strtolower($email);
    }

    /**
     * Generate password pattern for preview
     */
    private function generatePasswordPattern($karyawan)
    {
        $namaDepan = $this->extractFirstName($karyawan);
        
        if ($karyawan->tgl_lahir && $karyawan->tgl_lahir !== '1900-01-01') {
            try {
                $tanggalLahir = Carbon::parse($karyawan->tgl_lahir);
                return $namaDepan . $tanggalLahir->format('dmY');
            } catch (\Exception $e) {
                return 'rsud2024';
            }
        }
        
        return 'rsud2024';
    }

    /**
     * Extract first name for password generation
     * Handle various name formats with/without titles
     */
    private function extractFirstName($karyawan)
    {
        $nama = trim($karyawan->nama);
        
        // Split nama by spaces and get first meaningful name
        $namaParts = explode(' ', $nama);
        
        // Get first part that's not empty
        $namaDepan = '';
        foreach ($namaParts as $part) {
            $part = trim($part);
            if (!empty($part)) {
                $namaDepan = $part;
                break;
            }
        }
        
        // Clean up the name part (remove any remaining punctuation)
        $namaDepan = preg_replace('/[^a-zA-Z]/', '', $namaDepan);
        
        // Capitalize first letter
        $namaDepan = ucfirst(strtolower($namaDepan));
        
        // If result is empty or too short, use default
        if (empty($namaDepan) || strlen($namaDepan) < 2) {
            return 'User';
        }
        
        return $namaDepan;
    }

    /**
     * Generate actual password
     */
    private function generatePassword($karyawan)
    {
        $pattern = $this->generatePasswordPattern($karyawan);
        return Hash::make($pattern);
    }

    /**
     * Prepare user data for insertion
     */
    private function prepareUserData($karyawan)
    {
        $now = Carbon::now();
        
        return [
            'kd_karyawan' => $karyawan->kd_karyawan,
            'name' => $this->buildFullName($karyawan),
            'email' => $this->sanitizeEmail($karyawan->email),
            'email_verified_at' => null,
            'password' => $this->generatePassword($karyawan),
            'remember_token' => null,
            'created_at' => $now,
            'updated_at' => $now,
            'is_active' => ($karyawan->status_peg == 1) ? 1 : 0,
        ];
    }

    /**
     * Show migration summary for dry run
     */
    private function showMigrationSummary($records)
    {
        $this->info('Data yang akan dimigrasi:');
        $this->newLine();
        
        $headers = ['Kode', 'Nama Lengkap', 'Email', 'Password Pattern', 'Roles'];
        $tableData = [];
        
        foreach ($records->take(10) as $karyawan) {
            // Determine roles
            $roles = 'hrd_pegawai_biasa';
            if ($karyawan->kd_karyawan === '001635') {
                $roles .= ', hrd_superadmin';
            }
            
            $tableData[] = [
                $karyawan->kd_karyawan,
                $this->buildFullName($karyawan),
                $this->sanitizeEmail($karyawan->email) ?: 'NULL',
                $this->generatePasswordPattern($karyawan),
                $roles
            ];
        }
        
        $this->table($headers, $tableData);
        
        if ($records->count() > 10) {
            $this->info("... dan " . ($records->count() - 10) . " karyawan lainnya");
        }
    }
}
