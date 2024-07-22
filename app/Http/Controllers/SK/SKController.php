<?php

namespace App\Http\Controllers\SK;

use Mpdf\Mpdf;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\ErrorCorrectionLevel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
// use Barryvdh\DomPDF\PDF;
// use \Mpdf\Mpdf;

class SKController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index(Request $request)
    {
        if ($request->tahun_sk) {
            $year = $request->tahun_sk;
        } else {
            // $year = 2021;
            $year = 2024;
        }

        $skKontrak = DB::table('hrd_sk_pegawai_kontrak')
            ->select(
                'urut',
                DB::raw('count(kd_karyawan) as jumlah_pegawai'),
                'nomor_konsederan',
                'tahun_sk',
                'tgl_sk',
                'stt',
                'no_sk',
                'verif_1',
                'verif_2',
                'verif_3',
                'verif_4',
                'tgl_ttd',
                'path_dokumen'
            )
            ->where('tahun_sk', $year)
            ->groupBy(
                'urut',
                'nomor_konsederan',
                'tahun_sk',
                'tgl_sk',
                'stt',
                'no_sk',
                'verif_1',
                'verif_2',
                'verif_3',
                'verif_4',
                'tgl_ttd',
                'path_dokumen'
            )
            ->orderBy('urut', 'desc')
        ->get();

        return view('sk.index', [
            'skKontrak' => $skKontrak,
            'year' => $year
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'tujuan' => 'required',
        ], [
            'tujuan.required' => 'Tujuan SK harus dipilih.'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            if ($request->tujuan == 'single') {
                $max = DB::table('hrd_sk_pegawai_kontrak')
                    ->selectRaw('case when max(urut) is null then 1 else max(urut)+1 end as urut, max(no_per_kerja) as no_per_kerja')
                    ->first()
                ;
    
                $urut = $max ? $max->urut : 1;
                $no_perjanjian = $max ? $max->no_per_kerja + 1 : 1;

                $no_per_kerja = sprintf('%02s', $no_perjanjian);

                $date = Carbon::create(date('Y'), 1, 1);

                $getTgl = DB::table('hrd_karyawan as k')
                    ->select('ht.tgl_masuk', 'k.tgl_keluar_pensiun')
                    ->leftJoin('hrd_tempat_kerja as ht', 'k.kd_karyawan', '=', 'ht.kd_karyawan')
                    ->where('k.kd_karyawan', $request->karyawan)
                    ->orderBy('no_urut', 'desc')
                    ->first()
                ;

                if ($getTgl) {
                    $tgl_masuk = $getTgl->tgl_masuk ? Carbon::parse($getTgl->tgl_masuk)->format('Y-m-d') : '';
                    $tgl_keluar_pensiun = Carbon::parse($getTgl->tgl_keluar_pensiun)->format('Y-m-d');

                    if ($tgl_masuk == '') {
                        $tgl_masuk = $tgl_keluar_pensiun;
                    } else {
                        if (Carbon::parse($tgl_keluar_pensiun)->lte($date)) {
                            $tgl_masuk = $date->format('Y-m-d');
                        } else {
                            $tgl_masuk = Carbon::parse($tgl_masuk)->format('Y-m-d');
                        }
                    }
                }

                // dd($tgl_masuk, $tgl_keluar_pensiun);

                $maxKdIndex = DB::table('hrd_sk_pegawai_kontrak')
                    ->max('kd_index')
                ;
                $newKdIndex = $maxKdIndex ? $maxKdIndex + 1 : 1;

                $karyawan = DB::table('hrd_karyawan')
                    ->select('kd_karyawan')
                    ->where('kd_karyawan', $request->karyawan)
                    ->where('status_peg', 1)
                    ->first()
                ;

                $insert = DB::table('hrd_sk_pegawai_kontrak')
                    ->insert([
                        'kd_index' => $newKdIndex,
                        'kd_karyawan' => $karyawan->kd_karyawan,
                        'urut' => $urut,
                        'tahun_sk' => date('Y'),
                        'tgl_sk' => $tgl_masuk,
                        'stt' => 1,
                        'nomor_konsederan' => '',
                        'no_sk' => '',
                        'verif_1' => 0,
                        'kd_karyawan_verif_1' => null,
                        'verif_2' => 0,
                        'kd_karyawan_verif_2' => null,
                        'verif_3' => 0,
                        'kd_karyawan_verif_3' => null,
                        'verif_4' => 0,
                        'tgl_ttd' => null,
                        'kd_karyawan_verif_4' => null,
                        'no_per_kerja' => $no_per_kerja
                    ])
                ;

                if ($insert) {
                    return response()->json([
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Data SK berhasil disimpan.'
                    ]);
                } else {
                    return response()->json([
                        'code' => 500,
                        'status' => 'error',
                        'message' => 'Data SK gagal disimpan.'
                    ], 500);
                }
            } else if ($request->tujuan == 'all') {
                // $get_max = sqlsrv_query($konek, "select case when max(URUT) is null then 1 else max(URUT)+1 end as URUT, max(NO_PER_KERJA) as NO_PER_KERJA from HRD_SK_PEGAWAI_KONTRAK where TAHUN_SK = '".date('Y')."'");
                // $max = sqlsrv_fetch_array($get_max);
                // $no_perjanjian = $max['NO_PER_KERJA']+1;
                // $get_karyawan = sqlsrv_query($konek, "select KD_KARYAWAN, TGL_KELUAR_PENSIUN from HRD_KARYAWAN where KD_STATUS_KERJA = 3 and STATUS_PEG = 1 order by KD_KARYAWAN");

                $max = DB::table('hrd_sk_pegawai_kontrak')
                    ->selectRaw('case when max(urut) is null then 1 else max(urut)+1 end as urut, max(no_per_kerja) as no_per_kerja')
                    ->first()
                ;

                $urut = $max ? $max->urut : 1;
                $no_perjanjian = $max ? $max->no_per_kerja + 1 : 1;
                
                $no_per_kerja = sprintf('%02s', $no_perjanjian);

                $karyawan = DB::table('hrd_karyawan')
                    ->select('kd_karyawan', 'tgl_keluar_pensiun')
                    ->where('kd_status_kerja', 3)
                    ->where('status_peg', 1)
                    ->orderBy('kd_karyawan')
                    ->get()
                ;

                $date = Carbon::create(date('Y'), 1, 1);
                foreach ($karyawan as $k) {
                    $tgl_keluar_pensiun = Carbon::parse($k->tgl_keluar_pensiun)->format('Y-m-d');

                    if (Carbon::parse($tgl_keluar_pensiun)->lte($date)) {
                        $tgl = $date->format('Y-m-d');
                    } else {
                        $tgl = $tgl_keluar_pensiun;
                    }

                    $maxKdIndex = DB::table('hrd_sk_pegawai_kontrak')
                        ->max('kd_index')
                    ;
                    $newKdIndex = $maxKdIndex ? $maxKdIndex + 1 : 1;

                    $insert = DB::table('hrd_sk_pegawai_kontrak')
                        ->insert([
                            'kd_index' => $newKdIndex,
                            'kd_karyawan' => $k->kd_karyawan,
                            'urut' => $urut,
                            'tahun_sk' => date('Y'),
                            'tgl_sk' => $tgl,
                            'stt' => 1,
                            'nomor_konsederan' => $request->konsederan,
                            'no_sk' => $request->konsederan,
                            'verif_1' => 0,
                            'kd_karyawan_verif_1' => null,
                            'verif_2' => 0,
                            'kd_karyawan_verif_2' => null,
                            'verif_3' => 0,
                            'kd_karyawan_verif_3' => null,
                            'verif_4' => 0,
                            'tgl_ttd' => null,
                            'kd_karyawan_verif_4' => null,
                            'no_per_kerja' => $no_per_kerja
                        ])
                    ;
                }

                if ($insert) {
                    return response()->json([
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Data SK berhasil disimpan.'
                    ]);
                } else {
                    return response()->json([
                        'code' => 500,
                        'status' => 'error',
                        'message' => 'Data SK gagal disimpan.'
                    ], 500);
                }
            }
        }
    }

    public function firstVerification(Request $request)
    {
        $urut = $request->urut_rincian_verif;
        $tahun = $request->tahun_rincian_verif;
        $kd_karyawan = $request->kd_karyawan;

        // dd($urut, $tahun, $kd_karyawan);

        $update = DB::table('hrd_sk_pegawai_kontrak')
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->whereIn('kd_karyawan', $kd_karyawan)
            ->update([
                'verif_1' => 1,
                'kd_karyawan_verif_1' => auth()->user()->kd_karyawan
            ])
        ;

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data SK berhasil diverifikasi oleh Kasubbag. Kepegawaian.'
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Data SK gagal diverifikasi.'
            ], 500);
        }
    }

    public function secondVerification(Request $request)
    {
        $urut = $request->urut_rincian_verif;
        $tahun = $request->tahun_rincian_verif;
        $kd_karyawan = $request->kd_karyawan;

        $update = DB::table('hrd_sk_pegawai_kontrak')
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->whereIn('kd_karyawan', $kd_karyawan)
            ->update([
                'verif_2' => 1,
                'kd_karyawan_verif_2' => auth()->user()->kd_karyawan
            ])
        ;

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data SK berhasil diverifikasi oleh Kabag. TU.'
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Data SK gagal diverifikasi.'
            ], 500);
        }
    }

    public function thirdVerification(Request $request)
    {
        $urut = $request->urut_rincian_verif;
        $tahun = $request->tahun_rincian_verif;
        $kd_karyawan = $request->kd_karyawan;

        $update = DB::table('hrd_sk_pegawai_kontrak')
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->whereIn('kd_karyawan', $kd_karyawan)
            ->update([
                'verif_3' => 1,
                'kd_karyawan_verif_3' => auth()->user()->kd_karyawan
            ])
        ;

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data SK berhasil diverifikasi oleh Wadir. ADM dan Umum.'
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Data SK gagal diverifikasi.'
            ], 500);
        }
    }

    public function fourthVerification(Request $request)
    {
        // dd($request->all());
        $urut = $request->urut_rincian_verif;
        $tahun = $request->tahun_rincian_verif;
        $kd_karyawan = $request->kd_karyawan;

        $update = DB::table('hrd_sk_pegawai_kontrak')
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->whereIn('kd_karyawan', $kd_karyawan)
            ->update([
                'verif_4' => 1,
                'kd_karyawan_verif_4' => auth()->user()->kd_karyawan
            ])
        ;

        if ($update) {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data SK berhasil diverifikasi oleh Direktur.'
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Data SK gagal diverifikasi.'
            ], 500);
        }
    }

    public function finalisasi(Request $request)
    {
        // dd($request->all());
        $urut = $request->urut_rincian_verif;
        $tahun = $request->tahun_rincian_verif;
        $kd_karyawan = $request->kd_karyawan;
        $tgl_ttd = $request->tanggal;
        $passphrase = $request->passphrase;

        // get max no sk from hrd_sk_pegawai_kontrak where year = $tahun
        $getMaxNoSk = DB::table('hrd_sk_pegawai_kontrak')
            ->where('tahun_sk', $tahun)
            ->max('no_sk')
        ;

        try {
            if ($request->no_sk == '01') {
                $update = DB::table('hrd_sk_pegawai_kontrak')
                    ->where('urut', $urut)
                    ->where('tahun_sk', $tahun)
                    ->whereIn('kd_karyawan', $kd_karyawan)
                    ->update([
                        'tgl_ttd' => $tgl_ttd,
                    ])
                ;
            } else {
                $update = DB::table('hrd_sk_pegawai_kontrak')
                    ->where('urut', $urut)
                    ->where('tahun_sk', $tahun)
                    ->whereIn('kd_karyawan', $kd_karyawan)
                    ->update([
                        'tgl_ttd' => $tgl_ttd,
                        'no_sk' => sprintf('%02s', $getMaxNoSk + 1)
                    ])
                ;
            }
    
            if ($update) {
                // call printSk function
                $printSkResponse = $this->printSk($urut, $tahun, $kd_karyawan[0], $passphrase);
                $printSkData = json_decode($printSkResponse->getContent(), true);
                
                if ($printSkData['original']['code'] == 200) {
                    return response()->json([
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Data SK berhasil ditanadatangani secara digital.',
                    ]);
                } else {
                    $this->failedVerification($kd_karyawan, $urut, $tahun, $printSkData['original']['message']);

                    return response()->json([
                        'code' => 500,
                        'status' => 'error',
                        'message' => 'Data SK gagal ditanda tangani secara digital.' . $printSkData['original']['message'],
                    ], 500);
                }

            } else {
                $this->failedVerification($kd_karyawan, $urut, $tahun, 'Data SK gagal ditanadatangani secara digital.');

                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Data SK gagal ditanadatangani secara digital.'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->serverError($kd_karyawan, $urut, $tahun);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan. ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function printSk($urut, $tahun, $kd_karyawan, $passphrase)
    {
        // dd($urut, $tahun, $kd_karyawan, $passphrase);
        $logo = public_path('assets/media/rsud-langsa/logo-putih.png');
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');
        $logoEsign = public_path('assets/media/rsud-langsa/e-sign.png');
        // $passphrase = $request->passphrase;

        $getSk = DB::table('hrd_sk_pegawai_kontrak as sk')
            ->join('view_tampil_karyawan as vk', 'sk.kd_karyawan', '=', 'vk.kd_karyawan')
            ->select(
                'vk.kd_karyawan', 'vk.gelar_depan', 'vk.nama', 'vk.gelar_belakang', 'vk.tempat_lahir', 'vk.tgl_lahir', 'vk.jenis_kelamin', 'vk.jenjang_didik', 'vk.jurusan', 'sk.tahun_sk', 'sk.tgl_sk', 'sk.no_sk', 'sk.tgl_ttd'
            )
            ->where('sk.urut', $urut)
            ->where('sk.tahun_sk', $tahun)
            ->orderBy('vk.ruangan', 'desc')
            ->orderBy('vk.kd_karyawan', 'asc')
        ->get();

        $getDirektur = DB::table('view_tampil_karyawan as vk')
            ->join('hrd_golongan as g', 'vk.kd_gol_sekarang', '=', 'g.kd_gol')
            ->select('vk.*', 'g.alias_gol as golongan')
            ->where('vk.kd_jabatan_struktural', 1)
            ->where('vk.status_peg', 1)
            ->first()
        ;

        $totalRow = DB::table('hrd_sk_pegawai_kontrak')
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->count()
        ;

        $pdfFilePath = $totalRow > 1
            ? 'public/sk/SK Pegawai Kontrak-' . $tahun . '.pdf'
            : 'public/sk/SK Pegawai Kontrak-' . $tahun . '-' . $kd_karyawan . '.pdf'
        ;

        if (!Storage::exists('public/sk')) {
            Storage::makeDirectory('public/sk');
        }

        foreach ($getSk as $result) :
            $PNG_WEB_DIR = storage_path('app/public/qr-code/');
            $imgName = "{$result->no_sk}-{$result->tahun_sk}-{$result->kd_karyawan}.png";
            $link = "https://e-rsud.langsakota.go.id/hrd/cek-data.php?data=" . md5($result->kd_karyawan) . "&thn={$result->tahun_sk}";
            $this->generateQrCode($link, $PNG_WEB_DIR . $imgName, $logo);
        endforeach;

        $data = [
            'results' => $getSk,
            'direktur' => $getDirektur,
            'tahun' => $tahun,
            'logo' => $logo,
            'logoLangsa' => $logoLangsa,
            'logoEsign' => $logoEsign,
        ];

        Log::info('Data untuk PDF', ['data' => $data]);
        
        
        $pdf = \PDF::loadView('sk.sk-pegawai-kontrak', $data, [], [
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
                    // 'B' => 'Bookman Old Style Bold.ttf',
                    'I' => 'Bookman Old Style Italic.ttf',
                    // 'BI' => 'Bookman Old Style Bold Italic.ttf'
                ]
            ]
        ]);

        Storage::put($pdfFilePath, $pdf->output());

        // do e-signature
        try {
            $response = $this->sendPdfForSignatures($pdfFilePath, $passphrase, $urut, $tahun, $kd_karyawan);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error in printSk method:', ['exception' => $e]);
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat proses TTE: ' . $e->getMessage(),
                'module' => 'printSk',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function printPerjanjianKerja($urut, $tahun)
    {
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');

        $results = DB::table('hrd_sk_pegawai_kontrak as sk')
            ->join('view_tampil_karyawan as k', 'sk.kd_karyawan', '=', 'k.kd_karyawan')
            ->select(
                'k.kd_karyawan', 
                'k.gelar_depan', 
                'k.nama', 
                'k.gelar_belakang', 
                'k.tempat_lahir', 
                'k.tgl_lahir', 
                'k.jenis_kelamin', 
                'k.jenjang_didik', 
                'k.jurusan', 
                'sk.no_sk', 
                'sk.tgl_sk', 
                'sk.tahun_sk', 
                'sk.tgl_ttd', 
                'sk.no_per_kerja', 
                DB::raw("datename(dw, sk.tgl_sk) as hari_ttd"), 
                'k.no_ktp', 
                'k.sub_detail'
            )
            ->where('sk.urut', $urut)
            ->where('sk.tahun_sk', $tahun)
            ->orderBy('k.ruangan', 'desc')
            ->orderBy('k.kd_karyawan', 'asc')
            ->first();

        $direktur = DB::table('view_tampil_karyawan as vk')
            ->join('hrd_golongan as g', 'vk.kd_gol_sekarang', '=', 'g.kd_gol')
            ->select('vk.*', 'g.alias_gol as golongan')
            ->where('vk.kd_jabatan_struktural', 1)
            ->where('vk.status_peg', 1)
            ->first();

        // im using this package composer require mpdf/mpdf
        $pdf = new MPDF([
            'mode' => 'utf-8',
            'format' => [215, 330],
            'orientation' => 'P',
            'default_font_size' => 11,
            'default_font' => 'bookman-old-style',
            'fontDir' => public_path('assets/fonts/'),
            'fontdata' => [
                'bookman-old-style' => [
                    'R' => 'Bookman Old Style Regular.ttf',
                    'B' => 'Bookman Old Style Bold.ttf',
                    'I' => 'Bookman Old Style Italic.ttf',
                    'BI' => 'Bookman Old Style Bold Italic.ttf'
                ]
            ]
        ]);

        $pdf->addPage('P', '', '', '', '', 15, 15, 5, 15, 5, 5);

        $html = view('sk.perjanjian-kerja-page-1', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();
        // footer from view
        $pdf->SetHTMLFooter(view('sk.footer-perjanjian-kerja-page-1', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render());

        $pdf->WriteHTML($html);

        // tambah halaman baru page-2
        $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);

        $html = view('sk.perjanjian-kerja-page-2', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();
        $pdf->WriteHTML($html);

        // tambah halaman baru page-3
        $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);
        
        $html = view('sk.perjanjian-kerja-page-3', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();
        $pdf->WriteHTML($html);

        // tambah halaman baru page-4
        $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);

        $html = view('sk.perjanjian-kerja-page-4', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();
        $pdf->WriteHTML($html);

        $pdf->Output('Perjanjian-Kerja-' . $results->kd_karyawan . '-' . $results->tahun_sk . '.pdf', 'I');
    }

    public function old_dompdf_printPerjanjianKerja($urut, $tahun)
    {
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');

        $results = DB::table('hrd_sk_pegawai_kontrak as sk')
            ->join('view_tampil_karyawan as k', 'sk.kd_karyawan', '=', 'k.kd_karyawan')
            ->select(
                'k.kd_karyawan', 
                'k.gelar_depan', 
                'k.nama', 
                'k.gelar_belakang', 
                'k.tempat_lahir', 
                'k.tgl_lahir', 
                'k.jenis_kelamin', 
                'k.jenjang_didik', 
                'k.jurusan', 
                'sk.no_sk', 
                'sk.tgl_sk', 
                'sk.tahun_sk', 
                'sk.tgl_ttd', 
                'sk.no_per_kerja', 
                DB::raw("datename(dw, sk.tgl_sk) as hari_ttd"), 
                'k.no_ktp', 
                'k.sub_detail'
            )
            ->where('sk.urut', $urut)
            ->where('sk.tahun_sk', $tahun)
            ->orderBy('k.ruangan', 'desc')
            ->orderBy('k.kd_karyawan', 'asc')
            ->first();

        $direktur = DB::table('view_tampil_karyawan as vk')
            ->join('hrd_golongan as g', 'vk.kd_gol_sekarang', '=', 'g.kd_gol')
            ->select('vk.*', 'g.alias_gol as golongan')
            ->where('vk.kd_jabatan_struktural', 1)
            ->where('vk.status_peg', 1)
            ->first();

        // $data = [
        //     'results' => $results,
        //     'direktur' => $getDirektur,
        //     'tahun' => $tahun,
        //     'logoLangsa' => $logoLangsa,
        // ];

        $html = view('sk.perjanjian-kerja', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();

        // 21.5 x 33 cm
        $customPaperSize = [
            0, 0, 215*2.83, 330*2.83
        ];

        // $pdf = Pdf::loadView('sk.perjanjian-kerja', compact('results', 'direktur', 'tahun', 'logoLangsa'))->setPaper($customPaperSize, 'portrait');

        // use App Container
        $pdf = App::make('dompdf.wrapper');
        $perjanjianPdf = $pdf->setOption([
            'dpi' => 150,
        ]);

        // set paper size
        $perjanjianPdf = $pdf->loadHTML($html)->setPaper($customPaperSize, 'portrait');

        return $pdf->stream('Perjanjian-Kerja-' . $results->kd_karyawan . '-' . $results->tahun_sk . '.pdf');
        

        // return $pdf->stream('Perjanjian-Kerja-' . $results->kd_karyawan . '-' . $results->tahun_sk . '.pdf');
    }

    // public function surat_sakit($id)
    // {
    //     $data = SuratKeterangan::where('jenis_surat', 'surat_sakit')->where('kode', $id)->with('dokter', 'pasien')->first();

    //     if (!$data){
    //         return response()->json('Surat Keterangan tidak ditemukan');
    //     }

    //     $user = User::where('dokter_id', $data->dokter_id)->with('profil')->first();
    //     $file_ttd = $user->profil?->file_ttd;
    //     $terbilang = $this->terbilang($data->surat_jumlah_hari);
    //     $website = Website::first();
    //     $klinik = Klinik::where('id', $user->klinik_id)->with('kota')->first();
    //     $kecamatan = $klinik->kota?->nama_kota;
    //     $kecamatan = str_replace('KABUPATEN ', '', $kecamatan);
    //     $kecamatan = str_replace('KOTA ', '', $kecamatan);
    //     $hari_tanggal = Carbon::parse($data->tanggal_pemeriksaan)->isoFormat('dddd, D MMMM Y');
    //     $tanggal_surat = Carbon::parse($data->tanggal_surat)->isoFormat('D MMMM Y');
    //     $tanggal_mulai = Carbon::parse($data->tanggal_mulai)->isoFormat('D MMMM Y');
    //     $tanggal_berakhir = Carbon::parse($data->tanggal_berakhir)->isoFormat('D MMMM Y');

    //     if ($data){
    //         if ($klinik->id === 4){
    //             // $qrcode = $user->profil?->qrcode;
    //             $file_ttd = $user->profil?->file_foto_ttd;
    //             $html = view('admin.pdf.surat_sakit_mopoli',[
    //                 'data' => $data,
    //                 'terbilang' => $terbilang,
    //                 'klinik' => $klinik,
    //                 'kecamatan_klinik' => $kecamatan,
    //                 'file_ttd' => $file_ttd,
    //                 'hari_tanggal' => $hari_tanggal,
    //                 'tanggal_surat' => $tanggal_surat,
    //                 'tanggal_mulai' => $tanggal_mulai,
    //                 'tanggal_berakhir' => $tanggal_berakhir,
    //                 // 'qrcode' => $qrcode,
    //                 'file_ttd' => $file_ttd,
    //             ])->render();
    //         } else {
    //             if ($klinik->template_surat == 3){
    //                 $html = view('admin.pdf.surat_sakit_ketiga',[
    //                     'data' => $data,
    //                     'terbilang' => $terbilang,
    //                     'website' => $website,
    //                     'klinik' => $klinik,
    //                     'kecamatan_klinik' => $kecamatan,
    //                     'file_ttd' => $file_ttd,
    //                     'hari_tanggal' => $hari_tanggal,
    //                     'tanggal_surat' => $tanggal_surat,
    //                     'tanggal_mulai' => $tanggal_mulai,
    //                     'tanggal_berakhir' => $tanggal_berakhir,
    //                 ])->render();
    //             } else {
    //                 $html = view('admin.pdf.surat_sakit',[
    //                     'data' => $data,
    //                     'terbilang' => $terbilang,
    //                     'website' => $website,
    //                     'klinik' => $klinik,
    //                     'kecamatan_klinik' => $kecamatan,
    //                     'file_ttd' => $file_ttd,
    //                 ])->render();
    //             }
    //         }

            
    //         $pdf = App::make('dompdf.wrapper');
    //         $invPDF = $pdf->setOption([
    //             'dpi' => 150,
    //             'defaultFont' => 'Arial'
    //         ]);
    //         $invPDF = $pdf->loadHTML($html);
    //         return $pdf->stream('SURAT KETERANGAN SAKIT.pdf');
    //     } else {
    //         return response()->json('Data tidak ditemukan');
    //     }
    // }

    public function old_printPerjanjianKerja($urut, $tahun)
    {
        $logoLangsa = public_path('assets/media/rsud-langsa/Langsa.png');

        // select k.KD_KARYAWAN, k.GELAR_DEPAN, k.NAMA, k.GELAR_BELAKANG, k.TEMPAT_LAHIR, k.TGL_LAHIR, k.JENIS_KELAMIN, k.JENJANG_DIDIK, k.JURUSAN, sk.NO_SK, sk.TGL_SK, sk.TAHUN_SK, sk.TGL_TTD, sk.NO_PER_KERJA, datename(dw,sk.TGL_SK) as HARI_TTD, k.NO_KTP, k.SUB_DETAIL from HRD_SK_PEGAWAI_KONTRAK sk inner join VIEW_TAMPIL_KARYAWAN k on sk.KD_KARYAWAN = k.KD_KARYAWAN where sk.URUT = '172' and sk.TAHUN_SK = '2024' order by k.ruangan desc, k.KD_KARYAWAN asc
        $results = DB::table('hrd_sk_pegawai_kontrak as sk')
            ->join('view_tampil_karyawan as k', 'sk.kd_karyawan', '=', 'k.kd_karyawan')
            ->select(
                'k.kd_karyawan', 
                'k.gelar_depan', 
                'k.nama', 
                'k.gelar_belakang', 
                'k.tempat_lahir', 
                'k.tgl_lahir', 
                'k.jenis_kelamin', 
                'k.jenjang_didik', 
                'k.jurusan', 
                'sk.no_sk', 
                'sk.tgl_sk', 
                'sk.tahun_sk', 
                'sk.tgl_ttd', 
                'sk.no_per_kerja', 
                DB::raw("datename(dw, sk.tgl_sk) as hari_ttd"), 
                'k.no_ktp', 
                'k.sub_detail'
            )
            ->where('sk.urut', $urut)
            ->where('sk.tahun_sk', $tahun)
            ->orderBy('k.ruangan', 'desc')
            ->orderBy('k.kd_karyawan', 'asc')
            ->first();

            // dd($results);

        
        // $getSk = DB::table('hrd_sk_pegawai_kontrak as sk')
        //     ->join('view_tampil_karyawan as vk', 'sk.kd_karyawan', '=', 'vk.kd_karyawan')
        //     ->select(
        //         'vk.kd_karyawan', 'vk.gelar_depan', 'vk.nama', 'vk.gelar_belakang', 'vk.tempat_lahir', 'vk.tgl_lahir', 'vk.jenis_kelamin', 'vk.jenjang_didik', 'vk.jurusan', 'sk.tahun_sk', 'sk.tgl_sk', 'sk.no_sk', 'sk.tgl_ttd', 'sk.no_per_kerja'
        //     )
        //     ->where('sk.urut', $urut)
        //     ->where('sk.tahun_sk', $tahun)
        //     ->orderBy('vk.ruangan', 'desc')
        //     ->orderBy('vk.kd_karyawan', 'asc')
        // ->first();
        // dd($getSk);
        
        $getDirektur = DB::table('view_tampil_karyawan as vk')
            ->join('hrd_golongan as g', 'vk.kd_gol_sekarang', '=', 'g.kd_gol')
            ->select('vk.*', 'g.alias_gol as golongan')
            ->where('vk.kd_jabatan_struktural', 1)
            ->where('vk.status_peg', 1)
            ->first();

        $data = [
            'results' => $results,
            'direktur' => $getDirektur,
            'tahun' => $tahun,
            'logoLangsa' => $logoLangsa,
        ];

        // untuk halaman pertama margin atas 5, margin kanan 15, margin bawah 15, margin kiri 15 dan margin header 5, margin footer 5, kemudian untuk halaman kedua margin atas 15, margin kanan 15, margin bawah 15, margin kiri 15 dan margin header 5, margin footer 5
        $pdf = \PDF::loadView('sk.perjanjian-kerja-page-1', $data, [], [
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

        $pdf->getMpdf()->AddPageByArray([
            'margin_top' => 15,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 15,
            'margin_footer' => 5,
        ]);
        $pdf->getMpdf()->WriteHTML(view('sk.perjanjian-kerja-page-2', $data)->render());

        // page 3
        $pdf->getMpdf()->AddPageByArray([
            'margin_top' => 15,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 15,
            'margin_footer' => 5,
        ]);
        $pdf->getMpdf()->WriteHTML(view('sk.perjanjian-kerja-page-3', $data)->render());

        // page 4
        $pdf->getMpdf()->AddPageByArray([
            'margin_top' => 15,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 15,
            'margin_footer' => 5,
        ]);
        $pdf->getMpdf()->WriteHTML(view('sk.perjanjian-kerja-page-4', $data)->render());


        return $pdf->stream('Perjanjian Kerja-' . $results->kd_karyawan . '-' . $results->no_per_kerja . '-' . $tahun . '.pdf');
    }

    private function sendPdfForSignatures($pdfFilePath, $passphrase, $urut, $tahun, $kd_karyawan)
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
                $this->updateVerification($kd_karyawan, $urut, $tahun, $id_dokumen);

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
            $this->serverError($kd_karyawan, $urut, $tahun);

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

        $filename = 'SK Pegawai Kontrak-' . $id_dokumen . '.pdf';

        $year = date('Y');
        $directory = 'public/sk-tte/' . $year;
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
        DB::table('hrd_sk_pegawai_kontrak')
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
    
    public function getKaryawan(Request $request)
    {
        $search = $request->search;
        $query = DB::table('hrd_karyawan')
            ->select('kd_karyawan', 'gelar_depan', 'nama', 'gelar_belakang')
            ->where('status_peg', '1')
            ->where('kd_status_kerja', '3')
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

    public function rincianKaryawan(Request $request)
    {
        $year = $request->tahun;
        $urut = $request->urut;
        $kd_karyawan = $request->kd_karyawan;

        $results = DB::table('hrd_sk_pegawai_kontrak as sk')
            ->join('view_tampil_karyawan as k', 'sk.kd_karyawan', '=', 'k.kd_karyawan')
            ->select(
                'sk.tahun_sk',
                'sk.tgl_sk',
                'sk.no_sk',
                'k.kd_karyawan',
                'k.gelar_depan',
                'k.nama',
                'k.gelar_belakang',
                'k.tempat_lahir',
                'k.tgl_lahir',
                'k.jenjang_didik',
                'k.tahun_lulus',
                'k.kd_jenis_kelamin',
                'k.jurusan',
                'k.ruangan'
            )
            ->where('sk.tahun_sk', $year)
            ->where('sk.urut', $urut)
            ->groupBy(
                'sk.tahun_sk',
                'sk.tgl_sk',
                'sk.no_sk',
                'k.kd_karyawan',
                'k.gelar_depan',
                'k.nama',
                'k.gelar_belakang',
                'k.tempat_lahir',
                'k.tgl_lahir',
                'k.jenjang_didik',
                'k.tahun_lulus',
                'k.kd_jenis_kelamin',
                'k.jurusan',
                'k.ruangan'
            )
            ->orderBy('k.ruangan', 'desc')
            ->orderBy('k.kd_karyawan', 'asc')
        ->get();

        return view('sk.rincian', [
            'results' => $results
        ]);
    }

    public function verifikasiKaryawan(Request $request)
    {
        return view('sk.verifikasi');
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

    private function failedVerification($kd_karyawan, $urut, $tahun, $error)
    {
        DB::table('hrd_sk_pegawai_kontrak')
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->whereIn('kd_karyawan', $kd_karyawan)
            ->update([
                'verif_4' => 0,
                'kd_karyawan_verif_4' => null,
                'tgl_ttd' => null,
                'no_sk' => null,
            ])
        ;
    }

    private function serverError($kd_karyawan, $urut, $tahun)
    {
        DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->update([
                'verif_4' => 0,
                'kd_karyawan_verif_4' => null,
                'tgl_ttd' => null,
                'no_sk' => null,
            ])
        ;
    }

    private function updateVerification($kd_karyawan, $urut, $tahun, $id)
    {
        DB::table('hrd_sk_pegawai_kontrak')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('urut', $urut)
            ->where('tahun_sk', $tahun)
            ->update([
                'verif_4' => 1,
                'kd_karyawan_verif_4' => auth()->user()->kd_karyawan,
                'id_dokumen' => $id,
            ])
        ;
    }
}
 
