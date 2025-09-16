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

class BniSyariahKontrakExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithCustomValueBinder
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
        $textColumns = ['A', 'B', 'E', 'F'];
        
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
     * Query data pegawai kontrak untuk BNI Syariah
     * KD_STATUS_KERJA = 3 (kontrak), KD_JENIS_TENAGA not in ('1','0') (non-medis), STATUS_PEG = 1 (aktif)
     */
    public function collection()
    {
        // Query sesuai dengan sistem HRD original
        $sql = "SELECT KD_KARYAWAN, 
                       NO_KTP, 
                       GELAR_DEPAN, 
                       NAMA, 
                       GELAR_BELAKANG, 
                       ruangan, 
                       REK_BNI, 
                       REK_BNI_SYARIAH 
                FROM VIEW_TAMPIL_KARYAWAN 
                WHERE KD_STATUS_KERJA = 3 
                AND KD_JENIS_TENAGA NOT IN ('1','0') 
                AND STATUS_PEG = 1 
                ORDER BY KD_KARYAWAN";

        return collect(DB::connection('sqlsrv')->select($sql));
    }

    /**
     * Headers sesuai dengan yang ada di sistem HRD original
     */
    public function headings(): array
    {
        return [
            'ID PEG.',
            'NIK',
            'NAMA',
            'RUANGAN',
            'REK. BNI',
            'REK. BNI SYARIAH'
        ];
    }

    /**
     * Mapping data dari database ke array export
     * Format sesuai dengan sistem HRD original untuk BNI Syariah Kontrak
     */
    public function map($row): array
    {
        // Format nomor sebagai string untuk CustomValueBinder
        $formatNumber = function($number) {
            if (empty($number)) return '';
            return (string) $number;
        };

        // Format nama lengkap dengan gelar
        $namaLengkap = trim(($row->gelar_depan ?? '') . ' ' . ($row->nama ?? '') . ' ' . ($row->gelar_belakang ?? ''));

        return [
            $formatNumber($row->kd_karyawan ?? ''),      // ID PEG.
            $formatNumber($row->no_ktp ?? ''),           // NIK  
            $namaLengkap,                                // NAMA (dengan gelar)
            $row->ruangan ?? '',                         // RUANGAN
            $formatNumber($row->rek_bni ?? ''),          // REK. BNI
            $formatNumber($row->rek_bni_syariah ?? '')   // REK. BNI SYARIAH
        ];
    }

    /**
     * Format kolom sebagai text untuk nomor-nomor penting
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,  // ID PEG.
            'B' => NumberFormat::FORMAT_TEXT,  // NIK
            'E' => NumberFormat::FORMAT_TEXT,  // REK. BNI
            'F' => NumberFormat::FORMAT_TEXT,  // REK. BNI SYARIAH
        ];
    }

    /**
     * Styling untuk header
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
}
