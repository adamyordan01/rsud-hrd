@extends('layouts.backend', ['title' => $pageTitle])

@inject('carbon', 'Carbon\Carbon')

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        {{ $pageTitle }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">{{ $pageTitle }}</li>
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
                                <h3 class="fw-bold m-0">Data Kemampuan Bahasa</h3>
                            </div>
                            <a href="javascript:;" id="add-bahasa" class="btn btn-sm btn-primary align-self-center">
                                Tambah Data Kemampuan Bahasa
                            </a>
                        </div>
                        <div class="card-body p-9">
                            <div class="table-responsive">
                                <table id="bahasa-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable text-gray-600 fw-semibold">
                                    <thead>
                                        <tr>
                                            <th class="w-10px">No.</th>
                                            <th>Bahasa</th>
                                            <th>Tingkat Kemampuan</th>
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
    </div>

    
    <!-- Modal Kemampuan Bahasa -->
    <div class="modal fade" id="bahasaModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-550px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modal-title">Tambah Data Kemampuan Bahasa</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="bahasaForm">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="kd_karyawan" id="kd_karyawan" />
                    <input type="hidden" name="urut" id="urut" />
                    <div class="modal-body">
                        <!-- Bahasa Bahasa -->
                        <div class="d-flex flex-column mb-3">
                            <label class="required fw-semibold fs-6 mb-2">Bahasa</label>
                            <select 
                                name="bahasa" 
                                id="bahasa" 
                                class="form-select form-select-solid" 
                                data-control="select2" 
                                data-placeholder="Pilih Bahasa" 
                                data-allow-clear="true"
                                data-dropdown-parent="#bahasaModal"
                            >
                                <option value="">Pilih Bahasa</option>
                                @foreach ($bahasa as $item)
                                    <option value="{{ $item->kd_bahasa }}">{{ $item->bahasa }}</option>
                                @endforeach
                            </select>
                            <div class="fv-plugins-message-container invalid-feedback error-text bahasa_error"></div>
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <label class="required fw-semibold fs-6 mb-2">Tingkat Kemampuan</label>
                            <select 
                                name="tingkat_bahasa" 
                                id="tingkat_bahasa" 
                                class="form-select form-select-solid" 
                                data-control="select2" 
                                data-placeholder="Pilih Tingkat Kemampuan" 
                                data-allow-clear="true"
                                data-dropdown-parent="#bahasaModal"
                            >
                                <option value="">Pilih Tingkat Kemampuan</option>
                                @foreach ($tingkat_bahasa as $item)
                                    <option value="{{ $item->kd_tingkat_bahasa }}">{{ $item->tingkat_bahasa }}</option>
                                @endforeach
                            </select>
                            <div class="fv-plugins-message-container invalid-feedback error-text tingkat_bahasa_error"></div>
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
@endsection

@push('scripts')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $(document).ready(function() {
            const table = $('#bahasa-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.karyawan.kemampuan-bahasa.get-data', $karyawan->kd_karyawan) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahasa', name: 'bahasa' },
                    { data: 'tingkat_bahasa', name: 'tingkat_bahasa' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [
                    { className: 'text-end', targets: -1 },
                ],
                drawCallback: function() {
                    // Re-initialize menu dan sortable setelah table di-draw
                    KTMenu.createInstances();
                }
            });

            // Event handlers lainnya...
            $('#add-bahasa').on('click', () => {
                resetForm();
                $('#bahasaForm').attr('action', "{{ route('admin.karyawan.kemampuan-bahasa.store', $karyawan->kd_karyawan) }}");
                $('#bahasaModal').modal('show');
            });

            // Edit handler
            $('#bahasa-table').on('click', '.edit', function(e) {
                e.preventDefault();
                resetForm();

                const kd_karyawan = $(this).data('karyawan');
                const urut = $(this).data('urut');
                const url = "{{ route('admin.karyawan.kemampuan-bahasa.edit', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                    .replace(':kd_karyawan', kd_karyawan)
                    .replace(':urut', urut);

                $.ajax({
                    url,
                    type: 'GET',
                    success: (response) => {
                        if (response.success) {
                            $('#modal-title').text('Edit Data Kemampuan Bahasa');
                            $('#form-method').val('PATCH');
                            $('#bahasaForm').attr('action', "{{ route('admin.karyawan.kemampuan-bahasa.update', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                                .replace(':kd_karyawan', kd_karyawan)
                                .replace(':urut', urut));
                            $('#kd_karyawan').val(response.data.kd_karyawan);
                            $('#urut').val(response.data.urut_klrg);
                            $('#bahasa').val(response.data.kd_bahasa).trigger('change');
                            $('#tingkat_bahasa').val(response.data.kd_tingkat_bahasa).trigger('change');
                            $('#bahasaModal').modal('show');
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: (xhr) => toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data.')
                });
            });

            // Delete handler
            $('#bahasa-table').on('click', '.delete', function(e) {
                e.preventDefault();
                const kd_karyawan = $(this).data('karyawan');
                const urut = $(this).data('urut');
                const url = "{{ route('admin.karyawan.kemampuan-bahasa.destroy', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                    .replace(':kd_karyawan', kd_karyawan)
                    .replace(':urut', urut);

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data kemampuan bahasa akan dihapus secara permanen!",
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
            $('#bahasaForm').on('submit', function(e) {
                e.preventDefault();
                handleFormSubmit($(this), $(this).attr('action'));
            });

            // Helper functions
            const resetForm = () => {
                $('#bahasaForm')[0].reset();
                $('.error-text').text('');
                $('#modal-title').text('Tambah Data Kemampuan Bahasa');
                $('#form-method').val('POST');
                $('#kd_karyawan, #urut').val('');
                $('#bahasa, #tingkat_bahasa').val('').trigger('change');
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
                            $('#bahasaModal').modal('hide');
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