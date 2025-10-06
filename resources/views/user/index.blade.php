@extends('layouts.backend', ['title' => 'User Management'])

@push('styles')
    <style>

    </style>    
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
                            Seluruh Karyawan 
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
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i> <input type="text"
                            data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-12"
                            placeholder="Cari Karyawan ...">
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">

                <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table id="user-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                            <thead>
                                <tr>
                                    <th class="">No.</th>
                                    <th class="">ID. Pegawai</th>
                                    <th class="">Nama</th>
                                    <th class="">Email</th>
                                    <th class="">Roles</th>
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


<!-- Modal: Assign Role -->
<div class="modal fade" id="kt_modal_assign_role" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Assign Role ke User</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body mb-7">
                <form action="" method="POST" class="form" id="assign_role_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="assign_role_user_id">
                    <div class="d-flex flex-column" id="kt_modal_assign_role_scroll">
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Pilih Role</label>
                            <select name="roles[]" id="assign_roles" class="form-select form-select-solid" multiple data-control="select2" data-close-on-select="false" data-placeholder="Pilih Role">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <div class="fv-plugins-message-container invalid-feedback error-text roles_error"></div>
                        </div>
                        <div class="text-end pt-10">
                            <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-action="cancel">Discard</button>
                            <button type="submit" class="btn btn-primary" data-kt-menu-modal-action="submit">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">
                                    Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Detail User -->
<div class="modal fade" id="kt_modal_detail_user" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Detail User</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body mb-7">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                        <tr>
                            <th class="min-w-200px">ID Pegawai</th>
                            <td class="text-end" id="detail_kd_karyawan"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Nama</th>
                            <td class="text-end" id="detail_name"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Email</th>
                            <td class="text-end" id="detail_email"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Roles</th>
                            <td class="text-end" id="detail_roles"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // Initialize Datatables
            var table = $('#user-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true, // Enable searching
                ajax: '{{ route('admin.user-management.users.index') }}',
                columns: [
                    { 
                        data: null, 
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        name: 'id',
                        searchable: false,
                        orderable: false
                    },
                    { data: 'kd_karyawan', name: 'kd_karyawan', orderable: false, searchable: true },
                    { data: 'name', name: 'name', searchable: true },
                    { data: 'email', name: 'email', searchable: true },
                    { data: 'roles', name: 'roles', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: 'rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>', // Hide default search box
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Custom search functionality with debounce and visual feedback
            var searchTimeout;
            var $searchInput = $('[data-kt-user-table-filter="search"]');
            var $searchIcon = $searchInput.prev('i');
            
            $searchInput.on('keyup', function() {
                var value = this.value;
                
                // Clear existing timeout
                clearTimeout(searchTimeout);
                
                // Show loading state
                $searchIcon.removeClass('ki-magnifier').addClass('spinner-border spinner-border-sm');
                
                // Set new timeout - wait 500ms after user stops typing
                searchTimeout = setTimeout(function() {
                    table.search(value).draw();
                }, 500);
            });

            // Reset icon when search is complete
            table.on('draw', function() {
                $searchIcon.removeClass('spinner-border spinner-border-sm').addClass('ki-magnifier');
                KTMenu.createInstances();
            });

            // Assign Role
            $(document).on('click', '#assign-role', function () {
                var id = $(this).data('id');
                $('#assign_role_user_id').val(id);

                // Ambil data role user melalui AJAX
                $.ajax({
                    url: '{{ route('admin.user-management.users.show', ':id') }}'.replace(':id', id),
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.code == 200) {
                            var user = response.data;
                            var roles = user.roles || []; // Array nama role (misalnya ['pegawai_biasa', 'admin'])

                            // Set nilai default pada select
                            $('#assign_roles').val(roles).trigger('change'); // Update Select2
                            $('#kt_modal_assign_role').modal('show');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data role user.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            $('#assign_role_form').on('submit', function (e) {
                e.preventDefault();
                var form = this;
                var id = $('#assign_role_user_id').val();
                var url = '{{ route('admin.user-management.users.assign-role', ':id') }}'.replace(':id', id);
                var method = 'POST';
                var formData = new FormData(form);
                var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
                $(form).find('.btn-primary').append(loadingIndicator);

                $.ajax({
                    url: url,
                    method: method,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    data: formData,
                    beforeSend: function () {
                        loadingIndicator.show();
                        $(form).find('.btn-primary').attr('disabled', true);
                        $(form).find('.btn-primary .indicator-label').hide();
                    },
                    complete: function () {
                        loadingIndicator.hide();
                        $(form).find('.btn-primary').attr('disabled', false);
                        $(form).find('.btn-primary .indicator-label').show();
                    },
                    success: function (response) {
                        if (response.code == 200) {
                            $('#kt_modal_assign_role').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload();
                        }
                    },
                    error: function (xhr) {
                        let response = xhr.responseJSON;
                        if ($.isEmptyObject(response) == false) {
                            $('.error-text').remove();
                            $.each(response.errors, function (key, value) {
                                $('#' + key).closest('.fv-row').append('<div class="fv-plugins-message-container invalid-feedback error-text ' + key + '_error">' + value + '</div>');
                            });
                        }
                    }
                });
            });

            // Detail User
            $(document).on('click', '#detail-user', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('admin.user-management.users.show', ':id') }}'.replace(':id', id),
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.code == 200) {
                            var user = response.data;
                            
                            $('#detail_kd_karyawan').text(user.kd_karyawan ?? '-');
                            $('#detail_name').text(user.karyawan?.NAMA ?? user.name);
                            $('#detail_email').text(user.email);
                            // Pastikan user.roles adalah array string, lalu gabungkan dengan koma
                            $('#detail_roles').text(user.roles.length > 0 ? user.roles.join(', ') : '-');
                            $('#kt_modal_detail_user').modal('show');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil detail user.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            $('#kt_modal_assign_role').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $('.error-text').remove();
                $('#assign_roles').val(null).trigger('change');
            });
        });
    </script>
@endpush