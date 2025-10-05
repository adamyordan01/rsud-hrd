<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BackupService
{
    /**
     * Backup data karyawan bulanan ke tabel HRD_KARYAWAN_BACKUP
     *
     * @param int|null $bulan Bulan backup (default: bulan sebelumnya)
     * @param int|null $tahun Tahun backup (default: tahun ini atau tahun lalu jika bulan = 1)
     * @return array
     */
    public function backupMonthlyData($bulan = null, $tahun = null)
    {
        try {
            // Tentukan bulan dan tahun backup
            if ($bulan === null || $tahun === null) {
                $currentMonth = (int) date('m');
                $currentYear = (int) date('Y');
                
                if ($currentMonth == 1) {
                    $bulan = 12;
                    $tahun = $currentYear - 1;
                } else {
                    $bulan = $currentMonth - 1;
                    $tahun = $currentYear;
                }
            }

            $bulanFormatted = sprintf("%02d", $bulan);
            
            Log::info("Starting backup process for month: {$bulanFormatted}, year: {$tahun}");

            // Cek apakah backup untuk periode ini sudah ada
            $existingBackup = $this->checkBackupExists($bulanFormatted, $tahun);
            
            if ($existingBackup > 0) {
                return [
                    'success' => false,
                    'message' => "Backup untuk bulan {$bulanFormatted}-{$tahun} sudah dilakukan sebelumnya",
                    'existing_records' => $existingBackup
                ];
            }

            // Hitung total data yang akan dibackup
            $totalRecords = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->count();

            if ($totalRecords == 0) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada data karyawan untuk dibackup'
                ];
            }

            // Mulai proses backup
            $backupTime = Carbon::now()->format('Y-m-d H:i:s');
            $userId = Auth::id() ?? 'system';

            // Insert data ke HRD_KARYAWAN_BACKUP
            $backupQuery = "
                INSERT INTO HRD_KARYAWAN_BACKUP 
                SELECT 
                    KD_KARYAWAN, NO_ABSEN, NIP_LAMA, NIP_BARU, GELAR_DEPAN, NAMA, GELAR_BELAKANG, 
                    TEMPAT_LAHIR, TGL_LAHIR, NO_KTP, ALAMAT, KD_PROPINSI, KD_KABUPATEN, 
                    KD_KECAMATAN, KD_KELURAHAN, KD_JENIS_KELAMIN, KD_KULIT, TINGGI_BADAN, 
                    BERAT_BADAN, KODE_GOL_DAR, KD_SUKU, KD_AGAMA, KD_BANGSA, KD_STATUS_NIKAH, 
                    NO_KARIS, NO_KARPEG, NO_AKTE, NO_ASKES, NO_TASPEN, NO_NPWP, NO_HP, 
                    NO_HP_ALTERNATIF, KD_STATUS_RMH, KD_STATUS_KERJA, REK_BNI, TANGGUNGAN, 
                    KD_DIVISI, KD_UNIT_KERJA, KD_SUB_UNIT_KERJA, KD_RUANGAN, KD_JENIS_PEG, 
                    EMAIL, KD_GOL_MASUK, TMT_GOL_MASUK, KD_GOL_SEKARANG, TMT_GOL_SEKARANG, 
                    KD_JABATAN_STRUKTURAL, TMT_JABATAN_STRUKTURAL, KD_ESELON, TMT_ESELON, 
                    KD_JABFUNG, TMT_JABFUNG, KGB, RENCANA_KP, MASA_KERJA_THN, MASA_KERJA_BULAN, 
                    KD_PENDIDIKAN_TERAKHIR, TAHUN_LULUS, STATUS_PEG, TGL_KELUAR_PENSIUN, 
                    KD_BEBAN_TAMBAHAN, KD_BEBAN_KERJA, KD_KEDARURATAN, KD_RESIKO, 
                    KD_KLP_JASA1, KD_KLP_JASA2, KD_KLP_JASA3, PASSWORD, FOTO, KD_JENIS_TENAGA, 
                    NO_KK, NAMA_IBU_KANDUNG, KD_DETAIL_JENIS_TENAGA, KD_SUB_DETAIL_JENIS_TENAGA, 
                    TGL_UPDATE, USER_UPDATE, KD_JURUSAN, MASA_KERJA_THN_CPNS, MASA_KERJA_BLN_CPNS, 
                    REK_BPD_ACEH, REK_MANDIRI, PENILAI, PENILAI_2, PENILAI_3, 
                    '{$backupTime}', '{$bulanFormatted}', '{$tahun}', REK_BNI_SYARIAH, 
                    NEW_PASSWORD, REK_BSI 
                FROM HRD_KARYAWAN
            ";

            DB::connection('sqlsrv')->statement($backupQuery);

            // Insert log backup ke HRD_USER_BACKUP
            $userBackupQuery = "
                INSERT INTO HRD_USER_BACKUP (TGL_BACKUP, USER_BACKUP) 
                VALUES ('{$backupTime}', '{$userId}')
            ";

            DB::connection('sqlsrv')->statement($userBackupQuery);

            // Verifikasi backup berhasil
            $backupCount = $this->checkBackupExists($bulanFormatted, $tahun);

            Log::info("Backup completed successfully. Records backed up: {$backupCount}");

            return [
                'success' => true,
                'message' => "Backup berhasil dilakukan untuk periode {$bulanFormatted}-{$tahun}",
                'backup_count' => $backupCount,
                'total_original' => $totalRecords,
                'backup_time' => $backupTime
            ];

        } catch (\Exception $e) {
            Log::error('Backup process failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return [
                'success' => false,
                'message' => 'Backup gagal: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cek apakah backup untuk periode tertentu sudah ada
     *
     * @param string $bulan
     * @param int $tahun
     * @return int
     */
    public function checkBackupExists($bulan, $tahun)
    {
        try {
            return DB::connection('sqlsrv')
                ->table('hrd_karyawan_backup')
                ->where('BULAN_BACKUP', $bulan)
                ->where('TAHUN_BACKUP', $tahun)
                ->count();
        } catch (\Exception $e) {
            Log::error('Error checking backup existence: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get backup history
     *
     * @param int $limit
     * @return array
     */
    public function getBackupHistory($limit = 10)
    {
        try {
            $history = DB::connection('sqlsrv')
                ->table('hrd_karyawan_backup')
                ->select(
                    'BULAN_BACKUP as bulan',
                    'TAHUN_BACKUP as tahun',
                    DB::raw('COUNT(*) as total_records'),
                    DB::raw('MIN(TGL_BACKUP) as backup_date')
                )
                ->groupBy('BULAN_BACKUP', 'TAHUN_BACKUP')
                ->orderBy('TAHUN_BACKUP', 'desc')
                ->orderBy('BULAN_BACKUP', 'desc')
                ->limit($limit)
                ->get();

            return [
                'success' => true,
                'data' => $history
            ];
        } catch (\Exception $e) {
            Log::error('Error getting backup history: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal mengambil riwayat backup: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get backup data for specific period
     *
     * @param string $bulan
     * @param int $tahun
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBackupData($bulan, $tahun, $limit = 100, $offset = 0)
    {
        try {
            $data = DB::connection('sqlsrv')
                ->table('hrd_karyawan_backup')
                ->where('BULAN_BACKUP', $bulan)
                ->where('TAHUN_BACKUP', $tahun)
                ->select('KD_KARYAWAN', 'NAMA', 'NIP_BARU', 'STATUS_PEG', 'TGL_BACKUP')
                ->offset($offset)
                ->limit($limit)
                ->get();

            $total = $this->checkBackupExists($bulan, $tahun);

            return [
                'success' => true,
                'data' => $data,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ];
        } catch (\Exception $e) {
            Log::error('Error getting backup data: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal mengambil data backup: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if current user can perform backup
     *
     * @return bool
     */
    public function canPerformBackup()
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Sementara return true untuk admin/super-admin
        // Nanti bisa disesuaikan dengan sistem permission yang ada
        return true;

        // Uncomment dan sesuaikan dengan sistem permission yang digunakan:
        
        // Jika menggunakan Spatie Permission:
        // if ($user->can('hrd_manage_backup')) {
        //     return true;
        // }

        // Jika menggunakan role-based:
        // if ($user->roles && $user->roles->contains('name', 'admin')) {
        //     return true;
        // }

        // Jika menggunakan custom permission check:
        // if ($user->permissions && $user->permissions->contains('name', 'hrd_manage_backup')) {
        //     return true;
        // }

        // Tambahan cek berdasarkan ruangan jika diperlukan (seperti di HRD original)
        // if ($user->karyawan && in_array($user->karyawan->kd_ruangan, [91, 57])) {
        //     return true;
        // }

        // return false;
    }

    /**
     * Get monthly backup requirement info
     *
     * @return array
     */
    public function getBackupRequirement()
    {
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');
        
        if ($currentMonth == 1) {
            $requiredMonth = 12;
            $requiredYear = $currentYear - 1;
        } else {
            $requiredMonth = $currentMonth - 1;
            $requiredYear = $currentYear;
        }

        $requiredMonthFormatted = sprintf("%02d", $requiredMonth);
        $exists = $this->checkBackupExists($requiredMonthFormatted, $requiredYear);
        
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        return [
            'required_month' => $requiredMonth,
            'required_year' => $requiredYear,
            'required_month_formatted' => $requiredMonthFormatted,
            'required_month_name' => $monthNames[$requiredMonthFormatted],
            'backup_exists' => $exists > 0,
            'backup_count' => $exists,
            'is_required' => $exists == 0
        ];
    }
}