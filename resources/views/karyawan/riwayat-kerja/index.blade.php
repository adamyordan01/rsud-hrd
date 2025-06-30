@extends('layouts.backend', ['title' => 'Riwayat Kerja'])

@inject('carbon', 'Carbon\Carbon')

@push('styles')
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
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
                            Riwayat Kerja
                        </li>
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
                            <h3 class="fw-bold m-0">Riwayat Kerja</h3>
                        </div>

                        <a href="javascript:;" id="add-riwayat-kerja"
                            class="btn btn-sm btn-primary align-self-center">
                            Tambah Riwayat Kerja
                        </a>
                    </div>
        
                    <div class="card-body p-9">
                        <div id="kt_riwayat_kerja_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <table id="riwayat-kerja-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                                    <thead>
                                        <tr>
                                            <th class="">No.</th>
                                            <th class="">Pejabat & Perusahaan</th>
                                            <th class="">No. SK & TMT</th>
                                            <th class="">Tanggal SK</th>
                                            <th class="">Berkas</th>
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
    </div>

    <!-- Modal Riwayat Kerja (Add/Edit) -->
    <div class="modal fade" id="riwayatKerjaModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah Riwayat Kerja</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="riwayatKerjaForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="kd_karyawan" id="kd_karyawan" value="{{ $karyawan->kd_karyawan }}">
                    <input type="hidden" name="urut_kerja" id="urut_kerja">
                    
                    <div class="modal-body">
                        <div class="row">
                            <!-- Pejabat -->
                            <div class="col-md-6">
                                <div class="d-flex flex-column mb-3">
                                    <label class="fw-semibold fs-6 mb-2">Pejabat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-solid" name="pejabat" id="pejabat" placeholder="Nama Pejabat" />
                                    <div class="fv-plugins-message-container invalid-feedback error-text pejabat_error"></div>
                                </div>
                            </div>

                            <!-- Perusahaan -->
                            <div class="col-md-6">
                                <div class="d-flex flex-column mb-3">
                                    <label class="fw-semibold fs-6 mb-2">Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-solid" name="perusahaan" id="perusahaan" placeholder="Nama Perusahaan" />
                                    <div class="fv-plugins-message-container invalid-feedback error-text perusahaan_error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- No. SK -->
                            <div class="col-md-6">
                                <div class="d-flex flex-column mb-3">
                                    <label class="fw-semibold fs-6 mb-2">No. SK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-solid" name="no_sk" id="no_sk" placeholder="Nomor SK" />
                                    <div class="fv-plugins-message-container invalid-feedback error-text no_sk_error"></div>
                                </div>
                            </div>

                            <!-- Tanggal SK -->
                            <div class="col-md-6">
                                <div class="d-flex flex-column mb-3">
                                    <label class="fw-semibold fs-6 mb-2">Tanggal SK <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-solid" name="tgl_sk" id="tgl_sk" />
                                    <div class="fv-plugins-message-container invalid-feedback error-text tgl_sk_error"></div>
                                </div>
                            </div>
                        </div>

                        <!-- TMT -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">TMT (Terhitung Mulai Tanggal) <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-solid" name="tmt" id="tmt" />
                            <div class="fv-plugins-message-container invalid-feedback error-text tmt_error"></div>
                        </div>

                        <!-- Keterangan -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Keterangan</label>
                            <textarea class="form-control form-control-solid" name="ket" id="ket" rows="3" placeholder="Keterangan tambahan"></textarea>
                            <div class="fv-plugins-message-container invalid-feedback error-text ket_error"></div>
                        </div>

                        <!-- File Upload -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Upload Berkas SK</label>
                            <input type="file" class="form-control form-control-solid" name="sc_berkas" id="sc_berkas" accept=".pdf,.jpg,.jpeg,.png" />
                            <div class="form-text">Format yang diizinkan: PDF, JPG, JPEG, PNG. Maksimal 2MB.</div>
                            <div class="fv-plugins-message-container invalid-feedback error-text sc_berkas_error"></div>
                            
                            <!-- File yang sudah ada (untuk edit) -->
                            <div id="existing_file_info" class="mt-2" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="ki-duotone ki-file fs-2x me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    File saat ini: <a href="#" id="existing_file_link" target="_blank" class="fw-bold">Lihat File</a>
                                    <br><small class="text-muted">Upload file baru untuk mengganti file yang ada</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary submit-btn">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
    let table;
    let currentMode = 'add'; // 'add' atau 'edit'

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        // Initialize DataTable
        table = $('#riwayat-kerja-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.karyawan.riwayat-kerja.get-data', $karyawan->kd_karyawan) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'pejabat_perusahaan', name: 'pejabat_perusahaan' },
                { data: 'sk_tmt', name: 'sk_tmt' },
                { data: 'tgl_sk', name: 'tgl_sk' },
                { data: 'file', name: 'file', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        table.on('draw', function() {
            KTMenu.createInstances();
        });

        // Tombol Tambah Riwayat Kerja
        $('#add-riwayat-kerja').on('click', function() {
            openModal('add');
        });

        // Handle form submit
        $('#riwayatKerjaForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Handle tombol edit
        $(document).on('click', '.edit', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            editRiwayatKerja(kdKaryawan, urut);
        });

        // Handle tombol delete
        $(document).on('click', '.delete-riwayat-kerja', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            const perusahaan = $(this).data('perusahaan');
            deleteRiwayatKerja(kdKaryawan, urut, perusahaan);
        });

        // Handle modal hide event untuk reset form
        $('#riwayatKerjaModal').on('hidden.bs.modal', function() {
            resetForm();
        });
    });

    // Function untuk membuka modal
    function openModal(mode, data = null) {
        currentMode = mode;
        
        if (mode === 'add') {
            $('#modalTitle').text('Tambah Riwayat Kerja');
            $('#form_method').val('POST');
            resetForm();
        } else {
            $('#modalTitle').text('Edit Riwayat Kerja');
            $('#form_method').val('PATCH');
            populateForm(data);
        }
        
        $('#riwayatKerjaModal').modal('show');
    }

    // Function untuk reset form
    function resetForm() {
        $('#riwayatKerjaForm')[0].reset();
        $('#urut_kerja').val('');
        
        // Hide existing file info
        $('#existing_file_info').hide();
        
        // Clear error messages
        $('.error-text').text('');
        
        // Reset submit button
        $('.submit-btn').attr('disabled', false);
        $('.submit-btn .indicator-label').show();
        $('.submit-btn .indicator-progress').hide();
    }

    // Function untuk populate form saat edit
    function populateForm(data) {
        $('#urut_kerja').val(data.urut_kerja);
        $('#pejabat').val(data.pejabat);
        $('#perusahaan').val(data.perusahaan);
        $('#no_sk').val(data.no_sk);
        $('#tgl_sk').val(data.tgl_sk);
        $('#tmt').val(data.tmt);
        $('#ket').val(data.ket);
        
        // Show existing file info if exists
        if (data.sc_berkas && data.url_file) {
            $('#existing_file_link').attr('href', data.url_file).text(data.sc_berkas);
            $('#existing_file_info').show();
        } else {
            $('#existing_file_info').hide();
        }
    }

    // Function untuk submit form
    function submitForm() {
        const form = $('#riwayatKerjaForm');
        const formData = new FormData(form[0]);
        
        let url;
        if (currentMode === 'add') {
            url = "{{ route('admin.karyawan.riwayat-kerja.store', $karyawan->kd_karyawan) }}";
        } else {
            const kdKaryawan = $('#kd_karyawan').val();
            const urut = $('#urut_kerja').val();
            url = "{{ route('admin.karyawan.riwayat-kerja.update', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                .replace(':kd_karyawan', kdKaryawan)
                .replace(':urut', urut);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.error-text').text('');
                $('.submit-btn').attr('disabled', true);
                $('.submit-btn .indicator-label').hide();
                $('.submit-btn .indicator-progress').show();
            },
            success: function(response) {
                if (response.code === 200) {
                    toastr.success(response.message, 'Berhasil');
                    $('#riwayatKerjaModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(response.message, 'Error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.', 'Error');
                }
            },
            complete: function() {
                $('.submit-btn').attr('disabled', false);
                $('.submit-btn .indicator-label').show();
                $('.submit-btn .indicator-progress').hide();
            }
        });
    }

    // Function untuk edit riwayat kerja
    function editRiwayatKerja(kdKaryawan, urut) {
        const url = "{{ route('admin.karyawan.riwayat-kerja.edit', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
            .replace(':kd_karyawan', kdKaryawan)
            .replace(':urut', urut);

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                // Optional: show loading indicator
            },
            success: function(response) {
                if (response.success) {
                    openModal('edit', response.data);
                } else {
                    toastr.error(response.message, 'Error');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data.', 'Error');
            }
        });
    }

    // Function untuk delete riwayat kerja
    function deleteRiwayatKerja(kdKaryawan, urut, perusahaan) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus riwayat kerja di <strong>"${perusahaan}"</strong>?<br><br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    html: 'Sedang memproses permintaan Anda',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const url = "{{ route('admin.karyawan.riwayat-kerja.destroy', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                    .replace(':kd_karyawan', kdKaryawan)
                    .replace(':urut', urut);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('.delete-riwayat-kerja').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.code === 200) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#d33',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        });
                    },
                    complete: function() {
                        $('.delete-riwayat-kerja').prop('disabled', false);
                    }
                });
            }
        });
    }
</script>
@endpush