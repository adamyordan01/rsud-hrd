<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        {{ $title ?? 'RSUD Langsa | HRD' }}
    </title>
    <meta charset="utf-8" />
    <meta name="description" content="HRD - RSUD LANGSA" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Aplikasi HRD - RSUD Langsa" />
    <meta property="og:url" content="https://e-rsud.langsakota.go.id/rsud_hrd/" />
    <meta property="og:site_name" content="RSUD LANGSA" />

    @stack('meta')

    <link rel="canonical" href="https://e-rsud.langsakota.go.id/rsud_hrd/login" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->

    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/custom/vis-timeline/vis-timeline.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <!--end::Vendor Stylesheets-->


    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    @stack('styles')

    <!--begin::Google tag-->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-37564768-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-37564768-1');
    </script>
    <!--end::Google tag-->
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking)
        if (window.top != window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>
</head>
<!--end::Head-->

<!--begin::Body-->

<body id="kt_app_body" data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="on"
    data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true"
    data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
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
    <!--Begin::Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5FS8GGP" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!--End::Google Tag Manager (noscript) -->

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
            @include('layouts.user-modules.topbar')
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
    
    <!-- modal ketika ubah password -->
    <div class="modal fade" id="editPasswordModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-550px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_agenda_header">
                    <h2 class="fw-bold modal-title title-edit-rule">Edit Password</h2>

                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form
                    action="{{ route('change-password') }}"
                    method="POST"
                    class="form"
                    id="editPasswordForm"
                >
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" />
                    <div class="modal-body">
                        <!-- password saat ini -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Password Saat Ini
                            </label>
                            <input type="password" class="form-control form-control-solid" name="current_password" id="current_password" autofocus placeholder="Password Saat Ini" />
                            <div class="fv-plugins-message-container invalid-feedback error-text current_password_error"></div>
                        </div>

                        <!-- password baru -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Password Baru
                            </label>
                            <input type="password" class="form-control form-control-solid" name="new_password" id="new_password" autofocus placeholder="Password Baru" />
                            <div class="fv-plugins-message-container invalid-feedback error-text new_password_error"></div>
                        </div>

                        <!-- konfirmasi password baru -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Konfirmasi Password Baru
                            </label>
                            <input type="password" class="form-control form-control-solid" name="new_password_confirmation" id="new_password_confirmation" autofocus placeholder="Konfirmasi Password Baru" />
                            <div class="fv-plugins-message-container invalid-feedback error-text new_password_confirmation_error"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-btn" id="submit" data-kt-menu-modal-action="submit">
                            <span class="indicator-label">
                                Save Changes
                            </span>
                            <span class="indicator-progress">
                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        var hostUrl = "/metronic8/demo39/assets/";
    </script>

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
        // ketika #change-password di klik maka tampilkan modal edit password
        $('#change-password').on('click', function() {
            $('#editPasswordModal').modal('show');
        });

        // ketika form edit password di submit
        $('#editPasswordForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = new FormData(form[0]);

            var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
            $(form).find('#submit').append(loadingIndicator);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    loadingIndicator.show();
                    $('.error-text').text('');
                    $('.submit-btn').attr('disabled', true).find('.btn-text').addClass('d-none');
                    $('.submit-btn').find('.spinner-border').removeClass('d-none');
                    $(form).find('.btn-primary').attr('disabled', true);
                    $(form).find('.btn-primary .indicator-label').hide();
                },
                success: function (response) {
                    if (response.code === 200) {
                        toastr.success(response.message, 'Success');
                        $('#editPasswordModal').modal('hide');
                        form[0].reset();
                    } else {
                        toastr.error(response.message, 'Error');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('.' + key + '_error').text(value[0]);
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.');
                    }
                },
                complete: function () {
                    loadingIndicator.hide();
                    $('.submit-btn').attr('disabled', false).find('.btn-text').removeClass('d-none');
                    $('.submit-btn').find('.spinner-border').addClass('d-none');
                    $(form).find('.btn-primary').attr('disabled', false);
                    $(form).find('.btn-primary .indicator-label').show();
                }
            });
        });
    </script>

    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>