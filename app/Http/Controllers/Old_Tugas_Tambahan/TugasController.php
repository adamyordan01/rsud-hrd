<?php

namespace App\Http\Controllers\Tugas_Tambahan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TugasController extends Controller
{
    public function __construct()
    {
        // waktu asia jakarta
        date_default_timezone_set('Asia/Jakarta');
    }

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

        $totalTugasTambahanOnProcess = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 1)
            ->count();

        $totalTugasTambahanPending = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();

        return view('tugas-tambahan.index', [
            'jabatanStruktural' => $jabatanStruktural,
            'subDetail' => $subDetail,
            'divisi' => $divisi,
            'ruangan' => $ruangan,
            'totalTugasTambahanOnProcess' => $totalTugasTambahanOnProcess,
            'totalTugasTambahanPending' => $totalTugasTambahanPending
        ]);
    }

    public function store(Request $request)
    {
        $now = date("Y-m-d H:i:s");
        $kdTugasTambahan = $request->kd_tugas_tambahan_form;
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

        DB::table('hrd_r_tugas_tambahan')
            ->where('kd_tugas_tambahan', $kdTugasTambahan)
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
                'kd_tahap_tugas_tambahan' => 1,
                'tgl_ttd' => $tglTtd,
                'user_update' => auth()->user()->kd_karyawan,
                'tgl_update' => $now
            ]);

        // $this->logSuccess($kdMutasi, 'Proses Mutasi', 'Berhasil memproses data mutasi');

        // return response json dan redirect ke halaman daftar mutasi proses
        return response()->json([
            'code' => 1,
            'message' => "Berhasil menyimpan data tugas tambahan dengan Id: {$kdTugasTambahan}"
        ]);
    }

    public function storeTugasTambahan(Request $request)
    {
        $now = date("Y-m-d H:i:s");
        $idTugasTambahan = date('YmdHis');

        $karyawanExist = DB::table('hrd_karyawan')
            ->where('kd_karyawan', $request->kd_karyawan)
            ->first();

        if (!$karyawanExist) {
            return response()->json([
                'code' => 1,
                'message' => "Data dengan Id: {$request->kd_karyawan} tidak ditemukan"
            ]);
        }

        $tugasTambahanExist = DB::table('hrd_r_tugas_tambahan')
            ->where('kd_karyawan', $request->kd_karyawan)
            ->where('kd_tahap_tugas_tambahan', 0)
            ->first();

        if ($tugasTambahanExist) {
            return response()->json([
                'code' => 2,
                'message' => "Data tugas tambahan dengan Kode Karyawan: {$request->kd_karyawan} sudah ada"
            ]);
        } else {
            $tugasTambahanExistOnProcess = DB::table('hrd_r_tugas_tambahan')
                ->where('kd_karyawan', $request->kd_karyawan)
                ->where('kd_tahap_tugas_tambahan', 1)
                ->first();

            if ($tugasTambahanExistOnProcess) {
                return response()->json([
                    'code' => 4,
                    'message' => "Data tugas tambahan dengan Kode Karyawan: {$request->kd_karyawan} masih didalam Daftar Tugas Tambahan (Proses)"
                ]);
            }
        }

        DB::table('hrd_r_tugas_tambahan')
            ->insert([
                'kd_tugas_tambahan' => $idTugasTambahan,
                'kd_karyawan' => $request->kd_karyawan,
                'kd_tahap_tugas_tambahan' => 0,
                'user_update' => auth()->user()->kd_karyawan,
                'tgl_update' => $now,
                'kd_jenis_tugas_tambahan' => 1
            ]);

        // $this->logSuccess($idTugasTambahan, 'Tambah Nota', 'Berhasil menambahkan data mutasi');

        
        $totalTugasTambahanPending = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();

        return response()->json([
            'code' => 3,
            'message' => "Berhasil menambahkan data tugas tambahan dengan Id: {$idTugasTambahan}",
            'id_tugas_tambahan' => $idTugasTambahan,
            'total_tugas_tambahan_pending' => $totalTugasTambahanPending
        ]);        
    }

    public function editTugasOnPending($id)
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

        $tugasTambahanOnPending = DB::table('hrd_r_tugas_tambahan')
            ->join('view_tampil_karyawan', 'hrd_r_tugas_tambahan.kd_karyawan', '=', 'view_tampil_karyawan.kd_karyawan')
            ->select('hrd_r_tugas_tambahan.kd_karyawan', 'hrd_r_tugas_tambahan.kd_tugas_tambahan', 'view_tampil_karyawan.jab_struk', 'view_tampil_karyawan.gelar_depan', 'view_tampil_karyawan.nama', 'view_tampil_karyawan.gelar_belakang', 'view_tampil_karyawan.ruangan', 'view_tampil_karyawan.sub_detail')
            ->where('kd_tugas_tambahan', $id)
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->first();
        // dd($mutasiOnPending);
        
        $getDataTugasTambahanOnPending = DB::table('view_proses_tugas_tambahan')
            ->where('kd_tugas_tambahan', $id)
            ->first();

        $totalTugasTambahanOnProcess = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 1)
            ->count();

        $totalTugasTambahanPending = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();


        return view('tugas-tambahan.edit', [
            'jabatanStruktural' => $jabatanStruktural,
            'subDetail' => $subDetail,
            'divisi' => $divisi,
            'ruangan' => $ruangan,
            'tugasTambahanOnPending' => $tugasTambahanOnPending,
            'getDataTugasTambahanOnPending' => $getDataTugasTambahanOnPending,
            'totalTugasTambahanOnProcess' => $totalTugasTambahanOnProcess,
            'totalTugasTambahanPending' => $totalTugasTambahanPending
        ]);
    }

    public function updateTugasOnPending(Request $request, $id)
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

        // $this->logSuccess($kdMutasi, 'Edit Nota', 'Berhasil mengedit data mutasi');

        return response()->json([
            'code' => 1,
            'message' => "Berhasil menyimpan data mutasi dengan Id: {$kdMutasi}"
        ]);
    }

    public function editTugasOnProcess($id)
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

        $tugasTambahanOnProcess = DB::table('hrd_r_tugas_tambahan')
            ->join('view_tampil_karyawan', 'hrd_r_tugas_tambahan.kd_karyawan', '=', 'view_tampil_karyawan.kd_karyawan')
            ->select('hrd_r_tugas_tambahan.kd_karyawan', 'hrd_r_tugas_tambahan.kd_tugas_tambahan', 'view_tampil_karyawan.jab_struk', 'view_tampil_karyawan.gelar_depan', 'view_tampil_karyawan.nama', 'view_tampil_karyawan.gelar_belakang', 'view_tampil_karyawan.ruangan', 'view_tampil_karyawan.sub_detail')
            ->where('kd_tugas_tambahan', $id)
            ->where('kd_tahap_tugas_tambahan', 1)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->first();
        // dd($mutasiOnProcess);
        
        $getDataTugasTambahanOnProcess = DB::table('view_proses_tugas_tambahan')
            ->where('kd_tugas_tambahan', $id)
            ->first();

        $totalTugasTambahanOnProcess = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 1)
            ->count();

        $totalTugasTambahanPending = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();


        return view('tugas-tambahan.edit-tugas-tambahan-on-process', [
            'jabatanStruktural' => $jabatanStruktural,
            'subDetail' => $subDetail,
            'divisi' => $divisi,
            'ruangan' => $ruangan,
            'tugasTambahanOnProcess' => $tugasTambahanOnProcess,
            'getDataTugasTambahanOnProcess' => $getDataTugasTambahanOnProcess,
            'totalTugasTambahanOnProcess' => $totalTugasTambahanOnProcess,
            'totalTugasTambahanPending' => $totalTugasTambahanPending
        ]);
    }

    public function updateTugasOnProcess(Request $request, $id)
    {
        $now = date("Y-m-d H:i:s");

        $kdTugasTambahan = $id;
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

        DB::table('hrd_r_tugas_tambahan')
            ->where('kd_tugas_tambahan', $kdTugasTambahan)
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
                'kd_tahap_tugas_tambahan' => 1,
                'tgl_ttd' => $tglTtd,
                'user_update' => auth()->user()->kd_karyawan,
                'tgl_update' => $now
            ]);

        // $this->logSuccess($kdMutasi, 'Update Nota', 'Berhasil mengupdate data mutasi');

        return response()->json([
            'code' => 1,
            'message' => "Berhasil menyimpan data tugas tambahan dengan Id: {$kdTugasTambahan}"
        ]);
    }

    public function deleteTugasTambahan($id)
    {
        $tugasTambahanExist = DB::table('hrd_r_tugas_tambahan')
            ->where('kd_tugas_tambahan', $id)
            ->where('kd_tahap_tugas_tambahan', 0)
            ->first();

        if (!$tugasTambahanExist) {
            return response()->json([
                'code' => 1,
                'message' => "Data tugas tambahan dengan Id: {$id} tidak dapat dihapus, karena sudah dalam proses"
            ]);
        }

        DB::table('hrd_r_tugas_tambahan')
            ->where('kd_tugas_tambahan', $id)
            ->delete();

        $totalTugasTambahanPending = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();

        return response()->json([
            'code' => 2,
            'message' => "Berhasil menghapus data tugas tambahan dengan Id: {$id}",
            'total_tugas_tambahan_pending' => $totalTugasTambahanPending
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

        $nama_lengkap = trim("{$gelar_depan} {$nama}{$gelar_belakang}");

        $nip = $getPegawai->nip_baru ?? '----------------';

        if ($getPegawai->status_peg > 1) {
            return response()->json([
                'code' => 2,
                'message' => "Pegawai dengan NIP: {$nip} sudah tidak aktif"
            ]);
        }

        $tugasTambahan = DB::table('hrd_r_tugas_tambahan')
            ->where('kd_karyawan', $id)
            ->whereIn('kd_tahap_tugas_tambahan', [0, 1])
            ->first();

        if ($tugasTambahan) {
            $status = $tugasTambahan->kd_tahap_tugas_tambahan == 0 ? 'Tertunda' : 'Dalam Proses';

            return response()->json([
                'code' => 1,
                'message' => "Pegawai dengan Id: {$id} \n Nama: {$nama_lengkap} \n NIP: {$nip} \n Data masih dalam daftar tugas tambahan dengan status: {$status}"
            ]);
        }

        return response()->json([
            'code' => 4,
            'message' => "Berhasil mendapatkan data pegawai dengan Id: {$id} \n Nama: {$nama_lengkap} \n NIP: {$nip}",
            'nama' => $nama_lengkap,
            'nip' => $nip
        ]);
    }

    public function listTugasTambahan($id)
    {
        $listTugasTambahan = DB::table('hrd_r_tugas_tambahan as tugas')
            ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
            ->select('tugas.kd_karyawan', 'tugas.kd_tugas_tambahan', 'karyawan.jab_struk', 'karyawan.gelar_depan', 'karyawan.nama', 'karyawan.gelar_belakang', 'karyawan.ruangan', 'karyawan.sub_detail')
            ->where('kd_tugas_tambahan', $id)
            ->whereIn('kd_tahap_tugas_tambahan', [0, 1])
            ->where('kd_jenis_tugas_tambahan', 1)
            ->get();

            return view('tugas-tambahan.list-tugas-tambahan', compact('listTugasTambahan'));
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
