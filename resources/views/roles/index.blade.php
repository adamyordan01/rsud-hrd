@extends('layouts.backend', ['title' => 'Role Management'])

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
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a
                        href="#"
                        class="btn btn-flex btn-primary h-40px fs-7 fw-bold"
                        data-bs-toggle="modal"
                        data-bs-target="#kt_modal_add_role"
                    >
                        Tambah Role
                    </a>
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
                            data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-12"
                            placeholder="Cari Role ...">
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">

                <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table id="role-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                            <thead>
                                <tr>
                                    <th class="">No.</th>
                                    <th class="">Nama Role</th>
                                    <th class="">Permission</th>
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


<!-- Modal: Add Role -->
<div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_role_header">
                <h2 class="fw-bold">Tambah Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body mb-7">
                <form action="{{ route('admin.user-management.roles.store') }}" method="POST" class="form" id="add_role" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex flex-column" id="kt_modal_add_role_scroll">
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Nama Role</label>
                            <input type="text" name="name" id="name" class="form-control form-control-solid" autofocus>
                            <div class="fv-plugins-message-container invalid-feedback error-text name_error"></div>
                        </div>
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-solid"></textarea>
                            <div class="fv-plugins-message-container invalid-feedback error-text description_error"></div>
                        </div>
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Level</label>
                            <input type="number" name="level" class="form-control form-control-solid">
                            <div class="fv-plugins-message-container invalid-feedback error-text level_error"></div>
                        </div>
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Role Permissions</label>
                            <select name="permissions[]" class="form-select form-select-solid" data-control="select2"
                                data-close-on-select="false" data-placeholder="Select Permissions" data-allow-clear="true" multiple="multiple">
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                            <div class="fv-plugins-message-container invalid-feedback error-text permissions_error"></div>
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

<!-- Modal: Edit Role -->
<div class="modal fade" id="kt_modal_edit_role" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_edit_role_header">
                <h2 class="fw-bold">Edit Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body mb-7">
                <form action="" method="POST" class="form" id="edit_role" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_role_id">
                    <div class="d-flex flex-column" id="kt_modal_edit_role_scroll">
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Nama Role</label>
                            <input type="text" name="name" id="e_name" class="form-control form-control-solid" autofocus>
                            <div class="fv-plugins-message-container invalid-feedback error-text e_name_error"></div>
                        </div>
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Deskripsi</label>
                            <textarea name="description" id="e_description" class="form-control form-control-solid"></textarea>
                            <div class="fv-plugins-message-container invalid-feedback error-text e_description_error"></div>
                        </div>
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Level</label>
                            <input type="number" name="level" id="e_level" class="form-control form-control-solid">
                            <div class="fv-plugins-message-container invalid-feedback error-text e_level_error"></div>
                        </div>
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Role Permissions</label>
                            <select name="permissions[]" id="e_permissions" class="form-select form-select-solid"
                                data-control="select2" data-close-on-select="false" data-placeholder="Select Permissions"
                                data-allow-clear="true" multiple="multiple">
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                            <div class="fv-plugins-message-container invalid-feedback error-text e_permissions_error"></div>
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

