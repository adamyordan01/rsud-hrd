<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanupMutasiTemporaryFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mutasi:cleanup-temporary-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup temporary mutasi nota files after TTE completion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of temporary mutasi nota files...');
        
        try {
            $cleanedCount = 0;
            $year = date('Y');
            
            // Ambil data mutasi yang sudah selesai TTE (ada path_dokumen dan id_dokumen)
            $completedMutasi = DB::table('hrd_r_mutasi')
                ->whereNotNull('id_dokumen')
                ->whereNotNull('path_dokumen')
                ->whereRaw('YEAR(tgl_dibuat) = ?', [$year])
                ->get(['kd_mutasi', 'kd_karyawan', 'id_dokumen']);

            $this->info("Found " . $completedMutasi->count() . " completed mutasi records for year {$year}");

            foreach ($completedMutasi as $mutasi) {
                // File temporary yang akan dihapus
                $tempFile = 'mutasi-documents/' . $year . '/Nota_Tugas_Mutasi_' . $year . '_' . $mutasi->kd_mutasi . '_' . $mutasi->kd_karyawan . '.pdf';
                
                if (Storage::disk('hrd_files')->exists($tempFile)) {
                    Storage::disk('hrd_files')->delete($tempFile);
                    $cleanedCount++;
                    $this->line("Deleted: {$tempFile}");
                }
            }

            // Cleanup QR code files yang sudah tidak diperlukan
            $qrPath = 'public/qr-code-mutasi-nota/' . $year;
            if (Storage::exists($qrPath)) {
                $qrFiles = Storage::files($qrPath);
                foreach ($qrFiles as $qrFile) {
                    $fileName = basename($qrFile, '.png');
                    $parts = explode('-', $fileName);
                    if (count($parts) >= 2) {
                        $kdMutasi = $parts[0];
                        $kdKaryawan = $parts[1];
                        
                        // Cek apakah mutasi sudah selesai TTE
                        $completed = DB::table('hrd_r_mutasi')
                            ->where('kd_mutasi', $kdMutasi)
                            ->where('kd_karyawan', $kdKaryawan)
                            ->whereNotNull('id_dokumen')
                            ->whereNotNull('path_dokumen')
                            ->exists();
                            
                        if ($completed) {
                            Storage::delete($qrFile);
                            $cleanedCount++;
                            $this->line("Deleted QR: {$qrFile}");
                        }
                    }
                }
            }

            $this->info("Cleanup completed! Removed {$cleanedCount} temporary files.");
            
            // Log hasil cleanup
            Log::info("Mutasi nota temporary files cleanup completed", [
                'files_cleaned' => $cleanedCount,
                'year' => $year,
                'timestamp' => now()
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error during cleanup: ' . $e->getMessage());
            Log::error('Mutasi nota cleanup error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
