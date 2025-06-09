<a 
    href="{{ Storage::url('sip_files/' . $sip->sc_berkas) }}"
    target="_blank"
    class="btn btn-sm btn-icon btn-light btn-active-light-primary" id="download" data-karyawan="{{ $sip->kd_karyawan }}" data-urut="{{ $sip->urut }}">
    <i class="ki-duotone ki-cloud-download fs-5 m-0"><span class="path1"></span><span class="path2"></span></i>
</a>