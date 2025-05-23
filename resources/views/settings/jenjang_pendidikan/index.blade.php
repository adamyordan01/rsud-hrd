@extends('layouts.backend', ['title' => 'Jenjang Pendidikan'])

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .error-text {
            color: #dc3545;
            font-size: 0.875rem;
        }
        .sortable tbody tr {
            cursor: move;
        }
        .sortable .ui-sortable-helper {
            background: #f8f9fa;
            opacity: 0.8;
        }
    </style>
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
                        <li class="breadcrumb-item text-muted">Jenjang Pendidikan</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="javascript:;" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" id="add-jenjang">
                        Tambah Jenjang Pendidikan
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
                        <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Jenjang Pendidikan ...">
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table id="jenjangpendidikan-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold sortable">
                            <thead>
                                <tr>
                                    <th class="w-10px">No.</th>
                                    <th class="">Jenjang Pendidikan</th>
                                    <th class="">Nilai Indeks</th>
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

    <!-- Modal: Tambah/Edit Jenjang Pendidikan -->
    <div class="modal fade" id="jenjangModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-550px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah Jenjang Pendidikan</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="jenjangForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="kd_jenjang_didik" id="kd_jenjang_didik_hidden">
                    <div class="modal-body">
                        <!-- Nama Jenjang Pendidikan -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Nama Jenjang Pendidikan</label>
                            <input type="text" class="form-control form-control-solid" name="jenjang_didik" id="jenjang_didik" placeholder="Nama Jenjang Pendidikan" autofocus />
                            <div class="fv-plugins-message-container invalid-feedback error-text jenjang_didik_error"></div>
                        </div>
                        <!-- Nilai Indeks -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Nilai Indeks</label>
                            <input type="number" step="0.01" class="form-control form-control-solid" name="nilaiIndex" id="nilaiIndex" placeholder="Nilai Indeks" />
                            <div class="fv-plugins-message-container invalid-feedback error-text nilaiIndex_error"></div>
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
            var table = $('#jenjangpendidikan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.settings.jenjang-pendidikan.index") }}',
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
                        data: 'nilaiIndex', 
                        name: 'nilaiIndex',
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
                columnDefs: [
                    { targets: [2], orderData: [2] } // Pastikan nilaiIndex diurutkan dengan benar
                ],
                order: [[2, 'asc']], // Urutkan berdasarkan nilaiIndex
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                createdRow: function(row, data, dataIndex) {
                    $(row).attr('data-id', data.kd_jenjang_didik);
                },
                initComplete: function() {
                    $('[data-kt-user-table-filter="search"]').on('keyup', function() {
                        table.search(this.value).draw();
                    });
                    console.log('DataTable initialized with order:', table.order());
                }
            });

            // Inisialisasi jQuery UI Sortable
            $('#jenjangpendidikan-table tbody').sortable({
                items: 'tr',
                cursor: 'move',
                placeholder: 'ui-sortable-placeholder',
                update: function(event, ui) {
                    var order = $(this).sortable('toArray', { attribute: 'data-id' });
                    $.ajax({
                        url: '{{ route("admin.settings.jenjang-pendidikan.update-order") }}',
                        type: 'POST',
                        data: { order: order },
                        success: function(response) {
                            toastr.success(response.message, 'Success');
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Gagal menyimpan urutan.');
                        }
                    });
                }
            }).disableSelection();

            table.on('draw', function() {
                KTMenu.createInstances();
            });

            // Open modal for adding new record
            $('#add-jenjang').on('click', function() {
                $('#modalTitle').text('Tambah Jenjang Pendidikan');
                $('#formMethod').val('POST');
                $('#jenjangForm')[0].reset();
                $('.error-text').text('');
                $('#jenjangModal').modal('show');
            });

            // Open modal for editing record
            $('#jenjangpendidikan-table').on('click', '.edit', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route("admin.settings.jenjang-pendidikan.edit", ":id") }}'.replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        $('#modalTitle').text('Edit Jenjang Pendidikan');
                        $('#formMethod').val('PATCH');
                        $('#kd_jenjang_didik_hidden').val(response.data.kd_jenjang_didik);
                        $('#jenjang_didik').val(response.data.jenjang_didik);
                        $('#nilaiIndex').val(response.data.nilaiIndex);
                        $('.error-text').text('');
                        $('#jenjangModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Gagal mengambil data.');
                    }
                });
            });

            // Submit form (add/edit)
            $('#jenjangForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var method = $('#formMethod').val();
                var url = method === 'POST' 
                    ? '{{ route("admin.settings.jenjang-pendidikan.store") }}' 
                    : '{{ route("admin.settings.jenjang-pendidikan.update", ":id") }}'.replace(':id', $('#kd_jenjang_didik_hidden').val());
                
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
                        $('#jenjangModal').modal('hide');
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

            // Delete record
            $('#jenjangpendidikan-table').on('click', '.delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus jenjang pendidikan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("admin.settings.jenjang-pendidikan.destroy", ":id") }}'.replace(':id', id),
                            type: 'DELETE',
                            success: function(response) {
                                toastr.success(response.message, 'Success');
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message || 'Gagal menghapus data.');
                            }
                        });
                    }
                });
            });

            // Reset form on modal close
            $('#jenjangModal').on('hidden.bs.modal', function() {
                $('#jenjangForm')[0].reset();
                $('.error-text').text('');
            });
        });
    </script>
@endpush