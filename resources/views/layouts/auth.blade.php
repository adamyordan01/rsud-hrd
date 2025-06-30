
<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic 
Product Version: 8.2.3
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head>
<base href="../../../" />
		<title>HRD - Login</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:url" content="https://e-rsud.langsakota.go.id/rsud_hrd/login" />
		<meta property="og:site_name" content="RSUD Langsa" />
		<!--csrf-token-->
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="canonical" href="" />
		<link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>

        @stack('styles')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank">
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
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">

				@yield('content')
                
				<div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" style="background-image: url({{ asset('assets/media/misc/auth-bg.png') }})">
					<div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
						<a href="index.html" class="mb-0 mb-lg-12">
							<img alt="Logo" src="{{ asset('assets/media/logos/logo-hrd-2.png') }}" class="h-60px h-lg-125px" />
						</a>
						{{-- <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20" src="assets/media/misc/auth-screens.png" alt="" />
						<h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">Fast, Efficient and Productive</h1>
						<div class="d-none d-lg-block text-white fs-base text-center">In this kind of post, 
						<a href="#" class="opacity-75-hover text-warning fw-bold me-1">the blogger</a>introduces a person they’ve interviewed 
						<br />and provides some background information about 
						<a href="#" class="opacity-75-hover text-warning fw-bold me-1">the interviewee</a>and their 
						<br />work following this is a transcript of the interview.</div> --}}
					</div>
				</div>
			</div>
		</div>
		<script>var hostUrl = "assets/";</script>
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/custom/authentication/sign-in/general.js') }}"></script>

		<script>
			$.ajaxSetup({
				headers:{
					'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
				}
			});
			
			@if(session('error'))
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: '{{ session('error') }}',
				});
			@endif

			$(document).on('click', '[data-kt-password-meter-control="visibility"]', function () {
                var input = $(this).closest('.position-relative').find('input');
                var icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.toggleClass('d-none');
                } else {
                    input.attr('type', 'password');
                    icon.toggleClass('d-none');
                }
            });

			$('#kt_sign_in_form').submit(function(e){
				e.preventDefault();

				var formData = new FormData(this);
				var form = this;

				var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
				$(form).find('.btn-primary').append(loadingIndicator);

				$.ajax({
					url: $(this).attr('action'),
					method: $(this).attr('method'),
					data: formData,
					processData: false,
					contentType: false,
					beforeSend: function(){
						loadingIndicator.show();

						$(form).find('.invalid-feedback').remove();
						$(form).find('.is-invalid').removeClass('is-invalid');

						$(form).find('.btn-primary').attr('disabled', true);
                    	$(form).find('.btn-primary .indicator-label').hide();
					},
					complete: function(){
						loadingIndicator.remove();

						$(form).find('.btn-primary').attr('disabled', false);
						$(form).find('.btn-primary .indicator-label').show();
					},
					success: function(data){
						if(data.success == true){
							toastr.success(data.message, 'Success');

							// Redirect to dashboard after 1.5 seconds
							setTimeout(function(){
								window.location.href = data.redirect;
							}, 1500);
						}else{
							$('#kt_sign_in_submit').attr('disabled', false);
							$('#kt_sign_in_submit').html('Sign In');
							Swal.fire({
								icon: 'error',
								title: 'Oops...',
								text: data.errors.login,
							});
						}
					},
					error: function(xhr) {
						if (xhr.status === 419) {
							refreshCsrfToken().done(function () {
								toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
							})
						} else if (xhr.status === 500) {
							toastr.error('Internal Server Error', xhr.statusText);
						} else {
							handleFormErrors(xhr.responseJSON.errors);
						}
					}
				});
			});

			function handleFormErrors(errors) {
				if (!$.isEmptyObject(errors)) {
					$('.error-text').remove();

					$.each(errors, function(key, value) {
						$('#' + key).closest('.fv-row').append('<div class="fv-plugins-message-container invalid-feedback error-text '+ key +'_error">' + value + '</div>');
					})
				}
			}


			function refreshCsrfToken() {
				return $.get('/refresh-csrf').done(function(data) {
					$('meta[name="csrf-token"]').attr('content', data.csrf_token);

					// update @csrf too
					$('input[name="_token"]').val(data.csrf_token);

					// Update the token in the AJAX setup
					$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': data.csrf_token
						}
					});
				});
			}
		</script>

        @stack('scripts')
	</body>
</html>