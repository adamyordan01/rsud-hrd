<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/logo.png') }}" />
    <meta name="author" content="Kelvin Frasetia" />
    <title>Cetak Pegawai - Jenjang Pendidikan</title>
    <style>
        body {
            font-family: cambria;
            margin: 0;
            padding: 20px;
        }

        #all thead {
            display: table-header-group;
            font-size: 8pt;
        }

        .text-center {
            text-align: center;
        }

        #all td {
            font-size: 6pt;
        }

        .table-print {
            border-collapse: collapse;
            margin-top: 10px;
            width: 100%;
        }

        .table-print td {
            border-collapse: collapse;
            border: 1px solid #c4c4c4;
            padding: 1px;
        }

        .table-print thead {
            border-collapse: collapse;
        }

        .table-print thead th {
            border-collapse: collapse;
            border: 1px solid #c4c4c4;
            padding: 5px;
        }

        #all tfoot {
            display: table-footer-group;
        }

        .header_table {
            height: 100px;
        }

        .footer_table {
            height: 100px;
        }

        table thead {
            display: table-header-group;
        }

        .line2 {
            background: #282828;
            margin: 0px;
            margin-bottom: 5px;
            height: 1px !important;
        }

        @media print {
            @page {
                margin: 15px;
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

<body class="text-center" style="font-family: cambria" id="all" onLoad="cetak()">
    <table class="text-center t-font" style="margin-top: -20px; margin-bottom: -10px;" width="100%">
        <tr>
            <td width="140">
                <img src="{{ asset('assets/media/images/Langsa.png') }}" width="80" height="90">
            </td>
            <td style="vertical-align: top;">
                <p>
                    <b style="font-size: 12pt; margin-bottom: 0px;">PEMERINTAH KOTA LANGSA</b><br>
                    <b style="font-size: 17pt; margin-top: 0px; margin-bottom: 0px;">RUMAH SAKIT UMUM DAERAH
                        LANGSA</b><br>
                    <b style="font-size: 8.5pt; margin-top: 0px;">Alamat: Jln. Jend. A. Yani No.1 Kota Langsa – Provinsi
                        Pemerintah Aceh,</b><br>
                    <b style="font-size: 8pt; margin-top: 0px;">Telp. (0641) 22051 – 22800 (IGD) Fax. (0641)
                        22051</b><br>
                    <b style="font-size: 8pt; margin-top: 0px;">E-mail: rsudlangsa.aceh@gmail.com,
                        rsud@langsakota.go.id,</b><br>
                    <b style="font-size: 8pt; margin-top: 0px;">Website: www.rsud.langsakota.go.id</b><br>
                    <b style="font-size: 10pt; margin-top: 0px;">KOTA LANGSA</b>
                </p>
            </td>
            <td width="140"></td>
        </tr>
    </table>
    <hr style="background: #282828; margin: 0px; height: 3px !important;">
    </hr>
    <hr class="line2">
    </hr>

    <b style="font-size:10pt;">LAPORAN PEGAWAI BERDASARKAN JENJANG PENDIDIKAN - {{ $jenjangName }}
        @if($jurusanName)
            - {{ $jurusanName }}
        @endif
    </b>

    <table class="table-print t-font" width="100%">
        <thead>
            <tr>
                <th rowspan=3 style="text-align:center; vertical-align: middle;">No.</th>
                <th rowspan=3 style="text-align:center; vertical-align: middle;">Nama <br>Tempat & Tanggal Lahir<br>NIP
                    / No. KARPEG / ID Peg.</th>
                <th rowspan=3 style="text-align:center; vertical-align: middle;">L/P</th>
                <th rowspan=3 style="text-align:center; vertical-align: middle;"><label
                        style="text-decoration:underline;">NIK</label><br>No.ASKES/BPJS</th>
                <th colspan=4 style="text-align:center; vertical-align: middle;">Kepangkatan sekarang</th>
                <th colspan=3 style="text-align:center; vertical-align: middle;">Pendidikan terakhir</th>
                <th rowspan=3 style="text-align:center; vertical-align: middle;">Jenis tenaga</th>
                <th rowspan=3 style="text-align:center; vertical-align: middle;">Sub. Jenis tenaga<br>Ruangan</th>
                <th rowspan=3 style="text-align:center; vertical-align: middle;">Status kerja</th>
            </tr>
            <tr>
                <th rowspan=2 style="text-align:center; vertical-align: middle;">Pangkat / Gol.</th>
                <th rowspan=2 style="text-align:center; vertical-align: middle;" width="100">TMT</th>
                <th colspan=2 style="text-align:center; vertical-align: middle;">Masa kerja</th>
                <th rowspan=2 style="text-align:center; vertical-align: middle;">Jenjang</th>
                <th rowspan=2 style="text-align:center; vertical-align: middle;">Program studi</th>
                <th rowspan=2 style="text-align:center; vertical-align: middle;">Lulus tahun</th>
            </tr>
            <tr>
                <th>Thn.</th>
                <th>Bln.</th>
            </tr>
        </thead>
        <tbody>
            @if($karyawanData->isEmpty())
                <tr>
                    <td colspan='14' style='text-align:center;'>Tidak ada data yang ditemukan.</td>
                </tr>
            @else
                @foreach($karyawanData as $index => $data)
                    @php
                        $tmtgol = $data->tmt_gol_sekarang ? \Carbon\Carbon::parse($data->tmt_gol_sekarang)->format('d-m-Y') : "-";
                        $tglahir = $data->tgl_lahir ? \Carbon\Carbon::parse($data->tgl_lahir)->format('d-m-Y') : "-";
                        $jenis_kelamin = $data->jenis_kelamin === 'Pria' ? 'L' : ($data->jenis_kelamin === 'Wanita' ? 'P' : '?');
                        
                        // Format jenis tenaga berdasarkan eselon
                        $eselon = $data->eselon ?? '';
                        $jenisTenaga = ($eselon == '-' || $eselon == '' || $eselon == null) 
                            ? 'Tenaga ' . ($data->sub_detail ?? '')
                            : 'TENAGA MANAJEMEN';
                    @endphp
                    <tr>
                        <td style='text-align:center;'>{{ $index + 1 }}</td>
                        <td style='text-align:left;'>
                            <b>{{ $data->gelar_depan }} {{ $data->nama }}{{ $data->gelar_belakang }}</b><br>
                            {{ $data->tempat_lahir }}, {{ $tglahir }}<br>
                            {{ $data->nip_baru }}<br>
                            {{ $data->no_karpeg }} / {{ $data->kd_karyawan }}
                        </td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $jenis_kelamin }}</td>
                        <td style='text-align:left;'>
                            <p style='text-decoration:underline;'>{{ $data->no_ktp ?? '-' }}</p>
                            {{ $data->no_askes ?? '-' }}
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if($data->pangkat && $data->kd_gol_sekarang)
                                {{ $data->pangkat }} / {{ $data->kd_gol_sekarang }}
                            @else
                                -
                            @endif
                        </td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $tmtgol }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $data->masa_kerja_thn ?? '-' }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $data->masa_kerja_bulan ?? '-' }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $data->jenjang_didik ?? '-' }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $data->jurusan ?? '-' }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $data->tahun_lulus ?? '-' }}</td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $data->jenis_tenaga ?? '-' }}</td>
                        <td style='text-align:center;font-size:6pt;text-transform:uppercase;'>
                            {{ strtoupper($jenisTenaga) }}<br>
                            Pada {{ $data->ruangan ?? '-' }}
                        </td>
                        <td style='text-align:center; vertical-align: middle;'>{{ $data->status_kerja ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    {{-- <p style="text-align: right; margin-top: 20px; font-size: 10pt;">
        <b>Total Pegawai: {{ $totalKaryawan }} Orang</b><br>
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->format('d-m-Y H:i:s') }}<br>
    </p> --}}
</body>

</html>