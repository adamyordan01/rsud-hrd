<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KaryawanLuarController extends Controller
{
    public function index(Request $request)
    {
        $titleBreadcrumb = 'Data Pegawai Luar RS';

        if (request()->ajax()) {
            try {
                $karyawan = DB::connection('sqlsrv')
                    ->table('hrd_karyawan_luar')
                    ->select([
                        'kd_karyawan as kd_peg_luar', 
                        'gelar_depan', 
                        'nama', 
                        'gelar_belakang', 
                        'tempat_lahir', 
                        'tgl_lahir', 
                        'kd_jenis_kelamin as sex', 
                        'foto', 
                        'rek_bpd_aceh as bpd_aceh',
                        'rek_bni as bsi',
                    ])
                    ->where('status_peg', '1')
                    ->where('kd_status_kerja', '5'); // Status kerja pegawai luar

                return DataTables::of($karyawan)
                    ->addColumn('id_pegawai', function ($row) {
                        $kd_karyawan = '<span class="fw-bold text-dark" style="font-size: 12px;">' . ($row->kd_peg_luar ?? 'N/A') . '</span>';
                        
                        // Default photo untuk pegawai luar
                        $photo = $row->foto && $row->foto !== 'user.png' 
                            ? '<div class="symbol symbol-45px"><img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $row->foto . '" alt=""></div>'
                            : '<div class="symbol symbol-45px"><img src="https://ui-avatars.com/api/?name=' . urlencode($row->nama ?? 'User') . '&color=7F9CF5&background=EBF4FF" alt=""></div>';

                        return $kd_karyawan . '<br>' . $photo;
                    })
                    ->addColumn('nama_lengkap', function ($row) {
                        $namaLengkap = '';
                        if (!empty($row->gelar_depan)) {
                            $namaLengkap .= $row->gelar_depan . ' ';
                        }
                        $namaLengkap .= $row->nama ?? '';
                        if (!empty($row->gelar_belakang)) {
                            $namaLengkap .= $row->gelar_belakang;
                        }
                        $namaBold = '<span style="font-size: 12px; font-weight: bold;">' . $namaLengkap . '</span>';
                        
                        if ($row->tempat_lahir && $row->tgl_lahir) {
                            try {
                                $tanggal_lahir = Carbon::parse($row->tgl_lahir)->format('d-m-Y');
                                $ttl = $row->tempat_lahir . ', ' . $tanggal_lahir;
                                return $namaBold . '<br>' . $ttl;
                            } catch (\Exception $e) {
                                return $namaBold;
                            }
                        }
                        return $namaBold;
                    })
                    ->editColumn('jenis_kelamin', function($row) {
                        return ($row->sex ?? '2') == '1' ? 'L' : 'P';
                    })
                    ->addColumn('jenis_pegawai', function ($row) {
                        return 'Pegawai Luar'; // Default untuk pegawai luar
                    })
                    ->addColumn('rekening_bpd', function ($row) {
                        return $row->bpd_aceh ?? '-';
                    })
                    ->addColumn('rekening_bsi', function ($row) {
                        return $row->bsi ?? '-';
                    })
                    ->addColumn('action', function ($row) {
                        return view('karyawan-luar.columns._actions', ['karyawan' => $row]);
                    })
                    ->rawColumns(['id_pegawai', 'nama_lengkap', 'jenis_kelamin', 'jenis_pegawai', 'rekening_bpd', 'rekening_bsi', 'action'])
                    ->toJson();

            } catch (\Exception $e) {
                Log::error('Error in KaryawanLuarController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Internal Server Error'], 500);
            }
        }

        return view('karyawan-luar.index', compact('titleBreadcrumb'));
    }

    public function create()
    {
        $statusKerja = DB::connection('sqlsrv')
            ->table('hrd_status_kerja')
            ->select('kd_status_kerja', 'status_kerja')
            ->where('kd_status_kerja', '5') // Khusus pegawai luar
            ->orderBy('kd_status_kerja', 'asc')
            ->get();

        $jenisPegawai = DB::connection('sqlsrv')
            ->table('hrd_jenis_pegawai')
            ->select('kd_jenis_peg', 'jenis_peg')
            ->whereNotIn('kd_jenis_peg', [2]) // Exclude tertentu
            ->orderBy('jenis_peg', 'asc')
            ->get();

        $statusPegawai = DB::connection('sqlsrv')
            ->table('hrd_status_pegawai')
            ->select('kd_status_pegawai', 'status_pegawai')
            ->whereNotIn('kd_status_pegawai', ['3', '4'])
            ->orderBy('status_pegawai', 'asc')
            ->get();

        $warnaKulit = DB::connection('sqlsrv')
            ->table('hrd_kulit')
            ->select('kd_kulit', 'kulit')
            ->orderBy('kulit', 'asc')
            ->get();

        $sukuBangsa = DB::connection('sqlsrv')
            ->table('suku')
            ->select('kd_suku', 'suku')
            ->orderBy('suku', 'asc')
            ->get();

        $kebangsaan = DB::connection('sqlsrv')
            ->table('hrd_kebangsaan')
            ->select('kd_bangsa', 'kebangsaan')
            ->orderBy('kebangsaan', 'asc')
            ->get();

        $agama = DB::connection('sqlsrv')
            ->table('agama')
            ->select('kd_agama', 'agama')
            ->orderBy('agama', 'asc')
            ->get();

        $golonganDarah = DB::connection('sqlsrv')
            ->table('golongan_darah')
            ->select('kode', 'jenis')
            ->where('kode', '!=', '0')
            ->orderBy('jenis', 'asc')
            ->get();

        $provinsi = DB::connection('sqlsrv')
            ->table('propinsi')
            ->select('kd_propinsi', 'propinsi')
            ->orderBy('propinsi', 'asc')
            ->get();

        return view('karyawan-luar.create', [
            'statusKerja' => $statusKerja,
            'jenisPegawai' => $jenisPegawai,
            'statusPegawai' => $statusPegawai,
            'warnaKulit' => $warnaKulit,
            'sukuBangsa' => $sukuBangsa,
            'kebangsaan' => $kebangsaan,
            'agama' => $agama,
            'golonganDarah' => $golonganDarah,
            'provinsi' => $provinsi,
        ]);
    }

    public function store(Request $request)
    {
        // Generate kode karyawan dengan format RS0001, RS0002, dst
        $maxKdKaryawan = DB::connection('sqlsrv')
            ->table('hrd_karyawan_luar')
            ->select(DB::raw('MAX(RIGHT(kd_karyawan, 4)) as max_kd'))
            ->first();
        
        $nextNumber = empty($maxKdKaryawan->max_kd) ? 1 : $maxKdKaryawan->max_kd + 1;
        $kd_peg_luar = "RS" . sprintf('%04d', $nextNumber);

        $status_kerja = $request->status_kerja;
        $jenis_pegawai = $request->jenis_pegawai;
        $status_pegawai = $request->status_pegawai;

        $tmt = $request->tmt ? Carbon::createFromFormat('d/m/Y', $request->tmt)->format('Y-m-d') : null;
        $tmt = in_array($tmt, ['1970-01-01', '1900-01-01']) ? null : $tmt;

        $tmt_akhir = $request->tmt_akhir ? Carbon::createFromFormat('d/m/Y', $request->tmt_akhir)->format('Y-m-d') : null;
        $tmt_akhir = in_array($tmt_akhir, ['1970-01-01', '1900-01-01']) ? null : $tmt_akhir;

        $no_sk = $request->no_sk;
        $keterangan = $request->keterangan;

        $tgl_sk = $request->tgl_sk ? Carbon::createFromFormat('d/m/Y', $request->tgl_sk)->format('Y-m-d') : null;
        $tgl_sk = in_array($tgl_sk, ['1970-01-01', '1900-01-01']) ? null : $tgl_sk;

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
        $npwp = $request->npwp;
        $no_kk = $request->no_kk;
        $nama_ibu = $request->nama_ibu;
        $no_hp = $request->no_hp ?? 0;
        $hp_alternatif = $request->hp_alternatif ?? 0;
        $bsi = $request->bsi;
        $bpd_aceh = $request->bpd_aceh;

        $md5Password = md5(12345);
        $userCreated = auth()->user()->kd_karyawan;
        $now = Carbon::now();

        // Check if ktp already exists
        if ($ktp) {
            $ktpExists = DB::connection('sqlsrv')
                ->table('hrd_karyawan_luar')
                ->where('no_ktp', $ktp)
                ->first();

            if ($ktpExists) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Maaf tidak dapat menggunakan nomor KTP yang sama, KTP sudah terdaftar!',
                ]);
            }
        }

        $insertKaryawan = DB::connection('sqlsrv')
            ->table('hrd_karyawan_luar')
            ->insert([
                'kd_karyawan' => $kd_peg_luar,
                'gelar_depan' => $gelar_depan,
                'nama' => $nama,
                'gelar_belakang' => $gelar_belakang,
                'tempat_lahir' => $tempat_lahir,
                'tgl_lahir' => $tgl_lahir,
                'no_ktp' => $ktp,
                'alamat' => $alamat,
                'kd_propinsi' => $provinsi,
                'kd_kabupaten' => $kabupaten,
                'kd_kecamatan' => $kecamatan,
                'kd_kelurahan' => $kelurahan,
                'kd_jenis_kelamin' => $sex,
                'kd_kulit' => $warna_kulit,
                'tinggi_badan' => $tinggi_badan,
                'berat_badan' => $berat_badan,
                'kode_gol_dar' => $golongan_darah,
                'kd_suku' => $suku_bangsa,
                'kd_agama' => $agama,
                'kd_bangsa' => $kebangsaan,
                'no_npwp' => $npwp,
                'no_hp' => $no_hp,
                'no_hp_alternatif' => $hp_alternatif,
                'kd_status_kerja' => $status_kerja,
                'rek_bni' => $bsi,
                'email' => $email,
                'status_peg' => $status_pegawai,
                'tmt_gol_sekarang' => $tmt,
                'tmt_akhir' => $tmt_akhir,
                'password' => $md5Password,
                'foto' => 'user.png',
                'no_kk' => $no_kk,
                'nama_ibu_kandung' => $nama_ibu,
                'tgl_update' => $now,
                'user_update' => $userCreated,
                'rek_bpd_aceh' => $bpd_aceh,
                'kd_jenis_peg' => $jenis_pegawai,
            ]);

        if ($insertKaryawan) {
            // Insert to hrd_log
            $insertLog = DB::connection('sqlsrv')
                ->table('hrd_log')
                ->insert([
                    'kd_log' => 1, // '1' => 'Insert data karyawan'
                    'kd_karyawan' => $kd_peg_luar,
                    'kd_status_peg' => $status_pegawai,
                    'tmt_status_peg' => $tmt, // TMT mulai kerja
                    'ket' => $keterangan,
                    'no_sk' => $no_sk,
                    'tgl_sk' => $tgl_sk,
                ]);
            
            if ($insertLog) {
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Data pegawai luar berhasil disimpan!',
                ]);
            } else {
                return response()->json([
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'Data pegawai luar gagal disimpan!',
                ]);
            }
        }

        return response()->json([
            'code' => 422,
            'status' => 'error',
            'message' => 'Data pegawai luar gagal disimpan!',
        ]);
    }

    public function show($id)
    {
        $karyawanLuar = DB::connection('sqlsrv')
            ->table('hrd_karyawan_luar')
            ->select([
                'kd_karyawan as kd_peg_luar',
                'nama',
                'gelar_depan',
                'gelar_belakang',
                'tempat_lahir',
                'tgl_lahir',
                'kd_jenis_kelamin as sex',
                'no_hp',
                'email',
                'alamat',
                'no_ktp as ktp',
                'no_kk',
                'nama_ibu_kandung as nama_ibu',
                'no_npwp as npwp',
                'tinggi_badan',
                'berat_badan',
                'no_hp_alternatif as hp_alternatif',
                'rek_bpd_aceh as bpd_aceh',
                'rek_bni as bsi',
                'tmt_akhir',
                'tmt_gol_sekarang as tmt', // Field TMT yang digunakan di view
                'kd_status_kerja',
                'status_peg',
                'kd_jenis_peg',
                'foto'
            ])
            ->where('kd_karyawan', $id)
            ->first();

        if (!$karyawanLuar) {
            abort(404, 'Data pegawai luar tidak ditemukan');
        }

        return view('karyawan-luar.show', compact('karyawanLuar'));
    }

    public function edit($id)
    {
        $karyawanLuar = DB::connection('sqlsrv')
            ->table('hrd_karyawan_luar')
            ->select([
                'kd_karyawan as kd_peg_luar',
                'nama',
                'gelar_depan',
                'gelar_belakang',
                'tempat_lahir',
                'tgl_lahir',
                'kd_jenis_kelamin as sex',
                'no_hp',
                'email',
                'alamat',
                'rek_bpd_aceh as bpd_aceh',
                'kd_status_kerja as status_kerja',
                'status_peg as status_pegawai',
                'kd_jenis_peg as jenis_pegawai',
                'kd_propinsi as provinsi',
                'kd_kabupaten as kabupaten',
                'kd_kecamatan as kecamatan',
                'kd_kelurahan as kelurahan',
                'tmt_gol_sekarang as tmt',
                'tmt_akhir',
                'no_karis as no_sk',
                'tgl_update as tgl_sk',
                'alamat as keterangan',
                'no_ktp as ktp',
                'no_kk',
                'nama_ibu_kandung as nama_ibu',
                'no_npwp as npwp',
                'kd_kulit as warna_kulit',
                'kd_suku as suku_bangsa',
                'kd_agama as agama',
                'kd_bangsa as kebangsaan',
                'kode_gol_dar as golongan_darah',
                'tinggi_badan',
                'berat_badan',
                'no_hp_alternatif as hp_alternatif',
                'rek_bni as bsi'
            ])
            ->where('kd_karyawan', $id)
            ->first();

        if (!$karyawanLuar) {
            abort(404, 'Data pegawai luar tidak ditemukan');
        }

        $statusKerja = DB::connection('sqlsrv')
            ->table('hrd_status_kerja')
            ->select('kd_status_kerja', 'status_kerja')
            ->where('kd_status_kerja', '5')
            ->orderBy('kd_status_kerja', 'asc')
            ->get();

        $jenisPegawai = DB::connection('sqlsrv')
            ->table('hrd_jenis_pegawai')
            ->select('kd_jenis_peg', 'jenis_peg')
            ->whereNotIn('kd_jenis_peg', [2])
            ->orderBy('jenis_peg', 'asc')
            ->get();

        $statusPegawai = DB::connection('sqlsrv')
            ->table('hrd_status_pegawai')
            ->select('kd_status_pegawai', 'status_pegawai')
            ->whereNotIn('kd_status_pegawai', ['3', '4'])
            ->orderBy('status_pegawai', 'asc')
            ->get();

        $warnaKulit = DB::connection('sqlsrv')
            ->table('hrd_kulit')
            ->select('kd_kulit', 'kulit')
            ->orderBy('kulit', 'asc')
            ->get();

        $sukuBangsa = DB::connection('sqlsrv')
            ->table('suku')
            ->select('kd_suku', 'suku')
            ->orderBy('suku', 'asc')
            ->get();

        $kebangsaan = DB::connection('sqlsrv')
            ->table('hrd_kebangsaan')
            ->select('kd_bangsa', 'kebangsaan')
            ->orderBy('kebangsaan', 'asc')
            ->get();

        $agama = DB::connection('sqlsrv')
            ->table('agama')
            ->select('kd_agama', 'agama')
            ->orderBy('agama', 'asc')
            ->get();

        $golonganDarah = DB::connection('sqlsrv')
            ->table('golongan_darah')
            ->select('kode', 'jenis')
            ->where('kode', '!=', '0')
            ->orderBy('jenis', 'asc')
            ->get();

        $provinsi = DB::connection('sqlsrv')
            ->table('propinsi')
            ->select('kd_propinsi', 'propinsi')
            ->orderBy('propinsi', 'asc')
            ->get();

        $kabupaten = DB::connection('sqlsrv')
            ->table('kabupaten')
            ->select('kd_kabupaten', 'kabupaten')
            ->where('kd_propinsi', $karyawanLuar->provinsi ?? '')
            ->orderBy('kabupaten', 'asc')
            ->get();

        $kecamatan = DB::connection('sqlsrv')
            ->table('kecamatan')
            ->select('kd_kecamatan', 'kecamatan')
            ->where('kd_kabupaten', $karyawanLuar->kabupaten ?? '')
            ->orderBy('kecamatan', 'asc')
            ->get();

        $kelurahan = DB::connection('sqlsrv')
            ->table('kelurahan')
            ->select('kd_kelurahan', 'kelurahan')
            ->where('kd_kecamatan', $karyawanLuar->kecamatan ?? '')
            ->orderBy('kelurahan', 'asc')
            ->get();

        return view('karyawan-luar.edit', [
            'karyawanLuar' => $karyawanLuar,
            'statusKerja' => $statusKerja,
            'jenisPegawai' => $jenisPegawai,
            'statusPegawai' => $statusPegawai,
            'warnaKulit' => $warnaKulit,
            'sukuBangsa' => $sukuBangsa,
            'kebangsaan' => $kebangsaan,
            'agama' => $agama,
            'golonganDarah' => $golonganDarah,
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'kecamatan' => $kecamatan,
            'kelurahan' => $kelurahan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $status_kerja = $request->status_kerja;
        $jenis_pegawai = $request->jenis_pegawai;
        $status_pegawai = $request->status_pegawai;

        $tmt = $request->tmt ? Carbon::createFromFormat('d/m/Y', $request->tmt)->format('Y-m-d') : null;
        $tmt = in_array($tmt, ['1970-01-01', '1900-01-01']) ? null : $tmt;

        $tmt_akhir = $request->tmt_akhir ? Carbon::createFromFormat('d/m/Y', $request->tmt_akhir)->format('Y-m-d') : null;
        $tmt_akhir = in_array($tmt_akhir, ['1970-01-01', '1900-01-01']) ? null : $tmt_akhir;

        $no_sk = $request->no_sk;
        $keterangan = $request->keterangan;

        $tgl_sk = $request->tgl_sk ? Carbon::createFromFormat('d/m/Y', $request->tgl_sk)->format('Y-m-d') : null;
        $tgl_sk = in_array($tgl_sk, ['1970-01-01', '1900-01-01']) ? null : $tgl_sk;

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
        $npwp = $request->npwp;
        $no_kk = $request->no_kk;
        $nama_ibu = $request->nama_ibu;
        $no_hp = $request->no_hp ?? 0;
        $hp_alternatif = $request->hp_alternatif ?? 0;
        $bsi = $request->bsi;
        $bpd_aceh = $request->bpd_aceh;

        $userCreated = auth()->user()->kd_karyawan;
        $now = Carbon::now();

        $updateKaryawan = DB::connection('sqlsrv')
            ->table('hrd_karyawan_luar')
            ->where('kd_karyawan', $id)
            ->update([
                'gelar_depan' => $gelar_depan,
                'nama' => $nama,
                'gelar_belakang' => $gelar_belakang,
                'tempat_lahir' => $tempat_lahir,
                'tgl_lahir' => $tgl_lahir,
                'no_ktp' => $ktp,
                'alamat' => $alamat,
                'kd_propinsi' => $provinsi,
                'kd_kabupaten' => $kabupaten,
                'kd_kecamatan' => $kecamatan,
                'kd_kelurahan' => $kelurahan,
                'kd_jenis_kelamin' => $sex,
                'kd_kulit' => $warna_kulit,
                'tinggi_badan' => $tinggi_badan,
                'berat_badan' => $berat_badan,
                'kode_gol_dar' => $golongan_darah,
                'kd_suku' => $suku_bangsa,
                'kd_agama' => $agama,
                'kd_bangsa' => $kebangsaan,
                'no_npwp' => $npwp,
                'no_hp' => $no_hp,
                'no_hp_alternatif' => $hp_alternatif,
                'kd_status_kerja' => $status_kerja,
                'rek_bni' => $bsi,
                'email' => $email,
                'status_peg' => $status_pegawai,
                'tmt_akhir' => $tmt_akhir,
                'no_kk' => $no_kk,
                'nama_ibu_kandung' => $nama_ibu,
                'tgl_update' => $now,
                'user_update' => $userCreated,
                'rek_bpd_aceh' => $bpd_aceh,
                'kd_jenis_peg' => $jenis_pegawai,
            ]);

        if ($updateKaryawan) {
            // Insert to hrd_log untuk update
            $maxLogId = DB::connection('sqlsrv')
                ->table('hrd_log')
                ->max('kd_log');

            $insertLog = DB::connection('sqlsrv')
                ->table('hrd_log')
                ->insert([
                    'kd_log' => $maxLogId + 1,
                    'kd_karyawan' => $id,
                    'kd_status_peg' => $status_pegawai,
                    'tmt_status_peg' => $tmt,
                    'ket' => $keterangan,
                    'no_sk' => $no_sk,
                    'tgl_sk' => $tgl_sk,
                ]);
            
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data pegawai luar berhasil diupdate!',
            ]);
        }

        return response()->json([
            'code' => 422,
            'status' => 'error',
            'message' => 'Data pegawai luar gagal diupdate!',
        ]);
    }
}
