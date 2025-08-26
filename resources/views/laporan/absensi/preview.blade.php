<div class="text-center mb-4">
    <h4 class="fw-bold text-gray-800">DAFTAR HADIR PEGAWAI NEGERI SIPIL, HONORER, KONTRAK RSUD LANGSA</h4>
    <p class="text-muted">BULAN: {{ $dataBulan[$bulan] ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp; TAHUN: {{ $tahun }}</p>
    <p class="text-muted"><strong>Ruangan: {{ $ruanganData->ruangan ?? '-' }}</strong></p>
</div>

<div class="table-responsive">
    <table class="absen-table">
        <thead>
            <tr>
                <th rowspan="3" style="width: 30px;">No.</th>
                <th rowspan="3" style="width: 200px;">ID. PEG<br>NAMA PEGAWAI<br>NIP</th>
                <th rowspan="3" style="width: 120px;">Jabatan</th>
                <th rowspan="3" style="width: 120px;">Jenis Tenaga</th>
                <th colspan="4">Tanggal: ____</th>
                <th colspan="4">Tanggal: ____</th>
                <th colspan="4">Tanggal: ____</th>
                <th colspan="4">Tanggal: ____</th>
                <th colspan="4">Tanggal: ____</th>
                <th colspan="4">Tanggal: ____</th>
                <th colspan="4">Tanggal: ____</th>
                <th rowspan="3" style="width: 80px;">Status</th>
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
                @foreach($dataPegawai as $index => $pegawai)
                    @php
                        $gelarDepan = $pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '';
                        $gelarBelakang = $pegawai->gelar_belakang ? $pegawai->gelar_belakang : '';
                        $namaLengkap = $gelarDepan . $pegawai->nama . $gelarBelakang;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="employee-name">
                            {{ $pegawai->kd_karyawan }}<br>
                            <strong>{{ $namaLengkap }}</strong><br>
                            {{ $pegawai->nip_baru ?? '-' }}
                        </td>
                        <td>{{ $pegawai->jab_struk ?? '-' }}</td>
                        <td>{{ $pegawai->sub_detail ?? '-' }}</td>
                        
                        <!-- 28 kolom kosong untuk absensi 7 hari -->
                        @for($i = 0; $i < 28; $i++)
                            <td>&nbsp;</td>
                        @endfor
                        
                        <td>{{ $pegawai->status_kerja ?? '-' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="32" class="text-center">Tidak ada data pegawai</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if($dataPegawai->count() > 0)
    <div class="text-center mt-4">
        <p class="text-muted">
            <strong>Total Pegawai: {{ $dataPegawai->count() }} orang</strong>
        </p>
        <p class="text-muted">
            Formulir absensi siap untuk dicetak dan diisi secara manual
        </p>
    </div>
@endif
