<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
// use Twilio\Rest\Client;
use GuzzleHttp\Client;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Mpdf\Mpdf;

class MutasiOnProcessController extends Controller
{
    public function __construct()
    {
        // timezone asia jakarta
        date_default_timezone_set('Asia/Jakarta');   
    }

    public function old_index()
    {
        // "select KD_MUTASI from HRD_R_MUTASI where KD_TAHAP_MUTASI = 1 group by KD_MUTASI";
        $getMutasi = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 1)
            ->whereIn('kd_jenis_mutasi', [1, 3])
            ->orderBy('kd_mutasi', 'desc')
            // ->groupBy('kd_mutasi')
            ->get();
            // dd($getMutasi);
            // print_r($getMutasi);
            // die;

            // select count(KD_MUTASI) as hitung from HRD_R_MUTASI where KD_TAHAP_MUTASI = 1 and KD_JENIS_MUTASI = 1
        $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 1)
            // ->where('kd_jenis_mutasi', 1)
            ->whereIn('kd_jenis_mutasi', [1, 3])
            ->count();

        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->select('kd_mutasi')
            ->where('kd_tahap_mutasi', 0)
            // ->where('kd_jenis_mutasi', 1)
            ->whereIn('kd_jenis_mutasi', [1, 3])
            ->count();

        return view('mutasi.mutasi-on-process.index', [
            'getMutasi' => $getMutasi,
            'totalMutasiOnProcess' => $totalMutasiOnProcess,
            'totalMutasiPending' => $totalMutasiPending
        ]);
    }

    public function index()
    {
        // Hitung total mutasi untuk ditampilkan di halaman
        $totalMutasiOnProcess = DB::table('hrd_r_mutasi')
            ->where('kd_tahap_mutasi', 1)
            ->whereIn('kd_jenis_mutasi', [1, 3])
            ->count();

        $totalMutasiPending = DB::table('hrd_r_mutasi')
            ->where('kd_tahap_mutasi', 0)
            ->whereIn('kd_jenis_mutasi', [1, 3])
            ->count();

        // Tampilkan view dengan data tambahan
        return view('mutasi.mutasi-on-process.index', [
            'totalMutasiOnProcess' => $totalMutasiOnProcess,
            'totalMutasiPending' => $totalMutasiPending
        ]);
    }

    public function datatable(Request $request)
    {
        // Ambil jabatan dan ruangan dari request
        $jabatan = Auth::user()->karyawan->kd_jabatan_struktural;
        $ruangan = Auth::user()->karyawan->kd_ruangan;
        $searchValue = $request->search['value'];

        // Query dengan join untuk mendapatkan semua data yang diperlukan termasuk id_dokumen dan path_dokumen
        $query = DB::table('view_proses_mutasi as vpm')
            ->leftJoin('hrd_r_mutasi as hrm', function($join) {
                $join->on('vpm.kd_mutasi', '=', 'hrm.kd_mutasi')
                     ->on('vpm.kd_karyawan', '=', 'hrm.kd_karyawan');
            })
            ->select(
                'vpm.*',
                'hrm.id_dokumen',
                'hrm.path_dokumen',
                'hrm.kd_tahap_mutasi as current_tahap_mutasi'
            )
            ->whereIn('vpm.kd_jenis_mutasi', [1, 3]);

        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('vpm.kd_mutasi', 'like', "%{$searchValue}%")
                    ->orWhere('vpm.kd_karyawan', 'like', "%{$searchValue}%")
                    ->orWhere('vpm.nama', 'like', "%{$searchValue}%")
                    ->orWhere('vpm.jab_struk_lama', 'like', "%{$searchValue}%")
                    ->orWhere('vpm.jab_struk_baru', 'like', "%{$searchValue}%")
                    ->orWhere('vpm.ruangan_lama', 'like', "%{$searchValue}%")
                    ->orWhere('vpm.ruangan_baru', 'like', "%{$searchValue}%")
                    ->orWhere('vpm.tempat_lahir', 'like', "%{$searchValue}%");
            });
        }

        // Gunakan Yajra DataTables untuk memproses query
        return DataTables::of($query)
            // Modifikasi data untuk setiap baris
            ->editColumn('jenis_mutasi', function($item) {
                // Format jenis mutasi - menggunakan switch karena PHP 7.4 tidak support match
                switch($item->kd_jenis_mutasi) {
                    case 1:
                        return 'Mutasi (Nota)';
                    case 3:
                        return 'Tugas Tambahan';
                    default:
                        return 'Tidak Diketahui';
                }
            })
            ->editColumn('nama', function($item) {
                // Format nama dengan gelar
                $gelar_depan = $item->gelar_depan ? $item->gelar_depan . ' ' : '';
                $gelar_belakang = $item->gelar_belakang ? '' . $item->gelar_belakang : '';
                $nama = $gelar_depan . $item->nama . $gelar_belakang;

                // Tambahkan informasi tambahan
                $asn = ($item->kd_status_kerja == 1 || $item->kd_status_kerja == 7) 
                    ? "<br>" . $item->nip_baru . "<br>" . $item->no_karpeg 
                    : "";

                return $nama . '<br>' . 
                    $item->tempat_lahir . ', ' . 
                    date('d-m-Y', strtotime($item->tgl_lahir)) . 
                    $asn;
            })
            ->editColumn('status', function($item) {
                // Tentukan status verifikasi
                if ($item->verif_1 == null) {
                    return 'Menunggu verifikasi Kasubbag. Kepeg.';
                } elseif ($item->verif_2 == null) {
                    return 'Menunggu verifikasi Kabag. TU';
                } elseif ($item->verif_3 == null) {
                    return 'Menunggu verifikasi Wadir ADM dan Umum';
                } elseif ($item->verif_4 == null) {
                    return 'Menunggu verifikasi Direktur';
                }
                return 'Selesai';
            })
            ->addColumn('jabatan_lama', function($item) {
                return $item->jab_struk_lama . '<br>' . 
                    $item->sub_detail_lama . '<br>' . 
                    $item->ruangan_lama;
            })
            ->addColumn('jabatan_baru', function($item) {
                return $item->jab_struk_baru . '<br>' . 
                    $item->sub_detail_baru . '<br>' . 
                    $item->ruangan_baru;
            })
            ->addColumn('aksi', function($item) use ($jabatan, $ruangan) {
                $aksi = '';
    
                // Logika tombol verifikasi sesuai dengan kondisi jabatan dan ruangan
                if ($item->verif_1 == null) {
                    if ($jabatan == 19 || $ruangan == 57) {
                        $aksi .= '<a href="javascript:void(0)"
                            class="btn btn-info btn-sm d-block mb-2"
                            title="Verifikasi Ka.Sub.Bag. Kepeg."
                            data-id="' . $item->kd_mutasi . '"
                            data-karyawan="' . $item->kd_karyawan . '"
                            data-jenis-mutasi="' . $item->kd_jenis_mutasi . '"
                            data-url="' . route('admin.mutasi-on-process.first-verification') . '"
                            data-bs-toggle="modal"
                            data-bs-target="#kt_modal_verif"
                            id="verif1">
                            <i class="ki-duotone ki-double-check fs-2"><span class="path1"></span><span class="path2"></span></i> 
                            Verifikasi Ka.Sub.Bag. Kepeg.
                        </a>';
                    }
                } elseif ($item->verif_2 == null) {
                    if ($jabatan == 7 || $ruangan == 57) {
                        $aksi .= '<a href="javascript:void(0)"
                            class="btn btn-primary btn-sm d-block mb-2"
                            title="Verifikasi Kabag. TU"
                            data-id="' . $item->kd_mutasi . '"
                            data-karyawan="' . $item->kd_karyawan . '"
                            data-jenis-mutasi="' . $item->kd_jenis_mutasi . '"
                            data-url="' . route('admin.mutasi-on-process.second-verification') . '"
                            data-bs-toggle="modal"
                            data-bs-target="#kt_modal_verif"
                            id="verif2">
                            <i class="ki-duotone ki-double-check fs-2"><span class="path1"></span><span class="path2"></span></i>
                            Verifikasi Kabag. TU
                        </a>';
                    }
                } elseif ($item->verif_3 == null) {
                    if ($jabatan == 3 || $jabatan == 6 || $ruangan == 57) {
                        $aksi .= '<a href="javascript:void(0)"
                            class="btn btn-warning btn-sm d-block mb-2"
                            title="Menunggu verifikasi Wadir ADM dan Umum"
                            data-id="' . $item->kd_mutasi . '"
                            data-karyawan="' . $item->kd_karyawan . '"
                            data-jenis-mutasi="' . $item->kd_jenis_mutasi . '"
                            data-url="' . route('admin.mutasi-on-process.third-verification') . '"
                            data-bs-toggle="modal"
                            data-bs-target="#kt_modal_verif"
                            id="verif3">
                            <i class="ki-duotone ki-double-check fs-2"><span class="path1"></span><span class="path2"></span></i>
                            Menunggu verifikasi Wadir ADM dan Umum
                        </a>';
                    }
                    if ($jabatan == 1 || $ruangan == 57) {
                        $aksi .= '<a href="javascript:void(0)"
                            class="btn btn-success btn-sm d-block mb-2"
                            title="Menunggu verifikasi Direktur"
                            data-id="' . $item->kd_mutasi . '"
                            data-karyawan="' . $item->kd_karyawan . '"
                            data-jenis-mutasi="' . $item->kd_jenis_mutasi . '"
                            data-url="' . route('admin.mutasi-on-process.fourth-verification') . '"
                            id="verif4">
                            <i class="ki-duotone ki-double-check fs-2"><span class="path1"></span><span class="path2"></span></i>
                            Menunggu verifikasi Direktur
                        </a>';
                    }
                } elseif ($item->verif_4 == null) {
                    if ($jabatan == 1 || $ruangan == 57) {
                        $aksi .= '<a href="javascript:void(0)"
                            class="btn btn-success btn-sm d-block mb-2"
                            title="Menunggu verifikasi Direktur"
                            data-id="' . $item->kd_mutasi . '"
                            data-karyawan="' . $item->kd_karyawan . '"
                            data-jenis-mutasi="' . $item->kd_jenis_mutasi . '"
                            data-url="' . route('admin.mutasi-on-process.fourth-verification') . '"
                            id="verif4">
                            <i class="ki-duotone ki-double-check fs-2"><span class="path1"></span><span class="path2"></span></i>
                            Menunggu verifikasi Direktur
                        </a>';
                    }
                }
    
                // Tambahan tombol edit mutasi nota
                if (($ruangan == 91 || $ruangan == 57) && $item->current_tahap_mutasi == 1) {
                    $aksi .= '<a href="' . route('admin.mutasi.edit-mutasi-nota-on-process', [
                        'id' => $item->kd_mutasi, 
                        'jenis_mutasi' => $item->kd_jenis_mutasi
                    ]) . '" class="btn btn-light-dark btn-sm d-block mb-2">
                        <i class="ki-duotone ki-notepad-edit fs-2"><span class="path1"></span><span class="path2"></span></i>
                        Edit Mutasi Nota
                    </a>';
                }
    
                // Tombol cetak draft nota dan log
                $aksi .= '<a href="' . route('admin.mutasi-on-process.print-draft-sk', [
                    $item->kd_karyawan, 
                    $item->kd_mutasi, 
                    $item->kd_jenis_mutasi
                ]) . '" target="_blank" class="btn btn-primary btn-sm d-block mb-2">
                    <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                    Cetak Draft Nota
                </a>';

                // Tombol download dokumen final jika sudah selesai TTE
                if ($item->current_tahap_mutasi == 2 && !empty($item->id_dokumen) && !empty($item->path_dokumen)) {
                    $aksi .= '<a href="' . route('admin.mutasi-on-process.download-document', $item->id_dokumen) . '" 
                        class="btn btn-success btn-sm d-block mb-2" 
                        title="Download Dokumen Final">
                        <i class="ki-duotone ki-file-down fs-2"><span class="path1"></span><span class="path2"></span></i>
                        Download Dokumen Final
                    </a>';
                }

                $aksi .= '<a href="javascript:void(0)" 
                    class="btn btn-secondary btn-sm d-block mb-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#kt_modal_log" 
                    data-id="' . $item->kd_mutasi . '" 
                    data-karyawan="' . $item->kd_karyawan . '" 
                    id="log">
                    <i class="ki-duotone ki-timer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    Lihat Log
                </a>';
    
                return $aksi;
            })
            ->rawColumns(['nama', 'status', 'jabatan_lama', 'jabatan_baru', 'aksi'])
            ->make(true);
    }

    // Metode bantuan untuk memformat ASN
    private function formatASN($item)
    {
        return ($item->kd_status_kerja == 1 || $item->kd_status_kerja == 7) 
            ? "<br>" . $item->nip_baru . "<br>" . $item->no_karpeg 
            : "";
    }

    // Metode bantuan untuk menentukan status verifikasi
    private function determineVerificationStatus($item)
    {
        if ($item->verif_1 == null) {
            return 'Menunggu verifikasi Kasubbag. Kepeg.';
        } elseif ($item->verif_2 == null) {
            return 'Menunggu verifikasi Kabag. TU';
        } elseif ($item->verif_3 == null) {
            return 'Menunggu verifikasi Wadir ADM dan Umum';
        } elseif ($item->verif_4 == null) {
            return 'Menunggu verifikasi Direktur';
        }
        return 'Selesai';
    }

    // Metode bantuan untuk generate tombol aksi
    private function generateActionButtons($item)
    {
        $buttons = '';

        // Tambahkan logika untuk tombol aksi sesuai kebutuhan
        $buttons .= '<a href="' . route('admin.mutasi-on-process.print-draft-sk', [
            $item->kd_karyawan, 
            $item->kd_mutasi, 
            $item->kd_jenis_mutasi
        ]) . '" target="_blank" class="btn btn-primary btn-sm d-block mb-2">Cetak Draft Nota</a>';

        $buttons .= '<a href="javascript:void(0)" 
            class="btn btn-secondary btn-sm d-block mb-2" 
            data-bs-toggle="modal" 
            data-bs-target="#kt_modal_log" 
            data-id="' . $item->kd_mutasi . '" 
            data-karyawan="' . $item->kd_karyawan . '" 
            id="log">Lihat Log</a>';

        return $buttons;
    }

    public function firstVerification(Request $request)
    {
        $kd_karyawan = $request->kd_karyawan;
        $kd_mutasi = $request->kd_mutasi;

        // $getNoHp = DB::table('hrd_karyawan')
        //     ->select('no_hp')
        //     ->where('kd_jabatan_struktural', 19)
        //     ->first();

        // $getNoHp = DB::table('hrd_karyawan')
        //     ->select('no_hp')
        //     ->where('kd_karyawan', '001635')
        //     ->first();

        // 0822 6791 3292 _  ubah ke +6282267913292
        // $no_hp = $this->formatPhoneNumber($getNoHp->no_hp);

        $getDataKaryawan = DB::table('hrd_karyawan')
            ->select('gelar_depan', 'nama', 'gelar_belakang')
            ->where('kd_karyawan', $kd_karyawan)
            ->first();

        $gelar_depan = $getDataKaryawan->gelar_depan ? $getDataKaryawan->gelar_depan . ' ' : '';
        $gelar_belakang = $getDataKaryawan->gelar_belakang ? '' . $getDataKaryawan->gelar_belakang : '';
        $nama_karyawan = $gelar_depan . $getDataKaryawan->nama . $gelar_belakang;

            // $message = "Ada Mutasi Baru dengan Kode Mutasi: {$kd_mutasi} dan Kode Karyawan: {$kd_karyawan} \n Silahkan cek aplikasi pada aplikasi HRD untuk melakukan verifikasi atau dengan klik link berikut:\n https://e-rsud.langsakota.go.id/rsud_hrd/admin/mutasi-on-process";

            // buat pesannya menjadi seperti berikut ini
            // Ada Mutasi Nota Tugas Baru dengan rincian sebagai berikut:
            // Kode Mutasi: {kd_mutasi}
            // Kode Karyawan: {kd_karyawan}
            // Nama Karyawan: {nama_karyawan}
            // Silahkan cek aplikasi pada aplikasi HRD untuk melakukan verifikasi atau dengan klik link berikut:
            // https://e-rsud.langsakota.go.id/rsud_hrd/admin/mutasi-on-process

            // $message = "Ada Mutasi Nota Tugas Baru dengan rincian sebagai berikut:\nKode Mutasi: {$kd_mutasi}\nKode Karyawan: {$kd_karyawan}\nNama Karyawan: {$nama_karyawan}\nSilahkan cek aplikasi pada aplikasi HRD untuk melakukan verifikasi atau dengan klik link berikut:\nhttps://e-rsud.langsakota.go.id/rsud_hrd/admin/mutasi-on-process";

            // dd($no_hp, $message);

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

            

            // $this->whatsappNotification($no_hp, $message);

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
        // Validasi server-side
        $request->validate([
            'tanggal' => 'required|date',
            'passphrase' => 'required|string|min:1',
            'kd_mutasi' => 'required',
            'kd_karyawan' => 'required',
            'jenis_mutasi' => 'required'
        ], [
            'tanggal.required' => 'Tanggal tanda tangan harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'passphrase.required' => 'Passphrase harus diisi',
            'passphrase.min' => 'Passphrase tidak boleh kosong'
        ]);

        $jenisMutasi = $request->jenis_mutasi;
        $kd_mutasi = $request->kd_mutasi;
        $kd_karyawan = $request->kd_karyawan;
        $passphrase = $request->passphrase;
        $tanggal = $request->tanggal;

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
                    'tgl_ttd' => Carbon::parse($tanggal),
                    'no_nota' => $no_nota
                ]);

            
            $getEmail = DB::table('hrd_karyawan as hk')
                ->leftJoin('hrd_tempat_kerja as htk', 'hk.kd_karyawan', '=', 'htk.kd_karyawan')
                ->select('hk.email', DB::raw('Max(htk.no_urut) as urut_max'))
                ->where('hk.kd_karyawan', $kd_karyawan)
                ->where('htk.kd_jenis_mutasi', $jenisMutasi)
                ->groupBy('hk.email')
                ->first();
                // dd($getEmail);

            // get urut max and plus 1
            $urutMax = $getEmail ? $getEmail->urut_max + 1 : 1;

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
                    'no_urut' => $urutMax,
                    'kd_jenis_mutasi' => $jenisMutasi
                ]);

            // $query = sqlsrv_query($konek, "update HRD_KARYAWAN set KD_JABATAN_STRUKTURAL = '".$getAllMutasi['KD_JAB_STRUK']."', TMT_JABATAN_STRUKTURAL = '".date_format($getAllMutasi['TMT_JABATAN'], 'Y-m-d')."', KD_JENIS_TENAGA = '".$getAllMutasi['KD_JENIS_TENAGA']."', KD_DETAIL_JENIS_TENAGA = '".$getAllMutasi['KD_DETAIL']."', KD_SUB_DETAIL_JENIS_TENAGA = '".$getAllMutasi['KD_SUB_DETAIL']."', KD_DIVISI = '".$getAllMutasi['KD_DIVISI']."', KD_UNIT_KERJA = '".$getAllMutasi['KD_UNIT_KERJA']."', KD_SUB_UNIT_KERJA = '".$getAllMutasi['KD_SUB_UNIT_KERJA']."', KD_RUANGAN = '".$getAllMutasi['KD_RUANGAN']."' where KD_KARYAWAN = '$kdkar'");
            // update data karyawan jika kd_jenis_mutasi = 1
            if ($jenisMutasi == 1) {
                $updateKaryawan = DB::table('hrd_karyawan')
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

                DB::table('hrd_tempat_kerja')
                    ->where('kd_karyawan', $kd_karyawan)
                    ->where('no_urut', $urutMax - 1)
                    ->update([
                        'tgl_keluar' => $getMutasi->tmt_jabatan
                    ]);
            } else if ($jenisMutasi == 3) {
                // update hrd_tempat_kerja saja
                DB::table('hrd_tempat_kerja')
                    ->where('kd_karyawan', $kd_karyawan)
                    ->where('no_urut', $urutMax - 1)
                    ->update([
                        'tgl_keluar' => $getMutasi->tmt_jabatan
                    ]);
            }

            $printSkResponse = $this->printSk($kd_karyawan, $kd_mutasi, $passphrase, $jenisMutasi);
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

    public function rincian(Request $request)
    {
        $kdMutasi = $request->kd_mutasi;
        $kdKaryawan = $request->kd_karyawan;
        $jenisMutasi = $request->jenis_mutasi;

        // jika kd_jenis_mutasi = 1 maka tampilkan rincian mutasi pegawai
        if ($jenisMutasi == 1) {
            $getRincian = DB::table('view_proses_mutasi as vpm')
            ->join('view_max_mutasi as vmm', function ($join) use ($kdKaryawan) {
                $join->on('vpm.kd_mutasi', '=', 'vmm.kd_mutasi_max')
                     ->where('vmm.kd_karyawan', '=', $kdKaryawan);
            })
            ->where('vpm.kd_karyawan', $kdKaryawan)
            ->select('vpm.*')
            ->first();
        } else if ($jenisMutasi == 3) {
            // kalau jenis mutasi = 3 maka tampilkan rincian mutasi pegawai dengan mengambil data dari view_proses_mutasi berdasarkan kdMutasi
            $getRincian = DB::table('view_proses_mutasi as vpm')
                ->where('vpm.kd_mutasi', $kdMutasi)
                ->where('vpm.kd_karyawan', $kdKaryawan)
                ->select('vpm.*')
                ->first();
        }

        return view('mutasi.mutasi-on-process.rincian', [
            'getRincian' => $getRincian
        ]);
    }

    private function printSk($kd_karyawan, $kd_mutasi, $passphrase, $kd_jenis_mutasi)
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
            // print_r($getDirektur);
            // die;
            // dd($getVerifikasi);

        $year = date('Y');
        // Gunakan hrd_files disk untuk penyimpanan yang aman
        $pdfFilePath = 'mutasi-documents/' . $year . '/Nota_Tugas_Mutasi_' . $year . '_' . $kd_mutasi . '_' . $kd_karyawan . '.pdf';

        // Pastikan direktori ada di hrd_files disk
        $directory = 'mutasi-documents/' . $year;
        if (!Storage::disk('hrd_files')->exists($directory)) {
            Storage::disk('hrd_files')->makeDirectory($directory);
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
            'logoEsign' => $logoEsign,
            'kd_jenis_mutasi' => $kd_jenis_mutasi
        ];

        // Generate HTML dari blade template
        $html = view('mutasi.mutasi-on-process.nota-tugas-final', $data)->render();

        // Konfigurasi Mpdf
        $mpdf = new Mpdf([
            'format' => [215, 330], // ukuran kertas 21,5 x 33 cm
            'orientation' => 'P',
            'margin_top' => 10,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 25,
            'margin_footer' => 5,
            'default_font_size' => 11,
            'fontDir' => [base_path('public/assets/fonts/')],
            'fontdata' => [
                'bookman-old-style' => [
                    'R' => 'Bookman Old Style Regular.ttf',
                    'B' => 'Bookman Old Style Bold.ttf',
                    'I' => 'Bookman Old Style Italic.ttf',
                    'BI' => 'Bookman Old Style Bold Italic.ttf'
                ]
            ],
            'default_font' => 'bookman-old-style'
        ]);

        // Set watermark DRAFT untuk testing sebelum masuk production
        // $mpdf->SetWatermarkText('DRAFT', 0.1);
        // $mpdf->showWatermarkText = true;

        $mpdf->WriteHTML($html);
        $pdfOutput = $mpdf->Output('', 'S');

        // Simpan PDF ke hrd_files disk yang aman
        Storage::disk('hrd_files')->put($pdfFilePath, $pdfOutput);

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
    public function printDraftSk($kd_karyawan, $kd_mutasi, $kd_jenis_mutasi)
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

        // Data untuk template
        $data = [
            'getVerifikasi' => $getVerifikasi,
            'getDataLama' => $getDataLama,
            'getDirektur' => $getDirektur,
            'logo' => $logo,
            'logoLangsa' => $logoLangsa,
            'logoEsign' => $logoEsign,
            'kd_jenis_mutasi' => $kd_jenis_mutasi
        ];

        // Generate HTML dari blade template
        $html = view('mutasi.mutasi-on-process.nota-tugas-final', $data)->render();

        // Konfigurasi Mpdf dengan watermark DRAFT
        $mpdf = new Mpdf([
            'format' => [215, 330], // ukuran kertas 21,5 x 33 cm
            'orientation' => 'P',
            'margin_top' => 10,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 5,
            'margin_footer' => 5,
            'default_font_size' => 11,
            'fontDir' => [base_path('public/assets/fonts/')],
            'fontdata' => [
                'bookman-old-style' => [
                    'R' => 'Bookman Old Style Regular.ttf',
                    'B' => 'Bookman Old Style Bold.ttf',
                    'I' => 'Bookman Old Style Italic.ttf',
                    'BI' => 'Bookman Old Style Bold Italic.ttf'
                ]
            ],
            'default_font' => 'bookman-old-style'
        ]);

        // Set watermark untuk draft
        $mpdf->SetWatermarkText('DRAFT', 0.1);
        $mpdf->showWatermarkText = true;

        $mpdf->WriteHTML($html);

        // Download langsung tanpa simpan ke storage (untuk draft)
        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Draft Nota Tugas Mutasi - ' . $kd_mutasi . '-' . $kd_karyawan . '.pdf"');
    }

    private function getNotaNumber($kd_karyawan)
    {
        // Mengambil jenis mutasi untuk karyawan tertentu
        $jenisMutasi = DB::table('hrd_r_mutasi')
            ->select('kd_jenis_mutasi')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tahap_mutasi', 1)
            ->first();

        // Cek apakah data jenis mutasi ada
        if (!$jenisMutasi) {
            Log::info("No mutasi type found for kd_karyawan: {$kd_karyawan}");
            return null;
        }

        // Menentukan prefix berdasarkan kd_jenis_mutasi
        $prefix = '';
        if ($jenisMutasi->kd_jenis_mutasi == 1) {
            $prefix = 'NT';
        } elseif ($jenisMutasi->kd_jenis_mutasi == 3) {
            $prefix = 'NTT';
        } else {
            Log::info("Invalid kd_jenis_mutasi: {$jenisMutasi->kd_jenis_mutasi}");
            return null;
        }

        // Mengambil nomor nota yang sudah ada untuk tahun ini
        $year = date('Y');
        $fetchNotaNumber = DB::table('hrd_r_mutasi')
            ->select(DB::raw('SUBSTRING(no_nota, 7, 3) as nomor'))
            ->where('kd_jenis_mutasi', $jenisMutasi->kd_jenis_mutasi)
            ->whereRaw('RIGHT(no_nota, 4) = ?', [$year])
            ->orderBy(DB::raw('SUBSTRING(no_nota, 7, 3)'), 'desc')
            ->first();

        // Jika nomor nota kosong
        $currentNumber = $fetchNotaNumber ? (int)$fetchNotaNumber->nomor + 1 : 1;

        // Jika prefix adalah NTT dan belum ada nomor, mulai dari 1
        if ($prefix === 'NTT' && !$fetchNotaNumber) {
            $currentNumber = 1;
        }

        $notaNumber = sprintf('%03s', $currentNumber);
        // $no_nota = "875.{$jenisMutasi->kd_jenis_mutasi}/{$notaNumber}/{$prefix}/{$year}";
        $no_nota = "875.1/{$notaNumber}/{$prefix}/{$year}";

        // Update nomor nota ke tabel mutasi
        DB::table('hrd_r_mutasi')
            ->where('kd_karyawan', $kd_karyawan)
            ->where('kd_tahap_mutasi', 1)
            ->update([
                'no_nota' => $no_nota
            ]);

        return $no_nota;
    }


    private function old_getNotaNumber($kd_karyawan)
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
                        'contents' => Storage::disk('hrd_files')->get($pdfFilePath),
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

        $filename = 'Nota_Tugas_Mutasi_' . $id_dokumen . '.pdf';
        $year = date('Y');
        $directory = 'mutasi-documents/' . $year . '/signed';
        $filePath = $directory . '/' . $filename;

        // Pastikan direktori signed ada di hrd_files disk
        if (!Storage::disk('hrd_files')->exists($directory)) {
            Storage::disk('hrd_files')->makeDirectory($directory);
        }

        // Download file dari server TTE
        $client = new Client();
        $response = $client->request('GET', $endpoint, [
            'headers' => [
                'Authorization' => 'Basic ZXNpZ246cXdlcnR5'
            ]
        ]);

        // Simpan file yang sudah ditandatangani ke hrd_files disk
        Storage::disk('hrd_files')->put($filePath, $response->getBody()->getContents());

        // Update database dengan path baru
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

    private function formatPhoneNumber($phoneNumber) 
    {
        // Hapus semua spasi dan karakter _
        $phoneNumber = str_replace([' ', '_'], '', $phoneNumber);
    
        // Ganti awalan 08 dengan +628
        if (substr($phoneNumber, 0, 2) === '08') {
            $phoneNumber = substr_replace($phoneNumber, '+628', 0, 2);
        }
    
        return $phoneNumber;
    }

    // private function whatsappNotification($recepient, $message)
    // {
    //     $sid = getenv("TWILIO_AUTH_SID");
    //     $token = getenv("TWILIO_AUTH_TOKEN");
    //     $wa_from = getenv("TWILIO_WHATSAPP_FROM");

    //     $twilio = new Client($sid, $token);

    //     return $twilio->messages->create("whatsapp:$recepient", [
    //         "from" => "whatsapp:$wa_from",
    //         "body" => $message
    //     ]);
    // }

    public function array()
    {
        $array = ['Banana', 'banana', 'Apple', 'apple', 'zalaca'];

        // jadikan lower agar tidak case sensitive
        $array = array_map('strtolower', $array);

        // hapus duplikat
        $unique = array_unique($array);

        // urutkan
        sort($unique);

        print_r($unique);
    }

    /**
     * Download dokumen mutasi nota yang sudah ditandatangani
     */
    public function downloadMutasiDocument($id_dokumen)
    {
        try {
            // Cari record berdasarkan id_dokumen
            $mutasi = DB::table('hrd_r_mutasi')
                ->where('id_dokumen', $id_dokumen)
                ->whereNotNull('path_dokumen')
                ->first();

            if (!$mutasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }

            // Periksa apakah file ada di hrd_files disk
            if (!Storage::disk('hrd_files')->exists($mutasi->path_dokumen)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File dokumen tidak ditemukan'
                ], 404);
            }

            // Return file untuk download
            $fileContent = Storage::disk('hrd_files')->get($mutasi->path_dokumen);
            $fileName = 'Nota_Tugas_Mutasi_' . $id_dokumen . '.pdf';
            
            return response($fileContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup file temporary mutasi nota setelah TTE selesai
     */
    public function cleanupMutasiTemporaryFiles()
    {
        try {
            $cleanedCount = 0;
            $year = date('Y');
            
            // Ambil data mutasi yang sudah selesai TTE (ada path_dokumen dan id_dokumen)
            $completedMutasi = DB::table('hrd_r_mutasi')
                ->whereNotNull('id_dokumen')
                ->whereNotNull('path_dokumen')
                ->get(['kd_mutasi', 'kd_karyawan']);

            foreach ($completedMutasi as $mutasi) {
                // File temporary yang akan dihapus
                $tempFile = 'mutasi-documents/' . $year . '/Nota_Tugas_Mutasi_' . $year . '_' . $mutasi->kd_mutasi . '_' . $mutasi->kd_karyawan . '.pdf';
                
                if (Storage::disk('hrd_files')->exists($tempFile)) {
                    Storage::disk('hrd_files')->delete($tempFile);
                    $cleanedCount++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil membersihkan $cleanedCount file temporary mutasi nota"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat cleanup: ' . $e->getMessage()
            ], 500);
        }
    }
}
