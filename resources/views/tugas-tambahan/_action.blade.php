@php
    $jabatan = Auth::user()->karyawan->kd_jabatan_struktural;
    $ruangan = Auth::user()->karyawan->kd_ruangan;

    $filePathTte = $row->path_dokumen ?? '';
    $urlFilePathTte = url(str_replace('public', 'public/storage', $filePathTte));
@endphp

@if ($row->verif_1 == null)
    @if ($jabatan == 19 || $ruangan == 57)
        <a
            href="javascript:void(0)"
            class="btn btn-info btn-sm d-block mb-2"
            title="Verifikasi Ka.Sub.Bag. Kepeg."
            data-id="{{ $row->kd_tugas_tambahan }}"
            data-karyawan="{{ $row->kd_karyawan }}"
            data-url="{{ route('admin.tugas-tambahan.first-verification') }}"
            data-bs-toggle="modal"
            data-bs-target="#kt_modal_verif"
            id="verif1"
        >
            <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i> 
            Verifikasi Ka.Sub.Bag. Kepeg.
        </a>
    @endif
@elseif($row->verif_2 == null)
    @if ($jabatan == 7 || $ruangan == 57)
        <a
            href="javascript:void(0)"
            class="btn btn-primary btn-sm d-block mb-2"
            title="Verifikasi Kabag. TU"
            data-id="{{ $row->kd_tugas_tambahan }}"
            data-karyawan="{{ $row->kd_karyawan }}"
            data-url="{{ route('admin.tugas-tambahan.second-verification') }}"
            data-bs-toggle="modal"
            data-bs-target="#kt_modal_verif"
            id="verif2"
        >
            <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
            Verifikasi Kabag. TU
        </a>
    @endif
@elseif($row->verif_3 == null)
    @if ($jabatan == 3 || $ruangan == 57)
        <a
            href="javascript:void(0)"
            class="btn btn-warning btn-sm d-block mb-2"
            title="Menunggu verifikasi Wadir ADM dan Umum"
            data-id="{{ $row->kd_tugas_tambahan }}"
            data-karyawan="{{ $row->kd_karyawan }}"
            data-url="{{ route('admin.tugas-tambahan.third-verification') }}"
            data-bs-toggle="modal"
            data-bs-target="#kt_modal_verif"
            id="verif3"
        >
            <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
            Menunggu verifikasi Wadir ADM dan Umum
        </a>
    @endif
    @if ($jabatan == 1 || $ruangan == 57)
        <a
            href="javascript:void(0)"
            class="btn btn-success btn-sm d-block mb-2"
            title="Menunggu verifikasi Direktur"
            data-id="{{ $row->kd_tugas_tambahan }}"
            data-karyawan="{{ $row->kd_karyawan }}"
            data-url="{{ route('admin.mutasi-on-process.fourth-verification') }}"
            id="verif4"
        >
            <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
            Menunggu verifikasi Direktur
        </a>
    @endif
@elseif($row->verif_4 == null)
    @if ($jabatan == 1 || $ruangan == 57)
        <a
            href="javascript:void(0)"
            class="btn btn-success btn-sm d-block mb-2"
            title="Menunggu verifikasi Direktur"
            data-id="{{ $row->kd_tugas_tambahan }}"
            data-karyawan="{{ $row->kd_karyawan }}"
            data-url="{{ route('admin.mutasi-on-process.fourth-verification') }}"
            id="verif4"
        >
            <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
            Menunggu verifikasi Direktur
        </a>
    @endif
@elseif($row->path_dokumen != null)
    <a
        href="{{ $urlFilePathTte }}"
        class="btn btn-primary btn-sm d-block mb-2"
        title="Download TTE"
    >
        <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
        Cetak Nota
    </a>
@endif
{{-- cetak draft nota --}}
{{-- Route::get('/tugas-tambahan/print-draft-nota/{kd_karyawan}/{kd_tugas_tambahan}', [TugasTambahanController::class, 'printDraftNota'])->name('print-draft-nota'); --}}
    <a
        href="{{ route('admin.tugas-tambahan.print-draft-nota', [$row->kd_karyawan, $row->kd_tugas_tambahan]) }}"
        class="btn btn-primary btn-sm d-block mb-2"
        title="Cetak Draft Nota"
        target="_blank"
    >
        <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
        Cetak Draft Nota
    </a>