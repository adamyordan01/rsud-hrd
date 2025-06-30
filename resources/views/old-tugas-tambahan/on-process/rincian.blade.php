@php
    $gelar_depan = $getRincian->gelar_depan ? $getRincian->gelar_depan . '. ' : '';
    $gelar_belakang = $getRincian->gelar_belakang ? ' ' . $getRincian->gelar_belakang : '';
    $nama = $gelar_depan . $getRincian->nama . $gelar_belakang;
@endphp

<form action="#">
    @csrf
    <input type="hidden" name="kd_karyawan" id="kd_karyawan" value="{{ $getRincian->kd_karyawan }}">
    <input type="hidden" name="kd_tugas_tambahan" id="kd_tugas_tambahan" value="{{ $getRincian->kd_tugas_tambahan }}">
    <table class="table align-middle table-row-dashed table-striped fs-5 gy-5" id="kt_rincian_verif_1_table">
        <tr>
            <td>Kode Mutasi</td>
            <td class="fw-bold">{{ $getRincian->kd_tugas_tambahan }}</td>
        </tr>
        <tr>
            <td>ID Peg.</td>
            <td class="fw-bold">{{ $getRincian->kd_karyawan }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td class="fw-bold">{{ $nama }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td class="fw-bold">{{ $getRincian->nip_baru }}</td>
        </tr>
        <tr>
            <td>Status Kerja</td>
            <td class="fw-bold">{{ $getRincian->status_kerja }}</td>
        </tr>
        <tr>
            <td>Department</td>
            <td class="fw-bold">{{ $getRincian->departement }}</td>
        </tr>
        <tr>
            <td>Bagian / Bidang</td>
            <td class="fw-bold">{{ $getRincian->unit_kerja_baru }}</td>
        </tr>
        <tr>
            <td>Sub Bag. / Sub Bid. / Instalasi / Unit</td>
            <td class="fw-bold">{{ $getRincian->sub_unit_kerja_baru }}</td>
        </tr>
        <tr>
            <td>Ruangan</td>
            <td class="fw-bold">{{ $getRincian->ruangan_baru }}</td>
        </tr>
        <tr>
            <td>Jabatan Struktural / Non-Struktural</td>
            <td class="fw-bold">{{ $getRincian->jab_struk_baru }}</td>
        </tr>
        <tr>
            <td>Sub Jenis Tenaga</td>
            <td class="fw-bold">{{ $getRincian->sub_detail_baru }}</td>
        </tr>
    </table>
</form>