<div class="d-flex justify-content-end flex-shrink-0">
    <div class="w-100px">
        <div class="dropdown">
            <button class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                Aksi
                <i class="ki-duotone ki-down fs-5 ms-1"></i>
            </button>
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3 edit" 
                       data-karyawan="{{ $riwayatOrganisasi->kd_karyawan }}" 
                       data-urut="{{ $riwayatOrganisasi->urut_org }}">
                        <i class="ki-duotone ki-pencil fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>
                        Edit
                    </a>
                </div>
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3 delete-riwayat-organisasi" 
                       data-karyawan="{{ $riwayatOrganisasi->kd_karyawan }}" 
                       data-urut="{{ $riwayatOrganisasi->urut_org }}"
                       data-organisasi="{{ $riwayatOrganisasi->organisasi }}">
                        <i class="ki-duotone ki-trash fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
