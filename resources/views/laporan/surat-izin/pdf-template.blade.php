<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Surat Izin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
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
            width: 100%;
        }

        .info-table td {
            padding: 2px 5px;
            font-size: 9px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 7px;
        }

        .data-table th,
        .data-table td {
            border: 0.5px solid #000;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7px;
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

        /* Column width specific */
        .col-no { width: 25px; }
        .col-nama { width: 110px; }
        .col-unit { width: 90px; }
        .col-periode { width: 90px; }
        .col-lama { width: 45px; }
        .col-jenis { width: 80px; }
        .col-kategori { width: 80px; }
        .col-keperluan { width: 100px; }

        /* Filter info styling */
        .filter-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 3px;
            font-size: 8px;
        }

        .filter-info strong {
            color: #495057;
        }

        /* Summary section */
        .summary {
            margin-top: 15px;
            border: 1px solid #000;
            padding: 8px;
        }

        .summary h4 {
            margin: 0 0 8px 0;
            text-align: center;
            font-size: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            border: 0.5px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 7px;
        }

        .summary-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        /* Signature section */
        .signature {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            margin-left: 50px;
        }

        .signature-line {
            width: 150px;
            border-bottom: 1px solid #000;
            margin: 60px auto 5px auto;
        }

        /* No data message */
        .no-data {
            text-align: center;
            font-style: italic;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.hospital_name', 'RSUD') }}</h2>
        <h3>LAPORAN SURAT IZIN KARYAWAN</h3>
        @if($filterInfo['ruangan_name'] || $filterInfo['kategori_name'] || $filterInfo['bulan_name'] || $filterInfo['tahun_name'])
            <h3>
                @if($filterInfo['ruangan_name'])
                    RUANGAN: {{ strtoupper($filterInfo['ruangan_name']) }}
                @endif
                @if($filterInfo['kategori_name'])
                    @if($filterInfo['ruangan_name']) | @endif
                    KATEGORI: {{ strtoupper($filterInfo['kategori_name']) }}
                @endif
                @if($filterInfo['bulan_name'])
                    @if($filterInfo['ruangan_name'] || $filterInfo['kategori_name']) | @endif
                    BULAN: {{ strtoupper($filterInfo['bulan_name']) }}
                @endif
                @if($filterInfo['tahun_name'])
                    @if($filterInfo['ruangan_name'] || $filterInfo['kategori_name'] || $filterInfo['bulan_name']) | @endif
                    TAHUN: {{ $filterInfo['tahun_name'] }}
                @endif
            </h3>
        @endif
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Tanggal Cetak:</strong></td>
            <td>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</td>
            <td style="padding-left: 30px;"><strong>Total Data:</strong></td>
            <td>{{ $data->count() }} surat izin</td>
        </tr>
    </table>

    @if($filterInfo['ruangan_name'] || $filterInfo['kategori_name'] || $filterInfo['bulan_name'] || $filterInfo['tahun_name'])
        <div class="filter-info">
            <strong>Filter yang Diterapkan:</strong>
            @if($filterInfo['ruangan_name'])
                Ruangan: {{ $filterInfo['ruangan_name'] }};
            @endif
            @if($filterInfo['kategori_name'])
                Kategori: {{ $filterInfo['kategori_name'] }};
            @endif
            @if($filterInfo['bulan_name'])
                Bulan: {{ $filterInfo['bulan_name'] }};
            @endif
            @if($filterInfo['tahun_name'])
                Tahun: {{ $filterInfo['tahun_name'] }};
            @endif
        </div>
    @endif

    @if($data->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-nama">Nama Karyawan</th>
                    <th class="col-unit">Unit Kerja</th>
                    <th class="col-periode">Periode Izin</th>
                    <th class="col-lama">Lama</th>
                    <th class="col-jenis">Jenis Surat</th>
                    <th class="col-kategori">Kategori</th>
                    <th class="col-keperluan">Alasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @php
                        $tglMulai = $item->tgl_mulai ? \Carbon\Carbon::parse($item->tgl_mulai) : null;
                        $tglSelesai = $item->tgl_akhir ? \Carbon\Carbon::parse($item->tgl_akhir) : null;
                        $lamaIzin = '';
                        
                        if ($tglMulai && $tglSelesai) {
                            $days = $tglMulai->diffInDays($tglSelesai) + 1;
                            $lamaIzin = $days . ' hari';
                        }
                        
                        $periodeIzin = '';
                        if ($tglMulai && $tglSelesai) {
                            $periodeIzin = $tglMulai->format('d/m/y') . ' s/d ' . $tglSelesai->format('d/m/y');
                        } elseif ($tglMulai) {
                            $periodeIzin = $tglMulai->format('d/m/y');
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            <strong>{{ $item->nama_karyawan ?? '-' }}</strong><br>
                            <small>{{ $item->nip_baru ?? ($item->no_karpeg ?? '-') }}</small>
                        </td>
                        <td class="text-left">{{ $item->unit_name ?? '-' }}</td>
                        <td class="text-center">{{ $periodeIzin ?: '-' }}</td>
                        <td class="text-center">{{ $lamaIzin ?: '-' }}</td>
                        <td class="text-left">{{ $item->nm_jenis_surat ?? '-' }}</td>
                        <td class="text-left">{{ $item->nm_kategori_izin ?? '-' }}</td>
                        <td class="text-left">
                            {{ $item->alasan ? (strlen($item->alasan) > 40 ? substr($item->alasan, 0, 40) . '...' : $item->alasan) : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary">
            <h4>REKAPITULASI BERDASARKAN KATEGORI IZIN</h4>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kategori Izin</th>
                        <th style="width: 80px;">Jumlah Surat</th>
                        <th style="width: 80px;">Total Hari Izin</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedByKategori = $data->groupBy('nm_kategori_izin');
                        $totalSurat = 0;
                        $totalHari = 0;
                    @endphp
                    @foreach($groupedByKategori as $kategori => $items)
                        @php 
                            $jumlahSurat = count($items);
                            $totalSurat += $jumlahSurat;
                            
                            $hariKategori = 0;
                            foreach($items as $item) {
                                if ($item->tgl_mulai && $item->tgl_akhir) {
                                    $start = \Carbon\Carbon::parse($item->tgl_mulai);
                                    $end = \Carbon\Carbon::parse($item->tgl_akhir);
                                    $hariKategori += $start->diffInDays($end) + 1;
                                }
                            }
                            $totalHari += $hariKategori;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $kategori ?: 'Tidak Diketahui' }}</td>
                            <td>{{ $jumlahSurat }} surat</td>
                            <td>{{ $hariKategori }} hari</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="2">TOTAL KESELURUHAN</td>
                        <td>{{ $totalSurat }} surat</td>
                        <td>{{ $totalHari }} hari</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary by Unit Kerja -->
        @if(!$filterInfo['ruangan_name'])
            <div class="summary" style="margin-top: 10px;">
                <h4>REKAPITULASI BERDASARKAN UNIT KERJA</h4>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">No</th>
                            <th>Unit Kerja</th>
                            <th style="width: 80px;">Jumlah Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedByUnit = $data->groupBy('unit_name');
                            $totalByUnit = 0;
                        @endphp
                        @foreach($groupedByUnit as $unit => $items)
                            @php $totalByUnit += count($items); @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $unit ?: 'Tidak Diketahui' }}</td>
                                <td>{{ count($items) }} surat</td>
                            </tr>
                        @endforeach
                        <tr style="background-color: #f0f0f0; font-weight: bold;">
                            <td colspan="2">TOTAL KESELURUHAN</td>
                            <td>{{ $totalByUnit }} surat</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

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
</body>
</html>
