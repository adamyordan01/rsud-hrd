<div class="d-block gap-2 flex-wrap justify-content-end">
    <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary"
        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
    >
        <i class="ki-duotone ki-category fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
    </button>

    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
        data-kt-menu="true">
        <div class="menu-item px-3">
            <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                Actions
            </div>
        </div>

        <div class="menu-item px-3">
            <a 
                href="{{ route('admin.karyawan.sip.edit', ['id' => $sip->kd_karyawan, 'urut' => $sip->urut_sip]) }}"
                class="menu-link px-3 edit"
                data-karyawan="{{ $sip->kd_karyawan }}"
                data-urut="{{ $sip->urut_sip }}"
            >
                <i class="ki-duotone ki-pencil fs-3 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Edit
            </a>
        </div>

        <!-- jika status == 1 maka munculkan button approve -->
        @if($sip->status == 1)
            <div class="menu-item px-3">
                <a 
                    href="javascript:void(0)"
                    id="approve"
                    data-karyawan="{{ $sip->kd_karyawan }}"
                    data-urut="{{ $sip->urut }}"
                    class="menu-link px-3">
                    <i class="ki-duotone ki-check fs-3 me-2">
                        <span class="path"></span>
                        <span class="path"></span>
                    </i>
                    Setujui
                </a>
            </div>
        @endif
    </div>
</div>