<?php

namespace App\Http\Controllers\Tugas_Tambahan;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class TugasOnProcessController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');   
    }

    public function index()
    {
        $getTugasTambahan = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 1)
            ->orderBy('kd_tugas_tambahan', 'desc')
            ->groupBy('kd_tugas_tambahan')
            ->get();
            // print_r($getTugasTambahan);
            // die;
        // dd($getTugasTambahan);
        $totalTugasTambahanOnProcess = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugas_tambahan')
            ->where('kd_tahap_tugas_tambahan', 1)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();

        $totalTugasTambahanPending = DB::table('hrd_r_tugas_tambahan')
            ->select('kd_tugasTambahan')
            ->where('kd_tahap_tugas_tambahan', 0)
            ->where('kd_jenis_tugas_tambahan', 1)
            ->count();

        return view('tugas-tambahan.on-process.index', [
            'getTugasTambahan' => $getTugasTambahan,
            'totalTugasTambahanOnProcess' => $totalTugasTambahanOnProcess,
            'totalTugasTambahanPending' => $totalTugasTambahanPending
        ]);
    }

    public function firstVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        // $getNoHp = DB::table('hrd_karyawan')
        //     ->select('no_hp')
        //     ->where('kd_karyawan', '001635')
        //     ->first();

        // // 0822 6791 3292 _  ubah ke +6282267913292
        // $no_hp = $this->formatPhoneNumber($getNoHp->no_hp);

        $getDataKaryawan = DB::table('hrd_karyawan')
            ->select('gelar_depan', 'nama', 'gelar_belakang')
            ->where('kd_karyawan', $kd_karyawan)
            ->first();

        $gelar_depan = $getDataKaryawan->gelar_depan ? $getDataKaryawan->gelar_depan . ' ' : '';
        $gelar_belakang = $getDataKaryawan->gelar_belakang ? '' . $getDataKaryawan->gelar_belakang : '';
        $nama_karyawan = $gelar_depan . $getDataKaryawan->nama . $gelar_belakang;

        // $message = "Ada Mutasi Nota Tugas Baru dengan rincian sebagai berikut:\nKode Mutasi: {$kd_tugas_tambahan}\nKode Karyawan: {$kd_karyawan}\nNama Karyawan: {$nama_karyawan}\nSilahkan cek aplikasi pada aplikasi HRD untuk melakukan verifikasi atau dengan klik link berikut:\nhttps://e-rsud.langsakota.go.id/rsud_hrd/admin/mutasi-on-process";

        $update = DB::table('hrd_r_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_1' => 1,
                'kd_karyawan_verif_1' => auth()->user()->kd_karyawan,
                'waktu_verif_1' => Carbon::now()
            ]);

        if ($update) {
            // $this->logSuccess($kd_tugas_tambahan, 'Verifikasi 1', 'Nota Tugas Tambahan berhasil diverifikasi oleh Kasubbag. Kepegawaian');

            

            // $this->whatsappNotification($no_hp, $message);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Nota Tugas Tambahan berhasil diverifikasi oleh Kasubbag. Kepegawaian'
            ]);
        } else {
            // $this->logFailed($kd_tugas_tambahan, 'Verifikasi 1', 'Nota Tugas Tambahan gagal diverifikasi oleh Kasubbag. Kepegawaian');

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Tugas Tambahan gagal diverifikasi oleh Kasubbag. Kepegawaian'
            ]);
        }
    }

    public function secondVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        $update = DB::table('hrd_r_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_2' => 1,
                'kd_karyawan_verif_2' => auth()->user()->kd_karyawan,
                'waktu_verif_2' => Carbon::now()
            ]);

        if ($update) {
            // $this->logSuccess($kd_tugas_tambahan, 'Verifikasi 2', 'Nota Tugas Tambahan berhasil diverifikasi oleh Kabag. TU.');

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Nota Tugas Tambahan berhasil diverifikasi oleh Kabag. TU.'
            ]);
        } else {
            // $this->logFailed($kd_tugas_tambahan, 'Verifikasi 2', 'Nota Tugas Tambahan gagal diverifikasi oleh Kabag. TU.');

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Tugas Tambahan gagal diverifikasi oleh Kabag. TU.'
            ]);
        }
    }

    public function thirdVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        $update = DB::table('hrd_r_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_3' => 1,
                'kd_karyawan_verif_3' => auth()->user()->kd_karyawan,
                'waktu_verif_3' => Carbon::now()
            ]);

        if ($update) {
            // $this->logSuccess($kd_tugas_tambahan, 'Verifikasi 3', 'Nota Tugas Tambahan berhasil diverifikasi oleh Wadir. ADM dan Umum.');

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Nota Tugas Tambahan berhasil diverifikasi oleh Wadir. ADM dan Umum.'
            ]);
        } else {
            // $this->logFailed($kd_tugas_tambahan, 'Verifikasi 3', 'Nota Tugas Tambahan gagal diverifikasi oleh Wadir. ADM dan Umum.');

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Tugas Tambahan gagal diverifikasi oleh Wadir. ADM dan Umum.'
            ]);
        }
    }

    public function fourthVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        DB::beginTransaction();

        try {
            $update = DB::table('hrd_r_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_4' => 1,
                'kd_karyawan_verif_4' => auth()->user()->kd_karyawan,
                'waktu_verif_4' => Carbon::now()
            ]);

            // $this->logSuccess($kd_tugas_tambahan, 'Verifikasi 4', 'Nota Tugas Tambahan berhasil diverifikasi oleh Direktur.');

            DB::commit();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Nota Tugas Tambahan berhasil diverifikasi oleh Direktur.'
            ]);
        } catch (\Exception $e) {
            // $this->logFailed($kd_tugas_tambahan, 'Verifikasi 4', 'Nota Tugas Tambahan gagal diverifikasi oleh Direktur.' . $e->getMessage());

            DB::rollBack();

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Tugas Tambahan gagal diverifikasi oleh Direktur. ' . $e->getMessage()
            ]);
        }
    }

    public function finalisasi(Request $request)
    {
        $kd_tugas_tambahan = $request->kd_mutasi;
        $kd_karyawan = $request->kd_karyawan;
        $passphrase = $request->passphrase;

        DB::beginTransaction();

        try {
            $no_nota = $this->getNotaNumber($kd_karyawan);
            // dd($no_nota);

            $update = DB::table('hrd_r_tugas_tambahan')
                ->where('kd_karyawan', $kd_karyawan)
                ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
                ->update([
                    'kd_tahap_tugas_tambahan' => 2,
                    'stt_ttd' => 1,
                    'stt_stempel' => 1,
                    'user_update' => auth()->user()->kd_karyawan,
                    'tgl_update' => Carbon::now(),
                    'no_nota' => $no_nota
                ]);

            $getEmail = DB::table('hrd_karyawan as hk')
                ->leftJoin('hrd_tempat_kerja_tugas_tambahan as htktt', 'hk.kd_karyawan', '=', 'htktt.kd_karyawan')
                ->select('hk.email', DB::raw('Max(htktt.no_urut) as urut_max'))
                ->where('hk.kd_karyawan', $kd_karyawan)
                ->groupBy('hk.email')
                ->first();

            // get urut max and plus 1
            $urutMax = $getEmail->urut_max + 1;

            // $getMutasi = sqlsrv_query($konek, "select * from HRD_R_MUTASI where KD_MUTASI = '$kdmutasi'");
            $getMutasi = DB::table('hrd_r_tugas_tambahan')
                ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
                ->first();

            // $query = sqlsrv_query($konek, "insert into HRD_TEMPAT_KERJA (KD_KARYAWAN, KD_DIVISI, KD_UNIT_KERJA, KD_SUB_UNIT_KERJA, KD_RUANGAN, TGL_MASUK, KD_MUTASI, NO_URUT) select M.KD_KARYAWAN, M.KD_DIVISI, M.KD_UNIT_KERJA, M.KD_SUB_UNIT_KERJA, M.KD_RUANGAN, M.TMT_JABATAN, M.KD_MUTASI, '$urut2' from HRD_R_MUTASI M where M.KD_KARYAWAN = '$kdkar' and M.KD_MUTASI = '$kdmutasi'");
            DB::table('hrd_tempat_kerja_tugas_tambahan')
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

            // jangan update data karyawan, hanya update data tempat kerja
            // DB::table('hrd_karyawan')
            //     ->where('kd_karyawan', $kd_karyawan)
            //     ->update([
            //         'kd_jabatan_struktural' => $getMutasi->kd_jab_struk,
            //         'tmt_jabatan_struktural' => $getMutasi->tmt_jabatan,
            //         'kd_jenis_tenaga' => $getMutasi->kd_jenis_tenaga,
            //         'kd_detail_jenis_tenaga' => $getMutasi->kd_detail,
            //         'kd_sub_detail_jenis_tenaga' => $getMutasi->kd_sub_detail,
            //         'kd_divisi' => $getMutasi->kd_divisi,
            //         'kd_unit_kerja' => $getMutasi->kd_unit_kerja,
            //         'kd_sub_unit_kerja' => $getMutasi->kd_sub_unit_kerja,
            //         'kd_ruangan' => $getMutasi->kd_ruangan
            //     ]);

            // $query = sqlsrv_query($konek, "update HRD_TEMPAT_KERJA set TGL_KELUAR = (select TMT_JABATAN from HRD_R_MUTASI where KD_KARYAWAN = '$kdkar' and KD_MUTASI = '$kdmutasi') WHERE KD_KARYAWAN = '$kdkar' and NO_URUT = (select MAX(NO_URUT - 1) from HRD_TEMPAT_KERJA where KD_KARYAWAN = '$kdkar')");
            DB::table('hrd_tempat_kerja_tugas_tambahan')
                ->where('kd_karyawan', $kd_karyawan)
                ->where('no_urut', $urutMax - 1)
                ->update([
                    'tgl_keluar' => $getMutasi->tmt_jabatan
                ]);

            $printSkResponse = $this->printSk($kd_karyawan, $kd_tugas_tambahan, $passphrase);
            $printSkData = json_decode($printSkResponse->getContent(), true);

            if ($printSkData['original']['code'] == 200) {
                $this->logSuccess($kd_tugas_tambahan, 'Finalisasi', 'Nota Mutasi berhasil ditanda tangani secara digital.');

                DB::commit();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Nota Mutasi berhasil ditanda tangani secara digital.'
                ]);
            } else {
                $message = $printSkData['original']['message'] ?? 'Gagal menandatangani nota mutasi secara digital.';
                // $this->logFailed($kd_mutasi, 'Finalisasi', $message);
                $this->logFailed($kd_mutasi, 'Finalisasi', 'Nota Mutasi gagal ditanda tangani secara digital.' . $message);
                // DB::table('hrd_log_mutasi')
                //     ->insert([
                //         'kd_mutasi' => $kd_mutasi,
                //         'kd_karyawan' => $kd_karyawan,
                //         'tahap' => 'Finalisasi',
                //         'keterangan' => $message,
                //         'user' => auth()->user()->kd_karyawan,
                //         'waktu' => Carbon::now()
                //     ]);
                
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
            $this->logFailed($kd_mutasi, 'Finalisasi', 'Nota Mutasi gagal ditanda tangani secara digital.' . $e->getMessage());
            // DB::table('hrd_log_mutasi')
            //     ->insert([
            //         'kd_mutasi' => $kd_mutasi,
            //         'kd_karyawan' => $kd_karyawan,
            //         'tahap' => 'Finalisasi',
            //         'keterangan' => $message,
            //         'user' => auth()->user()->kd_karyawan,
            //         'waktu' => Carbon::now()
            //     ]);
            
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function printDraftSk($kd_karyawan, $kd_tugas_tambahan)
    {
        $logo = public_path('assets/media/rsud-langsa/logo-putih.png');
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');
        $logoEsign = public_path('assets/media/rsud-langsa/e-sign.png');

        $getVerifikasi = DB::table('view_verifikasi_tugas_tambahan as vv')
            ->join('view_tampil_karyawan as vtk', 'vv.kd_karyawan', '=', 'vtk.kd_karyawan')
            ->leftJoin('hrd_golongan as hg', 'hg.kd_gol', '=', 'vtk.kd_gol_sekarang')
            ->where('vv.kd_karyawan', $kd_karyawan)
            ->where('vv.kd_tugas_tambahan', $kd_tugas_tambahan)
            ->select('vv.*', 'vtk.pangkat', 'hg.alias_gol as alias_gol_sekarang')
            ->first();
        
        // $getDataLama = DB::table('HRD_TEMPAT_KERJA_TUGAS_TAMBAHAN as tempat')
        //     ->join('HRD_R_TUGAS_TAMBAHAN as tugas', function ($join) {
        //         $join->on('tugas.KD_KARYAWAN', '=', 'tempat.KD_KARYAWAN')
        //             ->on('tugas.kd_tugas_tambahan', '=', 'tempat.kd_tugas_tambahan');
        //     })
        //     ->join('HRD_JENIS_TENAGA_SUB_DETAIL as j', function ($join) {
        //         $join->on('tugas.KD_JENIS_TENAGA', '=', 'j.KD_JENIS_TENAGA')
        //             ->on('tugas.KD_DETAIL', '=', 'j.KD_DETAIL')
        //             ->on('tugas.KD_SUB_DETAIL', '=', 'j.KD_SUB_DETAIL');
        //     })
        //     ->join('HRD_RUANGAN as r', 'tempat.KD_RUANGAN', '=', 'r.kd_ruangan')
        //     ->select('tugas.KD_JENIS_TENAGA', 'tugas.KD_DETAIL', 'tugas.KD_SUB_DETAIL', 'j.SUB_DETAIL as JENIS_TENAGA', 'tempat.KD_RUANGAN', 'r.ruangan as RUANGAN')
        //     ->where('tempat.KD_KARYAWAN', $kd_karyawan)
        //     ->where(function ($query) use ($kd_karyawan) {
        //         $query->whereRaw('tempat.NO_URUT = (select max(NO_URUT) from HRD_TEMPAT_KERJA where KD_KARYAWAN = ?)', [$kd_karyawan]);
        //     })
        //     ->first();

        $getDataLama = DB::table('HRD_R_TUGAS_TAMBAHAN as tugas')
            ->join('HRD_JENIS_TENAGA_SUB_DETAIL as j', function ($join) {
                $join->on('tugas.KD_JENIS_TENAGA', '=', 'j.KD_JENIS_TENAGA')
                     ->on('tugas.KD_DETAIL', '=', 'j.KD_DETAIL')
                     ->on('tugas.KD_SUB_DETAIL', '=', 'j.KD_SUB_DETAIL');
            })
            ->join('HRD_RUANGAN as r', 'tugas.KD_RUANGAN', '=', 'r.kd_ruangan')
            ->select('tugas.KD_JENIS_TENAGA', 'tugas.KD_DETAIL', 'tugas.KD_SUB_DETAIL', 'j.SUB_DETAIL as JENIS_TENAGA', 'tugas.KD_RUANGAN', 'r.ruangan as RUANGAN')
            ->where('tugas.KD_KARYAWAN', $kd_karyawan)
            ->where(function ($query) use ($kd_karyawan) {
                $query->whereRaw('tugas.NO_URUT = (select max(NO_URUT) from HRD_R_TUGAS_TAMBAHAN where KD_KARYAWAN = ?)', [$kd_karyawan]);
            })
            ->first();
            dd($getDataLama);
            
        $getDirektur = DB::table('view_tampil_karyawan as vtk')
            ->join('hrd_golongan as hg', 'vtk.kd_gol_sekarang', '=', 'hg.kd_gol')
            ->where('vtk.kd_jabatan_struktural', 1)
            ->where('vtk.status_peg', 1)
            ->select('vtk.*', 'hg.alias_gol as alias_gol_sekarang')
            ->first();

            // dd($getDirektur);

        $year = date('Y');

        $PNG_WEB_DIR = storage_path('app/public/qr-code-tugas-tambahan/' . $year . '/');
        if (!Storage::exists('public/qr-code-tugas-tambahan/' . $year)) {
            Storage::makeDirectory('public/qr-code-tugas-tambahan/' . $year);
        }
        $imgName = $kd_tugas_tambahan . '-' . $kd_karyawan . '.png';
        // "https://e-rsud.langsakota.go.id/hrd/cek-data.php?data=" . md5($result->kd_karyawan) . "&thn={$result->tahun_sk}";
        $link = "https://e-rsudlangsa.id/hrd/cek-tugas-tambahan.php?kd-mutasi={$kd_tugas_tambahan}&kd-karyawan={$kd_karyawan}";

        $this->generateQrCode($link, $PNG_WEB_DIR . $imgName, $logo);

        $pdf = \PDF::loadView('tugas-tambahan.on-process.nota-tugas-final', [
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
        return $pdf->download('Draft Nota Tugas Tambahan - ' . $kd_tugas_tambahan . '-' . $kd_karyawan . '.pdf');
    }
    
    private function getNotaNumber($kd_karyawan)
    {
        $data = DB::table('hrd_r_tugas_tambahan')
            ->select('no_nota')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tahap_tugas_tambahan', 1)
            ->first();

        if (!$data) {
            Log::info("No data found for kd_karyawan: {$kd_karyawan} and kd_tahap_tugas_tambahan: 1");
        } else {
            Log::info("Data found: ", (array)$data);
        }

        // jika nomor nota kosong
        if (!$data || !$data->no_nota) {
            $year = date('Y');

            $fetchNotaNumber = DB::table('hrd_r_tugas_tambahan')
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
            // update nomor nota ke tabel tugas tambahan
            DB::table('hrd_r_tugas_tambahan')
                ->where('kd_karyawan', $kd_karyawan)
                ->where('kd_tahap_tugas_tambahan', 1)
                ->update([
                    'no_nota' => $no_nota
                ]);
            return $no_nota;
        }

        return $data->no_nota;
    }

    private function sendPdfForSignatures($pdfFilePath, $passphrase, $kd_tugas_tambahan, $kd_karyawan)
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
                // save id_dokumen to table hrd_r_tugas_tambahan
                DB::table('hrd_r_tugas_tambahan')
                    ->where('kd_karyawan', $kd_karyawan)
                    ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
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

        $filename = 'Nota Tugas Tugas Tambahan-' . $id_dokumen . '.pdf';
        $year = date('Y');
        $directory = 'public/nota-tugas-tambahan-tte/' . $year;
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
        DB::table('hrd_r_tugas_tambahan')
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

    public function rincian(Request $request)
    {
        $kdKaryawan = $request->kd_karyawan;

        $getRincian = DB::table('view_proses_tugas_tambahan as vptt')
        ->join('view_max_tugas_tambahan as vmtt', function ($join) use ($kdKaryawan) {
            $join->on('vptt.kd_tugas_tambahan', '=', 'vmtt.kd_tugas_tambahan_max')
                 ->where('vmtt.kd_karyawan', '=', $kdKaryawan);
        })
        ->where('vptt.kd_karyawan', $kdKaryawan)
        ->select('vptt.*')
        ->first();
        // dd($getRincian);

        return view('tugas-tambahan.on-process.rincian', [
            'getRincian' => $getRincian
        ]);
    }

    public function getLogTugasTambahan(Request $request) 
    {
        $logData = DB::table('hrd_r_tugas_tambahan as tugas')
            ->join('view_tampil_karyawan as vtk', 'vtk.kd_karyawan', '=', 'tugas.kd_karyawan')
            ->select('tugas.*', 'vtk.nama')
            ->where('tugas.kd_karyawan', $request->kd_karyawan)
            ->where('tugas.kd_tugas_tambahan', $request->kd_tugas_tambahan)
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

        return view('tugas-tambahan.on-process.timeline-item', ['verifData' => $verifData]);


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
