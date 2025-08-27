<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/Langsa.png') }}" />
    <meta name="author" content="RSUD Langsa" />
    <title>HRD RSUD LANGSA - Laporan Berdasarkan Golongan</title>
    
    <style>
        body {
            font-family: cambria;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-left {
            text-align: left;
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
            margin: 0px;
            border: none;
            margin-top: 10px;
        }
        
        .line2 {
            background: #282828;
            margin: 0px;
            margin-bottom: 5px;
            height: 1px !important;
            border: none;
        }
        
        .report-title {
            font-size: 10pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
            font-family: cambria;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #c4c4c4;
            padding: 1px;
            text-align: center;
            vertical-align: middle;
        }
        
        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 8pt;
            padding: 5px;
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
        
        table thead {
            display: table-header-group;
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
    <div class="report-title">
        <b style='font-size:10pt;'>DATA PEGAWAI PNS DAN PPPK DENGAN GOLONGAN {{ strtoupper($golonganNama) }}</b>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="3" style="text-align:center; vertical-align: middle;">No.</th>
                <th rowspan="3" style="text-align:center; vertical-align: middle;">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG / ID Peg.</th>
                <th rowspan="3" style="text-align:center; vertical-align: middle;">L/P</th>
                <th rowspan="3" style="text-align:center; vertical-align: middle;"><label style="text-decoration:underline;">NIK</label><br>No.ASKES/BPJS</th>
                <th colspan="4" style="text-align:center; vertical-align: middle;">Kepangkatan sekarang</th>
                <th colspan="3" style="text-align:center; vertical-align: middle;">Pendidikan terakhir</th>
                <th rowspan="3" style="text-align:center; vertical-align: middle;">Jenis tenaga</th>
                <th rowspan="3" style="text-align:center; vertical-align: middle;">Sub. Jenis tenaga<br>Ruangan</th>
            </tr>
            <tr>
                <th rowspan="2" style="text-align:center; vertical-align: middle;">Pangkat / Gol.</th>
                <th rowspan="2" style="text-align:center; vertical-align: middle;" width="100">TMT</th>
                <th colspan="2" style="text-align:center; vertical-align: middle;">Masa kerja</th>
                <th rowspan="2" style="text-align:center; vertical-align: middle;">Jenjang</th>
                <th rowspan="2" style="text-align:center; vertical-align: middle;">Program studi</th>
                <th rowspan="2" style="text-align:center; vertical-align: middle;">Lulus tahun</th>
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
                        
                        $jenis = '?';
                        if ($item->jenis_kelamin == 'Pria') {
                            $jenis = 'L';
                        } elseif ($item->jenis_kelamin == 'Wanita') {
                            $jenis = 'P';
                        }
                        
                        $gelarDepan = $item->gelar_depan ? $item->gelar_depan . ' ' : '';
                        $gelarBelakang = $item->gelar_belakang ? ', ' . $item->gelar_belakang : '';
                        $namaLengkap = $gelarDepan . $item->nama . $gelarBelakang;
                    @endphp
                    <tr>
                        <td style='text-align:center;'>{{ $index + 1 }}</td>
                        <td style='text-align:left;'>
                            <b>{{ $namaLengkap }}</b><br>
                            {{ $item->tempat_lahir }}, {{ $tglLahir }}<br>
                            @if($item->kd_status_kerja == 1 || $item->kd_status_kerja == 7)
                                {{ $item->nip_baru ?? '-' }}<br>{{ $item->no_karpeg ?? '-' }} / {{ $item->kd_karyawan ?? '-' }}
                            @else
                                {{ $item->kd_karyawan ?? '-' }}
                            @endif
                        </td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $jenis }}</td>
                        <td style='text-align:left;'>
                            @if($item->no_ktp)
                                <p style='text-decoration:underline;'>{{ $item->no_ktp }}</p>
                            @else
                                <p style='text-decoration:underline;'>-</p>
                            @endif
                            {{ $item->no_askes ?? '' }}
                        </td>
                        @if($item->kd_status_kerja == 1 || $item->kd_status_kerja == 7)
                            <td style='text-align:center; vertical-align: middle;'>{{ $item->pangkat ?? '-' }} / {{ $item->kd_gol_sekarang ?? '-' }}</td>
                            <td style='text-align:center; vertical-align: middle;'>{{ $tmtGol }}</td>
                            <td style='text-align:center; vertical-align: middle;'>{{ $item->masa_kerja_thn ?? '0' }}</td>
                            <td style='text-align:center; vertical-align: middle;'>{{ $item->masa_kerja_bulan ?? '0' }}</td>
                        @else
                            <td style='text-align:center; vertical-align: middle;'>-</td>
                            <td style='text-align:center; vertical-align: middle;'>-</td>
                            <td style='text-align:center; vertical-align: middle;'>-</td>
                            <td style='text-align:center; vertical-align: middle;'>-</td>
                        @endif
                        <td style='text-align:center; vertical-align: middle;'>{{ $item->jenjang_didik ?? '-' }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $item->jurusan ?? '-' }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $item->tahun_lulus ?? '-' }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $item->jenis_tenaga ?? '-' }}</td>
                        <td style='text-align:center;font-size:6pt;text-transform:uppercase;'>
                            {{ $item->sub_detail ? 'Tenaga ' . $item->sub_detail : '-' }}
                            @if($item->ruangan)
                                <br>Pada {{ $item->ruangan }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="13" class='text-center'>
                        Tidak ada data
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    
</body>
</html>
