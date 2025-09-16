<?php

namespace App\Http\Controllers\Export;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TestExportController extends Controller
{
    public function index()
    {
        // Simplified version for debugging
        $data = [];
        
        try {
            // Test basic queries
            $data['total_aktif'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', 1)
                ->count();
                
            $data['pns'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', 1)
                ->where('kd_status_kerja', 1)
                ->count();
                
            $data['honor'] = DB::connection('sqlsrv')
                ->table('hrd_karyawan')
                ->where('status_peg', 1)
                ->where('kd_status_kerja', 2)
                ->count();
                
            // Set other values to 0 for now
            $data['duk'] = $data['pns'];
            $data['kontrak_blud'] = 0;
            $data['kontrak_pemko'] = 0;
            $data['part_time'] = 0;
            $data['pppk'] = 0;
            $data['thl'] = 0;
            $data['tenaga_medis'] = 0;
            $data['perawat_bidan'] = 0;
            $data['penunjang_medis'] = 0;
            $data['non_kesehatan'] = 0;
            $data['pegawai_keluar'] = 0;
            $data['pegawai_pensiun'] = 0;
            $data['pegawai_tubel'] = 0;
            $data['bni_syariah_kontrak'] = 0;
            $data['bni_syariah_pns'] = 0;
            
            Log::info('Export data computed successfully', $data);
            
        } catch (\Exception $e) {
            Log::error('Error in index method: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Set all to 0 on error
            $data = [
                'pns' => 0, 'duk' => 0, 'honor' => 0, 'kontrak_blud' => 0, 'kontrak_pemko' => 0,
                'part_time' => 0, 'pppk' => 0, 'thl' => 0, 'total_aktif' => 0, 'tenaga_medis' => 0,
                'perawat_bidan' => 0, 'penunjang_medis' => 0, 'non_kesehatan' => 0, 'pegawai_keluar' => 0,
                'pegawai_pensiun' => 0, 'pegawai_tubel' => 0, 'bni_syariah_kontrak' => 0, 'bni_syariah_pns' => 0
            ];
        }
        
        return view('exports.index', ['exportData' => $data]);
    }
}
