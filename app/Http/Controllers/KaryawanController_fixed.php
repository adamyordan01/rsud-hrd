<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinsis = DB::connection('sqlsrv')
            ->table('dt_prop')
            ->select('kd_prop', 'prop')
            ->orderBy('prop')
            ->get();

        $kabupatens = collect();
        $kecamatans = collect();
        $kelurahans = collect();

        $jenis_kelamins = DB::connection('sqlsrv')
            ->table('dt_jenis_kelamin')
            ->select('kd_jenis_kelamin', 'jenis_kelamin')
            ->orderBy('jenis_kelamin')
            ->get();

        $warna_kulits = DB::connection('sqlsrv')
            ->table('dt_kulit')
            ->select('kd_kulit', 'kulit')
            ->orderBy('kulit')
            ->get();

        $suku_bangsas = DB::connection('sqlsrv')
            ->table('dt_suku')
            ->select('kd_suku', 'suku')
            ->orderBy('suku')
            ->get();

        $kebangsaans = DB::connection('sqlsrv')
            ->table('dt_bangsa')
            ->select('kd_bangsa', 'bangsa')
            ->orderBy('bangsa')
            ->get();

        $agamas = DB::connection('sqlsrv')
            ->table('dt_agama')
            ->select('kd_agama', 'agama')
            ->orderBy('agama')
            ->get();

        $golongan_darahs = DB::connection('sqlsrv')
            ->table('dt_golongan_darah')
            ->select('kode_gol_dar', 'gol_dar')
            ->orderBy('gol_dar')
            ->get();

        $status_nikahs = DB::connection('sqlsrv')
            ->table('dt_status_nikah')
            ->select('kd_status_nikah', 'status_nikah')
            ->orderBy('status_nikah')
            ->get();

        $status_rumahs = DB::connection('sqlsrv')
            ->table('dt_status_rumah')
            ->select('kd_status_rmh', 'status_rmh')
            ->orderBy('status_rmh')
            ->get();

        $golongans = DB::connection('sqlsrv')
            ->table('dt_golongan')
            ->select('kd_golongan', 'golongan')
            ->orderBy('golongan')
            ->get();

        $jabatan_strukturals = DB::connection('sqlsrv')
            ->table('dt_jabatan_struktural')
            ->select('kd_jabatan_struktural', 'jabatan_struktural')
            ->orderBy('jabatan_struktural')
            ->get();

        $eselons = DB::connection('sqlsrv')
            ->table('dt_eselon')
            ->select('kd_eselon', 'eselon')
            ->orderBy('eselon')
            ->get();

        $jabatan_fungsionals = DB::connection('sqlsrv')
            ->table('dt_jabatan_fungsional')
            ->select('kd_jabfung', 'jabfung')
            ->orderBy('jabfung')
            ->get();

        $pendidikans = DB::connection('sqlsrv')
            ->table('dt_pendidikan_terakhir')
            ->select('kd_pendidikan_terakhir', 'pendidikan_terakhir')
            ->orderBy('pendidikan_terakhir')
            ->get();

        // Tambahan dropdown untuk organisasi
        $divisis = DB::connection('sqlsrv')
            ->table('dt_divisi')
            ->select('kd_divisi', 'divisi')
            ->orderBy('divisi')
            ->get();

        $unit_kerjas = DB::connection('sqlsrv')
            ->table('dt_unit_kerja')
            ->select('kd_unit_kerja', 'unit_kerja')
            ->orderBy('unit_kerja')
            ->get();

        $sub_unit_kerjas = DB::connection('sqlsrv')
            ->table('dt_sub_unit_kerja')
            ->select('kd_sub_unit_kerja', 'sub_unit_kerja')
            ->orderBy('sub_unit_kerja')
            ->get();

        $ruangans = DB::connection('sqlsrv')
            ->table('dt_ruangan')
            ->select('kd_ruangan', 'ruangan')
            ->orderBy('ruangan')
            ->get();

        $jenis_tenagas = DB::connection('sqlsrv')
            ->table('dt_jenis_tenaga')
            ->select('kd_jenis_tenaga', 'jenis_tenaga')
            ->orderBy('jenis_tenaga')
            ->get();

        return view('karyawan.create', compact(
            'provinsis', 'kabupatens', 'kecamatans', 'kelurahans',
            'jenis_kelamins', 'warna_kulits', 'suku_bangsas', 'kebangsaans',
            'agamas', 'golongan_darahs', 'status_nikahs', 'status_rumahs',
            'golongans', 'jabatan_strukturals', 'eselons', 'jabatan_fungsionals',
            'pendidikans', 'divisis', 'unit_kerjas', 'sub_unit_kerjas', 'ruangans',
            'jenis_tenagas'
        ));
    }

    public function store(Request $request)
    {
        // Get max kd_karyawan
        $maxKdKaryawan = DB::connection('sqlsrv')
            ->table('hrd_karyawan')
            ->select(DB::raw('MAX(kd_karyawan) as max_kd_karyawan'))
            ->first();
        
        $kd_karyawan = empty($maxKdKaryawan->max_kd_karyawan) ? 000001 : sprintf('%06d', $maxKdKaryawan->max_kd_karyawan + 1);

        // Basic fields
        $status_kerja = $request->status_kerja;
        $jenis_pegawai = $request->jenis_pegawai;
        $gelar_depan = $request->gelar_depan;
        $nama = $request->nama;
        $gelar_belakang = $request->gelar_belakang;
        $tempat_lahir = $request->tempat_lahir;
        
        // Date parsing
        $tgl_lahir = $request->tgl_lahir ? Carbon::createFromFormat('d/m/Y', $request->tgl_lahir)->format('Y-m-d') : null;
        $tgl_lahir = in_array($tgl_lahir, ['1970-01-01', '1900-01-01']) ? null : $tgl_lahir;
        
        $ktp = $request->ktp;
        $email = $request->email;
        $alamat = $request->alamat;
        
        // Location fields
        $provinsi = $request->provinsi;
        $kabupaten = $request->kabupaten;
        $kecamatan = $request->kecamatan;
        $kelurahan = $request->kelurahan;
        
        // Organizational fields
        $divisi = $request->divisi ?? 0;
        $unit_kerja = $request->unit_kerja ?? 0;
        $sub_unit_kerja = $request->sub_unit_kerja ?? 0;
        $ruangan = $request->ruangan ?? 0;
        
        // Personal attributes
        $sex = $request->sex;
        $warna_kulit = $request->warna_kulit;
        $suku_bangsa = $request->suku_bangsa;
        $kebangsaan = $request->kebangsaan;
        $agama = $request->agama;
        $tinggi_badan = $request->tinggi_badan;
        $berat_badan = $request->berat_badan;
        $golongan_darah = $request->golongan_darah;
        $status_nikah = $request->status_nikah;
        
        // Document numbers
        $no_kartu = $request->no_kartu;
        $no_akte = $request->no_akte;
        $no_bpjs = $request->no_bpjs;
        $npwp = $request->npwp;
        $no_karpeg = $request->no_karpeg;
        $no_taspen = $request->no_taspen;
        $no_kk = $request->no_kk;
        
        // Contact info
        $nama_ibu = $request->nama_ibu;
        $no_hp = $request->no_hp ?? 0;
        $hp_alternatif = $request->hp_alternatif ?? 0;
        
        // Housing and bank
        $status_rumah = $request->status_rumah;
        $bsi = $request->bsi;
        $bpd_aceh = $request->bpd_aceh;
        $bni = $request->bni;
        $mandiri = $request->mandiri;
        $tanggungan = $request->tanggungan;
        
        // Education
        $pendidikan = $request->pendidikan;
        $jurusan = $request->jurusan;
        $tahun_lulus = $request->tahun_lulus;
        
        // Career progression
        $golongan_cpns = $request->golongan_cpns;
        $tmt_cpns = $request->tmt_cpns ? Carbon::createFromFormat('d/m/Y', $request->tmt_cpns)->format('Y-m-d') : null;
        $tmt_cpns = in_array($tmt_cpns, ['1970-01-01', '1900-01-01']) ? null : $tmt_cpns;
        
        $masa_kerja_tahun_cpns = $request->masa_kerja_tahun_cpns ?? 0;
        $masa_kerja_bulan_cpns = $request->masa_kerja_bulan_cpns ?? 0;
        
        $golongan_pns = $request->golongan_pns;
        $tmt_pns = $request->tmt_pns ? Carbon::createFromFormat('d/m/Y', $request->tmt_pns)->format('Y-m-d') : null;
        $tmt_pns = in_array($tmt_pns, ['1970-01-01', '1900-01-01']) ? null : $tmt_pns;
        
        $masa_kerja_tahun_pns = $request->masa_kerja_tahun_pns ?? 0;
        $masa_kerja_bulan_pns = $request->masa_kerja_bulan_pns ?? 0;
        
        // Jabatan
        $jabatan_struktural = $request->jabatan_struktural ?? 0;
        $tmt_jabstruk = $request->tmt_jabstruk ? Carbon::createFromFormat('d/m/Y', $request->tmt_jabstruk)->format('Y-m-d') : null;
        $tmt_jabstruk = in_array($tmt_jabstruk, ['1970-01-01', '1900-01-01']) ? null : $tmt_jabstruk;
        
        $jabatan_eselon = $request->jabatan_eselon ?? 0;
        $tmt_eselon = $request->tmt_eselon ? Carbon::createFromFormat('d/m/Y', $request->tmt_eselon)->format('Y-m-d') : null;
        $tmt_eselon = in_array($tmt_eselon, ['1970-01-01', '1900-01-01']) ? null : $tmt_eselon;
        
        $jabatan_fungsional = $request->jabatan_fungsional ?? 0;
        $tmt_jabfung = $request->tmt_jabfung ? Carbon::createFromFormat('d/m/Y', $request->tmt_jabfung)->format('Y-m-d') : null;
        $tmt_jabfung = in_array($tmt_jabfung, ['1970-01-01', '1900-01-01']) ? null : $tmt_jabfung;
        
        $gaji_berkala = $request->gaji_berkala ? Carbon::createFromFormat('d/m/Y', $request->gaji_berkala)->format('Y-m-d') : null;
        $gaji_berkala = in_array($gaji_berkala, ['1970-01-01', '1900-01-01']) ? null : $gaji_berkala;
        
        $kenaikan_pangkat = $request->kenaikan_pangkat ? Carbon::createFromFormat('d/m/Y', $request->kenaikan_pangkat)->format('Y-m-d') : null;
        $kenaikan_pangkat = in_array($kenaikan_pangkat, ['1970-01-01', '1900-01-01']) ? null : $kenaikan_pangkat;
        
        // Jenis Tenaga Logic - sesuai dengan sistem classic
        $jns_tenaga = $request->jns_tenaga;
        $jns_tenaga_sub_detail = '';
        
        // Get jenis tenaga sub detail berdasarkan sistem classic
        if ($jns_tenaga) {
            $jenisTenagaDetail = DB::connection('sqlsrv')
                ->table('dt_jenis_tenaga')
                ->where('kd_jenis_tenaga', $jns_tenaga)
                ->first();
            
            if ($jenisTenagaDetail) {
                $jns_tenaga_sub_detail = $jenisTenagaDetail->jenis_tenaga;
            }
        }

        $md5Password = md5(12345);
        $bcryptPassword = bcrypt(12345);
        $userCreated = auth()->user()->kd_karyawan;
        $now = Carbon::now();

        // Check if KTP already exists
        $ktpExists = DB::connection('sqlsrv')
            ->table('hrd_karyawan')
            ->where('no_ktp', $ktp)
            ->first();

        if ($ktpExists) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Maaf tidak dapat menggunakan nomor KTP yang sama, KTP sudah terdaftar!',
            ]);
        }

        // Insert employee data
        $insertKaryawan = DB::connection('sqlsrv')
            ->table('hrd_karyawan')
            ->insert([
                'kd_karyawan' => $kd_karyawan,
                'status_kerja' => $status_kerja,
                'jenis_pegawai' => $jenis_pegawai,
                'gelar_depan' => $gelar_depan,
                'nama' => $nama,
                'gelar_belakang' => $gelar_belakang,
                'tempat_lahir' => $tempat_lahir,
                'tgl_lahir' => $tgl_lahir,
                'no_ktp' => $ktp,
                'email' => $email,
                'alamat' => $alamat,
                'kd_propinsi' => $provinsi,
                'kd_kabupaten' => $kabupaten,
                'kd_kecamatan' => $kecamatan,
                'kd_kelurahan' => $kelurahan,
                'kd_divisi' => $divisi,
                'kd_unit_kerja' => $unit_kerja,
                'kd_sub_unit_kerja' => $sub_unit_kerja,
                'kd_ruangan' => $ruangan,
                'kd_jenis_kelamin' => $sex,
                'kd_kulit' => $warna_kulit,
                'kd_suku' => $suku_bangsa,
                'kd_bangsa' => $kebangsaan,
                'kd_agama' => $agama,
                'tinggi_badan' => $tinggi_badan,
                'berat_badan' => $berat_badan,
                'kode_gol_dar' => $golongan_darah,
                'kd_status_nikah' => $status_nikah,
                'no_karis' => $no_kartu,
                'no_akte' => $no_akte,
                'no_askes' => $no_bpjs,
                'no_npwp' => $npwp,
                'no_karpeg' => $no_karpeg,
                'no_taspen' => $no_taspen,
                'no_kk' => $no_kk,
                'nama_ibu_kandung' => $nama_ibu,
                'no_hp' => $no_hp,
                'no_hp_alternatif' => $hp_alternatif,
                'kd_status_rmh' => $status_rumah,
                'rek_bni_syariah' => $bsi,
                'rek_bpd_aceh' => $bpd_aceh,
                'rek_bni' => $bni,
                'rek_mandiri' => $mandiri,
                'tanggungan' => $tanggungan,
                'kd_gol_masuk' => $golongan_cpns,
                'tmt_gol_masuk' => $tmt_cpns,
                'kd_gol_sekarang' => $golongan_pns,
                'tmt_gol_sekarang' => $tmt_pns,
                'kd_jabatan_struktural' => $jabatan_struktural,
                'tmt_jabatan_struktural' => $tmt_jabstruk,
                'kd_eselon' => $jabatan_eselon,
                'tmt_eselon' => $tmt_eselon,
                'kd_jabfung' => $jabatan_fungsional,
                'tmt_jabfung' => $tmt_jabfung,
                'kgb' => $gaji_berkala,
                'rencana_kp' => $kenaikan_pangkat,
                'masa_kerja_thn' => $masa_kerja_tahun_pns,
                'masa_kerja_bulan' => $masa_kerja_bulan_pns,
                'kd_pendidikan_terakhir' => $pendidikan,
                'tahun_lulus' => $tahun_lulus,
                'jns_tenaga' => $jns_tenaga,
                'jns_tenaga_sub_detail' => $jns_tenaga_sub_detail,
                'password' => $md5Password,
                'foto' => 'user.png',
                'tgl_update' => $now,
                'user_update' => $userCreated,
            ]);

        // Insert log entry
        $insertLog = DB::connection('sqlsrv')
            ->table('hrd_log')
            ->insert([
                'kd_karyawan' => $kd_karyawan,
                'waktu' => $now,
                'nama_lengkap' => trim($gelar_depan . ' ' . $nama . ' ' . $gelar_belakang),
                'jenis_log' => 'Tambah Karyawan Baru',
                'keterangan' => 'Karyawan Baru dengan ID : ' . $kd_karyawan,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_created' => $userCreated
            ]);

        if ($insertKaryawan && $insertLog) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Karyawan berhasil ditambahkan!',
                'data' => [
                    'kd_karyawan' => $kd_karyawan,
                    'nama_lengkap' => trim($gelar_depan . ' ' . $nama . ' ' . $gelar_belakang)
                ]
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data karyawan!',
            ]);
        }
    }

    // Rest of the methods...
}
