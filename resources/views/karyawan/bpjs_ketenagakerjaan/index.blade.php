@extends('layouts.backend', ['title' => 'BPJS Ketenagakerjaan'])

@inject('carbon', 'Carbon\Carbon')

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        BPJS Ketenagakerjaan
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">BPJS Ketenagakerjaan</li>
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
                                <h3 class="fw-bold m-0">Data BPJS Ketenagakerjaan</h3>
                            </div>
                            <a href="javascript:;" id="add-bpjs-ketenagakerjaan" class="btn btn-sm btn-primary align-self-center">
                                Tambah Kartu BPJS Ketenagakerjaan
                            </a>
                        </div>
                        <div class="card-body p-9">
                            <div class="table-responsive">
                                <table id="bpjs-ketenagakerjaan-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable text-gray-600 fw-semibold">
                                    <thead>
                                        <tr>
                                            <th class="w-10px">No.</th>
                                            <th>Nomor Kartu</th>
                                            <th>File</th>
                                            <th>Status</th>
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
        </div>

        <!-- Modal BPJS Ketenagakerjaan -->
        <div class="modal fade" id="bpjsKetenagakerjaanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog mw-550px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold modal-title" id="modal-title">Tambah BPJS Ketenagakerjaan</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <form method="POST" class="form" id="bpjsKetenagakerjaanForm">
                        @csrf
                        <input type="hidden" name="_method" id="form-method" value="POST">
                        <input type="hidden" name="kd_karyawan" id="kd_karyawan" />
                        <input type="hidden" name="urut" id="urut" />
                        <div class="modal-body">
                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Nomor Kartu BPJS Ketenagakerjaan</label>
                                <input type="text" class="form-control form-control-solid" name="no_kartu" id="no_kartu" placeholder="Nomor BPJS Ketenagakerjaan" />
                                <div class="fv-plugins-message-container invalid-feedback error-text no_kartu_error"></div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <label class="fw-semibold fs-6 mb-2" id="foto_kartu_label">Foto Kartu</label>
                                <input type="file" class="form-control form-control-solid" name="foto_kartu" id="foto_kartu" placeholder="Foto Kartu BPJS Ketenagakerjaan" />
                                <div id="existing_file" class="mt-2"></div>
                                <div class="fv-plugins-message-container invalid-feedback error-text foto_kartu_error"></div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <label class="required fw-semibold fs-6 mb-2">Status</label>
                                <select name="status" id="status" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Status" data-allow-clear="true">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback error-text status_error"></div>
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

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(document).ready(function() {
            const table = $('#bpjs-ketenagakerjaan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.karyawan.bpjs-ketenagakerjaan.get-data', $karyawan->kd_karyawan) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'no_kartu', name: 'no_kartu' },
                    { data: 'file', name: 'file' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' },
                ],
                // tambahkan class text-end pada kolom action
                columnDefs: [
                    { className: 'text-end', targets: -1 },
                ],
            });

            table.on('draw', () => KTMenu.createInstances());

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            const resetForm = () => {
                $('#bpjsKetenagakerjaanForm')[0].reset();
                $('#existing_file').empty();
                $('#no_kartu_error, #foto_kartu_error, #status_error').text('');
                $('#modal-title').text('Tambah BPJS Ketenagakerjaan');
                $('#form-method').val('POST');
                $('#foto_kartu_label').text('Foto Kartu').removeClass('required');
                $('#kd_karyawan, #urut').val('');
                $('#status').val('1').trigger('change');
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
                            $('#bpjsKetenagakerjaanModal').modal('hide');
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

            $('#add-bpjs-ketenagakerjaan').on('click', () => {
                resetForm();
                $('#bpjsKetenagakerjaanForm').attr('action', "{{ route('admin.karyawan.bpjs-ketenagakerjaan.store', $karyawan->kd_karyawan) }}");
                $('#bpjsKetenagakerjaanModal').modal('show');
            });

            $('#bpjs-ketenagakerjaan-table').on('click', '.edit', function(e) {
                e.preventDefault();
                resetForm();

                const kd_karyawan = $(this).data('karyawan');
                const urut = $(this).data('urut');
                const url = "{{ route('admin.karyawan.bpjs-ketenagakerjaan.edit', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                    .replace(':kd_karyawan', kd_karyawan)
                    .replace(':urut', urut);

                $.ajax({
                    url,
                    type: 'GET',
                    success: (response) => {
                        if (response.success) {
                            $('#modal-title').text('Edit BPJS Ketenagakerjaan');
                            $('#form-method').val('PATCH');
                            $('#bpjsKetenagakerjaanForm').attr('action', "{{ route('admin.karyawan.bpjs-ketenagakerjaan.update', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                                .replace(':kd_karyawan', kd_karyawan)
                                .replace(':urut', urut));
                            $('#kd_karyawan').val(response.data.kd_karyawan);
                            $('#urut').val(response.data.urut_kartu);
                            $('#no_kartu').val(response.data.no_kartu);
                            $('#status').val(response.data.status).trigger('change');
                            $('#foto_kartu_label').text('Foto Kartu (opsional)').addClass('required');
                            if (response.data.foto_kartu) {
                                $('#existing_file').html(`File saat ini: <a href="${response.data.url_file}" target="_blank">${response.data.foto_kartu}</a>`);
                            }
                            $('#bpjsKetenagakerjaanModal').modal('show');
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: (xhr) => toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data.')
                });
            });

            $('#bpjsKetenagakerjaanForm').on('submit', function(e) {
                e.preventDefault();
                handleFormSubmit($(this), $(this).attr('action'));
            });
        });
    </script>
@endpush