<?php

namespace App\Http\Controllers\Tugas_Tambahan;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Illuminate\Support\Facades\Auth;

class TugasTambahanController extends Controller
{
    public function __construct()
    {
        
        $kd_karywan = ['001635', '001367', '000151', '000574', '001022', '001520', '000161', '000374', '000121'];

        // jika ada user yang mengakses halaman ini dan tidak memiliki salah satu dari kd_karyawan di atas, maka akan diarahkan ke halaman 404
        // if (!in_array(auth()->user()->kd_karyawan, $kd_karywan)) {
        //     abort(404);
        // }
    }

    public function index(Request $request)
    {
        $kd_karywan = ['001635', '001367', '000151', '000574', '001022', '001520', '000161', '000374', '000121'];
        // jika ada user yang mengakses halaman ini dan tidak memiliki salah satu dari kd_karyawan di atas, maka akan diarahkan ke halaman 404
        if (!in_array(auth()->user()->kd_karyawan, $kd_karywan)) {
            abort(403, 'You are not authorized to access this page');
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : Carbon::now()->endOfMonth()->format('Y-m-d');

        // Lanjutkan query Anda menggunakan $startDate dan $endDate
        $getTugasTambahan = DB::table('hrd_tugas_tambahan as tugas')
            ->join('hrd_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
            ->select('tugas.*', 'karyawan.nama', 'karyawan.gelar_depan', 'karyawan.gelar_belakang', 'karyawan.kd_karyawan', 'karyawan.nip_baru', 'karyawan.no_karpeg', 'karyawan.tempat_lahir', 'karyawan.tgl_lahir')
            ->whereBetween('tmt_awal', [$startDate, $endDate])
            ->orderBy('kd_tugas_tambahan', 'desc')
            ->get();

        if (request()->ajax()) {
            return datatables()->of($getTugasTambahan)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    return view('tugas-tambahan._action', compact('row'));
                })
                ->addColumn('nama_karyawan', function($row) {
                    return trim("{$row->gelar_depan} {$row->nama} {$row->gelar_belakang}");
                })
                ->addColumn('karyawan', function($row) {
                    $nip = $row->nip_baru ? "NIP: {$row->nip_baru}" : '';
                    $karpeg = $row->no_karpeg ? "KARPEG: {$row->no_karpeg}" : '';
                    return trim("{$row->gelar_depan} {$row->nama}{$row->gelar_belakang}") . '<br>' . $row->tempat_lahir . ', ' . Carbon::parse($row->tgl_lahir)->translatedFormat('d-m-Y') . '<br>' . $nip . '<br>' . $karpeg;
                })
                ->addColumn('tmt_awal', function($row) {
                    return Carbon::parse($row->tmt_awal)->translatedFormat('d F Y');
                })
                ->addColumn('tmt_akhir', function($row) {
                    return Carbon::parse($row->tmt_akhir)->translatedFormat('d F Y');
                })
                // buat tmt seperti ini 01-10-2024 s/d 01-10-2025
                ->addColumn('tmt', function($row) {
                    $tmt_awal = $row->tmt_awal ? Carbon::parse($row->tmt_awal)->translatedFormat('d-m-Y') : '-';
                    $tmt_akhir = $row->tmt_akhir ? Carbon::parse($row->tmt_akhir)->translatedFormat('d-m-Y') : '-';

                    return "{$tmt_awal} s/d {$tmt_akhir}";
                })
                ->addColumn('status', function($row) {
                    if ($row->verif_1 == null) {
                        return 'Menunggu verifikasi Kasubbag. Kepeg.';
                    } else if ($row->verif_2 == null) {
                        return 'Menunggu verifikasi Kabag. TU';
                    } else if ($row->verif_3 == null) {
                        return 'Menunggu verifikasi Wadir ADM dan Umum';
                    } else if ($row->verif_4 == null) {
                        return 'Menunggu verifikasi Direktur';
                    }
                })
                ->rawColumns(['action', 'karyawan'])
                ->make(true);
        }

        $jabatanStruktural = DB::table('hrd_jabatan_struktural')
            ->orderBy('jab_struk', 'ASC')
            ->get();

        $subDetail = DB::table('hrd_jenis_tenaga_sub_detail')
            ->orderBy('sub_detail', 'ASC')
            ->get();

        $ruangan = DB::table('hrd_ruangan')
            ->where('status_aktif', 1)
            ->orderBy('ruangan', 'ASC')
            ->get();

        return view('tugas-tambahan.index', compact('jabatanStruktural', 'subDetail', 'ruangan', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'karyawan' => 'required',
            'tugas' => 'required',
            'awal' => 'required',
            'akhir' => 'nullable',
            'isi_nota' => 'required',
            'isi_nota_2' => 'required',
        ], [
            'karyawan.required' => 'Karyawan harus tidak boleh kosong',
            'tugas.required' => 'Nama tugas tambahan harus diisi',
            'awal.required' => 'TMT awal harus diisi',
            'isi_nota.required' => 'Isi nota harus diisi',
            'isi_nota_2.required' => 'Isi nota 2 harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid',
                'errors' => $validator->errors()
            ], 422);
        }

