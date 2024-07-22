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

        // $tanggal = (int) date('d', strtotime($results->tgl_sk));
        // var_dump(date('d', strtotime($results->tgl_sk)));
        // var_dump($tanggal);
        // die;

        // buat hari, tanggal, bulan, tahun dalam bentuk seperti tanggal senin tujuh januari dua ribu dua puluh empat dari $results->tgl_sk
        // $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        // $bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];

        // buatkan fungsi penyebut untuk mengubah angka menjadi huruf
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

        // $hari_ttd = tanggal_indo($results->tgl_sk);

        // $cetak_hari = $hari[date('w', strtotime($results->tgl_sk))];
        // $cetak_bulan = $bulan[date('m', strtotime($results->tgl_sk))];

        $gelar_depan_direktur = $direktur->gelar_depan ? $direktur->gelar_depan . ' ' : '';
        $gelar_belakang_direktur = $direktur->gelar_belakang ? $direktur->gelar_belakang : '';
        $nama_direktur = $gelar_depan_direktur . $direktur->nama . $gelar_belakang_direktur;

        $gelar_depan_karyawan = $results->gelar_depan ? $results->gelar_depan . ' ' : '';
        $gelar_belakang_karyawan = $results->gelar_belakang ? $results->gelar_belakang : '';
        $nama_karyawan = $gelar_depan_karyawan . $results->nama . $gelar_belakang_karyawan;
    @endphp
    
    <div class="all">
        <table width="90%" style="margin:0 auto;margin-top:20px;">
            <tr>
                <td colspan="3" class="text-center">Pasal 4<br>Sanksi-sanksi</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;" width="10px">(1)</td>
                <td class="p-5 text-justify" colspan="2">
                    Pihak Kedua dapat diberhentikan sewaktu-waktu dari tugas pekerjaannya oleh Pihak Pertama, apabila
                    Pihak Kedua melanggar dan tidak mematuhi peraturan/ ketentuan yang berlaku di dalam peraturan
                    Perjanjian Kerja Tenaga Kontrak di Rumah Sakit Umum Daerah Langsa. Pemberhentian Pihak Kedua
                    tersebut tidak menghilangkan sanksi-sanksi lain sesuai dengan tingkat kesalahan/ pelanggaran yang
                    dilakukan.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(2)</td>
                <td class="p-5 text-justify" colspan="2">Sanksi diberikan apabila melakukan tindakan pelanggaran
                    kedisiplinan dan pelanggaran berupa :</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;" width="10px">a. </td>
                <td class="p-5 text-justify">Merusak dengan sengaja dan/atau menghilangkan asset baik secara keseluruhan
                    dan/atau sebagian asset milik Rumah Sakit Umum Daerah Langsa;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">b. </td>
                <td class="p-5 text-justify">Tidak hadir selama 5 (lima) hari kerja berturut-turut atau lebih dalam satu
                    bulan tanpa alasan dan tidak dilengkapi dengan bukti yang sah;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">c. </td>
                <td class="p-5 text-justify">Bekerja rangkap di instansi lain pada jam kerja yang disepakati;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">d. </td>
                <td class="p-5 text-justify">Melanggar peraturan perundang-undangan yang berlaku;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">e. </td>
                <td class="p-5 text-justify">Mencemarkan nama baik pimpinan, teman kerja dan/atau satuan Kerja;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">f. </td>
                <td class="p-5 text-justify">Menggunakan dan atau memanfaatkan fasilitas untuk usaha lain (kepentingan
                    pribadi) baik di dalam maupun di luar jam kerja tanpa izin yang sah;</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">g. </td>
                <td class="p-5 text-justify">Membocorkan rahasia jabatan dan dokumen Negara; dan</td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">&nbsp;</td>
                <td class="p-5" style="vertical-align:top;">h. </td>
                <td class="p-5 text-justify">Dinyatakan bersalah berdasarkan keputusan pengadilan yang telah berkekuatan
                    hukum tetap.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(3)</td>
                <td class="p-5 text-justify" colspan="2">Jika Pihak Kedua melanggar Pasal 4 ayat (1) dan (2), maka Pihak
                    Pertama berhak memutuskan hubungan kerja secara sepihak tanpa syarat.</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="text-center">Pasal 5<br>Pengunduran Diri</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(1)</td>
                <td class="p-5 text-justify" colspan="2">Selama Pihak Kedua menjadi Tenaga Kontrak di Rumah Sakit Umum
                    Daerah Langsa, Pihak Pertama tidak menjamin dan tidak menjanjikan Pihak Kedua untuk dapat diangkat
                    menjadi Calon Pegawai Negri Sipil (CPNS).</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(2)</td>
                <td class="p-5 text-justify" colspan="2">Apabila dikemudian hari Pihak Kedua diangkat menjadi Calon
                    Pegawai Negeri Sipil atau mengundurkan diri, maka semua hak-haknya menjadi gugur sebagai Tenaga
                    Kontrak.</td>
            </tr>
            <tr>
                <td style="vertical-align:top;">(3)</td>
                <td class="p-5 text-justify" colspan="2">Dalam hal pengunduran diri Pihak Kedua wajib memberitahu secara
                    tertulis kepada Pihak Pertama selambat-lambatnya dalam waktu 1 (satu) bulan sebelum mengundurkan
                    diri.</td>
            </tr>
        </table>
    </div>

</body>
</html>