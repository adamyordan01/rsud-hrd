<!--begin::Menu-->
<a href="#" class="btn btn-sm btn-icon btn-secondary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
    <i class="ki-duotone ki-dots-vertical fs-5 m-0">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
</a>
<!--begin::Menu 3-->
<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
    <!--begin::Heading-->
    <div class="menu-item px-3">
        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
            Aksi
        </div>
    </div>
    <!--end::Heading-->
    <!--begin::Menu item-->
    <div class="menu-item px-3">
        <a href="#" class="menu-link px-3 edit" data-karyawan="{{ $seminar->kd_karyawan }}" data-urut="{{ $seminar->urut_seminar }}">
            <i class="ki-duotone ki-pencil fs-6 text-primary me-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            Edit
        </a>
    </div>
    <!--end::Menu item-->
    <!--begin::Menu item-->
    <div class="menu-item px-3">
        <a href="#" class="menu-link px-3 delete-seminar" data-karyawan="{{ $seminar->kd_karyawan }}" data-urut="{{ $seminar->urut_seminar }}" data-seminar="{{ $seminar->nama_seminar }}">
            <i class="ki-duotone ki-trash fs-6 text-danger me-3">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
                <span class="path5"></span>
            </i>
            Hapus
        </a>
    </div>
    <!--end::Menu item-->
</div>
<!--end::Menu 3-->
<!--end::Menu-->
