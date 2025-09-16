<?php

namespace App\Http\Controllers\Export;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Alternative Export Controller untuk testing performa
 * Berisi 3 metode berbeda: HTML, maatwebsite/excel, PhpSpreadsheet
 */
class ExportControllerAlternative extends Controller
{
    /**
     * METHOD 1: HTML Table Export (TERCEPAT)
     * Menggunakan pendekatan seperti sistem HRD original
     */
    public function exportHTMLFast($type = 'semua')
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Raw SQL query untuk performa maksimal
        $sql = "SELECT TOP 2000 * FROM hrd_karyawan WHERE status_peg = 1";
        $data = DB::connection('sqlsrv')->select($sql);
        
        $filename = "Export_HTML_Fast_" . date('Y-m-d_H-i-s') . ".xls";
        
        // Set headers untuk Excel
        return response()->streamDownload(function() use ($data, $startTime, $startMemory) {
            echo '<style>.str{ mso-number-format:\@; }</style>';
            echo '<table border="1" cellspacing="0" width="100%">';
            echo '<thead style="font-size: 9pt;"><tr>';
            
            // Headers dari attachment yang Anda berikan
            $headers = [
                'ID PEG.', 'NO ABSEN', 'NIP LAMA', 'NIP BARU', 'GELAR DEPAN', 'NAMA', 'GELAR BELAKANG',
                'JENIS KELAMIN', 'TEMPAT LAHIR', 'TANGGAL LAHIR', 'NO KTP', 'ALAMAT', 'KELURAHAN',
                'KECAMATAN', 'KABUPATEN', 'PROVINSI', 'WARNA KULIT', 'TINGGI BADAN', 'BERAT BADAN',
                'GOLONGAN DARAH', 'SUKU', 'AGAMA', 'KEBANGSAAN', 'STATUS NIKAH', 'NO KARIS / KARSU',
                'NO KARPEG', 'NO AKTE KELAHIRAN', 'NO ASKES / BPJS KESEHATAN', 'BPJS KETENAGAKERJAAN',
                'NO TASPEN', 'NO NPWP', 'NO KK', 'NAMA IBU KANDUNG', 'EMAIL', 'NO HP', 'NO HP ALTERNATIF',
                'STATUS RUMAH', 'NO REKENING BPD ACEH', 'NO REKENING BNI', 'NO REKENING BNI SYARIAH',
                'NO REKENING MANDIRI', 'TANGGUNGAN', 'SUB DETAIL JENIS TENAGA', 'DETAIL JENIS TENAGA',
                'JENIS TENAGA', 'RUANGAN', 'SUB UNIT KERJA', 'UNIT KERJA', 'DIVISI', 'STATUS KERJA',
                'JENIS PEGAWAI', 'PANGKAT MASUK', 'GOLONGAN MASUK', 'TMT GOLONGAN MASUK',
                'PANGKAT SEKARANG', 'GOLONGAN SEKARANG', 'TMT GOLONGAN SEKARANG', 'MASA KERJA TAHUN',
                'MASA KERJA BULAN', 'JABATAN STRUKTURAL', 'TMT JABATAN STRUKTURAL', 'ESELON',
                'TMT ESELON', 'JABATAN FUNGSIONAL', 'TMT JABATAN FUNGSIONAL', 'RKP', 'KGB',
                'JENJANG DIDIK', 'JURUSAN', 'TAHUN LULUS', 'NO STR', 'TGL KADALUARSA STR',
                'NO SIP', 'TGL KADALUARSA SIP', 'TMT AKTIF'
            ];
            
            foreach ($headers as $header) {
                echo '<th>' . $header . '</th>';
            }
            echo '</tr></thead><tbody>';
            
            // Generate rows dengan streaming
            foreach ($data as $row) {
                echo '<tr>';
                // Mapping data sesuai field database
                $values = [
                    $row->kd_karyawan ?? '', $row->no_absen ?? '', $row->nip_lama ?? '',
                    $row->nip_baru ?? '', $row->gelar_depan ?? '', $row->nama ?? '',
                    $row->gelar_belakang ?? '', $row->jenis_kelamin ?? '', $row->tempat_lahir ?? '',
                    $row->tgl_lahir ? date('d-m-Y', strtotime($row->tgl_lahir)) : '',
                    $row->no_ktp ?? '', $row->alamat ?? '', $row->kelurahan ?? '',
                    $row->kecamatan ?? '', $row->kabupaten ?? '', $row->propinsi ?? '',
                    $row->kulit ?? '', $row->tinggi_badan ?? '', $row->berat_badan ?? '',
                    $row->goldar ?? '', $row->suku ?? '', $row->agama ?? '',
                    $row->kebangsaan ?? '', $row->status_nikah ?? '', $row->no_karis ?? '',
                    $row->no_karpeg ?? '', $row->no_akte ?? '', $row->no_askes ?? '',
                    $row->no_bpjs_ketenagakerjaan ?? '', $row->no_taspen ?? '',
                    $row->no_npwp ?? '', $row->no_kk ?? '', $row->nama_ibu_kandung ?? '',
                    $row->email ?? '', $row->no_hp ?? '', $row->no_hp_alternatif ?? '',
                    $row->status_rmh ?? '', $row->rek_bpd_aceh ?? '', $row->rek_bni ?? '',
                    $row->rek_bni_syariah ?? '', $row->rek_mandiri ?? '', $row->tanggungan ?? '',
                    '', '', '', '', '', '', '', '', '', '', '', '',
                    '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
                ];
                
                for ($i = 0; $i < count($headers); $i++) {
                    $value = $values[$i] ?? '';
                    // Format nomor sebagai string
                    if (strpos($headers[$i], 'NO ') === 0 || strpos($headers[$i], 'NIP') === 0 || $headers[$i] === 'ID PEG.') {
                        echo '<td class="str">' . htmlspecialchars($value) . '</td>';
                    } else {
                        echo '<td>' . htmlspecialchars($value) . '</td>';
                    }
                }
                echo '</tr>';
                
                // Flush setiap 100 rows
                static $counter = 0;
                if (++$counter % 100 == 0) {
                    flush();
                }
            }
            
            echo '</tbody></table>';
            
            // Performance info
            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);
            $executionTime = round($endTime - $startTime, 2);
            $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2);
            
