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

class PegawaiTubelExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithCustomValueBinder
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
        $textColumns = ['A', 'B', 'C', 'D', 'K', 'X', 'Y', 'Z', 'AB', 'AC', 'AF', 'AG', 'AH'];
        
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
     * Query data pegawai tubel - menggunakan raw SQL seperti sistem HRD original
     * STATUS_PEG = 4 untuk pegawai tugas belajar
     */
    public function collection()
    {
        // Query pegawai tubel sesuai original sistem HRD
        $sql = "SELECT * FROM VIEW_TAMPIL_KARYAWAN 
                WHERE STATUS_PEG = 4 
                ORDER BY KD_KARYAWAN";

        return collect(DB::connection('sqlsrv')->select($sql));
    }

    /**
     * Headers sesuai dengan yang ada di sistem HRD original untuk pegawai tubel
     * 68 kolom termasuk TMT TUBEL
     */
    public function headings(): array
    {
        return [
            'ID PEG.',
            'NO ABSEN',
            'NIP LAMA',
            'NIP BARU',
            'GELAR DEPAN',
            'NAMA',
            'GELAR BELAKANG',
            'JENIS KELAMIN',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'NO KTP',
            'ALAMAT',
            'KELURAHAN',
            'KECAMATAN',
            'KABUPATEN',
            'PROVINSI',
            'WARNA KULIT',
            'TINGGI BADAN',
            'BERAT BADAN',
            'GOLONGAN DARAH',
            'SUKU',
            'AGAMA',
            'KEBANGSAAN',
            'STATUS NIKAH',
            'NO KARIS / KARSU',
            'NO KARPEG',
            'NO AKTE KELAHIRAN',
            'NO ASKES / BPJS',
            'NO TASPEN',
            'NO NPWP',
            'NO KK',
            'NAMA IBU KANDUNG',
            'EMAIL',
            'NO HP',
            'NO HP ALTERNATIF',
            'STATUS RUMAH',
            'NO REKENING BPD ACEH',
            'NO REKENING BNI',
            'NO REKENING MANDIRI',
            'TANGGUNGAN',
            'SUB DETAIL JENIS TENAGA',
            'DETAIL JENIS TENAGA',
            'JENIS TENAGA',
            'RUANGAN',
            'SUB UNIT KERJA',
            'UNIT KERJA',
            'DIVISI',
            'STATUS KERJA',
            'JENIS PEGAWAI',
            'PANGKAT MASUK',
            'GOLONGAN MASUK',
            'TMT GOLONGAN MASUK',
            'PANGKAT SEKARANG',
            'GOLONGAN SEKARANG',
            'TMT GOLONGAN SEKARANG',
            'MASA KERJA TAHUN',
            'MASA KERJA BULAN',
            'JABATAN STRUKTURAL',
            'TMT JABATAN STRUKTURAL',
            'ESELON',
            'TMT ESELON',
            'JABATAN FUNGSIONAL',
            'TMT JABATAN FUNGSIONAL',
            'RKP',
            'KGB',
            'JENJANG DIDIK',
            'JURUSAN',
            'TAHUN LULUS',
            'TMT TUBEL'
        ];
    }

    /**
     * Mapping data dari database ke array export
     * Format sesuai dengan sistem HRD original untuk pegawai tubel
     */
    public function map($row): array
    {
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

        // Jenis kelamin - biarkan Wanita/Pria seperti original (tidak dikonversi)
        $jenisKelamin = $row->jenis_kelamin ?? '';

        return [
            $formatNumber($row->kd_karyawan ?? ''),               // ID PEG.
            $formatNumber($row->no_absen ?? ''),                  // NO ABSEN
            $formatNumber($row->nip_lama ?? ''),                  // NIP LAMA
            $formatNumber($row->nip_baru ?? ''),                  // NIP BARU
            $row->gelar_depan ?? '',                              // GELAR DEPAN
            $row->nama ?? '',                                     // NAMA
            $row->gelar_belakang ?? '',                           // GELAR BELAKANG
            $jenisKelamin,                                        // JENIS KELAMIN
            $row->tempat_lahir ?? '',                             // TEMPAT LAHIR
            $formatDate($row->tgl_lahir ?? ''),                   // TANGGAL LAHIR
            $formatNumber($row->no_ktp ?? ''),                    // NO KTP
            $row->alamat ?? '',                                   // ALAMAT
            $row->kelurahan ?? '',                                // KELURAHAN
            $row->kecamatan ?? '',                                // KECAMATAN
            $row->kabupaten ?? '',                                // KABUPATEN
            $row->propinsi ?? '',                                 // PROVINSI (note: 'propinsi' di DB original)
            $row->kulit ?? '',                                    // WARNA KULIT
            $row->tinggi_badan ?? '',                             // TINGGI BADAN
            $row->berat_badan ?? '',                              // BERAT BADAN
            $row->goldar ?? '',                                   // GOLONGAN DARAH
            $row->suku ?? '',                                     // SUKU
            $row->agama ?? '',                                    // AGAMA
            $row->kebangsaan ?? '',                               // KEBANGSAAN
            $row->status_nikah ?? '',                             // STATUS NIKAH
            $formatNumber($row->no_karis ?? ''),                  // NO KARIS / KARSU
            $formatNumber($row->no_karpeg ?? ''),                 // NO KARPEG
            $formatNumber($row->no_akte ?? ''),                   // NO AKTE KELAHIRAN
            $formatNumber($row->no_askes ?? ''),                  // NO ASKES / BPJS
            $formatNumber($row->no_taspen ?? ''),                 // NO TASPEN
            $formatNumber($row->no_npwp ?? ''),                   // NO NPWP
            $formatNumber($row->no_kk ?? ''),                     // NO KK
            $row->nama_ibu_kandung ?? '',                         // NAMA IBU KANDUNG
            $row->email ?? '',                                    // EMAIL
            $formatNumber($row->no_hp ?? ''),                     // NO HP
            $formatNumber($row->no_hp_alternatif ?? ''),          // NO HP ALTERNATIF
            $row->status_rmh ?? '',                               // STATUS RUMAH
            $formatNumber($row->rek_bpd_aceh ?? ''),              // NO REKENING BPD ACEH
            $formatNumber($row->rek_bni ?? ''),                   // NO REKENING BNI
            $formatNumber($row->rek_mandiri ?? ''),               // NO REKENING MANDIRI
            $row->tanggungan ?? '',                               // TANGGUNGAN
            $row->sub_detail ?? '',                               // SUB DETAIL JENIS TENAGA
            $row->detail_jenis_tenaga ?? '',                      // DETAIL JENIS TENAGA
            $row->jenis_tenaga ?? '',                             // JENIS TENAGA
            $row->ruangan ?? '',                                  // RUANGAN
            $row->sub_unit_kerja ?? '',                           // SUB UNIT KERJA
            $row->unit_kerja ?? '',                               // UNIT KERJA
            $row->divisi ?? '',                                   // DIVISI
            $row->status_kerja ?? '',                             // STATUS KERJA
            $row->jenis_peg ?? '',                                // JENIS PEGAWAI
            $row->pangkat_masuk ?? '',                            // PANGKAT MASUK
            $row->kd_gol_masuk ?? '',                             // GOLONGAN MASUK
            $formatDate($row->tmt_gol_masuk ?? ''),               // TMT GOLONGAN MASUK
            $row->pangkat ?? '',                                  // PANGKAT SEKARANG
            $row->kd_gol_sekarang ?? '',                          // GOLONGAN SEKARANG
            $formatDate($row->tmt_gol_sekarang ?? ''),            // TMT GOLONGAN SEKARANG
            $row->masa_kerja_tahun ?? '',                         // MASA KERJA TAHUN
            $row->masa_kerja_bulan ?? '',                         // MASA KERJA BULAN
            $row->jab_struk ?? '',                                // JABATAN STRUKTURAL
            $formatDate($row->tmt_jabatan_struktural ?? ''),      // TMT JABATAN STRUKTURAL
            $row->eselon ?? '',                                   // ESELON
            $formatDate($row->tmt_eselon ?? ''),                  // TMT ESELON
            $row->jab_fung ?? '',                                 // JABATAN FUNGSIONAL
            $formatDate($row->tmt_jabfung ?? ''),                 // TMT JABATAN FUNGSIONAL
            $formatDate($row->rencana_kp ?? ''),                  // RKP
            $formatDate($row->kgb ?? ''),                         // KGB
            $row->jenjang_didik ?? '',                            // JENJANG DIDIK
            $row->jurusan ?? '',                                  // JURUSAN
            $row->tahun_lulus ?? '',                              // TAHUN LULUS
            $formatDate($row->tgl_keluar_pensiun ?? '')           // TMT TUBEL (menggunakan field yang sama dengan keluar/pensiun)
        ];
    }

    /**
     * Format kolom sebagai text untuk nomor-nomor penting
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,  // ID PEG.
            'B' => NumberFormat::FORMAT_TEXT,  // NO ABSEN
            'C' => NumberFormat::FORMAT_TEXT,  // NIP LAMA
            'D' => NumberFormat::FORMAT_TEXT,  // NIP BARU
            'K' => NumberFormat::FORMAT_TEXT,  // NO KTP
            'X' => NumberFormat::FORMAT_TEXT,  // NO KARIS / KARSU
            'Y' => NumberFormat::FORMAT_TEXT,  // NO KARPEG
            'Z' => NumberFormat::FORMAT_TEXT,  // NO AKTE KELAHIRAN
            'AB' => NumberFormat::FORMAT_TEXT, // NO ASKES / BPJS
            'AC' => NumberFormat::FORMAT_TEXT, // NO TASPEN
            'AF' => NumberFormat::FORMAT_TEXT, // NO HP
            'AG' => NumberFormat::FORMAT_TEXT, // NO HP ALTERNATIF
            'AH' => NumberFormat::FORMAT_TEXT, // NO REKENING BPD ACEH
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
