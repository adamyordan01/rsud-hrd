<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\EmployeeProfileService;

class KaryawanBelumLengkapController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }

    public function index(Request $request)
    {
        $titleBreadcrumb = 'Data Pegawai Belum Lengkap';

        if (request()->ajax()) {
            try {
                // Query menggunakan view_tampil_karyawan yang sudah mencakup semua join yang diperlukan
                $karyawan = DB::connection('sqlsrv')
                    ->table('view_tampil_karyawan as v')
                    ->select([
                        'v.kd_karyawan',
                        'v.gelar_depan',
                        'v.nama',
                        'v.gelar_belakang',
                        'v.tempat_lahir',
                        'v.tgl_lahir',
                        'v.nip_baru',
                        'v.no_karpeg',
                        'v.kd_jenis_kelamin',
                        'v.alamat',
                        'v.email',
                        'v.rek_bni_syariah',
                        'v.rek_bpd_aceh',
                        'v.tgl_keluar_pensiun as tmt_kerja', // Menggunakan alias karena tidak ada tmt_kerja
                        'v.foto',
                        'v.no_ktp',
                        'v.ruangan as departemen',
                        'v.detail_jenis_tenaga as sub_jenis_tenaga',
                        'v.jurusan',
                        'v.status_rmh',
                        'v.status_kerja', // Tambahkan status kerja untuk cek PNS/PPPK
                        // Gabungkan alamat lengkap
                        DB::raw("CONCAT(COALESCE(v.kelurahan, ''), ', ', COALESCE(v.kecamatan, ''), ', ', COALESCE(v.kabupaten, ''), ', ', COALESCE(v.propinsi, '')) as alamat_lengkap")
                    ])
                    ->where('v.status_peg', '1')
                    ->where(function($query) {
                        // Kondisi untuk data yang belum lengkap
                        $query->whereNull('v.nama')
                              ->orWhere('v.nama', '')
                              ->orWhereNull('v.tempat_lahir')
                              ->orWhere('v.tempat_lahir', '')
                              ->orWhereNull('v.tgl_lahir')
                              ->orWhereNull('v.no_ktp')
                              ->orWhere('v.no_ktp', '')
                              ->orWhereNull('v.alamat')
                              ->orWhere('v.alamat', '')
                              ->orWhereNull('v.email')
                              ->orWhere('v.email', '')
                              // Rekening bank: warning hanya jika KEDUA rekening kosong
                              ->orWhere(function($subQuery) {
                                  $subQuery->where(function($rekQuery) {
                                      $rekQuery->whereNull('v.rek_bni_syariah')->orWhere('v.rek_bni_syariah', '');
                                  })->where(function($rekQuery) {
                                      $rekQuery->whereNull('v.rek_bpd_aceh')->orWhere('v.rek_bpd_aceh', '');
                                  });
                              })
                              ->orWhereNull('v.jurusan')
                              ->orWhere('v.jurusan', '')
                              ->orWhereNull('v.tgl_keluar_pensiun')
                              ->orWhereNull('v.ruangan')
                              ->orWhere('v.ruangan', '')
                              ->orWhereNull('v.detail_jenis_tenaga')
                              ->orWhere('v.detail_jenis_tenaga', '')
                              ->orWhereNull('v.status_rmh')
                              ->orWhere('v.status_rmh', '');
                    });
                    // Hapus orderBy dari sini karena akan ditangani oleh DataTables

                return DataTables::of($karyawan)
                    ->order(function ($query) {
                        $query->orderBy('nama', 'asc'); // Default order dalam DataTables
                    })
                    ->addColumn('id_pegawai', function ($row) {
                        $kd_karyawan = '<span class="fw-bold text-dark" style="font-size: 12px;">' . ($row->kd_karyawan ?? 'N/A') . '</span>';
                        
                        $photo = $row->foto && $row->foto !== 'user.png' 
                            ? '<div class="symbol symbol-45px"><img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $row->foto . '" alt=""></div>'
                            : '<div class="symbol symbol-45px"><img src="https://ui-avatars.com/api/?name=' . urlencode($row->nama ?? 'User') . '&color=7F9CF5&background=EBF4FF" alt=""></div>';

                        return $kd_karyawan . '<br>' . $photo;
                    })
                    ->addColumn('nama_lengkap', function ($row) {
                        $namaLengkap = '';
                        if (!empty($row->gelar_depan)) {
                            $namaLengkap .= $row->gelar_depan . ' ';
                        }
                        $namaLengkap .= $row->nama ?? '';
                        if (!empty($row->gelar_belakang)) {
                            $namaLengkap .= ' ' . $row->gelar_belakang;
                        }
                        $namaBold = '<span style="font-size: 12px; font-weight: bold;">' . $namaLengkap . '</span>';
                        
                        // Tempat dan tanggal lahir
                        $ttl = '';
                        if ($row->tempat_lahir && $row->tgl_lahir) {
                            try {
                                $tanggal_lahir = Carbon::parse($row->tgl_lahir)->format('d-m-Y');
                                $ttl = '<br><span style="font-size: 11px; color: #7E8299;">' . $row->tempat_lahir . ', ' . $tanggal_lahir . '</span>';
                            } catch (\Exception $e) {
                                $ttl = '<br><span style="font-size: 11px; color: #F1416C;">' . ($row->tempat_lahir ?? 'Belum diisi') . '</span>';
                            }
                        } else {
                            $ttl = '<br><span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                        }

                        // NIP / No. KARPEG (hanya untuk PNS dan PPPK)
                        $nip_karpeg = '';
                        // Cek status kerja untuk menentukan apakah perlu NIP/KARPEG
                        $status_kerja = $row->status_kerja ?? '';
                        
                        if (in_array(strtoupper($status_kerja), ['PNS', 'PPPK'])) {
                            // Untuk PNS dan PPPK, wajib ada NIP atau KARPEG
                            if ($row->nip_baru) {
                                $nip_karpeg = '<br><span style="font-size: 11px; color: #5E6278;">NIP: ' . $row->nip_baru . '</span>';
                            } elseif ($row->no_karpeg) {
                                $nip_karpeg = '<br><span style="font-size: 11px; color: #5E6278;">KARPEG: ' . $row->no_karpeg . '</span>';
                            } else {
                                $nip_karpeg = '<br><span style="font-size: 11px; color: #F1416C;">NIP/KARPEG: Belum diisi</span>';
                            }
                        } else {
                            // Untuk non-PNS/PPPK (kontrak, dll), tampilkan status kerja
                            $nip_karpeg = '<br><span style="font-size: 11px; color: #5E6278;">Status: ' . ($status_kerja ?: 'Belum diisi') . '</span>';
                        }
                        
                        return $namaBold . $ttl . $nip_karpeg;
                    })
                    ->editColumn('jenis_kelamin', function($row) {
                        return ($row->kd_jenis_kelamin ?? '2') == '1' ? 'L' : 'P';
                    })
                    ->addColumn('alamat_full', function ($row) {
                        $alamat = $row->alamat ?? '';
                        $alamat_lengkap = $row->alamat_lengkap ?? '';
                        
                        if ($alamat && $alamat_lengkap) {
                            return '<span style="font-size: 11px;">' . $alamat . '<br>' . $alamat_lengkap . '</span>';
                        } elseif ($alamat) {
                            return '<span style="font-size: 11px;">' . $alamat . '</span>';
                        } else {
                            return '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                        }
                    })
                    ->addColumn('jurusan_field', function ($row) {
                        return $row->jurusan ? '<span style="font-size: 11px;">' . $row->jurusan . '</span>' : '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                    })
                    ->addColumn('tgl_aktif', function ($row) {
                        if ($row->tmt_kerja) {
                            try {
                                $tmt = Carbon::parse($row->tmt_kerja)->format('d-m-Y');
                                return '<span style="font-size: 11px;">' . $tmt . '</span>';
                            } catch (\Exception $e) {
                                return '<span style="font-size: 11px; color: #F1416C;">Format tanggal salah</span>';
                            }
                        }
                        return '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                    })
                    ->addColumn('departemen_field', function ($row) {
                        return $row->departemen ? '<span style="font-size: 11px;">' . $row->departemen . '</span>' : '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                    })
                    ->addColumn('sub_jenis_tenaga_field', function ($row) {
                        return $row->sub_jenis_tenaga ? '<span style="font-size: 11px;">' . $row->sub_jenis_tenaga . '</span>' : '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                    })
                    ->addColumn('email_field', function ($row) {
                        return $row->email ? '<span style="font-size: 11px;">' . $row->email . '</span>' : '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                    })
                    ->addColumn('rekening_bsi', function ($row) {
                        return $row->rek_bni_syariah ? '<span style="font-size: 11px;">' . $row->rek_bni_syariah . '</span>' : '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                    })
                    ->addColumn('rekening_bpd', function ($row) {
                        return $row->rek_bpd_aceh ? '<span style="font-size: 11px;">' . $row->rek_bpd_aceh . '</span>' : '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                    })
                    ->addColumn('rekening_bank', function ($row) {
                        // Gabungkan rekening BSI dan BPD - jika salah satu ada, tidak masalah
                        $rek_bsi = $row->rek_bni_syariah;
                        $rek_bpd = $row->rek_bpd_aceh;
                        
                        $rekening_info = [];
                        if ($rek_bsi) {
                            $rekening_info[] = '<span style="font-size: 11px; color: #5E6278;">BSI: ' . $rek_bsi . '</span>';
                        }
                        if ($rek_bpd) {
                            $rekening_info[] = '<span style="font-size: 11px; color: #5E6278;">BPD: ' . $rek_bpd . '</span>';
                        }
                        
                        if (empty($rekening_info)) {
                            return '<span style="font-size: 11px; color: #F1416C;">Belum ada rekening</span>';
                        }
                        
                        return implode('<br>', $rekening_info);
                    })
                    ->addColumn('status_rumah_field', function ($row) {
                        return $row->status_rmh ? '<span style="font-size: 11px;">' . $row->status_rmh . '</span>' : '<span style="font-size: 11px; color: #F1416C;">Belum diisi</span>';
                    })
                    ->addColumn('kelengkapan', function ($row) {
                        // Hitung kelengkapan dengan logika yang benar
                        $totalFields = 11; // Field penting yang dicek (kurangi 1 karena rekening dihitung sebagai 1)
                        $filledFields = 0;
                        
                        // Field individual
                        $fields = [
                            $row->nama, $row->tempat_lahir, $row->tgl_lahir, $row->alamat,
                            $row->email, $row->jurusan, $row->tmt_kerja, $row->departemen,
                            $row->sub_jenis_tenaga, $row->status_rmh
                        ];
                        
                        foreach ($fields as $field) {
                            if (!empty($field)) {
                                $filledFields++;
                            }
                        }
                        
                        // Rekening bank: OK jika salah satu ada
                        if (!empty($row->rek_bni_syariah) || !empty($row->rek_bpd_aceh)) {
                            $filledFields++;
                        }
                        
                        $persentase = ($filledFields / $totalFields) * 100;
                        $color = $persentase < 50 ? 'danger' : ($persentase < 80 ? 'warning' : 'success');
                        
                        return '<div class="d-flex align-items-center">
                                    <div class="progress h-6px w-100px bg-light-' . $color . ' me-2">
                                        <div class="progress-bar bg-' . $color . '" role="progressbar" style="width: ' . round($persentase) . '%"></div>
                                    </div>
                                    <span class="text-' . $color . ' fw-bold fs-8">' . round($persentase) . '%</span>
                                </div>';
                    })
                    ->addColumn('action', function ($row) {
                        return view('karyawan-belum-lengkap.columns._actions', ['karyawan' => $row]);
                    })
                    ->rawColumns(['id_pegawai', 'nama_lengkap', 'alamat_full', 'jurusan_field', 'tgl_aktif', 'departemen_field', 'sub_jenis_tenaga_field', 'email_field', 'rekening_bsi', 'rekening_bpd', 'rekening_bank', 'status_rumah_field', 'kelengkapan', 'action'])
                    ->toJson();

            } catch (\Exception $e) {
                Log::error('Error in KaryawanBelumLengkapController: ' . $e->getMessage());
                return response()->json(['error' => 'Exception Message:\n\n' . $e->getMessage()], 500);
            }
        }

        return view('karyawan-belum-lengkap.index', compact('titleBreadcrumb'));
    }
}
