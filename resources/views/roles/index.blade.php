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
                        href="{{ route('admin.user-management.roles.create') }}"
                        class="btn btn-flex btn-primary h-40px fs-7 fw-bold"
                    >
                        <i class="ki-duotone ki-plus fs-6 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
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

<!-- Modal: Edit Role -->
<div class="modal fade" id="kt_modal_edit_role" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_edit_role_header">
                <h2 class="fw-bold">Edit Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
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
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_detail_role_header">
                <h2 class="fw-bold">
                    <i class="ki-outline ki-shield-tick fs-2 text-primary me-2"></i>
                    Detail Role
                </h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body p-7">
                <!-- Role Information Header -->
                <div class="card card-flush mb-7">
                    <div class="card-header pt-6 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label bg-light-primary">
                                    <i class="ki-outline ki-user-tick fs-2x text-primary" id="detail_role_icon"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="text-gray-900 fw-bold mb-1" id="detail_role_name">Role Name</h3>
                                <span class="badge badge-light-primary fs-7 fw-bold" id="detail_role_level">Level: -</span>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-sm btn-light-primary" id="quick_edit_role">
                                    <i class="ki-outline ki-pencil fs-6 me-1"></i>
                                    Quick Edit
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex flex-column mb-5">
                                    <label class="fs-6 fw-semibold text-muted mb-2">Deskripsi Role</label>
                                    <p class="fs-6 text-gray-800 mb-0" id="detail_role_description">-</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex flex-column mb-5">
                                    <label class="fs-6 fw-semibold text-muted mb-2">Total Users</label>
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-people fs-3 text-primary me-2"></i>
                                        <span class="fs-3 fw-bold text-gray-800" id="detail_total_role_users">0</span>
                                        <span class="fs-7 text-muted ms-1">pengguna</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex flex-column mb-5">
                                    <label class="fs-6 fw-semibold text-muted mb-2">Total Permissions</label>
                                    <div class="d-flex align-items-center">
                                        <i class="ki-outline ki-key fs-3 text-success me-2"></i>
                                        <span class="fs-3 fw-bold text-gray-800" id="detail_total_role_permissions">0</span>
                                        <span class="fs-7 text-muted ms-1">hak akses</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="card card-flush">
                    <div class="card-header pt-6">
                        <h4 class="card-title fw-bold">
                            <i class="ki-outline ki-key fs-2 text-success me-2"></i>
                            Hak Akses (Permissions)
                        </h4>
                        <div class="card-toolbar">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light-success fs-7 fw-bold me-2" id="permission_progress_text">0% Complete</span>
                                <div class="progress h-6px w-100px">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="permission_progress_bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-6">
                        <div id="detail_role_permissions_container">
                            <!-- Permissions akan ditampilkan disini dengan grouping -->
                            <div class="text-center py-10">
                                <i class="ki-outline ki-information-2 fs-3x text-muted"></i>
                                <p class="text-muted fs-6 mt-3">Tidak ada permission yang ditemukan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mt-7">
                    <div class="col-md-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="ki-outline ki-chart-simple fs-3x text-info me-4"></i>
                                    <div>
                                        <h4 class="fw-bold text-gray-800 mb-1" id="detail_dashboard_perms">0</h4>
                                        <span class="text-muted fs-7">Dashboard Permissions</span>
                                    </div>
                                </div>
                                <div class="progress h-6px">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 0%" id="dashboard_progress"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="ki-outline ki-people fs-3x text-warning me-4"></i>
                                    <div>
                                        <h4 class="fw-bold text-gray-800 mb-1" id="detail_karyawan_perms">0</h4>
                                        <span class="text-muted fs-7">Karyawan Permissions</span>
                                    </div>
                                </div>
                                <div class="progress h-6px">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" id="karyawan_progress"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="ki-outline ki-setting-2 fs-3x text-danger me-4"></i>
                                    <div>
                                        <h4 class="fw-bold text-gray-800 mb-1" id="detail_system_perms">0</h4>
                                        <span class="text-muted fs-7">System Permissions</span>
                                    </div>
                                </div>
                                <div class="progress h-6px">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" id="system_progress"></div>
                                </div>
                            </div>
                        </div>
                    </div>
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

            // Edit Role
            $(document).on('click', '#edit-role', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('admin.user-management.roles.edit', ':id') }}'.replace(':id', id),
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.code == 200) {
                            var role = response.data.role;
                            $('#edit_role_id').val(role.id);
                            
                            // Remove hrd_ prefix for display in form
                            var displayName = role.name.replace(/^hrd_/, '');
                            $('#e_name').val(displayName);
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
                var formData = new FormData(form);
                
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
                            
                            // Role basic info
                            var displayName = role.name.replace(/^hrd_/, '');
                            $('#detail_role_name').text(displayName);
                            $('#detail_role_description').text(role.description || 'Tidak ada deskripsi');
                            $('#detail_role_level').text('Level: ' + (role.level || 'Tidak ditentukan'));
                            $('#detail_total_role_users').text(role.users_count || 0);
                            $('#detail_total_role_permissions').text(role.permissions_count || 0);

                            // Set role icon berdasarkan nama role
                            var iconClass = getRoleIcon(displayName);
                            $('#detail_role_icon').removeClass().addClass(iconClass + ' fs-2x text-primary');

                            // Process permissions
                            var permissions = role.permissions || [];
                            var totalPermissions = permissions.length;
                            var availablePermissions = {{ $permissions->count() }}; // Total available permissions

                            // Calculate progress
                            var progressPercentage = availablePermissions > 0 ? Math.round((totalPermissions / availablePermissions) * 100) : 0;
                            $('#permission_progress_text').text(progressPercentage + '% Complete');
                            $('#permission_progress_bar').css('width', progressPercentage + '%');

                            // Group permissions by category
                            var groupedPermissions = groupPermissionsByCategory(permissions);
                            
                            // Display grouped permissions
                            displayGroupedPermissions(groupedPermissions);
                            
                            // Update statistics cards
                            updatePermissionStats(groupedPermissions);

                            // Quick edit button
                            $('#quick_edit_role').data('id', role.id);

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

            // Quick edit function
            $(document).on('click', '#quick_edit_role', function() {
                var roleId = $(this).data('id');
                $('#kt_modal_detail_role').modal('hide');
                // Trigger edit modal
                setTimeout(function() {
                    $('#edit-role[data-id="' + roleId + '"]').trigger('click');
                }, 300);
            });

            // Function to get role icon
            function getRoleIcon(roleName) {
                var icons = {
                    'pegawai_biasa': 'ki-outline ki-user',
                    'struktural': 'ki-outline ki-users-cog', 
                    'it_member': 'ki-outline ki-laptop-code',
                    'it_head': 'ki-outline ki-crown',
                    'kepegawaian': 'ki-outline ki-clipboard-list',
                    'superadmin': 'ki-outline ki-shield-alt',
                    'pegawai_viewer': 'ki-outline ki-eye'
                };
                return icons[roleName] || 'ki-outline ki-user-tag';
            }

            // Function to group permissions by category
            function groupPermissionsByCategory(permissions) {
                var groups = {
                    dashboard: [],
                    karyawan: [],
                    laporan: [],
                    export: [],
                    settings: [],
                    sk: [],
                    mutasi: [],
                    user_management: [],
                    other: []
                };

                permissions.forEach(function(permission) {
                    var name = permission.name.toLowerCase();
                    
                    if (name.includes('dashboard')) {
                        groups.dashboard.push(permission);
                    } else if (name.includes('karyawan')) {
                        groups.karyawan.push(permission);
                    } else if (name.includes('laporan')) {
                        groups.laporan.push(permission);
                    } else if (name.includes('export')) {
                        groups.export.push(permission);
                    } else if (name.includes('settings')) {
                        groups.settings.push(permission);
                    } else if (name.includes('sk')) {
                        groups.sk.push(permission);
                    } else if (name.includes('mutasi')) {
                        groups.mutasi.push(permission);
                    } else if (name.includes('user_management')) {
                        groups.user_management.push(permission);
                    } else {
                        groups.other.push(permission);
                    }
                });

                return groups;
            }

            // Function to display grouped permissions
            function displayGroupedPermissions(groups) {
                var container = $('#detail_role_permissions_container');
                var html = '';

                var categoryConfig = {
                    dashboard: { title: 'Dashboard', icon: 'ki-outline ki-chart-simple', color: 'info' },
                    karyawan: { title: 'Manajemen Karyawan', icon: 'ki-outline ki-people', color: 'primary' },
                    laporan: { title: 'Laporan', icon: 'ki-outline ki-document', color: 'success' },
                    export: { title: 'Export Data', icon: 'ki-outline ki-down', color: 'warning' },
                    settings: { title: 'Pengaturan Sistem', icon: 'ki-outline ki-setting-2', color: 'danger' },
                    sk: { title: 'Surat Keputusan', icon: 'ki-outline ki-document-signed', color: 'dark' },
                    mutasi: { title: 'Mutasi Pegawai', icon: 'ki-outline ki-shuffle', color: 'secondary' },
                    user_management: { title: 'Manajemen User', icon: 'ki-outline ki-user-edit', color: 'info' },
                    other: { title: 'Lainnya', icon: 'ki-outline ki-questionnaire-tablet', color: 'dark' }
                };

                Object.keys(groups).forEach(function(groupKey) {
                    var permissions = groups[groupKey];
                    if (permissions.length > 0) {
                        var config = categoryConfig[groupKey];
                        
                        html += '<div class="mb-6">';
                        html += '<div class="d-flex align-items-center mb-3">';
                        html += '<i class="' + config.icon + ' fs-2 text-' + config.color + ' me-3"></i>';
                        html += '<h5 class="fw-bold text-gray-800 mb-0">' + config.title + '</h5>';
                        html += '<span class="badge badge-light-' + config.color + ' ms-2">' + permissions.length + '</span>';
                        html += '</div>';
                        
                        html += '<div class="d-flex flex-wrap gap-2">';
                        permissions.forEach(function(permission) {
                            var cleanName = permission.name.replace(/^hrd_/, '').replace(/_/g, ' ');
                            html += '<span class="badge badge-light-' + config.color + ' fs-7">' + cleanName + '</span>';
                        });
                        html += '</div>';
                        html += '</div>';
                    }
                });

                if (html === '') {
                    html = '<div class="text-center py-10">';
                    html += '<i class="ki-outline ki-information-2 fs-3x text-muted"></i>';
                    html += '<p class="text-muted fs-6 mt-3">Tidak ada permission yang ditemukan</p>';
                    html += '</div>';
                }

                container.html(html);
            }

            // Function to update permission statistics
            function updatePermissionStats(groups) {
                var dashboardCount = groups.dashboard.length;
                var karyawanCount = groups.karyawan.length + groups.sk.length + groups.mutasi.length;
                var systemCount = groups.settings.length + groups.user_management.length + groups.export.length;

                $('#detail_dashboard_perms').text(dashboardCount);
                $('#detail_karyawan_perms').text(karyawanCount);
                $('#detail_system_perms').text(systemCount);

                // Update progress bars (sample calculation)
                var maxPerCategory = 10; // Adjust based on your system
                $('#dashboard_progress').css('width', Math.min((dashboardCount / maxPerCategory) * 100, 100) + '%');
                $('#karyawan_progress').css('width', Math.min((karyawanCount / maxPerCategory) * 100, 100) + '%');
                $('#system_progress').css('width', Math.min((systemCount / maxPerCategory) * 100, 100) + '%');
            }

            // Reset modal on close
            $('#kt_modal_edit_role').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $('.error-text').remove();
                $('#e_permissions').val(null).trigger('change');
            });
        });
    </script>
@endpush