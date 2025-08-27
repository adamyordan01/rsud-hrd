<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/Langsa.png') }}" />
    <meta name="author" content="RSUD Langsa" />
    <title>HRD RSUD LANGSA - Laporan Satya Lencana</title>
    
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
            font-size: 15pt;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }
        
        .category-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 15px 0 5px 0;
            font-family: "Times New Roman";
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
        
        .group-header {
            font-weight: bold;
            background-color: #f0f0f0;
            font-size: 8pt;
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
    
    <!-- Title -->
    <div class="report-title">
        <b>SATYA LENCANA</b>
    </div>

    @if(count($result) > 0)
        @php $globalNo = 0; @endphp
        @foreach($result as $category)
            <!-- Category Title -->
            <div class="category-title">
                PENGHARGAAN TANDA KEHORMATAN SATYA LENCANA {{ $category['title'] }}
            </div>
            
            <hr class="line2">
            
            <!-- Data Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>NIP LAMA</th>
                        <th>NIP BARU</th>
                        <th>NAMA</th>
                        <th>GOLONGAN</th>
                        <th>MASA KERJA THN</th>
                        <th>MASA KERJA BLN</th>
                        <th>PENDIDIKAN TERAKHIR</th>
                        <th>JURUSAN</th>
                        <th>TAHUN LULUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category['jenis_tenaga_groups'] as $group)
                        <!-- Header Jenis Tenaga -->
                        <tr class="group-header">
                            <td colspan="10" class="text-left">{{ $group['jenis_tenaga'] }}</td>
                        </tr>
                        
                        @foreach($group['pegawai_list'] as $pegawai)
                            @php $globalNo++; @endphp
                            <tr>
                                <td class="text-center">{{ $globalNo }}</td>
                                <td class="text-center">{{ $pegawai->nip_lama ?: '-' }}</td>
                                <td class="text-center">{{ $pegawai->nip_baru ?: '-' }}</td>
                                <td class="text-left">
                                    <b>{{ trim(($pegawai->gelar_depan ?: '') . ' ' . $pegawai->nama . ' ' . ($pegawai->gelar_belakang ?: '')) }}</b>
                                </td>
                                <td class="text-center">{{ $pegawai->kd_gol_sekarang ?: '-' }}</td>
                                <td class="text-center">{{ $pegawai->masa_kerja_thn ?: 0 }}</td>
                                <td class="text-center">{{ $pegawai->masa_kerja_bulan ?: 0 }}</td>
                                <td class="text-center">{{ $pegawai->jenjang_didik ?: '-' }}</td>
                                <td class="text-center">{{ $pegawai->jurusan ?: '-' }}</td>
                                <td class="text-center">{{ $pegawai->tahun_lulus ?: '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            
            @if(!$loop->last)
                <div style="page-break-after: always;"></div>
            @endif
        @endforeach
    @else
        <div style="text-align: center; padding: 50px;">
            <p>Tidak ada data pegawai yang memenuhi kriteria Satya Lencana</p>
        </div>
    @endif
    
</body>
</html>
