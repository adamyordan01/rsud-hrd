<div class="d-block gap-2 flex-wrap justify-content-end">
    <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary"
        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
    >
        <i class="ki-duotone ki-category fs-2"><span class="path1"></span><span class="path2"></span><span
                class="path3"></span><span class="path4"></span></i> 
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
                href="javascript:void(0)"
                class="menu-link px-3 edit"
                data-id="{{ $row->kd_pekerjaan }}"
                id="detail-user"
            >
                <i class="ki-duotone ki-eye fs-3 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                Edit
            </a>
        </div>
    </div>
</div>