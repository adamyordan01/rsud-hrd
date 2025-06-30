@extends('layouts.auth', ['title' => 'New Password'])

@section('content')
    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
        <div class="d-flex flex-center flex-column flex-lg-row-fluid">
            <div class="w-lg-500px p-10">
                <form class="form w-100" novalidate="novalidate" id="kt_new_password_form"
                    action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                    <div class="text-center mb-10">
                        <h1 class="text-gray-900 fw-bolder mb-3">
                            Atur Ulang Password
                        </h1>
                        <div class="text-gray-500 fw-semibold fs-6">
                            Sudah mereset password?
                            <a href="{{ route('login') }}" class="link-primary fw-bold">
                                Masuk
                            </a>
                        </div>
                    </div>

                    <div class="fv-row mb-8">
                        <div class="position-relative">
                            <input class="form-control bg-transparent" type="password"
                                placeholder="Password" name="password" autocomplete="off" />
                            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                id="toggle-password-visibility">
                                <i class="ki-outline ki-eye-slash fs-2"></i>
                                <i class="ki-outline ki-eye fs-2 d-none"></i>
                            </span>
                        </div>
                        <div id="password-error" class="invalid-feedback" style="display: none;"></div>
                    </div>

                    <div class="fv-row mb-8">
                        <input type="password" placeholder="Repeat Password" name="password_confirmation"
                            autocomplete="off" class="form-control bg-transparent" />
                        <div id="password_confirmation-error" class="invalid-feedback" style="display: none;"></div>
                    </div>

                    <div class="d-grid mb-10">
                        <button type="submit" id="kt_new_password_submit" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">
                                Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
    $(document).ready(function() {
        // Toggle visibility password
        $('#toggle-password-visibility').on('click', function() {
            const passwordInput = $('input[name="password"]');
            const eyeSlash = $(this).find('.ki-eye-slash');
            const eye = $(this).find('.ki-eye');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                eyeSlash.addClass('d-none');
                eye.removeClass('d-none');
            } else {
                passwordInput.attr('type', 'password');
                eyeSlash.removeClass('d-none');
                eye.addClass('d-none');
            }
        });

        // Handle form submission with AJAX
        $('#kt_new_password_form').on('submit', function(e) {
            e.preventDefault();

            // Show loading state
            let $button = $('#kt_new_password_submit');
            $button.addClass('disabled');
            $button.find('.indicator-label').hide();
            $button.find('.indicator-progress').show();

            // Clear previous errors
            $('input[name="password"]').removeClass('is-invalid');
            $('input[name="password_confirmation"]').removeClass('is-invalid');
            $('#password-error, #password_confirmation-error').hide().text('');

            $.ajax({
                url: '{{ route("password.update") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $button.removeClass('disabled');
                    $button.find('.indicator-label').show();
                    $button.find('.indicator-progress').hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    }).then(function() {
                        window.location.href = response.redirect;
                    });
                },
                error: function(xhr) {
                    $button.removeClass('disabled');
                    $button.find('.indicator-label').show();
                    $button.find('.indicator-progress').hide();

                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.email) {
                            // Tampilkan error email via SweetAlert karena field hidden
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errors.email[0],
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        }
                        if (errors.password) {
                            $('input[name="password"]').addClass('is-invalid');
                            $('#password-error').text(errors.password[0]).show();
                        }
                        if (errors.password_confirmation) {
                            $('input[name="password_confirmation"]').addClass('is-invalid');
                            $('#password_confirmation-error').text(errors.password_confirmation[0]).show();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message || 'Something went wrong!',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                }
            });
        });

        // Clear error on input change
        $('input[name="password"], input[name="password_confirmation"]').on('input', function() {
            $(this).removeClass('is-invalid');
            $(this).nextAll('.invalid-feedback').hide().text('');
        });
    });
    </script>
@endpush