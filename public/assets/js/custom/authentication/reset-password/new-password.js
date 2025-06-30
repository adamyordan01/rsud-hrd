"use strict";
var KTAuthNewPassword = (function () {
    var t, // Form element
        e, // Submit button
        r, // FormValidation instance
        o; // PasswordMeter instance

    return {
        init: function () {
            // Inisialisasi elemen
            t = document.querySelector("#kt_new_password_form");
            e = document.querySelector("#kt_new_password_submit");
            o = KTPasswordMeter.getInstance(t.querySelector('[data-kt-password-meter="true"]'));

            // Inisialisasi FormValidation
            r = FormValidation.formValidation(t, {
                fields: {
                    password: {
                        validators: {
                            notEmpty: {
                                message: "Password wajib diisi"
                            },
                            stringLength: {
                                min: 8,
                                message: "Password minimal 8 karakter"
                            }
                        }
                    },
                    "password_confirmation": {
                        validators: {
                            notEmpty: {
                                message: "Konfirmasi password wajib diisi"
                            },
                            identical: {
                                compare: function () {
                                    return t.querySelector('[name="password"]').value;
                                },
                                message: "Password dan konfirmasinya harus sama"
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            });

            // Simpan instance FormValidation ke data atribut form
            t.setAttribute('data-form-validation', '');
            t.formValidationInstance = r;

            // Toggle visibility password
            const visibilityToggle = t.querySelector('[data-kt-password-meter-control="visibility"]');
            if (visibilityToggle) {
                visibilityToggle.addEventListener('click', function () {
                    const passwordInput = t.querySelector('[name="password"]');
                    const eyeSlash = this.querySelector('.ki-eye-slash');
                    const eye = this.querySelector('.ki-eye');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeSlash.classList.add('d-none');
                        eye.classList.remove('d-none');
                    } else {
                        passwordInput.type = 'password';
                        eyeSlash.classList.remove('d-none');
                        eye.classList.add('d-none');
                    }
                });
            }

            // Validasi real-time pada input password
            t.querySelector('[name="password"]').addEventListener("input", function () {
                r.revalidateField("password");
                if (o && typeof o.check === 'function') {
                    o.check();
                }
            });

            // Validasi real-time pada input konfirmasi password
            t.querySelector('[name="password_confirmation"]').addEventListener("input", function () {
                r.revalidateField("password_confirmation");
            });
        }
    };
})();

// Inisialisasi saat DOM siap
KTUtil.onDOMContentLoaded(function () {
    KTAuthNewPassword.init();
});