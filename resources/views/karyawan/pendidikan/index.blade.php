@extends('layouts.backend', ['title' => 'Riwayat Pendidikan'])

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
                            Riwayat Pendidikan
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
                            <h3 class="fw-bold m-0">Riwayat Pendidikan</h3>
                        </div>

                        <a href="javascript:;" id="add-pendidikan"
                            class="btn btn-sm btn-primary align-self-center">
                            Tambah Pendidikan
                        </a>
                    </div>
        
                    <div class="card-body p-9">
                        <div id="kt_pendidikan_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <table id="pendidikan-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                                    <thead>
                                        <tr>
                                            <th class="">No.</th>
                                            <th class="">Jenjang & Jurusan</th>
                                            <th class="">Lembaga & Tempat</th>
                                            <th class="">Tahun Lulus</th>
                                            <th class="">No. Ijazah</th>
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

    <!-- Modal Pendidikan (Add/Edit) -->
    <div class="modal fade" id="pendidikanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog mw-550px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title" id="modalTitle">Tambah Pendidikan</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form method="POST" class="form" id="pendidikanForm">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="kd_karyawan" id="kd_karyawan" value="{{ $karyawan->kd_karyawan }}">
                    <input type="hidden" name="urut" id="urut_didik">
                    
                    <div class="modal-body">
                        <!-- Jenjang Pendidikan -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Jenjang Pendidikan <span class="text-danger">*</span></label>
                            <select class="form-control form-control-solid" data-control="select2" 
                                    data-placeholder="Pilih jenjang pendidikan" data-allow-clear="true" 
                                    name="kd_jenjang_didik" id="kd_jenjang_didik">
                                <option></option>
                                @foreach($jenjangPendidikan as $jenjang)
                                    <option value="{{ $jenjang->kd_jenjang_didik }}" data-grup="{{ $jenjang->grup_jurusan ?? '' }}">
                                        {{ $jenjang->jenjang_didik }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="fv-plugins-message-container invalid-feedback error-text kd_jenjang_didik_error"></div>
                        </div>

                        <!-- Jurusan -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Jurusan</label>
                            <select class="form-control form-control-solid" data-control="select2" 
                                    data-placeholder="Pilih jurusan" data-allow-clear="true" 
                                    name="kd_jurusan" id="kd_jurusan">
                                <option></option>
                            </select>
                            <div class="fv-plugins-message-container invalid-feedback error-text kd_jurusan_error"></div>
                        </div>

                        <!-- Nama Lembaga -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Nama Lembaga <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-solid" name="nama_lembaga" id="nama_lembaga" placeholder="Nama Lembaga" />
                            <div class="fv-plugins-message-container invalid-feedback error-text nama_lembaga_error"></div>
                        </div>

                        <!-- Tempat -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Tempat</label>
                            <input type="text" class="form-control form-control-solid" name="tempat" id="tempat" placeholder="Tempat" />
                            <div class="fv-plugins-message-container invalid-feedback error-text tempat_error"></div>
                        </div>

                        <!-- Tahun Lulus -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">Tahun Lulus <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-solid" name="tahun_lulus" id="tahun_lulus" placeholder="Tahun Lulus" maxlength="4" />
                            <div class="fv-plugins-message-container invalid-feedback error-text tahun_lulus_error"></div>
                        </div>

                        <!-- No Ijazah -->
                        <div class="d-flex flex-column mb-3">
                            <label class="fw-semibold fs-6 mb-2">No. Ijazah</label>
                            <input type="text" class="form-control form-control-solid" name="no_ijazah" id="no_ijazah" placeholder="No. Ijazah" />
                            <div class="fv-plugins-message-container invalid-feedback error-text no_ijazah_error"></div>
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
        table = $('#pendidikan-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.karyawan.pendidikan.get-pendidikan-data', $karyawan->kd_karyawan) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenjang_jurusan', name: 'jenjang_jurusan' },
                { data: 'lembaga_tempat', name: 'lembaga_tempat' },
                { data: 'tahun_lulus', name: 'tahun_lulus' },
                { data: 'no_ijazah', name: 'no_ijazah' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        table.on('draw', function() {
            KTMenu.createInstances();
        });

        // Initialize Select2
        $('#kd_jenjang_didik, #kd_jurusan').select2({
            dropdownParent: $('#pendidikanModal')
        });

        // Tombol Tambah Pendidikan
        $('#add-pendidikan').on('click', function() {
            openModal('add');
        });

        // Handle jenjang pendidikan change
        $('#kd_jenjang_didik').on('change', function() {
            const grupJurusan = $(this).find(':selected').data('grup');
            loadJurusan(grupJurusan);
        });

        // Handle form submit
        $('#pendidikanForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Handle tombol edit
        $(document).on('click', '.edit', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            editPendidikan(kdKaryawan, urut);
        });

        // Handle tombol delete
        $(document).on('click', '.delete-pendidikan', function(e) {
            e.preventDefault();
            const kdKaryawan = $(this).data('karyawan');
            const urut = $(this).data('urut');
            const lembaga = $(this).data('lembaga');
            deletePendidikan(kdKaryawan, urut, lembaga);
        });

        // Handle modal hide event untuk reset form
        $('#pendidikanModal').on('hidden.bs.modal', function() {
            resetForm();
        });
    });

    // Function untuk membuka modal
    function openModal(mode, data = null) {
        currentMode = mode;
        
        if (mode === 'add') {
            $('#modalTitle').text('Tambah Pendidikan');
            $('#form_method').val('POST');
            resetForm();
        } else {
            $('#modalTitle').text('Edit Pendidikan');
            $('#form_method').val('PATCH');
            populateForm(data);
        }
        
        $('#pendidikanModal').modal('show');
    }

    // Function untuk reset form
    function resetForm() {
        $('#pendidikanForm')[0].reset();
        $('#urut_didik').val('');
        
        // Reset select2
        $('#kd_jenjang_didik').val('').trigger('change');
        $('#kd_jurusan').val('').trigger('change');
        
        // Clear error messages
        $('.error-text').text('');
        
        // Reset submit button
        $('.submit-btn').attr('disabled', false);
        $('.submit-btn .indicator-label').show();
        $('.submit-btn .indicator-progress').hide();
    }

    // Function untuk populate form saat edit
    function populateForm(data) {
        $('#urut_didik').val(data.urutan_didik);
        $('#kd_jenjang_didik').val(data.kd_jenjang_didik).trigger('change');
        $('#nama_lembaga').val(data.nama_lembaga);
        $('#tempat').val(data.tempat);
        $('#tahun_lulus').val(data.tahun_lulus);
        $('#no_ijazah').val(data.no_ijazah);
        
        // Load jurusan berdasarkan grup dan set value
        if (data.grup_jurusan) {
            loadJurusan(data.grup_jurusan, data.kd_jurusan);
        }
    }

    // Function untuk load jurusan
    function loadJurusan(grupJurusan, selectedValue = null) {
        if (!grupJurusan) {
            $('#kd_jurusan').empty().append('<option value="">Pilih jenjang pendidikan terlebih dahulu</option>');
            return;
        }

        const url = "{{ route('admin.karyawan.jurusan', ':grup_jurusan') }}".replace(':grup_jurusan', grupJurusan);
        
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            beforeSend: function() {
                $('#kd_jurusan').empty().append('<option value="">Loading...</option>');
            },
            success: function(data) {
                $('#kd_jurusan').empty().append('<option value="">Pilih jurusan</option>');
                $.each(data, function(key, value) {
                    const selected = (selectedValue && selectedValue == value.kd_jurusan) ? 'selected' : '';
                    $('#kd_jurusan').append(`<option value="${value.kd_jurusan}" ${selected}>${value.jurusan}</option>`);
                });
                
                // Trigger change untuk update select2
                $('#kd_jurusan').trigger('change');
            },
            error: function(xhr) {
                console.error('Error loading jurusan:', xhr);
                toastr.error('Gagal memuat data jurusan', 'Error');
                $('#kd_jurusan').empty().append('<option value="">Error loading data</option>');
            }
        });
    }

    // Function untuk submit form
    function submitForm() {
        const form = $('#pendidikanForm');
        const formData = new FormData(form[0]);
        
        let url;
        if (currentMode === 'add') {
            url = "{{ route('admin.karyawan.pendidikan.store', $karyawan->kd_karyawan) }}";
        } else {
            const kdKaryawan = $('#kd_karyawan').val();
            const urut = $('#urut_didik').val();
            url = "{{ route('admin.karyawan.pendidikan.update', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
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
                    $('#pendidikanModal').modal('hide');
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

    // Function untuk edit pendidikan
    function editPendidikan(kdKaryawan, urut) {
        const url = "{{ route('admin.karyawan.pendidikan.edit', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
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

    // Function untuk delete pendidikan dengan SweetAlert
    function deletePendidikan(kdKaryawan, urut, lembaga) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus pendidikan dari <strong>"${lembaga}"</strong>?<br><br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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

                const url = "{{ route('admin.karyawan.pendidikan.destroy', ['id' => ':kd_karyawan', 'urut' => ':urut']) }}"
                    .replace(':kd_karyawan', kdKaryawan)
                    .replace(':urut', urut);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': $('meta[name="csrf-token"]').attr('content')
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
                    }
                });
            }
        });
    }
</script>
@endpush