@extends('layouts.backend', ['title' => 'Riwayat Organisasi'])

@inject('carbon', 'Carbon\Carbon')

@push('styles')
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Riwayat Organisasi
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.karyawan.show', $karyawan->kd_karyawan) }}" class="text-muted text-hover-primary">
                                Karyawan
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Riwayat Organisasi</li>
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
                            <h3 class="fw-bold m-0">Riwayat Organisasi</h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-primary" id="add-riwayat-organisasi">
                                <i class="ki-outline ki-plus fs-3"></i>
                                Tambah Riwayat Organisasi
                            </button>
                        </div>
                    </div>
        
                    <div class="card-body p-9">
                        <div class="table-responsive">
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="riwayat-organisasi-table">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>No</th>
                                        <th>Pejabat / Organisasi</th>
                                        <th>No. SK / Jabatan</th>
                                        <th>Tanggal SK</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
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

    <!-- Modal Riwayat Organisasi (Add/Edit) -->
    <div class="modal fade" id="riwayatOrganisasiModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah Riwayat Organisasi</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="riwayatOrganisasiForm">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="kd_karyawan" id="kd_karyawan" value="{{ $karyawan->kd_karyawan }}">
                    <input type="hidden" name="urut_org" id="urut_org">
                    
                    <div class="modal-body">
                        <div class="row">
                            <!-- Pejabat -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Pejabat</label>
                                <input type="text" class="form-control form-control-solid" name="pejabat" id="pejabat" placeholder="Masukkan nama pejabat">
                                <div class="text-danger error-text pejabat_error"></div>
                            </div>

                            <!-- No. SK -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">No. SK</label>
                                <input type="text" class="form-control form-control-solid" name="no_sk" id="no_sk" placeholder="Masukkan nomor SK">
                                <div class="text-danger error-text no_sk_error"></div>
                            </div>

                            <!-- Tanggal SK -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Tanggal SK</label>
                                <input type="date" class="form-control form-control-solid" name="tgl_sk" id="tgl_sk">
                                <div class="text-danger error-text tgl_sk_error"></div>
                            </div>

                            <!-- Organisasi -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Organisasi</label>
                                <input type="text" class="form-control form-control-solid" name="organisasi" id="organisasi" placeholder="Masukkan nama organisasi">
                                <div class="text-danger error-text organisasi_error"></div>
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-12 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Jabatan</label>
                                <input type="text" class="form-control form-control-solid" name="jabatan" id="jabatan" placeholder="Masukkan jabatan dalam organisasi">
                                <div class="text-danger error-text jabatan_error"></div>
                            </div>

                            <!-- Keterangan -->
                            <div class="col-md-12 mb-7">
                                <label class="fw-semibold fs-6 mb-2">Keterangan</label>
                                <textarea class="form-control form-control-solid" name="ket" id="ket" rows="3" placeholder="Masukkan keterangan tambahan (opsional)"></textarea>
                                <div class="text-danger error-text ket_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary submit-btn">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress" style="display: none;">
                                Menyimpan...
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
    let table;
    let currentMode = 'add'; // 'add' atau 'edit'

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        // Initialize DataTable
        table = $('#riwayat-organisasi-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.karyawan.riwayat-organisasi.get-data', $karyawan->kd_karyawan) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'pejabat_organisasi', name: 'pejabat_organisasi' },
                { data: 'sk_jabatan', name: 'sk_jabatan' },
                { data: 'tgl_sk', name: 'tgl_sk' },
                { data: 'ket', name: 'ket' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            language: {
                emptyTable: "Belum ada data riwayat organisasi",
                zeroRecords: "Data tidak ditemukan",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                search: "Cari:",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        table.on('draw', function() {
            KTMenu.createInstances();
        });

        // Tombol Tambah Riwayat Organisasi
        $('#add-riwayat-organisasi').on('click', function() {
            openModal('add');
        });

        // Handle form submit
        $('#riwayatOrganisasiForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Handle tombol edit
        $(document).on('click', '.edit', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            editRiwayatOrganisasi(kdKaryawan, urut);
        });

        // Handle tombol delete
        $(document).on('click', '.delete-riwayat-organisasi', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            const organisasi = $(this).data('organisasi');
            deleteRiwayatOrganisasi(kdKaryawan, urut, organisasi);
        });

        // Handle modal hide event untuk reset form
        $('#riwayatOrganisasiModal').on('hidden.bs.modal', function() {
            resetForm();
        });
    });

    // Function untuk membuka modal
    function openModal(mode, data = null) {
        currentMode = mode;
        
        if (mode === 'add') {
            $('#modalTitle').text('Tambah Riwayat Organisasi');
            $('#form_method').val('POST');
            resetForm();
        } else {
            $('#modalTitle').text('Edit Riwayat Organisasi');
            $('#form_method').val('PATCH');
            populateForm(data);
        }
        
        $('#riwayatOrganisasiModal').modal('show');
    }

    // Function untuk reset form
    function resetForm() {
        $('#riwayatOrganisasiForm')[0].reset();
        $('#urut_org').val('');
        
        // Clear error messages
        $('.error-text').text('');
        
        // Reset submit button
        $('.submit-btn').attr('disabled', false);
        $('.submit-btn .indicator-label').show();
        $('.submit-btn .indicator-progress').hide();
    }

    // Function untuk populate form saat edit
    function populateForm(data) {
        $('#urut_org').val(data.urut_org);
        $('#pejabat').val(data.pejabat);
        $('#no_sk').val(data.no_sk);
        $('#tgl_sk').val(data.tgl_sk);
        $('#organisasi').val(data.organisasi);
        $('#jabatan').val(data.jabatan);
        $('#ket').val(data.ket);
    }

    // Function untuk submit form
    function submitForm() {
        const form = $('#riwayatOrganisasiForm');
        const formData = new FormData(form[0]);
        
        let url;
        if (currentMode === 'add') {
            url = "{{ route('admin.karyawan.riwayat-organisasi.store', $karyawan->kd_karyawan) }}";
        } else {
            const kdKaryawan = $('#kd_karyawan').val();
            const urut = $('#urut_org').val();
            url = "{{ route('admin.karyawan.riwayat-organisasi.update', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
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
                    $('#riwayatOrganisasiModal').modal('hide');
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

    // Function untuk edit riwayat organisasi
    function editRiwayatOrganisasi(kdKaryawan, urut) {
        const url = "{{ route('admin.karyawan.riwayat-organisasi.edit', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
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

    // Function untuk delete riwayat organisasi
    function deleteRiwayatOrganisasi(kdKaryawan, urut, organisasi) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus riwayat organisasi di <strong>"${organisasi}"</strong>?<br><br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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

                const url = "{{ route('admin.karyawan.riwayat-organisasi.destroy', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                    .replace(':kd_karyawan', kdKaryawan)
                    .replace(':urut', urut);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.code === 200) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
