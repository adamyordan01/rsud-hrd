<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        CV-{{ $karyawan->nama ?? 'Karyawan' }}
    </title>
    <meta charset="utf-8" />
    <meta name="description" content="HRD - RSUD LANGSA" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Aplikasi HRD - RSUD Langsa" />
    <meta property="og:url" content="https://e-rsud.langsakota.go.id/rsud_hrd/" />
    <meta property="og:site_name" content="RSUD LANGSA" />
    <link rel="canonical" href="https://e-rsud.langsakota.go.id/rsud_hrd/login" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        /* Print Styles untuk A4 dan F4 Portrait */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0.5in;
                @bottom-right {
                    content: "RSUD Langsa";
                    font-size: 10px;
                    color: #999999;
                    font-family: Inter, sans-serif;
                    margin-right: 20px;
                    margin-bottom: 10px;
                }
            }
            
            /* Alternative untuk F4 - uncomment jika diperlukan */
            /*
            @page {
                size: 210mm 330mm; /* F4 size 
                margin: 0.5in;
                @bottom-right {
                    content: "RSUD Langsa";
                    font-size: 10px;
                    color: #999999;
                    font-family: Inter, sans-serif;
                    margin-right: 20px;
                    margin-bottom: 10px;
                }
            }
            */
            
            /* Remove all borders and outlines */
            * {
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
            }
            
            /* Force horizontal layout pada print */
            .row.cv-print-layout {
                display: flex !important;
                flex-direction: row !important;
                border: none !important;
            }
            
            .col-sidebar {
                flex: 0 0 25% !important;
                max-width: 25% !important;
                width: 25% !important;
                display: block !important;
                border: none !important;
                border-right: none !important;
            }
            
            .col-content {
                flex: 0 0 75% !important;
                max-width: 75% !important; 
                width: 75% !important;
                display: block !important;
                border: none !important;
                border-left: none !important;
            }
            
            /* Adjust font sizes for print */
            body { font-size: 12px !important; }
            .fs-6 { font-size: 11px !important; }
            .fs-2 { font-size: 18px !important; }
            h4 { font-size: 14px !important; }
            
            /* Photo adjustments */
            .w-150px { width: 120px !important; }
            
            /* Table adjustments */
            table { font-size: 11px !important; }
            
            /* Remove all background colors and ensure white background */
            body { 
                background: white !important; 
                background-color: white !important;
            }
            
            .bg-light { 
                background-color: #f8f9fa !important; 
                border: none !important;
            }
            
            /* Remove any container backgrounds */
            .app-root, .app-page, .app-wrapper, .app-main, .app-content {
                background: transparent !important;
                background-color: transparent !important;
            }
            
            /* Ensure content fits */
            .container-fluid { 
                padding: 0 !important; 
                background: transparent !important;
            }
            .p-5 { padding: 1rem !important; }
            
            /* Page breaks */
            .page-break { page-break-before: always; }
            
            /* Hide unnecessary elements */
            .d-print-none { display: none !important; }
            
            /* Remove any footer or bottom elements */
            footer, .footer { display: none !important; }
            
            /* Ensure no extra spacing at bottom */
            .mb-5 { margin-bottom: 0 !important; }
            
            /* Remove any form elements or buttons */
            button, input, select, textarea { display: none !important; }
            
            /* Remove any navigation or header elements */
            nav, .navbar, .nav { display: none !important; }
            
            /* Improve table layout and remove borders */
            table {
                border: none !important;
                border-collapse: collapse !important;
            }
            
            table tr, table td, table th {
                border: none !important;
                border-left: none !important;
                border-right: none !important;
                border-top: none !important;
                border-bottom: none !important;
            }
            
            table tr td:first-child {
                font-weight: bold;
                width: 40%;
                border: none !important;
            }
            
            table tr td:last-child {
                width: 60%;
                border: none !important;
            }
            
            /* Better spacing for sections */
            .d-flex.align-items-start {
                margin-bottom: 15px !important;
            }
            
            /* Improve hr styling - make them very light or remove */
            hr {
                margin: 8px 0 !important;
                border: none !important;
                border-top: 1px solid #e9ecef !important;
                background: none !important;
            }
            
            /* Alternative: completely hide hr if not needed */
            /* hr { display: none !important; } */
        }
        
        /* Screen styles - untuk preview sebelum print */
        @media screen {
            .cv-print-layout {
                display: flex;
                flex-wrap: nowrap;
            }
            
            .col-sidebar {
                flex: 0 0 25%;
                max-width: 25%;
            }
            
            .col-content {
                flex: 0 0 75%;
                max-width: 75%;
            }
        }
    </style>
