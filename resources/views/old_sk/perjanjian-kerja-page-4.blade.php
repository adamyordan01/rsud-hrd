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
        use App\Helpers\Denominator;

        $tanggal = (int) date('d', strtotime($result->tgl_sk));
        // var_dump(date('d', strtotime($result->tgl_sk)));
        // var_dump($tanggal);
        // die;

        // buat hari, tanggal, bulan, tahun dalam bentuk seperti tanggal senin tujuh januari dua ribu dua puluh empat dari $result->tgl_sk
        // $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        // $bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];

        // // buatkan fungsi penyebut untuk mengubah angka menjadi huruf
        // function penyebut($nilai) {
        //     $huruf = array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas');
        //     if ($nilai < 12) {
        //         return ' ' . $huruf[$nilai];
        //     } elseif ($nilai < 20) {
        //         return penyebut($nilai - 10) . ' Belas';
        //     } elseif ($nilai < 100) {
        //         return penyebut($nilai / 10) . ' Puluh' . penyebut($nilai % 10);
        //     } elseif ($nilai < 200) {
        //         return ' Seratus' . penyebut($nilai - 100);
        //     } elseif ($nilai < 1000) {
        //         return penyebut($nilai / 100) . ' Ratus' . penyebut($nilai % 100);
        //     } elseif ($nilai < 2000) {
        //         return ' Seribu' . penyebut($nilai - 1000);
        //     } elseif ($nilai < 1000000) {
        //         return penyebut($nilai / 1000) . ' Ribu' . penyebut($nilai % 1000);
        //     } elseif ($nilai < 1000000000) {
        //         return penyebut($nilai / 1000000) . ' Juta' . penyebut($nilai % 1000000);
        //     }
        // }

        // $hari_ttd = tanggal_indo($result->tgl_sk);

        // $cetak_hari = $hari[date('w', strtotime($result->tgl_sk))];
        // $cetak_bulan = $bulan[date('m', strtotime($result->tgl_sk))];

        $gelar_depan_direktur = $direktur->gelar_depan ? $direktur->gelar_depan . ' ' : '';
        $gelar_belakang_direktur = $direktur->gelar_belakang ? $direktur->gelar_belakang : '';
        $nama_direktur = $gelar_depan_direktur . $direktur->nama . $gelar_belakang_direktur;

        $gelar_depan_karyawan = $result->gelar_depan ? $result->gelar_depan . ' ' : '';
        $gelar_belakang_karyawan = $result->gelar_belakang ? $result->gelar_belakang : '';
        $nama_karyawan = $gelar_depan_karyawan . $result->nama . $gelar_belakang_karyawan;
    @endphp
    
    <div class="all">
        <table width="90%" style="margin:0 auto;margin-top:20px;">
            <tr>
                <td colspan="3" class="text-center">Pasal 6<br>Perpanjangan Kontrak</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td class="p-5 text-justify" colspan="3">
                    Perpanjangan kontrak kerja Tenaga Kontrak dilakukan berdasarkan hasil evaluasi kinerja dan
                    permohonan perpanjangan dari Pihak Kedua.</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="text-center">Pasal 7<br>Penutup</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;" width="10px">(1)</td>
                <td class="p-5 text-justify" colspan="2" width="10px">Surat Perjanjian Kerja Tenaga Kontrak ini dibuat
                    dan ditanda tangani oleh kedua belah pihak dalam rangkap dua, diatas materai yang cukup dan masing
                    masing mempunyai kekuatan hukum yang sama.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(2)</td>
                <td class="p-5 text-justify" colspan="2">Surat perjanjian ini dibuat dengan penuh kesadaran, penuh
                    tanggung jawab, tanpa pengaruh apapun dan oleh siapapun, disetujui, dibaca serta dimengerti akan
                    isinya.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(3)</td>
                <td class="p-5 text-justify" colspan="2">Hal-hal yang belum diatur dalam Perjanjian Kerja ini akan
                    ditetapkan kemudian oleh kedua belah pihak dalam perjanjian tambahan yang merupakan bagian yang
                    tidak terpisahkan dari dokumen ini.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(4)</td>
                <td class="p-5 text-justify" colspan="2">Bea materai yang timbul karena pembuatan Perjanjian Kerja ini
                    menjadi beban Pihak Kedua.</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td class="p-5 text-justify" colspan="3">Demikian Perjanjian Kerja Waktu Tertentu ini dibuat oleh Pihak
                    Pertama dan Pihak Kedua dalam keadaan sadar tanpa ada paksaan dari pihak manapun.</td>
            </tr>
        </table>
    </div>

    <table width="100%" style="border-collapse:collapse;position:fixed;bottom:0">
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center">PIHAK KEDUA</td>
            <td class="text-center">Direktur Rumah Sakit Umum Daerah Langsa<br>PIHAK PERTAMA</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center" style="vertical-align:top">
                <u>
                    <b>
                        {{ $nama_karyawan }}
                    </b>
                </u>
                <br>
                ID Peg. : {{ $result->kd_karyawan }}
            </td>
            <td class='text-center' style='vertical-align:top'>
                <u>
                    <b>
                        {{ $nama_direktur }}
                    </b>
                </u>
                <br> {{ formatNIP($direktur->nip_baru) }}
            </td>
        </tr>
    </table>

</body>
</html>