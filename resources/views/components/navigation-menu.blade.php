@php
    $ruangan = Auth::user()->karyawan->kd_ruangan;
@endphp
<div class="card-rounded bg-light d-flex flex-stack flex-wrap p-5 mb-8">
    <div class="hover-scroll-x">
        <ul class="nav flex-nowrap border-transparent fw-bold">
            @if ($ruangan == 91 || $ruangan == 57)
                <li class="nav-item my-1">
                    <a
                        class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi.index') ? 'active' : '' }}"
                        href="{{ route('admin.mutasi.index') }}"
                    >
                        <i class="ki-duotone ki-arrows-loop fs-1"><span class="path1"></span><span class="path2"></span></i>
                        Mutasi (Nota)
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a
                        class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi.tugas-tambahan') ? 'active' : '' }}"
                        href="{{ route('admin.mutasi.tugas-tambahan') }}"
                    >
                        <i class="ki-duotone ki-arrows-loop fs-1"><span class="path1"></span><span class="path2"></span></i>
                        Tugas Tambahan
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a 
                        class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase"
                        href=""
                    >
                    <i class="ki-duotone ki-arrows-loop fs-1"><span class="path1"></span><span class="path2"></span></i>
                    Mutasi (SK)
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a
                        class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-pending.*') ? 'active' : '' }}"
                        href="{{ route('admin.mutasi-pending.index') }}"
                    >
                    <i class="ki-duotone ki-abstract-5 fs-1"><span class="path1"></span><span class="path2"></span></i>
                        <span class="menu-title me-2">
                            Daftar Mutasi (Tetunda)
                        </span>
                        <span class="menu-badge">
                            <span class="badge badge-sm badge-circle badge-danger count-status-proses">
                                {{ $totalMutasiPending }}
                            </span>
                        </span>
                    </a>
                </li>
            @endif

            <li class="nav-item my-1">
                <a
                    class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-on-process.*') ? 'active' : '' }}"
                    href="{{ route('admin.mutasi-on-process.index') }}"
                >
                    <i class="ki-duotone ki-loading fs-1"><span class="path1"></span><span class="path2"></span></i>
                    <span class="menu-title me-2">
                        Daftar Mutasi (Proses)
                    </span>
                    <span class="menu-badge">
                        <span class="badge badge-sm badge-circle badge-danger count-status-proses">
                            {{ $totalMutasiOnProcess }}
                        </span>
                    </span>
                </a>
            </li>

            @if ($ruangan == 91 || $ruangan == 57)
                <li class="nav-item my-1">
                    <a
                        class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-verifikasi.*') ? 'active' : '' }}"
                        href="{{ route('admin.mutasi-verifikasi.index') }}"
                    >
                        <i class="ki-duotone ki-double-check fs-1"><span class="path1"></span><span class="path2"></span></i>
                        Daftar Mutasi (Verifikasi)
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>