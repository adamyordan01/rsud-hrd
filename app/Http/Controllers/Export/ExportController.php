<?php

namespace App\Http\Controllers\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PegawaiAktifExport;
use App\Exports\DukExport;
use App\Exports\PegawaiHonorExport;
use App\Exports\PegawaiKontrakBludExport;
use App\Exports\PegawaiKontrakPemkoExport;
use App\Exports\PegawaiPartTimeExport;
use App\Exports\PegawaiPppkExport;
use App\Exports\PegawaiThlExport;
use App\Exports\PegawaiMedisExport;
use App\Exports\PegawaiPerawatBidanExport;
use App\Exports\PegawaiPenunjangMedisExport;
use App\Exports\PegawaiNonKesehatanExport;
use App\Exports\PegawaiKeluarExport;
use App\Exports\PegawaiPensiunExport;
use App\Exports\PegawaiTubelExport;
use App\Exports\BniSyariahKontrakExport;
use App\Exports\BniSyariahPnsExport;

class ExportController extends Controller
{
    // Helper method untuk mendapatkan status aktif
    private function getActiveStatus()
    {
        static $activeStatus = null;
        
        if ($activeStatus === null) {
            try {
                $activeStatusValues = DB::connection('sqlsrv')
                    ->table('hrd_karyawan')
                    ->whereNotNull('status_peg')
                    ->where('status_peg', '!=', 0)
                    ->pluck('status_peg')
                    ->unique()
                    ->values()
                    ->toArray(); // Convert to array
                
                $activeStatus = !empty($activeStatusValues) ? $activeStatusValues[0] : 1;
                Log::info("Active status determined: " . $activeStatus);
            } catch (\Exception $e) {
                Log::error("Error determining active status: " . $e->getMessage());
                $activeStatus = 1; // Default fallback
            }
        }
        
        return $activeStatus;
    }

