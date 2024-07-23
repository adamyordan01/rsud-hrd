<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class MutasiOnProcessController extends Controller
{
    public function __construct()
    {
        // timezone asia jakarta
        date_default_timezone_set('Asia/Jakarta');   
    }

    public function index()
    {
        // "select KD_MUTASI from HRD_R_MUTASI where KD_TAHAP_MUTASI = 1 group by KD_MUTASI";
        $getMutasi = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 1)
            ->orderBy('kd_mutasi', 'desc')
            ->groupBy('kd_mutasi')
            ->get();
            // print_r($getMutasi);
            // die;

            // select count(KD_MUTASI) as hitung from HRD_R_MUTASI where KD_TAHAP_MUTASI = 1 and KD_JENIS_MUTASI = 1
        $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 1)
            ->where('kd_jenis_mutasi', 1)
            ->count();

        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            ->where('kd_jenis_mutasi', 1)
            ->count();

        return view('mutasi.mutasi-on-process.index', [
            'getMutasi' => $getMutasi,
            'totalMutasiOnProcess' => $totalMutasiOnProcess,
            'totalMutasiPending' => $totalMutasiPending
        ]);
    }

    public function firstVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_mutasi = $request->kd_mutasi;

        $update = DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_mutasi', $kd_mutasi)
            ->update([
                'verif_1' => 1,
                'kd_karyawan_verif_1' => auth()->user()->kd_karyawan,
                'waktu_verif_1' => Carbon::now()
            ]);

        if ($update) {
            $this->logSuccess($kd_mutasi, 'Verifikasi 1', 'Nota Mutasi berhasil diverifikasi oleh Kasubbag. Kepegawaian');

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Nota Mutasi berhasil diverifikasi oleh Kasubbag. Kepegawaian'
            ]);
        } else {
            $this->logFailed($kd_mutasi, 'Verifikasi 1', 'Nota Mutasi gagal diverifikasi oleh Kasubbag. Kepegawaian');

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Mutasi gagal diverifikasi oleh Kasubbag. Kepegawaian'
            ]);
        }
    }

    public function secondVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_mutasi = $request->kd_mutasi;

        $update = DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_mutasi', $kd_mutasi)
            ->update([
                'verif_2' => 1,
                'kd_karyawan_verif_2' => auth()->user()->kd_karyawan,
                'waktu_verif_2' => Carbon::now()
            ]);

        if ($update) {
            $this->logSuccess($kd_mutasi, 'Verifikasi 2', 'Nota Mutasi berhasil diverifikasi oleh Kabag. TU.');

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Nota Mutasi berhasil diverifikasi oleh Kabag. TU.'
            ]);
        } else {
            $this->logFailed($kd_mutasi, 'Verifikasi 2', 'Nota Mutasi gagal diverifikasi oleh Kabag. TU.');

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Mutasi gagal diverifikasi oleh Kabag. TU.'
            ]);
        }
    }

    public function thirdVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_mutasi = $request->kd_mutasi;

        $update = DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_mutasi', $kd_mutasi)
            ->update([
                'verif_3' => 1,
                'kd_karyawan_verif_3' => auth()->user()->kd_karyawan,
                'waktu_verif_3' => Carbon::now()
            ]);

        if ($update) {
            $this->logSuccess($kd_mutasi, 'Verifikasi 3', 'Nota Mutasi berhasil diverifikasi oleh Wadir. ADM dan Umum.');

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Nota Mutasi berhasil diverifikasi oleh Wadir. ADM dan Umum.'
            ]);
        } else {
            $this->logFailed($kd_mutasi, 'Verifikasi 3', 'Nota Mutasi gagal diverifikasi oleh Wadir. ADM dan Umum.');

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Mutasi gagal diverifikasi oleh Wadir. ADM dan Umum.'
            ]);
        }
    }

    public function fourthVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_mutasi = $request->kd_mutasi;

        DB::beginTransaction();

        try {
            $update = DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_mutasi', $kd_mutasi)
            ->update([
                'verif_4' => 1,
                'kd_karyawan_verif_4' => auth()->user()->kd_karyawan,
                'waktu_verif_4' => Carbon::now()
            ]);

            $this->logSuccess($kd_mutasi, 'Verifikasi 4', 'Nota Mutasi berhasil diverifikasi oleh Direktur.');

            DB::commit();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Nota Mutasi berhasil diverifikasi oleh Direktur.'
            ]);
        } catch (\Exception $e) {
            $this->logFailed($kd_mutasi, 'Verifikasi 4', 'Nota Mutasi gagal diverifikasi oleh Direktur.' . $e->getMessage());

            DB::rollBack();

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Mutasi gagal diverifikasi oleh Direktur. ' . $e->getMessage()
            ]);
        }
    }

    public function finalisasi(Request $request)
    {
        $kd_mutasi = $request->kd_mutasi;
        $kd_karyawan = $request->kd_karyawan;
        $passphrase = $request->passphrase;

        DB::beginTransaction();

        try {
            $no_nota = $this->getNotaNumber($kd_karyawan);
            // dd($no_nota);

            $update = DB::table('hrd_r_mutasi')
                ->where('kd_karyawan', $kd_karyawan)
                ->where('kd_mutasi', $kd_mutasi)
                ->update([
                    'kd_tahap_mutasi' => 2,
                    'stt_ttd' => 1,
                    'stt_stempel' => 1,
                    'user_update' => auth()->user()->kd_karyawan,
                    'tgl_update' => Carbon::now(),
                    'no_nota' => $no_nota
                ]);

            $getEmail = DB::table('hrd_karyawan as hk')
                ->leftJoin('hrd_tempat_kerja as htk', 'hk.kd_karyawan', '=', 'htk.kd_karyawan')
                ->select('hk.email', DB::raw('Max(htk.no_urut) as urut_max'))
                ->where('hk.kd_karyawan', $kd_karyawan)
                ->groupBy('hk.email')
                ->first();

            // get urut max and plus 1
            $urutMax = $getEmail->urut_max + 1;

            // $getMutasi = sqlsrv_query($konek, "select * from HRD_R_MUTASI where KD_MUTASI = '$kdmutasi'");
            $getMutasi = DB::table('hrd_r_mutasi')
                ->where('kd_mutasi', $kd_mutasi)
                ->first();

            // $query = sqlsrv_query($konek, "insert into HRD_TEMPAT_KERJA (KD_KARYAWAN, KD_DIVISI, KD_UNIT_KERJA, KD_SUB_UNIT_KERJA, KD_RUANGAN, TGL_MASUK, KD_MUTASI, NO_URUT) select M.KD_KARYAWAN, M.KD_DIVISI, M.KD_UNIT_KERJA, M.KD_SUB_UNIT_KERJA, M.KD_RUANGAN, M.TMT_JABATAN, M.KD_MUTASI, '$urut2' from HRD_R_MUTASI M where M.KD_KARYAWAN = '$kdkar' and M.KD_MUTASI = '$kdmutasi'");
            DB::table('hrd_tempat_kerja')
                ->insert([
                    'kd_karyawan' => $getMutasi->kd_karyawan,
                    'kd_divisi' => $getMutasi->kd_divisi,
                    'kd_unit_kerja' => $getMutasi->kd_unit_kerja,
                    'kd_sub_unit_kerja' => $getMutasi->kd_sub_unit_kerja,
                    'kd_ruangan' => $getMutasi->kd_ruangan,
                    'tgl_masuk' => $getMutasi->tmt_jabatan,
                    'kd_mutasi' => $getMutasi->kd_mutasi,
                    'no_urut' => $urutMax
                ]);

            // $query = sqlsrv_query($konek, "update HRD_KARYAWAN set KD_JABATAN_STRUKTURAL = '".$getAllMutasi['KD_JAB_STRUK']."', TMT_JABATAN_STRUKTURAL = '".date_format($getAllMutasi['TMT_JABATAN'], 'Y-m-d')."', KD_JENIS_TENAGA = '".$getAllMutasi['KD_JENIS_TENAGA']."', KD_DETAIL_JENIS_TENAGA = '".$getAllMutasi['KD_DETAIL']."', KD_SUB_DETAIL_JENIS_TENAGA = '".$getAllMutasi['KD_SUB_DETAIL']."', KD_DIVISI = '".$getAllMutasi['KD_DIVISI']."', KD_UNIT_KERJA = '".$getAllMutasi['KD_UNIT_KERJA']."', KD_SUB_UNIT_KERJA = '".$getAllMutasi['KD_SUB_UNIT_KERJA']."', KD_RUANGAN = '".$getAllMutasi['KD_RUANGAN']."' where KD_KARYAWAN = '$kdkar'");
            DB::table('hrd_karyawan')
                ->where('kd_karyawan', $kd_karyawan)
                ->update([
                    'kd_jabatan_struktural' => $getMutasi->kd_jab_struk,
                    'tmt_jabatan_struktural' => $getMutasi->tmt_jabatan,
                    'kd_jenis_tenaga' => $getMutasi->kd_jenis_tenaga,
                    'kd_detail_jenis_tenaga' => $getMutasi->kd_detail,
                    'kd_sub_detail_jenis_tenaga' => $getMutasi->kd_sub_detail,
                    'kd_divisi' => $getMutasi->kd_divisi,
                    'kd_unit_kerja' => $getMutasi->kd_unit_kerja,
                    'kd_sub_unit_kerja' => $getMutasi->kd_sub_unit_kerja,
                    'kd_ruangan' => $getMutasi->kd_ruangan
                ]);

            // $query = sqlsrv_query($konek, "update HRD_TEMPAT_KERJA set TGL_KELUAR = (select TMT_JABATAN from HRD_R_MUTASI where KD_KARYAWAN = '$kdkar' and KD_MUTASI = '$kdmutasi') WHERE KD_KARYAWAN = '$kdkar' and NO_URUT = (select MAX(NO_URUT - 1) from HRD_TEMPAT_KERJA where KD_KARYAWAN = '$kdkar')");
            DB::table('hrd_tempat_kerja')
                ->where('kd_karyawan', $kd_karyawan)
                ->where('no_urut', $urutMax - 1)
                ->update([
                    'tgl_keluar' => $getMutasi->tmt_jabatan
                ]);

            $printSkResponse = $this->printSk($kd_karyawan, $kd_mutasi, $passphrase);
            $printSkData = json_decode($printSkResponse->getContent(), true);

            if ($printSkData['original']['code'] == 200) {
                $this->logSuccess($kd_mutasi, 'Finalisasi', 'Nota Mutasi berhasil ditanda tangani secara digital.');

                DB::commit();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Nota Mutasi berhasil ditanda tangani secara digital.'
                ]);
            } else {
                $message = $printSkData['original']['message'] ?? 'Gagal menandatangani nota mutasi secara digital.';
                // $this->logFailed($kd_mutasi, 'Finalisasi', $message);
                DB::table('hrd_log_mutasi')
                    ->insert([
                        'kd_mutasi' => $kd_mutasi,
                        'kd_karyawan' => $kd_karyawan,
                        'tahap' => 'Finalisasi',
                        'keterangan' => $message,
                        'user' => auth()->user()->kd_karyawan,
                        'waktu' => Carbon::now()
                    ]);
                
                DB::rollBack();

                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Nota Mutasi gagal ditanda tangani secara digital. ' . $printSkData['original']['message']
                ], 400);
            }

        } catch (\Exception $e) {
            // $this->logFailed($kd_mutasi, 'Finalisasi', 'Nota Mutasi gagal ditanda tangani secara digital.' . $e->getMessage());
            $message = $e->getMessage() ?? 'Gagal menandatangani nota mutasi secara digital.';
            // $this->logFailed($kd_mutasi, 'Finalisasi', $message);
            DB::table('hrd_log_mutasi')
                ->insert([
                    'kd_mutasi' => $kd_mutasi,
                    'kd_karyawan' => $kd_karyawan,
                    'tahap' => 'Finalisasi',
                    'keterangan' => $message,
                    'user' => auth()->user()->kd_karyawan,
                    'waktu' => Carbon::now()
                ]);
            
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rincian(Request $request)
    {
        $kdKaryawan = $request->kd_karyawan;

        $getRincian = DB::table('view_proses_mutasi as vpm')
        ->join('view_max_mutasi as vmm', function ($join) use ($kdKaryawan) {
            $join->on('vpm.kd_mutasi', '=', 'vmm.kd_mutasi_max')
                 ->where('vmm.kd_karyawan', '=', $kdKaryawan);
        })
        ->where('vpm.kd_karyawan', $kdKaryawan)
        ->select('vpm.*')
        ->first();
        // dd($getRincian);

        return view('mutasi.mutasi-on-process.rincian', [
            'getRincian' => $getRincian
        ]);
    }

    private function printSk($kd_karyawan, $kd_mutasi, $passphrase)
    {
        $logo = public_path('assets/media/rsud-langsa/logo-putih.png');
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');
        $logoEsign = public_path('assets/media/rsud-langsa/e-sign.png');

        $getVerifikasi = DB::table('view_verifikasi as vv')
            ->join('view_tampil_karyawan as vtk', 'vv.kd_karyawan', '=', 'vtk.kd_karyawan')
            ->leftJoin('hrd_golongan as hg', 'hg.kd_gol', '=', 'vtk.kd_gol_sekarang')
            ->where('vv.kd_karyawan', $kd_karyawan)
            ->where('vv.kd_mutasi', $kd_mutasi)
            ->select('vv.*', 'vtk.pangkat', 'hg.alias_gol as alias_gol_sekarang')
            ->first();
            
        $getDataLama = DB::table('HRD_TEMPAT_KERJA as t')
            ->join('HRD_R_MUTASI as m', function ($join) {
                $join->on('m.KD_KARYAWAN', '=', 't.KD_KARYAWAN')
                     ->on('m.KD_MUTASI', '=', 't.KD_MUTASI');
            })
            ->join('HRD_JENIS_TENAGA_SUB_DETAIL as j', function ($join) {
                $join->on('m.KD_JENIS_TENAGA', '=', 'j.KD_JENIS_TENAGA')
                     ->on('m.KD_DETAIL', '=', 'j.KD_DETAIL')
                     ->on('m.KD_SUB_DETAIL', '=', 'j.KD_SUB_DETAIL');
            })
            ->join('HRD_RUANGAN as r', 't.KD_RUANGAN', '=', 'r.kd_ruangan')
            ->select('m.KD_JENIS_TENAGA', 'm.KD_DETAIL', 'm.KD_SUB_DETAIL', 'j.SUB_DETAIL as JENIS_TENAGA', 't.KD_RUANGAN', 'r.ruangan as RUANGAN')
            ->where('t.KD_KARYAWAN', $kd_karyawan)
            ->where(function ($query) use ($kd_karyawan) {
                $query->whereRaw('t.NO_URUT = (select max(NO_URUT) - 1 from HRD_TEMPAT_KERJA where KD_KARYAWAN = ?)', [$kd_karyawan]);
            })
            ->first();

        $getDirektur = DB::table('view_tampil_karyawan as vtk')
            ->join('hrd_golongan as hg', 'vtk.kd_gol_sekarang', '=', 'hg.kd_gol')
            ->where('vtk.kd_jabatan_struktural', 1)
            ->where('vtk.status_peg', 1)
            ->select('vtk.*', 'hg.alias_gol as alias_gol_sekarang')
            ->first();

        $year = date('Y');
        // $pdfFilePath = $totalRow > 1
        //     ? 'public/sk/SK Pegawai Kontrak-' . $tahun . '.pdf'
        //     : 'public/sk/SK Pegawai Kontrak-' . $tahun . '-' . $kd_karyawan . '.pdf'
        // ;
        // buat folder di storage/app/public/mutasi-nota/2024/kd_mutasi-kd_karyawan.pdf
        $pdfFilePath = 'public/mutasi-nota/' . $year . '/' . $kd_mutasi . '-' . $kd_karyawan . '.pdf';

        // jika folder belum ada maka buat folder tersebut
        if (!Storage::exists('public/mutasi-nota/' . $year)) {
            Storage::makeDirectory('public/mutasi-nota/' . $year);
        }
        
        $PNG_WEB_DIR = storage_path('app/public/qr-code-mutasi-nota/' . $year . '/');
        if (!Storage::exists('public/qr-code-mutasi-nota/' . $year)) {
            Storage::makeDirectory('public/qr-code-mutasi-nota/' . $year);
        }
        $imgName = $kd_mutasi . '-' . $kd_karyawan . '.png';
        // "https://e-rsud.langsakota.go.id/hrd/cek-data.php?data=" . md5($result->kd_karyawan) . "&thn={$result->tahun_sk}";
        $link = "https://e-rsud.langsakota.go.id/hrd/cek-mutasi-nota.php?kd-mutasi={$kd_mutasi}&kd-karyawan={$kd_karyawan}";

        $this->generateQrCode($link, $PNG_WEB_DIR . $imgName, $logo);

        $data = [
            'getVerifikasi' => $getVerifikasi,
            'getDataLama' => $getDataLama,
            'getDirektur' => $getDirektur,
            'logo' => $logo,
            'logoLangsa' => $logoLangsa,
            'logoEsign' => $logoEsign
        ];

        $pdf = \PDF::loadView('mutasi.mutasi-on-process.nota-tugas-final', $data, [], [
            'format' => [215, 330], // ukuran kertas 21,5 x 33 cm
            'orientation' => 'P',
            'margin_top' => 5,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 5,
            'margin_footer' => 5,
            // font size 11pt
            'default_font_size' => 11,
            'default_font' => 'bookman-old-style',
            'custom_font_dir' => base_path('public/assets/fonts/'),
            'custom_font_data' => [
                'bookman-old-style' => [
                    'R' => 'Bookman Old Style Regular.ttf',
                    'B' => 'Bookman Old Style Bold.ttf',
                    'I' => 'Bookman Old Style Italic.ttf',
                    'BI' => 'Bookman Old Style Bold Italic.ttf'
                ]
            ]
        ]);

        Storage::put($pdfFilePath, $pdf->output());

        try {
            $response = $this->sendPdfForSignatures($pdfFilePath, $passphrase, $kd_mutasi, $kd_karyawan);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'error' =>  $e->getMessage()
            ]);
        }
    }

    // buat fungsi printDraftSk untuk menampilkan preview nota tugas mutasi tidak perlu tanda tangan elektronik dan langsung download jangan simpan di storage
    public function printDraftSk($kd_karyawan, $kd_mutasi)
    {
        $logo = public_path('assets/media/rsud-langsa/logo-putih.png');
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');
        $logoEsign = public_path('assets/media/rsud-langsa/e-sign.png');

        $getVerifikasi = DB::table('view_verifikasi as vv')
            ->join('view_tampil_karyawan as vtk', 'vv.kd_karyawan', '=', 'vtk.kd_karyawan')
            ->leftJoin('hrd_golongan as hg', 'hg.kd_gol', '=', 'vtk.kd_gol_sekarang')
            ->where('vv.kd_karyawan', $kd_karyawan)
            ->where('vv.kd_mutasi', $kd_mutasi)
            ->select('vv.*', 'vtk.pangkat', 'hg.alias_gol as alias_gol_sekarang')
            ->first();

            // dd($getVerifikasi);
        // $getDataLama = sqlsrv_query($konek, "select m.KD_JENIS_TENAGA, m.KD_DETAIL, m.KD_SUB_DETAIL, j.SUB_DETAIL as JENIS_TENAGA, t.KD_RUANGAN, r.ruangan as RUANGAN from HRD_TEMPAT_KERJA t inner join HRD_R_MUTASI m on m.KD_KARYAWAN = t.KD_KARYAWAN and m.KD_MUTASI = t.KD_MUTASI inner join HRD_JENIS_TENAGA_SUB_DETAIL j on m.KD_JENIS_TENAGA = j.KD_JENIS_TENAGA and m.KD_DETAIL = j.KD_DETAIL and m.KD_SUB_DETAIL = j.KD_SUB_DETAIL inner join HRD_RUANGAN r on t.KD_RUANGAN = r.kd_ruangan where t.KD_KARYAWAN = '$kdkar' and t.NO_URUT = (select max(NO_URUT) from HRD_TEMPAT_KERJA where KD_KARYAWAN = '$kdkar')");
        $getDataLama = DB::table('HRD_TEMPAT_KERJA as t')
            ->join('HRD_R_MUTASI as m', function ($join) {
                $join->on('m.KD_KARYAWAN', '=', 't.KD_KARYAWAN')
                     ->on('m.KD_MUTASI', '=', 't.KD_MUTASI');
            })
            ->join('HRD_JENIS_TENAGA_SUB_DETAIL as j', function ($join) {
                $join->on('m.KD_JENIS_TENAGA', '=', 'j.KD_JENIS_TENAGA')
                     ->on('m.KD_DETAIL', '=', 'j.KD_DETAIL')
                     ->on('m.KD_SUB_DETAIL', '=', 'j.KD_SUB_DETAIL');
            })
            ->join('HRD_RUANGAN as r', 't.KD_RUANGAN', '=', 'r.kd_ruangan')
            ->select('m.KD_JENIS_TENAGA', 'm.KD_DETAIL', 'm.KD_SUB_DETAIL', 'j.SUB_DETAIL as JENIS_TENAGA', 't.KD_RUANGAN', 'r.ruangan as RUANGAN')
            ->where('t.KD_KARYAWAN', $kd_karyawan)
            ->where(function ($query) use ($kd_karyawan) {
                $query->whereRaw('t.NO_URUT = (select max(NO_URUT) from HRD_TEMPAT_KERJA where KD_KARYAWAN = ?)', [$kd_karyawan]);
            })
            ->first();

            // dd($getDataLama);
            
        $getDirektur = DB::table('view_tampil_karyawan as vtk')
            ->join('hrd_golongan as hg', 'vtk.kd_gol_sekarang', '=', 'hg.kd_gol')
            ->where('vtk.kd_jabatan_struktural', 1)
            ->where('vtk.status_peg', 1)
            ->select('vtk.*', 'hg.alias_gol as alias_gol_sekarang')
            ->first();

            // dd($getDirektur);

        $year = date('Y');

        $PNG_WEB_DIR = storage_path('app/public/qr-code-mutasi-nota/' . $year . '/');
        if (!Storage::exists('public/qr-code-mutasi-nota/' . $year)) {
            Storage::makeDirectory('public/qr-code-mutasi-nota/' . $year);
        }
        $imgName = $kd_mutasi . '-' . $kd_karyawan . '.png';
        // "https://e-rsud.langsakota.go.id/hrd/cek-data.php?data=" . md5($result->kd_karyawan) . "&thn={$result->tahun_sk}";
        $link = "https://e-rsud.langsakota.go.id/hrd/cek-mutasi-nota.php?kd-mutasi={$kd_mutasi}&kd-karyawan={$kd_karyawan}";

        $this->generateQrCode($link, $PNG_WEB_DIR . $imgName, $logo);

        $pdf = \PDF::loadView('mutasi.mutasi-on-process.nota-tugas-final', [
            'getVerifikasi' => $getVerifikasi,
            'getDataLama' => $getDataLama,
            'getDirektur' => $getDirektur,
            'logo' => $logo,
            'logoLangsa' => $logoLangsa,
            'logoEsign' => $logoEsign
        ], [], [
            'format' => [215, 330], // ukuran kertas 21,5 x 33 cm
            'orientation' => 'P',
            'margin_top' => 5,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 5,
            'margin_footer' => 5,
            'show_watermark' => true,
            'watermark' => 'DRAFT',
            'watermark_font' => 'Arial',
            'watermark_alpha' => 0.1,
            // font size 11pt
            'default_font_size' => 11,
            'default_font' => 'bookman-old-style',
            'custom_font_dir' => base_path('public/assets/fonts/'),
            'custom_font_data' => [
                'bookman-old-style' => [
                    'R' => 'Bookman Old Style Regular.ttf',
                    'B' => 'Bookman Old Style Bold.ttf',
                    'I' => 'Bookman Old Style Italic.ttf',
                    'BI' => 'Bookman Old Style Bold Italic.ttf'
                ]
            ]
        ]);

        // return $pdf->stream();
        // return pdf with name Draft Nota Tugas Mutasi - kd_mutasi-kd_karyawan.pdf
        return $pdf->download('Draft Nota Tugas Mutasi - ' . $kd_mutasi . '-' . $kd_karyawan . '.pdf');
    }

    private function getNotaNumber($kd_karyawan)
    {
        $data = DB::table('hrd_r_mutasi')
            ->select('no_nota')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tahap_mutasi', 1)
            ->first();

        if (!$data) {
            Log::info("No data found for kd_karyawan: {$kd_karyawan} and kd_tahap_mutasi: 1");
        } else {
            Log::info("Data found: ", (array)$data);
        }

        // jika nomor nota kosong
        if (!$data || !$data->no_nota) {
            $year = date('Y');

            $fetchNotaNumber = DB::table('hrd_r_mutasi')
                ->select(DB::raw('SUBSTRING(no_nota, 7, 3) as nomor'))
                ->whereRaw('RIGHT(no_nota, 4) = ?', [$year])
                ->orderBy(DB::raw('SUBSTRING(no_nota, 7, 3)'), 'desc')
                ->first();

            if (!$fetchNotaNumber) {
                Log::info("No nota number found for the year: {$year}");
            } else {
                Log::info("Fetched nota number: ", (array)$fetchNotaNumber);
            }

            $currentNumber = $fetchNotaNumber ? (int) $fetchNotaNumber->nomor + 1 : 1;
            $notaNumber = sprintf('%03s', $currentNumber);

            $no_nota = "875.1/{$notaNumber}/NT/{$year}";
            // update nomor nota ke tabel mutasi
            DB::table('hrd_r_mutasi')
                ->where('kd_karyawan', $kd_karyawan)
                ->where('kd_tahap_mutasi', 1)
                ->update([
                    'no_nota' => $no_nota
                ]);
            return $no_nota;
        }

        return $data->no_nota;
    }

    private function sendPdfForSignatures($pdfFilePath, $passphrase, $kd_mutasi, $kd_karyawan)
    {
        $endpoint = "http://123.108.100.83:85/api/sign/pdf";
        $client = new Client();

        try {
            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Basic ZXNpZ246cXdlcnR5'
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen(storage_path('app/' . $pdfFilePath), 'r'),
                        'filename' => basename($pdfFilePath)
                    ],
                    [
                        'name' => 'nik',
                        'contents' => '1271022205700001'
                    ],
                    [
                        'name' => 'passphrase',
                        'contents' => $passphrase
                    ],
                    [
                        'name' => 'tampilan',
                        'contents' => 'invisible'
                    ],
                ],
                'timeout' => 480,
            ]);
            
            // return response from headers
            $headers = $response->getHeaders();
            $id_dokumen = $headers['id_dokumen'][0];
            
            // if $headers['original']['code'] == 200 and id_dokumen is not empty
            if ($response->getStatusCode() == 200 && !empty($id_dokumen)) {
                // $this->updateVerification($kd_karyawan, $urut, $tahun, $id_dokumen);
                // save id_dokumen to table hrd_r_mutasi
                DB::table('hrd_r_mutasi')
                    ->where('kd_karyawan', $kd_karyawan)
                    ->where('kd_mutasi', $kd_mutasi)
                    ->update([
                        'id_dokumen' => $id_dokumen
                    ]);

                // download the document
                $this->downloadSignedDocument($id_dokumen);

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Proses TTE berhasil.'
                ]);
            } else {
                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Proses TTE gagal.' . $headers['original']['message'],
                ], 500);
            }
        } catch (\Exception $e) {
            // $this->serverError($kd_karyawan, $urut, $tahun);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function downloadSignedDocument($id_dokumen)
    {
        $endpoint = "http://123.108.100.83:85/api/sign/download/" . $id_dokumen;

        $filename = 'Nota Tugas Mutasi-' . $id_dokumen . '.pdf';
        $year = date('Y');
        $directory = 'public/mutasi-nota-tte/' . $year;
        $filePath = $directory . '/' . $filename;

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        // store the file to storage
        $client = new Client();
        $response = $client->request('GET', $endpoint, [
            'headers' => [
                'Authorization' => 'Basic ZXNpZ246cXdlcnR5'
            ],
            'sink' => storage_path('app/' . $filePath)
        ]);

        // save to database
        DB::table('hrd_r_mutasi')
            ->where('id_dokumen', $id_dokumen)
            ->update([
                'path_dokumen' => $filePath
            ])
        ;

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'File berhasil diunduh.'
        ]);
    }

    private function generateQrCode($data, $path, $logo)
    {
        $writer = new PngWriter();
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(350)
            ->setMargin(10)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        

        $result = $writer->write($qrCode);

        $qrImage = Image::make($result->getString());
        $logoImage = Image::make($logo)->resize(120, 120);

        $qrImage->insert($logoImage, 'center');
        $qrImage->save($path);
    }

    private function logSuccess($kd_mutasi, $action, $message)
    {
        DB::table('hrd_log_mutasi')
            ->insert([
                'mutasi_id' => $kd_mutasi,
                'user_id' => auth()->user()->kd_karyawan,
                'aksi' => $action,
                'pesan' => $message,
                'waktu' => Carbon::now()
            ]);
    }

    private function logFailed($kd_mutasi, $action, $message)
    {
        DB::table('hrd_log_mutasi')
            ->insert([
                'mutasi_id' => $kd_mutasi,
                'user_id' => auth()->user()->kd_karyawan,
                'aksi' => $action,
                'pesan' => $message,
                'waktu' => Carbon::now()
            ]);
    }

    public function getLogMutasi(Request $request) {
        $logData = DB::table('hrd_r_mutasi as hrm')
            ->join('view_tampil_karyawan as vtk', 'vtk.kd_karyawan', '=', 'hrm.kd_karyawan')
            ->select('hrm.*', 'vtk.nama')
            ->where('hrm.kd_karyawan', $request->kd_karyawan)
            ->where('hrm.kd_mutasi', $request->kd_mutasi)
            ->first();

        // if (!$logData) {
        //     return response('Data tidak ditemukan.');
        // }

        $verifData = [
            [
                'verif' => '1',
                'kd_karyawan_verif' => $logData->kd_karyawan_verif_1,
                'waktu_verif' => $logData->waktu_verif_1,
                'nama_verif' => DB::table('view_tampil_karyawan')->where('kd_karyawan', $logData->kd_karyawan_verif_1)->value('nama')
            ],
            [
                'verif' => '2',
                'kd_karyawan_verif' => $logData->kd_karyawan_verif_2,
                'waktu_verif' => $logData->waktu_verif_2,
                'nama_verif' => DB::table('view_tampil_karyawan')->where('kd_karyawan', $logData->kd_karyawan_verif_2)->value('nama')
            ],
            [
                'verif' => '3',
                'kd_karyawan_verif' => $logData->kd_karyawan_verif_3,
                'waktu_verif' => $logData->waktu_verif_3,
                'nama_verif' => DB::table('view_tampil_karyawan')->where('kd_karyawan', $logData->kd_karyawan_verif_3)->value('nama')
            ],
            [
                'verif' => '4',
                'kd_karyawan_verif' => $logData->kd_karyawan_verif_4,
                'waktu_verif' => $logData->waktu_verif_4,
                'nama_verif' => DB::table('view_tampil_karyawan')->where('kd_karyawan', $logData->kd_karyawan_verif_4)->value('nama')
            ],
        ];

        return view('mutasi.mutasi-on-process.timeline-item', ['verifData' => $verifData]);


        // $getLog = DB::table('hrd_r_mutasi as hrm')
        //     ->join('view_tampil_karyawan as vtk', 'vtk.kd_karyawan', '=', 'hrm.kd_karyawan')
        //     ->leftJoin('view_tampil_karyawan as verif1', 'verif1.kd_karyawan', '=', 'hrm.kd_karyawan_verif_1')
        //     ->leftJoin('view_tampil_karyawan as verif2', 'verif2.kd_karyawan', '=', 'hrm.kd_karyawan_verif_2')
        //     ->leftJoin('view_tampil_karyawan as verif3', 'verif3.kd_karyawan', '=', 'hrm.kd_karyawan_verif_3')
        //     ->leftJoin('view_tampil_karyawan as verif4', 'verif4.kd_karyawan', '=', 'hrm.kd_karyawan_verif_4')
        //     ->select(
        //         'hrm.*', 
        //         'vtk.nama',
        //         'verif1.nama as verif1_nama',
        //         'verif2.nama as verif2_nama',
        //         'verif3.nama as verif3_nama',
        //         'verif4.nama as verif4_nama'
        //     )
        //     ->where('hrm.kd_karyawan', $request->kd_karyawan)
        //     ->where('hrm.kd_mutasi', $request->kd_mutasi)
        //     ->first();

        // $log = [];
        
        // if ($getLog) {
        //     for ($i = 1; $i <= 4; $i++) {
        //         $verifKey = "verif_{$i}";
        //         $karyawanVerifKey = "kd_karyawan_verif_{$i}";
        //         $waktuVerifKey = "waktu_verif_{$i}";
        //         $namaVerifKey = "verif{$i}_nama";

        //         if ($getLog->$verifKey !== null) {
        //             $log[] = [
        //                 'verif' => $i,
        //                 'kd_karyawan_verif' => $getLog->$karyawanVerifKey,
        //                 'waktu_verif' => $getLog->$waktuVerifKey,
        //                 'nama_verif' => $getLog->$namaVerifKey,
        //             ];
        //         }
        //     }
        // }

        // return response()->json($log);
    }
}
