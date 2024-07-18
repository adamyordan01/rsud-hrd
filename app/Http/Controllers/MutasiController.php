<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    public function index()
    {
        $jabatanStruktural = DB::table('hrd_jabatan_struktural')
            ->orderBy('jab_struk', 'ASC')
            ->get();

        $subDetail = DB::table('hrd_jenis_tenaga_sub_detail')
            ->orderBy('sub_detail', 'ASC')
            ->get();

        $divisi = DB::table('hrd_divisi')
            ->orderBy('divisi', 'ASC')
            ->get();

        $ruangan = DB::table('hrd_ruangan')
            ->where('status_aktif', 1)
            ->orderBy('ruangan', 'ASC')
            ->get();

        $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 1)
            ->count();

        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            ->where('kd_jenis_mutasi', 1)
            ->count();

        return view('mutasi.index', [
            'jabatanStruktural' => $jabatanStruktural,
            'subDetail' => $subDetail,
            'divisi' => $divisi,
            'ruangan' => $ruangan,
            'totalMutasiOnProcess' => $totalMutasiOnProcess,
            'totalMutasiPending' => $totalMutasiPending
        ]);
    }

    public function storeMutasiNota(Request $request)
    {
        $now = date("Y-m-d H:i:s");
        $idMutasi = date('YmdHis');

        $karyawanExist = DB::table('hrd_karyawan')
            ->where('kd_karyawan', $request->kd_karyawan)
            ->first();

        if (!$karyawanExist) {
            return response()->json([
                'code' => 1,
                'message' => "Data dengan Id: {$request->kd_karyawan} tidak ditemukan"
            ]);
        }

        $mutasiExist = DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('kd_tahap_mutasi', 0)
            ->first();

        if ($mutasiExist) {
            return response()->json([
                'code' => 2,
                'message' => "Data mutasi dengan Kode Karyawan: {$request->kd_karyawan} sudah ada"
            ]);
        } else {
            $mutasiExistOnProcess = DB::table('hrd_r_mutasi')
                ->where('kd_karyawan', $request->kd_karyawan)
                ->where('kd_tahap_mutasi', 1)
                ->first();

            if ($mutasiExistOnProcess) {
                return response()->json([
                    'code' => 4,
                    'message' => "Data mutasi dengan Kode Karyawan: {$request->kd_karyawan} masih didalam Daftar Mutasi (Proses)"
                ]);
            }
        }

        DB::table('hrd_r_mutasi')
            ->insert([
                'kd_mutasi' => $idMutasi,
                'kd_karyawan' => $request->kd_karyawan,
                'kd_tahap_mutasi' => 0,
                'user_update' => auth()->user()->kd_karyawan,
                'tgl_update' => $now,
                'kd_jenis_mutasi' => 1
            ]);

        
        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            ->where('kd_jenis_mutasi', 1)
            ->count();

        return response()->json([
            'code' => 3,
            'message' => "Berhasil menambahkan data mutasi dengan Id: {$idMutasi}",
            'id_mutasi' => $idMutasi,
            'total_mutasi_pending' => $totalMutasiPending
        ]);        
    }

    public function store(Request $request)
    {

        $now = date("Y-m-d H:i:s");
        $kdMutasi = $request->kd_mutasi_form;
        $kdKaryawan = $request->kd_karyawan_form;
        $jenisTenaga = $request->sub_jenis_tenaga;

        $subDetailJenisTenaga = DB::table('hrd_jenis_tenaga_sub_detail')
            ->where('sub_detail', $jenisTenaga)
            ->first();

        $kdJenisTenaga = $subDetailJenisTenaga->kd_jenis_tenaga;
        $kdDetail = $subDetailJenisTenaga->kd_detail;
        $kdSubDetail = $subDetailJenisTenaga->kd_sub_detail;

        $tmtJabatan = Carbon::parse($request->tmt_jabatan)->format('Y-m-d');
        $tglTtd = Carbon::parse($request->tgl_ttd)->format('Y-m-d');

        DB::table('hrd_r_mutasi')
            ->where('kd_mutasi', $kdMutasi)
            ->update([
                'isi_nota' => $request->isi_nota,
                'isi_nota_2' => $request->isi_nota_2,
                'kd_jab_struk' => $request->jab_struk,
                'tmt_jabatan' => $tmtJabatan,
                'kd_jenis_tenaga' => $kdJenisTenaga,
                'kd_detail' => $kdDetail,
                'kd_sub_detail' => $kdSubDetail,
                'kd_divisi' => $request->divisi,
                'kd_unit_kerja' => $request->unit,
                'kd_sub_unit_kerja' => $request->sub_unit,
                'kd_ruangan' => $request->ruangan,
                'kd_tahap_mutasi' => 1,
                'tgl_ttd' => $tglTtd,
                'user_update' => auth()->user()->kd_karyawan,
                'tgl_update' => $now
            ]);

        // return response json dan redirect ke halaman daftar mutasi proses
        return response()->json([
            'code' => 1,
            'message' => "Berhasil menyimpan data mutasi dengan Id: {$kdMutasi}"
        ]);
    }

    public function editMutasiOnPending($id)
    {
        $jabatanStruktural = DB::table('hrd_jabatan_struktural')
            ->orderBy('jab_struk', 'ASC')
            ->get();

        $subDetail = DB::table('hrd_jenis_tenaga_sub_detail')
            ->orderBy('sub_detail', 'ASC')
            ->get();

        $divisi = DB::table('hrd_divisi')
            ->orderBy('divisi', 'ASC')
            ->get();

        $ruangan = DB::table('hrd_ruangan')
            ->where('status_aktif', 1)
            ->orderBy('ruangan', 'ASC')
            ->get();

        $mutasiOnPending = DB::table('hrd_r_mutasi')
            ->join('view_tampil_karyawan', 'hrd_r_mutasi.kd_karyawan', '=', 'view_tampil_karyawan.kd_karyawan')
            ->select('hrd_r_mutasi.kd_karyawan', 'hrd_r_mutasi.kd_mutasi', 'view_tampil_karyawan.jab_struk', 'view_tampil_karyawan.gelar_depan', 'view_tampil_karyawan.nama', 'view_tampil_karyawan.gelar_belakang', 'view_tampil_karyawan.ruangan', 'view_tampil_karyawan.sub_detail')
            ->where('kd_mutasi', $id)
            ->where('kd_tahap_mutasi', 0)
            ->where('kd_jenis_mutasi', 1)
            ->first();
        // dd($mutasiOnPending);
        
        $getDataMutasiOnPending = DB::table('VIEW_PROSES_MUTASI')
            ->where('KD_MUTASI', $id)
            ->first();

        $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 1)
            ->count();

        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            ->where('kd_jenis_mutasi', 1)
            ->count();


        return view('mutasi.edit', [
            'jabatanStruktural' => $jabatanStruktural,
            'subDetail' => $subDetail,
            'divisi' => $divisi,
            'ruangan' => $ruangan,
            'mutasiOnPending' => $mutasiOnPending,
            'getDataMutasiOnPending' => $getDataMutasiOnPending,
            'totalMutasiOnProcess' => $totalMutasiOnProcess,
            'totalMutasiPending' => $totalMutasiPending
        ]);
    }

    public function updateMutasiOnPending(Request $request, $id)
    {
        $now = date("Y-m-d H:i:s");

        $kdMutasi = $id;
        $kdKaryawan = $request->kd_karyawan_form;
        $jenisTenaga = $request->sub_jenis_tenaga;

        $subDetailJenisTenaga = DB::table('hrd_jenis_tenaga_sub_detail')
            ->where('sub_detail', $jenisTenaga)
            ->first();

        $kdJenisTenaga = $subDetailJenisTenaga->kd_jenis_tenaga;
        $kdDetail = $subDetailJenisTenaga->kd_detail;
        $kdSubDetail = $subDetailJenisTenaga->kd_sub_detail;

        $tmtJabatan = Carbon::parse($request->tmt_jabatan)->format('Y-m-d');
        $tglTtd = Carbon::parse($request->tgl_ttd)->format('Y-m-d');

        DB::table('hrd_r_mutasi')
            ->where('kd_mutasi', $kdMutasi)
            ->update([
                'isi_nota' => $request->isi_nota,
                'isi_nota_2' => $request->isi_nota_2,
                'kd_jab_struk' => $request->jab_struk,
                'tmt_jabatan' => $tmtJabatan,
                'kd_jenis_tenaga' => $kdJenisTenaga,
                'kd_detail' => $kdDetail,
                'kd_sub_detail' => $kdSubDetail,
                'kd_divisi' => $request->divisi,
                'kd_unit_kerja' => $request->unit,
                'kd_sub_unit_kerja' => $request->sub_unit,
                'kd_ruangan' => $request->ruangan,
                'kd_tahap_mutasi' => 1,
                'tgl_ttd' => $tglTtd,
                'user_update' => auth()->user()->kd_karyawan,
                'tgl_update' => $now
            ]);

        return response()->json([
            'code' => 1,
            'message' => "Berhasil menyimpan data mutasi dengan Id: {$kdMutasi}"
        ]);
    }

    public function editMutasiOnProcess($id)
    {
        $jabatanStruktural = DB::table('hrd_jabatan_struktural')
            ->orderBy('jab_struk', 'ASC')
            ->get();

        $subDetail = DB::table('hrd_jenis_tenaga_sub_detail')
            ->orderBy('sub_detail', 'ASC')
            ->get();

        $divisi = DB::table('hrd_divisi')
            ->orderBy('divisi', 'ASC')
            ->get();

        $ruangan = DB::table('hrd_ruangan')
            ->where('status_aktif', 1)
            ->orderBy('ruangan', 'ASC')
            ->get();

        $mutasiOnProcess = DB::table('hrd_r_mutasi')
            ->join('view_tampil_karyawan', 'hrd_r_mutasi.kd_karyawan', '=', 'view_tampil_karyawan.kd_karyawan')
            ->select('hrd_r_mutasi.kd_karyawan', 'hrd_r_mutasi.kd_mutasi', 'view_tampil_karyawan.jab_struk', 'view_tampil_karyawan.gelar_depan', 'view_tampil_karyawan.nama', 'view_tampil_karyawan.gelar_belakang', 'view_tampil_karyawan.ruangan', 'view_tampil_karyawan.sub_detail')
            ->where('kd_mutasi', $id)
            ->where('kd_tahap_mutasi', 1)
            ->where('kd_jenis_mutasi', 1)
            ->first();
        // dd($mutasiOnProcess);
        
        $getDataMutasiOnProcess = DB::table('VIEW_PROSES_MUTASI')
            ->where('KD_MUTASI', $id)
            ->first();

        $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 1)
            ->count();

        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            ->where('kd_jenis_mutasi', 1)
            ->count();


        return view('mutasi.edit-mutasi-nota-on-process', [
            'jabatanStruktural' => $jabatanStruktural,
            'subDetail' => $subDetail,
            'divisi' => $divisi,
            'ruangan' => $ruangan,
            'mutasiOnProcess' => $mutasiOnProcess,
            'getDataMutasiOnProcess' => $getDataMutasiOnProcess,
            'totalMutasiOnProcess' => $totalMutasiOnProcess,
            'totalMutasiPending' => $totalMutasiPending
        ]);
    }

    public function updateMutasiOnProcess(Request $request, $id)
    {
        $now = date("Y-m-d H:i:s");

        $kdMutasi = $id;
        $kdKaryawan = $request->kd_karyawan_form;
        $jenisTenaga = $request->sub_jenis_tenaga;

        $subDetailJenisTenaga = DB::table('hrd_jenis_tenaga_sub_detail')
            ->where('sub_detail', $jenisTenaga)
            ->first();

        $kdJenisTenaga = $subDetailJenisTenaga->kd_jenis_tenaga;
        $kdDetail = $subDetailJenisTenaga->kd_detail;
        $kdSubDetail = $subDetailJenisTenaga->kd_sub_detail;

        $tmtJabatan = Carbon::parse($request->tmt_jabatan)->format('Y-m-d');
        $tglTtd = Carbon::parse($request->tgl_ttd)->format('Y-m-d');

        DB::table('hrd_r_mutasi')
            ->where('kd_mutasi', $kdMutasi)
            ->update([
                'isi_nota' => $request->isi_nota,
                'isi_nota_2' => $request->isi_nota_2,
                'kd_jab_struk' => $request->jab_struk,
                'tmt_jabatan' => $tmtJabatan,
                'kd_jenis_tenaga' => $kdJenisTenaga,
                'kd_detail' => $kdDetail,
                'kd_sub_detail' => $kdSubDetail,
                'kd_divisi' => $request->divisi,
                'kd_unit_kerja' => $request->unit,
                'kd_sub_unit_kerja' => $request->sub_unit,
                'kd_ruangan' => $request->ruangan,
                'kd_tahap_mutasi' => 1,
                'tgl_ttd' => $tglTtd,
                'user_update' => auth()->user()->kd_karyawan,
                'tgl_update' => $now
            ]);

        return response()->json([
            'code' => 1,
            'message' => "Berhasil menyimpan data mutasi dengan Id: {$kdMutasi}"
        ]);
    }

    public function deleteMutasiNota($id)
    {
        $mutasiExist = DB::table('hrd_r_mutasi')
            ->where('kd_mutasi', $id)
            ->where('kd_tahap_mutasi', 0)
            ->first();

        if (!$mutasiExist) {
            return response()->json([
                'code' => 1,
                'message' => "Data mutasi dengan Id: {$id} tidak ditemukan"
            ]);
        }

        DB::table('hrd_r_mutasi')
            ->where('kd_mutasi', $id)
            ->delete();

        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            ->where('kd_jenis_mutasi', 1)
            ->count();

        return response()->json([
            'code' => 2,
            'message' => "Berhasil menghapus data mutasi dengan Id: {$id}",
            'total_mutasi_pending' => $totalMutasiPending
        ]);
    }

    public function checkPegawai($id)
    {
        $getPegawai = DB::table('hrd_karyawan')
            ->where('kd_karyawan', $id)
            ->first();

        if (!$getPegawai) {
            return response()->json([
                'code' => 3,
                'message' => "Data dengan Id: {$id} tidak ditemukan"
            ]);
        }

        $gelar_depan = $getPegawai->gelar_depan ?? '';
        $nama = $getPegawai->nama;
        $gelar_belakang = $getPegawai->gelar_belakang ?? '';

        $nama_lengkap = trim("{$gelar_depan} {$nama} {$gelar_belakang}");

        $nip = $getPegawai->nip_baru ?? '----------------';

        if ($getPegawai->status_peg > 1) {
            return response()->json([
                'code' => 2,
                'message' => "Pegawai dengan NIP: {$nip} sudah tidak aktif"
            ]);
        }

        $mutasi = DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $id)
            ->whereIn('kd_tahap_mutasi', [0, 1])
            ->first();

        if ($mutasi) {
            $status = $mutasi->kd_tahap_mutasi == 0 ? 'Tertunda' : 'Dalam Proses';

            return response()->json([
                'code' => 1,
                'message' => "Pegawai dengan Id: {$id} \n Nama: {$nama_lengkap} \n NIP: {$nip} \n Data masih dalam daftar mutasi dengan status: {$status}"
            ]);
        }

        return response()->json([
            'code' => 4,
            'message' => "Berhasil mendapatkan data pegawai dengan Id: {$id} \n Nama: {$nama_lengkap} \n NIP: {$nip}",
            'nama' => $nama_lengkap,
            'nip' => $nip
        ]);
    }

    public function listMutasiNota($id)
    {
        $listMutasiNota = DB::table('hrd_r_mutasi')
            ->join('view_tampil_karyawan', 'hrd_r_mutasi.kd_karyawan', '=', 'view_tampil_karyawan.kd_karyawan')
            ->select('hrd_r_mutasi.kd_karyawan', 'hrd_r_mutasi.kd_mutasi', 'view_tampil_karyawan.jab_struk', 'view_tampil_karyawan.gelar_depan', 'view_tampil_karyawan.nama', 'view_tampil_karyawan.gelar_belakang', 'view_tampil_karyawan.ruangan', 'view_tampil_karyawan.sub_detail')
            ->where('kd_mutasi', $id)
            ->whereIn('kd_tahap_mutasi', [0, 1])
            ->where('kd_jenis_mutasi', 1)
            ->get();

            return view('mutasi.list-nota', compact('listMutasiNota'));
    }

    public function getUnitKerja($id)
    {
        $unit = DB::table('hrd_unit_kerja')
            ->select('*')
            ->where('kd_divisi', $id)
            ->orderBy('unit_kerja', 'asc')
            ->get();

        return response()->json($unit);
    }

    public function getSubUnitKerja($id, $divisi)
    {
        $subUnit = DB::table('hrd_sub_unit_kerja')
            ->select('*')
            ->where('kd_unit_kerja', $id)
            ->where('kd_divisi', $divisi)
            ->orderBy('sub_unit_kerja', 'asc')
            ->get();

        return response()->json($subUnit);
    }
}
