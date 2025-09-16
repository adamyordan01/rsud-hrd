@extends('layouts.backend', ['title' => 'Seminar'])

@inject('carbon', 'Carbon\Carbon')

@push('styles')
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Seminar
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
                        <li class="breadcrumb-item text-muted">Seminar</li>
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
                            <h3 class="fw-bold m-0">Riwayat Seminar</h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-primary" id="add-seminar">
                                <i class="ki-outline ki-plus fs-3"></i>
                                Tambah Seminar
                            </button>
                        </div>
                    </div>
        
                    <div class="card-body p-9">
                        <div class="table-responsive">
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="seminar-table">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>No</th>
                                        <th>Periode</th>
                                        <th>Nama Seminar & Penyelenggara</th>
                                        <th>Sertifikat & Sumber Dana</th>
                                        <th>Jam & Tahun</th>
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

    <!-- Modal Seminar (Add/Edit) -->
    <div class="modal fade" id="seminarModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-850px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah Seminar</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="seminarForm">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="kd_karyawan" id="kd_karyawan" value="{{ $karyawan->kd_karyawan }}">
                    <input type="hidden" name="urut_seminar" id="urut_seminar">
                    
                    <div class="modal-body">
                        <div class="row">
                            <!-- Nama Seminar -->
                            <div class="col-md-12 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nama Seminar</label>
                                <input type="text" class="form-control form-control-solid" name="nama_seminar" id="nama_seminar" placeholder="Masukkan nama seminar">
                                <div class="text-danger error-text nama_seminar_error"></div>
                            </div>

                            <!-- Penyelenggara -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Penyelenggara</label>
                                <input type="text" class="form-control form-control-solid" name="penyelenggara" id="penyelenggara" placeholder="Masukkan nama penyelenggara">
                                <div class="text-danger error-text penyelenggara_error"></div>
                            </div>

                            <!-- Sumber Dana -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Sumber Dana</label>
                                <select class="form-select form-select-solid" name="kd_sumber_dana" id="kd_sumber_dana">
                                    <option value="">Pilih Sumber Dana</option>
                                    @foreach($sumber_dana as $dana)
                                        <option value="{{ $dana->kd_sumber_dana }}">{{ $dana->sumber_dana }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger error-text kd_sumber_dana_error"></div>
                            </div>

                            <!-- Tanggal Mulai -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Tanggal Mulai</label>
                                <input type="date" class="form-control form-control-solid" name="tgl_mulai" id="tgl_mulai">
                                <div class="text-danger error-text tgl_mulai_error"></div>
                            </div>

                            <!-- Tanggal Akhir -->
                            <div class="col-md-6 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Tanggal Akhir</label>
                                <input type="date" class="form-control form-control-solid" name="tgl_akhir" id="tgl_akhir">
                                <div class="text-danger error-text tgl_akhir_error"></div>
                            </div>

                            <!-- No. Sertifikat -->
                            <div class="col-md-6 mb-7">
                                <label class="fw-semibold fs-6 mb-2">No. Sertifikat</label>
                                <input type="text" class="form-control form-control-solid" name="no_sertifikat" id="no_sertifikat" placeholder="Masukkan nomor sertifikat (opsional)">
                                <div class="text-danger error-text no_sertifikat_error"></div>
                            </div>

                            <!-- Jumlah Jam -->
                            <div class="col-md-3 mb-7">
                                <label class="fw-semibold fs-6 mb-2">Jumlah Jam</label>
                                <input type="number" class="form-control form-control-solid" name="jml_jam" id="jml_jam" placeholder="0" min="0" step="0.5">
                                <div class="text-danger error-text jml_jam_error"></div>
                            </div>

                            <!-- Tahun -->
                            <div class="col-md-3 mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Tahun</label>
                                <input type="number" class="form-control form-control-solid" name="tahun" id="tahun" placeholder="2024" min="1900" max="2100">
                                <div class="text-danger error-text tahun_error"></div>
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
        // Set tahun default ke tahun sekarang
        $('#tahun').val(new Date().getFullYear());
        
        // Initialize DataTable
        table = $('#seminar-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.karyawan.seminar.data', $karyawan->kd_karyawan) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'periode', name: 'periode' },
                { data: 'nama_penyelenggara', name: 'nama_penyelenggara' },
                { data: 'sertifikat_dana', name: 'sertifikat_dana' },
                { data: 'jam_tahun', name: 'jam_tahun' },
                { data: 'ket', name: 'ket' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            language: {
                emptyTable: "Belum ada data seminar",
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

        // Tombol Tambah Seminar
        $('#add-seminar').on('click', function() {
            openModal('add');
        });

        // Handle form submit
        $('#seminarForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Handle tombol edit
        $(document).on('click', '.edit', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            editSeminar(kdKaryawan, urut);
        });

        // Handle tombol delete
        $(document).on('click', '.delete-seminar', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            const namaSeminar = $(this).data('seminar');
            deleteSeminar(kdKaryawan, urut, namaSeminar);
        });

        // Handle modal hide event untuk reset form
        $('#seminarModal').on('hidden.bs.modal', function() {
            resetForm();
        });

        // Auto update tahun based on tanggal mulai
        $('#tgl_mulai').on('change', function() {
            if (this.value) {
                const year = new Date(this.value).getFullYear();
                $('#tahun').val(year);
            }
        });
    });

    // Function untuk membuka modal
    function openModal(mode, data = null) {
        currentMode = mode;
        
        if (mode === 'add') {
            $('#modalTitle').text('Tambah Seminar');
            $('#form_method').val('POST');
            resetForm();
        } else {
            $('#modalTitle').text('Edit Seminar');
            $('#form_method').val('PUT');
            populateForm(data);
        }
        
        $('#seminarModal').modal('show');
    }

    // Function untuk reset form
    function resetForm() {
        $('#seminarForm')[0].reset();
        $('#urut_seminar').val('');
        $('#tahun').val(new Date().getFullYear());
        
        // Clear error messages
        $('.error-text').text('');
        
        // Reset submit button
        $('.submit-btn').attr('disabled', false);
        $('.submit-btn .indicator-label').show();
        $('.submit-btn .indicator-progress').hide();
    }

    // Function untuk populate form saat edit
    function populateForm(data) {
        $('#urut_seminar').val(data.urut_seminar);
        $('#kd_sumber_dana').val(data.kd_sumber_dana);
        $('#nama_seminar').val(data.nama_seminar);
        $('#no_sertifikat').val(data.no_sertifikat);
        $('#tgl_mulai').val(data.tgl_mulai);
        $('#tgl_akhir').val(data.tgl_akhir);
        $('#jml_jam').val(data.jml_jam);
        $('#penyelenggara').val(data.penyelenggara);
        $('#ket').val(data.ket);
        $('#tahun').val(data.tahun);
    }

    // Function untuk submit form
    function submitForm() {
        const form = $('#seminarForm');
        const formData = new FormData(form[0]);
        
        let url;
        if (currentMode === 'add') {
            url = "{{ route('admin.karyawan.seminar.store', $karyawan->kd_karyawan) }}";
        } else {
            const kdKaryawan = "{{ $karyawan->kd_karyawan }}";
            const urut = $('#urut_seminar').val();
            url = "{{ route('admin.karyawan.seminar.update', [':kd_karyawan', ':urut']) }}"
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
                    $('#seminarModal').modal('hide');
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

    // Function untuk edit seminar
    function editSeminar(kdKaryawan, urut) {
        const url = "{{ route('admin.karyawan.seminar.edit', [':kd_karyawan', ':urut']) }}"
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

    // Function untuk delete seminar
    function deleteSeminar(kdKaryawan, urut, namaSeminar) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus seminar <strong>"${namaSeminar}"</strong>?<br><br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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

                const url = "{{ route('admin.karyawan.seminar.destroy', [':kd_karyawan', ':urut']) }}"
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
