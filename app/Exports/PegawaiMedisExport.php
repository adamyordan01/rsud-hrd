<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;

class PegawaiMedisExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithCustomValueBinder, WithEvents
{
    private $bulan;
    private $tahun;

    public function __construct($bulan = null, $tahun = null)
    {
        $this->bulan = $bulan ?? date('m');
        $this->tahun = $tahun ?? date('Y');
    }

    /**
     * Custom Value Binder untuk handle nomor panjang
     */
    public function bindValue(Cell $cell, $value)
    {
        // Daftar kolom yang harus diformat sebagai text (nomor-nomor panjang)
        $textColumns = ['A', 'B', 'C', 'G', 'Y', 'Z', 'AA', 'AB'];
        
        $column = $cell->getColumn();
        
        // Jika kolom ada dalam daftar text columns dan valuenya adalah nomor
        if (in_array($column, $textColumns) && is_numeric($value) && !empty($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // Untuk value lainnya, gunakan default behavior
        return parent::bindValue($cell, $value);
    }

    /**
     * Query data pegawai medis sesuai dengan sistem HRD original
     * Menggabungkan data MAYOR dan MINOR dengan STR/SIP untuk RS Online
     */
    public function collection()
    {
        $sql = "SELECT k.*, 
                    CASE 
                        WHEN LEFT(k.KELOMPOK_SPESIALIS, 1) = '1' THEN 'MAYOR'
                        WHEN LEFT(k.KELOMPOK_SPESIALIS, 1) = '2' THEN 'MINOR'
                        ELSE 'LAINNYA'
                    END as KATEGORI_SPESIALIS,
                    str.NO_STR, 
                    str.TGL_KADALUARSA as STR_TGL_KADALUARSA, 
                    sip.NO_SIP, 
                    sip.TGL_KADALUARSA as SIP_TGL_KADALUARSA,
                    LEFT(k.KELOMPOK_SPESIALIS, 1) as SORT_KATEGORI
                FROM VIEW_TAMPIL_KARYAWAN k
                LEFT JOIN (
                    SELECT KD_KARYAWAN, NO_STR, TGL_KADALUARSA,
                           ROW_NUMBER() OVER (PARTITION BY KD_KARYAWAN ORDER BY TGL_KADALUARSA DESC) as rn
                    FROM HRD_R_STR
                ) str ON k.KD_KARYAWAN = str.KD_KARYAWAN AND str.rn = 1
                LEFT JOIN (
                    SELECT KD_KARYAWAN, NO_SIP, TGL_KADALUARSA,
                           ROW_NUMBER() OVER (PARTITION BY KD_KARYAWAN ORDER BY TGL_KADALUARSA DESC) as rn
                    FROM HRD_R_SIP
                ) sip ON k.KD_KARYAWAN = sip.KD_KARYAWAN AND sip.rn = 1
                WHERE k.KD_JENIS_TENAGA = '1' 
                  AND k.STATUS_PEG = 1
                ORDER BY LEFT(k.KELOMPOK_SPESIALIS, 1) ASC, 
                         k.KELOMPOK_SPESIALIS ASC, 
                         k.KD_STATUS_KERJA ASC, 
                         k.KD_SUB_DETAIL_JENIS_TENAGA ASC, 
                         k.NAMA ASC";

        $results = collect(DB::connection('sqlsrv')->select($sql));
        
        // Group by KATEGORI_SPESIALIS dan tambahkan separator
        $collection = collect();
        $currentKategori = null;
        
        foreach ($results as $row) {
            $kategori = $row->kategori_spesialis;
            
            // Tambahkan separator row jika kategori berubah
            if ($kategori !== $currentKategori) {
                $separatorRow = (object) [
                    'row_type' => 'HEADER',
                    'kategori_spesialis' => $kategori,
                    'nama' => $kategori
                ];
                $collection->push($separatorRow);
                $currentKategori = $kategori;
            }
            
            // Tambahkan data row dengan identifier
            $row->row_type = 'DATA';
            $collection->push($row);
        }

        return $collection;
    }

    /**
     * Headers sesuai dengan sistem HRD original untuk RS Online
     * 28 kolom termasuk STR/SIP untuk tenaga medis
     */
    public function headings(): array
    {
        return [
            'ID PEG.',
            'NIP LAMA',
            'NIP BARU',
            'GELAR DEPAN',
            'NAMA',
            'GELAR BELAKANG',
            'NO KTP',
            'JENIS KELAMIN',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'JENIS TENAGA',
            'RUANGAN',
            'JENIS PEGAWAI',
            'PANGKAT SEKARANG',
            'GOLONGAN SEKARANG',
            'TMT GOLONGAN SEKARANG',
            'MASA KERJA TAHUN',
            'MASA KERJA BULAN',
            'ESELON',
            'TMT ESELON',
            'TMT AKTIF',
            'JENJANG DIDIK',
            'JURUSAN',
            'TAHUN LULUS',
            'NO SIP',
            'TGL KADALUARSA SIP',
            'NO STR',
            'TGL KADALUARSA STR'
        ];
    }

    /**
     * Mapping data dari database ke array export
     * Format sesuai dengan sistem HRD original untuk RS Online
     */
    public function map($row): array
    {
        // Jika ini adalah row separator/header
        if (isset($row->row_type) && $row->row_type === 'HEADER') {
            $separatorRow = [$row->nama];
            // Tambahkan cell kosong untuk 27 kolom lainnya
            for ($i = 1; $i < 28; $i++) {
                $separatorRow[] = '';
            }
            return $separatorRow;
        }

        // Format tanggal helper - menggunakan format d-m-Y seperti original
        $formatDate = function($date) {
            if (empty($date)) return '';
            if (is_string($date)) {
                return date('d-m-Y', strtotime($date));
            }
            if (is_object($date) && method_exists($date, 'format')) {
                return $date->format('d-m-Y');
            }
            return '';
        };

        // Format nomor sebagai string untuk CustomValueBinder
        $formatNumber = function($number) {
            if (empty($number)) return '';
            return (string) $number;
        };

        // Konversi jenis kelamin sesuai original: Wanita→P, Pria→L, empty→?
        $jenisKelamin = $row->jenis_kelamin ?? '';
        if ($jenisKelamin == 'Wanita') {
            $jenisKelamin = 'P';
        } elseif ($jenisKelamin == 'Pria') {
            $jenisKelamin = 'L';
        } elseif (empty($jenisKelamin)) {
            $jenisKelamin = '?';
        }

        return [
            $formatNumber($row->kd_karyawan ?? ''),               // ID PEG.
            $formatNumber($row->nip_lama ?? ''),                  // NIP LAMA
            $formatNumber($row->nip_baru ?? ''),                  // NIP BARU
            $row->gelar_depan ?? '',                              // GELAR DEPAN
            $row->nama ?? '',                                     // NAMA
            $row->gelar_belakang ?? '',                           // GELAR BELAKANG
            $formatNumber($row->no_ktp ?? ''),                    // NO KTP
            $jenisKelamin,                                        // JENIS KELAMIN (converted)
            $row->tempat_lahir ?? '',                             // TEMPAT LAHIR
            $formatDate($row->tgl_lahir ?? ''),                   // TANGGAL LAHIR
            $row->sub_detail ?? '',                               // JENIS TENAGA (sub_detail sesuai original)
            $row->ruangan ?? '',                                  // RUANGAN
            $row->status_kerja ?? '',                             // JENIS PEGAWAI (status_kerja sesuai original)
            $row->pangkat ?? '',                                  // PANGKAT SEKARANG
            $row->kd_gol_sekarang ?? '',                          // GOLONGAN SEKARANG
            $formatDate($row->tmt_gol_sekarang ?? ''),            // TMT GOLONGAN SEKARANG
            $row->masa_kerja_thn ?? '',                           // MASA KERJA TAHUN (nama field sesuai original)
            $row->masa_kerja_bulan ?? '',                         // MASA KERJA BULAN
            $row->eselon ?? '',                                   // ESELON
            $formatDate($row->tmt_eselon ?? ''),                  // TMT ESELON
            $formatDate($row->tgl_keluar_pensiun ?? ''),          // TMT AKTIF
            $row->jenjang_didik ?? '',                            // JENJANG DIDIK
            $row->jurusan ?? '',                                  // JURUSAN
            $row->tahun_lulus ?? '',                              // TAHUN LULUS
            $formatNumber($row->no_sip ?? ''),                    // NO SIP - dari subquery
            $formatDate($row->sip_tgl_kadaluarsa ?? ''),          // TGL KADALUARSA SIP - dari subquery
            $formatNumber($row->no_str ?? ''),                    // NO STR - dari subquery
            $formatDate($row->str_tgl_kadaluarsa ?? '')           // TGL KADALUARSA STR - dari subquery
        ];
    }

    /**
     * Format kolom sebagai text untuk nomor-nomor penting
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,  // ID PEG.
            'B' => NumberFormat::FORMAT_TEXT,  // NIP LAMA
            'C' => NumberFormat::FORMAT_TEXT,  // NIP BARU
            'G' => NumberFormat::FORMAT_TEXT,  // NO KTP
            'Y' => NumberFormat::FORMAT_TEXT,  // NO SIP
            'Z' => NumberFormat::FORMAT_TEXT,  // (reserved)
            'AA' => NumberFormat::FORMAT_TEXT, // NO STR
            'AB' => NumberFormat::FORMAT_TEXT, // (reserved)
        ];
    }

    /**
     * Styling untuk header dan grouping
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 9
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E2E2']
                ]
            ],
        ];
    }

    /**
     * Register events untuk styling row separator MAYOR/MINOR
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $collection = $this->collection();
                
                $rowNumber = 2; // Start from row 2 (after header)
                
                foreach ($collection as $row) {
                    if (isset($row->row_type) && $row->row_type === 'HEADER') {
                        // Style untuk separator row MAYOR/MINOR
                        $sheet->getStyle("A{$rowNumber}:AB{$rowNumber}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 9
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFB1DEFC'] // Warna biru muda seperti original
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
                            ]
                        ]);
                        
                        // Merge cells untuk separator (kolom A sampai kolom ke-28)
                        $sheet->mergeCells("A{$rowNumber}:AB{$rowNumber}");
                    }
                    $rowNumber++;
                }
            },
        ];
    }
}
