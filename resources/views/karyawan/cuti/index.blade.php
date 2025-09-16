@extends('layouts.backend', ['title' => 'Riwayat Cuti'])

@inject('carbon', 'Carbon\Carbon')

@push('styles')
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Riwayat Cuti
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
                        <li class="breadcrumb-item text-muted">Riwayat Cuti</li>
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
                            <h3 class="fw-bold m-0">Riwayat Cuti</h3>
                        </div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-primary" id="add-cuti">
                                <i class="ki-duotone ki-plus fs-3"></i>
                                Tambah Riwayat Cuti
                            </button>
                        </div>
                    </div>
        
                    <div class="card-body p-9">
                        <div class="table-responsive">
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="cuti-table">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>No</th>
                                        <th>Jenis Cuti</th>
                                        <th>Periode Cuti</th>
                                        <th>Pejabat & SK</th>
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

    <!-- Modal Cuti (Add/Edit) -->
    <div class="modal fade" id="cutiModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah Riwayat Cuti</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="cutiForm">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="kd_karyawan" id="kd_karyawan" value="{{ $karyawan->kd_karyawan }}">
                    <input type="hidden" name="urut_cuti" id="urut_cuti">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Jenis Cuti</label>
                                    <select class="form-select form-select-solid" name="kd_cuti" id="kd_cuti" data-control="select2" data-placeholder="Pilih Jenis Cuti" data-dropdown-parent="#cutiModal">
                                        <option></option>
                                        <option value="1">Cuti Tahunan</option>
                                        <option value="2">Cuti Besar</option>
                                        <option value="3">Cuti Sakit</option>
                                        <option value="4">Cuti Melahirkan</option>
                                        <option value="5">Cuti Karena Alasan Penting</option>
                                        <option value="6">Cuti di Luar Tanggungan Negara</option>
                                    </select>
                                    <div class="text-danger fs-7 kd_cuti_error error-text"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Pejabat Pemberi</label>
                                    <input type="text" name="pejabat" id="pejabat" class="form-control form-control-solid" placeholder="Nama pejabat pemberi cuti" maxlength="50" />
                                    <div class="text-danger fs-7 pejabat_error error-text"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">No. SK</label>
                                    <input type="text" name="no_sk" id="no_sk" class="form-control form-control-solid" placeholder="Nomor surat keputusan" maxlength="50" />
                                    <div class="text-danger fs-7 no_sk_error error-text"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Tanggal SK</label>
                                    <input type="date" name="tgl_sk" id="tgl_sk" class="form-control form-control-solid" />
                                    <div class="text-danger fs-7 tgl_sk_error error-text"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Lama Hari</label>
                                    <input type="number" name="lama_hari" id="lama_hari" class="form-control form-control-solid" placeholder="Lama cuti dalam hari" min="1" />
                                    <div class="text-danger fs-7 lama_hari_error error-text"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Tanggal Mulai</label>
                                    <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control form-control-solid" />
                                    <div class="text-danger fs-7 tgl_mulai_error error-text"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Tanggal Akhir</label>
                                    <input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control form-control-solid" />
                                    <div class="text-danger fs-7 tgl_akhir_error error-text"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Keterangan</label>
                            <textarea name="ket" id="ket" class="form-control form-control-solid" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
                            <div class="text-danger fs-7 ket_error error-text"></div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary submit-btn">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
        table = $('#cuti-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.karyawan.cuti.data', $karyawan->kd_karyawan) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenis_cuti', name: 'jenis_cuti', orderable: false },
                { data: 'periode_cuti', name: 'periode_cuti', orderable: false },
                { data: 'pejabat_sk', name: 'pejabat_sk', orderable: false },
                { data: 'ket', name: 'ket', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            language: {
                emptyTable: "Belum ada data riwayat cuti",
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

        // Tombol Tambah Cuti
        $('#add-cuti').on('click', function() {
            openModal('add');
        });

        // Handle form submit
        $('#cutiForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Handle tombol edit
        $(document).on('click', '.edit', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            editCuti(kdKaryawan, urut);
        });

        // Handle tombol delete
        $(document).on('click', '.delete-cuti', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            const jenisCuti = $(this).data('jenis');
            deleteCuti(kdKaryawan, urut, jenisCuti);
        });

        // Handle modal hide event untuk reset form
        $('#cutiModal').on('hidden.bs.modal', function() {
            resetForm();
        });

        // Auto calculate lama hari berdasarkan tanggal mulai dan akhir
        $('#tgl_mulai, #tgl_akhir').on('change', function() {
            calculateLamaHari();
        });
    });

    // Function untuk menghitung lama hari otomatis
    function calculateLamaHari() {
        const tglMulai = $('#tgl_mulai').val();
        const tglAkhir = $('#tgl_akhir').val();
        
        if (tglMulai && tglAkhir) {
            const startDate = new Date(tglMulai);
            const endDate = new Date(tglAkhir);
            const timeDiff = endDate.getTime() - startDate.getTime();
            const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 karena include start date
            
            if (dayDiff > 0) {
                $('#lama_hari').val(dayDiff);
            }
        }
    }

    // Function untuk membuka modal
    function openModal(mode, data = null) {
        currentMode = mode;
        
        if (mode === 'add') {
            $('#modalTitle').text('Tambah Riwayat Cuti');
            $('#form_method').val('POST');
            resetForm();
        } else {
            $('#modalTitle').text('Edit Riwayat Cuti');
            $('#form_method').val('PUT');
            populateForm(data);
        }
        
        $('#cutiModal').modal('show');
    }

    // Function untuk reset form
    function resetForm() {
        $('#cutiForm')[0].reset();
        $('#urut_cuti').val('');
        $('#kd_cuti').val(null).trigger('change');
        
        // Clear error messages
        $('.error-text').text('');
        
        // Reset submit button
        $('.submit-btn').attr('disabled', false);
        $('.submit-btn .indicator-label').show();
        $('.submit-btn .indicator-progress').hide();
    }

    // Function untuk populate form saat edit
    function populateForm(data) {
        $('#urut_cuti').val(data.urut_cuti);
        $('#kd_cuti').val(data.kd_cuti).trigger('change');
        $('#pejabat').val(data.pejabat);
        $('#no_sk').val(data.no_sk);
        $('#tgl_sk').val(data.tgl_sk);
        $('#lama_hari').val(data.lama_hari);
        $('#tgl_mulai').val(data.tgl_mulai);
        $('#tgl_akhir').val(data.tgl_akhir);
        $('#ket').val(data.ket);
    }

    // Function untuk submit form
    function submitForm() {
        const form = $('#cutiForm');
        const formData = new FormData(form[0]);
        
        let url;
        if (currentMode === 'add') {
            url = "{{ route('admin.karyawan.cuti.store', $karyawan->kd_karyawan) }}";
        } else {
            const kdKaryawan = "{{ $karyawan->kd_karyawan }}";
            const urut = $('#urut_cuti').val();
            url = "{{ route('admin.karyawan.cuti.update', [':kd_karyawan', ':urut']) }}"
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
                    $('#cutiModal').modal('hide');
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

    // Function untuk edit cuti
    function editCuti(kdKaryawan, urut) {
        const url = "{{ route('admin.karyawan.cuti.edit', [':kd_karyawan', ':urut']) }}"
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

    // Function untuk delete cuti
    function deleteCuti(kdKaryawan, urut, jenisCuti) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus riwayat cuti <strong>"${jenisCuti}"</strong>?<br><br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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

                const url = "{{ route('admin.karyawan.cuti.destroy', [':kd_karyawan', ':urut']) }}"
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
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
