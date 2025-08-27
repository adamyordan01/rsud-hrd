<?php

namespace App\Http\Controllers\Laporan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PerPendidikanRuanganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $kdRuangan = $request->kd_ruangan; // * = semua ruangan, kd_ruangan = ruangan spesifik
            
            // Tentukan apakah menggunakan data current atau backup
            $isCurrentPeriod = ($bulan == date('m') && $tahun == date('Y'));
            $tableName = $isCurrentPeriod ? 'view_karyawan_perpendidikan_perruangan' : 'view_karyawan_perpendidikan_perruangan_backup';
            
            $result = [];
            $grandTotalPNS = $grandTotalPartTime = $grandTotalHonor = $grandTotalKontrak = $grandTotalLK = $grandTotalPR = 0;
            
            // Query ruangan berdasarkan filter
            $ruanganQuery = DB::connection('sqlsrv')
                ->table('hrd_ruangan')
                ->where('status_aktif', 1)
                ->orderBy('ruangan');
                
            if ($kdRuangan !== '*') {
                $ruanganQuery->where('kd_ruangan', $kdRuangan);
            }
            
            $ruanganList = $ruanganQuery->get();
            
            foreach ($ruanganList as $ruangan) {
                // Query jenis tenaga per ruangan
                $jenisTenagaList = DB::connection('sqlsrv')
                    ->table('hrd_jenis_tenaga')
                    ->orderBy('kd_jenis_tenaga')
                    ->get();
                
                $ruanganData = [
                    'ruangan' => $ruangan->ruangan,
                    'kd_ruangan' => $ruangan->kd_ruangan,
                    'jenis_tenaga_groups' => [],
                    'ruangan_total' => [
                        'pns' => 0,
                        'part_time' => 0,
                        'honor' => 0,
                        'kontrak' => 0,
                        'lk' => 0,
                        'pr' => 0
                    ]
                ];
                
                $hasDataInRuangan = false;
                
                foreach ($jenisTenagaList as $jenisTenaga) {
                    // Query data per jenis tenaga di ruangan ini
                    $query = DB::connection('sqlsrv')->table($tableName)
                        ->where('kd_jenis_tenaga', $jenisTenaga->kd_jenis_tenaga)
                        ->where('kd_ruangan', $ruangan->kd_ruangan);
                    
                    // Jika menggunakan data backup, tambahkan filter bulan dan tahun
                    if (!$isCurrentPeriod) {
                        $query->where('bulan_backup', $bulan)
                              ->where('tahun_backup', $tahun);
                    }
                    
                    $dataDetail = $query->orderBy('ruangan', 'asc')
                                      ->orderBy('kd_jenis_tenaga', 'asc')
                                      ->orderBy('nilaiindex', 'desc')
                                      ->orderBy('jurusan', 'asc')
                                      ->get();
                    
                    if ($dataDetail->count() > 0) {
                        $hasDataInRuangan = true;
                        
                        $jenisTenagaData = [
                            'jenis_tenaga' => $jenisTenaga->jenis_tenaga,
                            'kd_jenis_tenaga' => $jenisTenaga->kd_jenis_tenaga,
                            'details' => [],
                            'subtotal' => [
                                'pns' => 0,
                                'part_time' => 0,
                                'honor' => 0,
                                'kontrak' => 0,
                                'lk' => 0,
                                'pr' => 0
                            ]
                        ];
                        
                        foreach ($dataDetail as $item) {
                            $jenisTenagaData['details'][] = [
                                'jenjang_didik' => $item->jenjang_didik,
                                'jurusan' => $item->jurusan,
                                'pns' => $item->pns,
                                'part_time' => $item->part_time,
                                'honor' => $item->honor,
                                'kontrak' => $item->kontrak,
                                'lk' => $item->lk,
                                'pr' => $item->pr
                            ];
                            
                            // Akumulasi subtotal per jenis tenaga
                            $jenisTenagaData['subtotal']['pns'] += $item->pns;
                            $jenisTenagaData['subtotal']['part_time'] += $item->part_time;
                            $jenisTenagaData['subtotal']['honor'] += $item->honor;
                            $jenisTenagaData['subtotal']['kontrak'] += $item->kontrak;
                            $jenisTenagaData['subtotal']['lk'] += $item->lk;
                            $jenisTenagaData['subtotal']['pr'] += $item->pr;
                        }
                        
                        // Akumulasi total per ruangan
                        $ruanganData['ruangan_total']['pns'] += $jenisTenagaData['subtotal']['pns'];
                        $ruanganData['ruangan_total']['part_time'] += $jenisTenagaData['subtotal']['part_time'];
                        $ruanganData['ruangan_total']['honor'] += $jenisTenagaData['subtotal']['honor'];
                        $ruanganData['ruangan_total']['kontrak'] += $jenisTenagaData['subtotal']['kontrak'];
                        $ruanganData['ruangan_total']['lk'] += $jenisTenagaData['subtotal']['lk'];
                        $ruanganData['ruangan_total']['pr'] += $jenisTenagaData['subtotal']['pr'];
                        
                        $ruanganData['jenis_tenaga_groups'][] = $jenisTenagaData;
                    }
                }
                
                // Hanya tambahkan ruangan jika ada data
                if ($hasDataInRuangan) {
                    // Akumulasi grand total
                    $grandTotalPNS += $ruanganData['ruangan_total']['pns'];
                    $grandTotalPartTime += $ruanganData['ruangan_total']['part_time'];
                    $grandTotalHonor += $ruanganData['ruangan_total']['honor'];
                    $grandTotalKontrak += $ruanganData['ruangan_total']['kontrak'];
                    $grandTotalLK += $ruanganData['ruangan_total']['lk'];
                    $grandTotalPR += $ruanganData['ruangan_total']['pr'];
                    
                    $result[] = $ruanganData;
                }
            }
            
            return response()->json([
                'data' => $result,
                'grand_total' => [
                    'pns' => $grandTotalPNS,
                    'part_time' => $grandTotalPartTime,
                    'honor' => $grandTotalHonor,
                    'kontrak' => $grandTotalKontrak,
                    'lk' => $grandTotalLK,
                    'pr' => $grandTotalPR
                ]
            ]);
        }
        
        // Ambil data ruangan untuk dropdown
        $ruanganList = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->where('status_aktif', 1)
            ->orderBy('ruangan')
            ->get();
        
        return view('laporan.per-pendidikan-ruangan.index', compact('ruanganList'));
    }
    
    public function print(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kdRuangan = $request->kd_ruangan;
        
        // Validasi input
        if (empty($bulan) || empty($tahun)) {
            return redirect()->back()->with('error', 'Bulan dan tahun harus dipilih!');
        }
        
        // Tentukan apakah menggunakan data current atau backup
        $isCurrentPeriod = ($bulan == date('m') && $tahun == date('Y'));
        $tableName = $isCurrentPeriod ? 'view_karyawan_perpendidikan_perruangan' : 'view_karyawan_perpendidikan_perruangan_backup';
        
        $result = [];
        $grandTotalPNS = $grandTotalPartTime = $grandTotalHonor = $grandTotalKontrak = $grandTotalLK = $grandTotalPR = 0;
        
        // Query ruangan berdasarkan filter
        $ruanganQuery = DB::connection('sqlsrv')
            ->table('hrd_ruangan')
            ->where('status_aktif', 1)
            ->orderBy('ruangan');
            
        if ($kdRuangan !== '*') {
            $ruanganQuery->where('kd_ruangan', $kdRuangan);
        }
        
        $ruanganList = $ruanganQuery->get();
        
        foreach ($ruanganList as $ruangan) {
            // Query jenis tenaga per ruangan
            $jenisTenagaList = DB::connection('sqlsrv')
                ->table('hrd_jenis_tenaga')
                ->orderBy('kd_jenis_tenaga')
                ->get();
            
            $ruanganData = [
                'ruangan' => $ruangan->ruangan,
                'kd_ruangan' => $ruangan->kd_ruangan,
                'jenis_tenaga_groups' => [],
                'ruangan_total' => [
                    'pns' => 0,
                    'part_time' => 0,
                    'honor' => 0,
                    'kontrak' => 0,
                    'lk' => 0,
                    'pr' => 0
                ]
            ];
            
            $hasDataInRuangan = false;
            
            foreach ($jenisTenagaList as $jenisTenaga) {
                // Query data per jenis tenaga di ruangan ini
                $query = DB::connection('sqlsrv')->table($tableName)
                    ->where('kd_jenis_tenaga', $jenisTenaga->kd_jenis_tenaga)
                    ->where('kd_ruangan', $ruangan->kd_ruangan);
                
                // Jika menggunakan data backup, tambahkan filter bulan dan tahun
                if (!$isCurrentPeriod) {
                    $query->where('bulan_backup', $bulan)
                          ->where('tahun_backup', $tahun);
                }
                
                $dataDetail = $query->orderBy('ruangan', 'asc')
                                  ->orderBy('kd_jenis_tenaga', 'asc')
                                  ->orderBy('nilaiindex', 'desc')
                                  ->orderBy('jurusan', 'asc')
                                  ->get();
                
                if ($dataDetail->count() > 0) {
                    $hasDataInRuangan = true;
                    
                    $jenisTenagaData = [
                        'jenis_tenaga' => $jenisTenaga->jenis_tenaga,
                        'kd_jenis_tenaga' => $jenisTenaga->kd_jenis_tenaga,
                        'details' => [],
                        'subtotal' => [
                            'pns' => 0,
                            'part_time' => 0,
                            'honor' => 0,
                            'kontrak' => 0,
                            'lk' => 0,
                            'pr' => 0
                        ]
                    ];
                    
                    foreach ($dataDetail as $item) {
                        $jenisTenagaData['details'][] = [
                            'jenjang_didik' => $item->jenjang_didik,
                            'jurusan' => $item->jurusan,
                            'pns' => $item->pns,
                            'part_time' => $item->part_time,
                            'honor' => $item->honor,
                            'kontrak' => $item->kontrak,
                            'lk' => $item->lk,
                            'pr' => $item->pr
                        ];
                        
                        // Akumulasi subtotal per jenis tenaga
                        $jenisTenagaData['subtotal']['pns'] += $item->pns;
                        $jenisTenagaData['subtotal']['part_time'] += $item->part_time;
                        $jenisTenagaData['subtotal']['honor'] += $item->honor;
                        $jenisTenagaData['subtotal']['kontrak'] += $item->kontrak;
                        $jenisTenagaData['subtotal']['lk'] += $item->lk;
                        $jenisTenagaData['subtotal']['pr'] += $item->pr;
                    }
                    
                    // Akumulasi total per ruangan
                    $ruanganData['ruangan_total']['pns'] += $jenisTenagaData['subtotal']['pns'];
                    $ruanganData['ruangan_total']['part_time'] += $jenisTenagaData['subtotal']['part_time'];
                    $ruanganData['ruangan_total']['honor'] += $jenisTenagaData['subtotal']['honor'];
                    $ruanganData['ruangan_total']['kontrak'] += $jenisTenagaData['subtotal']['kontrak'];
                    $ruanganData['ruangan_total']['lk'] += $jenisTenagaData['subtotal']['lk'];
                    $ruanganData['ruangan_total']['pr'] += $jenisTenagaData['subtotal']['pr'];
                    
                    $ruanganData['jenis_tenaga_groups'][] = $jenisTenagaData;
                }
            }
            
            // Hanya tambahkan ruangan jika ada data
            if ($hasDataInRuangan) {
                // Akumulasi grand total
                $grandTotalPNS += $ruanganData['ruangan_total']['pns'];
                $grandTotalPartTime += $ruanganData['ruangan_total']['part_time'];
                $grandTotalHonor += $ruanganData['ruangan_total']['honor'];
                $grandTotalKontrak += $ruanganData['ruangan_total']['kontrak'];
                $grandTotalLK += $ruanganData['ruangan_total']['lk'];
                $grandTotalPR += $ruanganData['ruangan_total']['pr'];
                
                $result[] = $ruanganData;
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
            'pns' => $grandTotalPNS,
            'part_time' => $grandTotalPartTime,
            'honor' => $grandTotalHonor,
            'kontrak' => $grandTotalKontrak,
            'lk' => $grandTotalLK,
            'pr' => $grandTotalPR
        ];
        
        return view('laporan.per-pendidikan-ruangan.print', compact('result', 'grandTotal', 'periodeName', 'bulan', 'tahun', 'kdRuangan'));
    }
}
