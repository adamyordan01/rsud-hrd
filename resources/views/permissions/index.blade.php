@extends('layouts.backend', ['title' => 'Permission Management'])

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
                        Permission
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
                            Permission
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a
                        href="#"
                        class="btn btn-flex btn-primary h-40px fs-7 fw-bold"
                        data-bs-toggle="modal"
                        data-bs-target="#kt_modal_add_permission"
                    >
                        Tambah Permission
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
                            placeholder="Cari Permission ...">
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">

                <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table id="permission-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                            <thead>
                                <tr>
                                    <th class="">No.</th>
                                    <th class="">Nama Permission</th>
                                    <th class="">Slug</th>
                                    <th class="">Deskripsi</th>
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

<!-- Modal: Add Permission -->
<div class="modal fade" id="kt_modal_add_permission" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Permission</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body mb-7">
                <form action="{{ route('admin.user-management.permissions.store') }}" method="POST" class="form" id="add_permission" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex flex-column" id="kt_modal_add_permission_scroll">
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Nama Permission</label>
                            <input type="text" name="name" id="p_name" class="form-control form-control-solid" autofocus>
                            <div class="fv-plugins-message-container invalid-feedback error-text p_name_error"></div>
                        </div>
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Deskripsi</label>
                            <textarea name="description" id="p_description" class="form-control form-control-solid"></textarea>
                            <div class="fv-plugins-message-container invalid-feedback error-text p_description_error"></div>
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

<!-- Modal: Edit Permission -->
<div class="modal fade" id="kt_modal_edit_permission" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Edit Permission</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body mb-7">
                <form action="" method="POST" class="form" id="edit_permission" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_permission_id">
                    <div class="d-flex flex-column" id="kt_modal_edit_permission_scroll">
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Nama Permission</label>
                            <input type="text" name="name" id="e_p_name" class="form-control form-control-solid" autofocus>
                            <div class="fv-plugins-message-container invalid-feedback error-text e_p_name_error"></div>
                        </div>
                        <div class="d-flex flex-column fv-row mb-8">
                            <label class="fs-6 fw-semibold mb-2">Deskripsi</label>
                            <textarea name="description" id="e_p_description" class="form-control form-control-solid"></textarea>
                            <div class="fv-plugins-message-container invalid-feedback error-text e_p_description_error"></div>
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

<!-- Modal: Detail Permission -->
<div class="modal fade" id="kt_modal_detail_permission" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Detail Permission</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body mb-7">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                        <tr>
                            <th class="min-w-200px">Permission</th>
                            <td class="text-end" id="detail_permission"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Slug</th>
                            <td class="text-end" id="detail_permission_slug"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Deskripsi</th>
                            <td class="text-end" id="detail_permission_description"></td>
                        </tr>
                        <tr>
                            <th class="min-w-200px">Roles</th>
                            <td class="text-end" id="detail_permission_roles"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            // Initialize Datatables
            var table = $('#permission-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true, // Enable searching
                ajax: '{{ route('admin.user-management.permissions.index') }}',
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
                    { data: 'slug', name: 'slug', searchable: true },
                    { data: 'description', name: 'description', searchable: true },
                    { data: 'roles', name: 'roles', orderable: false, searchable: true },
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

            // Add Permission
            $('#add_permission').on('submit', function (e) {
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
                            $('#kt_modal_add_permission').modal('hide');
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
                                $('#p_' + key).closest('.fv-row').append('<div class="fv-plugins-message-container invalid-feedback error-text p_' + key + '_error">' + value + '</div>');
                            });
                        }
                    }
                });
            });

            // Edit Permission
            $(document).on('click', '#edit-permission', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('admin.user-management.permissions.edit', ':id') }}'.replace(':id', id), // Gunakan route yang benar
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.code == 200) {
                            var permission = response.data;
                            $('#edit_permission_id').val(permission.id);
                            $('#e_p_name').val(permission.name);
                            $('#e_p_description').val(permission.description);
                            $('#kt_modal_edit_permission').modal('show');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data permission.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            $('#edit_permission').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var id = $('#edit_permission_id').val();
                var url = '{{ route('admin.user-management.permissions.update', ':id') }}'.replace(':id', id);
                
                // Gunakan FormData untuk mengambil semua input dalam form
                var formData = new FormData(form[0]);
                
                // Tambahkan method PUT karena FormData tidak mendukung PUT secara langsung
                formData.append('_method', 'PUT');
                
                var loadingIndicator = $('.indicator-progress');
                
                $.ajax({
                    url: url,
                    type: 'POST', // Gunakan POST ketika menggunakan FormData dengan _method
                    data: formData,
                    contentType: false, // Jangan set contentType ketika menggunakan FormData
                    processData: false, // Jangan proses data ketika menggunakan FormData
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
                            $('#kt_modal_edit_permission').modal('hide');
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
                                $('#e_p_' + key).closest('.fv-row').append('<div class="fv-plugins-message-container invalid-feedback error-text e_p_' + key + '_error">' + value + '</div>');
                            });
                        }
                    }
                });
            });

            // Delete Permission
            $(document).on('click', '#delete-permission', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Permission ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.user-management.permissions.index') }}' + '/' + id,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function (response) {
                                if (response.code == 200) {
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
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal menghapus permission.',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        });
                    }
                });
            });

            // Detail Permission
            $(document).on('click', '#detail-permission', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('admin.user-management.permissions.show', ':id') }}'.replace(':id', id),
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.code == 200) {
                            var permission = response.data;
                            $('#detail_permission').text(permission.name);
                            $('#detail_permission_slug').text(permission.slug);
                            $('#detail_permission_description').text(permission.description ?? '-');
                            $('#detail_permission_roles').text(permission.roles.length > 0 ? permission.roles.map(role => role.name).join(', ') : '-');
                            $('#kt_modal_detail_permission').modal('show');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil detail permission.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            $('#kt_modal_add_permission, #kt_modal_edit_permission').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $('.error-text').remove();
            });
        });
    </script>
@endpush