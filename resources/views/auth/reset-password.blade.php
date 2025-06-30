@extends('layouts.auth', ['title' => 'Reset Password'])

@section('content')
    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
        <div class="d-flex flex-center flex-column flex-lg-row-fluid">
            <div class="w-lg-500px p-10">

                <form 
                    class="form w-100" 
                    novalidate="novalidate" 
                    id="forgot-password-form"
                >
                    @csrf
                    <div class="text-center mb-10">
                        <h1 class="text-gray-900 fw-bolder mb-3">
                            Lupa password ?
                        </h1>

                        <div class="text-gray-500 fw-semibold fs-6">
                            Masukkan email Anda untuk mereset password.
                        </div>
                    </div>

                    <div class="fv-row mb-8">
                        <input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" />
                        <div id="email-error" class="invalid-feedback" style="display: none;"></div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                        <button type="submit" id="kt_password_reset_submit" class="btn btn-primary me-4">

                            <span class="indicator-label">
                                Submit
                            </span>

                            <span class="indicator-progress">
                                Please wait... <span
                                    class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span></button>

                        <a href="{{ route('login') }}"
                            class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#forgot-password-form').on('submit', function(e) {
        e.preventDefault();

        // Show loading state
        let $button = $('#kt_password_reset_submit');
        $button.addClass('disabled');
        $button.find('.indicator-label').hide();
        $button.find('.indicator-progress').show();

        // Clear previous errors
        $('#email').removeClass('is-invalid');
        $('#email-error').hide().text('');

        $.ajax({
            url: '{{ route('forgot-password.send-email') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Reset button state
                $button.removeClass('disabled');
                $button.find('.indicator-label').show();
                $button.find('.indicator-progress').hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    confirmButtonText: 'OK'
                });
            },
            error: function(xhr) {
                // Reset button state
                $button.removeClass('disabled');
                $button.find('.indicator-label').show();
                $button.find('.indicator-progress').hide();

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    // Handle validation errors
                    let errors = xhr.responseJSON.errors;
                    if (errors.email) {
                        $('#email').addClass('is-invalid');
                        $('#email-error').text(errors.email[0]).show();
                    }
                } else {
                    // Handle other errors (e.g., email not found)
                    let errorMsg = xhr.responseJSON.message || 'Something went wrong!';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });

        // Clear error on input change
        $('#email').on('input', function() {
            $(this).removeClass('is-invalid');
            $('#email-error').hide().text('');
        });
    });
</script>
@endpush