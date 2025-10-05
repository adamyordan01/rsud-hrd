<div id="kt_app_header" class="app-header  d-flex flex-column flex-stack ">

    <div class="d-flex flex-stack flex-grow-1">

        <div class="app-header-logo d-flex align-items-center ps-lg-12" id="kt_app_header_logo">
            <div id="kt_app_sidebar_toggle"
                class="app-sidebar-toggle btn btn-sm btn-icon bg-body btn-color-gray-500 btn-active-color-primary w-30px h-30px ms-n2 me-4 d-none d-lg-flex "
                data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                data-kt-toggle-name="app-sidebar-minimize">

                <i class="ki-outline ki-abstract-14 fs-3 mt-1"></i> </div>

            <div class="btn btn-icon btn-active-color-primary w-35px h-35px ms-3 me-2 d-flex d-lg-none"
                id="kt_app_sidebar_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-2"></i> </div>

            <a href="{{ route('user.dashboard') }}" class="app-sidebar-logo">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-hrd.png') }}" class="h-35px theme-light-show" />
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-hrd.png') }}"
                    class="h-35px theme-dark-show" />
            </a>
        </div>

        <div class="app-navbar flex-grow-1 justify-content-end" id="kt_app_header_navbar">
            <div class="app-navbar-item d-flex align-items-stretch flex-lg-grow-1">

                <div id="kt_header_search" class="header-search d-flex align-items-center w-lg-350px"
                    data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter"
                    data-kt-search-layout="menu" data-kt-search-responsive="true" data-kt-menu-trigger="auto"
                    data-kt-menu-permanent="true" data-kt-menu-placement="bottom-start">

                    <div data-kt-search-element="toggle"
                        class="search-toggle-mobile d-flex d-lg-none align-items-center">
                        <div class="d-flex ">
                            <i class="ki-outline ki-magnifier fs-1 fs-1"></i> </div>
                    </div>
                </div>
            </div>

            <div class="app-navbar-item ms-2 ms-lg-6 me-2 me-lg-6" id="kt_header_user_menu_toggle">
                <!--begin::Menu wrapper-->
                @php
                    // Ambil nama user
                    $userNama = auth()->user()->name;

                    // Pisahkan nama menjadi array berdasarkan spasi
                    $namaArray = explode(' ', $userNama);

                    // Inisialisasi variabel untuk nama yang akan digunakan dalam URL avatar
                    $nama = '';
                    $defaultNama = 'Default+Name'; // Nilai default jika terjadi error

                    // Tentukan nama yang akan digunakan berdasarkan jumlah kata dalam array nama
                    if (count($namaArray) >= 2) {
                        // Jika ada lebih dari atau sama dengan 2 kata, gunakan kata pertama dan kedua
                        $nama = $namaArray[0] . '+' . $namaArray[1];
                    } elseif (count($namaArray) == 1) {
                        // Jika hanya ada 1 kata, gunakan kata tersebut
                        $nama = $namaArray[0];
                    } else {
                        // Jika terjadi error atau tidak ada kata, gunakan nilai default
                        $nama = $defaultNama;
                    }

                    // Ambil inisial nama user (inisial dari kata pertama)
                    $inisial = substr($namaArray[0], 0, 1);

                    // Buat URL avatar menggunakan nama yang telah ditentukan
                    $avatar = 'https://ui-avatars.com/api/?name=' . $nama . '&background=random';
                @endphp
                <div class="cursor-pointer symbol symbol-circle symbol-30px symbol-lg-45px"
                    data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                    data-kt-menu-placement="bottom-end">
                    <img
                        src="{{ $avatar }}"
                        alt="{{ auth()->user()->nama }}"
                    />
                </div>

                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-325px"
                    data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <img
                                    alt="{{ auth()->user()->karyawan->nama }}"
                                    src="{{ $avatar }}"
                                />
                            </div>
                            <!--end::Avatar-->

                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">
                                    {{ auth()->user()->karyawan->nama }}
                                </div>

                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
                                    {{ auth()->user()->karyawan->kd_karyawan }} - {{ auth()->user()->karyawan->email }}
                                </a>
                            </div>
                            <!--end::Username-->
                        </div>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu separator-->
                    <div class="separator my-2"></div>
                    <!--end::Menu separator-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="#" class="menu-link px-5">
                            My Profile
                        </a>
                    </div>
                    <!--end::Menu item-->
                    
                    <div class="menu-item px-5">
                        <a href="{{ route('user.kepegawaian.surat-izin') }}" class="menu-link px-5">
                            Surat Izin
                        </a>
                    </div>

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-text">Daftar Riwayat Hidup</span>
                            <span class="menu-badge">
                                <span class="badge badge-light-danger badge-circle fw-bold fs-7">3</span>
                            </span>
                        </a>
                    </div>

                    <!--begin::Menu separator-->
                    <div class="separator my-2"></div>
                    <!--end::Menu separator-->

                    @if(Auth::user()->roles->count() > 1)
                    <!--begin::Role switcher menu item-->
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-title position-relative">
                                {{ Session::get('active_role_display', 'Role') }}

                                <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                    <i class="ki-outline ki-user-tick fs-2"></i>
                                </span>
                            </span>
                        </a>

                        <!--begin::Role menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-200px"
                            data-kt-menu="true">
                            @foreach(Auth::user()->getHrdRoles() as $role)
                                @php
                                    $roleDisplayNames = [
                                        'pegawai_biasa' => 'Pegawai',
                                        'struktural' => 'Pejabat Struktural', 
                                        'it_member' => 'Staff IT',
                                        'it_head' => 'Kepala IT',
                                        'kepegawaian' => 'Staff Kepegawaian',
                                        'superadmin' => 'Super Administrator',
                                        'pegawai_viewer' => 'Viewer Pegawai'
                                    ];
                                    
                                    $roleIcons = [
                                        'pegawai_biasa' => 'ki-outline ki-user',
                                        'struktural' => 'ki-outline ki-people',
                                        'it_member' => 'ki-outline ki-code',
                                        'it_head' => 'ki-outline ki-crown',
                                        'kepegawaian' => 'ki-outline ki-document',
                                        'superadmin' => 'ki-outline ki-shield-tick',
                                        'pegawai_viewer' => 'ki-outline ki-eye'
                                    ];
                                    
                                    $displayName = $roleDisplayNames[$role] ?? ucwords(str_replace('_', ' ', $role));
                                    $icon = $roleIcons[$role] ?? 'ki-outline ki-user-tick';
                                    $isActive = Session::get('active_role') === 'hrd_'.$role;
                                @endphp
                                
                                <!--begin::Role item-->
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2 role-switch-item" 
                                       data-role="{{ $role }}"
                                       @if($isActive) style="background-color: var(--bs-primary-bg-subtle);" @endif>
                                        <span class="menu-icon">
                                            <i class="{{ $icon }} fs-2"></i>
                                        </span>
                                        <span class="menu-title">
                                            {{ $displayName }}
                                        </span>
                                        @if($isActive)
                                            <span class="ms-auto">
                                                <i class="ki-outline ki-check fs-2 text-success"></i>
                                            </span>
                                        @endif
                                    </a>
                                </div>
                                <!--end::Role item-->
                            @endforeach
                        </div>
                        <!--end::Role menu-->
                    </div>
                    <!--end::Role switcher menu item-->
                    @endif

                    <!--begin::Menu item-->
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-title position-relative">
                                Mode

                                <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                    <i class="ki-outline ki-night-day theme-light-show fs-2"></i> <i
                                        class="ki-outline ki-moon theme-dark-show fs-2"></i> </span>
                            </span>
                        </a>

                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                            data-kt-menu="true" data-kt-element="theme-mode-menu">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-outline ki-night-day fs-2"></i> </span>
                                    <span class="menu-title">
                                        Light
                                    </span>
                                </a>
                            </div>
                            <!--end::Menu item-->

                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-outline ki-moon fs-2"></i> </span>
                                    <span class="menu-title">
                                        Dark
                                    </span>
                                </a>
                            </div>
                            <!--end::Menu item-->

                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-outline ki-screen fs-2"></i> </span>
                                    <span class="menu-title">
                                        System
                                    </span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->

                    </div>
                    

                    <!--begin::Menu item-->
                    <div class="menu-item px-5 my-1">
                        <a href="javascript:void(0);" class="menu-link px-5" id="change-password">
                            Ubah Password
                        </a>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="menu-link px-5">
                            Sign Out
                        </a>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    <!--end::Menu item-->
                </div>
                <!--end::User account menu-->
                <!--end::Menu wrapper-->
            </div>

            @guest
                <div class="app-navbar-item ms-2 ms-lg-6 me-lg-6">
                    <a href="#"
                        class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-primary w-35px h-35px w-md-40px h-md-40px">
                        <i class="ki-outline ki-exit-right fs-1"></i>
                    </a>
                </div>
            @endguest

        </div>
    </div>

    <div class="app-header-separator"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle role switching
    document.querySelectorAll('.role-switch-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedRole = this.getAttribute('data-role');
            const currentActive = document.querySelector('.role-switch-item[style*="background-color"]');
            
            // Show loading state
            const originalText = this.querySelector('.menu-title').textContent;
            this.querySelector('.menu-title').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Switching...';
            
            // Make AJAX request to switch role
            fetch('{{ route("role-selector.switch") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    role: selectedRole
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Role berhasil diubah');
                    }
                    
                    // Redirect to appropriate dashboard
                    window.location.href = data.redirect;
                } else {
                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Gagal mengubah role');
                    }
                    
                    // Restore original text
                    this.querySelector('.menu-title').textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Terjadi kesalahan saat mengubah role');
                }
                
                // Restore original text
                this.querySelector('.menu-title').textContent = originalText;
            });
        });
    });
});
</script>