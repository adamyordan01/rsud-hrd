<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $surat->jenisSurat->jenis_surat }} - {{ $surat->karyawan->nama }}</title>
    <style>
        body {
            font-family: 'Cambria', serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .line2 {
            background: #282828;
            height: 1px;
            margin: 10px 0;
        }
        .content {
            text-align: left;
        }
        .table-print {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        .table-print td {
            padding: 5px;
            vertical-align: top;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-table {
            width: 100%;
        }
        .signature-table td {
            vertical-align: top;
            padding: 10px;
        }
        .signature-space {
            height: 80px;
            margin: 10px 0;
        }
        @media print {
            body { margin: 0; padding: 15px; }
            .no-print { display: none; }
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
    </style>
    <script>
        // Auto print surat
        window.onload = function() {
            window.print();
            setTimeout(function() { window.close(); }, 100);
        };
    </script>
</head>
<body>
    <div class="text-center">
        <b style="font-size: 14pt;">SURAT PERMOHONAN {{ strtoupper($surat->jenisSurat->jenis_surat) }}</b><br>
        <b>(Tidak Masuk Kerja / Terlambat Masuk Kerja / Pulang Sebelum Waktunya / Tidak Berada di Tempat Tugas / Tidak Melakukan Rekam Kehadiran)</b><br>
        <b>DI LINGKUNGAN {{ strtoupper(config('app.company_name', 'RSUD INDRAMAYU')) }}</b>
    </div>
    
    <hr class="line2">
    
    <p>Yang bertanda tangan di bawah ini:</p>
    
    <table class="table-print">
        <tr>
            <td width="200px">Nama</td>
            <td>:</td>
            <td>{{ ($surat->karyawan->gelar_depan ?? '') }} {{ $surat->karyawan->nama }}{{ ($surat->karyawan->gelar_belakang ?? '') }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $surat->karyawan->nip_baru ?? $surat->karyawan->nip_lama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pangkat/Golongan</td>
            <td>:</td>
            <td>{{ $surat->karyawan->pangkat ? $surat->karyawan->pangkat . ' / ' . ($surat->karyawan->kd_gol_sekarang ?? '') : '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ ($surat->karyawan->jab_struk ?? '') }} {{ ($surat->karyawan->ruangan ?? '') }}</td>
        </tr>
        <tr>
            <td>ID Pegawai</td>
            <td>:</td>
            <td>{{ $surat->kd_karyawan }}</td>
        </tr>
        <tr>
            <td>Status Kepegawaian</td>
            <td>:</td>
            <td>{{ $surat->karyawan->status_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td>Unit Kerja</td>
            <td>:</td>
            <td>{{ $surat->karyawan->ruangan ?? $surat->karyawan->unit_kerja ?? '-' }}</td>
        </tr>
    </table>
    
    <p>
        Dengan ini mengajukan permohonan izin {{ strtolower($surat->kategoriIzin->kategori) }}, 
        karena pada hari 
        @php
            $tglMulai = \Carbon\Carbon::parse($surat->tgl_mulai);
            $tglAkhir = \Carbon\Carbon::parse($surat->tgl_akhir);
            $listHari = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
            
            function tgl_indo($tanggal) {
                $bulan = [
                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                $pecahkan = explode('-', $tanggal);
                return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
            }
            
            if ($tglMulai->format('Y-m-d') == $tglAkhir->format('Y-m-d')) {
                $tanggal = '<b>' . $listHari[$tglMulai->format('l')] . '</b>, tanggal <b>' . tgl_indo($tglMulai->format('Y-m-d')) . '</b>';
            } else {
                $tanggal = '<b>' . $listHari[$tglMulai->format('l')] . '</b>, tanggal <b>' . tgl_indo($tglMulai->format('Y-m-d')) . '</b> s/d <b>' . $listHari[$tglAkhir->format('l')] . '</b>, tanggal <b>' . tgl_indo($tglAkhir->format('Y-m-d')) . '</b>';
            }
        @endphp
        {!! $tanggal !!} saya tidak dapat melaksanakan tugas sebagaimana mestinya dikarenakan alasan 
        <b>{{ $surat->alasan }}</b>.
    </p>
    
    <p>
        Demikian surat permohonan ini saya buat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya. 
        Atas perhatian dan kebijaksanaan Bapak/Ibu, saya ucapkan terima kasih.
    </p>
    
    <br><br>
    
    <table width="100%">
        <tr>
            @php
                // Ambil data atasan berdasarkan field PENILAI
                $atasan = null;
                if ($surat->karyawan->penilai) {
                    $atasan = \App\Models\ViewTampilKaryawan::where('KD_KARYAWAN', $surat->karyawan->penilai)->first();
                }
            @endphp
            <td width="65%">
                Persetujuan Atasan Langsung,<br>
                <b>KEPALA {{ strtoupper($atasan->ruangan ?? $surat->karyawan->ruangan ?? 'UNIT KERJA') }}</b><br><br><br><br><br><br>
                @if($atasan)
                    <b><u>{{ ($atasan->gelar_depan ?? '') }} {{ $atasan->nama }}{{ ($atasan->gelar_belakang ?? '') }}</u></b><br>
                    NIP. {{ $atasan->nip_baru ?? $atasan->nip_lama ?? '' }}
                @else
                    <b><u>(...................................)</u></b><br>
                    NIP. .....................................
                @endif
            </td>
            <td align="center">
                @php
                    function tgl_indo_sekarang($tanggal) {
                        $bulan = [
                            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        $pecahkan = explode('-', $tanggal);
                        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
                    }
                @endphp
                Indramayu, {{ tgl_indo_sekarang(date('Y-m-d')) }}<br>
                Pemohon,<br><br><br><br><br><br>
                <b><u>{{ ($surat->karyawan->gelar_depan ?? '') }} {{ $surat->karyawan->nama }}{{ ($surat->karyawan->gelar_belakang ?? '') }}</u></b><br>
                ID Pegawai: {{ $surat->kd_karyawan }}
            </td>
        </tr>
    </table>
</body>
</html>