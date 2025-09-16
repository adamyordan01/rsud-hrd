@extends('layouts.backend', ['title' => 'Penghargaan'])

@inject('carbon', 'Carbon\Carbon')

@push('styles')
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Penghargaan
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
                        <li class="breadcrumb-item text-muted">Penghargaan</li>
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
                            <h3 class="fw-bold m-0">Penghargaan</h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-primary" id="add-penghargaan">
                                <i class="ki-outline ki-plus fs-3"></i>
                                Tambah Penghargaan
                            </button>
                        </div>
                    </div>
        
                    <div class="card-body p-9">
                        <div class="table-responsive">
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="penghargaan-table">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>No</th>
                                        <th>Tanggal SK</th>
                                        <th>Pejabat / Bentuk</th>
                                        <th>No. SK / Event</th>
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

    <!-- Modal Penghargaan (Add/Edit) -->
    <div class="modal fade" id="penghargaanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah Penghargaan</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="penghargaanForm">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="kd_karyawan" id="kd_karyawan" value="{{ $karyawan->kd_karyawan }}">
                    <input type="hidden" name="urut_peng" id="urut_peng">
                    
                    <div class="modal-body">
                        <div class="row">
                            <!-- Pejabat -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Pejabat Pemberi</label>
                                <input type="text" class="form-control form-control-solid" name="pejabat" id="pejabat" placeholder="Masukkan nama pejabat pemberi">
                                <div class="text-danger error-text pejabat_error"></div>
                            </div>

                            <!-- Tanggal SK -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Tanggal SK</label>
                                <input type="date" class="form-control form-control-solid" name="tgl_sk" id="tgl_sk">
                                <div class="text-danger error-text tgl_sk_error"></div>
                            </div>

                            <!-- No. SK -->
                            <div class="col-md-6 mb-7">
                                <label class="fw-semibold fs-6 mb-2">No. SK</label>
                                <input type="text" class="form-control form-control-solid" name="no_sk" id="no_sk" placeholder="Masukkan nomor SK (opsional)">
                                <div class="text-danger error-text no_sk_error"></div>
                            </div>

                            <!-- Bentuk Penghargaan -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Bentuk Penghargaan</label>
                                <input type="text" class="form-control form-control-solid" name="bentuk" id="bentuk" placeholder="Masukkan bentuk penghargaan">
                                <div class="text-danger error-text bentuk_error"></div>
                            </div>

                            <!-- Event/Nama Penghargaan -->
                            <div class="col-md-12 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Event/Nama Penghargaan</label>
                                <input type="text" class="form-control form-control-solid" name="event" id="event" placeholder="Masukkan nama event atau nama penghargaan">
                                <div class="text-danger error-text event_error"></div>
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
        table = $('#penghargaan-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.karyawan.penghargaan.data', $karyawan->kd_karyawan) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tgl_sk', name: 'tgl_sk' },
                { data: 'pejabat_bentuk', name: 'pejabat_bentuk' },
                { data: 'sk_event', name: 'sk_event' },
                { data: 'ket', name: 'ket' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            language: {
                emptyTable: "Belum ada data penghargaan",
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

        // Tombol Tambah Penghargaan
        $('#add-penghargaan').on('click', function() {
            openModal('add');
        });

        // Handle form submit
        $('#penghargaanForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Handle tombol edit
        $(document).on('click', '.edit', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            editPenghargaan(kdKaryawan, urut);
        });

        // Handle tombol delete
        $(document).on('click', '.delete-penghargaan', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            const event = $(this).data('event');
            deletePenghargaan(kdKaryawan, urut, event);
        });

        // Handle modal hide event untuk reset form
        $('#penghargaanModal').on('hidden.bs.modal', function() {
            resetForm();
        });
    });

    // Function untuk membuka modal
    function openModal(mode, data = null) {
        currentMode = mode;
        
        if (mode === 'add') {
            $('#modalTitle').text('Tambah Penghargaan');
            $('#form_method').val('POST');
            resetForm();
        } else {
            $('#modalTitle').text('Edit Penghargaan');
            $('#form_method').val('PUT');
            populateForm(data);
        }
        
        $('#penghargaanModal').modal('show');
    }

    // Function untuk reset form
    function resetForm() {
        $('#penghargaanForm')[0].reset();
        $('#urut_peng').val('');
        
        // Clear error messages
        $('.error-text').text('');
        
        // Reset submit button
        $('.submit-btn').attr('disabled', false);
        $('.submit-btn .indicator-label').show();
        $('.submit-btn .indicator-progress').hide();
    }

    // Function untuk populate form saat edit
    function populateForm(data) {
        $('#urut_peng').val(data.urut_peng);
        $('#pejabat').val(data.pejabat);
        $('#tgl_sk').val(data.tgl_sk);
        $('#no_sk').val(data.no_sk);
        $('#bentuk').val(data.bentuk);
        $('#event').val(data.event);
        $('#ket').val(data.ket);
    }

    // Function untuk submit form
    function submitForm() {
        const form = $('#penghargaanForm');
        const formData = new FormData(form[0]);
        
        let url;
        if (currentMode === 'add') {
            url = "{{ route('admin.karyawan.penghargaan.store', $karyawan->kd_karyawan) }}";
        } else {
            const kdKaryawan = "{{ $karyawan->kd_karyawan }}";
            const urut = $('#urut_peng').val();
            url = "{{ route('admin.karyawan.penghargaan.update', [':kd_karyawan', ':urut']) }}"
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
                    $('#penghargaanModal').modal('hide');
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

    // Function untuk edit penghargaan
    function editPenghargaan(kdKaryawan, urut) {
        const url = "{{ route('admin.karyawan.penghargaan.edit', [':kd_karyawan', ':urut']) }}"
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

    // Function untuk delete penghargaan
    function deletePenghargaan(kdKaryawan, urut, event) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus penghargaan <strong>"${event}"</strong>?<br><br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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

                const url = "{{ route('admin.karyawan.penghargaan.destroy', [':kd_karyawan', ':urut']) }}"
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
