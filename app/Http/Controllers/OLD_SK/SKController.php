<?php

namespace App\Http\Controllers\SK;

use Mpdf\Mpdf;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\ImageManagerStatic as Image;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
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
        // Ambil tahun dari request atau gunakan tahun saat ini
        $tahun = $request->query('tahun', date('Y'));

        // Hitung jumlah SK untuk ditampilkan di halaman
        $totalSk = DB::table('hrd_sk_pegawai_kontrak')
            ->where('tahun_sk', $tahun)
            ->distinct('urut')
            ->count('urut');

        $totalPending = DB::table('hrd_sk_pegawai_kontrak')
            ->where('tahun_sk', $tahun)
            ->where('verif_1', 0)
            ->distinct('urut')
            ->count('urut');

        return view('sk.index', [
            'totalSk' => $totalSk,
            'totalPending' => $totalPending,
            'tahun' => $tahun
        ]);
    }

    public function datatable(Request $request)
    {
        // Ambil jabatan dan ruangan dari user yang login
        $jabatan = Auth::user()->karyawan->kd_jabatan_struktural;
        $ruangan = Auth::user()->karyawan->kd_ruangan;
        $searchValue = $request->search['value'];
        $tahun = $request->tahun ?: date('Y'); // Ambil tahun dari request atau gunakan tahun saat ini

        // Subquery untuk mendapatkan kd_karyawan pertama per urut
        $subQuery = DB::table('hrd_sk_pegawai_kontrak')
            ->select('urut', 'kd_karyawan')
            ->whereIn('kd_index', function ($query) use ($tahun) {
                $query->select(DB::raw('MIN(kd_index)'))
                    ->from('hrd_sk_pegawai_kontrak')
                    ->where('tahun_sk', $tahun)
                    ->groupBy('urut');
            })
            ->where('tahun_sk', $tahun);

        // Subquery untuk join dengan view_tampil_karyawan
        $vtkQuery = DB::table('view_tampil_karyawan as vtk')
            ->select('vtk.kd_karyawan', 'vtk.gelar_depan', 'vtk.nama', 'vtk.gelar_belakang', 'hspk_sub.urut')
            ->joinSub($subQuery, 'hspk_sub', function ($join) {
                $join->on('vtk.kd_karyawan', '=', 'hspk_sub.kd_karyawan');
            });

        // Query utama
        $query = DB::table('hrd_sk_pegawai_kontrak as hspk')
            ->select(
                'hspk.urut',
                DB::raw('COUNT(hspk.kd_karyawan) as jumlah_pegawai'),
                'hspk.nomor_konsederan',
                'hspk.tahun_sk',
                'hspk.tgl_sk',
                'hspk.stt',
                'hspk.no_sk',
                'hspk.verif_1',
                'hspk.verif_2',
                'hspk.verif_3',
                'hspk.verif_4',
                'hspk.tgl_ttd',
                'hspk.path_dokumen',
                DB::raw("CASE 
                            WHEN hspk.nomor_konsederan = '' THEN 
                                COALESCE(vtk.kd_karyawan + '<br>' + ISNULL(vtk.gelar_depan, '') + ' ' + vtk.nama + ISNULL(vtk.gelar_belakang, ''), '~') 
                            ELSE '~' 
                        END as nama")
            )
            ->leftJoinSub($vtkQuery, 'vtk', function ($join) {
                $join->on('vtk.urut', '=', 'hspk.urut');
            })
            ->where('hspk.tahun_sk', $tahun)
            ->groupBy(
                'hspk.urut',
                'hspk.nomor_konsederan',
                'hspk.tahun_sk',
                'hspk.tgl_sk',
                'hspk.stt',
                'hspk.no_sk',
                'hspk.verif_1',
                'hspk.verif_2',
                'hspk.verif_3',
                'hspk.verif_4',
                'hspk.tgl_ttd',
                'hspk.path_dokumen',
                'vtk.kd_karyawan',
                'vtk.gelar_depan',
                'vtk.nama',
                'vtk.gelar_belakang'
            );

        // Tambahkan filter pencarian jika ada
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('hspk.no_sk', 'like', "%{$searchValue}%")
                ->orWhere('hspk.nomor_konsederan', 'like', "%{$searchValue}%");
                
                // Check if vtk join exists before searching those columns
                if (Schema::hasTable('view_tampil_karyawan')) {
                    $q->orWhere('vtk.nama', 'like', "%{$searchValue}%")
                    ->orWhere('vtk.kd_karyawan', 'like', "%{$searchValue}%");
                }
            });
        }

        // Gunakan Yajra DataTables untuk memproses query
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('no_sk', function ($item) {
                return ($item->no_sk == "") ? "-" : "Peg. 445/{$item->no_sk}/SK/{$item->tahun_sk}";
            })
            ->editColumn('tgl_sk', function ($item) {
                return $item->tgl_sk ? date('d-m-Y', strtotime($item->tgl_sk)) : '';
            })
            ->editColumn('tgl_ttd', function ($item) {
                return $item->tgl_ttd ? date('d-m-Y', strtotime($item->tgl_ttd)) : '';
            })
            ->editColumn('status', function ($item) use ($jabatan, $ruangan) {
                return view('sk.datatables._status', compact('item', 'jabatan', 'ruangan'))->render();
            })
            ->addColumn('aksi', function ($item) use ($jabatan, $ruangan) {
                return view('sk.datatables._actions', compact('item', 'jabatan', 'ruangan'))->render();
            })
            // Disable the global search and use only the specific search fields
            ->filterColumn('no_sk', function($query, $keyword) {
                $query->where('hspk.no_sk', 'like', "%{$keyword}%");
            })
            ->filterColumn('nomor_konsederan', function($query, $keyword) {
                $query->where('hspk.nomor_konsederan', 'like', "%{$keyword}%");
            })
            ->filterColumn('nama', function($query, $keyword) {
                $query->where('vtk.nama', 'like', "%{$keyword}%")
                    ->orWhere('vtk.kd_karyawan', 'like', "%{$keyword}%");
            })
            ->rawColumns(['nama', 'status', 'aksi'])
            ->make(true);
    }

    public function old_2_datatable(Request $request)
    {
        // Ambil jabatan dan ruangan dari user yang login
        $jabatan = Auth::user()->karyawan->kd_jabatan_struktural;
        $ruangan = Auth::user()->karyawan->kd_ruangan;
        $searchValue = $request->search['value'];
        $tahun = $request->tahun ?: date('Y'); // Ambil tahun dari request atau gunakan tahun saat ini

        // Subquery untuk view_tampil_karyawan
        $subQuery = DB::table('view_tampil_karyawan as vtk')
            ->select('vtk.kd_karyawan', 'vtk.gelar_depan', 'vtk.nama', 'vtk.gelar_belakang', 'hspk_sub.urut')
            ->join(DB::raw("(
                SELECT urut, kd_karyawan
                FROM hrd_sk_pegawai_kontrak
                WHERE kd_index IN (
                    SELECT MIN(kd_index)
                    FROM hrd_sk_pegawai_kontrak
                    WHERE tahun_sk = '{$tahun}'
                    GROUP BY urut
                )
                AND tahun_sk = '{$tahun}'
            ) hspk_sub"), 'vtk.kd_karyawan', '=', 'hspk_sub.kd_karyawan');

        // Query dasar
        $query = DB::table('hrd_sk_pegawai_kontrak as hspk')
            ->select([
                'hspk.urut',
                DB::raw('COUNT(hspk.kd_karyawan) as jumlah_pegawai'),
                'hspk.nomor_konsederan',
                'hspk.tahun_sk',
                'hspk.tgl_sk',
                'hspk.stt',
                'hspk.no_sk',
                'hspk.verif_1',
                'hspk.verif_2',
                'hspk.verif_3',
                'hspk.verif_4',
                'hspk.tgl_ttd',
                'hspk.path_dokumen',
                DB::raw("CASE 
                    WHEN hspk.nomor_konsederan = '' THEN 
                        COALESCE(vtk.kd_karyawan + '<br>' + ISNULL(vtk.gelar_depan, '') + ' ' + vtk.nama + ISNULL(vtk.gelar_belakang, ''), '~') 
                    ELSE '~' 
                END as nama")
            ])
            ->leftJoin(DB::raw("({$subQuery->toSql()}) as vtk"), 'vtk.urut', '=', 'hspk.urut')
            ->where('hspk.tahun_sk', $tahun)
            ->groupBy(
                'hspk.urut',
                'hspk.nomor_konsederan',
                'hspk.tahun_sk',
                'hspk.tgl_sk',
                'hspk.stt',
                'hspk.no_sk',
                'hspk.verif_1',
                'hspk.verif_2',
                'hspk.verif_3',
                'hspk.verif_4',
                'hspk.tgl_ttd',
                'hspk.path_dokumen',
                'vtk.kd_karyawan',
                'vtk.gelar_depan',
                'vtk.nama',
                'vtk.gelar_belakang'
            );

        // Tambahkan filter pencarian jika ada
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('hspk.no_sk', 'like', "%{$searchValue}%")
                ->orWhere('hspk.nomor_konsederan', 'like', "%{$searchValue}%")
                ->orWhere('vtk.nama', 'like', "%{$searchValue}%")
                ->orWhere('vtk.kd_karyawan', 'like', "%{$searchValue}%");
            });
        }

        // Implementasi manual untuk total records dan filtered records
        $countQuery = clone $query;
        $totalRecords = $countQuery->count();
        
        $filteredRecords = $totalRecords; // Jika tidak ada filter tambahan
        
        // Hitung ulang jika ada pencarian
        if (!empty($searchValue)) {
            $filteredQuery = clone $query;
            $filteredRecords = $filteredQuery->count();
        }

        // Tambahkan pengurutan dan pagination
        $start = $request->start;
        $length = $request->length;
        
        if ($request->order && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];
            $columnName = $request->columns[$columnIndex]['data'];
            $columnDir = $request->order[0]['dir'];
            
            // Handle pengurutan khusus
            if ($columnName == 'status') {
                $query->orderBy('hspk.verif_1', $columnDir);
            } elseif ($columnName != 'aksi' && $columnName != 'nama') {
                $query->orderBy($columnName, $columnDir);
            } else {
                // Default sort by urut
                $query->orderBy('hspk.urut', 'desc');
            }
        } else {
            // Default sort
            $query->orderBy('hspk.urut', 'desc');
        }
        
        // Tambahkan pagination
        $data = $query->skip($start)->take($length)->get();

        // Format data untuk response
        $formattedData = $data->map(function($item) use ($jabatan, $ruangan) {
            return [
                'urut' => $item->urut,
                'jumlah_pegawai' => $item->jumlah_pegawai,
                'no_sk' => ($item->no_sk == "") ? "-" : "Peg. 445/{$item->no_sk}/SK/{$item->tahun_sk}",
                'nama' => $item->nama,
                'tgl_sk' => $item->tgl_sk ? date('d-m-Y', strtotime($item->tgl_sk)) : '',
                'tgl_ttd' => $item->tgl_ttd ? date('d-m-Y', strtotime($item->tgl_ttd)) : '',
                'status' => view('sk.datatables._status', compact('item', 'jabatan', 'ruangan'))->render(),
                'aksi' => view('sk.datatables._actions', compact('item', 'jabatan', 'ruangan'))->render(),
            ];
        });

        // Membuat response Yajra format
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $formattedData
        ]);
    }

    public function old_datatable(Request $request)
    {
        // Ambil jabatan dan ruangan dari user yang login
        $jabatan = Auth::user()->karyawan->kd_jabatan_struktural;
        $ruangan = Auth::user()->karyawan->kd_ruangan;
        $searchValue = $request->search['value'];
        $tahun = $request->tahun ?: date('Y'); // Ambil tahun dari request atau gunakan tahun saat ini

        // Query dasar menggunakan query gabungan
        $query = DB::table('hrd_sk_pegawai_kontrak as hspk')
            ->select(
                'hspk.urut',
                DB::raw('COUNT(hspk.kd_karyawan) as jumlah_pegawai'),
                'hspk.nomor_konsederan',
                'hspk.tahun_sk',
                'hspk.tgl_sk',
                'hspk.stt',
                'hspk.no_sk',
                'hspk.verif_1',
                'hspk.verif_2',
                'hspk.verif_3',
                'hspk.verif_4',
                'hspk.tgl_ttd',
                'hspk.path_dokumen',
                DB::raw("CASE 
                            WHEN hspk.nomor_konsederan = '' THEN 
                                COALESCE(vtk.kd_karyawan + '<br>' + ISNULL(vtk.gelar_depan, '') + ' ' + vtk.nama + ISNULL(vtk.gelar_belakang, ''), '~') 
                            ELSE '~' 
                         END as nama")
            )
            ->leftJoinSub(
                "(
                    SELECT vtk.kd_karyawan, vtk.gelar_depan, vtk.nama, vtk.gelar_belakang, hspk_sub.urut
                    FROM view_tampil_karyawan vtk
                    INNER JOIN (
                        SELECT urut, kd_karyawan
                        FROM hrd_sk_pegawai_kontrak
                        WHERE kd_index IN (
                            SELECT MIN(kd_index)
                            FROM hrd_sk_pegawai_kontrak
                            WHERE tahun_sk = ?
                            GROUP BY urut
                        )
                        AND tahun_sk = ?
                    ) hspk_sub ON vtk.kd_karyawan = hspk_sub.kd_karyawan
                )",
                'vtk',
                'vtk.urut',
                '=',
                'hspk.urut',
                [$tahun, $tahun]
            )
            ->where('hspk.tahun_sk', $tahun)
            ->groupBy(
                'hspk.urut',
                'hspk.nomor_konsederan',
                'hspk.tahun_sk',
                'hspk.tgl_sk',
                'hspk.stt',
                'hspk.no_sk',
                'hspk.verif_1',
                'hspk.verif_2',
                'hspk.verif_3',
                'hspk.verif_4',
                'hspk.tgl_ttd',
                'hspk.path_dokumen',
                'vtk.kd_karyawan',
                'vtk.gelar_depan',
                'vtk.nama',
                'vtk.gelar_belakang'
            );

        // Tambahkan filter pencarian jika ada
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('hspk.no_sk', 'like', "%{$searchValue}%")
                  ->orWhere('hspk.nomor_konsederan', 'like', "%{$searchValue}%")
                  ->orWhere('vtk.nama', 'like', "%{$searchValue}%")
                  ->orWhere('vtk.kd_karyawan', 'like', "%{$searchValue}%");
            });
        }

        // Gunakan Yajra DataTables untuk memproses query
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('no_sk', function($item) {
                return ($item->no_sk == "") ? "-" : "Peg. 445/{$item->no_sk}/SK/{$item->tahun_sk}";
            })
            ->editColumn('tgl_sk', function($item) {
                return $item->tgl_sk ? date('d-m-Y', strtotime($item->tgl_sk)) : '';
            })
            ->editColumn('tgl_ttd', function($item) {
                return $item->tgl_ttd ? date('d-m-Y', strtotime($item->tgl_ttd)) : '';
            })
            ->editColumn('status', function($item) use ($jabatan, $ruangan) {
                return view('sk.datatables._status', compact('item', 'jabatan', 'ruangan'))->render();
            })
            ->addColumn('aksi', function($item) use ($jabatan, $ruangan) {
                return view('sk.datatables._actions', compact('item', 'jabatan', 'ruangan'))->render();
            })
            ->rawColumns(['nama', 'status', 'aksi'])
            ->make(true);
    }

    public function old_index(Request $request)
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

        dd($skKontrak->toArray());
        dd($skKontrak);

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
        // Validasi input
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'passphrase' => 'required|string|min:1',
            'urut_rincian_verif' => 'required',
            'tahun_rincian_verif' => 'required',
            'kd_karyawan' => 'required|array|min:1'
        ], [
            'tanggal.required' => 'Tanggal tanda tangan SK wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'passphrase.required' => 'Passphrase (Password TTE) wajib diisi',
            'passphrase.min' => 'Passphrase tidak boleh kosong',
            'urut_rincian_verif.required' => 'Data urut tidak valid',
            'tahun_rincian_verif.required' => 'Data tahun tidak valid',
            'kd_karyawan.required' => 'Data karyawan tidak valid',
            'kd_karyawan.min' => 'Minimal harus ada satu karyawan yang dipilih'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

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

        // Gunakan disk hrd_files untuk penyimpanan SK
        $fileName = $totalRow > 1
            ? 'SK_Pegawai_Kontrak_' . $tahun . '_' . $urut . '.pdf'
            : 'SK_Pegawai_Kontrak_' . $tahun . '_' . $urut . '_' . $kd_karyawan . '.pdf'
        ;
        
        $pdfFilePath = 'sk-documents/' . $tahun . '/' . $fileName;

        // Pastikan direktori SK ada di disk hrd_files
        if (!Storage::disk('hrd_files')->exists('sk-documents/' . $tahun)) {
            Storage::disk('hrd_files')->makeDirectory('sk-documents/' . $tahun);
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
        
        
        // Use LaravelMpdf yang tersedia di sistem
        $pdf = PDF::loadView('sk.sk-pegawai-kontrak', $data, [], [
            'format' => [215, 330], // ukuran kertas 21,5 x 33 cm
            'orientation' => 'P',
            'margin_top' => 5,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 5,
            'margin_footer' => 5,
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

        // Simpan PDF ke disk hrd_files
        $pdfOutput = $pdf->output();
        
        Storage::disk('hrd_files')->put($pdfFilePath, $pdfOutput);

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
            // ->first();
            ->get();

            // dd($results);

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

        foreach ($results as $result) {
            $pdf->AddPage('P', '', '', '', '', 15, 15, 5, 15, 5, 5);

            $html = view('sk.perjanjian-kerja-page-1', compact('result', 'direktur', 'tahun', 'logoLangsa'))->render();
            // footer from view
            $pdf->SetHTMLFooter(view('sk.footer-perjanjian-kerja-page-1', compact('result', 'direktur', 'tahun', 'logoLangsa'))->render());

            $pdf->WriteHTML($html);

            // tambah halaman baru page-2
            $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);

            $html = view('sk.perjanjian-kerja-page-2', compact('result', 'direktur', 'tahun', 'logoLangsa'))->render();

            $pdf->WriteHTML($html);

            // tambah halaman baru page-3
            $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);

            $html = view('sk.perjanjian-kerja-page-3', compact('result', 'direktur', 'tahun', 'logoLangsa'))->render();

            $pdf->WriteHTML($html);

            // tambah halaman baru page-4
            $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);

            $html = view('sk.perjanjian-kerja-page-4', compact('result', 'direktur', 'tahun', 'logoLangsa'))->render();

            $pdf->WriteHTML($html);

            // $pdf->Output('Perjanjian-Kerja-' . $result->kd_karyawan . '-' . $result->tahun_sk . '.pdf', 'I');
            // $pdf->Output('Perjanjian-Kerja Pegawai Kontrak.pdf', 'I');
        }

        $pdf->Output('Perjanjian-Kerja Pegawai Kontrak.pdf', 'I');

        // $pdf->addPage('P', '', '', '', '', 15, 15, 5, 15, 5, 5);

        // $html = view('sk.perjanjian-kerja-page-1', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();
        // // footer from view
        // $pdf->SetHTMLFooter(view('sk.footer-perjanjian-kerja-page-1', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render());

        // $pdf->WriteHTML($html);

        // // tambah halaman baru page-2
        // $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);

        // $html = view('sk.perjanjian-kerja-page-2', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();
        // $pdf->WriteHTML($html);

        // // tambah halaman baru page-3
        // $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);
        
        // $html = view('sk.perjanjian-kerja-page-3', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();
        // $pdf->WriteHTML($html);

        // // tambah halaman baru page-4
        // $pdf->AddPage('P', '', '', '', '', 15, 15, 15, 15, 5, 5);

        // $html = view('sk.perjanjian-kerja-page-4', compact('results', 'direktur', 'tahun', 'logoLangsa'))->render();
        // $pdf->WriteHTML($html);

        // $pdf->Output('Perjanjian-Kerja-' . $results->kd_karyawan . '-' . $results->tahun_sk . '.pdf', 'I');
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

        // use App Container - use LaravelMpdf instead
        $pdf = PDF::loadHTML($html, [], [
            'format' => [215, 330], // 21.5 x 33 cm
            'orientation' => 'P',
            'margin_top' => 5,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 5,
            'margin_footer' => 5,
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

        return $pdf->stream('Perjanjian-Kerja-' . $results->kd_karyawan . '-' . $results->tahun_sk . '.pdf');
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
        $pdf = PDF::loadView('sk.perjanjian-kerja-page-1', $data, [], [
            'format' => [215, 330], // ukuran kertas 21,5 x 33 cm
            'orientation' => 'P',
            'margin_top' => 5,
            'margin_right' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_header' => 5,
            'margin_footer' => 5,
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
                $this->updateVerification($kd_karyawan, $urut, $tahun, $id_dokumen);

                // download the document
                $this->downloadSignedDocument($id_dokumen);

                // Hapus file temporary setelah berhasil dikirim ke server BSRE dan didownload
                try {
                    if (Storage::disk('hrd_files')->exists($pdfFilePath)) {
                        Storage::disk('hrd_files')->delete($pdfFilePath);
                        Log::info('File temporary SK berhasil dihapus', [
                            'file_path' => $pdfFilePath,
                            'urut' => $urut,
                            'tahun' => $tahun,
                            'kd_karyawan' => $kd_karyawan
                        ]);
                    }
                } catch (\Exception $deleteException) {
                    // Log error jika gagal menghapus file, tapi tidak menggagalkan proses utama
                    Log::warning('Gagal menghapus file temporary SK', [
                        'file_path' => $pdfFilePath,
                        'error' => $deleteException->getMessage(),
                        'urut' => $urut,
                        'tahun' => $tahun,
                        'kd_karyawan' => $kd_karyawan
                    ]);
                }

                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Proses TTE berhasil.'
                ]);
            } else {
                // Hapus file temporary jika TTE gagal
                try {
                    if (Storage::disk('hrd_files')->exists($pdfFilePath)) {
                        Storage::disk('hrd_files')->delete($pdfFilePath);
                        Log::info('File temporary SK dihapus karena TTE gagal', [
                            'file_path' => $pdfFilePath,
                            'response_code' => $response->getStatusCode(),
                            'urut' => $urut,
                            'tahun' => $tahun,
                            'kd_karyawan' => $kd_karyawan
                        ]);
                    }
                } catch (\Exception $deleteException) {
                    Log::warning('Gagal menghapus file temporary SK setelah TTE gagal', [
                        'file_path' => $pdfFilePath,
                        'delete_error' => $deleteException->getMessage(),
                        'urut' => $urut,
                        'tahun' => $tahun,
                        'kd_karyawan' => $kd_karyawan
                    ]);
                }

                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Proses TTE gagal.' . $headers['original']['message'],
                ], 500);
            }
        } catch (\Exception $e) {
            $this->serverError($kd_karyawan, $urut, $tahun);

            // Hapus file temporary jika terjadi error dalam proses TTE
            try {
                if (Storage::disk('hrd_files')->exists($pdfFilePath)) {
                    Storage::disk('hrd_files')->delete($pdfFilePath);
                    Log::info('File temporary SK dihapus karena error TTE', [
                        'file_path' => $pdfFilePath,
                        'error' => $e->getMessage(),
                        'urut' => $urut,
                        'tahun' => $tahun,
                        'kd_karyawan' => $kd_karyawan
                    ]);
                }
            } catch (\Exception $deleteException) {
                Log::warning('Gagal menghapus file temporary SK setelah error TTE', [
                    'file_path' => $pdfFilePath,
                    'delete_error' => $deleteException->getMessage(),
                    'original_error' => $e->getMessage(),
                    'urut' => $urut,
                    'tahun' => $tahun,
                    'kd_karyawan' => $kd_karyawan
                ]);
            }

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

        $filename = 'SK_Pegawai_Kontrak_TTE_' . $id_dokumen . '.pdf';

        $year = date('Y');
        $filePath = 'sk-documents/' . $year . '/' . $filename;

        // Pastikan direktori ada di disk hrd_files
        if (!Storage::disk('hrd_files')->exists('sk-documents/' . $year)) {
            Storage::disk('hrd_files')->makeDirectory('sk-documents/' . $year);
        }

        // Download file dari server TTE
        $client = new Client();
        $response = $client->request('GET', $endpoint, [
            'headers' => [
                'Authorization' => 'Basic ZXNpZ246cXdlcnR5'
            ]
        ]);

        // Simpan file ke disk hrd_files
        Storage::disk('hrd_files')->put($filePath, $response->getBody()->getContents());

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

    /**
     * Menampilkan dokumen SK dari disk hrd_files
     */
    public function showSkDocument($year, $filename)
    {
        try {
            $filePath = 'sk-documents/' . $year . '/' . $filename;
            
            if (!Storage::disk('hrd_files')->exists($filePath)) {
                abort(404, 'Dokumen SK tidak ditemukan');
            }

            $fileContent = Storage::disk('hrd_files')->get($filePath);
            
            // Determine MIME type based on file extension
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $mimeType = $extension === 'pdf' ? 'application/pdf' : 'application/octet-stream';

            return response($fileContent)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            Log::error('Error displaying SK document: ' . $e->getMessage());
            abort(500, 'Error saat mengakses dokumen SK');
        }
    }

    /**
     * Membersihkan file temporary SK yang sudah lama (lebih dari 24 jam)
     * untuk menghemat ruang penyimpanan
     */
    public function cleanupTemporaryFiles()
    {
        try {
            $currentYear = date('Y');
            $skDocumentsPath = 'sk-documents/' . $currentYear;
            
            if (!Storage::disk('hrd_files')->exists($skDocumentsPath)) {
                Log::info('Directory SK documents tidak ditemukan untuk cleanup', ['path' => $skDocumentsPath]);
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Tidak ada directory untuk dibersihkan'
                ]);
            }

            $files = Storage::disk('hrd_files')->allFiles($skDocumentsPath);
            $deletedCount = 0;
            $deletedFiles = [];
            
            foreach ($files as $filePath) {
                $fileName = basename($filePath);
                
                // Hanya hapus file yang belum ada dalam database (file temporary)
                // dengan pattern: SK_Pegawai_Kontrak_YYYY_XXX_YYYYYY.pdf (bukan yang TTE)
                if (preg_match('/^SK_Pegawai_Kontrak_\d{4}_\d+_\d+\.pdf$/', $fileName)) {
                    
                    // Cek apakah file sudah berumur lebih dari 24 jam
                    $fileLastModified = Storage::disk('hrd_files')->lastModified($filePath);
                    $hoursOld = (time() - $fileLastModified) / 3600;
                    
                    if ($hoursOld > 24) {
                        // Cek apakah file sudah ada record di database dengan path_dokumen yang berbeda
                        $fileNameParts = explode('_', str_replace('.pdf', '', $fileName));
                        if (count($fileNameParts) >= 5) {
                            $tahun = $fileNameParts[3];
                            $urut = $fileNameParts[4];
                            
                            $hasSignedVersion = DB::table('hrd_sk_pegawai_kontrak')
                                ->where('tahun_sk', $tahun)
                                ->where('urut', $urut)
                                ->whereNotNull('path_dokumen')
                                ->where('path_dokumen', '!=', $filePath)
                                ->exists();
                                
                            if ($hasSignedVersion) {
                                Storage::disk('hrd_files')->delete($filePath);
                                $deletedCount++;
                                $deletedFiles[] = $fileName;
                                
                                Log::info('File temporary SK dihapus (cleanup)', [
                                    'file_path' => $filePath,
                                    'file_name' => $fileName,
                                    'hours_old' => round($hoursOld, 2)
                                ]);
                            }
                        }
                    }
                }
            }
            
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => "Cleanup selesai. {$deletedCount} file temporary dihapus.",
                'deleted_count' => $deletedCount,
                'deleted_files' => $deletedFiles
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saat cleanup temporary files: ' . $e->getMessage());
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Error saat cleanup: ' . $e->getMessage()
            ], 500);
        }
    }
}
 
