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
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class PegawaiNonKesehatanExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithCustomValueBinder, WithEvents
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
        $textColumns = ['A', 'B', 'C', 'G', 'X', 'Y', 'Z', 'AA'];
        
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
     * Query data non kesehatan sesuai dengan sistem HRD original
     * Dengan STR/SIP data (meskipun mungkin kosong untuk non kesehatan)
     * Dengan header NON KESEHATAN
     */
    public function collection()
    {
        $sql = "SELECT k.*, 
                    str.NO_STR, 
                    str.TGL_KADALUARSA as STR_TGL_KADALUARSA, 
                    sip.NO_SIP, 
                    sip.TGL_KADALUARSA as SIP_TGL_KADALUARSA
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
                WHERE k.KD_JENIS_TENAGA = 4 
                  AND k.STATUS_PEG = 1
                ORDER BY k.KD_DETAIL_JENIS_TENAGA ASC, 
                         k.KD_SUB_DETAIL_JENIS_TENAGA ASC, 
                         k.KD_STATUS_KERJA ASC, 
                         k.NAMA ASC";

        $results = collect(DB::connection('sqlsrv')->select($sql));
        
        // Buat collection dengan header NON KESEHATAN
        $collection = collect();
        
        // Tambahkan header row seperti pada file original (menggunakan "PENUNJANG MEDIS" sesuai file asli)
        // Namun akan kita ganti menjadi "NON KESEHATAN" yang lebih sesuai
        $headerRow = (object) [
            'row_type' => 'HEADER',
            'kategori_detail' => 'NON KESEHATAN',
            'nama' => 'NON KESEHATAN'
        ];
        $collection->push($headerRow);
        
        // Tambahkan semua data dengan identifier
        foreach ($results as $row) {
            $row->row_type = 'DATA';
            $collection->push($row);
        }

        return $collection;
    }

    /**
     * Headers sesuai dengan sistem HRD original untuk RS Online
     * 27 kolom termasuk STR/SIP untuk non kesehatan (tidak ada NO ABSEN)
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
     * Dengan handling untuk row header
     */
    public function map($row): array
    {
        // Jika ini adalah row header
        if (isset($row->row_type) && $row->row_type === 'HEADER') {
            $result = array_fill(0, 27, ''); // Buat array 27 kolom kosong
            $result[0] = $row->nama; // Isi kolom pertama dengan "NON KESEHATAN"
            return $result;
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
            $row->jenjang_didik ?? '',                            // JENJANG DIDIK
            $row->jurusan ?? '',                                  // JURUSAN
            $row->tahun_lulus ?? '',                              // TAHUN LULUS
            $formatNumber($row->no_sip ?? ''),                    // NO SIP - dari subquery (mungkin kosong)
            $formatDate($row->sip_tgl_kadaluarsa ?? ''),          // TGL KADALUARSA SIP - dari subquery
            $formatNumber($row->no_str ?? ''),                    // NO STR - dari subquery (mungkin kosong)
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
            'X' => NumberFormat::FORMAT_TEXT,  // NO SIP
            'Y' => NumberFormat::FORMAT_TEXT,  // (reserved)
            'Z' => NumberFormat::FORMAT_TEXT,  // NO STR
            'AA' => NumberFormat::FORMAT_TEXT, // (reserved)
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
     * Register events untuk styling row header NON KESEHATAN
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $this->collection();
                $currentRow = 2; // Mulai dari baris 2 (setelah header)

                foreach ($data as $row) {
                    // Jika ini adalah row header
                    if (isset($row->row_type) && $row->row_type === 'HEADER') {
                        // Merge kolom A sampai AA (27 kolom) untuk row header
                        $sheet->mergeCells("A{$currentRow}:AA{$currentRow}");
                        
                        // Style untuk row header sesuai original (background biru)
                        $sheet->getStyle("A{$currentRow}:AA{$currentRow}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 9
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFB1DEFC'] // Warna biru seperti original
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['argb' => 'FF000000']
                                ]
                            ]
                        ]);
                    }
                    $currentRow++;
                }
            }
        ];
    }
}
