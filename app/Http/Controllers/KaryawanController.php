<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $titleBreadcrumb = 'Seluruh Karyawan';

        if (request()->ajax()) {
            $karyawan = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->select([
                    'hrd_karyawan.kd_karyawan', 
                    'hrd_karyawan.gelar_depan', 
                    'hrd_karyawan.nama', 
                    'hrd_karyawan.gelar_belakang', 
                    'hrd_karyawan.nip_baru', 
                    'hrd_karyawan.tempat_lahir', 
                    'hrd_karyawan.tgl_lahir', 
                    'hrd_karyawan.no_karpeg', 
                    'hrd_karyawan.kd_jenis_kelamin', 
                    'hrd_karyawan.tmt_gol_sekarang', 
                    'hrd_karyawan.kd_gol_sekarang', 
                    'hrd_karyawan.masa_kerja_thn', 
                    'hrd_karyawan.masa_kerja_bulan', 
                    'hrd_karyawan.tmt_eselon', 
                    'hrd_karyawan.foto', 
                    'hrd_karyawan.tahun_lulus',
                    'hrd_karyawan.rek_bni_syariah',
                    'hrd_eselon.eselon', 
                    'hrd_golongan.pangkat', 
                    'hrd_ruangan.ruangan', 
                    'hrd_jenjang_pendidikan.jenjang_didik', 
                    'hrd_jurusan.jurusan', 
                    'hrd_status_kerja.status_kerja', 
                    'hrd_jenis_tenaga_sub_detail.sub_detail',
                    'hrd_karyawan.foto_square',
                ])
                ->leftJoin('sex', 'hrd_karyawan.kd_jenis_kelamin', 'sex.kode')
                ->leftJoin('hrd_eselon', 'hrd_karyawan.kd_eselon', 'hrd_eselon.kd_eselon')
                ->leftJoin('hrd_golongan', 'hrd_karyawan.kd_gol_sekarang', 'hrd_golongan.kd_gol')
                ->leftJoin('hrd_jenjang_pendidikan', 'hrd_karyawan.kd_pendidikan_terakhir', 'hrd_jenjang_pendidikan.kd_jenjang_didik')
                ->leftJoin('hrd_jurusan', 'hrd_karyawan.kd_jurusan', 'hrd_jurusan.kd_jurusan')
                ->leftJoin('hrd_status_kerja', 'hrd_karyawan.kd_status_kerja', 'hrd_status_kerja.kd_status_kerja')
                ->leftJoin('hrd_ruangan', 'hrd_karyawan.kd_ruangan', 'hrd_ruangan.kd_ruangan')
                ->leftJoin('hrd_jenis_tenaga_sub_detail', function ($join) {
                    $join->on('hrd_karyawan.kd_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_jenis_tenaga')
                        ->on('hrd_karyawan.kd_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_detail')
                        ->on('hrd_karyawan.kd_sub_detail_jenis_tenaga', 'hrd_jenis_tenaga_sub_detail.kd_sub_detail');
                })
                ->where('hrd_karyawan.status_peg', '1');

            // Ambil status filter dari parameter
            $statusFilters = [];
            
            // Jika ada parameter statuses dari AJAX (dari checkbox)
            if ($request->has('statuses') && is_array($request->statuses) && !empty($request->statuses)) {
                $statusFilters = $request->statuses;
            } 
            // Jika ada parameter status dari URL (dari dashboard)
            elseif ($request->has('status')) {
                $status = $request->status;
                $jenisPegawai = $request->jenis_pegawai;
                
                if ($status == '3' && $jenisPegawai) {
                    if ($jenisPegawai == '2') {
                        $statusFilters[] = 'blud';
                    } elseif ($jenisPegawai == '1') {
                        $statusFilters[] = 'daerah';
                    }
                } else {
                    $statusFilters[] = $status;
                }
            }

            // Apply filter jika ada
            if (!empty($statusFilters)) {
                $karyawan->where(function($query) use ($statusFilters) {
                    foreach ($statusFilters as $status) {
                        if ($status == 'blud') {
                            $query->orWhere(function($q) {
                                $q->where('hrd_karyawan.kd_status_kerja', '3')
                                ->where('hrd_karyawan.kd_jenis_peg', '2');
                            });
                        } elseif ($status == 'daerah') {
                            $query->orWhere(function($q) {
                                $q->where('hrd_karyawan.kd_status_kerja', '3')
                                ->where('hrd_karyawan.kd_jenis_peg', '1');
                            });
                        } else {
                            $query->orWhere('hrd_karyawan.kd_status_kerja', $status);
                        }
                    }
                });
            }

            return DataTables::of($karyawan)
                ->addColumn('id_pegawai', function ($row) {
                    $kd_karyawan = '<span class="fw-bold text-dark" style="font-size: 12px;">' . $row->kd_karyawan . '</span>';
                    
                    // Prioritas foto_square dulu, jika tidak ada fallback ke foto lama, lalu ke avatar
                    if ($row->foto_square) {
                        $photoUrl = \App\Helpers\PhotoHelper::getPhotoUrl($row, 'foto_square');
                        $photo = '<div class="symbol symbol-45px"><img src="' . $photoUrl . '" alt=""></div>';
                    } elseif ($row->foto && Str::startsWith($row->foto, 'rsud_') && $row->foto !== 'user.png') {
                        // Hanya tampilkan foto dari server lama jika bukan user.png
                        $photo = '<div class="symbol symbol-45px"><img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $row->foto . '" alt=""></div>';
                    } else {
                        // Gunakan blank.png dari domain lokal untuk user.png atau tidak ada foto
                        $photo = '<div class="symbol symbol-45px"><img src="' . asset('assets/media/avatars/blank.png') . '" alt=""></div>';
                    }

                    return $kd_karyawan . '<br>' . $photo;
                })
                ->addColumn('nama_lengkap', function ($row) {
                    $nama = $row->nama;
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $nama . $gelarBelakang;
                    $namaBold = '<span style="font-size: 12px; font-weight: bold;">' . $namaLengkap . '</span>';
                    $tanggal_lahir = Carbon::parse($row->tgl_lahir)->format('d-m-Y');
                    $ttl = $row->tempat_lahir . ', ' . $tanggal_lahir;
                    return $namaBold . '<br>' . $ttl . '<br>' . $row->nip_baru . '<br>' . $row->no_karpeg;
                })
                ->editColumn('jenis_kelamin', fn($row) => $row->kd_jenis_kelamin == '1' ? 'L' : 'P')
                ->addColumn('status_kerja', fn($row) => $row->status_kerja)
                ->addColumn('golongan', function ($row) {
                    $pangkat = $row->pangkat . ' / ' . $row->kd_gol_sekarang;
                    $gol = ($row->kd_gol_sekarang == '0' || $row->kd_gol_sekarang == null) 
                        ? '-' 
                        : Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y');
                    return $pangkat . '<br>' . $gol;
                })
                ->addColumn('eselon', function ($row) {
                    $eselon = $row->eselon;
                    $tmtEselon = $row->tmt_eselon ? Carbon::parse($row->tmt_eselon)->format('d-m-Y') : '';
                    return $eselon . '<br>' . $tmtEselon;
                })
                ->addColumn('pendidikan', function ($row) {
                    return $row->jenjang_didik . '<br>' . $row->jurusan . '<br>Lulus thn. ' . $row->tahun_lulus;
                })
                ->addColumn('sub_detail', function ($row) {
                    $eselon = $row->eselon;
                    $jenisTenaga = ($eselon == '-' || $eselon == null) 
                        ? 'Tenaga ' . $row->sub_detail 
                        : 'Tenaga Manajemen';
                    return strtoupper($jenisTenaga) . '<br>PADA ' . $row->ruangan;
                })
                ->addColumn('action', function ($row) {
                    return view('karyawan.columns._actions', ['karyawan' => $row]);
                })
                ->rawColumns(['id_pegawai', 'nama_lengkap', 'jenis_kelamin', 'golongan', 'eselon', 'pendidikan', 'sub_detail', 'action'])
                ->toJson();
        }

        return view('karyawan.index', compact('titleBreadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinsis = DB::connection('sqlsrv')
            ->table('propinsi')
            ->select('kd_propinsi', 'propinsi')
            ->orderBy('propinsi')
            ->get();

        $kabupatens = collect();
        $kecamatans = collect();
        $kelurahans = collect();

        // Status Kerja
        $statusKerja = DB::connection('sqlsrv')
            ->table('hrd_status_kerja')
            ->select('kd_status_kerja', 'status_kerja')
            ->where('kd_status_kerja', '!=', 5)
            ->orderBy('kd_status_kerja', 'asc')
            ->get();

        // Jenis Pegawai
        $jenisPegawai = DB::connection('sqlsrv')
            ->table('hrd_jenis_pegawai')
            ->select('kd_jenis_peg', 'jenis_peg')
            ->orderBy('jenis_peg', 'asc')
            ->get();

        // Status Pegawai
        $statusPegawai = DB::connection('sqlsrv')
            ->table('hrd_status_pegawai')
            ->select('kd_status_pegawai', 'status_pegawai')
            ->orderBy('status_pegawai')
            ->get();

        // Hardcode jenis kelamin karena tidak menggunakan tabel
        $jenis_kelamins = collect([
            (object)['kd_jenis_kelamin' => '1', 'jenis_kelamin' => 'Laki-laki'],
            (object)['kd_jenis_kelamin' => '0', 'jenis_kelamin' => 'Perempuan']
        ]);

        $warna_kulits = DB::connection('sqlsrv')
            ->table('hrd_kulit')
            ->select('kd_kulit', 'kulit')
            ->orderBy('kulit')
            ->get();

        $suku_bangsas = DB::connection('sqlsrv')
            ->table('suku')
            ->select('kd_suku', 'suku')
            ->orderBy('suku')
            ->get();

        $kebangsaans = DB::connection('sqlsrv')
            ->table('hrd_kebangsaan')
            ->select('kd_bangsa', 'kebangsaan')
            ->orderBy('kebangsaan')
            ->get();

        $agamas = DB::connection('sqlsrv')
            ->table('agama')
            ->select('kd_agama', 'agama')
            ->orderBy('agama')
            ->get();

        $golongan_darahs = DB::connection('sqlsrv')
            ->table('golongan_darah')
            ->select('kode', 'jenis')
            ->where('kode', '!=', 0)
            ->orderBy('jenis')
            ->get();

        $status_nikahs = DB::connection('sqlsrv')
            ->table('hrd_status_nikah')
            ->select('kd_status_nikah', 'status_nikah')
            ->where('kd_status_nikah', '!=', 5)
            ->orderBy('status_nikah')
            ->get();

        $status_rumahs = DB::connection('sqlsrv')
            ->table('hrd_status_rmh')
            ->select('kd_status_rmh', 'status_rmh')
            ->orderBy('status_rmh')
            ->get();

        $golongans = DB::connection('sqlsrv')
            ->table('hrd_golongan')
            ->select('kd_gol', 'pangkat')
            ->orderBy('kd_gol')
            ->get();

        $jabatan_strukturals = DB::connection('sqlsrv')
            ->table('hrd_jabatan_struktural')
            ->select('kd_jab_struk', 'jab_struk')
            ->orderBy('jab_struk')
            ->get();

        $eselons = DB::connection('sqlsrv')
            ->table('hrd_eselon')
            ->select('kd_eselon', 'eselon')
            ->orderBy('eselon')
            ->get();

        $jabatan_fungsionals = DB::connection('sqlsrv')
            ->table('hrd_jabatan_fungsional')
            ->select('kd_jab_fung', 'jab_fung')
            ->orderBy('jab_fung')
            ->get();

        $pendidikans = DB::connection('sqlsrv')
            ->table('hrd_jenjang_pendidikan')
            ->select('kd_jenjang_didik', 'jenjang_didik', 'grup_jurusan')
            ->orderBy('nilaiindex', 'DESC')
            ->get();

        // Tambahan dropdown untuk organisasi
        $divisis = DB::connection('sqlsrv')
            ->table('hrd_divisi')
            ->select('kd_divisi', 'divisi')
            ->orderBy('divisi')
            ->get();

        $unit_kerjas = DB::connection('sqlsrv')
            ->table('hrd_unit_kerja')
            ->select('kd_unit_kerja', 'unit_kerja')
            ->orderBy('unit_kerja')
            ->get();

        $sub_unit_kerjas = DB::connection('sqlsrv')
            ->table('hrd_sub_unit_kerja')
            ->select('kd_sub_unit_kerja', 'sub_unit_kerja')
            ->orderBy('sub_unit_kerja')
            ->get();

        $ruangans = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->select('kd_ruangan', 'ruangan')
            ->orderBy('ruangan')
            ->get();

        $jenis_tenagas = DB::connection('sqlsrv')
            ->table('hrd_jenis_tenaga')
            ->select('kd_jenis_tenaga', 'jenis_tenaga')
            ->orderBy('jenis_tenaga')
            ->get();

        // Assign alias untuk sesuai dengan nama di view
        $provinsi = $provinsis;
        $warnaKulit = $warna_kulits;
        $sukuBangsa = $suku_bangsas;
        $kebangsaan = $kebangsaans;
        $agama = $agamas;
        $golonganDarah = $golongan_darahs;
        $statusNikah = $status_nikahs;
        $statusRumah = $status_rumahs;
        $pendidikan = $pendidikans;
        $golongan = $golongans;
        $jabatanStruktural = $jabatan_strukturals;
        $eselon = $eselons;
        $fungsional = $jabatan_fungsionals;

        return view('karyawan.create', compact(
            'statusKerja', 'jenisPegawai', 'statusPegawai',
            'provinsi', 'kabupatens', 'kecamatans', 'kelurahans',
            'jenis_kelamins', 'warnaKulit', 'sukuBangsa', 'kebangsaan',
            'agama', 'golonganDarah', 'statusNikah', 'statusRumah',
            'golongan', 'jabatanStruktural', 'eselon', 'fungsional',
            'pendidikan', 'divisis', 'unit_kerjas', 'sub_unit_kerjas', 'ruangans',
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
                ->table('hrd_jenis_tenaga')
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

        // Insert log entry sesuai dengan format classic HRD
        $insertLog = DB::connection('sqlsrv')
            ->table('hrd_log')
            ->insert([
                'kd_log' => 1, // '1' => 'Insert data karyawan'
                'kd_karyawan' => $kd_karyawan,
                'kd_status_peg' => $status_kerja,
                'tmt_status_peg' => $now,
                'ket' => 'Karyawan Baru dengan ID : ' . $kd_karyawan,
                'no_sk' => '',
                'tgl_sk' => null
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Debug return to check if controller is called
        // return response()->json(['debug' => 'Controller show method called', 'id' => $id]);
        
        // Force refresh cache jika ada parameter refresh
        $forceRefresh = request()->has('refresh') || request()->has('_refresh');
        $cacheKey = "karyawan_{$id}_profile";
        
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }
        
        $data = Cache::remember($cacheKey, 60 * 5, function () use ($id) {
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
            // dd($karyawan->kd_status_kerja);

            $alasanList = DB::table('hrd_keterangan_nametag')->select('ID', 'KETERANGAN')->get();

            return [
                'karyawan' => $karyawan,
                'nama_lengkap' => $namaLengkap,
                'alamat' => $alamat,
                'persentase_kelengkapan' => round($persentase, 0),
                'missing_fields' => $missingFields,
                'alasan_list' => $alasanList,
                'carbon' => Carbon::class,
            ];
        });

        return view('karyawan.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $karyawan = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('kd_karyawan', $id)
            ->first();

        if (!$karyawan) {
            abort(404, 'Karyawan tidak ditemukan');
        }

        $statusKerja = DB::connection('sqlsrv')
            ->table('hrd_status_kerja')
            ->select('kd_status_kerja', 'status_kerja')
            ->where('kd_status_kerja', '!=', 5)
            ->orderBy('kd_status_kerja', 'asc')
            ->get();

        $jenisPegawai = DB::connection('sqlsrv')
            ->table('hrd_jenis_pegawai')
            ->select('kd_jenis_peg', 'jenis_peg')
            ->orderBy('jenis_peg', 'asc')
            ->get();

        $statusPegawai = DB::connection('sqlsrv')
            ->table('hrd_status_pegawai')
            ->select('kd_status_pegawai', 'status_pegawai')
            ->orderBy('kd_status_pegawai', 'asc')
            ->get();

        $warnaKulit = DB::connection('sqlsrv')
            ->table('hrd_kulit')
            ->select('kd_kulit', 'kulit')
            ->orderBy('kd_kulit', 'asc')
            ->get();

        $sukuBangsa = DB::connection('sqlsrv')
            ->table('suku')
            ->select('kd_suku', 'suku')
            ->orderBy('kd_suku', 'asc')
            ->get();

        $kebangsaan = DB::connection('sqlsrv')
            ->table('hrd_kebangsaan')
            ->select('kd_bangsa', 'kebangsaan')
            ->orderBy('kd_bangsa', 'asc')
            ->get();

        $agama = DB::connection('sqlsrv')
            ->table('agama')
            ->select('kd_agama', 'agama')
            ->orderBy('kd_agama', 'asc')
            ->get();

        $golonganDarah = DB::connection('sqlsrv')
            ->table('golongan_darah')
            ->select('kode', 'jenis')
            ->orderBy('kode', 'asc')
            ->get();

        $statusNikah = DB::connection('sqlsrv')
            ->table('hrd_status_nikah')
            ->select('kd_status_nikah', 'status_nikah')
            ->orderBy('kd_status_nikah', 'asc')
            ->get();

        $statusRumah = DB::connection('sqlsrv')
            ->table('hrd_status_rmh')
            ->select('kd_status_rmh', 'status_rmh')
            ->orderBy('kd_status_rmh', 'asc')
            ->get();

        $provinsi = DB::connection('sqlsrv')
            ->table('propinsi')
            ->select('kd_propinsi', 'propinsi')
            ->orderBy('propinsi', 'asc')
            ->get();

        $kabupaten = DB::connection('sqlsrv')
            ->table('kabupaten')
            ->select('kd_kabupaten', 'kabupaten')
            ->where('kd_propinsi', $karyawan->kd_propinsi)
            ->orderBy('kabupaten', 'asc')
            ->get();

        $kecamatan = DB::connection('sqlsrv')
            ->table('kecamatan')
            ->select('kd_kecamatan', 'kecamatan')
            ->where('kd_kabupaten', $karyawan->kd_kabupaten)
            ->orderBy('kecamatan', 'asc')
            ->get();

        $kelurahan = DB::connection('sqlsrv')
            ->table('kelurahan')
            ->select('kd_kelurahan', 'kelurahan')
            ->where('kd_kecamatan', $karyawan->kd_kecamatan)
            ->orderBy('kelurahan', 'asc')
            ->get();

        $pendidikan = DB::connection('sqlsrv')
            ->table('hrd_jenjang_pendidikan')
            ->select('kd_jenjang_didik', 'jenjang_didik', 'grup_jurusan')
            ->orderBy('urutan', 'asc')
            ->get();

        $golongan = DB::connection('sqlsrv')
            ->table('hrd_golongan')
            ->select('kd_gol', 'pangkat')
            ->orderBy('urut', 'asc')
            ->get();

        $jabatanStruktural = DB::connection('sqlsrv')
            ->table('hrd_jabatan_struktural')
            ->select('kd_jab_struk', 'jab_struk')
            ->orderBy('kd_jab_struk', 'asc')
            ->get();

        $eselon = DB::connection('sqlsrv')
            ->table('hrd_eselon')
            ->select('kd_eselon', 'eselon')
            ->orderBy('kd_eselon', 'asc')
            ->get();

        $fungsional = DB::connection('sqlsrv')
            ->table('hrd_jabatan_fungsional')
            ->select('kd_jab_fung', 'jab_fung')
            ->orderBy('kd_jab_fung', 'asc')
            ->get();

        // Hardcode jenis kelamin karena tidak menggunakan tabel
        $jenis_kelamins = collect([
            (object)['kd_jenis_kelamin' => '1', 'jenis_kelamin' => 'Laki-laki'],
            (object)['kd_jenis_kelamin' => '0', 'jenis_kelamin' => 'Perempuan']
        ]);

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
            'jenis_kelamins' => $jenis_kelamins,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementation for updating employee data
        // Similar to store method but with update instead of insert
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implementation for soft delete or status change
    }

    // AJAX methods for dependent dropdowns
    public function getKabupaten($kd_prop)
    {
        $kabupatens = DB::connection('sqlsrv')
            ->table('kabupaten')
            ->select('kd_kabupaten', 'kabupaten')
            ->where('kd_propinsi', $kd_prop)
            ->orderBy('kabupaten')
            ->get();

        return response()->json($kabupatens);
    }

    public function getKecamatan($kd_kab)
    {
        $kecamatans = DB::connection('sqlsrv')
            ->table('kecamatan')
            ->select('kd_kecamatan', 'kecamatan')
            ->where('kd_kabupaten', $kd_kab)
            ->orderBy('kecamatan')
            ->get();

        return response()->json($kecamatans);
    }

    public function getKelurahan($kd_kec)
    {
        $kelurahans = DB::connection('sqlsrv')
            ->table('kelurahan')
            ->select('kd_kelurahan', 'kelurahan')
            ->where('kd_kecamatan', $kd_kec)
            ->orderBy('kelurahan')
            ->get();

        return response()->json($kelurahans);
    }

    public function getSubUnitKerja($kd_unit_kerja)
    {
        $sub_unit_kerjas = DB::connection('sqlsrv')
            ->table('hrd_sub_unit_kerja')
            ->select('kd_sub_unit_kerja', 'sub_unit_kerja')
            ->where('kd_unit_kerja', $kd_unit_kerja)
            ->orderBy('sub_unit_kerja')
            ->get();

        return response()->json($sub_unit_kerjas);
    }

    public function getRuangan($kd_sub_unit_kerja)
    {
        $ruangans = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->select('kd_ruangan', 'ruangan')
            ->where('kd_sub_unit_kerja', $kd_sub_unit_kerja)
            ->orderBy('ruangan')
            ->get();

        return response()->json($ruangans);
    }

    public function getJurusan($id)
    {
        $jurusan = DB::connection('sqlsrv')
            ->table('hrd_jurusan')
            ->select('kd_jurusan', 'jurusan', 'grup_jurusan')
            ->where('grup_jurusan', $id)
            ->orderBy('kd_jurusan', 'asc')
            ->get();

        return response()->json($jurusan);
    }

    public function uploadPhoto(Request $request, $id)
    {
        $karyawan = Karyawan::where('kd_karyawan', $id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'foto_square' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_small' => 'nullable|image|mimes:png,jpeg,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedPhotos = [];
        $photoTypes = [
            'foto_square' => ['width' => 400, 'height' => 400, 'format' => 'jpg'],
            'foto' => ['width' => 300, 'height' => 400, 'format' => 'jpg'],
            'foto_small' => ['width' => 200, 'height' => 267, 'format' => 'png'],
        ];

        foreach ($photoTypes as $type => $config) {
            if ($request->hasFile($type)) {
                try {
                    $file = $request->file($type);
                    
                    // Generate nama file unik
                    $fileName = $id . '_' . $type . '_' . time() . '.' . $config['format'];
                    
                    // Path untuk menyimpan di disk hrd_files
                    $photoPath = 'photos/' . $fileName;
                    
                    // Proses dan resize gambar jika diperlukan
                    $image = Image::make($file);
                    
                    // Resize sesuai konfigurasi
                    $image->fit($config['width'], $config['height'], function ($constraint) {
                        $constraint->upsize();
                    });
                    
                    // Konversi format jika diperlukan
                    if ($config['format'] === 'png') {
                        $image->encode('png', 100);
                    } else {
                        $image->encode('jpg', 100);
                    }
                    
                    // Simpan ke disk hrd_files
                    Storage::disk('hrd_files')->put($photoPath, $image->stream());
                    
                    // Hapus foto lama jika ada
                    if ($karyawan->{$type}) {
                        Storage::disk('hrd_files')->delete($karyawan->{$type});
                    }
                    
                    // Update database
                    DB::table('hrd_karyawan')->where('kd_karyawan', $id)->update([
                        $type => $photoPath
                    ]);
                    
                    $uploadedPhotos[$type] = $photoPath;
                    
                } catch (\Exception $e) {
                    Log::error("Error uploading {$type}: " . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => "Gagal mengupload {$type}. Silakan coba lagi."
                    ], 500);
                }
            }
        }

        // Hapus cache terlebih dahulu
        Cache::forget("karyawan_{$id}_profile");
        
        // Refresh data karyawan setelah cache dihapus
        $updatedKaryawan = Karyawan::where('kd_karyawan', $id)->first();
        
        // Log untuk debugging
        Log::info("Upload Photo completed for karyawan {$id}", [
            'uploaded_photos' => $uploadedPhotos,
            'current_photos' => [
                'foto_square' => $updatedKaryawan->foto_square,
                'foto' => $updatedKaryawan->foto,
                'foto_small' => $updatedKaryawan->foto_small
            ]
        ]);

        // Generate URL untuk response
        $photoUrls = [];
        foreach ($photoTypes as $type => $config) {
            if ($updatedKaryawan->{$type}) {
                // Untuk disk hrd_files yang private, kita perlu route khusus untuk akses file
                $photoUrls[$type] = route('photo.show', [
                    'type' => $type,
                    'id' => $id,
                    'filename' => basename($updatedKaryawan->{$type})
                ]);
            } else {
                $photoUrls[$type] = null;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diupload.',
            'photos' => $photoUrls
        ]);
    }

    // Method helper untuk mendapatkan URL foto
    public function getPhotoUrl($karyawan, $type)
    {
        if (!$karyawan->{$type}) {
            return null;
        }
        
        // Cek apakah foto menggunakan sistem lama
        if (Str::startsWith($karyawan->{$type}, 'rsud_') || $karyawan->{$type} === 'user.png') {
            return 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->{$type};
        }
        
        // Cek apakah foto ada di disk hrd_files
        if (Storage::disk('hrd_files')->exists($karyawan->{$type})) {
            return route('photo.show', [
                'type' => $type,
                'id' => $karyawan->kd_karyawan,
                'filename' => basename($karyawan->{$type})
            ]);
        }
        
        // Fallback ke storage public jika ada
        if (Storage::disk('public')->exists($karyawan->{$type})) {
            return Storage::url($karyawan->{$type});
        }
        
        return null;
    }

    // Method untuk menampilkan foto dari disk hrd_files
    public function showPhoto($type, $id, $filename)
    {
        try {
            $karyawan = Karyawan::where('kd_karyawan', $id)->firstOrFail();
            
            // Validasi apakah user berhak akses foto ini
            // Tambahkan logic authorization sesuai kebutuhan
            
            $photoPath = 'photos/' . $filename;
            
            if (!Storage::disk('hrd_files')->exists($photoPath)) {
                abort(404);
            }
            
            $file = Storage::disk('hrd_files')->get($photoPath);
            
            // Determine MIME type based on file extension
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $mimeType = 'image/jpeg';
                    break;
                case 'png':
                    $mimeType = 'image/png';
                    break;
                case 'gif':
                    $mimeType = 'image/gif';
                    break;
                default:
                    $mimeType = 'application/octet-stream';
            }
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=31536000'); // Cache 1 tahun
                
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
