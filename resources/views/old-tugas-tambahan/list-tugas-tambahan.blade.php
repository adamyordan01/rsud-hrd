<table class="table table-bordered table-stripped align-middle">
    <thead>
        <tr>
            <th>ID Peg.</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Ruangan</th>
            <th>Sub Jenis Tenaga</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listTugasTambahan as $item)
            @php
                $gelar_depan = $item->gelar_depan ? $item->gelar_depan . '. ' : '';
                $gelar_belakang = $item->gelar_belakang ? ', ' . $item->gelar_belakang : '';
                $nama = $gelar_depan . $item->nama . $gelar_belakang;
            @endphp
            <tr>
                <td>{{ $item->kd_karyawan }}</td>
                <td>{{ $nama }}</td>
                <td>{{ $item->jab_struk }}</td>
                <td>{{ $item->ruangan }}</td>
                <td>{{ $item->sub_detail }}</td>
                <td>
                    <button
                        type="button"
                        class="btn btn-light btn-sm btn-active-light-danger me-2"
                        data-id="{{ $item->kd_tugas_tambahan }}"
                        id="btn-delete-tugas-tambahan"
                    >
                        <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        Hapus
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>