            echo "<!-- Performance: {$executionTime}s, Memory: {$memoryUsed}MB -->";
            
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Cache-Control' => 'max-age=0'
        ]);
    }
    
    /**
     * METHOD 2: maatwebsite/excel (SEDANG)
     */
    public function exportMaatwebsite($type = 'semua')
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $data = DB::connection('sqlsrv')
            ->table('hrd_karyawan')
            ->where('status_peg', 1)
            ->limit(2000)
            ->get();
        
        return Excel::download(new class($data, $startTime, $startMemory) implements FromCollection, WithHeadings {
            private $data;
            private $startTime;
            private $startMemory;
            
            public function __construct($data, $startTime, $startMemory) {
                $this->data = $data;
                $this->startTime = $startTime;
                $this->startMemory = $startMemory;
            }
            
            public function collection() {
                return $this->data;
            }
            
            public function headings(): array {
                return [
                    'ID PEG.', 'NO ABSEN', 'NIP LAMA', 'NIP BARU', 'NAMA', 
                    'TEMPAT LAHIR', 'TANGGAL LAHIR', 'NO KTP', 'ALAMAT'
                    // Tambahkan headers lainnya sesuai kebutuhan
                ];
            }
        }, 'Export_Maatwebsite_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
    
    /**
     * METHOD 3: PhpSpreadsheet (PALING LAMBAT)
     */
    public function exportPhpSpreadsheet($type = 'semua')
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $data = DB::connection('sqlsrv')
            ->table('hrd_karyawan')
            ->where('status_peg', 1)
            ->limit(2000)
            ->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $headers = ['ID PEG.', 'NO ABSEN', 'NIP LAMA', 'NIP BARU', 'NAMA'];
        $sheet->fromArray([$headers], null, 'A1');
        
        // Data
        $row = 2;
        foreach ($data as $item) {
            $sheet->fromArray([
                $item->kd_karyawan,
                $item->no_absen,
                $item->nip_lama,
                $item->nip_baru,
                $item->nama
            ], null, 'A' . $row);
            $row++;
        }
        
        $filename = 'Export_PhpSpreadsheet_' . date('Y-m-d_H-i-s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    
    /**
     * Test route untuk membandingkan performa
     */
    public function performanceTest()
    {
        return view('exports.performance-test');
    }
}
