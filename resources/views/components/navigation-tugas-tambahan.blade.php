@php
    $ruangan = Auth::user()->karyawan->kd_ruangan;
@endphp
<div class="card-rounded bg-light d-flex flex-stack flex-wrap p-5 mb-8">
    <div class="hover-scroll-x">
        <ul class="nav flex-nowrap border-transparent fw-bold">
            @if ($ruangan == 91 || $ruangan == 57)
                <li class="nav-item my-1">
                    <a
                        class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.tugas-tambahan.*') ? 'active' : '' }}"
                        href="{{ route('admin.tugas-tambahan.index') }}"
                    >
                        <i class="ki-duotone ki-arrows-loop fs-1"><span class="path1"></span><span class="path2"></span></i>
                        Tugas Tambahan
                    </a>
                </li>
                <li class="nav-item my-1">
                    <a
                        class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.tugas-tambahan-pending.*') ? 'active' : '' }}"
                        href="{{ route('admin.tugas-tambahan-pending.index') }}"
                    >
                    <i class="ki-duotone ki-abstract-5 fs-1"><span class="path1"></span><span class="path2"></span></i>
                        <span class="menu-title me-2">
                            Daftar Tugas Tambahan (Tetunda)
                        </span>
                        <span class="menu-badge">
                            <span class="badge badge-sm badge-circle badge-danger count-status-proses">
                                {{ $totalTugasTambahanPending }}
                            </span>
                        </span>
                    </a>
                </li>
            @endif

            <li class="nav-item my-1">
                <a
                    class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.tugas-tambahan-on-process.*') ? 'active' : '' }}"
                    href="{{ route('admin.tugas-tambahan-on-process.index') }}"
                >
                    <i class="ki-duotone ki-loading fs-1"><span class="path1"></span><span class="path2"></span></i>
                    <span class="menu-title me-2">
                        Daftar Tugas Tambahan (Proses)
                    </span>
                    <span class="menu-badge">
                        <span class="badge badge-sm badge-circle badge-danger count-status-proses">
                            {{ $totalTugasTambahanOnProcess }}
                        </span>
                    </span>
                </a>
            </li>

            @if ($ruangan == 91 || $ruangan == 57)
                <li class="nav-item my-1">
                    <a
                        class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.tugas-tambahan-verifikasi.*') ? 'active' : '' }}"
                        href="{{ route('admin.tugas-tambahan-verifikasi.index') }}"
                    >
                        <i class="ki-duotone ki-double-check fs-1"><span class="path1"></span><span class="path2"></span></i>
                        Daftar Tugas Tambahan (Verifikasi)
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>