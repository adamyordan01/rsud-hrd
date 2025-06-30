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
    <div class="all" style="margin-top: 70px !important;">
        <table width="90%" style="margin:0 auto;margin-top:50px;">
            <tr>
                <td style="vertical-align:top;">(4)</td>
                <td class="p-5 text-justify" colspan="2">Selama hubungan kerja berlangsung, Pihak Kedua berkewajiban
                    sebagai berikut:</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;" width="10px">a. </td>
                <td class="p-5 text-justify">Hadir tepat waktu sesuai ketentuan yang berlaku di Rumah Sakit Umum Daerah
                    Langsa;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">b. </td>
                <td class="p-5 text-justify">Berpakaian seragam sesuai ketentuan dengan rapi dan sopan;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">c. </td>
                <td class="p-5 text-justify">Berkoordinasi dan bekerjasama dengan Pegawai yang lain;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">d. </td>
                <td class="p-5 text-justify">Mengisi daftar hadir setiap hari kerja; dan</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">e. </td>
                <td class="p-5 text-justify">Membuat laporan tertulis terkait pekerjaan atau kegiatan yang dilaksanakan
                    sesuai tupoksi yang telah ditetapkan.</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="text-center">Pasal 2<br>Hak - hak</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(1)</td>
                <td class="p-5 text-justify" colspan="2">Selama menjadi Tenaga Kontrak di Rumah Sakit Umum Daerah
                    Langsa, seperti tercantum pada pasal 1 surat perjanjian ini, Pihak Kedua akan menerima upah sesuai
                    dengan kemampuan keuangan Rumah Sakit Umum Daerah Langsa.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(2)</td>
                <td class="p-5 text-justify" colspan="2">Selama hubungan kerja berlangsung, Pihak Pertama mempunyai hak
                    sebagai berikut:</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">a. </td>
                <td class="p-5 text-justify">Menetapkan tugas, pokok, dan fungsi Pihak Kedua;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">b. </td>
                <td class="p-5 text-justify">Mengevaluasi dan mengawasi kinerja dan etika Pihak Kedua; dan</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">c. </td>
                <td class="p-5 text-justify">Memperoleh kinerja yang maksimal dari Pihak Kedua.</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="text-center">Pasal 3<br>Jangka Waktu dan Berakhirnya Perjanjian</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(1)</td>
                <td class="p-5 text-justify" colspan="2">
                    {{-- Surat Perjanjian Kerja Tenaga Kontrak ini berlaku untuk jangka
                    waktu 1 (satu) tahun terhitung mulai tanggal 1 Januari ".$_GET["thn']." sampai dengan tanggal 31
                    Desember ".$_GET['thn'].". --}}
                    Surat Perjanjian Kerja Tenaga Kontrak ini berlaku untuk jangka waktu 1 (satu) tahun terhitung mulai tanggal 1 Januari {{ date('Y', strtotime($result->tgl_sk)) }} sampai dengan tanggal 31 Desember {{ date('Y', strtotime($result->tgl_sk)) }}.
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(2)</td>
                <td class="p-5 text-justify" colspan="2">Perjanjian Kerja Tenaga Kontrak berakhir demi hukum dengan
                    telah berakhirnya waktu perjanjian kerja yang ditentukan.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(3)</td>
                <td class="p-5 text-justify" colspan="2">Perjanjian Kerja Tenaga Kontrak berakhir karena Pihak Kedua
                    mengundurkan diri.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(4)</td>
                <td class="p-5 text-justify" colspan="2">Perjanjian Kerja Tenaga Kontrak berakhir karena Pihak Kedua
                    meninggal dunia.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(5)</td>
                <td class="p-5 text-justify" colspan="2">Perjanjian Kerja Tenaga Kontrak berakhir karena Pihak Kedua
                    melanggar dan tidak mematuhi peraturan/ ketentuan yang berlaku di Rumah Sakit Umum Daerah Langsa.
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(6)</td>
                <td class="p-5 text-justify" colspan='2'>Adanya Keputusan Pengadilan atau Putusan Penetapan Lembaga yang
                    telah mempunyai kekuatan hukum tetap.</td>
            </tr>
        </table>
    </div>
    
    {{-- <htmlpagefooter name="page-footer">
        <table width="100%" style="border-collapse:collapse;position:fixed;bottom:0">
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td width="550px"></td>
                <td colspan="2" class="text-center" style="border:1px solid">Paraf</td>
            </tr>
            <tr>
                <td width="550px"></td>
                <td class="text-center" style="border:1px solid">Pihak 1</td>
                <td class="text-center" style="border:1px solid">Pihak 2</td>
            </tr>
            <tr>
                <td width="550px"></td>
                <td class="text-center" style="border:1px solid;padding:10px">&nbsp;</td>
                <td class="text-center" style="border:1px solid;padding:10px">&nbsp;</td>
            </tr>
        </table>
    </htmlpagefooter> --}}

</body>
</html>