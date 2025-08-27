<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SatyaLencanaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $result = $this->getSatyaLencanaData();
                
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } catch (\Exception $e) {
                Log::error('Error in SatyaLencanaController::index: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memuat data'
                ], 500);
            }
        }
        
        return view('laporan.satya-lencana.index');
    }
    
    public function print(Request $request)
    {
        try {
            $result = $this->getSatyaLencanaData();
            
            // Ambil data direktur untuk tanda tangan
            $direktur = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan')
                ->where('kd_jabatan_struktural', 1)
                ->where('kd_status_kerja', 1)
                ->first();
            
            return view('laporan.satya-lencana.print', compact('result', 'direktur'));
        } catch (\Exception $e) {
            Log::error('Error in SatyaLencanaController::print: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mencetak laporan');
        }
    }
    
    private function getSatyaLencanaData()
    {
        $categories = [
            ['min_years' => 30, 'max_years' => null, 'title' => 'XXX (30 TAHUN)'],
            ['min_years' => 20, 'max_years' => 29, 'title' => 'XX (20 TAHUN)'],
            ['min_years' => 10, 'max_years' => 19, 'title' => 'X (10 TAHUN)']
        ];
        
        $result = [];
        
        foreach ($categories as $category) {
            $categoryData = [
                'title' => $category['title'],
                'jenis_tenaga_groups' => []
            ];
            
            // Query untuk mendapatkan jenis tenaga yang memiliki pegawai dalam kategori ini
            $jenisTenagaQuery = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan as k')
                ->select('k.kd_jenis_tenaga', 'jt.jenis_tenaga')
                ->join('hrd_jenis_tenaga as jt', 'k.kd_jenis_tenaga', '=', 'jt.kd_jenis_tenaga')
                ->where('k.kd_status_kerja', 1)
                ->where('k.masa_kerja_thn', '>=', $category['min_years']);
            
            if ($category['max_years'] !== null) {
                $jenisTenagaQuery->where('k.masa_kerja_thn', '<=', $category['max_years']);
            }
            
            $jenisTenagaList = $jenisTenagaQuery
                ->groupBy('k.kd_jenis_tenaga', 'jt.jenis_tenaga')
                ->orderBy('k.kd_jenis_tenaga')
                ->get();
            
            foreach ($jenisTenagaList as $jenisTenaga) {
                // Query detail pegawai per jenis tenaga dengan kolom yang dioptimasi
                $pegawaiQuery = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan')
                    ->select([
                        'nip_lama',
                        'nip_baru', 
                        'gelar_depan',
                        'nama',
                        'gelar_belakang',
                        'kd_gol_sekarang',
                        'masa_kerja_thn',
                        'masa_kerja_bulan',
                        'jenjang_didik',
                        'jurusan',
                        'tahun_lulus'
                    ])
                    ->where('kd_jenis_tenaga', $jenisTenaga->kd_jenis_tenaga)
                    ->where('kd_status_kerja', 1)
                    ->where('masa_kerja_thn', '>=', $category['min_years']);
                
                if ($category['max_years'] !== null) {
                    $pegawaiQuery->where('masa_kerja_thn', '<=', $category['max_years']);
                }
                
                $pegawaiList = $pegawaiQuery
                    ->orderBy('masa_kerja_thn', 'desc')
                    ->orderBy('masa_kerja_bulan', 'desc')
                    ->orderBy('kd_gol_sekarang', 'desc')
                    ->get();
                
                if ($pegawaiList->count() > 0) {
                    $categoryData['jenis_tenaga_groups'][] = [
                        'jenis_tenaga' => $jenisTenaga->jenis_tenaga,
                        'pegawai_list' => $pegawaiList
                    ];
                }
            }
            
            // Hanya tambahkan kategori jika ada data
            if (!empty($categoryData['jenis_tenaga_groups'])) {
                $result[] = $categoryData;
            }
        }
        
        return $result;
    }
}
