<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class DukExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithCustomValueBinder
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
        $textColumns = ['B', 'G']; // NIP dan NO. KARPEG
        
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
     * Query data DUK - menggunakan VIEW_DUK seperti sistem HRD original
     */
    public function collection()
    {
        // Query yang sama dengan sistem HRD original
        $sql = "SELECT * FROM VIEW_DUK 
                ORDER BY KD_GOL_SEKARANG DESC, eselon DESC, MASA_KERJA_THN DESC, nilaiIndex DESC";

        return collect(DB::connection('sqlsrv')->select($sql));
    }

    /**
     * Headers sesuai dengan yang ada di sistem HRD original (DUK)
     */
    public function headings(): array
    {
        return [
            'No.',
            'NIP',
            'Nama',
            'L/P',
            'Tempat',
            'Tanggal Lahir',
            'No. KARPEG',
            'Pangkat CPNS',
            'Gol. CPNS',
            'TMT CPNS',
            'Pangkat Sekarang',
            'Gol. Sekarang',
            'TMT',
            'MK Tahun',
            'MK Bulan',
            'Eselon',
            'TMT',
            'Pendidikan terakhir',
            'Lulus tahun'
        ];
    }

    /**
     * Mapping data dari database ke array export
     * Sesuai dengan format DUK original
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        // Format tanggal helper - menggunakan format dd-mm-yyyy (sesuai original)
        $formatDate = function($date) {
            if (empty($date)) return '-';
            if (is_string($date)) {
                return date('d-m-Y', strtotime($date));
            }
            if (is_object($date) && method_exists($date, 'format')) {
                return $date->format('d-m-Y');
            }
            return '-';
        };

        // Format nomor sebagai string untuk mencegah scientific notation
        $formatNumber = function($number) {
            if (empty($number)) return '';
            return (string) $number;
        };

        // Konversi jenis kelamin sesuai original: Pria → L, Wanita → P
        $jenisKelamin = '';
        if (isset($row->jenis) || isset($row->jenis_kelamin)) {
            $jenis = $row->jenis ?? $row->jenis_kelamin ?? '';
            if ($jenis == 'Pria') {
                $jenisKelamin = 'L';
            } else if ($jenis == 'Wanita') {
                $jenisKelamin = 'P';
            } else {
                $jenisKelamin = $jenis; // fallback
            }
        }

        // Format nama lengkap dengan gelar
        $namaLengkap = trim(
            ($row->gelar_depan ?? '') . ' ' . 
            ($row->nama ?? '') . ' ' . 
            ($row->gelar_belakang ?? '')
        );

        // Format pendidikan terakhir
        $pendidikanTerakhir = trim(
            ($row->jenjang_didik ?? '') . ' ' . 
            ($row->jurusan ?? '')
        );

        return [
            $no,                                                  // No.
            $formatNumber($row->nip_baru ?? ''),                  // NIP
            $namaLengkap,                                         // Nama
            $jenisKelamin,                                        // L/P
            $row->tempat_lahir ?? '',                             // Tempat
            $formatDate($row->tgl_lahir ?? ''),                   // Tanggal Lahir
            $formatNumber($row->no_karpeg ?? ''),                 // No. KARPEG
            $row->pangkat_masuk ?? '',                            // Pangkat CPNS
            $row->kd_gol_masuk ?? '',                             // Gol. CPNS
            $formatDate($row->tmt_gol_masuk ?? ''),               // TMT CPNS
            $row->pangkat_sekarang ?? '',                         // Pangkat Sekarang
            $row->kd_gol_sekarang ?? '',                          // Gol. Sekarang
            $formatDate($row->tmt_gol_sekarang ?? ''),            // TMT
            $row->masa_kerja_thn ?? '',                           // MK Tahun
            $row->masa_kerja_bulan ?? '',                         // MK Bulan
            $row->eselon ?? '',                                   // Eselon
            $formatDate($row->tmt_eselon ?? ''),                  // TMT
            $pendidikanTerakhir,                                  // Pendidikan terakhir
            $row->tahun_lulus ?? ''                               // Lulus tahun
        ];
    }

    /**
     * Format kolom sebagai text untuk nomor-nomor penting
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,  // NIP
            'G' => NumberFormat::FORMAT_TEXT,  // NO. KARPEG
        ];
    }

    /**
     * Styling untuk header dan content
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 10
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E2E2']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            // Style untuk semua data
            'A2:S1000' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
