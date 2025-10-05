<?php

namespace App\Jobs;

use Mpdf\Mpdf;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Queue\SerializesModels;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Intervention\Image\ImageManagerStatic as Image;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class ProcessTteSkBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes per job
    public $tries = 3;
    public $maxExceptions = 3;

    protected $batchId;
    protected $karyawanList;
    protected $passphrase;
    protected $tglTtd;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($batchId, $karyawanList, $passphrase, $tglTtd)
    {
        $this->batchId = $batchId;
        $this->karyawanList = $karyawanList;
        $this->passphrase = $passphrase;
        $this->tglTtd = $tglTtd;
        
        // Set connection untuk queue
        $this->onConnection('database_mysql');
        $this->onQueue('sk_tte');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Starting TTE batch processing', [
            'batch_id' => $this->batchId,
            'karyawan_count' => count($this->karyawanList)
        ]);

        try {
            // Update batch status to processing
            $this->updateBatchStatus('processing');

            foreach ($this->karyawanList as $karyawan) {
                $this->processSingleKaryawan($karyawan);
                
                // Rate limiting: delay 12 seconds between requests
                // This allows ~5 requests per minute to avoid overwhelming TTE server
                sleep(12);
            }

            // Update batch status to completed
            $this->updateBatchStatus('completed');
            
            Log::info('TTE batch processing completed', ['batch_id' => $this->batchId]);

        } catch (\Exception $e) {
            Log::error('TTE batch processing failed', [
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->updateBatchStatus('failed');
            throw $e;
        }
    }

    private function processSingleKaryawan($karyawan)
    {
        try {
            // Update current processing status
            $this->updateCurrentProcessing($karyawan['kd_karyawan'], $karyawan['nama']);
            
            // Update progress record to processing
            $this->updateProgressStatus($karyawan['kd_karyawan'], 'processing');

            // Get batch info
            $batch = DB::connection('mysql_queue')
                ->table('sk_batch_process')
                ->where('id', $this->batchId)
                ->first();

            // Generate and process TTE for this karyawan
            $result = $this->generateAndProcessTte($batch->urut, $batch->tahun_sk, $karyawan['kd_karyawan']);

            if ($result['success']) {
                // Update progress to success
                $this->updateProgressStatus($karyawan['kd_karyawan'], 'success', null, $result['id_dokumen'], $result['path_dokumen']);
                
                // Update main database
                $this->updateMainDatabase($batch->urut, $batch->tahun_sk, $karyawan['kd_karyawan'], $result['id_dokumen']);
                
                // Increment success count
                $this->incrementCounter('success_count');
                
                Log::info('TTE success for karyawan', [
                    'batch_id' => $this->batchId,
                    'kd_karyawan' => $karyawan['kd_karyawan'],
                    'id_dokumen' => $result['id_dokumen']
                ]);
            } else {
                // Update progress to failed
                $this->updateProgressStatus($karyawan['kd_karyawan'], 'failed', $result['error']);
                
                // Increment failed count  
                $this->incrementCounter('failed_count');
                
                Log::error('TTE failed for karyawan', [
                    'batch_id' => $this->batchId,
                    'kd_karyawan' => $karyawan['kd_karyawan'],
                    'error' => $result['error']
                ]);
            }

            // Increment processed count
            $this->incrementCounter('processed_count');

        } catch (\Exception $e) {
            Log::error('Error processing karyawan', [
                'batch_id' => $this->batchId,
                'kd_karyawan' => $karyawan['kd_karyawan'],
                'error' => $e->getMessage()
            ]);
            
            $this->updateProgressStatus($karyawan['kd_karyawan'], 'failed', $e->getMessage());
            $this->incrementCounter('failed_count');
            $this->incrementCounter('processed_count');
        }
    }

    private function generateAndProcessTte($urut, $tahun, $kdKaryawan)
    {
        try {
            // Generate PDF using existing logic from SKController
            $logo = public_path('assets/media/rsud-langsa/logo-putih.png');
            $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');
            $logoEsign = public_path('assets/media/rsud-langsa/e-sign.png');

            $getSk = DB::table('hrd_sk_pegawai_kontrak as sk')
                ->join('view_tampil_karyawan as vk', 'sk.kd_karyawan', '=', 'vk.kd_karyawan')
                ->select(
                    'vk.kd_karyawan', 'vk.gelar_depan', 'vk.nama', 'vk.gelar_belakang', 
                    'vk.tempat_lahir', 'vk.tgl_lahir', 'vk.jenis_kelamin', 'vk.jenjang_didik', 
                    'vk.jurusan', 'sk.tahun_sk', 'sk.tgl_sk', 'sk.no_sk', 'sk.tgl_ttd'
                )
                ->where('sk.urut', $urut)
                ->where('sk.tahun_sk', $tahun)
                ->where('sk.kd_karyawan', $kdKaryawan)
                ->first();

            if (!$getSk) {
                return ['success' => false, 'error' => 'Data SK tidak ditemukan'];
            }

            $getDirektur = DB::table('view_tampil_karyawan as vk')
                ->join('hrd_golongan as g', 'vk.kd_gol_sekarang', '=', 'g.kd_gol')
                ->select('vk.*', 'g.alias_gol as golongan')
                ->where('vk.kd_jabatan_struktural', 1)
                ->where('vk.status_peg', 1)
                ->first();

            // Generate QR Code
            $PNG_WEB_DIR = storage_path('app/public/qr-code/');
            $imgName = "{$getSk->no_sk}-{$getSk->tahun_sk}-{$getSk->kd_karyawan}.png";
            $link = "https://e-rsud.langsakota.go.id/hrd/cek-data.php?data=" . md5($getSk->kd_karyawan) . "&thn={$getSk->tahun_sk}";
            $this->generateQrCode($link, $PNG_WEB_DIR . $imgName, $logo);

            // Generate PDF filename
            $fileName = 'SK_Pegawai_Kontrak_' . $tahun . '_' . $urut . '_' . $kdKaryawan . '.pdf';
            $pdfFilePath = 'sk-documents/' . $tahun . '/' . $fileName;

            // Ensure directory exists
            if (!Storage::disk('hrd_files')->exists('sk-documents/' . $tahun)) {
                Storage::disk('hrd_files')->makeDirectory('sk-documents/' . $tahun);
            }

            // Generate PDF
            $data = [
                'results' => [$getSk],
                'direktur' => $getDirektur,
                'tahun' => $tahun,
                'logo' => $logo,
                'logoLangsa' => $logoLangsa,
                'logoEsign' => $logoEsign,
            ];

            $pdf = PDF::loadView('sk.sk-pegawai-kontrak', $data, [], [
                'format' => [215, 330],
                'orientation' => 'P',
                'margin_top' => 5,
                'margin_right' => 15,
                'margin_bottom' => 15,
                'margin_left' => 15,
                'margin_header' => 5,
                'margin_footer' => 5,
                'default_font_size' => 11,
                'default_font' => 'bookman-old-style',
                'custom_font_dir' => base_path('public/assets/fonts/'),
                'custom_font_data' => [
                    'bookman-old-style' => [
                        'R' => 'Bookman Old Style Regular.ttf',
                        'B' => 'Bookman Old Style Bold.ttf',
                        'I' => 'Bookman Old Style Italic.ttf',
                        'BI' => 'Bookman Old Style Bold Italic.ttf'
                    ]
                ]
            ]);

            // Save PDF to disk
            $pdfOutput = $pdf->output();
            Storage::disk('hrd_files')->put($pdfFilePath, $pdfOutput);

            // Send to TTE server
            $tteResult = $this->sendPdfForSignature($pdfFilePath, $this->passphrase);

            if ($tteResult['success']) {
                // Download signed document
                $downloadResult = $this->downloadSignedDocument($tteResult['id_dokumen'], $tahun);
                
                if ($downloadResult['success']) {
                    // Clean up temporary file
                    if (Storage::disk('hrd_files')->exists($pdfFilePath)) {
                        Storage::disk('hrd_files')->delete($pdfFilePath);
                    }
                    
                    return [
                        'success' => true,
                        'id_dokumen' => $tteResult['id_dokumen'],
                        'path_dokumen' => $downloadResult['path_dokumen']
                    ];
                } else {
                    return ['success' => false, 'error' => 'Gagal download dokumen yang sudah ditandatangani'];
                }
            } else {
                // Clean up temporary file on failure
                if (Storage::disk('hrd_files')->exists($pdfFilePath)) {
                    Storage::disk('hrd_files')->delete($pdfFilePath);
                }
                return ['success' => false, 'error' => $tteResult['error']];
            }

        } catch (\Exception $e) {
            Log::error('Error in generateAndProcessTte', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function sendPdfForSignature($pdfFilePath, $passphrase)
    {
        $endpoint = "http://123.108.100.83:85/api/sign/pdf";
        $client = new Client();

        try {
            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Basic ZXNpZ246cXdlcnR5'
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => Storage::disk('hrd_files')->get($pdfFilePath),
                        'filename' => basename($pdfFilePath)
                    ],
                    [
                        'name' => 'nik',
                        'contents' => '1271022205700001'
                    ],
                    [
                        'name' => 'passphrase',
                        'contents' => $passphrase
                    ],
                    [
                        'name' => 'tampilan',
                        'contents' => 'invisible'
                    ],
                ],
                'timeout' => 120,
            ]);

            $headers = $response->getHeaders();
            $id_dokumen = $headers['id_dokumen'][0] ?? null;

            if ($response->getStatusCode() == 200 && !empty($id_dokumen)) {
                return ['success' => true, 'id_dokumen' => $id_dokumen];
            } else {
                return ['success' => false, 'error' => 'TTE server response error'];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'TTE request failed: ' . $e->getMessage()];
        }
    }

    private function downloadSignedDocument($idDokumen, $tahun)
    {
        $endpoint = "http://123.108.100.83:85/api/sign/download/" . $idDokumen;
        $filename = 'SK_Pegawai_Kontrak_TTE_' . $idDokumen . '.pdf';
        $filePath = 'sk-documents/' . $tahun . '/' . $filename;

        try {
            $client = new Client();
            $response = $client->request('GET', $endpoint, [
                'headers' => [
                    'Authorization' => 'Basic ZXNpZ246cXdlcnR5'
                ]
            ]);

            Storage::disk('hrd_files')->put($filePath, $response->getBody()->getContents());
            
            return ['success' => true, 'path_dokumen' => $filePath];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Download failed: ' . $e->getMessage()];
        }
    }

    private function generateQrCode($data, $path, $logo)
    {
        $writer = new PngWriter();
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(350)
            ->setMargin(10)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);

        $qrImage = Image::make($result->getString());
        $logoImage = Image::make($logo)->resize(120, 120);

        $qrImage->insert($logoImage, 'center');
        $qrImage->save($path);
    }

    private function updateBatchStatus($status)
    {
        $updateData = ['status' => $status];
        
        if ($status == 'processing') {
            $updateData['started_at'] = now();
        } elseif (in_array($status, ['completed', 'failed'])) {
            $updateData['completed_at'] = now();
        }

        DB::connection('mysql_queue')
            ->table('sk_batch_process')
            ->where('id', $this->batchId)
            ->update($updateData);
    }

    private function updateCurrentProcessing($kdKaryawan, $namaKaryawan)
    {
        DB::connection('mysql_queue')
            ->table('sk_batch_process')
            ->where('id', $this->batchId)
            ->update([
                'current_karyawan_id' => $kdKaryawan,
                'current_karyawan_name' => $namaKaryawan
            ]);
    }

    private function updateProgressStatus($kdKaryawan, $status, $errorMessage = null, $idDokumen = null, $pathDokumen = null)
    {
        $updateData = ['status' => $status];
        
        if ($status == 'processing') {
            $updateData['processed_at'] = now();
        } elseif ($status == 'failed') {
            $updateData['error_message'] = $errorMessage;
        } elseif ($status == 'success') {
            $updateData['id_dokumen'] = $idDokumen;
            $updateData['path_dokumen'] = $pathDokumen;
        }

        DB::connection('mysql_queue')
            ->table('sk_tte_progress')
            ->where('batch_id', $this->batchId)
            ->where('kd_karyawan', $kdKaryawan)
            ->update($updateData);
    }

    private function incrementCounter($counterName)
    {
        DB::connection('mysql_queue')
            ->table('sk_batch_process')
            ->where('id', $this->batchId)
            ->increment($counterName);
    }

    private function updateMainDatabase($urut, $tahun, $kdKaryawan, $idDokumen)
    {
        // Update main SQL Server database
        DB::table('hrd_sk_pegawai_kontrak')
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->where('kd_karyawan', $kdKaryawan)
            ->update([
                'tgl_ttd' => $this->tglTtd,
                'id_dokumen' => $idDokumen,
                'verif_4' => 1,
                'kd_karyawan_verif_4' => auth()->user()->kd_karyawan ?? 'system'
            ]);
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('TTE batch job failed permanently', [
            'batch_id' => $this->batchId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Update batch status to failed
        $this->updateBatchStatus('failed');
        
        // Mark all pending progress as failed
        DB::connection('mysql_queue')
            ->table('sk_tte_progress')
            ->where('batch_id', $this->batchId)
            ->where('status', 'pending')
            ->update([
                'status' => 'failed',
                'error_message' => 'Job failed permanently: ' . $exception->getMessage()
            ]);
    }
}
