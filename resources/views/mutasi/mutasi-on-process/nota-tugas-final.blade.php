<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Tugas - Mutasi Nota</title>
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

        $gelar_depan = $getVerifikasi->gelar_depan ? $getVerifikasi->gelar_depan . ". " : "";
        $gelar_belakang = $getVerifikasi->gelar_belakang ? " " . $getVerifikasi->gelar_belakang : "";
        $nama = $getVerifikasi->nama;
        $nama_lengkap = $gelar_depan . $nama . $gelar_belakang;

        $gelar_depan_direktur = $getDirektur->gelar_depan ? $getDirektur->gelar_depan . " " : "";
        $gelar_belakang_direktur = $getDirektur->gelar_belakang ? " " . $getDirektur->gelar_belakang : "";
        $nama_direktur = $getDirektur->nama;
        $nama_lengkap_direktur = $gelar_depan_direktur . $nama_direktur . $gelar_belakang_direktur;

        // if ($hasilLama['JENIS_TENAGA'] != "" && $hasilLama['RUANGAN'] != "") {
        //     $jenistenaga = "<br>".$hasilLama['JENIS_TENAGA']." PADA ".$hasilLama['RUANGAN'];
        // } else {
        //     $jenistenaga = "";
        // }
        $jenis_tenaga = $getDataLama->jenis_tenaga;
        $ruangan = $getDataLama->ruangan;

        if ($jenis_tenaga != "" && $ruangan != "") {
            $jenistenaga = "<br>" . $jenis_tenaga . " PADA " . $ruangan;
        } else {
            $jenistenaga = "";
        }

        // seperti pns, pppk, dll
        $kd_status_kerja = $getVerifikasi->kd_status_kerja;
        $status_kerja = "";

        if ($kd_status_kerja == 1) {
            $status_kerja = "NIP. " . $getVerifikasi->nip_baru . " - " . "ID Peg : " . $getVerifikasi->kd_karyawan . "<br>" . $getVerifikasi->pangkat . " (" . ($getVerifikasi->alias_gol_sekarang ?? '') . ")<br>" . $getVerifikasi->jab_fung;
        } else if ($kd_status_kerja == 7) {
            // NIPPPK. 199801022024050001 - ID Peg : 001635
            // Golongan VIII
            // Teknisi Elektromedis Penyelia
            // buat seperti bentuk di atas
            $status_kerja = "NIPPPK. " . $getVerifikasi->nip_baru . " - " . "ID Peg : " . $getVerifikasi->kd_karyawan . "<br>" . "Golongan " . $getVerifikasi->alias_gol_sekarang . "<br>" . $getVerifikasi->jab_fung;

            // $status_kerja = "NIPPPK. " . $getVerifikasi->nip_baru . " - " . "ID Peg : " . $getVerifikasi->kd_karyawan . "<br>" . $getVerifikasi->pangkat . " (" . ($getVerifikasi->alias_gol_sekarang ?? '') . ")<br>" . $getVerifikasi->jab_fung;
        } else {
            $status_kerja = "ID Peg : " . $getVerifikasi->kd_karyawan . "<br>" . $getVerifikasi->jab_fung;
        }

        $year = date('Y');
    @endphp

    <div class="all">
        <table class="text-center t-font" width="100%">
            <tr>
                <td width="110" style="vertical-align: middle; text-align: left">
                    <img src="{{ $logoLangsa }}" width="80" alt="">
                </td>
                <td style="vertical-align: middle">
                    <p>
                        <b style="font-size: 14pt; margin-bottom: 0px">
                            PEMERINTAH KOTA LANGSA
                        </b>
                        <br>
                        <b style="font-size: 16pt; margin-bottom: 0px; margin-top: 0px">
                            RUMAH SAKIT UMUM DAERAH LANGSA
                        </b>
                        <br>
                        <b style="font-size: 8pt; margin-top: 0px;font-weight: 500">
                            Alamat : Jln. Jend. A. Yani No.1 Kota Langsa - Provinsi Pemerintah Aceh, Telp. (0641) 22051 - 22800 (IGD)</b>
                            <br>
                        <b style="font-size: 8pt; margin-top: 0px;font-weight: 500">
                            Fax. (0641) 22051, E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,
                        </b>
                        <br>
                        <b style="font-size: 8pt; margin-top: 0px;font-weight: 500">
                            Website : www.rsud.langsakota.go.id
                        </b>
                        </br>
                    </p>
                </td>
                <td width="40"></td>
            </tr>
        </table>
        <div
            style="border-top: 2.5px solid #282828;border-bottom: 1px solid #282828;border-left: 0;border-right: 0; margin: 0px;padding-top:1px">
        </div>
        <label style="margin-top: 10px">
            <b style="font-size: 14pt; margin-bottom: 0px;">
                <u>NOTA TUGAS</u>
            </b>
        </label>
        <br>
        {{-- <span class="qr"></span> --}}
        <table class="table-print t-font" width="85%">
            <tr>
                <td style="width:20px;"></td>
                <td style="width:75px;"></td>
                <td style="width:10px;"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">
                    DARI
                </td>
                <td>:</td>
                <td colspan="2"> Direktur Rumah Sakit Umum Daerah Langsa</td>
            </tr>
            <tr>
                <td
                    colspan="2"
                    style="vertical-align:top;"
                >
                    KEPADA
                </td>
                <td style="vertical-align:top;">:</td>
                <td colspan="2" style="vertical-align: top;line-height: 20px;">
                    <b>
                        {{ $nama_lengkap }}
                        &nbsp;&nbsp;&nbsp;
                    </b>
                    <br>
                    {!! $status_kerja !!}
                    {!! $jenistenaga !!}
                </td>
            </tr>
            <tr>
                <td colspan="2">NOMOR</td>
                <td>:</td>
                <td colspan="2">
                    {{ $getVerifikasi->no_nota }}
                </td>
            </tr>
            <tr>
                <td colspan=2>TANGGAL</td>
                <td>:</td>
                <td colspan='2'>
                    {{ tanggal_indo($getVerifikasi->tmt_jabatan) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top;">
                    TEMBUSAN
                </td>
                <td style="vertical-align: top;">:</td>
                <td colspan="2" style="line-height: 25px;">
                    1. Para Wakil Direktur RSUD Langsa<br>2. Para Ka.Bid / Ka. Bag RSUD Langsa<br>3. Para Kepala SMF
                    RSUD Langsa<br>4. Para Ka. Sub Bag / Kasie RSUD Langsa<br>5. Para Kepala Instalasi / Ruangan / Unit
                    RSUD Langsa
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center;"><b>ISI</b></td>
            </tr>
            <tr>
                <td style="vertical-align: top;">1. </td>
                <td colspan="4" style="text-align: justify;">
                    {{-- {{ $getVerifikasi->isi_nota }} --}}
                    {!! $getVerifikasi->isi_nota !!}
                </td>
            </tr>
            <tr>
                <td>2.</td>
                <td colspan="4" style="text-align: justify;">
                    {{-- {{ $getVerifikasi->isi_nota_2 }} --}}
                    {!! $getVerifikasi->isi_nota_2 !!}
                </td>
            </tr>
            <tr>
                <td>3.</td>
                <td colspan="4" style="text-align: justify;">
                    Demikianlah untuk dimaklumi dan dilaksanakan dengan penuh
                    tanggung jawab.
                </td>
            </tr>
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td width="380px" style="text-align: center; line-height: 20px;">
                    Langsa, {{ tanggal_indo($getVerifikasi->tgl_ttd) }}<br>
                    <b>DIREKTUR RUMAH SAKIT UMUM DAERAH LANGSA</b><br>
                    <img
                        src="{{ storage_path('app/public/qr-code-mutasi-nota/' . $year . '/' . $getVerifikasi->kd_mutasi . '-' . $getVerifikasi->kd_karyawan . '.png') }}"
                        alt="QR Code"
                        style="width: 130px; height: 130px; margin-top: 10px; margin-bottom: 10px;"
                    /><br>
                    <b><u>{{ $nama_lengkap_direktur }}</u></b><br>
                    {{ $getDirektur->pangkat }} ({{ $getDirektur->alias_gol_sekarang }})<br>
                    NIP. {{ formatNIP($getDirektur->nip_baru) }}
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
                                src="{{ $logoEsign }}"
                                width='150px'
                            >
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </htmlpagefooter>
</body>
</html>