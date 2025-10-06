@php
    $jabatan = Auth::user()->karyawan->kd_jabatan_struktural;
    $ruangan = Auth::user()->karyawan->kd_ruangan;
    // var_dump($jabatan, $ruangan);
@endphp

<div id="kt_app_sidebar" class="app-sidebar  flex-column " data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <!--begin::Wrapper-->
    <div class="app-sidebar-wrapper">
        <div id="kt_app_sidebar_wrapper" class="hover-scroll-y my-5 my-lg-2 mx-4" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_header" data-kt-scroll-wrappers="#kt_app_sidebar_wrapper"
            data-kt-scroll-offset="5px">

            <!--begin::Sidebar menu-->
            <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false"
                class="app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention menu-state-bullet-primary px-3 mb-5">

                
                @if (Auth::check() && Auth::user()->hasAnyRole(['hrd_struktural', 'hrd_it_member', 'hrd_it_head', 'hrd_kepegawaian', 'hrd_superadmin']))
                    <div class="menu-item">
                        <a
                            class="menu-link @if (request()->routeIs('admin.dashboard.*')) active @endif"
                            href="{{ route('admin.dashboard.index') }}"
                        >
                            <span class="menu-icon">
                                <i class="ki-duotone ki-element-11 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>
                @endif



                {{-- <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link--><span class="menu-link"><span class="menu-icon"><i
                                class="ki-outline ki-home-2 fs-2"></i></span><span
                            class="menu-title">Dashboards</span><span class="menu-arrow"></span></span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link--><a class="menu-link" href="/metronic8/demo39/index.html"><span
                                    class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                                    class="menu-title">Default</span></a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link--><a class="menu-link"
                                href="/metronic8/demo39/dashboards/ecommerce.html"><span class="menu-bullet"><span
                                        class="bullet bullet-dot"></span></span><span
                                    class="menu-title">eCommerce</span></a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link--><a class="menu-link active"
                                href="/metronic8/demo39/dashboards/projects.html"><span class="menu-bullet"><span
                                        class="bullet bullet-dot"></span></span><span
                                    class="menu-title">Projects</span></a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link--><a class="menu-link"
                                href="/metronic8/demo39/dashboards/online-courses.html"><span class="menu-bullet"><span
                                        class="bullet bullet-dot"></span></span><span class="menu-title">Online
                                    Courses</span></a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link--><a class="menu-link"
                                href="/metronic8/demo39/dashboards/marketing.html"><span class="menu-bullet"><span
                                        class="bullet bullet-dot"></span></span><span
                                    class="menu-title">Marketing</span></a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <div class="menu-inner flex-column collapse " id="kt_app_sidebar_menu_dashboards_collapse">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/bidding.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Bidding</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/pos.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span class="menu-title">POS
                                        System</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/call-center.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span class="menu-title">Call
                                        Center</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/logistics.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Logistics</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/website-analytics.html"><span
                                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Website Analytics</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/finance-performance.html"><span
                                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Finance Performance</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/store-analytics.html"><span
                                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Store Analytics</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/social.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Social</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/delivery.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Delivery</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/crypto.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Crypto</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/school.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span
                                        class="menu-title">School</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link"
                                    href="/metronic8/demo39/dashboards/podcast.html"><span class="menu-bullet"><span
                                            class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Podcast</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link--><a class="menu-link" href="/metronic8/demo39/landing.html"><span
                                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                                        class="menu-title">Landing</span></a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <div class="menu-item">
                            <div class="menu-content">
                                <a class="btn btn-flex btn-color-primary d-flex flex-stack fs-base p-0 ms-2 mb-2 toggle collapsible collapsed"
                                    data-bs-toggle="collapse" href="#kt_app_sidebar_menu_dashboards_collapse"
                                    data-kt-toggle-text="Show Less">
                                    <span data-kt-toggle-text-target="true">Show 12 More</span> <i
                                        class="ki-outline ki-minus-square toggle-on fs-2 me-0"></i><i
                                        class="ki-outline ki-plus-square toggle-off fs-2 me-0"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end:Menu sub-->
                </div> --}}

                @if (Auth::check() && Auth::user()->hasAnyRole(['hrd_struktural', 'hrd_it_member', 'hrd_it_head', 'hrd_kepegawaian', 'hrd_superadmin']))
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.karyawan.*', 'admin.karyawan-luar.*', 'admin.karyawan-belum-lengkap.*', 'admin.pegawai-tidak-aktif.*') ? 'here show' : '' }}">
                        <span class="menu-link">
                            <span class="menu-icon">
                                {{-- <i class="ki-outline ki-gift fs-2"></i> --}}
                                <i class="ki-duotone ki-people fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">Pegawai</span><span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            <div class="menu-item">
                                <a 
                                    class="menu-link @if (request()->routeIs('admin.karyawan.*') && !request()->has('status')) active @endif"
                                    href="{{ route('admin.karyawan.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Seluruh Pegawai</span>
                                </a>
                            </div>
                            
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ (request()->routeIs('admin.karyawan.*') && (request()->has('status') || request()->has('jenis_pegawai'))) ? 'hover show' : '' }}">
                                <span class="menu-link">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jenis Pegawai</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion">
                                    <div class="menu-item">
                                        <a 
                                            class="menu-link @if (request()->routeIs('admin.karyawan.*') && request()->get('status') == '1') active @endif"
                                            href="{{ route('admin.karyawan.index', ['status' => 1]) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">PNS</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a 
                                            class="menu-link @if (request()->routeIs('admin.karyawan.*') && request()->get('status') == '7') active @endif"
                                            href="{{ route('admin.karyawan.index', ['status' => 7]) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">PPPK</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a 
                                            class="menu-link @if (request()->routeIs('admin.karyawan.*') && request()->get('status') == '3' && request()->get('jenis_pegawai') == '2') active @endif"
                                            href="{{ route('admin.karyawan.index', ['status' => 3, 'jenis_pegawai' => 2]) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">BLUD</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a 
                                            class="menu-link @if (request()->routeIs('admin.karyawan.*') && request()->get('status') == '3' && request()->get('jenis_pegawai') == '1') active @endif"
                                            href="{{ route('admin.karyawan.index', ['status' => 3, 'jenis_pegawai' => 1]) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">PEMKO</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a 
                                            class="menu-link @if (request()->routeIs('admin.karyawan.*') && request()->get('status') == '4') active @endif"
                                            href="{{ route('admin.karyawan.index', ['status' => 4]) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">PT</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a 
                                            class="menu-link @if (request()->routeIs('admin.karyawan.*') && request()->get('status') == '2') active @endif"
                                            href="{{ route('admin.karyawan.index', ['status' => 2]) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">Honor</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a 
                                            class="menu-link @if (request()->routeIs('admin.karyawan.*') && request()->get('status') == '6') active @endif"
                                            href="{{ route('admin.karyawan.index', ['status' => 6]) }}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">THL</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="menu-item">
                                <a 
                                    class="menu-link @if (request()->routeIs('admin.karyawan-luar.*')) active @endif"
                                    href="{{ route('admin.karyawan-luar.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Pegawai Luar</span>
                                </a>
                            </div>
                            
                            <div class="menu-item">
                                <a 
                                    class="menu-link @if (request()->routeIs('admin.karyawan-belum-lengkap.*')) active @endif"
                                    href="{{ route('admin.karyawan-belum-lengkap.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Pegawai Belum Lengkap</span>
                                </a>
                            </div>
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion @if (request()->routeIs('admin.pegawai-tidak-aktif.*')) hover show @endif">
                                <!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Pegawai Tidak Aktif</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <!--end:Menu link-->
                                
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <a class="menu-link @if (request()->routeIs('admin.pegawai-tidak-aktif.pensiun')) active @endif" 
                                           href="{{ route('admin.pegawai-tidak-aktif.pensiun') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Pegawai Pensiun</span>
                                        </a>
                                    </div>
                                    <!--end:Menu item-->
                                    
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <a class="menu-link @if (request()->routeIs('admin.pegawai-tidak-aktif.keluar')) active @endif" 
                                           href="{{ route('admin.pegawai-tidak-aktif.keluar') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Pegawai Keluar</span>
                                        </a>
                                    </div>
                                    <!--end:Menu item-->
                                    
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <a class="menu-link @if (request()->routeIs('admin.pegawai-tidak-aktif.tugas-belajar')) active @endif" 
                                           href="{{ route('admin.pegawai-tidak-aktif.tugas-belajar') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Pegawai Tugas Belajar</span>
                                        </a>
                                    </div>
                                    <!--end:Menu item-->
                                    
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <a class="menu-link @if (request()->routeIs('admin.pegawai-tidak-aktif.meninggal')) active @endif" 
                                           href="{{ route('admin.pegawai-tidak-aktif.meninggal') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Pegawai Meninggal Dunia</span>
                                        </a>
                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                        </div>
                        <!--end:Menu sub-->
                    </div>

                    {{-- menu untuk sk tanpa dropdown --}}
                    @hasPermission('hrd_view_sk_karyawan')
                    <div class="menu-item">
                        <a
                            class="menu-link @if (request()->routeIs('admin.sk-kontrak.*')) active @endif"
                            href="{{ route('admin.sk-kontrak.index') }}"
                        >
                            <span class="menu-icon">
                                <i class="ki-duotone ki-document fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">SK Karyawan</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    @endhasPermission
                
                    <!-- check jika $jabatan saat login adalah 19 atau 7 atau 3 atau 1 maka arahkan ke menu ini -->
                    @hasPermission('hrd_view_mutasi_karyawan')
                    @if ($jabatan == 19 || $jabatan == 7 || $jabatan == 3 || $jabatan == 1)
                        <div class="menu-item">
                            <a
                                class="menu-link @if (request()->routeIs('admin.mutasi*')) active @endif"
                                href="{{ route('admin.mutasi-on-process.index') }}"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-arrows-loop fs-2"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                                <span class="menu-title">Mutasi</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-danger">
                                        {{ $totalMutasiOnProcess }}
                                    </span>
                                </span>
                            </a>
                        </div>

                        <!-- menu tugas tambahan -->
                        <div class="menu-item">
                            <a
                                class="menu-link @if (request()->routeIs('admin.tugas-tambahan*')) active @endif"
                                href="{{ route('admin.tugas-tambahan.index') }}"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-office-bag fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </span>
                                <span class="menu-title">Tugas Tambahan</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-danger">
                                        {{ $totalTugasTambahan }}
                                    </span>
                                </span>
                            </a>
                        </div>
                    @else
                        <div class="menu-item">
                            <a
                                class="menu-link @if (request()->routeIs('admin.mutasi*')) active @endif"
                                href="{{ route('admin.mutasi.index') }}"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-arrows-loop fs-2"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                                <span class="menu-title">Mutasi</span>
                            </a>
                        </div>

                        <!-- menu tugas tambahan -->
                        <div class="menu-item">
                            <a
                                class="menu-link @if (request()->routeIs('admin.tugas-tambahan*')) active @endif"
                                href="{{ route('admin.tugas-tambahan.index') }}"
                            >
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-office-bag fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </span>
                                <span class="menu-title">Tugas Tambahan</span>
                            </a>
                        </div>
                    @endif
                    @endhasPermission
                @endif

                <!-- Export Menu -->
                <div class="menu-item">
                    <a 
                        class="menu-link @if (request()->routeIs('admin.export.*')) active @endif"
                        href="{{ route('admin.export.index') }}">
                            <span class="menu-icon">
                                <i class="ki-outline ki-file-down fs-2"></i>
                            </span>
                            <span class="menu-title">Export Data</span>
                    </a>
                </div>

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.laporan.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-filter-tablet fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Laporan</span><span class="menu-arrow"></span>
                    </span>
                    
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.duk.*')) active @endif"
                                href="{{ route('admin.laporan.duk.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Daftar Urut Kepangkatan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.pns-usia.*')) active @endif"
                                href="{{ route('admin.laporan.pns-usia.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Daftar PNS per Usia</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.struktural.*')) active @endif"
                                href="{{ route('admin.laporan.struktural.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Daftar Struktural</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.kepala-ruangan.*')) active @endif"
                                href="{{ route('admin.laporan.kepala-ruangan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Kepala Ruangan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.koordinator.*')) active @endif"
                                href="{{ route('admin.laporan.koordinator.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Koordinator</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.rekap-pegawai.*')) active @endif"
                                href="{{ route('admin.laporan.rekap-pegawai.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Rekap Pegawai Aktif</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.rekap-ruangan.*')) active @endif"
                                href="{{ route('admin.laporan.rekap-ruangan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Rekap Pegawai Per-Ruangan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.golongan.*')) active @endif"
                                href="{{ route('admin.laporan.golongan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Berdasarkan Golongan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.jenjang-pendidikan.*')) active @endif"
                                href="{{ route('admin.laporan.jenjang-pendidikan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jenjang Pendidikan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.per-pendidikan.*')) active @endif"
                                href="{{ route('admin.laporan.per-pendidikan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jumlah Pegawai Per-Pendidikan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.per-pendidikan-ruangan.*')) active @endif"
                                href="{{ route('admin.laporan.per-pendidikan-ruangan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jumlah Pegawai Per-Pendidikan Per-Ruangan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.per-jenis-tenaga.*')) active @endif"
                                href="{{ route('admin.laporan.per-jenis-tenaga.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jumlah Pegawai Per-Jenis Tenaga</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.per-jenis-tenaga-ruangan.*')) active @endif"
                                href="{{ route('admin.laporan.per-jenis-tenaga-ruangan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jumlah Pegawai Per-Jenis Tenaga Per-Ruangan</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.data-taspen.*')) active @endif"
                                href="{{ route('admin.laporan.data-taspen.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Data Taspen</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.satya-lencana.*')) active @endif"
                                href="{{ route('admin.laporan.satya-lencana.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Satya Lencana</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.jabatan-fungsional.*')) active @endif"
                                href="{{ route('admin.laporan.jabatan-fungsional.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jabatan Fungsional</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.surat-izin.*')) active @endif"
                                href="{{ route('admin.laporan.surat-izin.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Surat Izin</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.laporan.absensi.*')) active @endif"
                                href="{{ route('admin.laporan.absensi.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Absensi Pegawai</span>
                            </a>
                        </div>
                    </div>
                    <!--end:Menu sub-->
                </div>

                <!-- user management -->
                @hasPermission('view_user_management')
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.user-management.*', 'admin.settings.user-management.*') ? 'here show' : '' }}">
                        <span class="menu-link">
                            {{-- <span class="menu-icon"><i class="ki-outline ki-gift fs-2"></i></span> --}}
                            <span class="menu-icon">
                                <i class="ki-duotone ki-people fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </span>
                            <span class="menu-title">User Management</span><span class="menu-arrow"></span>
                        </span>
                        
                        <div class="menu-sub menu-sub-accordion">
                            <!-- Native HRD User Access -->
                            <div class="menu-item">
                                <a 
                                    class="menu-link @if (request()->routeIs('admin.settings.user-management.*')) active @endif"
                                    href="{{ route('admin.settings.user-management.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">HRD User Access</span>
                                </a>
                            </div>
                            
                            @hasPermission('view_roles')
                                <div class="menu-item">
                                    <a 
                                        class="menu-link @if (request()->routeIs('admin.user-management.roles.*')) active @endif"
                                        href="{{ route('admin.user-management.roles.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Roles</span>
                                    </a>
                                </div>
                            @endhasPermission

                            @hasPermission('view_permissions')
                            <div class="menu-item">
                                <a 
                                    class="menu-link @if (request()->routeIs('admin.user-management.permissions.*')) active @endif"
                                    href="{{ route('admin.user-management.permissions.index') }}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Permission</span>
                                </a>
                            </div>
                            @endhasPermission

                            @hasPermission('view_users')
                                <div class="menu-item">
                                    <a 
                                        class="menu-link @if (request()->routeIs('admin.user-management.users.*')) active @endif"
                                        href="{{ route('admin.user-management.users.index') }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Users</span>
                                    </a>
                                </div>
                            @endhasPermission
                        </div>
                        <!--end:Menu sub-->
                    </div>
                @endhasPermission
                
                @hasPermission('hrd_view_settings')
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.settings.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        {{-- <span class="menu-icon"><i class="ki-outline ki-gift fs-2"></i></span> --}}
                        <span class="menu-icon">
                            <i class="ki-duotone ki-gear fs-2"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                        <span class="menu-title">Master</span><span class="menu-arrow"></span>
                    </span>
                    
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.settings.jenjang-pendidikan.*')) active @endif"
                                href="{{ route('admin.settings.jenjang-pendidikan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jenjang Pendidikan</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.settings.ruangan.*')) active @endif"
                                href="{{ route('admin.settings.ruangan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Ruangan</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.settings.jurusan.*')) active @endif"
                                href="{{ route('admin.settings.jurusan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Jurusan</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.settings.tenaga-management.*')) active @endif"
                                href="{{ route('admin.settings.tenaga-management.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Tenaga Management</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.settings.pekerjaan.*')) active @endif"
                                href="{{ route('admin.settings.pekerjaan.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Pekerjaan</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.settings.hubungan_keluarga.*')) active @endif"
                                href="{{ route('admin.settings.hubungan_keluarga.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Hubungan Keluarga</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.settings.bahasa.*')) active @endif"
                                href="{{ route('admin.settings.bahasa.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Bahasa</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a 
                                class="menu-link @if (request()->routeIs('admin.settings.wilayah.*')) active @endif"
                                href="{{ route('admin.settings.wilayah.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Master Wilayah</span>
                            </a>
                        </div>

                    </div>
                    <!--end:Menu sub-->
                </div>
                @endhasPermission
            </div>
            <!--end::Sidebar menu-->
        </div>
    </div>
    <!--end::Wrapper-->
</div>