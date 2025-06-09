@extends('layouts.backend', ['title' => 'Data Keluarga'])

@inject('carbon', 'Carbon\Carbon')

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Keluarga
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Keluarga</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <x-employee-header :karyawan="$karyawan" :missing-fields="$missing_fields" :persentase-kelengkapan="$persentase_kelengkapan" />

            <div class="row g-10 g-xl-10">
                <div class="col-md-12">
                    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
                        <div class="card-header cursor-pointer">
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Data Keluarga</h3>
                            </div>
                            <a href="javascript:;" id="add-keluarga" class="btn btn-sm btn-primary align-self-center">
                                Tambah Data Keluarga
                            </a>
                        </div>
                        <div class="card-body p-9">
                            <div class="table-responsive">
                                <table id="keluarga-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable text-gray-600 fw-semibold">
                                    <thead>
                                        <tr>
                                            <th class="w-10px">No.</th>
                                            <th>Nama</th>
                                            <th>Tempat, Tanggal Lahir</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Pendidikan</th>
                                            <th>Pekerjaan</th>
                                            <th class="d-none">Urut</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sortable"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Keluarga -->
        <div class="modal fade" id="keluargaModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog mw-550px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold modal-title" id="modal-title">Tambah Data Keluarga</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <form method="POST" class="form" id="keluargaForm">
                        @csrf
                        <input type="hidden" name="_method" id="form-method" value="POST">
                        <input type="hidden" name="kd_karyawan" id="kd_karyawan" />
                        <input type="hidden" name="urut" id="urut" />
                        <div class="modal-body">
                            <!-- Hubungan Keluarga -->
                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Hubungan</label>
                                <select 
                                    name="hubungan" 
                                    id="hubungan" 
                                    class="form-select form-select-solid" 
                                    data-control="select2" 
                                    data-placeholder="Pilih hubungan" 
                                    data-allow-clear="true"
                                    data-dropdown-parent="#keluargaModal"
                                >
                                    <option value="">Pilih Hubungan</option>
                                    @foreach ($hubungan as $item)
                                        <option value="{{ $item->kd_hub_klrg }}">{{ $item->hub_klrg }}</option>
                                    @endforeach
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback error-text hubungan_error"></div>
                            </div>

                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Nama</label>
                                <input type="text" class="form-control form-control-solid" name="nama" id="nama" placeholder="Nama Lengkap" />
                                <div class="fv-plugins-message-container invalid-feedback error-text nama_error"></div>
                            </div>
                            
                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Tempat Lahir</label>
                                <input type="text" class="form-control form-control-solid" name="tempat_lahir" id="tempat_lahir" placeholder="Tempat Lahir" />
                                <div class="fv-plugins-message-container invalid-feedback error-text tempat_lahir_error"></div>
                            </div>

                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Tanggal Lahir</label>
                                <input type="date" class="form-control form-control-solid" name="tgl_lahir" id="tgl_lahir" />
                                <div class="fv-plugins-message-container invalid-feedback error-text tgl_lahir_error"></div>
                            </div>

                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Jenis Kelamin</label>
                                <select 
                                    name="sex" 
                                    id="sex" 
                                    class="form-select form-select-solid" 
                                    data-control="select2" 
                                    data-placeholder="Pilih Jenis Kelamin" 
                                    data-allow-clear="true"
                                    data-dropdown-parent="#keluargaModal"
                                >
                                    <option value="1">Laki-laki</option>
                                    <option value="0">Perempuan</option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback error-text sex_error"></div>
                            </div>

                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Pendidikan Terakhir</label>
                                <select 
                                    name="pendidikan" 
                                    id="pendidikan" 
                                    class="form-select form-select-solid" 
                                    data-control="select2" 
                                    data-placeholder="Pilih Pendidikan Terakhir" 
                                    data-allow-clear="true"
                                    data-dropdown-parent="#keluargaModal"
                                >
                                    <option value="">Pilih Pendidikan Terakhir</option>
                                    @foreach ($pendidikan as $item)
                                        <option value="{{ $item->kd_jenjang_didik }}">{{ $item->jenjang_didik }}</option>
                                    @endforeach
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback error-text pendidikan_error"></div>
                            </div>
                            
                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Pekerjaan</label>
                                <select 
                                    name="pekerjaan" 
                                    id="pekerjaan" 
                                    class="form-select form-select-solid" 
                                    data-control="select2" 
                                    data-placeholder="Pilih Pekerjaan" 
                                    data-allow-clear="true"
                                    data-dropdown-parent="#keluargaModal"
                                >
                                    <option value="">Pilih Pekerjaan</option>
                                    @foreach ($pekerjaan as $item)
                                        <option value="{{ $item->kd_pekerjaan }}">{{ $item->pekerjaan }}</option>
                                    @endforeach
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback error-text pekerjaan_error"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary submit-btn">
                                <span class="indicator-label">Save Changes</span>
                                <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        /* CSS yang diperbaiki untuk sortable functionality */

        .sortable-placeholder {
            border: 2px dashed #007bff;
            background-color: rgba(0, 123, 255, 0.1);
            height: 50px;
            visibility: visible !important;
            box-sizing: border-box;
        }

        .sortable-handle {
            cursor: move;
            color: #6c757d;
            transition: color 0.15s ease-in-out;
        }

        .sortable-handle:hover {
            color: #007bff;
        }

        /* Style untuk row yang sedang di-drag */
        .ui-sortable-helper {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border: 1px solid #dee2e6;
            opacity: 0.9;
        }

        /* Style untuk table row saat sorting */
        .ui-sortable tr {
            transition: none;
        }

        .ui-sortable tr:hover .sortable-handle {
            color: #007bff;
        }

        /* Ensure proper spacing for action buttons */
        .d-flex.gap-2 {
            align-items: center;
        }

        /* Fix untuk DataTable saat sorting */
        #keluarga-table tbody.ui-sortable tr {
            cursor: default;
        }

        #keluarga-table tbody.ui-sortable tr .sortable-handle {
            cursor: move;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(document).ready(function() {
            const table = $('#keluarga-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.karyawan.keluarga.get-data', $karyawan->kd_karyawan) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'tempat_tanggal_lahir', name: 'tempat_tanggal_lahir' },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin' },
                    { data: 'pendidikan', name: 'pendidikan' },
                    { data: 'pekerjaan', name: 'pekerjaan' },
                    { data: 'urut', name: 'urut', className: 'd-none' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [
                    { className: 'text-end', targets: -1 },
                    {
                        targets: '_all',
                        createdCell: function(td, cellData, rowData, row, col) {
                            // Set data-urut attribute pada tr element
                            if (col === 6) { // Kolom urut (hidden)
                                $(td).parent('tr').attr('data-urut', rowData.urut);
                                $(td).parent('tr').attr('id', 'row-' + rowData.urut);
                            }
                        }
                    }
                ],
                order: [[6, 'asc']], // Order by urut column
                drawCallback: function() {
                    // Re-initialize menu dan sortable setelah table di-draw
                    KTMenu.createInstances();
                    initSortable();
                }
            });

            // Function untuk inisialisasi sortable
            const initSortable = () => {
                // Destroy existing sortable jika ada
                if ($('#keluarga-table tbody').hasClass('ui-sortable')) {
                    $('#keluarga-table tbody').sortable('destroy');
                }

                $('#keluarga-table tbody').sortable({
                    handle: '.sortable-handle',
                    placeholder: 'sortable-placeholder',
                    forcePlaceholderSize: true,
                    tolerance: 'pointer',
                    helper: function(e, tr) {
                        var $originals = tr.children();
                        var $helper = tr.clone();
                        $helper.children().each(function(index) {
                            $(this).width($originals.eq(index).width());
                        });
                        return $helper;
                    },
                    start: function(event, ui) {
                        ui.placeholder.height(ui.item.height());
                    },
                    update: function(event, ui) {
                        // Ambil urutan baru dari DOM
                        const newOrder = [];
                        $('#keluarga-table tbody tr').each(function() {
                            const urut = $(this).attr('data-urut');
                            if (urut && urut !== '') {
                                newOrder.push(parseInt(urut));
                            }
                        });

                        console.log('New order:', newOrder);

                        if (newOrder.length > 0) {
                            // Kirim AJAX request untuk update urutan
                            $.ajax({
                                url: "{{ route('admin.karyawan.keluarga.reorder', $karyawan->kd_karyawan) }}",
                                type: 'POST',
                                data: { 
                                    order: newOrder,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                beforeSend: function() {
                                    // Disable sortable sementara
                                    $('#keluarga-table tbody').sortable('disable');
                                },
                                success: function(response) {
                                    if (response.success) {
                                        toastr.success(response.message, 'Success');
                                        // Reload table untuk mendapatkan data terbaru
                                        table.ajax.reload(null, false);
                                    } else {
                                        toastr.error(response.message, 'Error');
                                        table.ajax.reload(null, false);
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Reorder error:', xhr.responseJSON);
                                    toastr.error(xhr.responseJSON?.message || 'Gagal menyimpan urutan.');
                                    table.ajax.reload(null, false);
                                },
                                complete: function() {
                                    // Re-enable sortable
                                    $('#keluarga-table tbody').sortable('enable');
                                }
                            });
                        }
                    }
                }).disableSelection();
            };

            // Event handlers lainnya...
            $('#add-keluarga').on('click', () => {
                resetForm();
                $('#keluargaForm').attr('action', "{{ route('admin.karyawan.keluarga.store', $karyawan->kd_karyawan) }}");
                $('#keluargaModal').modal('show');
            });

            // Edit handler
            $('#keluarga-table').on('click', '.edit', function(e) {
                e.preventDefault();
                resetForm();

                const kd_karyawan = $(this).data('karyawan');
                const urut = $(this).data('urut');
                const url = "{{ route('admin.karyawan.keluarga.edit', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                    .replace(':kd_karyawan', kd_karyawan)
                    .replace(':urut', urut);

                $.ajax({
                    url,
                    type: 'GET',
                    success: (response) => {
                        if (response.success) {
                            $('#modal-title').text('Edit Data Keluarga');
                            $('#form-method').val('PATCH');
                            $('#keluargaForm').attr('action', "{{ route('admin.karyawan.keluarga.update', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                                .replace(':kd_karyawan', kd_karyawan)
                                .replace(':urut', urut));
                            $('#kd_karyawan').val(response.data.kd_karyawan);
                            $('#urut').val(response.data.urut_klrg);
                            $('#hubungan').val(response.data.hubungan).trigger('change');
                            $('#nama').val(response.data.nama);
                            $('#tempat_lahir').val(response.data.tempat_lahir);
                            $('#tgl_lahir').val(response.data.tgl_lahir);
                            $('#sex').val(response.data.sex).trigger('change');
                            $('#pendidikan').val(response.data.pendidikan).trigger('change');
                            $('#pekerjaan').val(response.data.pekerjaan).trigger('change');
                            $('#keluargaModal').modal('show');
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: (xhr) => toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data.')
                });
            });

            // Delete handler
            $('#keluarga-table').on('click', '.delete', function(e) {
                e.preventDefault();
                const kd_karyawan = $(this).data('karyawan');
                const urut = $(this).data('urut');
                const url = "{{ route('admin.karyawan.keluarga.destroy', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                    .replace(':kd_karyawan', kd_karyawan)
                    .replace(':urut', urut);

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data keluarga akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url,
                            type: 'DELETE',
                            success: (response) => {
                                if (response.success) {
                                    toastr.success(response.message, 'Success');
                                    table.ajax.reload();
                                } else {
                                    toastr.error(response.message, 'Error');
                                }
                            },
                            error: (xhr) => {
                                console.error('Error hapus:', xhr.responseJSON);
                                toastr.error(xhr.responseJSON?.message || 'Gagal menghapus data.');
                            }
                        });
                    }
                });
            });

            // Form submit handler
            $('#keluargaForm').on('submit', function(e) {
                e.preventDefault();
                handleFormSubmit($(this), $(this).attr('action'));
            });

            // Helper functions
            const resetForm = () => {
                $('#keluargaForm')[0].reset();
                $('.error-text').text('');
                $('#modal-title').text('Tambah Data Keluarga');
                $('#form-method').val('POST');
                $('#kd_karyawan, #urut').val('');
                $('#hubungan, #sex, #pendidikan, #pekerjaan').val('').trigger('change');
            };

            const handleFormSubmit = (form, url) => {
                const formData = new FormData(form[0]);
                const $submitBtn = $(form).find('.submit-btn');
                const $indicatorLabel = $submitBtn.find('.indicator-label');
                const $indicatorProgress = $submitBtn.find('.indicator-progress');

                $.ajax({
                    url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: () => {
                        $indicatorProgress.show();
                        $('.error-text').text('');
                        $submitBtn.prop('disabled', true);
                        $indicatorLabel.hide();
                    },
                    success: (response) => {
                        if (response.success) {
                            toastr.success(response.message, 'Success');
                            $('#keluargaModal').modal('hide');
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: (xhr) => {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, (key, value) => {
                                $(`.${key}_error`).text(value[0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.');
                        }
                    },
                    complete: () => {
                        $indicatorProgress.hide();
                        $submitBtn.prop('disabled', false);
                        $indicatorLabel.show();
                    }
                });
            };

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
@endpush