<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupKaryawanData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:karyawan 
                            {--month= : Bulan backup (1-12)}
                            {--year= : Tahun backup}
                            {--force : Force backup meskipun sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup data karyawan ke tabel HRD_KARYAWAN_BACKUP';

    protected $backupService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== BACKUP DATA KARYAWAN ===');
        $this->info('Starting backup process...');

        // Get parameters
        $month = $this->option('month');
        $year = $this->option('year');
        $force = $this->option('force');

        // Validate month if provided
        if ($month && ($month < 1 || $month > 12)) {
            $this->error('Bulan harus antara 1-12');
            return 1;
        }

        // Validate year if provided
        if ($year && ($year < 2000 || $year > date('Y') + 1)) {
            $this->error('Tahun tidak valid');
            return 1;
        }

        // Get backup requirement info
        $requirement = $this->backupService->getBackupRequirement();
        
        if (!$month || !$year) {
            $month = $requirement['required_month'];
            $year = $requirement['required_year'];
            $this->info("Menggunakan periode default: {$requirement['required_month_name']} {$year}");
        }

        // Check if backup already exists
        if (!$force) {
            $monthFormatted = sprintf("%02d", $month);
            $exists = $this->backupService->checkBackupExists($monthFormatted, $year);
            
            if ($exists > 0) {
                $this->warn("Backup untuk periode {$monthFormatted}-{$year} sudah ada ({$exists} records)");
                
                if (!$this->confirm('Apakah Anda ingin melanjutkan backup ulang?')) {
                    $this->info('Backup dibatalkan');
                    return 0;
                }
            }
        }

        // Show confirmation
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $this->info("Periode backup: {$monthNames[$month]} {$year}");
        
        if (!$this->confirm('Lanjutkan proses backup?')) {
            $this->info('Backup dibatalkan');
            return 0;
        }

        // Start backup process with progress bar
        $this->info('Memulai proses backup...');
        
        $progressBar = $this->output->createProgressBar(3);
        $progressBar->setFormat('verbose');
        
        $progressBar->start();
        $progressBar->setMessage('Checking existing data...');
        $progressBar->advance();
        
        $progressBar->setMessage('Backing up data...');
        $result = $this->backupService->backupMonthlyData($month, $year);
        $progressBar->advance();
        
        $progressBar->setMessage('Verifying backup...');
        $progressBar->advance();
        
        $progressBar->finish();
        $this->newLine(2);

        // Show results
        if ($result['success']) {
            $this->info('✅ BACKUP BERHASIL!');
            $this->info("📊 Total records yang dibackup: {$result['backup_count']}");
            $this->info("📅 Waktu backup: {$result['backup_time']}");
            
            if (isset($result['total_original'])) {
                $this->info("📋 Total data original: {$result['total_original']}");
            }
            
            return 0;
        } else {
            $this->error('❌ BACKUP GAGAL!');
            $this->error("Error: {$result['message']}");
            
            if (isset($result['error'])) {
                $this->error("Detail: {$result['error']}");
            }
            
            return 1;
        }
    }
}