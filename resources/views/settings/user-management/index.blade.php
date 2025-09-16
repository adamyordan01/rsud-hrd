@extends('layouts.backend', ['title' => $pageTitle])

@push('styles')
<style>
    .user-info {
        display: flex;
        align-items: center;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f1f3f4;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-weight: bold;
        color: #5e6278;
    }
    .user-details h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }
    .user-details small {
        color: #7e8299;
        font-size: 12px;
    }
</style>
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        User Management
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Settings</li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">User Management</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="javascript:;" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" id="add-user">
                        <i class="ki-outline ki-plus fs-2"></i>
                        Tambah User
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                        <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari User ...">
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <!-- Filter by Access Level -->
                        <select class="form-select form-select-solid w-150px me-3" id="filter-access">
                            <option value="all">Semua Akses</option>
                            @foreach($accessLevels as $access)
                                <option value="{{ $access->kd_akses }}" {{ $access->kd_akses == 1 ? 'selected' : '' }}>{{ $access->akses }}</option>
                            @endforeach
                        </select>
                        
                        <button type="button" class="btn btn-light-primary me-3" id="refresh-table">
                            <i class="ki-outline ki-arrows-circle fs-2"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table id="user-management-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-50px">No</th>
                                    <th class="min-w-200px">Identitas Admin</th>
                                    <th class="min-w-125px">Level Akses</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="text-end min-w-100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tambah/Edit User -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah User</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="userForm">
                    @csrf
                    
                    <div class="modal-body">
                        <!-- Level Akses -->
                        <div class="d-flex flex-column mb-5">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Level Akses</span>
                            </label>
                            <select class="form-select form-select-solid" name="kd_akses" id="kd_akses" data-control="select2" data-placeholder="Pilih Level Akses">
                                <option></option>
                                @foreach($accessLevels as $access)
                                    <option value="{{ $access->kd_akses }}">{{ $access->akses }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-2 error-text" id="error-kd_akses"></div>
                        </div>

                        <!-- Pilih Karyawan -->
                        <div class="d-flex flex-column mb-5" id="employee-select-group">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Pilih Karyawan</span>
                            </label>
                            <select class="form-select form-select-solid" name="kd_karyawan" id="kd_karyawan" data-control="select2" data-placeholder="Pilih Level Akses Terlebih Dahulu" disabled>
                                <option></option>
                            </select>
                            <div class="text-danger mt-2 error-text" id="error-kd_karyawan"></div>
                        </div>

                        <!-- Info Karyawan untuk Edit -->
                        <div class="d-flex flex-column mb-5 d-none" id="employee-info-group">
                            <label class="fs-6 fw-semibold mb-2">Karyawan</label>
                            <div class="card card-flush border">
                                <div class="card-body p-4">
                                    <div class="user-info">
                                        <div class="user-avatar" id="employee-avatar"></div>
                                        <div class="user-details">
                                            <h6 id="employee-name"></h6>
                                            <small id="employee-nip"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Level Akses untuk Edit -->
                        <div class="d-flex flex-column mb-5 d-none" id="access-edit-group">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Level Akses Baru</span>
                            </label>
                            <select class="form-select form-select-solid" name="kd_akses_edit" id="kd_akses_edit" data-control="select2" data-placeholder="Pilih Level Akses">
                                <option></option>
                                @foreach($accessLevels as $access)
                                    <option value="{{ $access->kd_akses }}">{{ $access->akses }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger mt-2 error-text" id="error-kd_akses_edit"></div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary submit-btn">
                            <span class="indicator-label">Save</span>
                            <span class="indicator-progress d-none">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
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
            var table = $('#user-management-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.settings.user-management.index") }}',
                    type: 'GET',
                    data: function (d) {
                        d.filter_akses = $('#filter-access').val();
                    }
                },
                columns: [
                    { 
                        data: 'DT_RowIndex', 
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    { 
                        data: 'employee_info', 
                        name: 'employee_info',
                        orderable: true,
                        searchable: true
                    },
                    { 
                        data: 'access_level', 
                        name: 'wap.akses',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                order: [[1, 'asc']], // Sort by employee info
                createdRow: function(row, data, dataIndex) {
                    $(row).attr('data-id', data.kd_karyawan);
                },
                initComplete: function() {
                    $('[data-kt-user-table-filter="search"]').on('keyup', function() {
                        table.search(this.value).draw();
                    });
                }
            });

            // Filter by access level
            $('#filter-access').on('change', function() {
                table.ajax.reload();
            });

            // Refresh table
            $('#refresh-table').on('click', function() {
                table.ajax.reload(null, false);
            });

            table.on('draw', function() {
                KTMenu.createInstances();
            });

            // Initialize Select2
            $('#kd_karyawan, #kd_akses, #kd_akses_edit').select2({
                dropdownParent: $('#userModal')
            });

            // On access level change, load available employees
            $('#kd_akses').on('change', function() {
                var kdAkses = $(this).val();
                if (kdAkses) {
                    loadEmployeesByAccess(kdAkses);
                    $('#kd_karyawan').prop('disabled', false).attr('data-placeholder', 'Pilih Karyawan');
                } else {
                    $('#kd_karyawan').empty().append('<option></option>').prop('disabled', true).attr('data-placeholder', 'Pilih Level Akses Terlebih Dahulu');
                }
                $('#kd_karyawan').trigger('change');
            });

            function loadEmployeesByAccess(kdAkses) {
                $.ajax({
                    url: '{{ route("admin.settings.user-management.employees-by-access") }}',
                    type: 'GET',
                    data: { kd_akses: kdAkses },
                    success: function(response) {
                        if (response.success) {
                            var select = $('#kd_karyawan');
                            select.empty().append('<option></option>');
                            
                            $.each(response.data, function(index, employee) {
                                select.append('<option value="' + employee.kd_karyawan + '">' + 
                                    employee.display_text + '</option>');
                            });
                            
                            select.trigger('change');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading employees:', xhr);
                    }
                });
            }

            // Open modal for adding new user
            $('#add-user').on('click', function() {
                $('#modalTitle').text('Tambah User');
                $('#userForm')[0].reset();
                $('.error-text').text('');
                
                // Reset access level dropdown and trigger change to load employees
                $('#kd_akses').val('1').trigger('change'); // Default to HRD
                
                $('#userModal').modal('show');
            });

            // Submit form (add only, edit removed)
            $('#userForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                
                var formData = new FormData(form[0]);

                // Show loading
                form.find('.submit-btn .indicator-label').addClass('d-none');
                form.find('.submit-btn .indicator-progress').removeClass('d-none');
                form.find('.submit-btn').prop('disabled', true);

                $.ajax({
                    url: '{{ route("admin.settings.user-management.store") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            $('#userModal').modal('hide');
                            table.ajax.reload(null, false);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.errors || {};
                        
                        // Clear previous errors
                        $('.error-text').text('');
                        
                        // Display field errors
                        $.each(errors, function(field, messages) {
                            $('#error-' + field).text(messages[0]);
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.',
                        });
                    },
                    complete: function() {
                        // Hide loading
                        form.find('.submit-btn .indicator-label').removeClass('d-none');
                        form.find('.submit-btn .indicator-progress').addClass('d-none');
                        form.find('.submit-btn').prop('disabled', false);
                    }
                });
            });

            // Delete user
            $('#user-management-table').on('click', '.delete', function() {
                var kdKaryawan = $(this).data('id');
                var kdAkses = $(this).data('akses');
                var namaKaryawan = $(this).data('nama');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'User "' + namaKaryawan + '" akan dihapus dari sistem!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("admin.settings.user-management.destroy", ":id") }}'.replace(':id', kdKaryawan),
                            type: 'DELETE',
                            data: {
                                kd_akses: kdAkses
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload(null, false);
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.',
                                });
                            }
                        });
                    }
                });
            });

            // Function to refresh available employees (not needed anymore)
            // Removed refreshAvailableEmployees function as we now load employees by access level

            // Reset form on modal close
            $('#userModal').on('hidden.bs.modal', function() {
                $('#userForm')[0].reset();
                $('#kd_karyawan, #kd_akses').val(null).trigger('change');
                $('.error-text').text('');
            });
        });
    </script>
@endpush
