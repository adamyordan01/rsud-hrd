@extends('layouts.backend', ['title' => 'Riwayat'])

@inject('carbon', 'Carbon\Carbon')

@php
    
@endphp

@push('styles')
@endpush


@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2 ">
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Karyawan
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Riwayat SIP
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content  flex-column-fluid ">
    <div id="kt_app_content_container" class="app-container  container-fluid ">
        <x-employee-header :karyawan="$karyawan" :missing-fields="$missing_fields" :persentase-kelengkapan="$persentase_kelengkapan" />

        <div class="row g-10 g-xl-10">
            <div class="col-md-12">
                <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
                    <div class="card-header cursor-pointer">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Daftar Surat Izin Praktik</h3>
                        </div>

                        <a href="javascript:;" id="add-sip"
                            class="btn btn-sm btn-primary align-self-center">
                            Tambah SIP
                        </a>
                    </div>
        
                    <div class="card-body p-9">
                        <div id="kt_sip_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <table id="sip-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                                    <thead>
                                        <tr>
                                            <th class="">No.</th>
                                            <th class="">No. SIP</th>
                                            <th class="">Tgl. SIP</th>
                                            <th class="">Tgl. Kadaluarsa </th>
                                            <th class="">Keterangan</th>
                                            <th class="">File</th>
                                            <th class="">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>

    <!-- Modal tambah SIP -->
    <div class="modal fade" id="addSipModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-550px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_agenda_header">
                    <h2 class="fw-bold modal-title title-add-sip">Tambah SIP</h2>

                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form
                    action="{{ route('admin.karyawan.sip.store', $karyawan->kd_karyawan) }}"
                    method="POST"
                    class="form"
                    id="addSipForm"
                >
                    @csrf
                    <div class="modal-body">
                        <!-- nomor sip -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Nomor SIP
                            </label>
                            <input type="text" class="form-control form-control-solid" name="no_sip" id="no_sip" autofocus placeholder="Nomor SIP" />
                            <div class="fv-plugins-message-container invalid-feedback error-text no_sip_error"></div>
                        </div>

                        <!-- tanggal sip -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Tanggal SIP
                            </label>
                            <input type="date" class="form-control form-control-solid" name="tgl_sip" id="tgl_sip" autofocus placeholder="Tanggal SIP" />
                            <div class="fv-plugins-message-container invalid-feedback error-text tgl_sip_error"></div>
                        </div>

                        <!-- sediakan checkbox untuk menandai jika sip tidak memiliki masa berlaku -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="form-check form-check-custom form-check-solid"
                            >
                                <input type="checkbox" class="form-check-input" name="tidak_ada_masa_berlaku" id="tidak_ada_masa_berlaku" />
                                <span class="form-check-label text-gray-600">Tidak memiliki masa berlaku</span>
                            </label>
                            <div class="fv-plugins-message-container invalid-feedback error-text tidak_ada_masa_berlaku_error"></div>
                        </div>

                        <!-- tanggal kadaluarsa sip -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Tanggal Kadaluarsa SIP
                            </label>
                            <input type="date" class="form-control form-control-solid" name="tgl_kadaluarsa" id="tgl_kadaluarsa" autofocus placeholder="Tanggal Kadaluarsa SIP" />
                            <div class="fv-plugins-message-container invalid-feedback error-text tgl_kadaluarsa_error"></div>
                        </div>

                        <!-- keterangan sip -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Keterangan
                            </label>
                            <textarea class="form-control form-control-solid" name="ket" id="ket" autofocus placeholder="Keterangan"></textarea>
                            <div class="fv-plugins-message-container invalid-feedback error-text ket_error"></div>
                        </div>

                        <!-- file sip -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                File SIP
                            </label>
                            <input type="file" class="form-control form-control-solid" name="sc_berkas" id="sc_berkas" autofocus placeholder="File SIP" />
                            <div class="fv-plugins-message-container invalid-feedback error-text sc_berkas_error"></div>
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

    <!-- Modal edit sip -->
    <div class="modal fade" id="editSipModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-550px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_edit_agenda_header">
                    <h2 class="fw-bold modal-title title-edit-sip">Edit SIP</h2>

                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form
                    method="POST"
                    class="form"
                    id="editSipForm"
                >
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="kd_karyawan" id="kd_karyawan" />
                    <input type="hidden" name="urut" id="urut" />
                    <div class="modal-body">
                        <!-- nomor sip -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Nomor SIP
                            </label>
                            <input type="text" class="form-control form-control-solid" name="no_sip" id="e_no_sip" autofocus placeholder="Nomor SIP" />
                            <div class="fv-plugins-message-container invalid-feedback error-text e_no_sip_error"></div>
                        </div>

                        <!-- tanggal sip -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Tanggal SIP
                            </label>
                            <input type="date" class="form-control form-control-solid" name="tgl_sip" id="e_tgl_sip" autofocus placeholder="Tanggal SIP" />
                            <div class="fv-plugins-message-container invalid-feedback error-text e_tgl_sip_error"></div>
                        </div>

                        <!-- sediakan checkbox untuk menandai jika sip tidak memiliki masa berlaku -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="form-check form-check-custom form-check-solid"
                            >
                                <input type="checkbox" class="form-check-input" name="tidak_ada_masa_berlaku" id="e_tidak_ada_masa_berlaku" />
                                <span class="form-check-label text-gray-600">Tidak memiliki masa berlaku</span>
                            </label>
                            <div class="fv-plugins-message-container invalid-feedback error-text e_tidak_ada_masa_berlaku_error"></div>
                        </div>

                        <!-- tanggal kadaluarsa sip -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Tanggal Kadaluarsa SIP
                            </label>
                            <input type="date" class="form-control form-control-solid" name="tgl_kadaluarsa" id="e_tgl_kadaluarsa" autofocus placeholder="Tanggal Kadaluarsa SIP" />
                            <div class="fv-plugins-message-container invalid-feedback error-text e_tgl_kadaluarsa_error"></div>
                        </div>

                        <!-- keterangan SIP -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Keterangan
                            </label>
                            <textarea class="form-control form-control-solid" name="ket" id="e_ket" autofocus placeholder="Keterangan"></textarea>
                            <div class="fv-plugins-message-container invalid-feedback error-text e_ket_error"></div>
                        </div>

                        <!-- file SIP -->
                        <div class="d-flex flex-column mb-3">
                            <label
                                class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                File SIP
                            </label>
                            <input type="file" class="form-control form-control-solid" name="sc_berkas" id="e_sc_berkas" autofocus placeholder="File SIP" />
                            <div class="fv-plugins-message-container invalid-feedback error-text e_sc_berkas_error"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-btn" data-kt-menu-modal-action="submit">
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
</div>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            var table = $('#sip-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.karyawan.sip.get-sip-data', $karyawan->kd_karyawan) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'no_sip', name: 'no_sip' },
                    { data: 'tgl_sip', name: 'tgl_sip' },
                    { data: 'tgl_kadaluarsa', name: 'tgl_kadaluarsa' },
                    { data: 'ket', name: 'ket' },
                    { data: 'file', name: 'file' },
                    { data: 'action', name: 'action' },
                ]
            });

            table.on('draw', function() {
                KTMenu.createInstances();
            });

            // Cek pesan error dari session
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            // Ketika tombol tambah SIP di klik
            $('#add-sip').on('click', function() {
                $('#addSipModal').modal('show');
            });

            // ketika tidak_ada_masa_berlaku maka tanggal_kadaluarsa_sip di disable
            $('#tidak_ada_masa_berlaku').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#tgl_kadaluarsa').attr('disabled', true);
                } else {
                    $('#tgl_kadaluarsa').attr('disabled', false);
                }
            });

            // ketika e_tidak_ada_masa_berlaku maka e_tgl_kadaluarsa di disable
            $('#e_tidak_ada_masa_berlaku').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#e_tgl_kadaluarsa').attr('disabled', true);
                } else {
                    $('#e_tgl_kadaluarsa').attr('disabled', false);
                }
            });

            // Ketika form tambah SIP di submit
            $('#addSipForm').on('submit', function(e) {
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
                            $('#addSipModal').modal('hide');
                            form[0].reset();
                            // $('#sip-table').DataTable().ajax.reload();
                            table.ajax.reload();
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

            // Ketika tombol edit SIP di klik
            $('#sip-table').on('click', '.edit', function(e) {
                e.preventDefault();

                var kd_karyawan = $(this).data('karyawan');
                var urut = $(this).data('urut');

                console.log(kd_karyawan, urut);

                var url = "{{ route('admin.karyawan.sip.edit', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}";
                url = url.replace(':kd_karyawan', kd_karyawan);
                url = url.replace(':urut', urut);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#editSipModal').modal('show');
                        $('#kd_karyawan').val(response.data.kd_karyawan);
                        $('#urut').val(response.data.urut_sip);
                        $('#e_no_sip').val(response.data.no_sip);
                        $('#e_tgl_sip').val(response.data.tgl_sip);
                        $('#e_tgl_kadaluarsa').val(response.data.tgl_kadaluarsa);
                        $('#e_ket').val(response.data.ket);

                        // Tampilkan nama file
                        if (response.data.sc_berkas) {
                            $('#existing_file').remove(); // Hapus info file lama jika ada
                            
                            $('#e_sc_berkas').after('<div id="existing_file" class="mt-2">File saat ini: <a href="' + response.data.url_file + '" target="_blank">' + response.data.sc_berkas + '</a></div>');
                        }
                        
                        // Atur checkbox berdasarkan tgl_kadaluarsa
                        if (!response.data.tgl_kadaluarsa) {
                            $('#e_tidak_ada_masa_berlaku').prop('checked', true);
                            $('#e_tgl_kadaluarsa').attr('disabled', true);
                        } else {
                            $('#e_tidak_ada_masa_berlaku').prop('checked', false);
                            $('#e_tgl_kadaluarsa').attr('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data.');
                    }
                });
            });

            // Submit form edit via AJAX
            $('#editSipForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var kd_karyawan = $('#kd_karyawan').val();
                var urut = $('#urut').val();
                var url = "{{ route('admin.karyawan.sip.update', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}";
                url = url.replace(':kd_karyawan', kd_karyawan);
                url = url.replace(':urut', urut);

                var formData = new FormData(form[0]);
                formData.append('_method', 'PATCH'); // Tambahkan _method untuk PATCH

                var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
                $(form).find('.submit-btn').append(loadingIndicator);

                $.ajax({
                    url: url,
                    type: 'POST', // Gunakan POST karena Laravel membutuhkan ini untuk PATCH
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        loadingIndicator.show();
                        $('.error-text').text('');
                        $('.submit-btn').attr('disabled', true).find('.indicator-label').addClass('d-none');
                        $('.submit-btn').find('.spinner-border').removeClass('d-none');
                        $(form).find('.btn-primary').attr('disabled', true);
                        $(form).find('.btn-primary .indicator-label').hide();
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            toastr.success(response.message, 'Success');
                            $('#editSipModal').modal('hide');
                            form[0].reset();
                            table.ajax.reload(null, false);
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
                        $('.submit-btn').attr('disabled', false).find('.indicator-label').removeClass('d-none');
                        $('.submit-btn').find('.spinner-border').addClass('d-none');
                        $(form).find('.btn-primary').attr('disabled', false);
                        $(form).find('.btn-primary .indicator-label').show();
                    }
                });
            });
        });
    </script>
@endpush