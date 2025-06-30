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
            <a href="{{ route('admin.karyawan.edit', $karyawan->kd_karyawan) }}" class="menu-link px-3">
                <i class="ki-duotone ki-pencil fs-3 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Edit
            </a>
        </div>

        <!-- Curriculums Vitaes -->
        <div class="menu-item px-3">
            {{-- Route::get('karyawan/identitas/cv/{id}', [CvController::class, 'show'])->name('cv'); --}}
            <a href="{{ route('admin.karyawan.cv', $karyawan->kd_karyawan) }}" class="menu-link px-3">
                <i class="ki-duotone ki-files-tablet fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>
                Curriculum Vitae (CV)
            </a>
        </div>

        <!-- Riwayat -->
        <div class="menu-item px-3">
            {{-- Route::get('/karyawan/show/{id}', [KaryawanController::class, 'show'])->name('show'); --}}
            <a href="{{ route('admin.karyawan.show', $karyawan->kd_karyawan) }}" class="menu-link px-3">
                <i class="ki-duotone ki-arrow-circle-left fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>
                Riwayat
            </a>
        </div>
    </div>
</div>