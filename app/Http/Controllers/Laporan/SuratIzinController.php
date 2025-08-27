<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Mpdf\Mpdf;

class SuratIzinController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kdRuangan = $request->kd_ruangan;
            $kdKategori = $request->kd_kategori;
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            
            // Query utama dengan JOIN 5 tabel sesuai legacy
            $query = DB::connection('sqlsrv')
                ->table('HRD_SURAT_IZIN as si')
                ->leftJoin('HRD_JENIS_SURAT as js', 'si.kd_jenis_surat', '=', 'js.kd_jenis_surat')
                ->leftJoin('HRD_KATEGORI_IZIN as ki', 'si.kd_kategori', '=', 'ki.kd_kategori')
                ->leftJoin('HRD_KARYAWAN as k', 'si.kd_karyawan', '=', 'k.kd_karyawan')
                ->leftJoin('HRD_RUANGAN as r', 'k.kd_ruangan', '=', 'r.kd_ruangan')
                ->select([
                    'si.*',
                    'js.jenis_surat as nm_jenis_surat',
                    'ki.kategori as nm_kategori_izin',
                    'k.nama as nama_karyawan',
                    'k.nip_baru',
                    'k.no_karpeg',
                    'r.ruangan',
                    DB::raw('CASE 
                        WHEN r.ruangan IS NOT NULL THEN r.ruangan 
                        ELSE CAST(k.kd_ruangan AS VARCHAR(50)) 
                    END as unit_name')
                ]);

            // Filter berdasarkan ruangan jika dipilih
            if (!empty($kdRuangan) && $kdRuangan != '') {
                $query->where('k.kd_ruangan', $kdRuangan);
            }

            // Filter berdasarkan kategori jika dipilih
            if (!empty($kdKategori) && $kdKategori != '') {
                $query->where('si.kd_kategori', $kdKategori);
            }

            // Filter berdasarkan bulan dan tahun jika dipilih
            if (!empty($bulan) && $bulan != '') {
                $query->whereRaw('MONTH(si.tgl_mulai) = ?', [$bulan]);

        if (!empty($tahun) && $tahun != "") {
            $query->whereRaw("YEAR(si.tgl_mulai) = ?", [$tahun]);
        }
            }

            if (!empty($tahun) && $tahun != '') {
                $query->whereRaw('YEAR(si.tgl_mulai) = ?', [$tahun]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function ($row) {
                    return $row->nama_karyawan ?? '-';
                })
                ->addColumn('nip_display', function ($row) {
                    return $row->nip_baru ?? ($row->no_karpeg ?? '-');
                })
                ->addColumn('unit_kerja', function ($row) {
                    return $row->unit_name ?? '-';
                })
                // ->addColumn('tanggal_surat', function ($row) {
                //     return $row->tgl_surat ? Carbon::parse($row->tgl_surat)->format('d/m/Y') : '-';
                // })
                ->addColumn('periode_izin', function ($row) {
                    $tglMulai = $row->tgl_mulai ? Carbon::parse($row->tgl_mulai)->format('d/m/Y') : '';
                    $tglSelesai = $row->tgl_akhir ? Carbon::parse($row->tgl_akhir)->format('d/m/Y') : '';
                    
                    if ($tglMulai && $tglSelesai) {
                        return $tglMulai . ' s/d ' . $tglSelesai;
                    } elseif ($tglMulai) {
                        return $tglMulai;
                    }
                    return '-';
                })
                ->addColumn('lama_izin', function ($row) {
                    if ($row->tgl_mulai && $row->tgl_akhir) {
                        $startDate = Carbon::parse($row->tgl_mulai);
                        $endDate = Carbon::parse($row->tgl_akhir);
                        $days = $startDate->diffInDays($endDate) + 1; // +1 karena termasuk hari pertama
                        return $days . ' hari';
                    }
                    return '-';
                })
                ->addColumn('jenis_surat', function ($row) {
                    return $row->nm_jenis_surat ?? '-';
                })
                ->addColumn('kategori_izin', function ($row) {
                    return $row->nm_kategori_izin ?? '-';
                })
                ->addColumn('alasan', function ($row) {
                    return $row->alasan ?? '-';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value']) {
                        $query->where(function($q) use ($search) {
                            $q->where('k.nama', 'LIKE', '%' . $search . '%')
                              ->orWhere('k.nip_baru', 'LIKE', '%' . $search . '%')
                              ->orWhere('k.no_karpeg', 'LIKE', '%' . $search . '%')
                            //   ->orWhere('si.no_surat', 'LIKE', '%' . $search . '%')
                              ->orWhere('js.jenis_surat', 'LIKE', '%' . $search . '%')
                              ->orWhere('ki.kategori', 'LIKE', '%' . $search . '%')
                              ->orWhere('si.alasan', 'LIKE', '%' . $search . '%');
                        });
                    }
                })
                ->order(function ($query) {
                    $query->orderBy('si.tgl_mulai', 'DESC')
                          ->orderBy('k.nama', 'ASC');
                })
                ->make(true);
        }

        // Load data untuk dropdown
        $ruangan = DB::connection('sqlsrv')
            ->table('HRD_RUANGAN')
            ->orderBy('ruangan', 'ASC')
            ->get();

        $kategoriIzin = DB::connection('sqlsrv')
            ->table('HRD_KATEGORI_IZIN')
            ->orderBy('kategori', 'ASC')
            ->get();

        return view('laporan.surat-izin.index', compact('ruangan', 'kategoriIzin'));
    }
    
    public function print(Request $request)
    {
        $kdRuangan = $request->kd_ruangan;
        $kdKategori = $request->kd_kategori;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        // Array nama bulan
        $dataBulan = [
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
            '4' => 'April', '5' => 'Mei', '6' => 'Juni',
            '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Query data sesuai filter
        $query = DB::connection('sqlsrv')
            ->table('HRD_SURAT_IZIN as si')
            ->leftJoin('HRD_JENIS_SURAT as js', 'si.kd_jenis_surat', '=', 'js.kd_jenis_surat')
            ->leftJoin('HRD_KATEGORI_IZIN as ki', 'si.kd_kategori', '=', 'ki.kd_kategori')
            ->leftJoin('HRD_KARYAWAN as k', 'si.kd_karyawan', '=', 'k.kd_karyawan')
            ->leftJoin('HRD_RUANGAN as r', 'k.kd_ruangan', '=', 'r.kd_ruangan')
            ->select([
                'si.*',
                'js.jenis_surat as nm_jenis_surat',
                'ki.kategori as nm_kategori_izin',
                'k.nama as nama_karyawan',
                'k.nip_baru',
                'k.no_karpeg',
                'r.ruangan',
                DB::raw('COALESCE(r.ruangan, CAST(k.kd_ruangan AS VARCHAR(10))) as unit_name')
            ]);

        // Apply filters
        if (!empty($kdRuangan) && $kdRuangan != '') {
            $query->where('k.kd_ruangan', $kdRuangan);
        }

        if (!empty($kdKategori) && $kdKategori != '') {
            $query->where('si.kd_kategori', $kdKategori);
        }

        if (!empty($bulan) && $bulan != '') {
            $query->whereRaw('MONTH(si.tgl_mulai) = ?', [$bulan]);

        if (!empty($tahun) && $tahun != "") {
            $query->whereRaw("YEAR(si.tgl_mulai) = ?", [$tahun]);
        }
        }

        $data = $query->orderBy('si.tgl_mulai', 'DESC')
                     ->orderBy('k.nama', 'ASC')
                     ->get();

        // Get filter info for display
        $filterInfo = [
            'ruangan_name' => '',
            'kategori_name' => '',
            'bulan_name' => '',
            'tahun_name' => $tahun ?? ''
        ];

        if (!empty($kdRuangan)) {
            $ruanganData = DB::connection('sqlsrv')
                ->table('HRD_RUANGAN')
                ->where('kd_ruangan', $kdRuangan)
                ->first();
            $filterInfo['ruangan_name'] = $ruanganData->ruangan ?? '';
        }

        if (!empty($kdKategori)) {
            $kategoriData = DB::connection('sqlsrv')
                ->table('HRD_KATEGORI_IZIN')
                ->where('kd_kategori', $kdKategori)
                ->first();
            $filterInfo['kategori_name'] = $kategoriData->kategori ?? '';
        }

        if (!empty($bulan)) {
            $filterInfo['bulan_name'] = $dataBulan[$bulan] ?? '';
        }

        return view('laporan.surat-izin.print', compact('data', 'filterInfo', 'kdRuangan', 'kdKategori', 'bulan', 'tahun'));
    }
    
    public function pdf(Request $request)
    {
        $kdRuangan = $request->kd_ruangan;
        $kdKategori = $request->kd_kategori;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        // Array nama bulan
        $dataBulan = [
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
            '4' => 'April', '5' => 'Mei', '6' => 'Juni',
            '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Query data sesuai filter
        $query = DB::connection('sqlsrv')
            ->table('HRD_SURAT_IZIN as si')
            ->leftJoin('HRD_JENIS_SURAT as js', 'si.kd_jenis_surat', '=', 'js.kd_jenis_surat')
            ->leftJoin('HRD_KATEGORI_IZIN as ki', 'si.kd_kategori', '=', 'ki.kd_kategori')
            ->leftJoin('HRD_KARYAWAN as k', 'si.kd_karyawan', '=', 'k.kd_karyawan')
            ->leftJoin('HRD_RUANGAN as r', 'k.kd_ruangan', '=', 'r.kd_ruangan')
            ->select([
                'si.*',
                'js.jenis_surat as nm_jenis_surat',
                'ki.kategori as nm_kategori_izin',
                'k.nama as nama_karyawan',
                'k.nip_baru',
                'k.no_karpeg',
                'r.ruangan',
                DB::raw('COALESCE(r.ruangan, CAST(k.kd_ruangan AS VARCHAR(10))) as unit_name')
            ]);

        // Apply filters
        if (!empty($kdRuangan) && $kdRuangan != '') {
            $query->where('k.kd_ruangan', $kdRuangan);
        }

        if (!empty($kdKategori) && $kdKategori != '') {
            $query->where('si.kd_kategori', $kdKategori);
        }

        if (!empty($bulan) && $bulan != '') {
            $query->whereRaw('MONTH(si.tgl_mulai) = ?', [$bulan]);

        if (!empty($tahun) && $tahun != "") {
            $query->whereRaw("YEAR(si.tgl_mulai) = ?", [$tahun]);
        }
        }

        $data = $query->orderBy('si.tgl_mulai', 'DESC')
                     ->orderBy('k.nama', 'ASC')
                     ->get();

        // Get filter info for display
        $filterInfo = [
            'ruangan_name' => '',
            'kategori_name' => '',
            'bulan_name' => '',
            'tahun_name' => $tahun ?? ''
        ];

        if (!empty($kdRuangan)) {
            $ruanganData = DB::connection('sqlsrv')
                ->table('HRD_RUANGAN')
                ->where('kd_ruangan', $kdRuangan)
                ->first();
            $filterInfo['ruangan_name'] = $ruanganData->ruangan ?? '';
        }

        if (!empty($kdKategori)) {
            $kategoriData = DB::connection('sqlsrv')
                ->table('HRD_KATEGORI_IZIN')
                ->where('kd_kategori', $kdKategori)
                ->first();
            $filterInfo['kategori_name'] = $kategoriData->kategori ?? '';
        }

        if (!empty($bulan)) {
            $filterInfo['bulan_name'] = $dataBulan[$bulan] ?? '';
        }

        // Generate HTML content for PDF
        $html = view('laporan.surat-izin.pdf-template', compact('data', 'filterInfo', 'kdRuangan', 'kdKategori', 'bulan'))->render();

        // Initialize mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape orientation
            'orientation' => 'L',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
            'default_font_size' => 9,
            'default_font' => 'Arial'
        ]);

        $mpdf->SetTitle('Laporan Surat Izin');
        $mpdf->SetAuthor(config('app.name'));
        $mpdf->SetCreator(config('app.name'));
        $mpdf->SetSubject('Laporan Surat Izin Karyawan');

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Generate filename
        $filename = 'Laporan_Surat_Izin_' . date('Y-m-d_H-i-s');
        if ($filterInfo['ruangan_name']) {
            $filename .= '_' . str_replace(' ', '_', $filterInfo['ruangan_name']);
        }
        if ($filterInfo['kategori_name']) {
            $filename .= '_' . str_replace(' ', '_', $filterInfo['kategori_name']);
        }
        if ($filterInfo['bulan_name']) {
            $filename .= '_' . $filterInfo['bulan_name'];
        }
        $filename .= '.pdf';

        // Output PDF
        return response($mpdf->Output($filename, 'D'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    public function checkData(Request $request)
    {
        $kdRuangan = $request->kd_ruangan;
        $kdKategori = $request->kd_kategori;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        try {
            $query = DB::connection('sqlsrv')
                ->table('HRD_SURAT_IZIN as si')
                ->leftJoin('HRD_KARYAWAN as k', 'si.kd_karyawan', '=', 'k.kd_karyawan');

            // Apply filters
            if (!empty($kdRuangan) && $kdRuangan != '') {
                $query->where('k.kd_ruangan', $kdRuangan);
            }

            if (!empty($kdKategori) && $kdKategori != '') {
                $query->where('si.kd_kategori', $kdKategori);
            }

            if (!empty($bulan) && $bulan != '') {
                $query->whereRaw('MONTH(si.tgl_mulai) = ?', [$bulan]);
            }

            if (!empty($tahun) && $tahun != '') {
                $query->whereRaw('YEAR(si.tgl_mulai) = ?', [$tahun]);
            }

            $count = $query->count();

            return response()->json([
                'status' => 'success',
                'data_count' => $count,
                'message' => $count > 0 ? "Ditemukan {$count} data surat izin" : 'Tidak ada data ditemukan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRuangan()
    {
        try {
            $ruangan = DB::connection('sqlsrv')
                ->table('HRD_RUANGAN')
                ->select('kd_ruangan', 'ruangan')
                ->orderBy('ruangan', 'ASC')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $ruangan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKategoriIzin()
    {
        try {
            $kategori = DB::connection('sqlsrv')
                ->table('HRD_KATEGORI_IZIN')
                ->select('kd_kategori', 'kategori')
                ->orderBy('kategori', 'ASC')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $kategori
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
