<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/Langsa.png') }}" />
    <meta name="author" content="RSUD Langsa" />
    <title>HRD RSUD LANGSA - Laporan Data Taspen</title>
    
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
            width: 70px;
            height: 90px;
        }
        
        .header-text {
            text-align: center;
        }
        
        .header-text .title-main {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header-text .title-sub {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
        }
        
        .header-text .address {
            font-size: 7pt;
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
            height: 0.5px !important;
            border: none;
        }
        
        .report-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .description-table {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .description-table td {
            padding: 5px;
            vertical-align: top;
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
            font-size: 8pt;
        }
        
        .data-table .text-left {
            text-align: left;
        }
        
        .employee-name {
            font-weight: bold;
        }
        
        .signature-section {
            border: 0px;
            line-height: 25px;
            font-size: 11pt;
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
            <td width="80">
                <img src="{{ asset('assets/media/images/Langsa.png') }}" class="logo" alt="Logo RSUD Langsa">
            </td>
            <td class="header-text" style="vertical-align: top;">
                <div class="title-main">PEMERINTAH KOTA LANGSA</div>
                <div class="title-sub">RUMAH SAKIT UMUM DAERAH LANGSA</div>
                <div class="address">
                    Alamat : Jln. Jend. A. Yani No.1 Kota Langsa - Provinsi Pemerintah Aceh,<br>
                    Telp. (0641) 22051 - 22800 (IGD) Fax. (0641) 22051<br>
                    E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,<br>
                    Website : www.rsud.langsakota.go.id
                </div>
                <div class="city">KOTA LANGSA</div>
            </td>
            <td width="60"></td>
        </tr>
    </table>
    
    <hr class="line1">
    <hr class="line2">
    
    <!-- Title -->
    <div class="report-title">
        <b style='font-size: 11pt;'>LAPORAN DATA TASPEN</b>
    </div>

    <!-- Description -->
    <table class="description-table">
        <tr>
            <td></td>
            <td width="25px" style="text-align: right; padding: 5px; white-space: nowrap; vertical-align: top;">Daftar :</td>
            <td width="200px" style="text-align: justify; padding: 5px;">Nomor Kartu Tanda Penduduk (KTP) dan Nomor HP Aparatur Sipil Negara (ASN) Dinas/Badan/Kantor Kota Langsa</td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align:center; vertical-align: middle;">No.</th>
                <th style="text-align:center; vertical-align: middle;">Nama</th>
                <th style="text-align:center; vertical-align: middle;">NIP</th>
                <th style="text-align:center; vertical-align: middle;">No. KTP</th>
                <th style="text-align:center; vertical-align: middle;">No. HP</th>
                <th style="text-align:center; vertical-align: middle;">Ket</th>
            </tr>
        </thead>
        <tbody>
            @if($data->count() > 0)
                @foreach($data as $index => $item)
                    @php
                        $gelarDepan = $item->gelar_depan ? $item->gelar_depan . ' ' : '';
                        $gelarBelakang = $item->gelar_belakang ? $item->gelar_belakang : '';
                        $namaLengkap = $gelarDepan . $item->nama . $gelarBelakang;
                    @endphp
                    <tr>
                        <td style='text-align:center;'>{{ $index + 1 }}</td>
                        <td style='text-align:left; max-width:150px;'>
                            <b>{{ $namaLengkap }}</b>
                        </td>
                        <td style='text-align:center;'>{{ $item->nip_baru ?? '-' }}</td>
                        <td style='text-align:center;'>{{ $item->no_ktp ?? '-' }}</td>
                        <td style='text-align:center;'>{{ $item->no_hp ?? '-' }}</td>
                        <td style='text-align:center; width:100px;'>&nbsp;</td>
                    </tr>
                @endforeach
                
                <!-- Signature Section -->
                <tr>
                    <td colspan="3" class="signature-section" style="text-align: center; border:0px;"></td>
                    <td colspan="3" class="signature-section" style="text-align: center; border:0px;">
                        Langsa, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
                        DIREKTUR RUMAH SAKIT UMUM DAERAH LANGSA<br><br><br>
                        @if($direktur)
                            <b><u>{{ ($direktur->gelar_depan ? $direktur->gelar_depan . ' ' : '') . $direktur->nama . ($direktur->gelar_belakang ? $direktur->gelar_belakang : '') }}</u></b><br>
                            NIP. {{ $direktur->nip_baru ?? '-' }}
                        @else
                            <b><u>[NAMA DIREKTUR]</u></b><br>
                            NIP. [NIP DIREKTUR]
                        @endif
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="6" class='text-center'>
                        Tidak ada data
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    
</body>
</html>