</head>
<body>
    @inject('carbon', 'Carbon\Carbon')

    @php
        $kd_status_kerja = $karyawan->kd_status_kerja;

        $gelar_depan = $karyawan->gelar_depan ? $karyawan->gelar_depan . " " : "";
        $gelar_belakang = $karyawan->gelar_belakang ? $karyawan->gelar_belakang : "";
        $nama = $karyawan->nama;
        $nama_lengkap = $gelar_depan . $nama . $gelar_belakang;

        // <?php echo $isi['ALAMAT'].", Kel. ".$isi['KELURAHAN'].", <br>Kec. ".$isi['KECAMATAN'].", Kab./Kota ".$isi['KABUPATEN'].", Prov. ".$isi['PROPINSI'];

        $alamat = $karyawan->alamat . ", Kel. " . $karyawan->kelurahan . ", Kec. " . $karyawan->kecamatan . ", Kab./Kota " . $karyawan->kabupaten . ", Prov. " . $karyawan->propinsi;
    @endphp
    
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page  flex-column flex-column-fluid " id="kt_app_page">
            <div class="app-wrapper  flex-column flex-row-fluid " id="kt_app_wrapper">
                <div class="app-main flex-column flex-row-fluid " id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content  flex-column-fluid ">
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                <div class="row g-0 mb-5 cv-print-layout">
                                    <div class="col-sidebar p-5 bg-light">
                                        <div class="text-center pb-5">
                                            @php
                                                // Menggunakan PhotoHelper untuk konsistensi
                                                $photoUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto');
                                                $hasValidPhoto = PhotoHelper::hasPhoto($karyawan, 'foto');
                                            @endphp
                                            
                                            <img
                                                src="{{ $photoUrl }}"
                                                alt="{{ $hasValidPhoto ? $nama_lengkap : 'Default Avatar' }}"
                                                class="w-150px rounded"
                                            />
                                        </div>
                                        <div class="mt-5">
                                            <div class="fw-bold fs-6">ID. Pegawai</div>
                                            <div class="fs-6">
                                                {{ $karyawan->kd_karyawan }}
                                            </div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. KTP</div>
                                            <div class="fs-6">{{ $karyawan->no_ktp }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        @if ($kd_status_kerja == 1)
                                            <div class="mt-3">
                                                <div class="fw-bold fs-6">NIP</div>
                                                <div class="fs-6">
                                                    {{ $karyawan->nip_baru }}
                                                </div>
                                            </div>
                                            <hr class="border-gray-600">
                                            <div class="mt-3">
                                                <div class="fw-bold fs-6">NIP Lama</div>
                                                <div class="fs-6">{{ $karyawan->nip_lama }}</div>
                                            </div>
                                            <hr class="border-gray-600">
                                        @elseif($kd_status_kerja == 7)
                                            <div class="mt-3">
                                                <div class="fw-bold fs-6">NIPPPK</div>
                                                <div class="fs-6">{{ $karyawan->nip_baru }}</div>
                                            </div>
                                            <hr class="border-gray-600">
                                        @endif
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Tempat, Tanggal Lahir</div>
                                            <div class="fs-6">
                                                {{-- Langsa, 01 Februari 1998 --}}
                                                {{ $karyawan->tempat_lahir }}, {{ $carbon->parse($karyawan->tgl_lahir)->locale('id_ID')->isoFormat('LL') }}
                                            </div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Jenis Kelamin</div>
                                            <div class="fs-6">{{ $karyawan->jenis_kelamin }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Agama</div>
                                            <div class="fs-6">{{ $karyawan->agama }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Kedudukan Pegawai</div>
                                            <div class="fs-6">{{ $karyawan->status_pegawai }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Status Pegawai</div>
                                            <div class="fs-6">{{ $karyawan->status_kerja }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Golongan Darah</div>
                                            <div class="fs-6">{{ $karyawan->goldar }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Status Nikah</div>
                                            <div class="fs-6">{{ $karyawan->status_nikah }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. NPWP</div>
                                            <div class="fs-6">{{ $karyawan->no_npwp }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        @if ($kd_status_kerja == 1 && $kd_status_kerja == 7)
                                            <div class="mt-3">
                                                <div class="fw-bold fs-6">No. Karpeg</div>
                                                <div class="fs-6">{{ $karyawan->no_karpeg }}</div>
                                            </div>
                                            <hr class="border-gray-600">
                                        @endif
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. Askes / BPJS</div>
                                            <div class="fs-6">{{ $karyawan->no_askes }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        @if ($kd_status_kerja == 1 && $kd_status_kerja == 7)
                                            <div class="mt-3">
                                                <div class="fw-bold fs-6">No. Taspen</div>
                                                <div class="fs-6">{{ $karyawan->no_taspen }}</div>
                                            </div>
                                            <hr class="border-gray-600">
                                            <div class="mt-3">
                                                <div class="fw-bold fs-6">No. KARIS/KARSU</div>
                                                <div class="fs-6">{{ $karyawan->no_karis }}</div>
                                            </div>
                                            <hr class="border-gray-600">
                                        @endif
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. KK</div>
                                            <div class="fs-6">{{ $karyawan->no_kk }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. BPJS Ketenagakerjaan</div>
                                            <div class="fs-6">{{ $karyawan->no_bpjs_ketenagakerjaan }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. Rekening BNI</div>
                                            <div class="fs-6">{{ $karyawan->rek_bni }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. Rekening BSI</div>
                                            <div class="fs-6">{{ $karyawan->rek_bsi }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. Rekening BPD Aceh</div>
                                            <div class="fs-6">{{ $karyawan->rek_bpd_aceh }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Berat badan</div>
                                            <div class="fs-6">{{ $karyawan->berat_badan }} Kg</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Tinggi badan</div>
                                            <div class="fs-6">{{ $karyawan->tinggi_badan }} Cm</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Warna kulit</div>
                                            <div class="fs-6">{{ $karyawan->kulit }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Suku</div>
                                            <div class="fs-6">{{ $karyawan->suku }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Kebangsaan</div>
                                            <div class="fs-6">{{ $karyawan->kebangsaan }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">No. HP</div>
                                            <div class="fs-6">{{ $karyawan->no_hp }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Status Rumah</div>
                                            <div class="fs-6">{{ $karyawan->status_rmh }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                        <div class="mt-3">
                                            <div class="fw-bold fs-6">Ibu Kandung</div>
                                            <div class="fs-6">{{ $karyawan->nama_ibu_kandung }}</div>
                                        </div>
                                        <hr class="border-gray-600">
                                    </div>
                                    <div class="col-content p-5">
                                        <div class="bg-white mb-2">
                                            <div class="fw-bold fs-2">{{ $nama_lengkap }}</div>
                                            <div class="fs-6">
                                                {{ $karyawan->email ? $karyawan->email : 'Email belum terisi' }}
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start mb-4">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class=" fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Alamat</h4>
                                                    <div class="fs-6 text-gray-700 ">
                                                        {{ $alamat }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">TEMPAT BEKERJA</h4>
                                                    <table class="table">
                                                        <tr>
                                                            <th class="fw-bold">SKPD</th>
                                                            <th>:</th>
                                                            <td>RUMAH SAKIT UMUM DAERAH LANGSA</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="fw-bold">Departemen</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->divisi }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="fw-bold">Bagian / Bidang</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->unit_kerja }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="fw-bold">Sub.Bag / Sub.Bid / Unit / Instalasi</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->sub_unit_kerja }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="fw-bold">Ruangan</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->ruangan }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">TUGAS TAMBAHAN</h4>
                                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Tugas Tambahan</th>
                                                                <th>Jabatan</th>
                                                                <th>Ruangan</th>
                                                                <th>TMT Awal</th>
                                                                <th>TMT Akhir</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @forelse ($tugasTambahan as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->nama_tugas_tambahan }}</td>
                                                                    <td>{{ $item->jab_struk_tambahan ?? '-' }}</td>
                                                                    <td>{{ $item->ruangan_tambahan ?? '-' }}</td>
                                                                    <td>{{ $carbon->parse($item->tmt_awal)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                    <td>{{ $item->tmt_akhir ? $carbon->parse($item->tmt_akhir)->locale('id_ID')->isoFormat('LL') : '-' }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center">Tidak terdapat tugas tambahan</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">JABATAN SAAT INI</h4>
                                                    <table class="table">
                                                        <tr>
                                                            <th class="fw-bold">Kelompok Jabatan</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->jenis_tenaga }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="fw-bold">Jenis Tenaga</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->sub_detail }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="fw-bold">Jabatan struktural / Non-Struktural</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->jab_struk }}</td>
                                                        </tr>
                                                        @if($kd_status_kerja == 1 && $kd_status_kerja == 7)
                                                            <tr>
                                                                <th class="fw-bold">Jabatan Fungsional</th>
                                                                <th>:</th>
                                                                <td>{{ $karyawan->jab_fung }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="fw-bold">Eselon</th>
                                                                <th>:</th>
                                                                <td>{{ $karyawan->eselon }}</td>
                                                            </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        @if($kd_status_kerja == 1 && $kd_status_kerja == 7)
                                            <div class="d-flex align-items-start">
                                                <i class="ki-duotone ki-arrow-up-right fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                    <div class="fw-semibold">
                                                        <h4 class="text-gray-900 fw-bold">PANGKAT TERAKHIR</h4>
                                                        <table class="table">
                                                            <tr>
                                                                <th class="fw-bold">Gol. / Pangkat</th>
                                                                <th>:</th>
                                                                <td>IIId / Penata Tingkat I</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="fw-bold">TMT Pangkat</th>
                                                                <th>:</th>
                                                                <td>01-04-2019</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="fw-bold">Masa kerja</th>
                                                                <th>:</th>
                                                                <td>13 Tahun 4 Bulan</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="fw-bold">Rencana Kenaikan Pangkat</th>
                                                                <th>:</th>
                                                                <td>01-04-2019</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="fw-bold">Tanggungan</th>
                                                                <th>:</th>
                                                                <td>3</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <i class="ki-duotone ki-arrow-up-right fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                    <div class="fw-semibold">
                                                        <h4 class="text-gray-900 fw-bold">PENGANGKATAN CPNS</h4>
                                                        <table class="table">
                                                            <tr>
                                                                <th class="fw-bold">Gol. / Pangkat</th>
                                                                <th>:</th>
                                                                <td>IIa / Pengatur Muda</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="fw-bold">TMT Gol. / Pangkat</th>
                                                                <th>:</th>
                                                                <td>12-01-2000</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="fw-bold">Masa kerja</th>
                                                                <th>:</th>
                                                                <td>0 Tahun 0 Bulan</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">PENDIDIKAN UMUM</h4>
                                                    <table class="table">
                                                        <tr>
                                                            <th class="fw-bold">Tingkat Pendidikan</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->jenjang_didik }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="fw-bold">Program Studi</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->jurusan }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="fw-bold">Tahun Lulus</th>
                                                            <th>:</th>
                                                            <td>{{ $karyawan->tahun_lulus }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">ANGGOTA KELUARGA</h4>
                                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Nama Anggota Keluarga</th>
                                                                <th>TTL</th>
                                                                <th>Jenis Kelamin</th>
                                                                <th>Pendidikan</th>
                                                                <th>Pekerjaan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @forelse ($keluarga as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->nama }}</td>
                                                                    <td>{{ $item->tempat_lahir }}, {{ $carbon->parse($item->tgl_lahir)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                    <td>{{ $item->jk == 0 ? 'Perempuan' : 'Laki-laki' }}</td>
                                                                    <td>{{ $item->jenjang_didik }}</td>
                                                                    <td>{{ $item->pekerjaan }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center">Data anggota keluarga belum terisi.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">KEMAMPUAN BAHASA</h4>
                                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Bahasa</th>
                                                                <th>Tingkat Kemampuan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @forelse ($bahasa as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->bahasa }}</td>
                                                                    <td>{{ $item->tingkat_bahasa }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3" class="text-center">Data kemampuan bahasa belum terisi.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">RIWAYAT PENDIDIKAN</h4>
                                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Jenis Pendidikan</th>
                                                                <th>Jurusan</th>
                                                                <th>Nama Lembaga</th>
                                                                <th>Tahun Lulus</th>
                                                                <th>No. Ijazah</th>
                                                                <th>Alamat Lembaga</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @forelse ($riwayatPendidikan as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->jenjang_didik }}</td>
                                                                    <td>{{ $item->jurusan }}</td>
                                                                    <td>{{ $item->nama_lembaga }}</td>
                                                                    <td>{{ $item->tahun_lulus }}</td>
                                                                    <td>{{ $item->no_ijazah }}</td>
                                                                    <td>{{ $item->tempat }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center">Data riwayat pendidikan belum terisi.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">RIWAYAT PEKERJAAN</h4>
                                                    @if ($kd_status_kerja == 1 && $kd_status_kerja == 7)
                                                        <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                            <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                                <tr>
                                                                    <th rowspan="2">No Urut</th>
                                                                    <th rowspan="2">Uraian Perubahan Pangkat/Jabatan</th>
                                                                    <th rowspan="2">Mulai dan Sampai</th>
                                                                    <th rowspan="2">Gol/R. Gaji</th>
                                                                    <th rowspan="2">Gaji</th>
                                                                    <th colspan="3">Surat Keputusan/Bukti Pengalaman</th>
                                                                    <th rowspan="2">Keterangan</th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Nomor</th>
                                                                    <th>Tanggal</th>
                                                                    <th>Pejabat</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="fw-semibold text-gray-600">
                                                                @forelse ($riwayatKepangkatan as $item)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $item->pangkat }}</td>
                                                                        <td>{{ $carbon->parse($item->mulai_dan_sampai)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                        <td>{{ $item->golongan }}</td>
                                                                        <td>Rp. {{ number_format($item->gaji, 0, ',', '.') }}</td>
                                                                        <td>{{ $item->nomors_sk }}</td>
                                                                        <td>{{ $carbon->parse($item->tgl_sk)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                        <td>{{ $item->pejabat_sk }}</td>
                                                                        <td>{{ $item->ket }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="9" class="text-center">Data riwayat kepangkatan belum terisi.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                            <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Nama Pejabat</th>
                                                                    <th>Tanggal SK</th>
                                                                    <th>No. SK</th>
                                                                    <th>TMT</th>
                                                                    <th>Perusahaan/Lembaga</th>
                                                                    <th>Keterangan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="fw-semibold text-gray-600">
                                                                @forelse ($riwayatPekerjaan as $item)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $item->pejabat }}</td>
                                                                        <td>{{ $carbon->parse($item->tgl_sk)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                        <td>{{ $item->no_sk }}</td>
                                                                        <td>{{ $carbon->parse($item->tmt)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                        <td>{{ $item->perusahaan }}</td>
                                                                        <td>{{ $item->ket }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="7" class="text-center">Data riwayat pekerjaan belum terisi.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">RIWAYAT PENGALAMAN ORGANISASI</h4>
                                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Pejabat</th>
                                                                <th>No. SK</th>
                                                                <th>Tanggal SK</th>
                                                                <th>Organisasi</th>
                                                                <th>Jabatan</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @forelse ($riwayatOrganisasi as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->pejabat }}</td>
                                                                    <td>{{ $item->no_sk }}</td>
                                                                    <td>{{ $carbon->parse($item->tgl_sk)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                    <td>{{ $item->organisasi }}</td>
                                                                    <td>{{ $item->jabatan }}</td>
                                                                    <td>{{ $item->ket }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center">Data riwayat organisasi belum terisi.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">RIWAYAT PENGHARGAAN</h4>
                                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Pejabat</th>
                                                                <th>No. SK</th>
                                                                <th>Tanggal SK</th>
                                                                <th>Bentuk</th>
                                                                <th>Event</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @forelse ($riwayatPenghargaan as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->pejabat }}</td>
                                                                    <td>{{ $item->no_sk }}</td>
                                                                    <td>{{ $carbon->parse($item->tgl_sk)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                    <td>{{ $item->bentuk }}</td>
                                                                    <td>{{ $item->event }}</td>
                                                                    <td>{{ $item->ket }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center">Data riwayat penghargaan belum terisi.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">RIWAYAT SEMINAR</h4>
                                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Seminar</th>
                                                                <th>No. Sertifikat</th>
                                                                <th>Tanggal Acara</th>
                                                                <th>Penyelenggara</th>
                                                                <th>Sumber Dana</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @forelse ($riwayatSeminar as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->nama_seminar }}</td>
                                                                    <td>{{ $item->no_sertifikat }}</td>
                                                                    <td>{{ $carbon->parse($item->tgl_mulai)->locale('id_ID')->isoFormat('LL') . ' s/d ' . $carbon->parse($item->tgl_akhir)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                    <td>{{ $item->penyelenggara }}</td>
                                                                    <td>{{ $item->sumber_dana }}</td>
                                                                    <td>{{ $item->ket }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center">Data riwayat seminar belum terisi.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="ki-duotone ki-arrow-up-right fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 ms-2">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">RIWAYAT TUGAS</h4>
                                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                                        <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Jabatan</th>
                                                                <th>Jenis Tenaga</th>
                                                                <th>Ruangan</th>
                                                                <th>Tgl. Masuk</th>
                                                                <th>Tgl. Keluar</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @forelse ($riwayatTugas as $item)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $item->jab_struk }}</td>
                                                                    <td>{{ $item->sub_detail }}</td>
                                                                    <td>{{ $item->ruangan }}</td>
                                                                    <td>{{ $carbon->parse($item->tgl_masuk)->locale('id_ID')->isoFormat('LL') }}</td>
                                                                    <td>{{ $item->tgl_keluar ? $carbon->parse($item->tgl_keluar)->locale('id_ID')->isoFormat('LL') : '-' }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center">Data riwayat tugas belum terisi.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    
    <script>
        // Set nama file untuk PDF
        const namaKaryawan = '{{ $karyawan->nama ?? "Karyawan" }}';
        const originalTitle = document.title;
        
        // Function untuk set nama file PDF
        function setFileNameForPrint() {
            document.title = `CV-${namaKaryawan}`;
        }
        
        // Function untuk restore original title
        function restoreTitle() {
            document.title = originalTitle;
        }
        
        // Auto print when page loads
        window.addEventListener('load', function() {
            setFileNameForPrint();
            setTimeout(function() {
                window.print();
            }, 500);
        });
        
        // Function untuk switch ukuran kertas
        function setPaperSize(size) {
            const style = document.createElement('style');
            if (size === 'f4') {
                style.textContent = `
                    @media print {
                        @page {
                            size: 210mm 330mm;
                            margin: 0.5in;
                        }
                    }
                `;
            } else {
                style.textContent = `
                    @media print {
                        @page {
                            size: A4 portrait;
                            margin: 0.5in;
                        }
                    }
                `;
            }
            document.head.appendChild(style);
        }
        
        // Event listener untuk mendeteksi print
        window.addEventListener('beforeprint', function() {
            setFileNameForPrint();
        });
        
        window.addEventListener('afterprint', function() {
            restoreTitle();
        });
        
        // Override window.print untuk selalu set nama file
        const originalPrint = window.print;
        window.print = function() {
            setFileNameForPrint();
            originalPrint();
        };
        
        // Keyboard shortcut untuk print
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>