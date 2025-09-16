<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PegawaiTidakAktifController extends Controller
{
    public function index(Request $request)
    {
        $titleBreadcrumb = 'Pegawai Tidak Aktif';

        if (request()->ajax()) {
            $karyawan = DB::connection('sqlsrv')
                ->table('view_tampil_karyawan as v')
                ->select([
                    'v.kd_karyawan',
                    'v.nama',
                    'v.gelar_depan',
                    'v.gelar_belakang',
                    'v.tempat_lahir',
                    'v.tgl_lahir',
                    'v.nip_baru as nip',
                    'v.no_karpeg',
                    'v.jenis_kelamin',
                    'v.pangkat',
                    'v.alias_gol as golongan',
                    'v.tmt_gol_sekarang as tmt_pangkat',
                    'v.masa_kerja_thn',
                    'v.masa_kerja_bulan',
                    'v.eselon',
                    'v.tmt_eselon',
                    'v.jenjang_didik',
                    'v.jenis_peg',
                    'v.sub_detail',
                    'v.ruangan',
                    'v.status_kerja',
                    'v.status_peg',
                    'v.rek_bni_syariah',
                    'v.rek_bpd_aceh',
                    'v.tgl_keluar_pensiun',
                    'v.status_pegawai as keterangan_keluar',
                    'v.kd_jenis_peg'
                ])
                ->whereIn('v.status_peg', [2, 3, 4, 5]); // 2=keluar, 3=pensiun, 4=tugas belajar, 5=meninggal

            // Filter berdasarkan multiple status tidak aktif
            if ($request->has('statuses') && is_array($request->statuses) && !empty($request->statuses)) {
                $karyawan->whereIn('v.status_peg', $request->statuses);
            }

            // Filter berdasarkan jenis pegawai (menggunakan kd_status_kerja)
            if ($request->has('jenis_pegawai') && !empty($request->jenis_pegawai)) {
                $jenisPegawai = $request->jenis_pegawai;
                
                if ($jenisPegawai == 'blud') {
                    // Kontrak BLUD: kd_status_kerja = 3 dan kd_jenis_peg = 2
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 2);
                } elseif ($jenisPegawai == 'pemko') {
                    // Kontrak PEMKO: kd_status_kerja = 3 dan kd_jenis_peg = 1
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 1);
                } else {
                    // Untuk jenis lainnya menggunakan kd_status_kerja
                    $karyawan->where('v.kd_status_kerja', $jenisPegawai);
                }
            }

            return DataTables::of($karyawan)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->get('search')['value'])) {
                        $search = $request->get('search')['value'];
                        $query->where(function ($q) use ($search) {
                            $q->where('v.kd_karyawan', 'like', "%{$search}%")
                              ->orWhere('v.nama', 'like', "%{$search}%")
                              ->orWhere('v.nip_baru', 'like', "%{$search}%")
                              ->orWhere('v.no_karpeg', 'like', "%{$search}%");
                        });
                    }
                })
                ->addColumn('id_pegawai', function ($row) {
                    return sprintf('%06d', $row->kd_karyawan);
                })
                ->addColumn('nama_lengkap', function ($row) {
                    $gelarDepan = !empty($row->gelar_depan) ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = !empty($row->gelar_belakang) ? ', ' . $row->gelar_belakang : '';
                    $nama = $gelarDepan . $row->nama . $gelarBelakang;
                    
                    // Format tempat tanggal lahir
                    $ttl = '';
                    if (!empty($row->tempat_lahir) || !empty($row->tgl_lahir)) {
                        $tempat = $row->tempat_lahir ?: '-';
                        $tanggal = '';
                        if (!empty($row->tgl_lahir)) {
                            try {
                                $tanggal = date('d-m-Y', strtotime($row->tgl_lahir));
                            } catch (\Exception $e) {
                                $tanggal = '-';
                            }
                        } else {
                            $tanggal = '-';
                        }
                        $ttl = $tempat . ', ' . $tanggal;
                    }

                    // Format NIP/Karpeg berdasarkan jenis pegawai
                    $nipKarpeg = '';
                    if (in_array($row->kd_jenis_peg, [1, 7])) { // PNS = 1, PPPK = 7
                        $nipKarpeg = !empty($row->nip) ? $row->nip : (!empty($row->no_karpeg) ? $row->no_karpeg : '-');
                    } else {
                        // Untuk KONTRAK, HONOR, PART TIME, THL tidak perlu NIP/Karpeg
                        $nipKarpeg = '-';
                    }

                    return '<div class="d-flex flex-column">' .
                        '<div class="text-gray-800 text-hover-primary mb-1 fw-bold">' . $nama . '</div>' .
                        '<span class="text-muted fw-semibold d-block fs-7">' . $ttl . '</span>' .
                        '<span class="text-muted fw-semibold d-block fs-7">' . $nipKarpeg . '</span>' .
                        '</div>';
                })
                ->addColumn('jenis_kelamin', function ($row) {
                    return $row->jenis_kelamin == 'Pria' ? 'L' : 'P';
                })
                ->addColumn('golongan', function ($row) {
                    $golongan = !empty($row->pangkat) ? $row->pangkat : '-';
                    
                    $tmt = '';
                    if (!empty($row->tmt_pangkat)) {
                        try {
                            $tmt = date('d-m-Y', strtotime($row->tmt_pangkat));
                        } catch (\Exception $e) {
                            $tmt = '-';
                        }
                    } else {
                        $tmt = '-';
                    }

                    return '<div class="d-flex flex-column">' .
                        '<div class="text-gray-800 mb-1 fw-bold">' . $golongan . '</div>' .
                        '<span class="text-muted fw-semibold d-block fs-7">' . $tmt . '</span>' .
                        '</div>';
                })
                ->addColumn('masa_kerja_thn', function ($row) {
                    return $row->masa_kerja_thn ?: '0';
                })
                ->addColumn('masa_kerja_bulan', function ($row) {
                    return $row->masa_kerja_bulan ?: '0';
                })
                ->addColumn('eselon', function ($row) {
                    $eselon = !empty($row->eselon) ? $row->eselon : '-';
                    
                    $tmt = '';
                    if (!empty($row->tmt_eselon)) {
                        try {
                            $tmt = date('d-m-Y', strtotime($row->tmt_eselon));
                        } catch (\Exception $e) {
                            $tmt = '-';
                        }
                    } else {
                        $tmt = '-';
                    }

                    if ($eselon == '-' && $tmt == '-') {
                        return '-';
                    }

                    return '<div class="d-flex flex-column">' .
                        '<div class="text-gray-800 mb-1 fw-bold">' . $eselon . '</div>' .
                        '<span class="text-muted fw-semibold d-block fs-7">' . $tmt . '</span>' .
                        '</div>';
                })
                ->addColumn('pendidikan', function ($row) {
                    return !empty($row->jenjang_didik) ? $row->jenjang_didik : '-';
                })
                ->addColumn('sub_detail', function ($row) {
                    $subDetail = !empty($row->sub_detail) ? $row->sub_detail : '-';
                    $ruangan = !empty($row->ruangan) ? $row->ruangan : '-';

                    return '<div class="d-flex flex-column">' .
                        '<div class="text-gray-800 mb-1 fw-bold">' . $subDetail . '</div>' .
                        '<span class="text-muted fw-semibold d-block fs-7">' . $ruangan . '</span>' .
                        '</div>';
                })
                ->addColumn('status_kerja', function ($row) {
                    $badgeClass = 'badge-light-secondary';
                    $statusText = $row->status_kerja ?: '-';
                    
                    // Warna badge berdasarkan status tidak aktif
                    switch ($row->status_peg) {
                        case 2: // Keluar
                            $badgeClass = 'badge-light-warning';
                            break;
                        case 3: // Pensiun
                            $badgeClass = 'badge-light-info';
                            break;
                        case 4: // Tugas Belajar
                            $badgeClass = 'badge-light-primary';
                            break;
                        case 5: // Meninggal
                            $badgeClass = 'badge-light-dark';
                            break;
                        default:
                            $badgeClass = 'badge-light-secondary';
                    }

                    return '<span class="badge ' . $badgeClass . ' fw-bold">' . $statusText . '</span>';
                })
                ->addColumn('tanggal_keluar', function ($row) {
                    if (!empty($row->tgl_keluar_pensiun)) {
                        try {
                            return date('d-m-Y', strtotime($row->tgl_keluar_pensiun));
                        } catch (\Exception $e) {
                            return '-';
                        }
                    }
                    return '-';
                })
                ->addColumn('rekening_bank', function ($row) {
                    $bsi = !empty($row->rek_bni_syariah) ? $row->rek_bni_syariah : '';
                    $bpd = !empty($row->rek_bpd_aceh) ? $row->rek_bpd_aceh : '';
                    
                    if (!empty($bsi) && !empty($bpd)) {
                        return '<div class="d-flex flex-column">' .
                            '<span class="text-gray-800 fw-bold">BSI: ' . $bsi . '</span>' .
                            '<span class="text-muted fw-semibold fs-7">BPD: ' . $bpd . '</span>' .
                            '</div>';
                    } elseif (!empty($bsi)) {
                        return '<span class="text-gray-800 fw-bold">BSI: ' . $bsi . '</span>';
                    } elseif (!empty($bpd)) {
                        return '<span class="text-gray-800 fw-bold">BPD: ' . $bpd . '</span>';
                    } else {
                        return '<span class="text-muted">-</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex justify-content-center flex-shrink-0">
                        <a href="' . route('admin.karyawan.show', $row->kd_karyawan) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Lihat Detail">
                            <i class="ki-outline ki-eye fs-2"></i>
                        </a>
                        <a href="' . route('admin.karyawan.edit', $row->kd_karyawan) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Edit">
                            <i class="ki-outline ki-pencil fs-2"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['nama_lengkap', 'golongan', 'eselon', 'sub_detail', 'status_kerja', 'rekening_bank', 'action'])
                ->make(true);
        }

        // Hitung statistik untuk dashboard
        $statistics = [
            'total' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->whereIn('status_peg', [2, 3, 4, 5])->count(),
            'keluar' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->where('status_peg', 2)->count(),
            'pensiun' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->where('status_peg', 3)->count(),
            'tugas_belajar' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->where('status_peg', 4)->count(),
            'meninggal' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->where('status_peg', 5)->count(),
        ];

        return view('pegawai-tidak-aktif.index', compact('titleBreadcrumb', 'statistics'));
    }

    public function pensiun(Request $request)
    {
        $titleBreadcrumb = 'Pegawai Pensiun';

        if (request()->ajax()) {
            $karyawan = $this->getKaryawanByStatus([3]); // Status pensiun = 3

            // Filter berdasarkan jenis pegawai (menggunakan kd_status_kerja)
            if ($request->has('jenis_pegawai') && !empty($request->jenis_pegawai)) {
                $jenisPegawai = $request->jenis_pegawai;
                
                if ($jenisPegawai == 'blud') {
                    // Kontrak BLUD: kd_status_kerja = 3 dan kd_jenis_peg = 2
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 2);
                } elseif ($jenisPegawai == 'pemko') {
                    // Kontrak PEMKO: kd_status_kerja = 3 dan kd_jenis_peg = 1
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 1);
                } else {
                    // Untuk jenis lainnya menggunakan kd_status_kerja
                    $karyawan->where('v.kd_status_kerja', $jenisPegawai);
                }
            }

            return $this->formatDataTables($karyawan);
        }

        $statistics = $this->getStatistics();
        return view('pegawai-tidak-aktif.index', compact('titleBreadcrumb', 'statistics'));
    }

    public function keluar(Request $request)
    {
        $titleBreadcrumb = 'Pegawai Keluar';

        if (request()->ajax()) {
            $karyawan = $this->getKaryawanByStatus([2]); // Status keluar = 2

            // Filter berdasarkan jenis pegawai (menggunakan kd_status_kerja)
            if ($request->has('jenis_pegawai') && !empty($request->jenis_pegawai)) {
                $jenisPegawai = $request->jenis_pegawai;
                
                if ($jenisPegawai == 'blud') {
                    // Kontrak BLUD: kd_status_kerja = 3 dan kd_jenis_peg = 2
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 2);
                } elseif ($jenisPegawai == 'pemko') {
                    // Kontrak PEMKO: kd_status_kerja = 3 dan kd_jenis_peg = 1
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 1);
                } else {
                    // Untuk jenis lainnya menggunakan kd_status_kerja
                    $karyawan->where('v.kd_status_kerja', $jenisPegawai);
                }
            }

            return $this->formatDataTables($karyawan);
        }

        $statistics = $this->getStatistics();
        return view('pegawai-tidak-aktif.index', compact('titleBreadcrumb', 'statistics'));
    }

    public function tugasBelajar(Request $request)
    {
        $titleBreadcrumb = 'Pegawai Tugas Belajar';

        if (request()->ajax()) {
            $karyawan = $this->getKaryawanByStatus([4]); // Status tugas belajar = 4

            // Filter berdasarkan jenis pegawai (menggunakan kd_status_kerja)
            if ($request->has('jenis_pegawai') && !empty($request->jenis_pegawai)) {
                $jenisPegawai = $request->jenis_pegawai;
                
                if ($jenisPegawai == 'blud') {
                    // Kontrak BLUD: kd_status_kerja = 3 dan kd_jenis_peg = 2
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 2);
                } elseif ($jenisPegawai == 'pemko') {
                    // Kontrak PEMKO: kd_status_kerja = 3 dan kd_jenis_peg = 1
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 1);
                } else {
                    // Untuk jenis lainnya menggunakan kd_status_kerja
                    $karyawan->where('v.kd_status_kerja', $jenisPegawai);
                }
            }

            return $this->formatDataTables($karyawan);
        }

        $statistics = $this->getStatistics();
        return view('pegawai-tidak-aktif.index', compact('titleBreadcrumb', 'statistics'));
    }

    public function meninggal(Request $request)
    {
        $titleBreadcrumb = 'Pegawai Meninggal Dunia';

        if (request()->ajax()) {
            $karyawan = $this->getKaryawanByStatus([5]); // Status meninggal = 5

            // Filter berdasarkan jenis pegawai (menggunakan kd_status_kerja)
            if ($request->has('jenis_pegawai') && !empty($request->jenis_pegawai)) {
                $jenisPegawai = $request->jenis_pegawai;
                
                if ($jenisPegawai == 'blud') {
                    // Kontrak BLUD: kd_status_kerja = 3 dan kd_jenis_peg = 2
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 2);
                } elseif ($jenisPegawai == 'pemko') {
                    // Kontrak PEMKO: kd_status_kerja = 3 dan kd_jenis_peg = 1
                    $karyawan->where('v.kd_status_kerja', 3)->where('v.kd_jenis_peg', 1);
                } else {
                    // Untuk jenis lainnya menggunakan kd_status_kerja
                    $karyawan->where('v.kd_status_kerja', $jenisPegawai);
                }
            }

            return $this->formatDataTables($karyawan);
        }

        $statistics = $this->getStatistics();
        return view('pegawai-tidak-aktif.index', compact('titleBreadcrumb', 'statistics'));
    }

    private function getKaryawanByStatus($statusArray)
    {
        return DB::connection('sqlsrv')
            ->table('view_tampil_karyawan as v')
            ->select([
                'v.kd_karyawan',
                'v.nama',
                'v.gelar_depan',
                'v.gelar_belakang',
                'v.tempat_lahir',
                'v.tgl_lahir',
                'v.nip_baru as nip',
                'v.no_karpeg',
                'v.jenis_kelamin',
                'v.pangkat',
                'v.alias_gol as golongan',
                'v.tmt_gol_sekarang as tmt_pangkat',
                'v.masa_kerja_thn',
                'v.masa_kerja_bulan',
                'v.eselon',
                'v.tmt_eselon',
                'v.jenjang_didik',
                'v.jenis_peg',
                'v.sub_detail',
                'v.ruangan',
                'v.status_kerja',
                'v.status_peg',
                'v.rek_bni_syariah',
                'v.rek_bpd_aceh',
                'v.tgl_keluar_pensiun',
                'v.status_pegawai as keterangan_keluar',
                'v.kd_jenis_peg'
            ])
            ->whereIn('v.status_peg', $statusArray);
    }

    private function getStatistics()
    {
        return [
            'total' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->whereIn('status_peg', [2, 3, 4, 5])->count(),
            'keluar' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->where('status_peg', 2)->count(),
            'pensiun' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->where('status_peg', 3)->count(),
            'tugas_belajar' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->where('status_peg', 4)->count(),
            'meninggal' => DB::connection('sqlsrv')->table('view_tampil_karyawan')->where('status_peg', 5)->count(),
        ];
    }

    private function formatDataTables($karyawan)
    {
        return DataTables::of($karyawan)
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request()->get('search')['value'])) {
                    $search = request()->get('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('v.kd_karyawan', 'like', "%{$search}%")
                          ->orWhere('v.nama', 'like', "%{$search}%")
                          ->orWhere('v.nip_baru', 'like', "%{$search}%")
                          ->orWhere('v.no_karpeg', 'like', "%{$search}%");
                    });
                }
            })
            ->addColumn('id_pegawai', function ($row) {
                return sprintf('%06d', $row->kd_karyawan);
            })
            ->addColumn('nama_lengkap', function ($row) {
                $gelarDepan = !empty($row->gelar_depan) ? $row->gelar_depan . ' ' : '';
                $gelarBelakang = !empty($row->gelar_belakang) ? ', ' . $row->gelar_belakang : '';
                $nama = $gelarDepan . $row->nama . $gelarBelakang;
                
                // Format tempat tanggal lahir
                $ttl = '';
                if (!empty($row->tempat_lahir) || !empty($row->tgl_lahir)) {
                    $tempat = $row->tempat_lahir ?: '-';
                    $tanggal = '';
                    if (!empty($row->tgl_lahir)) {
                        try {
                            $tanggal = date('d-m-Y', strtotime($row->tgl_lahir));
                        } catch (\Exception $e) {
                            $tanggal = '-';
                        }
                    } else {
                        $tanggal = '-';
                    }
                    $ttl = $tempat . ', ' . $tanggal;
                }

                // Format NIP/Karpeg berdasarkan jenis pegawai
                $nipKarpeg = '';
                if (in_array($row->kd_jenis_peg, [1, 7])) { // PNS = 1, PPPK = 7
                    $nipKarpeg = !empty($row->nip) ? $row->nip : (!empty($row->no_karpeg) ? $row->no_karpeg : '-');
                } else {
                    // Untuk KONTRAK, HONOR, PART TIME, THL tidak perlu NIP/Karpeg
                    $nipKarpeg = '-';
                }

                return '<div class="d-flex flex-column">' .
                    '<div class="text-gray-800 text-hover-primary mb-1 fw-bold">' . $nama . '</div>' .
                    '<span class="text-muted fw-semibold d-block fs-7">' . $ttl . '</span>' .
                    '<span class="text-muted fw-semibold d-block fs-7">' . $nipKarpeg . '</span>' .
                    '</div>';
            })
            ->addColumn('jenis_kelamin', function ($row) {
                return $row->jenis_kelamin == 'Pria' ? 'L' : 'P';
            })
            ->addColumn('golongan', function ($row) {
                $golongan = !empty($row->pangkat) ? $row->pangkat : '-';
                
                $tmt = '';
                if (!empty($row->tmt_pangkat)) {
                    try {
                        $tmt = date('d-m-Y', strtotime($row->tmt_pangkat));
                    } catch (\Exception $e) {
                        $tmt = '-';
                    }
                } else {
                    $tmt = '-';
                }

                return '<div class="d-flex flex-column">' .
                    '<div class="text-gray-800 mb-1 fw-bold">' . $golongan . '</div>' .
                    '<span class="text-muted fw-semibold d-block fs-7">' . $tmt . '</span>' .
                    '</div>';
            })
            ->addColumn('masa_kerja_thn', function ($row) {
                return $row->masa_kerja_thn ?: '0';
            })
            ->addColumn('masa_kerja_bulan', function ($row) {
                return $row->masa_kerja_bulan ?: '0';
            })
            ->addColumn('eselon', function ($row) {
                $eselon = !empty($row->eselon) ? $row->eselon : '-';
                
                $tmt = '';
                if (!empty($row->tmt_eselon)) {
                    try {
                        $tmt = date('d-m-Y', strtotime($row->tmt_eselon));
                    } catch (\Exception $e) {
                        $tmt = '-';
                    }
                } else {
                    $tmt = '-';
                }

                if ($eselon == '-' && $tmt == '-') {
                    return '-';
                }

                return '<div class="d-flex flex-column">' .
                    '<div class="text-gray-800 mb-1 fw-bold">' . $eselon . '</div>' .
                    '<span class="text-muted fw-semibold d-block fs-7">' . $tmt . '</span>' .
                    '</div>';
            })
            ->addColumn('pendidikan', function ($row) {
                return !empty($row->jenjang_didik) ? $row->jenjang_didik : '-';
            })
            ->addColumn('sub_detail', function ($row) {
                $subDetail = !empty($row->sub_detail) ? $row->sub_detail : '-';
                $ruangan = !empty($row->ruangan) ? $row->ruangan : '-';

                return '<div class="d-flex flex-column">' .
                    '<div class="text-gray-800 mb-1 fw-bold">' . $subDetail . '</div>' .
                    '<span class="text-muted fw-semibold d-block fs-7">' . $ruangan . '</span>' .
                    '</div>';
            })
            ->addColumn('status_kerja', function ($row) {
                $badgeClass = 'badge-light-secondary';
                $statusText = $row->status_kerja ?: '-';
                
                // Warna badge berdasarkan status tidak aktif
                switch ($row->status_peg) {
                    case 2: // Keluar
                        $badgeClass = 'badge-light-warning';
                        break;
                    case 3: // Pensiun
                        $badgeClass = 'badge-light-info';
                        break;
                    case 4: // Tugas Belajar
                        $badgeClass = 'badge-light-primary';
                        break;
                    case 5: // Meninggal
                        $badgeClass = 'badge-light-dark';
                        break;
                    default:
                        $badgeClass = 'badge-light-secondary';
                }

                return '<span class="badge ' . $badgeClass . ' fw-bold">' . $statusText . '</span>';
            })
            ->addColumn('tanggal_keluar', function ($row) {
                if (!empty($row->tgl_keluar_pensiun)) {
                    try {
                        return date('d-m-Y', strtotime($row->tgl_keluar_pensiun));
                    } catch (\Exception $e) {
                        return '-';
                    }
                }
                return '-';
            })
            ->addColumn('rekening_bank', function ($row) {
                $bsi = !empty($row->rek_bni_syariah) ? $row->rek_bni_syariah : '';
                $bpd = !empty($row->rek_bpd_aceh) ? $row->rek_bpd_aceh : '';
                
                if (!empty($bsi) && !empty($bpd)) {
                    return '<div class="d-flex flex-column">' .
                        '<span class="text-gray-800 fw-bold">BSI: ' . $bsi . '</span>' .
                        '<span class="text-muted fw-semibold fs-7">BPD: ' . $bpd . '</span>' .
                        '</div>';
                } elseif (!empty($bsi)) {
                    return '<span class="text-gray-800 fw-bold">BSI: ' . $bsi . '</span>';
                } elseif (!empty($bpd)) {
                    return '<span class="text-gray-800 fw-bold">BPD: ' . $bpd . '</span>';
                } else {
                    return '<span class="text-muted">-</span>';
                }
            })
            ->addColumn('action', function ($row) {
                return '<div class="d-flex justify-content-center flex-shrink-0">
                    <a href="' . route('admin.karyawan.show', $row->kd_karyawan) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Lihat Detail">
                        <i class="ki-outline ki-eye fs-2"></i>
                    </a>
                    <a href="' . route('admin.karyawan.edit', $row->kd_karyawan) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Edit">
                        <i class="ki-outline ki-pencil fs-2"></i>
                    </a>
                </div>';
            })
            ->rawColumns(['nama_lengkap', 'golongan', 'eselon', 'sub_detail', 'status_kerja', 'rekening_bank', 'action'])
            ->make(true);
    }

    public function cetakLaporan($status)
    {
        // Map status dari URL ke status_peg database
        $statusMapping = [
            'keluar' => 2,
            'pensiun' => 3,
            'tugas-belajar' => 4,
            'meninggal' => 5
        ];

        $statusPeg = $statusMapping[$status] ?? 2;

        // Query data pegawai tidak aktif
        $karyawan = DB::connection('sqlsrv')
            ->table('view_tampil_karyawan')
            ->where('status_peg', $statusPeg)
            ->orderBy('ruangan', 'ASC')
            ->orderBy('nilaiindex', 'DESC')
            ->orderBy('tahun_lulus', 'ASC')
            ->orderBy('nama', 'ASC')
            ->get();

        // Map status untuk judul
        $judulMapping = [
            'keluar' => 'DATA PEGAWAI KELUAR',
            'pensiun' => 'DATA PEGAWAI PENSIUN',
            'tugas-belajar' => 'DATA PEGAWAI TUGAS BELAJAR',
            'meninggal' => 'DATA PEGAWAI MENINGGAL'
        ];

        $judul = $judulMapping[$status] ?? 'DATA PEGAWAI TIDAK AKTIF';

        return view('pegawai-tidak-aktif.cetak', compact('karyawan', 'judul', 'status'));
    }
}
