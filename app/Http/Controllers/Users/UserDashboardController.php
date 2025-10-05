<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\EmployeeProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    protected $employeeProfileService;
    
    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }

    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Menggunakan EmployeeProfileService untuk kelengkapan data
        $profileData = $this->employeeProfileService->getEmployeeProfile($karyawan->kd_karyawan);
        
        // Data personal menggunakan service
        $personalData = $this->getPersonalData($karyawan, $profileData);
        
        // Data kepegawaian
        $kepegawaianData = $this->getKepegawaianData($karyawan);
        
        // Data sertifikasi
        $sertifikasiData = $this->getSertifikasiData($karyawan);
        
        // Notifikasi penting menggunakan data dari service
        $notifications = $this->getNotifications($karyawan, $profileData);

        // Pastikan semua data ada dan berbentuk array
        $personalData = $personalData ?? [];
        $kepegawaianData = $kepegawaianData ?? [];
        $sertifikasiData = $sertifikasiData ?? [];
        $notifications = $notifications ?? [];

        return view('users.dashboard', compact(
            'karyawan',
            'personalData', 
            'kepegawaianData',
            'sertifikasiData',
            'notifications'
        ));
    }

    private function getPersonalData($karyawan, $profileData)
    {
        // Menggunakan data kelengkapan dari EmployeeProfileService
        $completionPercentage = $profileData['persentase_kelengkapan'];
        $missingFields = $profileData['missing_fields'];
        $totalMissingFields = count($missingFields);
        
        return [
            'completion_percentage' => $completionPercentage,
            'missing_fields' => $missingFields,
            'total_missing_fields' => $totalMissingFields,
            'status_kerja' => $this->getStatusKerjaText($karyawan->kd_status_kerja),
            'jenis_tenaga' => $karyawan->jenis_tenaga ?? 'Belum diatur',
            'ruangan' => $karyawan->nama_ruangan ?? 'Belum diatur',
            'golongan' => $karyawan->kd_gol ?? 'Belum diatur'
        ];
    }

    private function getKepegawaianData($karyawan)
    {
        // Hitung jumlah SK
        $totalSk = DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('stt', '>', 0)
            ->count();

        // SK terbaru
        $skTerbaru = DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('stt', '>', 0)
            ->orderBy('tgl_sk', 'desc')
            ->first();

        // Mutasi terbaru
        $mutasiTerbaru = DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('tmt_jabatan', 'desc')
            ->first();

        // Surat izin tahun ini
        $suratIzinTahunIni = DB::table('hrd_surat_izin')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->whereYear('tgl_mulai', date('Y'))
            ->count();

        return [
            'total_sk' => $totalSk,
            'sk_terbaru' => $skTerbaru,
            'mutasi_terbaru' => $mutasiTerbaru,
            'surat_izin_tahun_ini' => $suratIzinTahunIni
        ];
    }

    private function getSertifikasiData($karyawan)
    {
        // STR aktif
        $strAktif = DB::table('hrd_r_str')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('tgl_kadaluarsa', '>=', date('Y-m-d'))
            ->count();

        // SIP aktif
        $sipAktif = DB::table('hrd_r_sip')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('tgl_kadaluarsa', '>=', date('Y-m-d'))
            ->count();

        // Seminar tahun ini
        $seminarTahunIni = DB::table('hrd_r_seminar')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('tahun', date('Y'))
            ->count();

        // Total penghargaan
        $totalPenghargaan = DB::table('hrd_r_penghargaan')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->count();

        return [
            'str_aktif' => $strAktif,
            'sip_aktif' => $sipAktif,
            'seminar_tahun_ini' => $seminarTahunIni,
            'total_penghargaan' => $totalPenghargaan
        ];
    }

    private function getNotifications($karyawan, $profileData)
    {
        $notifications = [];

        // STR akan berakhir dalam 30 hari
        $strExpiringSoon = DB::table('hrd_r_str')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('tgl_kadaluarsa', '>', date('Y-m-d'))
            ->where('tgl_kadaluarsa', '<=', date('Y-m-d', strtotime('+30 days')))
            ->get();

        foreach ($strExpiringSoon as $str) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'STR Akan Berakhir',
                'message' => "STR No. {$str->no_str} akan berakhir pada " . date('d/m/Y', strtotime($str->tgl_kadaluarsa)),
                'date' => $str->tgl_kadaluarsa,
                'no_sertifikat' => $str->no_str,
                'jenis' => 'STR'
            ];
        }

        // SIP akan berakhir dalam 30 hari
        $sipExpiringSoon = DB::table('hrd_r_sip')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('tgl_kadaluarsa', '>', date('Y-m-d'))
            ->where('tgl_kadaluarsa', '<=', date('Y-m-d', strtotime('+30 days')))
            ->get();

        foreach ($sipExpiringSoon as $sip) {
            $notifications[] = [
                'type' => 'warning', 
                'title' => 'SIP Akan Berakhir',
                'message' => "SIP No. {$sip->no_sip} akan berakhir pada " . date('d/m/Y', strtotime($sip->tgl_kadaluarsa)),
                'date' => $sip->tgl_kadaluarsa,
                'no_sertifikat' => $sip->no_sip,
                'jenis' => 'SIP'
            ];
        }

        // Profil belum lengkap menggunakan data dari EmployeeProfileService
        $completionPercentage = $profileData['persentase_kelengkapan'];
        if ($completionPercentage < 80) {
            $missingFieldsCount = count($profileData['missing_fields']);
            $notifications[] = [
                'type' => 'info',
                'title' => 'Profil Belum Lengkap',
                'message' => "Kelengkapan profil Anda {$completionPercentage}%. Masih ada {$missingFieldsCount} data yang perlu dilengkapi.",
                'date' => date('Y-m-d')
            ];
        }

        // Sort by date
        usort($notifications, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $notifications;
    }

    private function getStatusKerjaText($kdStatusKerja)
    {
        $statusMap = [
            1 => 'PNS',
            2 => 'Honor',
            3 => 'Kontrak',
            4 => 'Part Time',
            5 => 'Magang',
            6 => 'THL',
            7 => 'PPPK'
        ];

        return $statusMap[$kdStatusKerja] ?? 'Tidak Diketahui';
    }
}