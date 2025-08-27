<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PerJenisTenagaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            
            // Tentukan apakah menggunakan data current atau backup
            $isCurrentPeriod = ($bulan == date('m') && $tahun == date('Y'));
            $tableName = $isCurrentPeriod ? 'view_karyawan_perjenistenaga' : 'view_karyawan_perjenistenaga_backup';
            
            $result = [];
            $grandTotalPNS = $grandTotalPPPK = $grandTotalPartTime = $grandTotalKontrakDaerah = $grandTotalKontrakBlud = $grandTotalTHL = 0;
            $grandTotalLK = $grandTotalPR = 0;
            
            // Query jenis tenaga
            $jenisTenagaList = DB::connection('sqlsrv')
                ->table('hrd_jenis_tenaga')
                ->orderBy('kd_jenis_tenaga')
                ->get();
            
            foreach ($jenisTenagaList as $jenisTenaga) {
                // Query data per jenis tenaga
                $query = DB::connection('sqlsrv')->table($tableName)
                    ->where('kd_jenis_tenaga', $jenisTenaga->kd_jenis_tenaga);
                
                // Jika menggunakan data backup, tambahkan filter bulan dan tahun
                if (!$isCurrentPeriod) {
                    $query->where('bulan_backup', $bulan)
                          ->where('tahun_backup', $tahun);
                }
                
                $dataDetail = $query->orderBy('sub_detail')->get();
                
                if ($dataDetail->count() > 0) {
                    $jenisTenagaData = [
                        'jenis_tenaga' => $jenisTenaga->jenis_tenaga,
                        'kd_jenis_tenaga' => $jenisTenaga->kd_jenis_tenaga,
                        'details' => [],
                        'subtotal' => [
                            'pns' => 0,
                            'pppk' => 0,
                            'part_time' => 0,
                            'kontrak_daerah' => 0,
                            'kontrak_blud' => 0,
                            'thl' => 0,
                            'lk' => 0,
                            'pr' => 0
                        ]
                    ];
                    
                    foreach ($dataDetail as $item) {
                        $detailData = [
                            'sub_detail' => $item->sub_detail,
                            'pns' => (int)$item->pns,
                            'pppk' => (int)$item->pppk,
                            'part_time' => (int)$item->part_time,
                            'kontrak_daerah' => (int)$item->kontrak_daerah,
                            'kontrak_blud' => (int)$item->kontrak_blud,
                            'thl' => (int)($item->thl ?? 0),
                            'lk' => (int)$item->lk,
                            'pr' => (int)$item->pr
                        ];
                        
                        $jenisTenagaData['details'][] = $detailData;
                        
                        // Akumulasi subtotal per jenis tenaga
                        $jenisTenagaData['subtotal']['pns'] += $detailData['pns'];
                        $jenisTenagaData['subtotal']['pppk'] += $detailData['pppk'];
                        $jenisTenagaData['subtotal']['part_time'] += $detailData['part_time'];
                        $jenisTenagaData['subtotal']['kontrak_daerah'] += $detailData['kontrak_daerah'];
                        $jenisTenagaData['subtotal']['kontrak_blud'] += $detailData['kontrak_blud'];
                        $jenisTenagaData['subtotal']['thl'] += $detailData['thl'];
                        $jenisTenagaData['subtotal']['lk'] += $detailData['lk'];
                        $jenisTenagaData['subtotal']['pr'] += $detailData['pr'];
                    }
                    
                    // Akumulasi grand total
                    $grandTotalPNS += $jenisTenagaData['subtotal']['pns'];
                    $grandTotalPPPK += $jenisTenagaData['subtotal']['pppk'];
                    $grandTotalPartTime += $jenisTenagaData['subtotal']['part_time'];
                    $grandTotalKontrakDaerah += $jenisTenagaData['subtotal']['kontrak_daerah'];
                    $grandTotalKontrakBlud += $jenisTenagaData['subtotal']['kontrak_blud'];
                    $grandTotalTHL += $jenisTenagaData['subtotal']['thl'];
                    $grandTotalLK += $jenisTenagaData['subtotal']['lk'];
                    $grandTotalPR += $jenisTenagaData['subtotal']['pr'];
                    
                    $result[] = $jenisTenagaData;
                }
            }
            
            // Hitung grand total gabungan
            $grandTotalStatus = $grandTotalPNS + $grandTotalPPPK + $grandTotalPartTime + $grandTotalKontrakDaerah + $grandTotalKontrakBlud + $grandTotalTHL;
            $grandTotalGender = $grandTotalLK + $grandTotalPR;
            
            return response()->json([
                'data' => $result,
                'grand_total' => [
                    'pns' => $grandTotalPNS,
                    'pppk' => $grandTotalPPPK,
                    'part_time' => $grandTotalPartTime,
                    'kontrak_daerah' => $grandTotalKontrakDaerah,
                    'kontrak_blud' => $grandTotalKontrakBlud,
                    'thl' => $grandTotalTHL,
                    'lk' => $grandTotalLK,
                    'pr' => $grandTotalPR,
                    'total_status' => $grandTotalStatus,
                    'total_gender' => $grandTotalGender
                ]
            ]);
        }
        
        return view('laporan.per-jenis-tenaga.index');
    }
    
    public function print(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        // Validasi input
        if (empty($bulan) || empty($tahun)) {
            return redirect()->back()->with('error', 'Bulan dan tahun harus dipilih!');
        }
        
        // Tentukan apakah menggunakan data current atau backup
        $isCurrentPeriod = ($bulan == date('m') && $tahun == date('Y'));
        $tableName = $isCurrentPeriod ? 'view_karyawan_perjenistenaga' : 'view_karyawan_perjenistenaga_backup';
        
        $result = [];
        $grandTotalPNS = $grandTotalPPPK = $grandTotalPartTime = $grandTotalKontrakDaerah = $grandTotalKontrakBlud = $grandTotalTHL = 0;
        $grandTotalLK = $grandTotalPR = 0;
        
        // Query jenis tenaga
        $jenisTenagaList = DB::connection('sqlsrv')
            ->table('hrd_jenis_tenaga')
            ->orderBy('kd_jenis_tenaga')
            ->get();
        
        foreach ($jenisTenagaList as $jenisTenaga) {
            // Query data per jenis tenaga
            $query = DB::connection('sqlsrv')->table($tableName)
                ->where('kd_jenis_tenaga', $jenisTenaga->kd_jenis_tenaga);
            
            // Jika menggunakan data backup, tambahkan filter bulan dan tahun
            if (!$isCurrentPeriod) {
                $query->where('bulan_backup', $bulan)
                      ->where('tahun_backup', $tahun);
            }
            
            $dataDetail = $query->orderBy('sub_detail')->get();
            
            if ($dataDetail->count() > 0) {
                $jenisTenagaData = [
                    'jenis_tenaga' => $jenisTenaga->jenis_tenaga,
                    'kd_jenis_tenaga' => $jenisTenaga->kd_jenis_tenaga,
                    'details' => [],
                    'subtotal' => [
                        'pns' => 0,
                        'pppk' => 0,
                        'part_time' => 0,
                        'kontrak_daerah' => 0,
                        'kontrak_blud' => 0,
                        'thl' => 0,
                        'lk' => 0,
                        'pr' => 0
                    ]
                ];
                
                foreach ($dataDetail as $item) {
                    $detailData = [
                        'sub_detail' => $item->sub_detail,
                        'pns' => (int)$item->pns,
                        'pppk' => (int)$item->pppk,
                        'part_time' => (int)$item->part_time,
                        'kontrak_daerah' => (int)$item->kontrak_daerah,
                        'kontrak_blud' => (int)$item->kontrak_blud,
                        'thl' => (int)($item->thl ?? 0),
                        'lk' => (int)$item->lk,
                        'pr' => (int)$item->pr
                    ];
                    
                    $jenisTenagaData['details'][] = $detailData;
                    
                    // Akumulasi subtotal per jenis tenaga
                    $jenisTenagaData['subtotal']['pns'] += $detailData['pns'];
                    $jenisTenagaData['subtotal']['pppk'] += $detailData['pppk'];
                    $jenisTenagaData['subtotal']['part_time'] += $detailData['part_time'];
                    $jenisTenagaData['subtotal']['kontrak_daerah'] += $detailData['kontrak_daerah'];
                    $jenisTenagaData['subtotal']['kontrak_blud'] += $detailData['kontrak_blud'];
                    $jenisTenagaData['subtotal']['thl'] += $detailData['thl'];
                    $jenisTenagaData['subtotal']['lk'] += $detailData['lk'];
                    $jenisTenagaData['subtotal']['pr'] += $detailData['pr'];
                }
                
                // Akumulasi grand total
                $grandTotalPNS += $jenisTenagaData['subtotal']['pns'];
                $grandTotalPPPK += $jenisTenagaData['subtotal']['pppk'];
                $grandTotalPartTime += $jenisTenagaData['subtotal']['part_time'];
                $grandTotalKontrakDaerah += $jenisTenagaData['subtotal']['kontrak_daerah'];
                $grandTotalKontrakBlud += $jenisTenagaData['subtotal']['kontrak_blud'];
                $grandTotalTHL += $jenisTenagaData['subtotal']['thl'];
                $grandTotalLK += $jenisTenagaData['subtotal']['lk'];
                $grandTotalPR += $jenisTenagaData['subtotal']['pr'];
                
                $result[] = $jenisTenagaData;
            }
        }
        
        // Format nama periode untuk header
        $dataBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        $periodeName = $dataBulan[$bulan] . ' ' . $tahun;
        
        // Hitung grand total gabungan
        $grandTotalStatus = $grandTotalPNS + $grandTotalPPPK + $grandTotalPartTime + $grandTotalKontrakDaerah + $grandTotalKontrakBlud + $grandTotalTHL;
        $grandTotalGender = $grandTotalLK + $grandTotalPR;
        
        $grandTotal = [
            'pns' => $grandTotalPNS,
            'pppk' => $grandTotalPPPK,
            'part_time' => $grandTotalPartTime,
            'kontrak_daerah' => $grandTotalKontrakDaerah,
            'kontrak_blud' => $grandTotalKontrakBlud,
            'thl' => $grandTotalTHL,
            'lk' => $grandTotalLK,
            'pr' => $grandTotalPR,
            'total_status' => $grandTotalStatus,
            'total_gender' => $grandTotalGender
        ];
        
        return view('laporan.per-jenis-tenaga.print', compact('result', 'grandTotal', 'periodeName', 'bulan', 'tahun'));
    }
}
