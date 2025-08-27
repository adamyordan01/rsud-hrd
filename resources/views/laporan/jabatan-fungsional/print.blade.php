<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Jabatan Fungsional - {{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/skins/_all-skins.min.css') }}">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .main-header,
            .main-sidebar,
            .control-sidebar {
                display: none !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }

        .info-table {
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 2px 5px;
            font-size: 11px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }

        .data-table td.text-left {
            text-align: left;
        }

        .data-table td.text-right {
            text-align: right;
        }

        .data-table td.text-center {
            text-align: center;
        }

        /* Header styling */
        .data-table thead th {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .data-table thead th[colspan] {
            background-color: #d8d8d8;
        }

        /* Signature section */
        .signature {
            margin-top: 30px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            margin-left: 50px;
        }

        .signature-line {
            width: 200px;
            border-bottom: 1px solid #000;
            margin: 80px auto 5px auto;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }

        /* Summary section */
        .summary {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 10px;
        }

        .summary h4 {
            margin: 0 0 10px 0;
            text-align: center;
            font-size: 12px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .summary-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        /* No data message */
        .no-data {
            text-align: center;
            font-style: italic;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="content-wrapper">
        <div class="header">
            <h2>{{ config('app.hospital_name', 'RSUD') }}</h2>
            <h3>LAPORAN JABATAN FUNGSIONAL</h3>
            @if($bulan || $tahun)
                <h3>
                    @if($bulan && $tahun)
                        PERIODE: {{ $bulanNama }} {{ $tahun }}
                    @elseif($bulan)
                        BULAN: {{ $bulanNama }}
                    @elseif($tahun)
                        TAHUN: {{ $tahun }}
                    @endif
                </h3>
            @endif
        </div>

        <table class="info-table">
            <tr>
                <td><strong>Tanggal Cetak:</strong></td>
                <td>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</td>
                <td style="padding-left: 50px;"><strong>Total Data:</strong></td>
                <td>{{ $data->count() }} orang</td>
            </tr>
        </table>

        @if($data->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 30px;">No</th>
                        <th rowspan="2" style="width: 120px;">Nama Lengkap</th>
                        <th rowspan="2" style="width: 100px;">NIP</th>
                        <th rowspan="2" style="width: 70px;">Tgl Lahir</th>
                        <th rowspan="2" style="width: 40px;">Umur</th>
                        <th rowspan="2" style="width: 100px;">Ruangan</th>
                        <th colspan="3">Jabatan</th>
                        <th rowspan="2" style="width: 70px;">Tgl Masuk</th>
                        <th rowspan="2" style="width: 60px;">Masa Kerja</th>
                        <th rowspan="2" style="width: 80px;">Pendidikan</th>
                        <th rowspan="2" style="width: 80px;">Gaji Pokok</th>
                    </tr>
                    <tr>
                        <th style="width: 80px;">Struktural</th>
                        <th style="width: 90px;">Fungsional</th>
                        <th style="width: 60px;">Golongan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-left">{{ $item->nama_lengkap ?? '-' }}</td>
                            <td class="text-center">{{ $item->nip ?? '-' }}</td>
                            <td class="text-center">
                                @if($item->tanggal_lahir)
                                    {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->tanggal_lahir)
                                    {{ \Carbon\Carbon::parse($item->tanggal_lahir)->age }} tahun
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-left">{{ $item->ruangan ?? '-' }}</td>
                            <td class="text-left">{{ $item->jab_struktural ?? '-' }}</td>
                            <td class="text-left">{{ $item->jab_fung ?? '-' }}</td>
                            <td class="text-center">{{ $item->golongan ?? '-' }}</td>
                            <td class="text-center">
                                @if($item->tanggal_masuk)
                                    {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->tanggal_masuk)
                                    @php
                                        $masaKerja = \Carbon\Carbon::parse($item->tanggal_masuk)->diff(\Carbon\Carbon::now());
                                        $tahun = $masaKerja->y;
                                        $bulan = $masaKerja->m;
                                    @endphp
                                    {{ $tahun }}th {{ $bulan }}bl
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-left">{{ $item->pendidikan ?? '-' }}</td>
                            <td class="text-right">
                                @if($item->gaji_pokok)
                                    Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Section -->
            <div class="summary">
                <h4>REKAPITULASI BERDASARKAN RUANGAN</h4>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Ruangan/Unit Kerja</th>
                            <th style="width: 100px;">Jumlah Pegawai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedByRuangan = $data->groupBy('ruangan');
                            $totalPegawai = 0;
                        @endphp
                        @foreach($groupedByRuangan as $ruangan => $items)
                            @php $totalPegawai += count($items); @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $ruangan ?: 'Tidak Ada Ruangan' }}</td>
                                <td>{{ count($items) }} orang</td>
                            </tr>
                        @endforeach
                        <tr style="background-color: #f0f0f0; font-weight: bold;">
                            <td colspan="2">TOTAL KESELURUHAN</td>
                            <td>{{ $totalPegawai }} orang</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary by Functional Position -->
            <div class="summary" style="margin-top: 15px;">
                <h4>REKAPITULASI BERDASARKAN JABATAN FUNGSIONAL</h4>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Jabatan Fungsional</th>
                            <th style="width: 100px;">Jumlah Pegawai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedByJabatan = $data->groupBy('jab_fung');
                            $totalByJabatan = 0;
                        @endphp
                        @foreach($groupedByJabatan as $jabatan => $items)
                            @php $totalByJabatan += count($items); @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $jabatan ?: 'Tidak Ada Jabatan Fungsional' }}</td>
                                <td>{{ count($items) }} orang</td>
                            </tr>
                        @endforeach
                        <tr style="background-color: #f0f0f0; font-weight: bold;">
                            <td colspan="2">TOTAL KESELURUHAN</td>
                            <td>{{ $totalByJabatan }} orang</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @else
            <div class="no-data">
                <h3>Tidak ada data yang ditemukan</h3>
                <p>Silakan coba dengan filter yang berbeda</p>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature">
            <div class="signature-box">
                {{ config('app.city', 'Kota') }}, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>
                <strong>{{ config('app.position_title', 'Kepala Bagian HRD') }}</strong><br>
                <div class="signature-line"></div>
                <strong>{{ config('app.signature_name', 'Nama Kepala HRD') }}</strong><br>
                NIP. {{ config('app.signature_nip', '123456789012345678') }}
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 100);
        };

        // Close window after printing
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 100);
        };
    </script>
</body>
</html>
