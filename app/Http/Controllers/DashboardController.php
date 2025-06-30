<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Karyawan\JenisTenagaController;

class DashboardController extends Controller
{
    // admin dashboard
    public function index()
    {
        $pegawaiPns = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 1)
            ->where('status_peg', 1)
            ->first();

        $pegawaiHonor = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 2)
            ->where('status_peg', 1)
            ->first();

        $pppk = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 7)
            ->where('status_peg', 1)
            ->first();

        $pegawaiKontrakBlud = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 3)
            ->where('status_peg', 1)
            ->where('kd_jenis_peg', 2)
            ->first();

        $pegawaiKontrakPemko = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 3)
            ->where('status_peg', 1)
            ->where('kd_jenis_peg', 1)
            ->first();

        $pegawaiPartTime = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 4)
            ->where('status_peg', 1)
            ->first();

        $pegawaiLuar = DB::table('hrd_karyawan_luar')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 5)
            ->where('status_peg', 1)
            ->first();

        $pegawaiLuar = collect($pegawaiLuar);

        $pegawaiThl = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('kd_status_kerja', 6)
            ->where('status_peg', 1)
            ->first();

        $totalPegawai = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki_laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perempuan')
            ->where('status_peg', 1)
            ->first();

        // Data untuk karyawan yang belum ada jenis tenaga
        $belumAdaJenisTenaga = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_jenis_kelamin = 1 then 1 else 0 end) as laki')
            ->selectRaw('sum(case when kd_jenis_kelamin = 0 then 1 else 0 end) as perem')
            ->selectRaw('sum(case when kd_status_kerja = 1 then 1 else 0 end) as pns')
            ->selectRaw('sum(case when kd_status_kerja = 2 then 1 else 0 end) as honor')
            ->selectRaw('sum(case when kd_status_kerja = 3 then 1 else 0 end) as kontrak')
            ->selectRaw('sum(case when kd_status_kerja = 4 then 1 else 0 end) as partime')
            ->selectRaw('sum(case when kd_status_kerja = 6 then 1 else 0 end) as thl')
            ->selectRaw('sum(case when kd_status_kerja = 7 then 1 else 0 end) as pppk')
            ->where('status_peg', 1)
            ->where(function($query) {
                $query->whereNull('kd_jenis_tenaga')
                      ->orWhere('kd_jenis_tenaga', '');
            })
            ->first();

        // Data untuk card jenis tenaga Medis (kd_jenis_tenaga = 1)
        $medis = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_status_kerja = 1 then 1 else 0 end) as pns')
            ->selectRaw('sum(case when kd_status_kerja = 2 then 1 else 0 end) as honor')
            ->selectRaw('sum(case when kd_status_kerja = 3 then 1 else 0 end) as kontrak')
            ->selectRaw('sum(case when kd_status_kerja = 4 then 1 else 0 end) as partime')
            ->selectRaw('sum(case when kd_status_kerja = 6 then 1 else 0 end) as thl')
            ->selectRaw('sum(case when kd_status_kerja = 7 then 1 else 0 end) as pppk')
            ->where('kd_jenis_tenaga', 1)
            ->where('status_peg', 1)
            ->first();

        // Data untuk card jenis tenaga Perawat-Bidan (kd_jenis_tenaga = 2)
        $perawatBidan = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_status_kerja = 1 then 1 else 0 end) as pns')
            ->selectRaw('sum(case when kd_status_kerja = 2 then 1 else 0 end) as honor')
            ->selectRaw('sum(case when kd_status_kerja = 3 then 1 else 0 end) as kontrak')
            ->selectRaw('sum(case when kd_status_kerja = 4 then 1 else 0 end) as partime')
            ->selectRaw('sum(case when kd_status_kerja = 6 then 1 else 0 end) as thl')
            ->selectRaw('sum(case when kd_status_kerja = 7 then 1 else 0 end) as pppk')
            ->where('kd_jenis_tenaga', 2)
            ->where('status_peg', 1)
            ->first();

        // Data untuk card jenis tenaga Penunjang Medis (kd_jenis_tenaga = 3)
        $penunjangMedis = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_status_kerja = 1 then 1 else 0 end) as pns')
            ->selectRaw('sum(case when kd_status_kerja = 2 then 1 else 0 end) as honor')
            ->selectRaw('sum(case when kd_status_kerja = 3 then 1 else 0 end) as kontrak')
            ->selectRaw('sum(case when kd_status_kerja = 4 then 1 else 0 end) as partime')
            ->selectRaw('sum(case when kd_status_kerja = 6 then 1 else 0 end) as thl')
            ->selectRaw('sum(case when kd_status_kerja = 7 then 1 else 0 end) as pppk')
            ->where('kd_jenis_tenaga', 3)
            ->where('status_peg', 1)
            ->first();

        // Data untuk card jenis tenaga Non-Kesehatan (kd_jenis_tenaga = 4)
        $nonKesehatan = DB::table('hrd_karyawan')
            ->selectRaw('count(kd_status_kerja) as jumlah')
            ->selectRaw('sum(case when kd_status_kerja = 1 then 1 else 0 end) as pns')
            ->selectRaw('sum(case when kd_status_kerja = 2 then 1 else 0 end) as honor')
            ->selectRaw('sum(case when kd_status_kerja = 3 then 1 else 0 end) as kontrak')
            ->selectRaw('sum(case when kd_status_kerja = 4 then 1 else 0 end) as partime')
            ->selectRaw('sum(case when kd_status_kerja = 6 then 1 else 0 end) as thl')
            ->selectRaw('sum(case when kd_status_kerja = 7 then 1 else 0 end) as pppk')
            ->where('kd_jenis_tenaga', 4)
            ->where('status_peg', 1)
            ->first();


        $pegawai = [
            'pns' => $pegawaiPns,
            'pppk' => $pppk,
            'honor' => $pegawaiHonor,
            'kontrak_blud' => $pegawaiKontrakBlud,
            'kontrak_pemko' => $pegawaiKontrakPemko,
            'part_time' => $pegawaiPartTime,
            'luar' => $pegawaiLuar,
            'thl' => $pegawaiThl,
            'total' => $totalPegawai,
        ];

        // Ambil detail untuk setiap jenis tenaga
        $jenisTenaga = [
            'medis' => [
                'data' => $medis,
                'detail' => $this->getDetailJenisTenaga(1)
            ],
            'perawat_bidan' => [
                'data' => $perawatBidan,
                'detail' => $this->getDetailJenisTenaga(2)
            ],
            'penunjang_medis' => [
                'data' => $penunjangMedis,
                'detail' => $this->getDetailJenisTenaga(3)
            ],
            'non_kesehatan' => [
                'data' => $nonKesehatan,
                'detail' => $this->getDetailJenisTenaga(4)
            ],
            'belum_ada_jenis_tenaga' => $belumAdaJenisTenaga
        ];

        // TAMBAHAN: Data untuk 3 kolom layout seperti kode lama
        $dashboardData = [
            'jenjang_pendidikan' => $this->getJenjangPendidikan(),
            'pangkat_golongan' => $this->getPangkatGolongan(),
            'data_pegawai' => $this->getDataPegawai(),
            'total_pegawai_aktif' => $this->getTotalPegawaiAktif(),
            'total_pns_pppk' => $this->getTotalPnsPppk()
        ];

        // dd($pegawai);
        // dd($pegawai['luar']['jumlah']);
        
        return view('dashboard.index', compact('pegawai', 'jenisTenaga', 'dashboardData'));
    }

    // user dashboard
    public function userDashboard()
    {
        return view('users.dashboard.index');
    }

    private function getDetailJenisTenaga($kdJenisTenaga)
    {
        $details = DB::table('hrd_jenis_tenaga_detail')
            ->where('kd_jenis_tenaga', $kdJenisTenaga)
            ->orderBy('detail_jenis_tenaga')
            ->get();

        foreach ($details as $detail) {
            $detail->statistik = DB::table('hrd_karyawan')
                ->selectRaw('sum(case when kd_status_kerja = 1 then 1 else 0 end) as pns')
                ->selectRaw('sum(case when kd_status_kerja = 2 then 1 else 0 end) as honor')
                ->selectRaw('sum(case when kd_status_kerja = 3 then 1 else 0 end) as kontrak')
                ->selectRaw('sum(case when kd_status_kerja = 4 then 1 else 0 end) as partime')
                ->selectRaw('sum(case when kd_status_kerja = 6 then 1 else 0 end) as thl')
                ->selectRaw('sum(case when kd_status_kerja = 7 then 1 else 0 end) as pppk')
                ->where('kd_detail_jenis_tenaga', $detail->kd_detail)
                ->where('kd_jenis_tenaga', $kdJenisTenaga)
                ->where('status_peg', 1)
                ->first();
        }

        return $details;
    }

    /**
     * Ambil data jenjang pendidikan untuk dashboard
     */
    private function getJenjangPendidikan()
    {
        return DB::table('hrd_jenjang_pendidikan')
            ->join('hrd_karyawan', 'hrd_karyawan.kd_pendidikan_terakhir', '=', 'hrd_jenjang_pendidikan.kd_jenjang_didik')
            ->select('hrd_jenjang_pendidikan.jenjang_didik', 'hrd_jenjang_pendidikan.nilaiindex', 'hrd_karyawan.kd_pendidikan_terakhir')
            ->where('hrd_jenjang_pendidikan.kd_jenjang_didik', '!=', 15)
            ->where('hrd_karyawan.status_peg', 1)
            ->groupBy('hrd_jenjang_pendidikan.jenjang_didik', 'hrd_jenjang_pendidikan.nilaiindex', 'hrd_karyawan.kd_pendidikan_terakhir')
            ->orderBy('hrd_jenjang_pendidikan.nilaiIndex', 'asc')
            ->get();
    }

    /**
     * Ambil data pangkat/golongan untuk dashboard
     */
    private function getPangkatGolongan()
    {
        return DB::table('hrd_golongan')
            ->join('hrd_karyawan', 'hrd_golongan.kd_gol', '=', 'hrd_karyawan.kd_gol_sekarang')
            ->select('hrd_golongan.kd_gol', DB::raw('COUNT(hrd_golongan.urut) as jumlah'))
            ->where('hrd_karyawan.kd_gol_sekarang', '!=', '-')
            ->where('hrd_karyawan.status_peg', 1)
            ->whereIn('hrd_karyawan.kd_status_kerja', [1, 7]) // PNS dan PPPK
            ->groupBy('hrd_golongan.kd_gol', 'hrd_golongan.urut')
            ->orderBy('hrd_golongan.urut')
            ->get();
    }

    /**
     * Ambil data pegawai untuk dashboard (Belum Lengkap, Mutasi, dll)
     */
    private function getDataPegawai()
    {
        // Data untuk "Belum Lengkap"
        $dataBelumLengkap = DB::table('view_tampil_karyawan')
            ->selectRaw("
                sum(case when no_ktp = '' or no_ktp is null then 1 else 0 end) as ktp,
                sum(case when kd_jenis_tenaga = '' then 1 else 0 end) as jenistenaga,
                sum(case when tempat_lahir = '' or tgl_lahir is null then 1 else 0 end) as ttl,
                sum(case when alamat = '' then 1 else 0 end) as alamat,
                sum(case when no_npwp = '' then 1 else 0 end) as npwp,
                sum(case when no_karpeg = '' and kd_status_kerja = 1 then 1 else 0 end) as nokarpeg,
                sum(case when no_askes is null or no_askes = '-' or no_askes = '' or no_askes = '0' then 1 else 0 end) as noaskes,
                sum(case when email = '' then 1 else 0 end) as email,
                sum(case when rek_bni_syariah = '' or rek_bni_syariah is null then 1 else 0 end) as bni_syariah,
                sum(case when rek_bpd_aceh = '' and kd_status_kerja = 1 then 1 else 0 end) as bpd,
                sum(case when (kd_jurusan = '0' or kd_jurusan is null) and nilaiindex > 11 then 1 else 0 end) as jurusan,
                sum(case when no_bpjs_ketenagakerjaan IS NULL then 1 else 0 end) as bpjs_ketenagakerjaan
            ")
            ->where('status_peg', 1)
            ->first();

        // Data STR dan SIP
        $noStr = DB::table('view_tampil_karyawan')
            ->leftJoin('hrd_r_str', 'view_tampil_karyawan.kd_karyawan', '=', 'hrd_r_str.kd_karyawan')
            ->whereNull('hrd_r_str.no_str')
            ->whereIn('view_tampil_karyawan.kd_jenis_tenaga', [1,2,3])
            ->where('view_tampil_karyawan.status_peg', 1)
            ->count();

        $noSip = DB::table('view_tampil_karyawan')
            ->where('status_peg', 1)
            ->where('kd_jenis_tenaga', 1)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('hrd_r_sip')
                      ->whereRaw('hrd_r_sip.kd_karyawan = view_tampil_karyawan.kd_karyawan');
            })
            ->count();

        // Data mutasi tahun ini
        $mutasiTahunIni = DB::table('view_verifikasi')
            ->where('kd_tahap_mutasi', 2)
            ->whereYear('tmt_jabatan', date('Y'))
            ->count();

        // Total ruangan aktif
        $totalRuangan = DB::table('hrd_ruangan')
            ->where('status_aktif', 1)
            ->count();

        return [
            'belum_lengkap' => $dataBelumLengkap,
            'no_str' => $noStr,
            'no_sip' => $noSip,
            'mutasi_tahun_ini' => $mutasiTahunIni,
            'total_ruangan' => $totalRuangan
        ];
    }

    /**
     * Ambil total pegawai aktif
     */
    private function getTotalPegawaiAktif()
    {
        return DB::table('hrd_karyawan')->where('status_peg', 1)->count();
    }

    /**
     * Ambil total PNS dan PPPK
     */
    private function getTotalPnsPppk()
    {
        return DB::table('hrd_karyawan')
            ->whereIn('kd_status_kerja', [1, 7])
            ->where('status_peg', 1)
            ->count();
    }
}
