<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/media/images/logo.png') }}" />
    <meta name="author" content="HRD RSUD LANGSA" />
    <title>Daftar Urut Kepangkatan - {{ $dataBulan[$bulan] ?? 'Sekarang' }} {{ $tahun }}</title>
    
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
            font-size: 7pt;
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
            height: 2px;
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
        <div style="font-size: 12pt; font-weight: bold;">DAFTAR URUT KEPANGKATAN</div>
        <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">
            PERIODE {{ $dataBulan[$bulan] ?? 'SEKARANG' }} {{ $tahun }}
        </div>
    </div>

    {{-- Table Data --}}
    <table class="table-print">
        <thead>
            <tr>
                <th rowspan="3" style="width: 30px;">No.</th>
                <th rowspan="3" style="width: 150px;">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG</th>
                <th rowspan="3" style="width: 25px;">L/P</th>
                <th colspan="2">Kepangkatan CPNS</th>
                <th colspan="4">Kepangkatan Sekarang</th>
                <th colspan="2">Eselon</th>
                <th rowspan="3" style="width: 100px;">Pendidikan Terakhir</th>
                <th rowspan="3" style="width: 60px;">Lulus Tahun</th>
            </tr>
            <tr>
                <th rowspan="2" style="width: 80px;">Pangkat / Gol.</th>
                <th rowspan="2" style="width: 70px;">TMT</th>
                <th rowspan="2" style="width: 80px;">Pangkat / Gol.</th>
                <th rowspan="2" style="width: 70px;">TMT</th>
                <th colspan="2">Masa Kerja</th>
                <th rowspan="2" style="width: 80px;">Nama</th>
                <th rowspan="2" style="width: 70px;">TMT</th>
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
                    $jenis = $row->jenis == 'Pria' ? 'L' : 'P';
                    
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
                        {{ $row->no_karpeg ?? '-' }}
                    </td>
                    <td class="text-center">{{ $jenis }}</td>
                    <td class="text-center">{{ ($row->pangkat_masuk ?? '-') . ' / ' . ($row->kd_gol_masuk ?? '-') }}</td>
                    <td class="text-center">{{ $tmtGolMasuk }}</td>
                    <td class="text-center">{{ ($row->pangkat_sekarang ?? '-') . ' / ' . ($row->kd_gol_sekarang ?? '-') }}</td>
                    <td class="text-center">{{ $tmtGol }}</td>
                    <td class="text-center">{{ $row->masa_kerja_thn ?? '0' }}</td>
                    <td class="text-center">{{ $row->masa_kerja_bulan ?? '0' }}</td>
                    <td class="text-center">{{ $row->eselon ?? '-' }}</td>
                    <td class="text-center">{{ $tmtEselon }}</td>
                    <td class="text-center">
                        {{ $row->jenjang_didik ?? '-' }}<br>
                        {{ $row->jurusan ?? '-' }}
                    </td>
                    <td class="text-center">{{ $row->tahun_lulus ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak Ada Data</td>
                </tr>
            @endforelse
            
            {{-- Signature Section --}}
            <tr>
                <td colspan="6" style="border: 0px; text-align: center;"></td>
                <td colspan="7" style="border: 0px; text-align: center; line-height: 25px; font-size: 11pt; width: 270px;">
                    Langsa, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
                    Plt. DIREKTUR RUMAH SAKIT UMUM DAERAH LANGSA<br><br><br>
                    
                    @if($direktur)
                        <strong>
                            <u>{{ ($direktur->gelar_depan ? $direktur->gelar_depan . ' ' : '') . $direktur->nama . ($direktur->gelar_belakang ?? '') }}</u>
                        </strong>
                        <div style="line-height: 0.7;">
                            {{ $direktur->PANGKAT ?? '' }}
                        </div>
                        <div>
                            NIP. {{ $direktur->nip_baru ?? '' }}
                        </div>
                        <div style="line-height: 1.2;">
                            SPMT Nomor: 800.1.11.1/6784/2023<br>Tgl. 18 Desember 2023
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>