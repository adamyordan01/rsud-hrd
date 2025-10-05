<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserSertifikasiController extends Controller
{
    public function str()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Ambil data STR
        $dataStr = DB::table('hrd_r_str')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('tgl_kadaluarsa', 'desc')
            ->get();

        // Tambahkan status untuk setiap STR
        foreach ($dataStr as $str) {
            $str->status = $this->getStatusSertifikat($str->tgl_kadaluarsa);
            $str->masa_berlaku = $this->hitungMasaBerlaku($str->tgl_kadaluarsa);
        }

        return view('users.sertifikasi.str', compact('dataStr', 'karyawan'));
    }

    public function sip()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Ambil data SIP
        $dataSip = DB::table('hrd_r_sip')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('tgl_kadaluarsa', 'desc')
            ->get();

        // Tambahkan status untuk setiap SIP
        foreach ($dataSip as $sip) {
            $sip->status = $this->getStatusSertifikat($sip->tgl_kadaluarsa);
            $sip->masa_berlaku = $this->hitungMasaBerlaku($sip->tgl_kadaluarsa);
        }

        return view('users.sertifikasi.sip', compact('dataSip', 'karyawan'));
    }

    public function seminar()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Ambil data seminar
        $dataSeminar = DB::table('hrd_r_seminar')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('tgl_mulai', 'desc')
            ->get();

        // Statistik seminar
        $statistik = [
            'total_seminar' => $dataSeminar->count(),
            'seminar_tahun_ini' => $dataSeminar->filter(function($item) {
                return $item->tahun == date('Y');
            })->count(),
            'total_jp' => $dataSeminar->sum('jml_jam') ?? 0,
            'jp_tahun_ini' => $dataSeminar->filter(function($item) {
                return $item->tahun == date('Y');
            })->sum('jml_jam') ?? 0
        ];

        return view('users.sertifikasi.seminar', compact('dataSeminar', 'karyawan', 'statistik'));
    }

    public function penghargaan()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Ambil data penghargaan
        $dataPenghargaan = DB::table('hrd_r_penghargaan')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->orderBy('tgl_sk', 'desc')
            ->get();

        // Tambahkan tahun berdasarkan tgl_sk untuk setiap penghargaan
        foreach ($dataPenghargaan as $penghargaan) {
            if ($penghargaan->tgl_sk) {
                $penghargaan->tahun = date('Y', strtotime($penghargaan->tgl_sk));
            } else {
                $penghargaan->tahun = date('Y');
            }
        }

        // Kelompokkan berdasarkan tahun
        $penghargaanPerTahun = $dataPenghargaan->groupBy('tahun');

        // Statistik penghargaan
        $statistik = [
            'total_penghargaan' => $dataPenghargaan->count(),
            'penghargaan_tahun_ini' => $dataPenghargaan->filter(function($item) {
                return $item->tahun == date('Y');
            })->count(),
            'total_bentuk' => $dataPenghargaan->pluck('bentuk')->unique()->count(),
            'pejabat_terbanyak' => $dataPenghargaan->groupBy('pejabat')->sortByDesc(function($group) {
                return $group->count();
            })->keys()->first() ?? '-'
        ];

        return view('users.sertifikasi.penghargaan', compact('dataPenghargaan', 'penghargaanPerTahun', 'karyawan', 'statistik'));
    }

    public function downloadStrFile($urut)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa STR ini milik user yang sedang login
        $str = DB::table('hrd_r_str')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('urut_str', $urut)
            ->first();

        if (!$str) {
            abort(404, 'Data STR tidak ditemukan.');
        }

        if (empty($str->sc_berkas)) {
            abort(404, 'File STR tidak tersedia.');
        }

        $filepath = storage_path("app/hrd_files/str/{$str->sc_berkas}");

        if (!file_exists($filepath)) {
            abort(404, 'File STR tidak ditemukan.');
        }

        return response()->download($filepath, $str->sc_berkas);
    }

    public function downloadSipFile($urut)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa SIP ini milik user yang sedang login
        $sip = DB::table('hrd_r_sip')
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->where('urut_sip', $urut)
            ->first();

        if (!$sip) {
            abort(404, 'Data SIP tidak ditemukan.');
        }

        if (empty($sip->sc_berkas)) {
            abort(404, 'File SIP tidak tersedia.');
        }

        $filepath = storage_path("app/hrd_files/sip/{$sip->sc_berkas}");

        if (!file_exists($filepath)) {
            abort(404, 'File SIP tidak ditemukan.');
        }

        return response()->download($filepath, $sip->sc_berkas);
    }

    // Helper functions
    private function getStatusSertifikat($tglAkhir)
    {
        $sekarang = date('Y-m-d');
        $tglExpiry = date('Y-m-d', strtotime($tglAkhir));
        $selisihHari = (strtotime($tglExpiry) - strtotime($sekarang)) / (60 * 60 * 24);

        if ($selisihHari < 0) {
            return ['status' => 'expired', 'text' => 'Kadaluarsa', 'class' => 'badge-danger'];
        } elseif ($selisihHari <= 30) {
            return ['status' => 'warning', 'text' => 'Akan Berakhir', 'class' => 'badge-warning'];
        } else {
            return ['status' => 'active', 'text' => 'Aktif', 'class' => 'badge-success'];
        }
    }

    private function hitungMasaBerlaku($tglAkhir)
    {
        $sekarang = new \DateTime();
        $akhir = new \DateTime($tglAkhir);
        $interval = $sekarang->diff($akhir);

        if ($akhir < $sekarang) {
            return "Kadaluarsa {$interval->days} hari yang lalu";
        } else {
            if ($interval->y > 0) {
                return "{$interval->y} tahun {$interval->m} bulan lagi";
            } elseif ($interval->m > 0) {
                return "{$interval->m} bulan {$interval->d} hari lagi";
            } else {
                return "{$interval->d} hari lagi";
            }
        }
    }

    private function formatTanggal($tanggal)
    {
        if (empty($tanggal) || $tanggal == '0000-00-00') {
            return '-';
        }
        
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $tgl = date('j', strtotime($tanggal));
        $bln = $bulan[date('n', strtotime($tanggal))];
        $thn = date('Y', strtotime($tanggal));
        
        return "{$tgl} {$bln} {$thn}";
    }

    public function strDetail($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Ambil detail STR berdasarkan urut_str
        $str = DB::table('hrd_r_str')
            ->where('urut_str', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$str) {
            return response()->json(['error' => 'STR tidak ditemukan.'], 404);
        }

        // Tambahkan status dan masa berlaku
        $str->status = $this->getStatusSertifikat($str->tgl_kadaluarsa);
        $str->masa_berlaku = $this->hitungMasaBerlaku($str->tgl_kadaluarsa);

        $html = view('users.sertifikasi.partials.str-detail', compact('str'))->render();
        
        return response()->json(['html' => $html]);
    }

    public function downloadStr($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa STR ini milik user yang sedang login
        $str = DB::table('hrd_r_str')
            ->where('urut_str', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$str) {
            abort(404, 'Data STR tidak ditemukan.');
        }

        // Check if document exists
        if (!$str->sc_berkas || !file_exists(storage_path('app/hrd_files/str/' . $str->sc_berkas))) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $fileName = 'STR_' . $str->no_str . '_' . $karyawan->kd_karyawan . '.pdf';
        
        return response()->download(storage_path('app/hrd_files/str/' . $str->sc_berkas), $fileName);
    }

    public function sipDetail($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Ambil detail SIP berdasarkan urut_sip
        $sip = DB::table('hrd_r_sip')
            ->where('urut_sip', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$sip) {
            return response()->json(['error' => 'SIP tidak ditemukan.'], 404);
        }

        // Tambahkan status dan masa berlaku
        $sip->status = $this->getStatusSertifikat($sip->tgl_kadaluarsa);
        $sip->masa_berlaku = $this->hitungMasaBerlaku($sip->tgl_kadaluarsa);

        $html = view('users.sertifikasi.partials.sip-detail', compact('sip'))->render();
        
        return response()->json(['html' => $html]);
    }

    public function downloadSip($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa SIP ini milik user yang sedang login
        $sip = DB::table('hrd_r_sip')
            ->where('urut_sip', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$sip) {
            abort(404, 'Data SIP tidak ditemukan.');
        }

        // Check if document exists
        if (!$sip->sc_berkas || !file_exists(storage_path('app/hrd_files/sip/' . $sip->sc_berkas))) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $fileName = 'SIP_' . $sip->no_sip . '_' . $karyawan->kd_karyawan . '.pdf';
        
        return response()->download(storage_path('app/hrd_files/sip/' . $sip->sc_berkas), $fileName);
    }

    public function seminarDetail($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Ambil detail seminar berdasarkan urut_seminar
        $seminar = DB::table('hrd_r_seminar')
            ->where('urut_seminar', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$seminar) {
            return response()->json(['error' => 'Seminar tidak ditemukan.'], 404);
        }

        $html = view('users.sertifikasi.partials.seminar-detail', compact('seminar'))->render();
        
        return response()->json(['html' => $html]);
    }

    public function downloadSertifikatSeminar($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        // Verifikasi bahwa seminar ini milik user yang sedang login
        $seminar = DB::table('hrd_r_seminar')
            ->where('urut_seminar', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$seminar) {
            abort(404, 'Data seminar tidak ditemukan.');
        }

        // Check if document exists
        if (!$seminar->no_sertifikat || !file_exists(storage_path('app/hrd_files/seminar/' . $seminar->no_sertifikat))) {
            abort(404, 'File sertifikat tidak ditemukan.');
        }

        $fileName = 'Sertifikat_Seminar_' . str_replace(' ', '_', $seminar->nama_seminar) . '_' . $karyawan->kd_karyawan . '.pdf';
        
        return response()->download(storage_path('app/hrd_files/seminar/' . $seminar->no_sertifikat), $fileName);
    }

    public function penghargaanDetail($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Ambil detail penghargaan berdasarkan urut_peng
        $penghargaan = DB::table('hrd_r_penghargaan')
            ->where('urut_peng', $id)
            ->where('kd_karyawan', $karyawan->kd_karyawan)
            ->first();

        if (!$penghargaan) {
            return response()->json(['error' => 'Penghargaan tidak ditemukan.'], 404);
        }

        // Tambahkan tahun berdasarkan tgl_sk
        if ($penghargaan->tgl_sk) {
            $penghargaan->tahun = date('Y', strtotime($penghargaan->tgl_sk));
        }

        $html = view('users.sertifikasi.partials.penghargaan-detail', compact('penghargaan'))->render();
        
        return response()->json(['html' => $html]);
    }
}