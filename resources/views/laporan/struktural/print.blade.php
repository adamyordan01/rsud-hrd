<!DOCTYPE html>
<html lang="id">
<head>
    
    <div class="line-separator"></div>
    <div class="line-separator-thin"></div>
    
    <div style="margin: 15px 0;">
        <div style="font-size: 10pt; font-weight: bold;">DAFTAR STRUKTURAL</div>
        <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">
            PERIODE {{ $dataBulan[$bulan] ?? 'SEKARANG' }} {{ $tahun }}
        </div>
    </div>

    {{-- Table Data --}}
    <table class="table-print">
        <thead>
            <tr>
                <th rowspan="3" style="width: 30px;">No.</th>
                <th rowspan="3" style="width: 150px;">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG / ID Peg.</th>
                <th rowspan="3" style="width: 25px;">L/P</th>
                <th colspan="4">Kepangkatan Sekarang</th>
                <th rowspan="3" style="width: 60px;">Eselon</th>
                <th rowspan="3" style="width: 70px;">TMT</th>
                <th rowspan="3" style="width: 120px;">Jabatan Struktural</th>
                <th rowspan="3" style="width: 70px;">TMT</th>
            </tr>
            <tr>
                <th rowspan="2" style="width: 80px;">Pangkat / Gol.</th>
                <th rowspan="2" style="width: 70px;">TMT</th>
                <th colspan="2">Masa Kerja</th>
            </tr>
            <tr>
                <th style="width: 30px;">Thn.</th>
                <th style="width: 30px;">Bln.</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($data as $row)
                @php
                    $tglLahir = $row->tgl_lahir ? \Carbon\Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-';
                    $tmtGol = $row->tmt_gol_sekarang ? \Carbon\Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                    $tmtJabStruk = $row->tmt_jabatan_struktural ? \Carbon\Carbon::parse($row->tmt_jabatan_struktural)->format('d-m-Y') : '-';
                    $tmtEselon = $row->tmt_eselon ? \Carbon\Carbon::parse($row->tmt_eselon)->format('d-m-Y') : '-';
                    
                    if ($row->jenis_kelamin == 'Pria') {
                        $jenis = 'L';
                    } elseif ($row->jenis_kelamin == 'Wanita') {
                        $jenis = 'P';
                    } else {
                        $jenis = '?';
                    }
                    
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $row->nama . $gelarBelakang;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-left">
                        <strong>{{ $namaLengkap }}</strong><br>
                        {{ $row->tempat_lahir }}, {{ $tglLahir }}<br>
                        {{ $row->nip_baru ?? '-' }}<br>
                        {{ $row->no_karpeg ?? '-' }} / {{ $row->kd_karyawan ?? '-' }}
                    </td>
                    <td class="text-center">{{ $jenis }}</td>
                    <td class="text-center">{{ ($row->pangkat ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-') }}</td>
                    <td class="text-center">{{ $tmtGol }}</td>
                    <td class="text-center">{{ $row->masa_kerja_thn ?? '0' }}</td>
                    <td class="text-center">{{ $row->masa_kerja_bulan ?? '0' }}</td>
                    <td class="text-center">{{ $row->eselon ?? '-' }}</td>
                    <td class="text-center">{{ $tmtEselon }}</td>
                    <td class="text-center">{{ $row->jab_struk ?? '-' }}</td>
                    <td class="text-center">{{ $tmtJabStruk }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak Ada Data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/logo.png') }}" />
    <meta name="author" content="HRD RSUD LANGSA" />
    <title>Daftar Struktural - {{ $dataBulan[$bulan] ?? 'Sekarang' }} {{ $tahun }}</title>
    
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 8pt;
            margin: 0;
            padding: 15px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-left {
            text-align: left;
        }
        
        .table-print {
            border-collapse: collapse;
            margin-top: 10px;
            width: 100%;
        }
        
        .table-print td,
        .table-print th {
            border: 1px solid #000;
            padding: 3px;
            font-size: 6pt;
        }
        
        .table-print th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        
        .header-table {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .header-table td {
            border: none;
            padding: 5px;
        }
        
        .line-separator {
            background: #000;
            height: 3px;
            margin: 5px 0;
        }
        
        .line-separator-thin {
            background: #000;
            height: 1px;
            margin: 2px 0;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
        }
    </style>
    
    <script>
        function cetak() {
            window.print();
            setTimeout(window.close, 0);
        }
    </script>
</head>

<body class="text-center" onLoad="cetak()">
    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td width="140">
                <img src="{{ asset('assets/media/images/Langsa.png') }}" width="80" height="90">
            </td>
            <td style="vertical-align: top;">
                <div class="text-center">
                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 5px;">
                        PEMERINTAH KOTA LANGSA
                    </div>
                    <div style="font-size: 17pt; font-weight: bold; margin-bottom: 5px;">
                        RUMAH SAKIT UMUM DAERAH LANGSA
                    </div>
                    <div style="font-size: 8pt;">
                        Alamat : Jln. Jend. A. Yani No.1 Kota Langsa Provinsi Pemerintah Aceh,<br>
                        Telp. (0641) 22051 22800 (IGD) Fax. (0641) 22051<br>
                        E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,<br>
                        Website : www.rsud.langsakota.go.id<br>
                        <strong style="font-size: 10pt;">KOTA LANGSA</strong>
                    </div>
                </div>
            </td>
            <td width="140"></td>
        </tr>
    </table>

    <div class="line-separator"></div>
    <div class="line-separator-thin"></div>

    <div style="margin: 15px 0;">
        <div style="font-size: 10pt; font-weight: bold;">DAFTAR STRUKTURAL</div>
        <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">
            PERIODE {{ $dataBulan[$bulan] ?? 'SEKARANG' }} {{ $tahun }}
        </div>
    </div>

    {{-- Table Data --}}
    <table class="table-print">
        <thead>
            <tr>
                <th rowspan="3" style="width: 30px;">No.</th>
                <th rowspan="3" style="width: 150px;">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG / ID Peg.</th>
                <th rowspan="3" style="width: 25px;">L/P</th>
                <th colspan="4">Kepangkatan Sekarang</th>
                <th rowspan="3" style="width: 60px;">Eselon</th>
                <th rowspan="3" style="width: 70px;">TMT</th>
                <th rowspan="3" style="width: 120px;">Jabatan Struktural</th>
                <th rowspan="3" style="width: 70px;">TMT</th>
            </tr>
            <tr>
                <th rowspan="2" style="width: 80px;">Pangkat / Gol.</th>
                <th rowspan="2" style="width: 70px;">TMT</th>
                <th colspan="2">Masa Kerja</th>
            </tr>
            <tr>
                <th style="width: 30px;">Thn.</th>
                <th style="width: 30px;">Bln.</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($data as $row)
                @php
                    $tmtGol = $row->tmt_gol_sekarang ? \Carbon\Carbon::parse($row->tmt_gol_sekarang)->format('d-m-Y') : '-';
                    $tmtGolMasuk = $row->tmt_gol_masuk ? \Carbon\Carbon::parse($row->tmt_gol_masuk)->format('d-m-Y') : '-';
                    $tmtEselon = $row->tmt_eselon ? \Carbon\Carbon::parse($row->tmt_eselon)->format('d-m-Y') : '-';
                    $tglLahir = $row->tgl_lahir ? \Carbon\Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-';
                    $jenis = $row->jenis_kelamin == 'Pria' ? 'L' : 'P';
                    
                    $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                    $gelarBelakang = $row->gelar_belakang ? $row->gelar_belakang : '';
                    $namaLengkap = $gelarDepan . $row->nama . $gelarBelakang;
                @endphp

                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-left">
                        <strong>{{ $namaLengkap }}</strong><br>
                        {{ $row->tempat_lahir }}, {{ $tglLahir }}<br>
                        {{ $row->nip_baru ?? '-' }}<br>
                        {{ $row->no_karpeg ?? '-' }} / {{ $row->kd_karyawan ?? '-' }}
                    </td>
                    <td class="text-center">{{ $jenis }}</td>
                    <td class="text-center">{{ ($row->pangkat ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-') }}</td>
                    <td class="text-center">{{ $tmtGol }}</td>
                    <td class="text-center">{{ $row->masa_kerja_thn ?? '0' }}</td>
                    <td class="text-center">{{ $row->masa_kerja_bulan ?? '0' }}</td>
                    <td class="text-center">{{ $row->eselon ?? '-' }}</td>
                    <td class="text-center">{{ $tmtEselon }}</td>
                    <td class="text-left">{{ $row->jab_struk ?? '-' }}</td>
                    <td class="text-center">{{ $tmtJabStruk }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak Ada Data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>