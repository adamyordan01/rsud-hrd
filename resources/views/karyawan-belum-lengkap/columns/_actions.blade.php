<div class="d-flex justify-content-end flex-shrink-0">
    <a href="{{ route('admin.karyawan.show', $karyawan->kd_karyawan) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Lihat Detail">
        <i class="ki-duotone ki-eye fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
    </a>
    <a href="{{ route('admin.karyawan.edit', $karyawan->kd_karyawan) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="tooltip" title="Edit Data">
        <i class="ki-duotone ki-pencil fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </a>
</div>
