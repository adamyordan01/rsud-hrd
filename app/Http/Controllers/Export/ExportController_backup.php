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
                
            // Export khusus bank
            $kontrakStatusArray = [3, 4, 6]; // Kontrak, Part Time, THL
            $data['bni_syariah_kontrak'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->whereIn('kd_status_kerja', $kontrakStatusArray)
                ->count();
                
            $pnsStatusArray = [1, 2, 7]; // PNS, Honor, PPPK
            $data['bni_syariah_pns'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', $activeStatus)
                ->whereIn('kd_status_kerja', $pnsStatusArray)
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

    // Export DUK (PNS)
    public function exportDuk()
    {
        try {
            return $this->exportData(
                'DUK_Daftar_Urut_Kepangkatan',
                ['status_peg' => $this->getActiveStatus(), 'kd_status_kerja' => 1]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting DUK: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data DUK: ' . $e->getMessage()], 500);
        }
    }

    // Export Pegawai Honor
    public function exportHonor()
    {
        try {
            return $this->exportData(
                'Pegawai_Honor',
                ['status_peg' => $this->getActiveStatus(), 'kd_status_kerja' => 2]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Honor: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Honor: ' . $e->getMessage()], 500);
        }
    }

    // Export Kontrak BLUD
    public function exportKontrakBlud()
    {
        try {
            return $this->exportData(
                'Pegawai_Kontrak_BLUD',
                ['status_peg' => $this->getActiveStatus(), 'kd_status_kerja' => 3, 'kd_jenis_peg' => 2]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Kontrak BLUD: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Kontrak BLUD: ' . $e->getMessage()], 500);
        }
    }

    // Export Kontrak Pemko
    public function exportKontrakPemko()
    {
        try {
            return $this->exportData(
                'Pegawai_Kontrak_Pemko',
                ['status_peg' => $this->getActiveStatus(), 'kd_status_kerja' => 3, 'kd_jenis_peg' => 1]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Kontrak Pemko: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Kontrak Pemko: ' . $e->getMessage()], 500);
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
    public function exportTenagaMedis()
    {
        try {
            return $this->exportData(
                'Tenaga_Medis',
                ['status_peg' => $this->getActiveStatus(), 'kd_jenis_tenaga' => 1]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Tenaga Medis: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Tenaga Medis: ' . $e->getMessage()], 500);
        }
    }

    public function exportPerawatBidan()
    {
        try {
            return $this->exportData(
                'Perawat_Bidan',
                ['status_peg' => $this->getActiveStatus(), 'kd_jenis_tenaga' => 2]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Perawat Bidan: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Perawat Bidan: ' . $e->getMessage()], 500);
        }
    }

    public function exportPenunjangMedis()
    {
        try {
            return $this->exportData(
                'Penunjang_Medis',
                ['status_peg' => $this->getActiveStatus(), 'kd_jenis_tenaga' => 3]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Penunjang Medis: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Penunjang Medis: ' . $e->getMessage()], 500);
        }
    }

    public function exportNonKesehatan()
    {
        try {
            return $this->exportData(
                'Non_Kesehatan',
                ['status_peg' => $this->getActiveStatus(), 'kd_jenis_tenaga' => 4]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Non Kesehatan: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Non Kesehatan: ' . $e->getMessage()], 500);
        }
    }

    // Export pegawai keluar, pensiun, tubel
    public function exportPegawaiKeluar()
    {
        try {
            return $this->exportData(
                'Pegawai_Keluar',
                ['status_peg' => 0]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Keluar: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Pegawai Keluar: ' . $e->getMessage()], 500);
        }
    }

    public function exportPegawaiPensiun()
    {
        try {
            return $this->exportData(
                'Pegawai_Pensiun',
                ['status_peg' => 0, 'kd_alasan_keluar' => 1]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Pensiun: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Pegawai Pensiun: ' . $e->getMessage()], 500);
        }
    }

    public function exportPegawaiTubel()
    {
        try {
            return $this->exportData(
                'Pegawai_Tubel',
                ['status_peg' => 1, 'status_tubel' => 1]
            );
        } catch (\Exception $e) {
            Log::error('Error exporting Pegawai Tubel: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data Pegawai Tubel: ' . $e->getMessage()], 500);
        }
    }

    // Export untuk keperluan bank
    public function exportBniSyariahKontrak()
    {
        try {
            return $this->exportDataWithWhereIn(
                'BNI_Syariah_Kontrak',
                ['status_peg' => $this->getActiveStatus()],
                'kd_status_kerja',
                [3, 4, 6] // Kontrak, Part Time, THL
            );
        } catch (\Exception $e) {
            Log::error('Error exporting BNI Syariah Kontrak: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data BNI Syariah Kontrak: ' . $e->getMessage()], 500);
        }
    }

    public function exportBniSyariahPns()
    {
        try {
            return $this->exportDataWithWhereIn(
                'BNI_Syariah_PNS',
                ['status_peg' => $this->getActiveStatus()],
                'kd_status_kerja',
                [1, 2, 7] // PNS, Honor, PPPK
            );
        } catch (\Exception $e) {
            Log::error('Error exporting BNI Syariah PNS: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal export data BNI Syariah PNS: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi umum untuk export data dengan optimasi memory
    private function exportData($title, $conditions)
    {
        try {
            // Set performance optimizations dengan memory yang lebih besar
            set_time_limit(900); // 15 menit
            ini_set('memory_limit', '3072M'); // 3GB memory
            
            Log::info("Starting export for: $title with conditions: " . json_encode($conditions));
            
            // Query dengan field yang essential saja untuk mengurangi memory
            $query = DB::connection('sqlsrv')
                ->table('hrd_karyawan as k')
                ->select([
                    'k.kd_karyawan as id_peg',
                    'k.nip_lama',
                    'k.nip_baru', 
                    'k.gelar_depan',
                    'k.nama',
                    'k.gelar_belakang',
                    'k.tempat_lahir',
                    'k.tgl_lahir as tanggal_lahir',
                    'k.no_ktp',
                    'k.alamat',
                    'k.tinggi_badan',
                    'k.berat_badan',
                    'k.no_karis',
                    'k.no_karpeg',
                    'k.no_akte',
                    'k.no_askes as no_bpjs_kesehatan',
                    'k.no_taspen',
                    'k.no_npwp',
                    'k.no_kk',
                    'k.nama_ibu_kandung',
                    'k.email',
                    'k.no_hp',
                    'k.no_hp_alternatif',
                    'k.rek_bpd_aceh',
                    'k.rek_bni',
                    'k.rek_bni_syariah',
                    'k.rek_mandiri',
                    'k.tanggungan',
                    'k.kd_gol_masuk as golongan_masuk',
                    'k.tmt_gol_masuk',
                    'k.kd_gol_sekarang as golongan_sekarang',
                    'k.tmt_gol_sekarang',
                    'k.masa_kerja_thn',
                    'k.masa_kerja_bulan',
                    'k.tmt_jab_struk as tmt_jabatan_struktural',
                    'k.tmt_eselon',
                    'k.tmt_jabfung as tmt_jabatan_fungsional',
                    'k.rencana_kp as rkp',
                    'k.kgb',
                    'k.tahun_lulus'
                ]);

            // Apply conditions
            foreach ($conditions as $field => $value) {
                $query->where("k.$field", $value);
            }

            // Gunakan chunking untuk mengurangi memory usage
            $allData = collect();
            $query->orderBy('k.nama')->chunk(500, function($chunk) use ($allData) {
                foreach ($chunk as $item) {
                    $allData->push($item);
                }
                // Force garbage collection
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            });

            Log::info("Total records retrieved: " . $allData->count());
            
            if ($allData->isEmpty()) {
                return response()->json(['error' => 'Tidak ada data untuk diekspor'], 404);
            }
            
            return $this->generateExcelSimple($allData, $title);

        } catch (\Exception $e) {
            Log::error('Export Error for ' . $title . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Gagal mengeksport data: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk export data dengan whereIn (sama dengan exportData tapi dengan whereIn)
    private function exportDataWithWhereIn($title, $conditions, $whereInField, $whereInValues)
    {
        try {
            set_time_limit(900);
            ini_set('memory_limit', '3072M');
            
            Log::info("Starting export for: $title with conditions: " . json_encode($conditions));
            
            $query = DB::connection('sqlsrv')
                ->table('hrd_karyawan as k')
                ->select([
                    'k.kd_karyawan as id_peg',
                    'k.nip_lama',
                    'k.nip_baru', 
                    'k.gelar_depan',
                    'k.nama',
                    'k.gelar_belakang',
                    'k.tempat_lahir',
                    'k.tgl_lahir as tanggal_lahir',
                    'k.no_ktp',
                    'k.alamat',
                    'k.tinggi_badan',
                    'k.berat_badan',
                    'k.no_karis',
                    'k.no_karpeg',
                    'k.no_akte',
                    'k.no_askes as no_bpjs_kesehatan',
                    'k.no_taspen',
                    'k.no_npwp',
                    'k.no_kk',
                    'k.nama_ibu_kandung',
                    'k.email',
                    'k.no_hp',
                    'k.no_hp_alternatif',
                    'k.rek_bpd_aceh',
                    'k.rek_bni',
                    'k.rek_bni_syariah',
                    'k.rek_mandiri',
                    'k.tanggungan',
                    'k.kd_gol_masuk as golongan_masuk',
                    'k.tmt_gol_masuk',
                    'k.kd_gol_sekarang as golongan_sekarang',
                    'k.tmt_gol_sekarang',
                    'k.masa_kerja_thn',
                    'k.masa_kerja_bulan',
                    'k.tmt_jab_struk as tmt_jabatan_struktural',
                    'k.tmt_eselon',
                    'k.tmt_jabfung as tmt_jabatan_fungsional',
                    'k.rencana_kp as rkp',
                    'k.kgb',
                    'k.tahun_lulus'
                ]);

            // Apply conditions
            foreach ($conditions as $field => $value) {
                $query->where("k.$field", $value);
            }

            // Apply whereIn condition
            $query->whereIn("k.$whereInField", $whereInValues);

            // Gunakan chunking
            $allData = collect();
            $query->orderBy('k.nama')->chunk(500, function($chunk) use ($allData) {
                foreach ($chunk as $item) {
                    $allData->push($item);
                }
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            });

            Log::info("Total records retrieved: " . $allData->count());
            
            if ($allData->isEmpty()) {
                return response()->json(['error' => 'Tidak ada data untuk diekspor'], 404);
            }
            
            return $this->generateExcelSimple($allData, $title);

        } catch (\Exception $e) {
            Log::error('Export Error for ' . $title . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Gagal mengeksport data: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi untuk generate Excel yang disederhanakan untuk menghemat memory
    private function generateExcelSimple($data, $title)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set sheet title
        $sheet->setTitle(substr($title, 0, 31));
        
        // Header information
        $sheet->setCellValue('A1', 'RUMAH SAKIT UMUM DAERAH LANGSA');
        $sheet->setCellValue('A2', str_replace('_', ' ', strtoupper($title)));
        $sheet->setCellValue('A3', 'Tanggal Export: ' . date('d/m/Y H:i:s'));
        
        // Header dengan field essential
        $headers = [
            'NO', 'ID PEG.', 'NIP LAMA', 'NIP BARU', 'GELAR DEPAN', 'NAMA', 'GELAR BELAKANG',
            'TEMPAT LAHIR', 'TANGGAL LAHIR', 'NO KTP', 'ALAMAT', 'TINGGI BADAN', 'BERAT BADAN',
            'NO KARIS / KARSU', 'NO KARPEG', 'NO AKTE KELAHIRAN', 'NO ASKES / BPJS KESEHATAN',
            'NO TASPEN', 'NO NPWP', 'NO KK', 'NAMA IBU KANDUNG', 'EMAIL', 'NO HP', 'NO HP ALTERNATIF',
            'NO REKENING BPD ACEH', 'NO REKENING BNI', 'NO REKENING BNI SYARIAH', 'NO REKENING MANDIRI',
            'TANGGUNGAN', 'GOLONGAN MASUK', 'TMT GOLONGAN MASUK', 'GOLONGAN SEKARANG', 'TMT GOLONGAN SEKARANG',
            'MASA KERJA TAHUN', 'MASA KERJA BULAN', 'TMT JABATAN STRUKTURAL', 'TMT ESELON',
            'TMT JABATAN FUNGSIONAL', 'RKP', 'KGB', 'TAHUN LULUS'
        ];
        
        // Merge header cells
        $lastCol = chr(64 + count($headers));
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");
        
        // Set header row
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }
        
        // Fill data
        $row = 6;
        $no = 1;
        foreach ($data as $item) {
            $col = 'A';
            
            $values = [
                $no,
                $item->id_peg ?? '',
                $item->nip_lama ?? '',
                $item->nip_baru ?? '',
                $item->gelar_depan ?? '',
                $item->nama ?? '',
                $item->gelar_belakang ?? '',
                $item->tempat_lahir ?? '',
                $item->tanggal_lahir ? date('d/m/Y', strtotime($item->tanggal_lahir)) : '',
                $item->no_ktp ?? '',
                $item->alamat ?? '',
                $item->tinggi_badan ?? '',
                $item->berat_badan ?? '',
                $item->no_karis ?? '',
                $item->no_karpeg ?? '',
                $item->no_akte ?? '',
                $item->no_bpjs_kesehatan ?? '',
                $item->no_taspen ?? '',
                $item->no_npwp ?? '',
                $item->no_kk ?? '',
                $item->nama_ibu_kandung ?? '',
                $item->email ?? '',
                $item->no_hp ?? '',
                $item->no_hp_alternatif ?? '',
                $item->rek_bpd_aceh ?? '',
                $item->rek_bni ?? '',
                $item->rek_bni_syariah ?? '',
                $item->rek_mandiri ?? '',
                $item->tanggungan ?? '',
                $item->golongan_masuk ?? '',
                $item->tmt_gol_masuk ? date('d/m/Y', strtotime($item->tmt_gol_masuk)) : '',
                $item->golongan_sekarang ?? '',
                $item->tmt_gol_sekarang ? date('d/m/Y', strtotime($item->tmt_gol_sekarang)) : '',
                $item->masa_kerja_thn ?? '',
                $item->masa_kerja_bulan ?? '',
                $item->tmt_jabatan_struktural ? date('d/m/Y', strtotime($item->tmt_jabatan_struktural)) : '',
                $item->tmt_eselon ? date('d/m/Y', strtotime($item->tmt_eselon)) : '',
                $item->tmt_jabatan_fungsional ? date('d/m/Y', strtotime($item->tmt_jabatan_fungsional)) : '',
                $item->rkp ? date('d/m/Y', strtotime($item->rkp)) : '',
                $item->kgb ? date('d/m/Y', strtotime($item->kgb)) : '',
                $item->tahun_lulus ?? ''
            ];
            
            foreach ($values as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            
            $row++;
            $no++;
            
            // Clear memory setiap 100 rows
            if ($no % 100 == 0) {
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }
        }
        
        // Basic styling
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle("A1:{$lastCol}3")->applyFromArray($headerStyle);
        
        $columnHeaderStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0']
            ]
        ];
        $sheet->getStyle("A5:{$lastCol}5")->applyFromArray($columnHeaderStyle);
        
        // Create file
        $writer = new Xlsx($spreadsheet);
        $filename = $title . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = storage_path('app/temp/' . $filename);
        
        // Create temp directory if not exists
        if (!file_exists(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0755, true);
        }
        
        $writer->save($tempFile);
        
        // Clear memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
