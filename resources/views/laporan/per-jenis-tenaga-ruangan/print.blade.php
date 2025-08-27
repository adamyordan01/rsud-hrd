<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/Langsa.png') }}" />
    <meta name="author" content="RSUD Langsa" />
    <title>HRD RSUD LANGSA - Laporan Jumlah Pegawai Per-Jenis Tenaga Per-Ruangan</title>
    
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
            text-transform: uppercase;
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
        
        .ruangan-header {
            font-weight: bold;
            background-color: #e8e8e8;
            font-size: 8pt;
        }
        
        .group-header {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .subtotal-row {
            font-weight: bold;
            background-color: #f8f8f8;
        }
        
        .ruangan-total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .grand-total-row {
            font-weight: bold;
            background-color: #e8e8e8;
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
        <b style='font-size:10pt;'>JUMLAH PEGAWAI PER-JENIS TENAGA PER-RUANGAN</b><br>
        <b style='font-size:10pt;'>PERIODE {{ $periodeName }}</b>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">JENIS TENAGA</th>
                <th colspan="6">STATUS</th>
                <th colspan="2">JENIS KELAMIN</th>
            </tr>
            <tr>
                <th>PNS</th>
                <th>PPPK</th>
                <th>PART TIME</th>
                <th>KONTRAK DAERAH</th>
                <th>KONTRAK BLUD</th>
                <th>THL</th>
                <th>LAKI-LAKI</th>
                <th>PEREMPUAN</th>
            </tr>
        </thead>
        <tbody>
            @if(count($result) > 0)
                @foreach($result as $ruangan)
                    <!-- Header Ruangan -->
                    <tr class="ruangan-header">
                        <td colspan="10" class="text-center">{{ $ruangan['ruangan'] }}</td>
                    </tr>
                    
                    @foreach($ruangan['jenis_tenaga_groups'] as $group)
                        <!-- Header Jenis Tenaga -->
                        <tr class="group-header">
                            <td colspan="10" class="text-left">TENAGA {{ $group['jenis_tenaga'] }}</td>
                        </tr>
                        
                        @php $no = 1; @endphp
                        @foreach($group['details'] as $item)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="text-left">{{ $item['sub_detail'] }}</td>
                                <td class="text-center">{{ $item['pns'] }}</td>
                                <td class="text-center">{{ $item['pppk'] }}</td>
                                <td class="text-center">{{ $item['part_time'] }}</td>
                                <td class="text-center">{{ $item['kontrak_daerah'] }}</td>
                                <td class="text-center">{{ $item['kontrak_blud'] }}</td>
                                <td class="text-center">{{ $item['thl'] }}</td>
                                <td class="text-center">{{ $item['lk'] }}</td>
                                <td class="text-center">{{ $item['pr'] }}</td>
                            </tr>
                        @endforeach
                        
                        <!-- Subtotal per jenis tenaga -->
                        <tr class="subtotal-row">
                            <td colspan="2" class="text-center">JUMLAH</td>
                            <td class="text-center">{{ $group['subtotal']['pns'] }}</td>
                            <td class="text-center">{{ $group['subtotal']['pppk'] }}</td>
                            <td class="text-center">{{ $group['subtotal']['part_time'] }}</td>
                            <td class="text-center">{{ $group['subtotal']['kontrak_daerah'] }}</td>
                            <td class="text-center">{{ $group['subtotal']['kontrak_blud'] }}</td>
                            <td class="text-center">{{ $group['subtotal']['thl'] }}</td>
                            <td class="text-center">{{ $group['subtotal']['lk'] }}</td>
                            <td class="text-center">{{ $group['subtotal']['pr'] }}</td>
                        </tr>
                    @endforeach
                    
                    <!-- Total per ruangan -->
                    <tr class="ruangan-total-row">
                        <td colspan="2" class="text-center">TOTAL {{ $ruangan['ruangan'] }}</td>
                        <td class="text-center">{{ $ruangan['ruangan_total']['pns'] }}</td>
                        <td class="text-center">{{ $ruangan['ruangan_total']['pppk'] }}</td>
                        <td class="text-center">{{ $ruangan['ruangan_total']['part_time'] }}</td>
                        <td class="text-center">{{ $ruangan['ruangan_total']['kontrak_daerah'] }}</td>
                        <td class="text-center">{{ $ruangan['ruangan_total']['kontrak_blud'] }}</td>
                        <td class="text-center">{{ $ruangan['ruangan_total']['thl'] }}</td>
                        <td class="text-center">{{ $ruangan['ruangan_total']['lk'] }}</td>
                        <td class="text-center">{{ $ruangan['ruangan_total']['pr'] }}</td>
                    </tr>
                @endforeach
                
                <!-- Grand Total -->
                <tr class="grand-total-row">
                    <td colspan="2" class="text-center">GRAND TOTAL</td>
                    <td class="text-center">{{ $grandTotal['pns'] }}</td>
                    <td class="text-center">{{ $grandTotal['pppk'] }}</td>
                    <td class="text-center">{{ $grandTotal['part_time'] }}</td>
                    <td class="text-center">{{ $grandTotal['kontrak_daerah'] }}</td>
                    <td class="text-center">{{ $grandTotal['kontrak_blud'] }}</td>
                    <td class="text-center">{{ $grandTotal['thl'] }}</td>
                    <td class="text-center">{{ $grandTotal['lk'] }}</td>
                    <td class="text-center">{{ $grandTotal['pr'] }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="10" class="text-center">Tidak Ada Data</td>
                </tr>
            @endif
        </tbody>
    </table>
    
</body>
</html>
