<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Pegawai Kontrak</title>
    <style>
        @page {
            footer: page-footer;
        }
        .all {
            font-family: bookman-old-style;
            text-align: center;
            font-size: 11pt !important;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .t-font {
            font-family: bookman-old-style;
        }

        .table-print {
            border-collapse: collapse;
            margin: 0 auto;
            margin-top: 5px;
            text-align: left;
            font-family: bookman-old-style;
        }

        .table-print td {
            border-collapse: collapse;
            padding: 5px;
            font-size: 11pt !important;
            font-size: 10pt;
        }

        .table-print {
            border-collapse: collapse;
        }

        .table-print thead th {
            border-collapse: collapse;
            border: 1px solid #c4c4c4;
            padding: 5px;
        }

        .line2 {
            background: #282828;
            margin: 0px;
            margin-bottom: 15px;
            border: 0.1px solid #282828;
        }

        .footer {
            position: fixed;
            width: 120px;
            left: 55px;
            bottom: 10px;
            border: 1px solid;
        }

        .img {
            width: 120px;
            height: 120px;
        }

        .qr {
            position: absolute;
            top: 120px;
            right: 10px;
        }

        .p-5 {
            padding-left: 5px;
        }

        .flex-container {
            display: flex;
        }
    </style>
</head>
<body>
    @php
        use App\Helpers\HijriDateHelper;
    @endphp
    @foreach ($results as $result)
        <div class="all">
            <table class="text-center t-font" width="100%">
                <tr>
                    <td width="110" style="vertical-align: middle; text-align: left">
                        <img src="{{ $logoLangsa }}" width="80" alt="">
                    </td>
                    <td style="vertical-align: middle">
                        <p>
                            <b style="font-size: 20pt; margin-bottom: 0px">
                                PEMERINTAH KOTA LANGSA
                            </b>
                            <br>
                            <b style="font-size: 20pt; margin-bottom: 0px; margin-top: 0px">
                                RUMAH SAKIT UMUM DAERAH LANGSA
                            </b>
                        </p>
                    </td>
                    <td width="40"></td>
                </tr>
            </table>
            <div style="border-top: 2.5px solid #282828; border-bottom: 1px solid #282828; border-left: 0; border-right: 0; padding-top: 1px; margin-bottom: 10px"></div>
            <div class="">
                <p style="margin: 0; padding: 0;">
                    PETIKAN <br>
                    KEPUTUSAN DIREKTUR RUMAH SAKIT UMUM DAERAH LANGSA <br>
                    NOMOR : Peg. 445/{{ $result->no_sk }}/SK/{{ $tahun }}
                </p>
                <p style="margin: 0; padding: 8px;">
                    TENTANG
                </p>
                <p style="margin: 0; padding: 0;">
                    PENGANGKATAN TENAGA KONTRAK <br>
                    PADA RUMAH SAKIT UMUM DAERAH LANGSA
                </p>
                <p style="margin: 0; padding: 8px;">
                    TAHUN {{ $tahun }}
                </p>
                DIREKTUR RUMAH SAKIT UMUM DAERAH LANGSA
            </div>
            <table width="100%" style="margin-top:20px;">
                <tr>
                    <td width="115px">Menimbang</td>
                    <td class="text-center" width="10px">:</td>
                    <td colspan="3" class="p-5">dst.</td>
                </tr>
                <tr>
                    <td>Mengingat</td>
                    <td class="text-center">:</td>
                    <td colspan="3" class="p-5">dst.</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center">MEMUTUSKAN : </td>
                </tr>
                <tr>
                    <td>KESATU</td>
                    <td class="text-center">:</td>
                    <td class="p-5" colspan="3">Menunjuk/mengangkat yang tersebut dibawah ini, ID Peg. {{ $result->kd_karyawan }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center"></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center"></td>
                    <td class="p-5" width="200">Nama</td>
                    <td class="text-center" width="10px">:</td>
                    <td class="p-5"><b>{{ $result->gelar_depan }} {{ $result->nama }}{{ $result->gelar_belakang }}</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center"></td>
                    <td class="p-5">Tempat/Tanggal Lahir</td>
                    <td class="text-center" width="10px">:</td>
                    <td class="p-5">{{ $result->tempat_lahir }}, {{ tanggal_indo($result->tgl_lahir) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center"></td>
                    <td class="p-5">Jenis Kelamin</td>
                    <td class="text-center" width="10px">:</td>
                    <td class="p-5">{{ $result->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center"></td>
                    <td class="p-5">Pendidikan</td>
                    <td class="text-center" width="10px">:</td>
                    <td class="p-5">{{ $result->jenjang_didik }} - {{ $result->jurusan }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center"></td>
                    <td class="p-5">Unit Kerja</td>
                    <td class="text-center" width="10px">:</td>
                    <td class="p-5">Rumah Sakit Umum Daerah Langsa</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center"></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center"></td>
                    <td colspan="3" class="p-5 text-justify">Keputusan ini berlaku Terhitung Mulai Tanggal <b>{{ tanggal_indo($result->tgl_sk) }}</b> sampai dengan <b>31 Desember {{ $result->tahun_sk }}</b> Untuk melaksanakan tugas sebagai Tenaga Kontrak pada Rumah Sakit Umum Daerah Langsa dan kepadanya diberikan upah sesuai dengan kemampuan keuangan RSUD Langsa.</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center"></td>
                </tr>
                <tr>
                    <td style="vertical-align:top">KEDUA</td>
                    <td class="text-center" style="vertical-align:top">:</td>
                    <td colspan="3" class="p-5 text-justify">Dalam melaksanakan tugasnya nama yang tersebut pada diktum KESATU bertanggung jawab kepada Direktur Rumah Sakit Umum Daerah Langsa secara berjenjang.</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center"></td>
                </tr>
                <tr>
                    <td style="vertical-align:top">KETIGA</td>
                    <td class="text-center" style="vertical-align:top">:</td>
                    <td colspan="3" class="p-5 text-justify">Segala biaya akibat ditetapkannya keputusan ini dibebankan kepada Anggaran Badan Layanan Umum Daerah Rumah Sakit Umum Daerah Langsa {{ $tahun }}.</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center"></td>
                </tr>
                <tr>
                    <td style="vertical-align:top">KEEMPAT</td>
                    <td class="text-center" style="vertical-align:top">:</td>
                    <td colspan="3" class="p-5 text-justify">Petikan Keputusan ini diberikan kepada yang bersangkutan dan yang berkepentingan untuk diketahui dan digunakan sebagaimana mestinya dengan ketentuan apabila dikemudian hari terdapat kekeliruan dalam penetapan ini akan diadakan perbaikan sebagaimana mestinya.</td>
                </tr>
            </table>
            <table width="100%">
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td width="250px"></td>
                    <td width="120px">Ditetapkan di </td>
                    <td colspan="2">Langsa</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Pada Tanggal</td>
                    <td class="p-5" width="220px" style="border-bottom:1px solid">{{ tanggal_indo($result->tgl_ttd) }} M</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="p-5">{{ convertToHijriah($result->tgl_ttd) }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="text-center">
                        DIREKTUR RUMAH SAKIT UMUM DAERAH LANGSA,
                        <br>
                        <img
                            style="width: 130px; height: 130px; margin-top: 10px; margin-bottom: 10px;"
                            src="{{ storage_path('app/public/qr-code/' . $result->no_sk . '-' . $result->tahun_sk . '-' . $result->kd_karyawan . '.png') }}"
                        />
                        <br>
                        {{ $direktur->nama }}
                    </td>
                </tr>
            </table>
        </div>

        <htmlpagefooter name="page-footer">
            <div style='width: 100%' class='flex-container'>
                <div align='left' style='width: 75%;float: left;'>
                    <table style='font-size: 10px'>
                        <tr>
                            <td>Catatan:</td>
                        </tr>
                        <tr>
                            <td>
                                <ul>
                                    <li>UU ITE No. 11 Tahun 2008 Pasal 5 ayat 1 'Informasi Elektronik dan/atau Dokumen
                                        Elektronik dan/atau hasil cetaknya merupakan alat dan bukti hukum yang sah'</li>
                                    <li>Dokumen ini tertanda ditandatangani secara elektronik menggunakan sertifikat
                                        elektronik yang diterbitkan BSrE</li>
                                    <li>Surat ini dapat dibuktikan keasliannya dengan menggunakan qr code yang telah
                                        tersedia</li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </div>
                <div align='left' style='width: 25%;float: left;'>
                    <table>
                        <tr>
                            <td>
                                <img
                                {{-- C:\laragon\www\rsud_hrd\public\assets\media\rsud-langsa --}}
                                    src="{{ $logoEsign }}"
                                    width='150px'
                                >
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </htmlpagefooter>
    @endforeach
</body>
</html>