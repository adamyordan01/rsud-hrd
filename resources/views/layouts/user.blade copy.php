<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $title ?? 'Portal Pegawai' }} | RSUD HRD</title>
    <meta charset="utf-8" />
    <meta name="description" content="Portal Pegawai RSUD HRD - Sistem Informasi Kepegawaian" />
    <meta name="keywords" content="RSUD, HRD, Portal Pegawai, Kepegawaian" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Portal Pegawai RSUD HRD" />
    <meta property="og:site_name" content="RSUD HRD" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->

    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    @stack('vendor-styles')
    <!--end::Vendor Stylesheets-->

    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    @stack('styles')
    
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->

    <!--begin::Custom Styles-->
    {{-- <style>
        :root {
            --kt-user-primary: #1e40af;
            --kt-user-primary-light: #dbeafe;
            --kt-user-sidebar-bg: #f8fafc;
            --kt-user-sidebar-text: #475569;
            --kt-user-sidebar-hover: #e2e8f0;
        }
        
        /* User Area Styling */
        .user-area {
            --kt-primary: var(--kt-user-primary);
        }
        
        /* Sidebar khusus user - hanya styling warna, bukan layout */
        .user-area .app-sidebar .menu-link {
            color: var(--kt-user-sidebar-text);
            border-radius: 8px;
            margin: 2px 0;
        }
        
        .user-area .app-sidebar .menu-link:hover {
            background: var(--kt-user-sidebar-hover);
            color: var(--kt-user-primary);
        }
        
        .user-area .app-sidebar .menu-link.active {
            background: var(--kt-user-primary-light);
            color: var(--kt-user-primary);
            font-weight: 600;
        }
        
        /* Header styling */
        .user-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom button styles for user area */
        .btn-user-primary {
            background: var(--kt-user-primary);
            border-color: var(--kt-user-primary);
            color: white;
        }
        
        .btn-user-primary:hover {
            background: #1e3a8a;
            border-color: #1e3a8a;
            color: white;
        }
    </style> --}}
</head>

<body id="kt_app_body" data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="on"
    data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true"
    data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">

    <!--begin::loader-->
    <div class="app-page-loader flex-column">
        <span class="spinner-border text-primary" role="status"></span>
        <span class="text-muted fs-6 fw-semibold mt-5">Loading...</span>
    </div>
    <!--end::Loader-->

    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page  flex-column flex-column-fluid " id="kt_app_page">
            <!--begin::Header-->
            @include('layouts.user-modules.header')
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper  flex-column flex-row-fluid " id="kt_app_wrapper">
                <!--begin::Sidebar-->
                @include('layouts.user-modules.sidebar')
                <!--end::Sidebar-->


                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid " id="kt_app_main">
                    <!--begin::Content wrapper-->
                    <div class="d-flex flex-column flex-column-fluid">

                        <!--begin::Toolbar-->
                        @yield('toolbar')
                        <!--end::Toolbar-->

                        <!--begin::Content-->
                        @yield('content')
                        <!--end::Content-->

                    </div>
                    <!--end::Content wrapper-->


                    <!--begin::Footer-->
                    @include('layouts.user-modules.footer')
                    <!--end::Footer-->
                </div>
                <!--end:::Main-->


            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
    
    <!--begin::Modal - Change Password-->
    <div class="modal fade" id="editPasswordModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-550px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title">Ganti Password</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form action="{{ route('change-password') }}" method="POST" class="form" id="editPasswordForm">
                    @csrf
                    <div class="modal-body">
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Password Saat Ini</label>
                            <input type="password" class="form-control form-control-solid" name="current_password" placeholder="Password Saat Ini" />
                            <div class="fv-plugins-message-container invalid-feedback error-text current_password_error"></div>
                        </div>
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Password Baru</label>
                            <input type="password" class="form-control form-control-solid" name="password" placeholder="Password Baru" />
                            <div class="fv-plugins-message-container invalid-feedback error-text password_error"></div>
                        </div>
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control form-control-solid" name="password_confirmation" placeholder="Konfirmasi Password Baru" />
                            <div class="fv-plugins-message-container invalid-feedback error-text password_confirmation_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-user-primary submit-btn">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Modal - Change Password-->

    <!--begin::Javascript-->
    <script>var hostUrl = "/metronic8/demo39/assets/";</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->

    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/vis-timeline/vis-timeline.bundle.js') }}"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <!--end::Vendors Javascript-->

    <!--begin::Custom Javascript(used for this page only)-->
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-campaign.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-project/type.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-project/budget.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-project/settings.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-project/team.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-project/targets.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-project/files.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-project/complete.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-project/main.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/new-address.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>
    <!--end::Custom Javascript-->

    @stack('scripts')

    <script>
        // CSRF Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Change Password Modal
        $('#change-password').on('click', function() {
            $('#editPasswordModal').modal('show');
        });

        // Change Password Form
        $('#editPasswordForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = new FormData(form[0]);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('.error-text').text('');
                    form.find('.submit-btn').attr('disabled', true);
                    form.find('.indicator-label').hide();
                    form.find('.indicator-progress').show();
                },
                success: function (response) {
                    $('#editPasswordModal').modal('hide');
                    form[0].reset();
                    
                    Swal.fire({
                        text: "Password berhasil diubah!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-user-primary"
                        }
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('.' + key + '_error').text(value[0]);
                        });
                    } else {
                        Swal.fire({
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-user-primary"
                            }
                        });
                    }
                },
                complete: function () {
                    form.find('.submit-btn').attr('disabled', false);
                    form.find('.indicator-label').show();
                    form.find('.indicator-progress').hide();
                }
            });
        });
    </script>

    <script>
        // Debug script untuk memastikan layout initialization
        document.addEventListener('DOMContentLoaded', function() {
            console.log('User layout loaded');
            
            // Pastikan layout classes sudah benar
            const appMain = document.getElementById('kt_app_main');
            const appWrapper = document.getElementById('kt_app_wrapper');
            const appSidebar = document.getElementById('kt_app_sidebar');
            
            console.log('App main:', appMain ? 'found' : 'missing');
            console.log('App wrapper:', appWrapper ? 'found' : 'missing');
            console.log('App sidebar:', appSidebar ? 'found' : 'missing');
            
            // Force layout refresh jika diperlukan
            if (window.KTApp && typeof window.KTApp.init === 'function') {
                window.KTApp.init();
                console.log('KTApp initialized');
            }
        });
    </script>
</body>
</html>