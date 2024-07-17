<form action="#">
    @csrf
    <input type="hidden" name="urut_rincian_verif" value="" id="urut_rincian_verif">
    <input type="hidden" name="tahun_rincian_verif" value="" id="tahun_rincian_verif">
    <table class="table align-middle table-row-dashed table-striped fs-6 gy-5" id="kt_rincian_verif_1_table">
        <thead>
            <tr class="text-gray-600 fw-bold fs-7 text-uppercase gs-0">
                <th class="w-10px text-center">
                    No
                </th>
                <th class="w-10px">
                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                        <input
                            class="form-check-input header-checkbox"
                            type="checkbox"
                            data-kt-check="true"
                            data-kt-check-target="#kt_rincian_verif_1_table .form-check-input"
                        />
                    </div>
                </th>
                <th class="text-start min-w-200px">Identitas Peg. Kontrak</th>
                <th class="text-center min-w-40px">JK</th>
                <th class="text-start min-w-100px">Pend. Terakhir</th>
                <th class="text-start min-w-100px">TMT. SK</th>
            </tr>
        </thead>
        <tbody class="fw-semibold text-gray-600">
            @foreach ($results as $item)
                <tr>
                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox"
                                name="kd_karyawan[]"
                                value="{{ $item->kd_karyawan }}"
                            />
                        </div>
                    </td>
                    <td>
                        {{ $item->kd_karyawan }}<br>
                        <b>{{ $item->gelar_depan }} {{ $item->nama }} {{ $item->gelar_belakang }}</b><br>
                        {{ $item->tempat_lahir }}, {{ date('d-m-Y', strtotime($item->tgl_lahir)) }}
                    </td>
                    <td class="text-center">
                        {{ ($item->kd_jenis_kelamin == 0) ? 'P' : 'L' }}
                    </td>
                    <td class="text-center">
                        {{ $item->jenjang_didik }}<br>
                        {{ $item->jurusan }}<br>
                        Lulus Thn. {{ $item->tahun_lulus }}
                    </td>
                    <td class="text-start">
                        {{ date('d-m-Y', strtotime($item->tgl_sk)) }} s/d 31-12-{{ $item->tahun_sk }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</form>

@push('scripts')
    <script>
    </script>
@endpush