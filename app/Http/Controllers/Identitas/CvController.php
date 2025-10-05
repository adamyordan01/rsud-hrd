<?php

namespace App\Http\Controllers\Identitas;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CvController extends Controller
{
    public function index()
    {
        
    }

    public function show($id)
    {
        $karyawan = Karyawan::where('kd_karyawan', $id)->firstOrFail();
        // dd($karyawan);
        $keluarga = DB::table('HRD_R_KELUARGA')
            ->select(
                'HRD_R_KELUARGA.KD_KARYAWAN', 
                'HRD_R_KELUARGA.URUT_KLRG', 
                'HRD_HUB_KELUARGA.HUB_KLRG', 
                'HRD_R_KELUARGA.NAMA', 
                'HRD_R_KELUARGA.TEMPAT_LAHIR', 
                'HRD_R_KELUARGA.TGL_LAHIR', 
                'HRD_R_KELUARGA.JK', 
                'HRD_JENJANG_PENDIDIKAN.JENJANG_DIDIK', 
                'HRD_PEKERJAAN.PEKERJAAN'
            )
            ->leftJoin('HRD_HUB_KELUARGA', 'HRD_R_KELUARGA.KD_HUB_KLRG', '=', 'HRD_HUB_KELUARGA.KD_HUB_KLRG')
            ->leftJoin('HRD_JENJANG_PENDIDIKAN', 'HRD_R_KELUARGA.KD_JENJANG_DIDIK', '=', 'HRD_JENJANG_PENDIDIKAN.KD_JENJANG_DIDIK')
            ->join('HRD_PEKERJAAN', 'HRD_R_KELUARGA.KD_PEKERJAAN', '=', 'HRD_PEKERJAAN.KD_PEKERJAAN')
            ->where('HRD_R_KELUARGA.KD_KARYAWAN', '=', $id)
            ->orderBy('HRD_R_KELUARGA.TGL_LAHIR', 'ASC')
            ->get();
        $bahasa = DB::table('HRD_R_BAHASA')
            ->select(
                'HRD_R_BAHASA.KD_KARYAWAN',
                'HRD_R_BAHASA.URUT_BAHASA',
                'HRD_BAHASA.BAHASA',
                'HRD_TINGKAT_BAHASA.TINGKAT_BAHASA'
            )
            ->join('HRD_BAHASA', 'HRD_R_BAHASA.KD_BAHASA', '=', 'HRD_BAHASA.KD_BAHASA')
            ->join('HRD_TINGKAT_BAHASA', 'HRD_R_BAHASA.KD_TINGKAT_BAHASA', '=', 'HRD_TINGKAT_BAHASA.KD_TINGKAT_BAHASA')
            ->where('HRD_R_BAHASA.KD_KARYAWAN', '=', $id)
            ->get();
        $riwayatPendidikan = DB::table('HRD_R_PENDIDIKAN')
            ->select(
                'HRD_R_PENDIDIKAN.KD_KARYAWAN',
                'HRD_JENJANG_PENDIDIKAN.JENJANG_DIDIK',
                'HRD_JURUSAN.JURUSAN',
                'HRD_R_PENDIDIKAN.NAMA_LEMBAGA',
                'HRD_R_PENDIDIKAN.TAHUN_LULUS',
                'HRD_R_PENDIDIKAN.NO_IJAZAH',
                'HRD_R_PENDIDIKAN.TEMPAT',
                'HRD_R_PENDIDIKAN.URUT_DIDIK'
            )
            ->join('HRD_JENJANG_PENDIDIKAN', 'HRD_R_PENDIDIKAN.KD_JENJANG_DIDIK', '=', 'HRD_JENJANG_PENDIDIKAN.KD_JENJANG_DIDIK')
            ->join('HRD_JURUSAN', 'HRD_R_PENDIDIKAN.KD_JURUSAN', '=', 'HRD_JURUSAN.KD_JURUSAN')
            ->where('HRD_R_PENDIDIKAN.KD_KARYAWAN', '=', $id)
            ->orderBy('HRD_R_PENDIDIKAN.URUT_DIDIK', 'ASC')
            ->get();
        $riwayatKepangkatan = DB::table('hrd_riwayat_kepangkatan as pangkat')
            ->join('hrd_golongan as b', 'pangkat.perubahan_pangkat_id', '=', 'b.kd_gol')
            ->join('hrd_golongan as c', 'pangkat.golongan_id', '=', 'c.kd_gol')
            ->select('pangkat.*', 'b.pangkat as pangkat', 'c.alias_gol as golongan')
            ->where('pangkat.user_id', '=', $id)
            ->orderBy('pangkat.urut', 'asc')
            ->get();
        $riwayatPekerjaan = DB::table('HRD_R_KERJA')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_KERJA', 'ASC')
            ->get();
        $riwayatOrganisasi = DB::table('HRD_R_ORGANISASI')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_ORG', 'ASC')
            ->get();
        $riwayatPenghargaan = DB::table('HRD_R_PENGHARGAAN')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_PENG', 'ASC')
            ->get();
        $riwayatSeminar = DB::table('hrd_r_seminar')
            ->join('hrd_sumber_dana', 'hrd_r_seminar.kd_sumber_dana', '=', 'hrd_sumber_dana.kd_sumber_dana')
            ->selectRaw('*, lower(hrd_sumber_dana.sumber_dana) as sumber_dana')
            ->where('hrd_r_seminar.kd_karyawan', '=', $id)
            ->orderBy('hrd_r_seminar.urut_seminar', 'asc')
            ->get();
        $riwayatTugas = DB::table('view_tempat_kerja')
            ->where('kd_karyawan', '=', $id)
            ->orderBy('no_urut', 'desc')
            ->get();
        $tugasTambahan = DB::table('hrd_tugas_tambahan as tugas')
            ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
            ->leftJoin('hrd_jabatan_struktural as jabatan', 'tugas.kd_jab_struk', '=', 'jabatan.kd_jab_struk')
            ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', function($join) {
                $join->on('tugas.kd_jenis_tenaga', '=', 'sub_detail.kd_jenis_tenaga')
                    ->on('tugas.kd_detail', '=', 'sub_detail.kd_detail')
                    ->on('tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail');
            })
            ->leftJoin('hrd_ruangan as ruangan', 'tugas.kd_ruangan', '=', 'ruangan.kd_ruangan')
            ->select(
                'tugas.*', 
                'karyawan.*', 
                'jabatan.jab_struk as jab_struk_tambahan', 
                'sub_detail.sub_detail as sub_detail_tambahan', 
                'ruangan.ruangan as ruangan_tambahan'
            )
            ->where('tugas.kd_karyawan', '=', $id)
            ->whereNotNull('tugas.verif_4')
            ->whereNotNull('tugas.kd_karyawan_verif_4')
            ->whereNotNull('tugas.waktu_verif_4')
            ->whereNotNull('id_dokumen')
            ->orderBy('tugas.tmt_awal', 'asc')
            ->get();

        return view('karyawan.identitas.cv', compact(
            'karyawan', 
            'keluarga', 
            'bahasa', 
            'riwayatPendidikan', 
            'riwayatPekerjaan', 
            'riwayatOrganisasi', 
            'riwayatPenghargaan', 
            'riwayatSeminar', 
            'riwayatTugas', 
            'tugasTambahan'
        ));
    }

    public function print($id)
    {
        $karyawan = Karyawan::where('kd_karyawan', $id)->firstOrFail();
        $keluarga = DB::table('HRD_R_KELUARGA')
            ->select(
                'HRD_R_KELUARGA.KD_KARYAWAN', 
                'HRD_R_KELUARGA.URUT_KLRG', 
                'HRD_HUB_KELUARGA.HUB_KLRG', 
                'HRD_R_KELUARGA.NAMA', 
                'HRD_R_KELUARGA.TEMPAT_LAHIR', 
                'HRD_R_KELUARGA.TGL_LAHIR', 
                'HRD_R_KELUARGA.JK', 
                'HRD_JENJANG_PENDIDIKAN.JENJANG_DIDIK', 
                'HRD_PEKERJAAN.PEKERJAAN'
            )
            ->leftJoin('HRD_HUB_KELUARGA', 'HRD_R_KELUARGA.KD_HUB_KLRG', '=', 'HRD_HUB_KELUARGA.KD_HUB_KLRG')
            ->leftJoin('HRD_JENJANG_PENDIDIKAN', 'HRD_R_KELUARGA.KD_JENJANG_DIDIK', '=', 'HRD_JENJANG_PENDIDIKAN.KD_JENJANG_DIDIK')
            ->join('HRD_PEKERJAAN', 'HRD_R_KELUARGA.KD_PEKERJAAN', '=', 'HRD_PEKERJAAN.KD_PEKERJAAN')
            ->where('HRD_R_KELUARGA.KD_KARYAWAN', '=', $id)
            ->orderBy('HRD_R_KELUARGA.TGL_LAHIR', 'ASC')
            ->get();
        $bahasa = DB::table('HRD_R_BAHASA')
            ->select(
                'HRD_R_BAHASA.KD_KARYAWAN',
                'HRD_R_BAHASA.URUT_BAHASA',
                'HRD_BAHASA.BAHASA',
                'HRD_TINGKAT_BAHASA.TINGKAT_BAHASA'
            )
            ->join('HRD_BAHASA', 'HRD_R_BAHASA.KD_BAHASA', '=', 'HRD_BAHASA.KD_BAHASA')
            ->join('HRD_TINGKAT_BAHASA', 'HRD_R_BAHASA.KD_TINGKAT_BAHASA', '=', 'HRD_TINGKAT_BAHASA.KD_TINGKAT_BAHASA')
            ->where('HRD_R_BAHASA.KD_KARYAWAN', '=', $id)
            ->get();
        $riwayatPendidikan = DB::table('HRD_R_PENDIDIKAN')
            ->select(
                'HRD_R_PENDIDIKAN.KD_KARYAWAN',
                'HRD_JENJANG_PENDIDIKAN.JENJANG_DIDIK',
                'HRD_JURUSAN.JURUSAN',
                'HRD_R_PENDIDIKAN.NAMA_LEMBAGA',
                'HRD_R_PENDIDIKAN.TAHUN_LULUS',
                'HRD_R_PENDIDIKAN.NO_IJAZAH',
                'HRD_R_PENDIDIKAN.TEMPAT',
                'HRD_R_PENDIDIKAN.URUT_DIDIK'
            )
            ->join('HRD_JENJANG_PENDIDIKAN', 'HRD_R_PENDIDIKAN.KD_JENJANG_DIDIK', '=', 'HRD_JENJANG_PENDIDIKAN.KD_JENJANG_DIDIK')
            ->join('HRD_JURUSAN', 'HRD_R_PENDIDIKAN.KD_JURUSAN', '=', 'HRD_JURUSAN.KD_JURUSAN')
            ->where('HRD_R_PENDIDIKAN.KD_KARYAWAN', '=', $id)
            ->orderBy('HRD_R_PENDIDIKAN.URUT_DIDIK', 'ASC')
            ->get();
        $riwayatKepangkatan = DB::table('hrd_riwayat_kepangkatan as pangkat')
            ->join('hrd_golongan as b', 'pangkat.perubahan_pangkat_id', '=', 'b.kd_gol')
            ->join('hrd_golongan as c', 'pangkat.golongan_id', '=', 'c.kd_gol')
            ->select('pangkat.*', 'b.pangkat as pangkat', 'c.alias_gol as golongan')
            ->where('pangkat.user_id', '=', $id)
            ->orderBy('pangkat.urut', 'asc')
            ->get();
        $riwayatPekerjaan = DB::table('HRD_R_KERJA')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_KERJA', 'ASC')
            ->get();
        $riwayatOrganisasi = DB::table('HRD_R_ORGANISASI')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_ORG', 'ASC')
            ->get();
        $riwayatPenghargaan = DB::table('HRD_R_PENGHARGAAN')
            ->where('KD_KARYAWAN', '=', $id)
            ->orderBy('URUT_PENG', 'ASC')
            ->get();
        $riwayatSeminar = DB::table('hrd_r_seminar')
            ->join('hrd_sumber_dana', 'hrd_r_seminar.kd_sumber_dana', '=', 'hrd_sumber_dana.kd_sumber_dana')
            ->selectRaw('*, lower(hrd_sumber_dana.sumber_dana) as sumber_dana')
            ->where('hrd_r_seminar.kd_karyawan', '=', $id)
            ->orderBy('hrd_r_seminar.urut_seminar', 'asc')
            ->get();
        $riwayatTugas = DB::table('view_tempat_kerja')
            ->where('kd_karyawan', '=', $id)
            ->orderBy('no_urut', 'desc')
            ->get();
        $tugasTambahan = DB::table('hrd_tugas_tambahan as tugas')
            ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
            ->leftJoin('hrd_jabatan_struktural as jabatan', 'tugas.kd_jab_struk', '=', 'jabatan.kd_jab_struk')
            ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', function($join) {
                $join->on('tugas.kd_jenis_tenaga', '=', 'sub_detail.kd_jenis_tenaga')
                    ->on('tugas.kd_detail', '=', 'sub_detail.kd_detail')
                    ->on('tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail');
            })
            ->leftJoin('hrd_ruangan as ruangan', 'tugas.kd_ruangan', '=', 'ruangan.kd_ruangan')
            ->select(
                'tugas.*', 
                'karyawan.*', 
                'jabatan.jab_struk as jab_struk_tambahan', 
                'sub_detail.sub_detail as sub_detail_tambahan', 
                'ruangan.ruangan as ruangan_tambahan'
            )
            ->where('tugas.kd_karyawan', '=', $id)
            ->whereNotNull('tugas.verif_4')
            ->whereNotNull('tugas.kd_karyawan_verif_4')
            ->whereNotNull('tugas.waktu_verif_4')
            ->whereNotNull('id_dokumen')
            ->orderBy('tugas.tmt_awal', 'asc')
            ->get();

        return view('karyawan.identitas.print-cv', compact(
            'karyawan', 
            'keluarga', 
            'bahasa', 
            'riwayatPendidikan', 
            'riwayatPekerjaan', 
            'riwayatOrganisasi', 
            'riwayatPenghargaan', 
            'riwayatSeminar', 
            'riwayatTugas', 
            'tugasTambahan'
        ));
    }
}
