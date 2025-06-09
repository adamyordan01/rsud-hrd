<div class="d-flex gap-2 flex-wrap justify-content-end align-items-center">
    <!-- Sortable Handle -->
    <span class="sortable-handle me-2" title="Drag to reorder">
        <i class="fas fa-grip-vertical fs-4 text-muted"></i>
    </span>

    <!-- Action Menu Button -->
    <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" 
        data-kt-menu-placement="bottom-end">
        <i class="ki-duotone ki-category fs-2">
            <span class="path1"></span><span class="path2"></span>
            <span class="path3"></span><span class="path4"></span>
        </i>
    </button>

    <!-- Dropdown Menu -->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
        data-kt-menu="true">
        <div class="menu-item px-3">
            <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                Actions
            </div>
        </div>

        <div class="menu-item px-3">
            <a href="javascript:;" class="menu-link px-3 edit" 
               data-karyawan="{{ $row->kd_karyawan }}" 
               data-urut="{{ $row->urut_klrg }}">
                <i class="ki-duotone ki-pencil fs-3 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Edit
            </a>
        </div>

        <div class="menu-item px-3">
            <a href="javascript:;" class="menu-link px-3 delete" 
               data-karyawan="{{ $row->kd_karyawan }}" 
               data-urut="{{ $row->urut_klrg }}">
               <i class="ki-duotone ki-trash fs-3 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                </i>
                Delete
            </a>
        </div>
    </div>
</div>