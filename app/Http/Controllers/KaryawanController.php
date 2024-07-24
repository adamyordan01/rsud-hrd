<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KaryawanController extends Controller
{
    public function index()
    {
        // dd(request()->jenis_tenaga);
        if (request()->ajax()) {
            $karyawan = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select('hrd_karyawan.kd_karyawan', 'hrd_karyawan.gelar_depan', 'hrd_karyawan.nama', 'hrd_karyawan.gelar_belakang', 'hrd_karyawan.nip_baru', 'hrd_karyawan.tempat_lahir', 'hrd_karyawan.tgl_lahir', 'hrd_karyawan.no_karpeg', 'hrd_karyawan.kd_jenis_kelamin', 'hrd_karyawan.tmt_gol_sekarang', 'hrd_karyawan.kd_gol_sekarang', 'hrd_karyawan.masa_kerja_thn', 'hrd_karyawan.masa_kerja_bulan', 'hrd_karyawan.tmt_eselon', 'hrd_karyawan.foto', 'hrd_karyawan.tahun_lulus','hrd_karyawan.rek_bni_syariah','hrd_eselon.eselon', 'hrd_golongan.pangkat', 'hrd_ruangan.ruangan', 'hrd_jenjang_pendidikan.jenjang_didik', 'hrd_jurusan.jurusan', 'hrd_status_kerja.status_kerja', 'hrd_jenis_tenaga_sub_detail.sub_detail'
                )
                ->join('sex', 'hrd_karyawan.kd_jenis_kelamin', 'sex.kode')
                ->leftJoin('hrd_eselon', 'hrd_karyawan.kd_eselon', 'hrd_eselon.kd_eselon')
                ->leftJoin('hrd_golongan', 'hrd_karyawan.kd_gol_sekarang', 'hrd_golongan.kd_gol')
                ->leftJoin('hrd_jenjang_pendidikan', 'hrd_karyawan.kd_pendidikan_terakhir', 'hrd_jenjang_pendidikan.kd_jenjang_didik')
                ->leftJoin('hrd_jurusan', 'hrd_karyawan.kd_jurusan', 'hrd_jurusan.kd_jurusan')
                ->leftJoin('hrd_status_kerja', 'hrd_karyawan.kd_status_kerja', 'hrd_status_kerja.kd_status_kerja')
                ->leftJoin('hrd_ruangan', 'hrd_karyawan.kd_ruangan', 'hrd_ruangan.kd_ruangan')
                ->leftJoin('hrd_jenis_tenaga_sub_detail', function ($join) {
                    $join->on('hrd_karyawan.kd_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_jenis_tenaga');
                    $join->on('hrd_karyawan.kd_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_detail');
                    $join->on('hrd_karyawan.kd_sub_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_sub_detail');
                })
                ->where('hrd_karyawan.status_peg', '1')
            ;

            if (request()->has('jenis_tenaga') && request()->jenis_tenaga != 'all') {
                $karyawan->where('hrd_karyawan.kd_status_kerja', request()->jenis_tenaga);
            }

            $karyawan = $karyawan->get();

            // return DataTables::of($karyawan)->toJson();

            // $karyawan = Karyawan::select(
            //         'hkar.kd_karyawan', 'hkar.gelar_depan', 'hkar.nama', 'hkar.gelar_belakang', 'hkar.nip_baru', 'hkar.tempat_lahir', 'hkar.tgl_lahir', 'hkar.no_karpeg', 'hkar.kd_jenis_kelamin', 'hkar.tmt_gol_sekarang', 'hkar.kd_gol_sekarang', 'hkar.masa_kerja_thn', 'hkar.masa_kerja_bulan', 'hkar.tmt_eselon', 'hkar.foto', 'hkar.tahun_lulus','hkar.rek_bni_syariah',
            //         'eselon.eselon',
            //         'gol.pangkat',
            //         'ruangan.ruangan',
            //         'jenjang.jenjang_didik',
            //         'jurusan.jurusan',
            //         'status_kerja.status_kerja',
            //         'sub_detail.sub_detail'
            //     )
            //     ->from('hrd_karyawan as hkar')
            //     ->join('sex as sex', 'hkar.kd_jenis_kelamin', 'sex.kode')
            //     ->leftJoin('hrd_eselon as eselon', 'hkar.kd_eselon', 'eselon.kd_eselon')
            //     ->leftJoin('hrd_golongan as gol', 'hkar.kd_gol_sekarang', 'gol.kd_gol')
            //     ->leftJoin('hrd_jenjang_pendidikan as jenjang', 'hkar.kd_pendidikan_terakhir', 'jenjang.kd_jenjang_didik')
            //     ->leftJoin('hrd_jurusan as jurusan', 'hkar.kd_jurusan', 'jurusan.kd_jurusan')
            //     ->leftJoin('hrd_status_kerja as status_kerja', 'hkar.kd_status_kerja', 'status_kerja.kd_status_kerja')
            //     ->leftJoin('hrd_ruangan as ruangan', 'hkar.kd_ruangan', 'ruangan.kd_ruangan')
            //     ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', function ($join) {
            //         $join->on('hkar.kd_jenis_tenaga', 'sub_detail.kd_jenis_tenaga');
            //         $join->on('hkar.kd_detail_jenis_tenaga', 'sub_detail.kd_detail');
            //         $join->on('hkar.kd_sub_detail_jenis_tenaga', 'sub_detail.kd_sub_detail');
            //     })
            //     ->where('hkar.status_peg', '1')
            // ;

        return DataTables::of($karyawan)
            ->addColumn('id_pegawai', function ($karyawan) {
                $kd_karyawan = '<span class="fw-bold text-dark" style="font-size: 12px;">' . $karyawan->kd_karyawan . '</span>';

                if ($karyawan->foto != null) {
                    // https://e-rsud.langsakota.go.id/hrd/user/images/profil/rsud_000002.jpg
                    // make photo rounded
                    // $photo = '<img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto . '" class="symbol symbol-35px">';
                    // <div class="symbol symbol-45px">
                    //     <img src="/metronic8/demo39/assets/media/avatars/300-6.jpg" alt="">     
                    // </div>
                    $photo = '<div class="symbol symbol-45px">
                        <img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto . '" alt="">
                    </div>';
                } else {
                    // $photo = '<img src="https://ui-avatars.com/api/?name=' . $karyawan->nama . '&color=7F9CF5&background=EBF4FF" class="symbol symbol-35px">';
                    $photo = '<div class="symbol symbol-45px">
                        <img src="https://ui-avatars.com/api/?name=' . $karyawan->nama . '&color=7F9CF5&background=EBF4FF" alt="">
                    </div>';
                }

                // $photo = '<img src="https://ui-avatars.com/api/?name=' . $karyawan->nama . '&color=7F9CF5&background=EBF4FF" class="img-fluid img-thumbnail" style="width: 50px; height: 50px;">';

                return $kd_karyawan . '<br>' . $photo;
            })
            ->addColumn('nama_lengkap', function ($karyawan) {
                $nama = $karyawan->nama;
                $gelarDepan = $karyawan->gelar_depan ? $karyawan->gelar_depan . ' ' : '';
                $gelarBelakang = $karyawan->gelar_belakang ? $karyawan->gelar_belakang : '';
                $namaLengkap = $gelarDepan . $nama . $gelarBelakang;
                $namaBold = '<span style="font-size: 12px; font-weight: bold;">' . $namaLengkap . '</span>';
                $tanggal_lahir = Carbon::parse($karyawan->tgl_lahir)->format('d-m-Y');

                $ttl = $karyawan->tempat_lahir . ', ' . $tanggal_lahir;
                $nip = $karyawan->nip_baru;
                $karpeg = $karyawan->no_karpeg;

                return $namaBold . '<br>' . $ttl . '<br>' . $nip . '<br>' . $karpeg;
            })
            ->editColumn('jenis_kelamin', function ($karyawan) {
                $sex = $karyawan->kd_jenis_kelamin;

                if ($sex == '1') {
                    $sex = 'L';
                } else {
                    $sex = 'P';
                }

                return $sex;
            })
            ->addColumn('status_kerja', function ($karyawan) {
                $status = $karyawan->status_kerja;

                return $status;
            })
            ->addColumn('golongan', function ($karyawan) {
                $pangkat = $karyawan->pangkat . ' / ' . $karyawan->kd_gol_sekarang;
                if ($karyawan->kd_gol_sekarang == '0' || $karyawan->kd_gol_sekarang == null || $karyawan->kd_gol_sekarang == '') {
                    $gol = '-';
                } else {
                    $gol = Carbon::parse($karyawan->tmt_gol_sekarang)->format('d-m-Y');
                }

                return $pangkat . '<br>' . $gol;
            })
            ->addColumn('eselon', function ($karyawan) {
                $eselon = $karyawan->eselon;
                $tmtEselon = $karyawan->tmt_eselon ? Carbon::parse($karyawan->tmt_eselon)->format('d-m-Y') : '';

                return $eselon . '<br>' . $tmtEselon;
            })
            ->addColumn('pendidikan', function ($karyawan) {
                $jenjang = $karyawan->jenjang_didik;
                $jurusan = $karyawan->jurusan;
                $lulus = $karyawan->tahun_lulus;

                return $jenjang . '<br>' . $jurusan . '<br>' . 'Lulus thn. ' . $lulus;
            })
            ->addColumn('sub_detail', function ($karyawan) {
                $eselon = $karyawan->eselon;

                if ($eselon == '-' || $eselon == null || $eselon == '') {
                    $jenisTenaga = 'Tenaga ' . $karyawan->sub_detail;
                } else {
                    $jenisTenaga = 'Tenaga Manajemen';
                }

                $jenisTenaga = strtoupper($jenisTenaga);

                return $jenisTenaga . '<br>' . 'PADA ' . $karyawan->ruangan;
            })
            ->addColumn('action', function ($karyawan) {
                return view('karyawan.columns._actions', compact('karyawan'));
            })
            ->rawColumns(['id_pegawai', 'nama_lengkap', 'jenis_kelamin', 'golongan', 'eselon', 'pendidikan', 'sub_detail', 'action'])
            ->toJson();
        }

        return view('karyawan.index');
    }

    public function create()
    {
        $statusKerja = DB::connection('sqlsrv')
            ->table('hrd_status_kerja')
            ->select('kd_status_kerja', 'status_kerja')
            ->orderBy('kd_status_kerja', 'asc')
            ->get()
        ;

        $jenisPegawai = DB::connection('sqlsrv')
            ->table('hrd_jenis_pegawai')
            ->select('kd_jenis_peg', 'jenis_peg')
            ->orderBy('kd_jenis_peg', 'asc')
            ->get()
        ;

        $statusPegawai = DB::connection('sqlsrv')
            ->table('hrd_status_pegawai')
            ->select('kd_status_pegawai', 'status_pegawai')
            ->orderBy('kd_status_pegawai', 'asc')
            ->get()
        ;

        $warnaKulit = DB::connection('sqlsrv')
            ->table('hrd_kulit')
            ->select('kd_kulit', 'kulit')
            ->orderBy('kd_kulit', 'asc')
            ->get()
        ;

        $sukuBangsa = DB::connection('sqlsrv')
            ->table('suku')
            ->select('kd_suku', 'suku')
            ->orderBy('kd_suku', 'asc')
            ->get()
        ;

        $kebangsaan = DB::connection('sqlsrv')
            ->table('hrd_kebangsaan')
            ->select('kd_bangsa', 'kebangsaan')
            ->orderBy('kd_bangsa', 'asc')
            ->get()
        ;

        $agama = DB::connection('sqlsrv')
            ->table('agama')
            ->select('kd_agama', 'agama')
            ->orderBy('kd_agama', 'asc')
            ->get()
        ;

        $golonganDarah = DB::connection('sqlsrv')
            ->table('golongan_darah')
            ->select('kode', 'jenis')
            ->orderBy('kode', 'asc')
            ->get()
        ;

        $statusNikah = DB::connection('sqlsrv')
            ->table('hrd_status_nikah')
            ->select('kd_status_nikah', 'status_nikah')
            ->orderBy('kd_status_nikah', 'asc')
            ->get()
        ;

        $statusRumah = DB::connection('sqlsrv')
            ->table('hrd_status_rmh')
            ->select('kd_status_rmh', 'status_rmh')
            ->orderBy('kd_status_rmh', 'asc')
            ->get()
        ;

        $provinsi = DB::connection('sqlsrv')
            ->table('propinsi')
            ->select('kd_propinsi', 'propinsi')
            ->orderBy('propinsi', 'asc')
            ->get()
        ;

        $pendidikan = DB::connection('sqlsrv')
            ->table('hrd_jenjang_pendidikan')
            ->select('kd_jenjang_didik', 'jenjang_didik', 'grup_jurusan')
            ->orderBy('urutan', 'asc')
            ->get()
        ;

        $golongan = DB::connection('sqlsrv')
            ->table('hrd_golongan')
            ->select('kd_gol', 'pangkat')
            ->orderBy('urut', 'asc')
            ->get()
        ;

        $jabatanStruktural = DB::connection('sqlsrv')
            ->table('hrd_jabatan_struktural')
            ->select('kd_jab_struk', 'jab_struk')
            ->orderBy('kd_jab_struk', 'asc')
            ->get()
        ;

        $eselon = DB::connection('sqlsrv')
            ->table('hrd_eselon')
            ->select('kd_eselon', 'eselon')
            ->orderBy('kd_eselon', 'asc')
            ->get()
        ;

        $fungsional = DB::connection('sqlsrv')
            ->table('hrd_jabatan_fungsional')
            ->select('kd_jab_fung', 'jab_fung')
            ->orderBy('kd_jab_fung', 'asc')
            ->get()
        ;


        return view('karyawan.create', [
            'statusKerja' => $statusKerja,
            'jenisPegawai' => $jenisPegawai,
            'statusPegawai' => $statusPegawai,
            'warnaKulit' => $warnaKulit,
            'sukuBangsa' => $sukuBangsa,
            'kebangsaan' => $kebangsaan,
            'agama' => $agama,
            'golonganDarah' => $golonganDarah,
            'statusNikah' => $statusNikah,
            'statusRumah' => $statusRumah,
            'provinsi' => $provinsi,
            'pendidikan' => $pendidikan,
            'golongan' => $golongan,
            'jabatanStruktural' => $jabatanStruktural,
            'eselon' => $eselon,
            'fungsional' => $fungsional,
        ]);
    }

    public function store(Request $request)
    {
        // dd(auth()->user()->kd_karyawan);
        // dd($request->all());
        // check max of kd_karyawan
        $maxKdKaryawan = DB::connection('sqlsrv')
            ->table('hrd_karyawan')
            ->select(DB::raw('MAX(kd_karyawan) as max_kd_karyawan'))
            ->first()
        ;
        $kd_karyawan = empty($maxKdKaryawan->max_kd_karyawan) ? 000001 : sprintf('%06d', $maxKdKaryawan->max_kd_karyawan + 1);

        $status_kerja = $request->status_kerja;
        $jenis_pegawai = $request->jenis_pegawai;
        $status_pegawai = $request->status_pegawai;

        $tmt = $request->tmt ? Carbon::createFromFormat('d/m/Y', $request->tmt)->format('Y-m-d') : null;
        $tmt = in_array($tmt, ['1970-01-01', '1900-01-01']) ? null : $tmt;

        $no_sk = $request->no_sk;
        $keterangan = $request->keterangan;

        $tgl_sk = $request->tgl_sk ? Carbon::createFromFormat('d/m/Y', $request->tgl_sk)->format('Y-m-d') : null;
        $tgl_sk = in_array($tgl_sk, ['1970-01-01', '1900-01-01']) ? null : $tgl_sk;

        $nip_lama = $request->nip_lama;
        $nip = $request->nip;
        $gelar_depan = $request->gelar_depan;
        $nama = $request->nama;
        $gelar_belakang = $request->gelar_belakang;
        $tempat_lahir = $request->tempat_lahir;
        // $tgl_lahir = $request->tgl_lahir;
        // $formattedTglLahir = Carbon::parse($tgl_lahir)->format('Y-m-d');
        // $formattedTglLahir = (empty($formattedTglLahir) || $formattedTglLahir == '1970-01-01' || $formattedTglLahir == '1900-01-01') ? null : $formattedTglLahir;
        $tgl_lahir = $request->tgl_lahir ? Carbon::createFromFormat('d/m/Y', $request->tgl_lahir)->format('Y-m-d') : null;
        $tgl_lahir = in_array($tgl_lahir, ['1970-01-01', '1900-01-01']) ? null : $tgl_lahir;
        $ktp = $request->ktp;
        $email = $request->email;
        $alamat = $request->alamat;
        $provinsi = $request->provinsi;
        $kabupaten = $request->kabupaten;
        $kecamatan = $request->kecamatan;
        $kelurahan = $request->kelurahan;
        $sex = $request->sex ?? null;
        $warna_kulit = $request->warna_kulit;
        $suku_bangsa = $request->suku_bangsa;
        $kebangsaan = $request->kebangsaan;
        $agama = $request->agama;
        $tinggi_badan = $request->tinggi_badan;
        $berat_badan = $request->berat_badan;
        $golongan_darah = $request->golongan_darah;
        $status_nikah = $request->status_nikah;
        $no_kartu = $request->no_kartu; // karis/karsu
        $no_akte = $request->no_akte;
        $no_bpjs = $request->no_bpjs;
        $npwp = $request->npwp;
        $no_karpeg = $request->no_karpeg;
        $no_taspen = $request->no_taspen;
        $no_kk = $request->no_kk;
        $nama_ibu = $request->nama_ibu;
        $no_hp = $request->no_hp ?? 0;
        $hp_alternatif = $request->hp_alternatif ?? 0;
        $status_rumah = $request->status_rumah;
        $bsi = $request->bsi;
        $bpd_aceh = $request->bpd_aceh;
        $tanggungan = $request->tanggungan;
        $pendidikan = $request->pendidikan;
        $jurusan = $request->jurusan;
        $tahun_lulus = $request->tahun_lulus;
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

        $md5Password = md5(12345);
        $bcryptPassword = bcrypt(12345);

        $userCreated = auth()->user()->kd_karyawan;

        $now = Carbon::now();

        // check if ktp already exists in database then return error message in json
        $ktpExists = DB::connection('sqlsrv')
            ->table('hrd_karyawan')
            ->where('no_ktp', $ktp)
            ->first()
        ;

        if ($ktpExists) {
            return response()->json([
                // kode error untuk ktp sudah terdaftar berapa?
                'code' => 400,
                'status' => 'error',
                'message' => 'Maaf tidak dapat menggunakan nomor KTP yang sama, KTP sudah terdaftar!',
            ]);
        } else {
            $insertKaryawan = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->insert([
                    'kd_karyawan' => $kd_karyawan,
                    'no_absen' => 0,
                    'kd_status_kerja' => $status_kerja,
                    'kd_jenis_tenaga' => $jenis_pegawai,
                    'status_peg' => $status_pegawai,
                    'tgl_keluar_pensiun' => $tmt,
                    // 'no_sk' => $no_sk,
                    // 'tgl_sk' => $tgl_sk,
                    'nip_lama' => $nip_lama,
                    'nip_baru' => $nip,
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
                    'password' => $md5Password,
                    'foto' => 'user.png',
                    'tgl_update' => $now,
                    'user_update' => $userCreated,
                    'masa_kerja_thn_cpns' => $masa_kerja_tahun_cpns,
                    'masa_kerja_bln_cpns' => $masa_kerja_bulan_cpns,
                    'kd_jurusan' => $jurusan,
                ])
            ;

            if ($insertKaryawan) {
                // insert to hrd_log
                $insertLog = DB::connection('sqlsrv')
                    ->table('hrd_log')
                    ->insert([
                        'kd_log' => 1, // '1' => 'Insert data karyawan'
                        'kd_karyawan' => $kd_karyawan,
                        'kd_status_peg' => $status_pegawai,
                        'tmt_status_peg' => $tmt,
                        'ket' => $keterangan,
                        'no_sk' => $no_sk,
                        'tgl_sk' => $tgl_sk,
                    ])
                ;
                
                if ($insertLog) {
                    return response()->json([
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Data karyawan berhasil disimpan!',
                    ]);
                } else {
                    return response()->json([
                        'code' => 422,
                        'status' => 'error',
                        'message' => 'Data karyawan gagal disimpan!',
                    ]);
                }
            }
        }
    }

    public function edit($id)
    {
        // select all data from hrd_karyawan where kd_karyawan = $id
        // $karyawan = DB::connection('sqlsrv')
        //     ->table('hrd_karyawan')
        //     ->select(
        //         '*',
        //     )
        //     ->where('kd_karyawan', $id)
        //     ->first()
        // ;
        // select all data from view view_tampil_karyawan where kd_karyawan = $id
        $karyawan = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('kd_karyawan', $id)
            ->first()
        ;
        
        $statusKerja = DB::connection('sqlsrv')
            ->table('hrd_status_kerja')
            ->select('kd_status_kerja', 'status_kerja')
            ->orderBy('kd_status_kerja', 'asc')
            ->get()
        ;

        $jenisPegawai = DB::connection('sqlsrv')
            ->table('hrd_jenis_pegawai')
            ->select('kd_jenis_peg', 'jenis_peg')
            ->orderBy('kd_jenis_peg', 'asc')
            ->get()
        ;

        $statusPegawai = DB::connection('sqlsrv')
            ->table('hrd_status_pegawai')
            ->select('kd_status_pegawai', 'status_pegawai')
            ->orderBy('kd_status_pegawai', 'asc')
            ->get()
        ;

        $warnaKulit = DB::connection('sqlsrv')
            ->table('hrd_kulit')
            ->select('kd_kulit', 'kulit')
            ->orderBy('kd_kulit', 'asc')
            ->get()
        ;

        $sukuBangsa = DB::connection('sqlsrv')
            ->table('suku')
            ->select('kd_suku', 'suku')
            ->orderBy('kd_suku', 'asc')
            ->get()
        ;

        $kebangsaan = DB::connection('sqlsrv')
            ->table('hrd_kebangsaan')
            ->select('kd_bangsa', 'kebangsaan')
            ->orderBy('kd_bangsa', 'asc')
            ->get()
        ;

        $agama = DB::connection('sqlsrv')
            ->table('agama')
            ->select('kd_agama', 'agama')
            ->orderBy('kd_agama', 'asc')
            ->get()
        ;

        $golonganDarah = DB::connection('sqlsrv')
            ->table('golongan_darah')
            ->select('kode', 'jenis')
            ->orderBy('kode', 'asc')
            ->get()
        ;

        $statusNikah = DB::connection('sqlsrv')
            ->table('hrd_status_nikah')
            ->select('kd_status_nikah', 'status_nikah')
            ->orderBy('kd_status_nikah', 'asc')
            ->get()
        ;

        $statusRumah = DB::connection('sqlsrv')
            ->table('hrd_status_rmh')
            ->select('kd_status_rmh', 'status_rmh')
            ->orderBy('kd_status_rmh', 'asc')
            ->get()
        ;

        $provinsi = DB::connection('sqlsrv')
            ->table('propinsi')
            ->select('kd_propinsi', 'propinsi')
            ->orderBy('propinsi', 'asc')
            ->get()
        ;

        $kabupaten = DB::connection('sqlsrv')
            ->table('kabupaten')
            ->select('kd_kabupaten', 'kabupaten')
            ->where('kd_propinsi', $karyawan->kd_propinsi)
            ->orderBy('kabupaten', 'asc')
            ->get()
        ;

        $kecamatan = DB::connection('sqlsrv')
            ->table('kecamatan')
            ->select('kd_kecamatan', 'kecamatan')
            ->where('kd_kabupaten', $karyawan->kd_kabupaten)
            ->orderBy('kecamatan', 'asc')
            ->get()
        ;

        $kelurahan = DB::connection('sqlsrv')
            ->table('kelurahan')
            ->select('kd_kelurahan', 'kelurahan')
            ->where('kd_kecamatan', $karyawan->kd_kecamatan)
            ->orderBy('kelurahan', 'asc')
            ->get()
        ;

        $pendidikan = DB::connection('sqlsrv')
            ->table('hrd_jenjang_pendidikan')
            ->select('kd_jenjang_didik', 'jenjang_didik', 'grup_jurusan')
            ->orderBy('urutan', 'asc')
            ->get()
        ;

        $golongan = DB::connection('sqlsrv')
            ->table('hrd_golongan')
            ->select('kd_gol', 'pangkat')
            ->orderBy('urut', 'asc')
            ->get()
        ;

        $jabatanStruktural = DB::connection('sqlsrv')
            ->table('hrd_jabatan_struktural')
            ->select('kd_jab_struk', 'jab_struk')
            ->orderBy('kd_jab_struk', 'asc')
            ->get()
        ;

        $eselon = DB::connection('sqlsrv')
            ->table('hrd_eselon')
            ->select('kd_eselon', 'eselon')
            ->orderBy('kd_eselon', 'asc')
            ->get()
        ;

        $fungsional = DB::connection('sqlsrv')
            ->table('hrd_jabatan_fungsional')
            ->select('kd_jab_fung', 'jab_fung')
            ->orderBy('kd_jab_fung', 'asc')
            ->get()
        ;

        // $kd_didik = $isi['KD_PENDIDIKAN_TERAKHIR'];
        // $sttgrup = sqlsrv_query($konek, "select GRUP_JURUSAN from HRD_JENJANG_PENDIDIKAN where KD_JENJANG_DIDIK = '$kd_didik'");
        // $hasil = sqlsrv_fetch_array($sttgrup);
        // $grup = $hasil['GRUP_JURUSAN'];
        // $sttjenjang = sqlsrv_query($konek, "select * from HRD_JURUSAN where GRUP_JURUSAN = '$grup' order by KD_JURUSAN ASC");
        // while ($data = sqlsrv_fetch_array($sttjenjang)){
        // echo "<option value=".$data['KD_JURUSAN'].">".$data['JURUSAN']."</option>";
        // }

        return view('karyawan.edit', [
            'karyawan' => $karyawan,
            'statusKerja' => $statusKerja,
            'jenisPegawai' => $jenisPegawai,
            'statusPegawai' => $statusPegawai,
            'warnaKulit' => $warnaKulit,
            'sukuBangsa' => $sukuBangsa,
            'kebangsaan' => $kebangsaan,
            'agama' => $agama,
            'golonganDarah' => $golonganDarah,
            'statusNikah' => $statusNikah,
            'statusRumah' => $statusRumah,
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'kecamatan' => $kecamatan,
            'kelurahan' => $kelurahan,
            'pendidikan' => $pendidikan,
            'golongan' => $golongan,
            'jabatanStruktural' => $jabatanStruktural,
            'eselon' => $eselon,
            'fungsional' => $fungsional,
            // 'jurusan' => $jurusan,
        ]);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $status_kerja = $request->status_kerja;
        $jenis_pegawai = $request->jenis_pegawai;
        $status_pegawai = $request->status_pegawai;

        $tmt = $request->tmt ? Carbon::createFromFormat('d/m/Y', $request->tmt)->format('Y-m-d') : null;
        $tmt = in_array($tmt, ['1970-01-01', '1900-01-01']) ? null : $tmt;

        $no_sk = $request->no_sk;
        $keterangan = $request->keterangan;

        $tgl_sk = $request->tgl_sk ? Carbon::createFromFormat('d/m/Y', $request->tgl_sk)->format('Y-m-d') : null;
        $tgl_sk = in_array($tgl_sk, ['1970-01-01', '1900-01-01']) ? null : $tgl_sk;

        $nip_lama = $request->nip_lama;
        $nip = $request->nip;
        $gelar_depan = $request->gelar_depan;
        $nama = $request->nama;
        $gelar_belakang = $request->gelar_belakang;
        $tempat_lahir = $request->tempat_lahir;
        $tgl_lahir = $request->tgl_lahir ? Carbon::createFromFormat('d/m/Y', $request->tgl_lahir)->format('Y-m-d') : null;
        $tgl_lahir = in_array($tgl_lahir, ['1970-01-01', '1900-01-01']) ? null : $tgl_lahir;
        $ktp = $request->ktp;
        $email = $request->email;
        $alamat = $request->alamat;
        $provinsi = $request->provinsi;
        $kabupaten = $request->kabupaten;
        $kecamatan = $request->kecamatan;
        $kelurahan = $request->kelurahan;
        $sex = $request->sex ?? null;
        $warna_kulit = $request->warna_kulit;
        $suku_bangsa = $request->suku_bangsa;
        $kebangsaan = $request->kebangsaan;
        $agama = $request->agama;
        $tinggi_badan = $request->tinggi_badan;
        $berat_badan = $request->berat_badan;
        $golongan_darah = $request->golongan_darah;
        $status_nikah = $request->status_nikah;
        $no_kartu = $request->no_kartu; // karis/karsu
        $no_akte = $request->no_akte;
        $no_bpjs = $request->no_bpjs;
        $npwp = $request->npwp;
        $no_karpeg = $request->no_karpeg;
        $no_taspen = $request->no_taspen;
        $no_kk = $request->no_kk;
        $nama_ibu = $request->nama_ibu;
        $no_hp = $request->no_hp ?? 0;
        $hp_alternatif = $request->hp_alternatif ?? 0;
        $status_rumah = $request->status_rumah;
        $bsi = $request->bsi;
        $bpd_aceh = $request->bpd_aceh;
        $tanggungan = $request->tanggungan;
        $pendidikan = $request->pendidikan;
        $jurusan = $request->jurusan;
        $tahun_lulus = $request->tahun_lulus;
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

        $md5Password = md5(12345);
        $bcryptPassword = bcrypt(12345);

        $userCreated = auth()->user()->kd_karyawan;

        $now = Carbon::now();

        $updateKaryawan = DB::connection('sqlsrv')
            ->table('hrd_karyawan')
            ->where('kd_karyawan', $id)
            ->update([
                'kd_status_kerja' => $status_kerja,
                'kd_jenis_tenaga' => $jenis_pegawai,
                'status_peg' => $status_pegawai,
                'tgl_keluar_pensiun' => $tmt,
                // 'no_sk' => $no_sk,
                // 'tgl_sk' => $tgl_sk,
                'nip_lama' => $nip_lama,
                'nip_baru' => $nip,
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
                'password' => $md5Password,
                'foto' => 'user.png',
                'tgl_update' => $now,
                'user_update' => $userCreated,
                'masa_kerja_thn_cpns' => $masa_kerja_tahun_cpns,
                'masa_kerja_bln_cpns' => $masa_kerja_bulan_cpns,
                'kd_jurusan' => $jurusan,
            ])
        ;

        // max log id where kd_karyawan = $id
        $maxLogId = DB::connection('sqlsrv')
            ->table('hrd_log')
            ->where('kd_karyawan', $id)
            ->max('kd_log')
        ;

        if ($updateKaryawan) {
            // checkk if $maxLogId is > 0 update hrd_log else insert hrd_log

            if ($maxLogId > 0) {
                $updateLog = DB::connection('sqlsrv')
                    ->table('hrd_log')
                    ->where('kd_karyawan', $id)
                    ->where('kd_log', $maxLogId)
                    ->update([
                        'tmt_status_peg' => $tmt,
                        'ket' => $keterangan,
                        'kd_status_peg' => $status_pegawai,
                        'tgl_sk' => $tgl_sk,
                        'no_sk' => $no_sk,
                    ])
                ;
            } else {
                $insertLog = DB::connection('sqlsrv')
                    ->table('hrd_log')
                    ->insert([
                        'kd_log' => 1, // '1' => 'Insert data karyawan'
                        'kd_karyawan' => $id,
                        'kd_status_peg' => $status_pegawai,
                        'tmt_status_peg' => $tmt,
                        'ket' => $keterangan,
                        'no_sk' => $no_sk,
                        'tgl_sk' => $tgl_sk,
                    ])
                ;
            }

            if ($updateLog) {
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Data karyawan berhasil diupdate!',
                ]);
            } else if ($insertLog   ) {
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Data karyawan berhasil diupdate!',
                ]);
            } else {
                return response()->json([
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'Data karyawan gagal diupdate!',
                ]);
            }
        }
    }

    public function getJurusan($id)
    {
        $jurusan = DB::connection('sqlsrv')
            ->table('hrd_jurusan')
            ->select('kd_jurusan', 'jurusan', 'grup_jurusan')
            ->where('grup_jurusan', $id)
            ->orderBy('kd_jurusan', 'asc')
            ->get()
        ;

        return response()->json($jurusan);
    }
}
