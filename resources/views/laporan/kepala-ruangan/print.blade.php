<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/Langsa.png') }}" />
    <meta name="author" content="RSUD Langsa" />
    <title>HRD RSUD LANGSA - Daftar Kepala Ruangan</title>
    
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .header-table td {
            vertical-align: top;
            padding: 5px;
        }
        
        .logo {
            width: 80px;
            height: 90px;
        }
        
        .header-text {
            text-align: center;
        }
        
        .header-text .title-main {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header-text .title-sub {
            font-size: 17pt;
            font-weight: bold;
            margin: 0;
        }
        
        .header-text .address {
            font-size: 8pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }
        
        .header-text .city {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .line1 {
            background: #282828;
            height: 3px;
            margin: 10px 0 0 0;
            border: none;
        }
        
        .line2 {
            background: #282828;
            height: 1px;
            margin: 5px 0;
            border: none;
        }
        
        .report-title {
            font-size: 10pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .period-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #c4c4c4;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        
        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 7pt;
        }
        
        .data-table td {
            font-size: 6pt;
        }
        
        .data-table .text-left {
            text-align: left;
        }
        
        .employee-name {
            font-weight: bold;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            
            .no-print {
                display: none;
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
    
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td width="140">
                <img src="{{ asset('assets/media/images/Langsa.png') }}" class="logo" alt="Logo RSUD Langsa">
            </td>
            <td class="header-text">
                <div class="title-main">PEMERINTAH KOTA LANGSA</div>
                <div class="title-sub">RUMAH SAKIT UMUM DAERAH LANGSA</div>
                <div class="address">
                    Alamat : Jln. Jend. A. Yani No.1 Kota Langsa Provinsi Pemerintah Aceh,<br>
                    Telp. (0641) 22051 22800 (IGD) Fax. (0641) 22051<br>
                    E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,<br>
                    Website : www.rsud.langsakota.go.id
                </div>
                <div class="city">KOTA LANGSA</div>
            </td>
            <td width="140"></td>
        </tr>
    </table>
    
    <hr class="line1">
    <hr class="line2">
    
    <!-- Title -->
    <div class="report-title">Daftar Kepala Ruangan / Instalasi / Unit</div>
    <div class="period-title">
        PERIODE {{ $dataBulan[$bulan] ?? '' }} {{ $tahun }}
    </div>
    
    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="3">No.</th>
                <th rowspan="3">
                    Nama<br>
                    Tempat & Tanggal Lahir<br>
                    NIP / No. KARPEG / ID Peg.
                </th>
                <th rowspan="3">L/P</th>
                <th colspan="4">Kepangkatan sekarang</th>
                <th rowspan="3">Eselon</th>
                <th rowspan="3" width="100">TMT</th>
                <th rowspan="3">Jabatan non-struktural</th>
                <th rowspan="3" width="100">TMT</th>
            </tr>
            <tr>
                <th rowspan="2">Pangkat / Gol.</th>
                <th rowspan="2" width="100">TMT</th>
                <th colspan="2">Masa kerja</th>
            </tr>
            <tr>
                <th>Thn.</th>
                <th>Bln.</th>
            </tr>
        </thead>
        <tbody>
            @if($data->count() > 0)
                @foreach($data as $index => $item)
                    @php
                        $tglLahir = $item->tgl_lahir ? \Carbon\Carbon::parse($item->tgl_lahir)->format('d-m-Y') : '-';
                        $tmtGol = $item->tmt_gol_sekarang ? \Carbon\Carbon::parse($item->tmt_gol_sekarang)->format('d-m-Y') : '-';
                        $tmtJabStruk = $item->tmt_jabatan_struktural ? \Carbon\Carbon::parse($item->tmt_jabatan_struktural)->format('d-m-Y') : '-';
                        $tmtEselon = $item->tmt_eselon ? \Carbon\Carbon::parse($item->tmt_eselon)->format('d-m-Y') : '-';
                        
                        $jenis = '?';
                        if ($item->jenis_kelamin == 'Pria') {
                            $jenis = 'L';
                        } elseif ($item->jenis_kelamin == 'Wanita') {
                            $jenis = 'P';
                        }
                        
                        $gelarDepan = $item->gelar_depan ? $item->gelar_depan . ' ' : '';
                        $gelarBelakang = $item->gelar_belakang ? $item->gelar_belakang : '';
                        $namaLengkap = $gelarDepan . $item->nama . $gelarBelakang;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">
                            <div class="employee-name">{{ $namaLengkap }}</div>
                            <div>{{ $item->tempat_lahir }}, {{ $tglLahir }}</div>
                            <div>{{ $item->nip_baru ?? '-' }}</div>
                            <div>{{ $item->no_karpeg ?? '-' }} / {{ $item->kd_karyawan ?? '-' }}</div>
                        </td>
                        <td>{{ $jenis }}</td>
                        <td>{{ $item->pangkat ?? '-' }} / {{ $item->kd_gol_sekarang ?? '-' }}</td>
                        <td>{{ $tmtGol }}</td>
                        <td>{{ $item->masa_kerja_thn ?? '0' }}</td>
                        <td>{{ $item->masa_kerja_bulan ?? '0' }}</td>
                        <td>{{ $item->eselon ?? '-' }}</td>
                        <td>{{ $tmtEselon }}</td>
                        <td>
                            {{ $item->jab_struk ?? '-' }}
                            @if($item->ruangan)
                                <br>Pada {{ $item->ruangan }}
                            @endif
                        </td>
                        <td>{{ $tmtJabStruk }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="11">Tidak Ada Data</td>
                </tr>
            @endif
        </tbody>
    </table>
    
</body>
</html>
