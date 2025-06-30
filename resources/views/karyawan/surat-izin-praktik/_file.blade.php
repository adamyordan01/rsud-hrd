<a 
    href="{{ route('admin.karyawan.sip.download', ['id' => $sip->kd_karyawan, 'urut' => $sip->urut_sip]) }}"
    target="_blank"
    class="btn btn-sm btn-icon btn-light btn-active-light-primary" 
    id="download" 
    data-karyawan="{{ $sip->kd_karyawan }}" 
    data-urut="{{ $sip->urut_sip }}">
    <i class="ki-duotone ki-cloud-download fs-5 m-0">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
</a>