<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/Langsa.png') }}" />
    <meta name="author" content="RSUD Langsa" />
    <title>HRD RSUD LANGSA - Daftar Hadir Pegawai</title>
    
    <style>
        body {
            font-family: cambria;
            font-size: 12px;
            margin: 0;
            padding: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .absen-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 6pt;
        }
        
        .absen-table td {
            border: 1px solid #c4c4c4;
            padding: 1px;
            text-align: center;
            vertical-align: middle;
        }
        
        .absen-table th {
            border: 1px solid #c4c4c4;
            padding: 5px;
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 8pt;
            text-align: center;
            vertical-align: middle;
        }
        
        .employee-data {
            text-align: left !important;
            font-size: 6pt;
        }
        
        .employee-name {
            font-weight: bold;
        }
        
        table thead {
            display: table-header-group;
        }
        
        .page-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .room-info {
            font-size: 10pt;
            margin-bottom: 10px;
            text-align: left;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 5px;
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
<body onLoad="cetak()">
    
    <div class="page-title text-center">
        <strong>DAFTAR HADIR PEGAWAI NEGERI SIPIL, HONORER, KONTRAK RSUD LANGSA</strong><br>
        BULAN: {{ $dataBulan[$bulan] ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TAHUN: {{ $tahun }}
    </div>
    
    <div class="room-info">
        Ruangan: {{ $ruanganData->ruangan ?? '-' }}
    </div>
    
    <table class="absen-table">
        <thead>
            <tr>
                <th width="10px" rowspan="3">No.</th>
                <th rowspan="3">ID. PEG<br>NAMA PEGAWAI<br>NIP</th>
                <th rowspan="3">Jabatan</th>
                <th rowspan="3">Jenis Tenaga</th>
                <th colspan=4 style="text-align: left">Tanggal: </th>
                <th colspan=4 style="text-align: left">Tanggal: </th>
                <th colspan=4 style="text-align: left">Tanggal: </th>
                <th colspan=4 style="text-align: left">Tanggal: </th>
                <th colspan=4 style="text-align: left">Tanggal: </th>
                <th colspan=4 style="text-align: left">Tanggal: </th>
                <th colspan=4 style="text-align: left">Tanggal: </th>
                <th rowspan="3">Status</th>
            </tr>
            <tr>
                <th colspan="2">Masuk</th>
                <th colspan="2">Pulang</th>
                <th colspan="2">Masuk</th>
                <th colspan="2">Pulang</th>
                <th colspan="2">Masuk</th>
                <th colspan="2">Pulang</th>
                <th colspan="2">Masuk</th>
                <th colspan="2">Pulang</th>
                <th colspan="2">Masuk</th>
                <th colspan="2">Pulang</th>
                <th colspan="2">Masuk</th>
                <th colspan="2">Pulang</th>
                <th colspan="2">Masuk</th>
                <th colspan="2">Pulang</th>
            </tr>
            <tr>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
                <th><i>Jam</i></th>
                <th><i>Paraf</i></th>
            </tr>
        </thead>
        <tbody>
            @if($dataPegawai->count() > 0)
                @php $no = 0; @endphp
                @foreach($dataPegawai as $pegawai)
                    @php
                        $gelarDepan = $pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '';
                        $gelarBelakang = $pegawai->gelar_belakang ? $pegawai->gelar_belakang : '';
                        $namaLengkap = $gelarDepan . $pegawai->nama . $gelarBelakang;
                    @endphp
                    <tr>
                        <td class='text-center'>{{ ++$no }}</td>
                        <td class="employee-data">
                            {{ $pegawai->kd_karyawan }}<br>
                            <span class="employee-name">{{ $namaLengkap }}</span><br>
                            {{ $pegawai->nip_baru ?? '-' }}
                        </td>
                        <td class='text-center'>{{ $pegawai->jab_struk ?? '-' }}</td>
                        <td class='text-center'>{{ $pegawai->sub_detail ?? '-' }}</td>
                        
                        <!-- 28 kolom kosong untuk absensi 7 hari -->
                        <td></td><td></td><td></td><td></td>
                        <td></td><td></td><td></td><td></td>
                        <td></td><td></td><td></td><td></td>
                        <td></td><td></td><td></td><td></td>
                        <td></td><td></td><td></td><td></td>
                        <td></td><td></td><td></td><td></td>
                        <td></td><td></td><td></td><td></td>
                        
                        <td class='text-center'>{{ $pegawai->status_kerja ?? '-' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="32" class='text-center'>Tidak ada data pegawai</td>
                </tr>
            @endif
        </tbody>
    </table>
    
</body>
</html>
