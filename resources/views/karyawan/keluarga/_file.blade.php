<a 
    href="{{ Storage::disk('hrd_files')->url($row->foto_kartu) }}"
    target="_blank"
    class="btn btn-sm btn-icon btn-light btn-active-light-primary" id="download" data-karyawan="{{ $row->kd_karyawan }}" data-urut="{{ $row->urut }}">
    <i class="ki-duotone ki-cloud-download fs-5 m-0"><span class="path1"></span><span class="path2"></span></i>
</a>