    public function index()
    {
        // Initialize data array dengan default values
        $data = [
            'total_aktif' => 0,
            'pns' => 0,
            'duk' => 0,
            'honor' => 0,
            'kontrak_blud' => 0,
            'kontrak_pemko' => 0,
            'part_time' => 0,
            'pppk' => 0,
            'thl' => 0,
            'tenaga_medis' => 0,
            'perawat_bidan' => 0,
            'penunjang_medis' => 0,
            'non_kesehatan' => 0,
            'pegawai_keluar' => 0,
            'pegawai_pensiun' => 0,
            'pegawai_tubel' => 0,
            'bni_syariah_kontrak' => 0,
            'bni_syariah_pns' => 0
        ];
        
        // Hitung data untuk setiap kategori export
        try {
            // Gunakan helper method untuk mendapatkan status aktif
            $activeStatus = $this->getActiveStatus();
            Log::info("Using active status: " . $activeStatus);
            
            // Total PNS (KD_STATUS_KERJA = 1)
            $data['pns'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_status_kerja', 1)
                ->count();
                
            // DUK sama dengan PNS (backward compatibility)
            $data['duk'] = $data['pns'];
                
            // Honor (KD_STATUS_KERJA = 2)
            $data['honor'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_status_kerja', 2)
                ->count();
                
            // Kontrak BLUD (KD_STATUS_KERJA = 3, KD_JENIS_PEG = 2)
            $data['kontrak_blud'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_status_kerja', 3)
                ->where('kd_jenis_peg', 2)
                ->count();
                
            // Kontrak Pemko (KD_STATUS_KERJA = 3, KD_JENIS_PEG = 1)
            $data['kontrak_pemko'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_status_kerja', 3)
                ->where('kd_jenis_peg', 1)
                ->count();
                
            // Part Time (KD_STATUS_KERJA = 4)
            $data['part_time'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_status_kerja', 4)
                ->count();
                
            // PPPK (KD_STATUS_KERJA = 7)
            $data['pppk'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_status_kerja', 7)
                ->count();
                
            // THL (KD_STATUS_KERJA = 6)
            $data['thl'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_status_kerja', 6)
                ->count();
                
            // Total Aktif (semua pegawai aktif)
            $data['total_aktif'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->count();
                
            // Berdasarkan jenis tenaga
            // Tenaga Medis (KD_JENIS_TENAGA = 1)
            $data['tenaga_medis'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_jenis_tenaga', 1)
                ->count();
                
            // Perawat Bidan (KD_JENIS_TENAGA = 2)
            $data['perawat_bidan'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_jenis_tenaga', 2)
                ->count();
                
            // Penunjang Medis (KD_JENIS_TENAGA = 3)
            $data['penunjang_medis'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_jenis_tenaga', 3)
                ->count();
                
            // Non Kesehatan (KD_JENIS_TENAGA = 4)
            $data['non_kesehatan'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->where('kd_jenis_tenaga', 4)
                ->count();
                
            // Pegawai keluar, pensiun, tubel
            try {
                $inactiveStatusesCollection = DB::connection('sqlsrv')
                    ->table('hrd_karyawan')
                    ->where('status_peg', '!=', $activeStatus)
                    ->pluck('status_peg')
                    ->unique()
                    ->values();
                    
                $inactiveStatuses = $inactiveStatusesCollection->toArray();
                
                if (empty($inactiveStatuses) || !is_array($inactiveStatuses)) {
                    $inactiveStatuses = [0, 2, 3, 4]; // Default fallback values
                }
                
                Log::info("Inactive status values: " . json_encode($inactiveStatuses));
            } catch (\Exception $e) {
                Log::error("Error getting inactive statuses: " . $e->getMessage());
                $inactiveStatuses = [0, 2, 3, 4]; // Default fallback values
            }
            
            $data['pegawai_keluar'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->whereIn('status_peg', $inactiveStatuses)
                ->count();
                
            $data['pegawai_pensiun'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', 3) // Berdasarkan project HRD lama
                ->count();
                
            $data['pegawai_tubel'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', 4) // Berdasarkan project HRD lama
                ->count();
                
            // Export khusus bank - sesuai dengan query original
            $data['bni_syariah_kontrak'] = DB::connection('sqlsrv')
                ->table('VIEW_TAMPIL_KARYAWAN')
                ->where('KD_STATUS_KERJA', 3) // Hanya kontrak
                ->whereNotIn('KD_JENIS_TENAGA', ['1','0']) // Non-medis
                ->where('STATUS_PEG', 1) // Aktif
                ->count();
                
            // PNS + Honor + PPPK untuk BNI Syariah - sesuai dengan query original + PPPK
            $data['bni_syariah_pns'] = DB::connection('sqlsrv')
                ->table('VIEW_TAMPIL_KARYAWAN')
                ->whereIn('KD_STATUS_KERJA', ['1','2','7']) // PNS, Honor, PPPK
                ->whereNotIn('KD_JENIS_TENAGA', ['0']) // Semua kecuali tidak aktif
                ->where('STATUS_PEG', 1) // Aktif
                ->count();

            // Log all counts for debugging
            Log::info('Export counts: ', $data);
                
        } catch (\Exception $e) {
            Log::error('Error counting export data: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return view('exports.index', ['exportData' => $data]);
    }

    // Debug function untuk melihat data di browser
    public function debug()
    {
        try {
            // Basic connection test
            $totalTest = DB::connection('sqlsrv')->table('hrd_karyawan')->count();
            
            // Get unique status_peg values
            $statusPegValues = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select('status_peg', DB::raw('COUNT(*) as count'))
                ->groupBy('status_peg')
                ->orderBy('status_peg')
                ->get();
            
            // Get unique kd_status_kerja values
            $statusKerjaValues = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select('kd_status_kerja', DB::raw('COUNT(*) as count'))
                ->groupBy('kd_status_kerja')
                ->orderBy('kd_status_kerja')
                ->get();
            
            // Get unique kd_jenis_tenaga values
            $jenisTenagaValues = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select('kd_jenis_tenaga', DB::raw('COUNT(*) as count'))
                ->groupBy('kd_jenis_tenaga')
                ->orderBy('kd_jenis_tenaga')
                ->get();
            
            // Sample data
            $sampleData = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select('kd_karyawan', 'nama', 'status_peg', 'kd_status_kerja', 'kd_jenis_peg', 'kd_jenis_tenaga')
                ->limit(20)
                ->get();
            
            return response()->json([
                'total_records' => $totalTest,
                'status_peg_distribution' => $statusPegValues,
                'status_kerja_distribution' => $statusKerjaValues,
                'jenis_tenaga_distribution' => $jenisTenagaValues,
                'sample_data' => $sampleData,
                'active_status_detected' => $this->getActiveStatus()
            ], 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database connection failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    // Export Total Aktif (Semua pegawai aktif)
    public function totalAktif()
    {
        try {
            return $this->exportData(
                'Total_Pegawai_Aktif',
                ['status_peg' => $this->getActiveStatus()]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting total aktif: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export Pegawai Aktif menggunakan maatwebsite/excel (NEW METHOD)
     * Dengan semua kolom lengkap seperti sistem HRD original
     */
    public function exportPegawaiAktifMaatwebsite(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai Aktif with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "Pegawai_Aktif_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiAktifExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Aktif with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai Aktif: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export DUK (PNS) menggunakan maatwebsite/excel
    public function exportDuk(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export DUK with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "DUK_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new DukExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting DUK with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data DUK: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export Pegawai Honor menggunakan maatwebsite/excel
    public function exportHonor(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai Honor with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "Pegawai_Honor_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiHonorExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Honor with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai Honor: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export Kontrak BLUD menggunakan maatwebsite/excel
    public function exportKontrakBlud(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai Kontrak BLUD with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "Pegawai_Kontrak_BLUD_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiKontrakBludExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Kontrak BLUD with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai Kontrak BLUD: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export Kontrak Pemko menggunakan maatwebsite/excel
    public function exportKontrakPemko(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai Kontrak Pemko with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "Pegawai_Kontrak_Pemko_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiKontrakPemkoExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Kontrak Pemko with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai Kontrak Pemko: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export Part Time menggunakan maatwebsite/excel  
    public function exportPartTimeMaatwebsite(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai Part Time with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "Pegawai_Part_Time_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiPartTimeExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Part Time with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai Part Time: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export PPPK menggunakan maatwebsite/excel
    public function exportPppkMaatwebsite(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai PPPK with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "Pegawai_PPPK_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiPppkExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai PPPK with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai PPPK: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export THL menggunakan maatwebsite/excel
    public function exportThlMaatwebsite(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai THL with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "Pegawai_THL_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiThlExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai THL with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai THL: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export Part Time
    public function exportPartTime()
    {
        try {
            return $this->exportData(
                'Pegawai_Part_Time',
                ['status_peg' => $this->getActiveStatus(), 'kd_status_kerja' => 4]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Part Time: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Part Time: ' . $e->getMessage()], 500);
        }
    }

    // Export PPPK
    public function exportPppk()
    {
        try {
            return $this->exportData(
                'Pegawai_PPPK',
                ['status_peg' => $this->getActiveStatus(), 'kd_status_kerja' => 7]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting PPPK: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data PPPK: ' . $e->getMessage()], 500);
        }
    }

    // Export THL
    public function exportThl()
    {
        try {
            return $this->exportData(
                'Pegawai_THL',
                ['status_peg' => $this->getActiveStatus(), 'kd_status_kerja' => 6]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting THL: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data THL: ' . $e->getMessage()], 500);
        }
    }

    // Export berdasarkan jenis tenaga
    public function exportTenagaMedis(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai Medis (RS Online) with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "RS_Online_Pegawai_Medis_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiMedisExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Medis with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai Medis: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPerawatBidan(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Perawat-Bidan (RS Online) with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "RS_Online_Perawat_Bidan_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiPerawatBidanExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Perawat-Bidan with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Perawat-Bidan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPenunjangMedis(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Penunjang Medis (RS Online) with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "RS_Online_Penunjang_Medis_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiPenunjangMedisExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Penunjang Medis with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Penunjang Medis: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportNonKesehatan(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Non Kesehatan (RS Online) with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "RS_Online_Non_Kesehatan_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiNonKesehatanExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Non Kesehatan with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Non Kesehatan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export pegawai keluar, pensiun, tubel
    public function exportPegawaiKeluar(Request $request)
    {
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            Log::info("Starting export Pegawai Keluar with maatwebsite/excel for periode: {$bulan}-{$tahun}");
            
            $filename = "Pegawai_Keluar_Periode_{$bulan}-{$tahun}_" . date('Y-m-d_H-i-s');
            
            return Excel::download(
                new PegawaiKeluarExport($bulan, $tahun), 
                $filename . '.xlsx'
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Keluar with maatwebsite/excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export data Pegawai Keluar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Pegawai Pensiun - STATUS_PEG = 3
     */
    public function exportPegawaiPensiun(Request $request)
    {
        try {
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            
            Log::info('Starting Pegawai Pensiun export', [
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);

            $filename = "Data_Pegawai_Pensiun_{$bulan}_{$tahun}.xlsx";
            
            return Excel::download(
                new PegawaiPensiunExport($bulan, $tahun), 
                $filename
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Pensiun: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export Pegawai Tubel - STATUS_PEG = 4
     */
    public function exportPegawaiTubel(Request $request)
    {
        try {
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            
            Log::info('Starting Pegawai Tubel export', [
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);

            $filename = "Data_Pegawai_Tubel_{$bulan}_{$tahun}.xlsx";
            
            return Excel::download(
                new PegawaiTubelExport($bulan, $tahun), 
                $filename
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Tubel: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export BNI Syariah Kontrak - KD_STATUS_KERJA = 3, non-medis, aktif
     */
    public function exportBniSyariahKontrak(Request $request)
    {
        try {
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            
            Log::info('Starting BNI Syariah Kontrak export', [
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);

            $filename = "BNI_Syariah_Kontrak_{$bulan}_{$tahun}.xlsx";
            
            return Excel::download(
                new BniSyariahKontrakExport($bulan, $tahun), 
                $filename
            );
        } catch (\Exception $e) {
            Log::error('Error exporting BNI Syariah Kontrak: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export BNI Syariah PNS - KD_STATUS_KERJA in (1,2,7) - PNS, Honor, PPPK
     */
    public function exportBniSyariahPns(Request $request)
    {
        try {
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
            
            Log::info('Starting BNI Syariah PNS export', [
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);

            $filename = "BNI_Syariah_PNS_PPPK_{$bulan}_{$tahun}.xlsx";
            
            return Excel::download(
                new BniSyariahPnsExport($bulan, $tahun), 
                $filename
            );
        } catch (\Exception $e) {
            Log::error('Error exporting BNI Syariah PNS: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi umum untuk export data dengan optimasi seperti sistem lama
    private function exportData($title, $conditions)
    {
        try {
            // Set performance optimizations minimal seperti sistem lama
            set_time_limit(600); // 10 menit  
            ini_set('memory_limit', '2048M'); // 2GB memory
            
            Log::info("Starting export for: $title with conditions: " . json_encode($conditions));
            
            // Gunakan raw query sederhana seperti sistem lama untuk performa maksimal
            $whereClause = [];
            $params = [];
            
            foreach ($conditions as $field => $value) {
                $whereClause[] = "k.$field = ?";
                $params[] = $value;
            }
            
            $whereString = implode(' AND ', $whereClause);
            
            // Query sederhana tanpa JOIN berlebihan - mirip sistem lama
            $sql = "SELECT 
                k.kd_karyawan as id_peg,
                k.no_absen,
                k.nip_lama,
                k.nip_baru, 
                k.gelar_depan,
                k.nama,
                k.gelar_belakang,
                k.tempat_lahir,
                k.tgl_lahir as tanggal_lahir,
                k.no_ktp,
                k.alamat,
                k.tinggi_badan,
                k.berat_badan,
                k.no_karis,
                k.no_karpeg,
                k.no_akte,
                k.no_askes as no_bpjs_kesehatan,
                k.no_taspen,
                k.no_npwp,
                k.no_kk,
                k.nama_ibu_kandung,
                k.email,
                k.no_hp,
                k.no_hp_alternatif,
                k.rek_bpd_aceh,
                k.rek_bni,
                k.rek_bni_syariah,
                k.rek_mandiri,
                k.tanggungan,
                k.kd_gol_masuk as golongan_masuk,
                k.tmt_gol_masuk,
                k.kd_gol_sekarang as golongan_sekarang,
                k.tmt_gol_sekarang,
                k.masa_kerja_thn,
                k.masa_kerja_bulan,
                k.tmt_jab_struk as tmt_jabatan_struktural,
                k.tmt_eselon,
                k.tmt_jabfung as tmt_jabatan_fungsional,
                k.rencana_kp as rkp,
                k.kgb,
                k.tahun_lulus
            FROM hrd_karyawan k 
            WHERE $whereString 
            ORDER BY k.nama";
            
            // Eksekusi raw query untuk performance maksimal
            $result = DB::connection('sqlsrv')->select($sql, $params);
            
            Log::info("Total records retrieved: " . count($result));
            
            if (empty($result)) {
                return response()->json(['error' => 'Tidak ada data untuk diekspor'], 404);
            }
            
            // Gunakan simple Excel export seperti sistem lama
            return $this->generateExcelFast($result, $title);

        } catch (\Exception $e) {
            Log::error('Export Error for ' . $title . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Gagal mengeksport data: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk export data dengan whereIn - optimasi seperti sistem lama
    private function exportDataWithWhereIn($title, $conditions, $whereInField, $whereInValues)
    {
        try {
            set_time_limit(600);
            ini_set('memory_limit', '2048M');
            
            Log::info("Starting export for: $title with conditions: " . json_encode($conditions));
            
            // Build where clause
            $whereClause = [];
            $params = [];
            
            foreach ($conditions as $field => $value) {
                $whereClause[] = "k.$field = ?";
                $params[] = $value;
            }
            
            // Add whereIn clause
            $placeholders = str_repeat('?,', count($whereInValues) - 1) . '?';
            $whereClause[] = "k.$whereInField IN ($placeholders)";
            $params = array_merge($params, $whereInValues);
            
            $whereString = implode(' AND ', $whereClause);
            
            // Query sederhana seperti sistem lama
            $sql = "SELECT 
                k.kd_karyawan as id_peg,
                k.no_absen,
                k.nip_lama,
                k.nip_baru, 
                k.gelar_depan,
                k.nama,
                k.gelar_belakang,
                k.tempat_lahir,
                k.tgl_lahir as tanggal_lahir,
                k.no_ktp,
                k.alamat,
                k.tinggi_badan,
                k.berat_badan,
                k.no_karis,
                k.no_karpeg,
                k.no_akte,
                k.no_askes as no_bpjs_kesehatan,
                k.no_taspen,
                k.no_npwp,
                k.no_kk,
                k.nama_ibu_kandung,
                k.email,
                k.no_hp,
                k.no_hp_alternatif,
                k.rek_bpd_aceh,
                k.rek_bni,
                k.rek_bni_syariah,
                k.rek_mandiri,
                k.tanggungan,
                k.kd_gol_masuk as golongan_masuk,
                k.tmt_gol_masuk,
                k.kd_gol_sekarang as golongan_sekarang,
                k.tmt_gol_sekarang,
                k.masa_kerja_thn,
                k.masa_kerja_bulan,
                k.tmt_jab_struk as tmt_jabatan_struktural,
                k.tmt_eselon,
                k.tmt_jabfung as tmt_jabatan_fungsional,
                k.rencana_kp as rkp,
                k.kgb,
                k.tahun_lulus
            FROM hrd_karyawan k 
            WHERE $whereString 
            ORDER BY k.nama";
            
            $result = DB::connection('sqlsrv')->select($sql, $params);
            
            Log::info("Total records retrieved: " . count($result));
            
            if (empty($result)) {
                return response()->json(['error' => 'Tidak ada data untuk diekspor'], 404);
            }
            
            return $this->generateExcelFast($result, $title);

        } catch (\Exception $e) {
            Log::error('Export Error for ' . $title . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Gagal mengeksport data: ' . $e->getMessage()], 500);
        }
    }

    // Method export Excel yang cepat seperti sistem lama (HTML table as Excel)
    private function generateExcelFast($data, $title)
    {
        $filename = $title . '_' . date('Y-m-d_H-i-s') . '.xls';
        
        // Set headers untuk Excel download seperti sistem lama
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Style untuk format Excel
        echo '<style> .str{ mso-number-format:\@; } </style>';
        echo '<table border=1 cellspacing="0" width="100%">';
        
        // Header table
        echo '<thead style="font-size: 9pt;">
                <tr>
                    <th>NO</th>
                    <th>ID PEG.</th>
                    <th>NO ABSEN</th>
                    <th>NIP LAMA</th>
                    <th>NIP BARU</th>
                    <th>GELAR DEPAN</th>
                    <th>NAMA</th>
                    <th>GELAR BELAKANG</th>
                    <th>TEMPAT LAHIR</th>
                    <th>TANGGAL LAHIR</th>
                    <th>NO KTP</th>
                    <th>ALAMAT</th>
                    <th>TINGGI BADAN</th>
                    <th>BERAT BADAN</th>
                    <th>NO KARIS / KARSU</th>
                    <th>NO KARPEG</th>
                    <th>NO AKTE KELAHIRAN</th>
                    <th>NO ASKES / BPJS KESEHATAN</th>
                    <th>NO TASPEN</th>
                    <th>NO NPWP</th>
                    <th>NO KK</th>
                    <th>NAMA IBU KANDUNG</th>
                    <th>EMAIL</th>
                    <th>NO HP</th>
                    <th>NO HP ALTERNATIF</th>
                    <th>NO REKENING BPD ACEH</th>
                    <th>NO REKENING BNI</th>
                    <th>NO REKENING BNI SYARIAH</th>
                    <th>NO REKENING MANDIRI</th>
                    <th>TANGGUNGAN</th>
                    <th>GOLONGAN MASUK</th>
                    <th>TMT GOLONGAN MASUK</th>
                    <th>GOLONGAN SEKARANG</th>
                    <th>TMT GOLONGAN SEKARANG</th>
                    <th>MASA KERJA TAHUN</th>
                    <th>MASA KERJA BULAN</th>
                    <th>TMT JABATAN STRUKTURAL</th>
                    <th>TMT ESELON</th>
                    <th>TMT JABATAN FUNGSIONAL</th>
                    <th>RKP</th>
                    <th>KGB</th>
                    <th>TAHUN LULUS</th>
                </tr>
              </thead>
              <tbody>';
        
        // Output data secara streaming seperti sistem lama
        $no = 1;
        foreach ($data as $item) {
            echo "<tr>
                <td>$no</td>
                <td class='str'>" . ($item->id_peg ?? '') . "</td>
                <td class='str'>" . ($item->no_absen ?? '') . "</td>
                <td class='str'>" . ($item->nip_lama ?? '') . "</td>
                <td class='str'>" . ($item->nip_baru ?? '') . "</td>
                <td>" . ($item->gelar_depan ?? '') . "</td>
                <td>" . ($item->nama ?? '') . "</td>
                <td>" . ($item->gelar_belakang ?? '') . "</td>
                <td>" . ($item->tempat_lahir ?? '') . "</td>
                <td>" . ($item->tanggal_lahir ? date('d-m-Y', strtotime($item->tanggal_lahir)) : '') . "</td>
                <td class='str'>" . ($item->no_ktp ?? '') . "</td>
                <td>" . ($item->alamat ?? '') . "</td>
                <td>" . ($item->tinggi_badan ?? '') . "</td>
                <td>" . ($item->berat_badan ?? '') . "</td>
                <td class='str'>" . ($item->no_karis ?? '') . "</td>
                <td class='str'>" . ($item->no_karpeg ?? '') . "</td>
                <td class='str'>" . ($item->no_akte ?? '') . "</td>
                <td class='str'>" . ($item->no_bpjs_kesehatan ?? '') . "</td>
                <td class='str'>" . ($item->no_taspen ?? '') . "</td>
                <td class='str'>" . ($item->no_npwp ?? '') . "</td>
                <td class='str'>" . ($item->no_kk ?? '') . "</td>
                <td>" . ($item->nama_ibu_kandung ?? '') . "</td>
                <td>" . ($item->email ?? '') . "</td>
                <td class='str'>" . ($item->no_hp ?? '') . "</td>
                <td class='str'>" . ($item->no_hp_alternatif ?? '') . "</td>
                <td class='str'>" . ($item->rek_bpd_aceh ?? '') . "</td>
                <td class='str'>" . ($item->rek_bni ?? '') . "</td>
                <td class='str'>" . ($item->rek_bni_syariah ?? '') . "</td>
                <td class='str'>" . ($item->rek_mandiri ?? '') . "</td>
                <td>" . ($item->tanggungan ?? '') . "</td>
                <td>" . ($item->golongan_masuk ?? '') . "</td>
                <td>" . ($item->tmt_gol_masuk ? date('d-m-Y', strtotime($item->tmt_gol_masuk)) : '') . "</td>
                <td>" . ($item->golongan_sekarang ?? '') . "</td>
                <td>" . ($item->tmt_gol_sekarang ? date('d-m-Y', strtotime($item->tmt_gol_sekarang)) : '') . "</td>
                <td>" . ($item->masa_kerja_thn ?? '') . "</td>
                <td>" . ($item->masa_kerja_bulan ?? '') . "</td>
                <td>" . ($item->tmt_jabatan_struktural ? date('d-m-Y', strtotime($item->tmt_jabatan_struktural)) : '') . "</td>
                <td>" . ($item->tmt_eselon ? date('d-m-Y', strtotime($item->tmt_eselon)) : '') . "</td>
                <td>" . ($item->tmt_jabatan_fungsional ? date('d-m-Y', strtotime($item->tmt_jabatan_fungsional)) : '') . "</td>
                <td>" . ($item->rkp ? date('d-m-Y', strtotime($item->rkp)) : '') . "</td>
                <td>" . ($item->kgb ? date('d-m-Y', strtotime($item->kgb)) : '') . "</td>
                <td>" . ($item->tahun_lulus ?? '') . "</td>
            </tr>";
            
            $no++;
            
            // Flush output setiap 100 rows untuk streaming
            if ($no % 100 == 0) {
                ob_flush();
                flush();
            }
        }
        
        echo '</tbody></table>';
        
        // Force download dan close seperti sistem lama
        echo '<script>
            setTimeout(function() {
                window.close();
            }, 100);
        </script>';
        
        exit(); // Stop execution untuk langsung download
    }
}
