<?php

namespace App\Services;

use App\Models\Karyawan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmployeeProfileService
{
    public function getEmployeeProfile($id)
    {
        return Cache::remember("karyawan_{$id}_profile", 60 * 5, function () use ($id) {
            $karyawan = Karyawan::where('kd_karyawan', $id)->firstOrFail();

            // Daftar field wajib
            $requiredFields = [
                'nama', 'tempat_lahir', 'tgl_lahir', 'no_ktp', 'alamat', 'kd_propinsi',
                'kd_kabupaten', 'kd_kecamatan', 'kd_kelurahan', 'kd_jenis_kelamin',
                'kd_kulit', 'tinggi_badan', 'berat_badan', 'kode_gol_dar', 'kd_suku',
                'kd_agama', 'kd_bangsa', 'kd_status_nikah', 'no_akte', 'no_askes',
                'no_npwp', 'no_hp', 'kd_status_rmh', 'kd_status_kerja', 'email',
                'kd_pendidikan_terakhir', 'tahun_lulus', 'foto', 'no_kk',
                'nama_ibu_kandung', 'kd_jurusan', 'rek_bni_syariah'
            ];

            $pnsPppkFields = [
                'nip_baru', 'no_karis', 'no_karpeg', 'no_taspen', 'tanggungan',
                'masa_kerja_thn', 'masa_kerja_bulan'
            ];

            // Label informatif untuk setiap field
            $fieldLabels = [
                'nama' => 'Nama Lengkap',
                'tempat_lahir' => 'Tempat Lahir',
                'tgl_lahir' => 'Tanggal Lahir',
                'no_ktp' => 'Nomor KTP',
                'alamat' => 'Alamat',
                'kd_propinsi' => 'Provinsi',
                'kd_kabupaten' => 'Kabupaten/Kota',
                'kd_kecamatan' => 'Kecamatan',
                'kd_kelurahan' => 'Kelurahan',
                'kd_jenis_kelamin' => 'Jenis Kelamin',
                'kd_kulit' => 'Warna Kulit',
                'tinggi_badan' => 'Tinggi Badan',
                'berat_badan' => 'Berat Badan',
                'kode_gol_dar' => 'Golongan Darah',
                'kd_suku' => 'Suku',
                'kd_agama' => 'Agama',
                'kd_bangsa' => 'Kebangsaan',
                'kd_status_nikah' => 'Status Pernikahan',
                'no_akte' => 'Nomor Akte Kelahiran',
                'no_askes' => 'Nomor Askes',
                'no_npwp' => 'Nomor NPWP',
                'no_hp' => 'Nomor HP',
                'kd_status_rmh' => 'Status Rumah',
                'kd_status_kerja' => 'Status Kerja',
                'email' => 'Email',
                'kd_pendidikan_terakhir' => 'Pendidikan Terakhir',
                'tahun_lulus' => 'Tahun Lulus',
                'foto' => 'Foto Profil',
                'no_kk' => 'Nomor Kartu Keluarga',
                'nama_ibu_kandung' => 'Nama Ibu Kandung',
                'kd_jurusan' => 'Jurusan Pendidikan',
                'rek_bni_syariah' => 'Nomor Rekening BNI Syariah',
                'nip_baru' => 'NIP Baru',
                'no_karis' => 'Nomor Karis/Karsu',
                'no_karpeg' => 'Nomor Karpeg',
                'no_taspen' => 'Nomor Taspen',
                'tanggungan' => 'Jumlah Tanggungan',
                'masa_kerja_thn' => 'Masa Kerja (Tahun)',
                'masa_kerja_bulan' => 'Masa Kerja (Bulan)'
            ];

            // Tentukan status kerja
            $statusKerja = $karyawan->kd_status_kerja;
            $isPnsOrPppk = in_array($statusKerja, [1, 7]);
            $finalRequiredFields = $isPnsOrPppk ? array_merge($requiredFields, $pnsPppkFields) : $requiredFields;

            // Hitung field terisi dan deteksi kosong
            $filledFields = 0;
            $missingFields = [];
            foreach ($finalRequiredFields as $field) {
                $value = $karyawan->$field;
                if (!is_null($value) && $value !== '' && $value !== 0) {
                    $filledFields++;
                } else {
                    $missingFields[] = $fieldLabels[$field] ?? str_replace('_', ' ', ucwords(strtolower($field)));
                }
            }

            $totalRequiredFields = count($finalRequiredFields);
            $persentase = $totalRequiredFields > 0 ? ($filledFields / $totalRequiredFields) * 100 : 0;

            $namaLengkap = trim(($karyawan->gelar_depan ?? '') . ' ' . $karyawan->nama . ' ' . ($karyawan->gelar_belakang ?? ''));
            $alamat = trim($karyawan->alamat . ', ' . $karyawan->kd_kelurahan . ', ' . $karyawan->kd_kecamatan . ', ' . $karyawan->kd_kabupaten . ', ' . $karyawan->kd_propinsi);

            $alasanList = DB::table('hrd_keterangan_nametag')->select('ID', 'KETERANGAN')->get();

            return [
                'karyawan' => $karyawan,
                'nama_lengkap' => $namaLengkap,
                'alamat' => $alamat,
                'persentase_kelengkapan' => round($persentase, 0),
                'missing_fields' => $missingFields,
                'alasan_list' => $alasanList,
            ];
        });
    }
}