        // make kd_tugas_tambahan using time
        $kd_tugas_tambahan = time();
        $tmt_awal = $request->awal ? Carbon::createFromFormat('d/m/Y', $request->awal)->format('Y-m-d') : null;
        $tmt_akhir = $request->akhir ? Carbon::createFromFormat('d/m/Y', $request->akhir)->format('Y-m-d') : null;
        $jenisTenaga = $request->sub_jenis_tenaga;

        $subDetailJenisTenaga = DB::table('hrd_jenis_tenaga_sub_detail')
            ->where('sub_detail', $jenisTenaga)
            ->first();

        $kdJenisTenaga = $subDetailJenisTenaga ? $subDetailJenisTenaga->kd_jenis_tenaga : null;
        $kdDetail = $subDetailJenisTenaga ? $subDetailJenisTenaga->kd_detail : null;
        $kdSubDetail = $subDetailJenisTenaga ? $subDetailJenisTenaga->kd_sub_detail : null;

        $insert = DB::table('hrd_tugas_tambahan')
            ->insert([
                'kd_tugas_tambahan' => $kd_tugas_tambahan,
                'kd_karyawan' => $request->karyawan,
                'nama_tugas_tambahan' => $request->tugas,
                'tmt_awal' => $tmt_awal,
                'tmt_akhir' => $tmt_akhir,
                'isi_nota' => $request->isi_nota,
                'isi_nota_2' => $request->isi_nota_2,
                'user_update' => auth()->user()->kd_karyawan,
                'tgl_update' => date('Y-m-d H:i:s'),
                'tahun' => Carbon::now()->format('Y'),
                'kd_ruangan' => $request->ruangan ?? null,
                'kd_jab_struk' => $request->jab_struk ?? null,
                'kd_jenis_tenaga' => $kdJenisTenaga ?? null,
                'kd_detail' => $kdDetail ?? null,
                'kd_sub_detail' => $kdSubDetail ?? null,
            ]);