<!-- Modal: Detail Role -->
<div class="modal fade" id="kt_modal_detail_role" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_detail_role_header">
                <h2 class="fw-bold">Detail Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body mb-7">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                        <tr>
                            <th class="min-w-200px">Role</th>
                            <td class="text-end" id="detail_role"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Role Permissions</th>
                            <td class="text-end" id="detail_role_permissions"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Total Role Users</th>
                            <td class="text-end" id="detail_total_role_users"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Total Role Permissions</th>
                            <td class="text-end" id="detail_total_role_permissions"></td>
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
            var table = $('#role-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true, // Enable searching
                ajax: '{{ route('admin.user-management.roles.index') }}',
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
                    { data: 'name', name: 'name', searchable: true },
                    { data: 'permissions', name: 'permissions', orderable: false, searchable: false },
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

            // Instance table - removed duplicate KTMenu.createInstances()

            // Custom search functionality with debounce and visual feedback
            var searchTimeout;
            var $searchInput = $('input[data-kt-customer-table-filter="search"]');
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

            // Add Role
            $('#add_role').on('submit', function (e) {
                e.preventDefault();
                var form = this;
                var url = $(this).attr('action');
                var method = $(this).attr('method');
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
                        if (response.code == 201) {
                            $('#kt_modal_add_role').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
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

            // Edit Role
            $(document).on('click', '#edit-role', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('admin.user-management.roles.edit', ':id') }}'.replace(':id', id),
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.code == 200) {
                            var role = response.data;
                            $('#edit_role_id').val(role.id);
                            $('#e_name').val(role.name);
                            $('#e_description').val(role.description);
                            $('#e_level').val(role.level);

                            // Set permissions
                            var permissions = role.permissions.map(permission => permission.id);
                            $('#e_permissions').val(permissions).trigger('change');

                            $('#kt_modal_edit_role').modal('show');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data role.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            $('#edit_role').on('submit', function (e) {
                e.preventDefault();
                var form = this;
                var id = $('#edit_role_id').val();
                var url = '{{ route('admin.user-management.roles.update', ':id') }}'.replace(':id', id);
                
                // Gunakan FormData untuk mengambil semua input dalam form
                var formData = new FormData(form[0]);
                
                // Tambahkan method PUT karena FormData tidak mendukung PUT secara langsung
                formData.append('_method', 'PUT');
                
                var loadingIndicator = $('.indicator-progress');
                

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
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
                            $('#kt_modal_edit_role').modal('hide');
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
                                $('#e_' + key).closest('.fv-row').append('<div class="fv-plugins-message-container invalid-feedback error-text e_' + key + '_error">' + value + '</div>');
                            });
                        }
                    }
                });
            });

            // Detail Role
            $(document).on('click', '#detail-role', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('admin.user-management.roles.show', ':id') }}'.replace(':id', id),
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.code == 200) {
                            var role = response.data;
                            $('#detail_role').text(role.name);
                            $('#detail_total_role_users').text(role.users_count);
                            $('#detail_total_role_permissions').text(role.permissions_count);

                            var permissions = role.permissions;
                            if (permissions.length > 0) {
                                var permissionBadges = permissions.map(permission => '<span class="badge badge-light">' + permission.name + '</span>').join(' ');
                                $('#detail_role_permissions').html(permissionBadges);
                            } else {
                                $('#detail_role_permissions').html('<span class="badge badge-light">-</span>');
                            }

                            $('#kt_modal_detail_role').modal('show');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil detail role.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            // Reset modal on close
            $('#kt_modal_add_role, #kt_modal_edit_role').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $('.error-text').remove();
                $('#e_permissions').val(null).trigger('change');
            });

            // $('#kt_modal_add_role, #kt_modal_edit_role').on('hidden.bs.modal', function () {
            //     $(this).find('form')[0].reset();
            //     $('.error-text').remove();
            //     $('#e_permissions').val(null).trigger('change');
            // });
        });


        // $(document).on('click', function(e) {
        //     if ($(e.target).hasClass('menu-link')) {
        //         e.preventDefault();

        //         var id = $(e.target).data('id');
        //         // Route::get('/user-management/roles/edit/{id}', [RoleController::class, 'edit'])->name('edit');
        //         var url = "{{ route('admin.user-management.roles.edit', ':id') }}";
        //         url = url.replace(':id', id);

        //         $.ajax({
        //             url: url,
        //             method: 'GET',
        //             dataType: 'json',
        //             success: function(response) {
        //                 if (response.code == 200) {
        //                     $('#kt_modal_edit_role').modal('show');
        //                     $('#kt_modal_edit_role').find('input[name="id"]').val(response.data.role.id);
        //                     $('#kt_modal_edit_role').find('#e_name').val(response.data.role.name);

        //                     // kosongkan element select
        //                     var $permissionsSelect = $('#kt_modal_edit_role').find('#e_permissions');
        //                     $permissionsSelect.val(null).trigger('change');

        //                     // jika role memiliki permission, tambahkan permission ke dalam select
        //                     if (response.data.role.permissions.length > 0) {
        //                         var permissions = response.data.role.permissions.map(function(permission) {
        //                             return permission.id;
        //                         });

        //                         $permissionsSelect.val(permissions).trigger('change');
        //                     }
        //                 }
        //             }
        //         })
        //     }
        // })
        // ketika edit role di klik
        $(document).on('click', '#edit-role', function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            var url = "{{ route('admin.user-management.roles.edit', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.code == 200) {
                        $('#kt_modal_edit_role').modal('show');
                        $('#kt_modal_edit_role').find('input[name="id"]').val(response.data.role.id);
                        $('#kt_modal_edit_role').find('#e_name').val(response.data.role.name);

                        // kosongkan element select
                        var $permissionsSelect = $('#kt_modal_edit_role').find('#e_permissions');
                        $permissionsSelect.val(null).trigger('change');

                        // jika role memiliki permission, tambahkan permission ke dalam select
                        if (response.data.role.permissions.length > 0) {
                            var permissions = response.data.role.permissions.map(function(permission) {
                                return permission.id;
                            });

                            $permissionsSelect.val(permissions).trigger('change');
                        }
                    }
                }
            })
        })

        // ketika edit role form di submit
        $('#edit_role').on('submit', function(e) {
            e.preventDefault();
            
            var form = this;
            var url = "{{ route('admin.user-management.roles.update', ':id') }}";
            url = url.replace(':id', $(form).find('input[name="id"]').val());

            var method = $(this).attr('method');

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
                beforeSend: function() {
                    loadingIndicator.show();

                    $(form).find('.btn-primary').attr('disabled', true);
                    $(form).find('btn-primary .indicator-label').hide();
                },
                complete: function() {
                    loadingIndicator.hide();

                    $(form).find('.btn-primary').attr('disabled', false);
                    $(form).find('btn-primary .indicator-label').show();
                },
                success: function(response) {
                    if (response.code == 200) {
                        $('#kt_modal_edit_role').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // settimeout to reload entire afater 0.5s
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(xhr) {
                    let response = xhr.responseJSON;
                    console.log(response.errors);
                    if ($.isEmptyObject(response) == false) {
                        $('.error-text').remove();

                        $.each(response.errors, function(key, value) {
                            $('#' + key).closest('.fv-row').append('<div class="fv-plugins-message-container invalid-feedback error-text '+ key +'_error">' + value + '</div>');
                        })
                    }
                }
            })
        })

        // ketika detail role di klik, maka ambil data dan tampilkam di modal
        $(document).on('click', '#detail-role', function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            var url = "{{ route('admin.user-management.roles.show', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.code == 200) {
                        $('#kt_modal_detail_role').modal('show');
                        $('#kt_modal_detail_role').find('#detail_role').text(response.data.role.name);
                        
                        // jika terdapat permission, tampilkan permission dalam bentuk badge, jika tidak tampilkan -
                        if (response.data.role.permissions.length > 0) {
                            var permissions = response.data.role.permissions.map(function(permission) {
                                return '<span class="badge badge-light">' + permission.name + '</span>';
                            });

                            $('#kt_modal_detail_role').find('#detail_role_permissions').html(permissions);
                        } else {
                            $('#kt_modal_detail_role').find('#detail_role_permissions').html('<span class="badge badge-light">-</span>');
                        }

                        $('#kt_modal_detail_role').find('#detail_total_role_users').text(response.data.total_user_on_role);
                        $('#kt_modal_detail_role').find('#detail_total_role_permissions').text(response.data.total_permissions);
                    }
                }
            })
        })



        // jika tombol close dan discard di klik maka form akan direset
        $('#kt_modal_add_role').on('hide.bs.modal', function() {
            $('#add_role').trigger('reset');
        })

        $('#kt_modal_edit_role').on('hide.bs.modal', function() {
            $('#edit_role').trigger('reset');
        })

        // data-kt-menu-modal-action="close" akan menutup modal dan mereset form
        $(document).on('click', '[data-kt-menu-modal-action="close"]', function(e) {
            e.preventDefault();

            var modal = $(this).closest('.modal');
            modal.modal('hide');
        })

        // data-kt-menu-modal-action="cancel" akan menutup modal dan mereset form
        $(document).on('click', '[data-kt-menu-modal-action="cancel"]', function(e) {
            e.preventDefault();

            var modal = $(this).closest('.modal');
            modal.modal('hide');
        })
    </script>
@endpush