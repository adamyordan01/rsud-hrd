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
        use App\Helpers\UtilityHelper;

        $tanggal = (int) date('d', strtotime($result->tgl_sk));
        // var_dump(date('d', strtotime($result->tgl_sk)));
        // var_dump($tanggal);
        // die;

        // buat hari, tanggal, bulan, tahun dalam bentuk seperti tanggal senin tujuh januari dua ribu dua puluh empat dari $result->tgl_sk
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];

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

        $hari_ttd = tanggal_indo($result->tgl_sk);

        $cetak_hari = $hari[date('w', strtotime($result->tgl_sk))];
        $cetak_bulan = $bulan[date('m', strtotime($result->tgl_sk))];

        $gelar_depan_direktur = $direktur->gelar_depan ? $direktur->gelar_depan . ' ' : '';
        $gelar_belakang_direktur = $direktur->gelar_belakang ? $direktur->gelar_belakang : '';
        $nama_direktur = $gelar_depan_direktur . $direktur->nama . $gelar_belakang_direktur;

        $gelar_depan_karyawan = $result->gelar_depan ? $result->gelar_depan . ' ' : '';
        $gelar_belakang_karyawan = $result->gelar_belakang ? $result->gelar_belakang : '';
        $nama_karyawan = $gelar_depan_karyawan . $result->nama . $gelar_belakang_karyawan;
    @endphp
    
    <div class="all">
        <table class="text-center t-font" width="100%">
            <tr>
                <td width="110" style="vertical-align: middle;text-align: left"><img src="{{ $logoLangsa }}"
                        width="80" height="100" /></td>
                <td style="vertical-align: middle;">
                    <p>
                        <b style="font-size: 19pt; margin-bottom: 0px;">PEMERINTAH KOTA LANGSA</b><br>
                        <b style="font-size: 19pt; margin-top: 0px; margin-bottom: 0px;">RUMAH SAKIT UMUM DAERAH LANGSA</b>
                    </p>
                </td>
                <td width="40"></td>
            </tr>
        </table>
        <div
            style="border-top: 2.5px solid #282828;border-bottom: 1px solid #282828;border-left: 0;border-right: 0; margin: 0px;padding-top:1px;margin-bottom:10px">
        </div>
        <div>
            <p style="margin:0;padding:0">
                PERJANJIAN KERJA<br>&nbsp;
            </p>
            <p style="margin:0;padding:">ANTARA</p>
            <p style="margin:0;padding:0">
                RUMAH SAKIT UMUM DAERAH LANGSA
            </p>
            <p style="margin:0;padding:0">
                DENGAN
            </p>
            <p style="margin:0;padding:">
                TENAGA KONTRAK
            </p>
            <p style="margin:0;padding:8px;">
                Nomor : Peg. 445/{{ $result->no_per_kerja }}/PKS/{{ $result->tahun_sk }}
            </p>
        </div>
        <table width="90%" style="margin:0 auto;margin-top:20px;">
            <tr>
                <td colspan="5" class="text-justify">
                    Pada hari ini 
                    <b>
                        {{ $cetak_hari }}
                    </b> Tanggal 
                    <b
                        style="text-transform:capitalize;">
                        {{-- ".penyebut(date_format($result["TGL_SK"], 'd'))." --}}
                        {{ penyebut($tanggal) }}
                    </b> Bulan
                    <b>
                        {{-- ".$bulan[date_format($result['TGL_SK'], 'm')]." --}}
                        {{ $cetak_bulan }}
                    </b> Tahun 
                    <b style='text-transform:capitalize;'>{{ penyebut(date('Y', strtotime($result->tgl_sk))) }}</b>, kami yang bertanda tangan di bawah ini :
                </td>
            </tr>
            <tr>
                <td colspan='5'></td>
            </tr>
            <tr>
                <td width='30px'>1.</td>
                <td class='p-5' width='180px'>Nama</td>
                <td class='text-center' width='10px'>:</td>
                <td colspan='2' class='p-5'>
                    {{-- dr. HELMIZA FAHRY, Sp.OT --}}
                    {{ $nama_direktur }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>NIP</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>
                    {{ formatNIP($direktur->nip_baru) }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>Jabatan</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>Direktur </td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>Unit Kerja</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>Rumah Sakit Umum Daerah Langsa</td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>Alamat Unit Kerja</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>Jln. Jend. A. Yani No. 1 Kota Langsa</td>
            </tr>
            <tr>
                <td colspan='5'></td>
            </tr>
            <tr>
                <td colspan='5' class='text-justify'>Bertindak untuk dan atas nama Rumah Sakit Umum Daerah Langsa,
                    selanjutnya dalam hal ini disebutkan sebagai <b>PIHAK PERTAMA</b></td>
            </tr>
            <tr>
                <td colspan='5'></td>
            </tr>
            <tr>
                <td>2.</td>
                <td class='p-5' width='180px'>Nama</td>
                <td class='text-center' width='10px'>:</td>
                <td colspan='2' class='p-5'>
                    {{-- ".$result['GELAR_DEPAN']." ".$result['NAMA'].$result['GELAR_BELAKANG']." --}}
                    {{ $nama_karyawan }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>NIK</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>
                    {{-- ".$result['NO_KTP']." --}}
                    {{ $result->no_ktp }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>ID Peg.</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>
                    {{-- ".$result['KD_KARYAWAN']." --}}
                    {{ $result->kd_karyawan }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>Tempat, Tangggal Lahir</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>
                    {{-- ".$result['TEMPAT_LAHIR'].", ".tgl_indo(date_format($result['TGL_LAHIR'], --}}
                    {{-- 'Y-m-d'))." --}}
                    {{ $result->tempat_lahir }}, {{ tanggal_indo($result->tgl_lahir) }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>Pendidikan</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>
                    {{-- ".$result['JENJANG_DIDIK']." - ".$result['JURUSAN']." --}}
                    {{ $result->jenjang_didik }} - {{ $result->jurusan }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td class='p-5'>Jenis Kelamin</td>
                <td class='text-center'>:</td>
                <td colspan='2' class='p-5'>
                    {{-- ".$result['JENIS_KELAMIN']." --}}
                    {{ $result->jenis_kelamin }}
                </td>
            </tr>
            <tr>
                <td colspan='5'></td>
            </tr>
            <tr>
                <td colspan='5' class='text-justify'>Bertindak untuk dan atas namanya sendiri, selanjutnya dalam hal ini
                    disebut sebagai <b>PIHAK KEDUA</b></td>
            </tr>
            <tr>
                <td colspan='5'>&nbsp;</td>
            </tr>
            <tr>
                <td colspan='5' class='text-justify'>
                    Kedua belah pihak sepakat mengadakan Perjanjian Kerja Tenaga Kontrak pada Rumah Sakit Umum Daerah Langsa untuk jenis objek pekerjaan adalah 
                    <b>
                        {{-- TENAGA ".$result['SUB_DETAIL']." --}}
                        TENAGA {{ $result->sub_detail }}
                    </b> 
                    yang diatur dalam pasal-pasal sebagai berikut:</td>
            </tr>
            <tr>
                <td colspan='5' class='text-center'>Pasal 1<br>Kewajiban</td>
            </tr>
            <tr>
                <td colspan='5'>&nbsp;</td>
            </tr>
            <tr>
                <td style='vertical-align:top;'>(1)</td>
                <td class='p-5 text-justify' colspan='4'>Pihak Pertama menerima Pihak Kedua untuk bekerja di Rumah Sakit
                    Umum Daerah Langsa sebagai Tenaga Kontrak.</td>
            </tr>
            <tr>
                <td style='vertical-align:top;'>(2)</td>
                <td class='p-5 text-justify' colspan='4'>Pihak Kedua sanggup menjadi pekerja pada Pihak Pertama dalam waktu
                    yang telah ditentukan sesuai pasal 3 ayat (1) perjanjian ini dan akan mematuhi segala ketentuan dan
                    peraturan yang berlaku di Rumah Sakit Umum Daerah Langsa.</td>
            </tr>
            <tr>
                <td style='vertical-align:top;'>(3)</td>
                <td class='p-5 text-justify' colspan='4'>Pihak Kedua sanggup menjaga kerahasiaan seluruh data dan informasi
                    Rumah Sakit Umum Daerah Langsa terhadap kepentingan pihak luar, baik data yang secara langsung maupun
                    tidak langsung.</td>
            </tr>
        </table>
    </div>
    
    <htmlpagefooter name="page-footer">
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
    </htmlpagefooter>

    

</body>
</html>