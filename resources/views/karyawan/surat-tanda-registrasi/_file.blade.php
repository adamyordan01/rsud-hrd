<a 
    href="{{ route('admin.karyawan.str.download', ['id' => $str->kd_karyawan, 'urut' => $str->urut_str]) }}"
    target="_blank"
    class="btn btn-sm btn-icon btn-light btn-active-light-primary" 
    id="download" 
    data-karyawan="{{ $str->kd_karyawan }}" 
    data-urut="{{ $str->urut_str }}">
    <i class="ki-duotone ki-cloud-download fs-5 m-0">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
</a>