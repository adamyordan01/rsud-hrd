@extends('layouts.backend', ['title' => $pageTitle])

@push('styles')
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Master Data
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Pekerjaan</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="javascript:;" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" id="add-pekerjaan">
                        Tambah Pekerjaan
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
                        <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Pekerjaan ...">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table id="pekerjaan-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold sortable">
                            <thead>
                                <tr>
                                    <th class="w-10px">No.</th>
                                    <th class="">Nama Pekerjaan</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tambah/Edit Pekerjaan -->
    <div class="modal fade" id="pekerjaanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-550px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah Pekerjaan</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="pekerjaanForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="kd_pekerjaan" id="kd_pekerjaan_hidden">
                    <div class="modal-body">
                        <!-- Nama Pekerjaan -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2 required">Nama Pekerjaan</label>
                            <input type="text" class="form-control form-control-solid" name="pekerjaan" id="pekerjaan" placeholder="Nama Pekerjaan" autofocus />
                            <div class="fv-plugins-message-container invalid-feedback error-text pekerjaan_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-btn" data-kt-menu-modal-action="submit">
                            <span class="indicator-label">Save Changes</span>
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
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            var table = $('#pekerjaan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.settings.pekerjaan.index") }}',
                    type: 'GET'
                },
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
                    { 
                        data: 'name', 
                        name: 'name',
                        orderable: true
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
                createdRow: function(row, data, dataIndex) {
                    $(row).attr('data-id', data.kd_pekerjaan);
                },
                initComplete: function() {
                    $('[data-kt-user-table-filter="search"]').on('keyup', function() {
                        table.search(this.value).draw();
                    });
                }
            });

            table.on('draw', function() {
                KTMenu.createInstances();
            });

            // Open modal for adding new record
            $('#add-pekerjaan').on('click', function() {
                $('#modalTitle').text('Tambah Pekerjaan');
                $('#formMethod').val('POST');
                $('#pekerjaanForm')[0].reset();
                $('.error-text').text('');
                $('#pekerjaanModal').modal('show');
            });

            // Open modal for editing record
            $('#pekerjaan-table').on('click', '.edit', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route("admin.settings.pekerjaan.edit", ":id") }}'.replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        $('#modalTitle').text('Edit Pekerjaan');
                        $('#formMethod').val('PATCH');
                        $('#kd_pekerjaan_hidden').val(response.data.kd_pekerjaan);
                        $('#pekerjaan').val(response.data.pekerjaan);
                        $('.error-text').text('');
                        $('#pekerjaanModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Gagal mengambil data.');
                    }
                });
            });

            // Submit form (add/edit)
            $('#pekerjaanForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var method = $('#formMethod').val();
                var url = method === 'POST' 
                    ? '{{ route("admin.settings.pekerjaan.store") }}' 
                    : '{{ route("admin.settings.pekerjaan.update", ":id") }}'.replace(':id', $('#kd_pekerjaan_hidden').val());
                
                var formData = new FormData(form[0]);
                if (method === 'PATCH') {
                    formData.append('_method', 'PATCH');
                }

                var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
                form.find('.submit-btn').append(loadingIndicator);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        loadingIndicator.show();
                        $('.error-text').text('');
                        form.find('.submit-btn').attr('disabled', true);
                        form.find('.btn-primary .indicator-label').hide();
                    },
                    success: function(response) {
                        toastr.success(response.message, 'Success');
                        $('#pekerjaanModal').modal('hide');
                        form[0].reset();
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('.' + key + '_error').text(value[0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.');
                        }
                    },
                    complete: function() {
                        loadingIndicator.hide();
                        form.find('.submit-btn').attr('disabled', false);
                        form.find('.btn-primary .indicator-label').show();
                    }
                });
            });

            // Reset form on modal close
            $('#pekerjaanModal').on('hidden.bs.modal', function() {
                $('#pekerjaanForm')[0].reset();
                $('.error-text').text('');
            });
        });
    </script>
@endpush