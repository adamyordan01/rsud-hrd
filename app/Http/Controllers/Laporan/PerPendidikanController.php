<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PerPendidikanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            
            // Tentukan apakah menggunakan data current atau backup
            $isCurrentPeriod = ($bulan == date('m') && $tahun == date('Y'));
            $tableName = $isCurrentPeriod ? 'view_karyawan_perpendidikan' : 'view_karyawan_perpendidikan_backup';
            
            // Ambil data jenis tenaga sebagai grouping utama
            $jenisTenagaList = DB::connection('sqlsrv')
                ->table('hrd_jenis_tenaga')
                ->orderBy('kd_jenis_tenaga')
                ->get();
            
            $result = [];
            $totalPNS = $totalPPPK = $totalPartTime = $totalHonor = $totalKontrak = $totalLK = $totalPR = 0;
            
            foreach ($jenisTenagaList as $jenisTenaga) {
                // Query untuk mendapatkan data pegawai per pendidikan berdasarkan jenis tenaga
                $query = DB::connection('sqlsrv')->table($tableName)
                    ->where('kd_jenis_tenaga', $jenisTenaga->kd_jenis_tenaga);
                
                // Jika menggunakan data backup, tambahkan filter bulan dan tahun
                if (!$isCurrentPeriod) {
                    $query->where('bulan_backup', $bulan)
                          ->where('tahun_backup', $tahun);
                }
                
            $dataDetail = $query->orderBy('kd_jenis_tenaga', 'asc')
                              ->orderBy('nilaiindex', 'desc')
                              ->orderBy('jurusan', 'asc')
                              ->get();
                              
            if ($dataDetail->count() > 0) {
                    $groupData = [
                        'jenis_tenaga' => $jenisTenaga->jenis_tenaga,
                        'kd_jenis_tenaga' => $jenisTenaga->kd_jenis_tenaga,
                        'details' => [],
                        'subtotal' => [
                            'pns' => 0,
                            'pppk' => 0,
                            'part_time' => 0,
                            'honor' => 0,
                            'kontrak' => 0,
                            'lk' => 0,
                            'pr' => 0
                        ]
                    ];
                    
                    foreach ($dataDetail as $item) {
                        $groupData['details'][] = [
                            'jenjang_didik' => $item->jenjang_didik,
                            'jurusan' => $item->jurusan,
                            'pns' => $item->pns,
                            'pppk' => $item->pppk,
                            'part_time' => $item->part_time,
                            'honor' => $item->honor,
                            'kontrak' => $item->kontrak,
                            'lk' => $item->lk,
                            'pr' => $item->pr
                        ];
                        
                        // Akumulasi subtotal per jenis tenaga
                        $groupData['subtotal']['pns'] += $item->pns;
                        $groupData['subtotal']['pppk'] += $item->pppk;
                        $groupData['subtotal']['part_time'] += $item->part_time;
                        $groupData['subtotal']['honor'] += $item->honor;
                        $groupData['subtotal']['kontrak'] += $item->kontrak;
                        $groupData['subtotal']['lk'] += $item->lk;
                        $groupData['subtotal']['pr'] += $item->pr;
                    }
                    
                    // Akumulasi grand total
                    $totalPNS += $groupData['subtotal']['pns'];
                    $totalPPPK += $groupData['subtotal']['pppk'];
                    $totalPartTime += $groupData['subtotal']['part_time'];
                    $totalHonor += $groupData['subtotal']['honor'];
                    $totalKontrak += $groupData['subtotal']['kontrak'];
                    $totalLK += $groupData['subtotal']['lk'];
                    $totalPR += $groupData['subtotal']['pr'];
                    
                    $result[] = $groupData;
                }
            }
            
            return response()->json([
                'data' => $result,
                'grand_total' => [
                    'pns' => $totalPNS,
                    'pppk' => $totalPPPK,
                    'part_time' => $totalPartTime,
                    'honor' => $totalHonor,
                    'kontrak' => $totalKontrak,
                    'lk' => $totalLK,
                    'pr' => $totalPR
                ]
            ]);
        }
        
        return view('laporan.per-pendidikan.index');
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
        $tableName = $isCurrentPeriod ? 'view_karyawan_perpendidikan' : 'view_karyawan_perpendidikan_backup';
        
        // Ambil data jenis tenaga sebagai grouping utama
        $jenisTenagaList = DB::connection('sqlsrv')
            ->table('hrd_jenis_tenaga')
            ->orderBy('kd_jenis_tenaga')
            ->get();
        
        $result = [];
        $totalPNS = $totalPPPK = $totalPartTime = $totalHonor = $totalKontrak = $totalLK = $totalPR = 0;
        
        foreach ($jenisTenagaList as $jenisTenaga) {
            // Query untuk mendapatkan data pegawai per pendidikan berdasarkan jenis tenaga
            $query = DB::connection('sqlsrv')->table($tableName)
                ->where('kd_jenis_tenaga', $jenisTenaga->kd_jenis_tenaga);
            
            // Jika menggunakan data backup, tambahkan filter bulan dan tahun
            if (!$isCurrentPeriod) {
                $query->where('bulan_backup', $bulan)
                      ->where('tahun_backup', $tahun);
            }
            
            $dataDetail = $query->orderBy('kd_jenis_tenaga', 'asc')
                              ->orderBy('nilaiindex', 'desc')
                              ->orderBy('jurusan', 'asc')
                              ->get();
            
            if ($dataDetail->count() > 0) {
                $groupData = [
                    'jenis_tenaga' => $jenisTenaga->jenis_tenaga,
                    'kd_jenis_tenaga' => $jenisTenaga->kd_jenis_tenaga,
                    'details' => [],
                    'subtotal' => [
                        'pns' => 0,
                        'pppk' => 0,
                        'part_time' => 0,
                        'honor' => 0,
                        'kontrak' => 0,
                        'lk' => 0,
                        'pr' => 0
                    ]
                ];
                
                foreach ($dataDetail as $item) {
                    $groupData['details'][] = [
                        'jenjang_didik' => $item->jenjang_didik,
                        'jurusan' => $item->jurusan,
                        'pns' => $item->pns,
                        'pppk' => $item->pppk,
                        'part_time' => $item->part_time,
                        'honor' => $item->honor,
                        'kontrak' => $item->kontrak,
                        'lk' => $item->lk,
                        'pr' => $item->pr
                    ];
                    
                    // Akumulasi subtotal per jenis tenaga
                    $groupData['subtotal']['pns'] += $item->pns;
                    $groupData['subtotal']['pppk'] += $item->pppk;
                    $groupData['subtotal']['part_time'] += $item->part_time;
                    $groupData['subtotal']['honor'] += $item->honor;
                    $groupData['subtotal']['kontrak'] += $item->kontrak;
                    $groupData['subtotal']['lk'] += $item->lk;
                    $groupData['subtotal']['pr'] += $item->pr;
                }
                
                // Akumulasi grand total
                $totalPNS += $groupData['subtotal']['pns'];
                $totalPPPK += $groupData['subtotal']['pppk'];
                $totalPartTime += $groupData['subtotal']['part_time'];
                $totalHonor += $groupData['subtotal']['honor'];
                $totalKontrak += $groupData['subtotal']['kontrak'];
                $totalLK += $groupData['subtotal']['lk'];
                $totalPR += $groupData['subtotal']['pr'];
                
                $result[] = $groupData;
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
        
        $grandTotal = [
            'pns' => $totalPNS,
            'pppk' => $totalPPPK,
            'part_time' => $totalPartTime,
            'honor' => $totalHonor,
            'kontrak' => $totalKontrak,
            'lk' => $totalLK,
            'pr' => $totalPR
        ];
        
        return view('laporan.per-pendidikan.print', compact('result', 'grandTotal', 'periodeName', 'bulan', 'tahun'));
    }
}
