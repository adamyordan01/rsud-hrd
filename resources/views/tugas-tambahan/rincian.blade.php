@inject('carbon', 'Carbon\Carbon')

@php
    $gelar_depan = $getRincian->gelar_depan ? $getRincian->gelar_depan . '. ' : '';
    $gelar_belakang = $getRincian->gelar_belakang ? $getRincian->gelar_belakang : '';
    $nama = $gelar_depan . $getRincian->nama . $gelar_belakang;
@endphp

<form action="#">
    @csrf
    <input type="hidden" name="kd_karyawan" id="kd_karyawan" value="{{ $getRincian->kd_karyawan }}">
    <input type="hidden" name="kd_tugas_tambahan" id="kd_tugas_tambahan" value="{{ $getRincian->kd_tugas_tambahan }}">

    <table class="table align-middle table-row-dashed table-striped fs-5 gy-5">
        <tr>
            <th>Kode Mutasi</th>
            <td class="fw-bold">{{ $getRincian->kd_tugas_tambahan }}</td>
        </tr>
        <tr>
            <th>Tugas Tambahan</th>
            <td class="fw-bold">{{ $getRincian->nama_tugas_tambahan }}</td>
        </tr>
        <tr>
            <th>TMT</th>
            <td class="fw-bold">
                {{ $carbon->parse($getRincian->tmt_awal)->translatedFormat('d-m-Y') }}
                s/d
                {{ $carbon->parse($getRincian->tmt_akhir)->translatedFormat('d-m-Y') }}
            </td>
        </tr>
        <tr>
            <th>ID Peg.</th>
            <td class="fw-bold">{{ $getRincian->kd_karyawan }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td class="fw-bold">{{ $nama }}</td>
        </tr>
        <tr>
            <th>NIP/NIPPPK</th>
            <td class="fw-bold">{{ $getRincian->nip_baru ? $getRincian->nip_baru : '-' }}</td>
        </tr>
        <tr>
            <th>Ruangan</th>
            <td class="fw-bold">{{ $getRincian->ruangan }}</td>
        </tr>
        <tr>
            <th>Jabatan Struktural</th>
            <td class="fw-bold">{{ $getRincian->jab_struk }}</td>
        </tr>
        <tr>
            <th>Sub Jenis Tenaga</th>
            <td class="fw-bold">{{ $getRincian->sub_detail }}</td>
        </tr>
        <tr>
            <th>Ruangan Tugas Tambahan</th>
            <td class="fw-bold">{{ $getRincian->ruangan_tambahan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jabatan Struktural Tugas Tambahan</th>
            <td class="fw-bold">{{ $getRincian->jab_struk_tambahan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Sub Jenis Tenaga Tugas Tambahan</th>
            <td class="fw-bold">{{ $getRincian->sub_detail_tambahan ?? '-' }}</td>
        </tr>
    </table>
</form>