        if ($insert) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data tugas tambahan berhasil disimpan'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Data tugas tambahan gagal disimpan'
            ], 500);
        }

    }

    public function firstVerifivation(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        $update = DB::table('hrd_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_1' => 1,
                'kd_karyawan_verif_1' => auth()->user()->kd_karyawan,
                'waktu_verif_1' => Carbon::now()
            ]);

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Verifikasi berhasil'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Verifikasi gagal'
            ], 500);
        }
    }

    public function secondVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        $update = DB::table('hrd_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_2' => 1,
                'kd_karyawan_verif_2' => auth()->user()->kd_karyawan,
                'waktu_verif_2' => Carbon::now()
            ]);

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Verifikasi berhasil'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Verifikasi gagal'
            ], 500);
        }
    }

    public function thirdVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        $update = DB::table('hrd_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_3' => 1,
                'kd_karyawan_verif_3' => auth()->user()->kd_karyawan,
                'waktu_verif_3' => Carbon::now()
            ]);

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Verifikasi berhasil'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Verifikasi gagal'
            ], 500);
        }
    }

    public function fourthVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        $update = DB::table('hrd_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_4' => 1,
                'kd_karyawan_verif_4' => auth()->user()->kd_karyawan,
                'waktu_verif_4' => Carbon::now()
            ]);

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Verifikasi berhasil'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Verifikasi gagal'
            ], 500);
        }
    }

    public function finalisasi(Request $request)
    {
        // validasi request
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required',
            'passphrase' => 'required',
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'passphrase.required' => 'Passphrase harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid',
                'errors' => $validator->errors()
            ], 422);
        }


        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;
        $tgl_ttd = $request->tanggal;
        $passphrase = $request->passphrase;

        DB::beginTransaction();

        try {
            $no_nota = $this->getNotaNumber($kd_tugas_tambahan);

            $update = DB::table('hrd_tugas_tambahan')
                ->where('kd_karyawan', $kd_karyawan)
                ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
                ->update([
                    'no_nota' => $no_nota,
                    'tgl_ttd' => $tgl_ttd,
                ]);

            $printNotaResponse = $this->printNota($kd_karyawan, $kd_tugas_tambahan, $passphrase);
            $printNotaData = json_decode($printNotaResponse->getContent(), true);

            if ($printNotaData['original']['code'] == 200) {
                DB::commit();

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Nota Tugas Tambahan berhasil ditandatangani.'
                ]);
            } else {
                $message = $printNotaData['original']['message'] ?? 'Nota Tugas Tambahan gagal ditandatangani.';

                DB::rollBack();

                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Nota Tugas Tambahan gagal ditandatangani. ' . $printNotaData['original']['message']
                ], 400);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage() ?? 'Nota Tugas Tambahan gagal ditandatangani.';
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Nota Tugas Tambahan gagal ditandatangani. ' . $message,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rincian(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        // INNER JOIN dbo.HRD_JENIS_TENAGA_SUB_DETAIL ON dbo.HRD_JENIS_TENAGA_SUB_DETAIL.KD_JENIS_TENAGA = dbo.HRD_R_MUTASI.KD_JENIS_TENAGA AND dbo.HRD_JENIS_TENAGA_SUB_DETAIL.KD_DETAIL = dbo.HRD_R_MUTASI.KD_DETAIL AND dbo.HRD_JENIS_TENAGA_SUB_DETAIL.KD_SUB_DETAIL = dbo.HRD_R_MUTASI.KD_SUB_DETAIL
        $getRincian = DB::table('hrd_tugas_tambahan as tugas')
            ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
            ->leftJoin('hrd_jabatan_struktural as jabatan', 'tugas.kd_jab_struk', '=', 'jabatan.kd_jab_struk')
            // ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', 'tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail')
            ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', function($join) {
                $join->on('tugas.kd_jenis_tenaga', '=', 'sub_detail.kd_jenis_tenaga')
                    ->on('tugas.kd_detail', '=', 'sub_detail.kd_detail')
                    ->on('tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail');
            })
            ->leftJoin('hrd_ruangan as ruangan', 'tugas.kd_ruangan', '=', 'ruangan.kd_ruangan')
            ->select('tugas.*', 'karyawan.*', 'jabatan.jab_struk as jab_struk_tambahan', 'sub_detail.sub_detail as sub_detail_tambahan', 'ruangan.ruangan as ruangan_tambahan')
            ->where('tugas.kd_karyawan', $kd_karyawan)
            ->where('tugas.kd_tugas_tambahan', $kd_tugas_tambahan)
            ->first();

            // dd([
            //     'getRincian' => $getRincian,
            //     'jab_struk' => $getRincian->jab_struk_tambahan,
            //     'sub_detail' => $getRincian->sub_detail_tambahan,
            //     'ruangan' => $getRincian->ruangan_tambahan
            // ]);

        return view('tugas-tambahan.rincian', compact('getRincian'));
    }

    private function printNota($kd_karyawan, $kd_tugas_tambahan, $passphrase)
    {
        $logo = public_path('assets/media/rsud-langsa/logo-putih.png');
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');
        $logoEsign = public_path('assets/media/rsud-langsa/e-sign.png');

        // ambil data verifikasi seperti data rincian
        // $getVerifikasi = DB::table('hrd_tugas_tambahan as tugas')
        //     ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
        //     ->select('tugas.*', 'karyawan.*')
        //     ->where('tugas.kd_karyawan', $kd_karyawan)
        //     ->where('tugas.kd_tugas_tambahan', $kd_tugas_tambahan)
        //     ->first();
        $getVerifikasi = DB::table('hrd_tugas_tambahan as tugas')
            ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
            ->leftJoin('hrd_jabatan_struktural as jabatan', 'tugas.kd_jab_struk', '=', 'jabatan.kd_jab_struk')
            // ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', 'tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail')
            ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', function($join) {
                $join->on('tugas.kd_jenis_tenaga', '=', 'sub_detail.kd_jenis_tenaga')
                    ->on('tugas.kd_detail', '=', 'sub_detail.kd_detail')
                    ->on('tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail');
            })
            ->leftJoin('hrd_ruangan as ruangan', 'tugas.kd_ruangan', '=', 'ruangan.kd_ruangan')
            ->select('tugas.*', 'karyawan.*', 'jabatan.jab_struk as jab_struk_tambahan', 'sub_detail.sub_detail as sub_detail_tambahan', 'ruangan.ruangan as ruangan_tambahan')
            ->where('tugas.kd_karyawan', $kd_karyawan)
            ->where('tugas.kd_tugas_tambahan', $kd_tugas_tambahan)
            ->first();

            // dd($getVerifikasi);

        // ambil data direktur
        $getDirektur = DB::table('view_tampil_karyawan as karyawan')
            ->join('hrd_golongan as golongan', 'karyawan.kd_gol_sekarang', '=', 'golongan.kd_gol')
            ->select('karyawan.*', 'golongan.alias_gol as golongan')
            ->where('karyawan.kd_jabatan_struktural', 1)
            ->where('karyawan.status_peg', 1)
            ->first()
        ;

        // dd($getDirektur);

        $year = date('Y');

        // buat folder di storage/app/public/nota-tugas-tambahan/2024/kd_tugas_tambahan-kd_karyawan.pdf
        $pdfFilePath = 'public/nota-tugas-tambahan/' . $year . '/' . $kd_tugas_tambahan . '-' . $kd_karyawan . '.pdf';

        // jika folder storage/app/public/nota-tugas-tambahan/2024 belum ada, maka buat folder tersebut
        if (!Storage::exists('public/nota-tugas-tambahan/' . $year)) {
            Storage::makeDirectory('public/nota-tugas-tambahan/' . $year);
        }

        // generate QR Code
        $PNG_WEB_DIR = storage_path('app/public/qr-code-nota-tugas-tambahan/' . $year . '/');
        if (!Storage::exists('public/qr-code-nota-tugas-tambahan/' . $year)) {
            Storage::makeDirectory('public/qr-code-nota-tugas-tambahan/' . $year);
        }

        $imgName = "{$kd_tugas_tambahan}-{$kd_karyawan}.png";
        $link = "https://e-rsudlangsa.id/hrd/cek-nota-tugas-tambahan.php?kd_tugas_tambahan={$kd_tugas_tambahan}&kd_karyawan={$kd_karyawan}";

        $this->generateQrCode($link, $PNG_WEB_DIR . $imgName, $logo);

        $data = [
            'getVerifikasi' => $getVerifikasi,
            'getDirektur' => $getDirektur,
            'logo' => $logo,
            'logoLangsa' => $logoLangsa,
            'logoEsign' => $logoEsign,
        ];
        
        $pdf = \PDF::loadView('tugas-tambahan.nota-tugas-tambahan', $data, [], [
            'format' => [215, 330],
            'orientation' => 'P',
            'margin_top' => 10,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 25,
            'margin_footer' => 5,
            'default_font_size' => 11,
            'default_font' => 'bookman-old-style',
            // custom font berada di public/assets/fonts/
            'custom_font_dir' => base_path('public/assets/fonts/'),
            // 'custom_font_dir' => public_path('assets/fonts/'),
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
            $response = $this->sendPdfForSignatures($pdfFilePath, $passphrase, $kd_tugas_tambahan, $kd_karyawan);

            return response()->json($response);
        } catch (\Exception $th) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function printDraftNota($kd_karyawan, $kd_tugas_tambahan)
    {
        $logo = public_path('assets/media/rsud-langsa/logo-putih.png');
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');
        $logoEsign = public_path('assets/media/rsud-langsa/e-sign.png');

        // ambil data verifikasi seperti data rincian
        // $getVerifikasi = DB::table('hrd_tugas_tambahan as tugas')
        //     ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
        //     ->select('tugas.*', 'karyawan.*')
        //     ->where('tugas.kd_karyawan', $kd_karyawan)
        //     ->where('tugas.kd_tugas_tambahan', $kd_tugas_tambahan)
        //     ->first();

        $getVerifikasi = DB::table('hrd_tugas_tambahan as tugas')
            ->join('view_tampil_karyawan as karyawan', 'tugas.kd_karyawan', '=', 'karyawan.kd_karyawan')
            ->leftJoin('hrd_jabatan_struktural as jabatan', 'tugas.kd_jab_struk', '=', 'jabatan.kd_jab_struk')
            // ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', 'tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail')
            ->leftJoin('hrd_jenis_tenaga_sub_detail as sub_detail', function($join) {
                $join->on('tugas.kd_jenis_tenaga', '=', 'sub_detail.kd_jenis_tenaga')
                    ->on('tugas.kd_detail', '=', 'sub_detail.kd_detail')
                    ->on('tugas.kd_sub_detail', '=', 'sub_detail.kd_sub_detail');
            })
            ->leftJoin('hrd_ruangan as ruangan', 'tugas.kd_ruangan', '=', 'ruangan.kd_ruangan')
            ->select('tugas.*', 'karyawan.*', 'jabatan.jab_struk as jab_struk_tambahan', 'sub_detail.sub_detail as sub_detail_tambahan', 'ruangan.ruangan as ruangan_tambahan')
            ->where('tugas.kd_karyawan', $kd_karyawan)
            ->where('tugas.kd_tugas_tambahan', $kd_tugas_tambahan)
            ->first();

            // dd($getVerifikasi);

        // ambil data direktur
        $getDirektur = DB::table('view_tampil_karyawan as karyawan')
            ->join('hrd_golongan as golongan', 'karyawan.kd_gol_sekarang', '=', 'golongan.kd_gol')
            ->select('karyawan.*', 'golongan.alias_gol as golongan')
            ->where('karyawan.kd_jabatan_struktural', 1)
            ->where('karyawan.status_peg', 1)
            ->first()
        ;

        // dd($getDirektur);

        $year = date('Y');

        // buat folder di storage/app/public/nota-tugas-tambahan/2024/kd_tugas_tambahan-kd_karyawan.pdf
        $pdfFilePath = 'public/nota-tugas-tambahan/' . $year . '/' . $kd_tugas_tambahan . '-' . $kd_karyawan . '.pdf';

        // jika folder storage/app/public/nota-tugas-tambahan/2024 belum ada, maka buat folder tersebut
        if (!Storage::exists('public/nota-tugas-tambahan/' . $year)) {
            Storage::makeDirectory('public/nota-tugas-tambahan/' . $year);
        }

        // generate QR Code
        $PNG_WEB_DIR = storage_path('app/public/qr-code-nota-tugas-tambahan/' . $year . '/');
        if (!Storage::exists('public/qr-code-nota-tugas-tambahan/' . $year)) {
            Storage::makeDirectory('public/qr-code-nota-tugas-tambahan/' . $year);
        }

        $imgName = "{$kd_tugas_tambahan}-{$kd_karyawan}.png";
        $link = "https://e-rsudlangsa.id/hrd/cek-nota-tugas-tambahan.php?kd_tugas_tambahan={$kd_tugas_tambahan}&kd_karyawan={$kd_karyawan}";

        $this->generateQrCode($link, $PNG_WEB_DIR . $imgName, $logo);

        $data = [
            'getVerifikasi' => $getVerifikasi,
            'getDirektur' => $getDirektur,
            'logo' => $logo,
            'logoLangsa' => $logoLangsa,
            'logoEsign' => $logoEsign,
        ];

        $pdf = \PDF::loadView('tugas-tambahan.nota-tugas-tambahan', $data, [], [
            'format' => [215, 330],
            'orientation' => 'P',
            'margin_top' => 10,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 25,
            'margin_footer' => 5,
            'show_watermark' => true,
            'watermark' => 'DRAFT',
            'watermark_font' => 'Arial',
            'watermark_alpha' => 0.1,
            'default_font_size' => 11,
            'default_font' => 'bookman-old-style',
            // custom font berada di public/assets/fonts/
            'custom_font_dir' => base_path('public/assets/fonts/'),
            // 'custom_font_dir' => public_path('assets/fonts/'),
            'custom_font_data' => [
                'bookman-old-style' => [
                    'R' => 'Bookman Old Style Regular.ttf',
                    'B' => 'Bookman Old Style Bold.ttf',
                    'I' => 'Bookman Old Style Italic.ttf',
                    'BI' => 'Bookman Old Style Bold Italic.ttf'
                ]
            ]
        ]);

        return $pdf->download('Nota Tugas Tambahan - ' . $kd_tugas_tambahan . '-' . $kd_karyawan . '.pdf');
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
                // save id_dokumen to table hrd_r_mutasi
                DB::table('hrd_tugas_tambahan')
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

        $filename = 'Nota Tugas Tambahan-' . $id_dokumen . '.pdf';
        $year = date('Y');
        // $directory = 'public/mutasi-nota-tte/' . $year;
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
        DB::table('hrd_tugas_tambahan')
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

    private function getNotaNumber($kd_tugas_tambahan)
    {
        $prefix = 'NTT';
        $year = date('Y');

        $fetchNotaNumber = DB::table('hrd_tugas_tambahan')
            ->select(DB::raw('SUBSTRING(no_nota, 7, 3) as nomor'))
            ->whereRaw('RIGHT(no_nota, 4) = ?', [$year])
            ->orderBy(DB::raw('SUBSTRING(no_nota, 7, 3)'), 'desc')
            ->first()
        ;

        $currentNumber = $fetchNotaNumber ? (int)$fetchNotaNumber->nomor + 1 : 1;

        $notaNumber = sprintf('%03d', $currentNumber);

        $no_nota = "875.1/{$notaNumber}/{$prefix}/{$year}";

        // update no_nota to hrd_tugas_tambahan
        // DB::table('hrd_tugas_tambahan')
        //     ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
        //     ->update([
        //         'no_nota' => $no_nota
        //     ]);

        return $no_nota;
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

    public function getKaryawan(Request $request)
    {
        $search = $request->search;
        $query = DB::table('hrd_karyawan')
            ->select('kd_karyawan', 'gelar_depan', 'nama', 'gelar_belakang')
            ->where('status_peg', '1')
            ->orderBy('kd_karyawan')
        ;

        if ($search) {
            // $query->where('nama', 'LIKE', "%{$search}%")
            //     ->orWhere('kd_karyawan', 'LIKE', "%{$search}%");
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kd_karyawan', 'like', "%{$search}%")
                ;
            });
        } else {
            $query->limit(10);
        }

        $karyawan = $query->get();

        $results = [];

        foreach ($karyawan as $k) {
            $fullName = trim("{$k->gelar_depan} {$k->nama}{$k->gelar_belakang}");
            $results[] = [
                'id' => $k->kd_karyawan,
                'text' => "{$k->kd_karyawan} - {$fullName}"
            ];
        }

        return response()->json([
            'results' => $results
        ]);
    }

    // batal finalisasi, maka kosongkan field VERIF_4, KD_KARYAWAN_VERIF_4, WAKTU_VERIF_4
    public function batalFinalisasi(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_tugas_tambahan = $request->kd_tugas_tambahan;

        $update = DB::table('hrd_tugas_tambahan')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tugas_tambahan', $kd_tugas_tambahan)
            ->update([
                'verif_4' => null,
                'kd_karyawan_verif_4' => null,
                'waktu_verif_4' => null,
                'no_nota' => null,
                'tgl_ttd' => null,
                'id_dokumen' => null
            ]);

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Batal finalisasi berhasil'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Batal finalisasi gagal'
            ], 500);
        }
    }
}
