<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupSkTemporaryFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sk:cleanup-temporary {--hours=24 : Hours old for file to be considered for cleanup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup temporary SK files that are older than specified hours';

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
        $hoursThreshold = $this->option('hours');
        $this->info("Starting cleanup of SK temporary files older than {$hoursThreshold} hours...");
        
        try {
            $currentYear = date('Y');
            $skDocumentsPath = 'sk-documents/' . $currentYear;
            
            if (!Storage::disk('hrd_files')->exists($skDocumentsPath)) {
                $this->warn('SK documents directory not found: ' . $skDocumentsPath);
                return Command::SUCCESS;
            }

            $files = Storage::disk('hrd_files')->allFiles($skDocumentsPath);
            $deletedCount = 0;
            $checkedCount = 0;
            
            foreach ($files as $filePath) {
                $fileName = basename($filePath);
                
                // Hanya proses file temporary SK dengan pattern yang benar
                if (preg_match('/^SK_Pegawai_Kontrak_\d{4}_\d+_\d+\.pdf$/', $fileName)) {
                    $checkedCount++;
                    
                    // Cek umur file
                    $fileLastModified = Storage::disk('hrd_files')->lastModified($filePath);
                    $hoursOld = (time() - $fileLastModified) / 3600;
                    
                    if ($hoursOld > $hoursThreshold) {
                        // Cek apakah sudah ada versi TTE yang tersimpan
                        $fileNameParts = explode('_', str_replace('.pdf', '', $fileName));
                        if (count($fileNameParts) >= 5) {
                            $tahun = $fileNameParts[3];
                            $urut = $fileNameParts[4];
                            
                            $hasSignedVersion = DB::table('hrd_sk_pegawai_kontrak')
                                ->where('tahun_sk', $tahun)
                                ->where('urut', $urut)
                                ->whereNotNull('path_dokumen')
                                ->where('path_dokumen', '!=', $filePath)
                                ->exists();
                                
                            if ($hasSignedVersion) {
                                Storage::disk('hrd_files')->delete($filePath);
                                $deletedCount++;
                                
                                $this->info("Deleted: {$fileName} (age: " . round($hoursOld, 1) . " hours)");
                                
                                Log::info('SK temporary file cleaned up via command', [
                                    'file_path' => $filePath,
                                    'file_name' => $fileName,
                                    'hours_old' => round($hoursOld, 2),
                                    'command' => 'sk:cleanup-temporary'
                                ]);
                            } else {
                                $this->warn("Skipped: {$fileName} (no signed version found)");
                            }
                        }
                    } else {
                        $this->line("Skipped: {$fileName} (age: " . round($hoursOld, 1) . " hours - too young)");
                    }
                }
            }
            
            $this->info("Cleanup completed!");
            $this->info("Files checked: {$checkedCount}");
            $this->info("Files deleted: {$deletedCount}");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Error during cleanup: ' . $e->getMessage());
            Log::error('Error in SK cleanup command: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
