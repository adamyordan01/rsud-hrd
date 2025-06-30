@extends('layouts.backend', ['title' => 'Surat Izin'])

@push('styles')
<style>
    #surat-izin-table th {
        font-size: 12px;
        vertical-align: middle;
    }
    
    #surat-izin-table td {
        font-size: 12px;
        vertical-align: top;
    }
    
    .filter-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Surat Izin & Cuti
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Surat Izin</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button class="btn btn-flex btn-primary h-40px fs-7 fw-bold" id="tambah-surat" data-bs-toggle="modal" data-bs-target="#form-surat">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Buat Surat
                </button>
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
                        <input type="text" data-kt-surat-table-filter="search" 
                               class="form-control form-control-solid w-250px ps-12" 
                               placeholder="Cari surat...">
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-surat-table-toolbar="base">
                        <button type="button" class="btn btn-light-primary me-3" data-kt-surat-table-filter="reset">
                            <i class="ki-duotone ki-arrow-right fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table id="surat-izin-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">No</th>
                                <th class="min-w-125px">Jenis Surat</th>
                                <th class="min-w-100px text-center">Tanggal</th>
                                <th class="min-w-150px">Kategori</th>
                                <th class="min-w-200px">Alasan</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Form --}}
<div class="modal fade" id="form-surat" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="data-surat" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Buat Surat Izin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Jenis Surat</label>
                        <div class="col-lg-8">
                            <select class="form-select form-select-solid" name="jenis_surat" id="jenis_surat" data-control="select2" data-dropdown-parent="#form-surat" data-placeholder="Pilih jenis surat" required>
                                <option value="">== PILIH ==</option>
                                @foreach($jenisSurat as $jenis)
                                    <option value="{{ $jenis->kd_jenis_surat }}">{{ $jenis->jenis_surat }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="jenis_surat-error"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Kategori</label>
                        <div class="col-lg-8">
                            <select class="form-select form-select-solid" name="kategori_izin" id="kategori_izin" data-control="select2" data-dropdown-parent="#form-surat" data-placeholder="Pilih kategori izin" required>
                                <option value="">== PILIH ==</option>
                            </select>
                            <div class="invalid-feedback" id="kategori_izin-error"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Tanggal Mulai</label>
                        <div class="col-lg-8">
                            <input type="date" class="form-control form-control-solid" name="tgl_mulai" id="tgl_mulai" required>
                            <div class="invalid-feedback" id="tgl_mulai-error"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Tanggal Akhir</label>
                        <div class="col-lg-8">
                            <input type="date" class="form-control form-control-solid" name="tgl_selesai" id="tgl_selesai" required>
                            <div class="invalid-feedback" id="tgl_selesai-error"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Alasan</label>
                        <div class="col-lg-8">
                            <textarea class="form-control form-control-solid" rows="4" placeholder="Masukkan alasan izin/cuti" name="alasan" id="alasan" required></textarea>
                            <div class="invalid-feedback" id="alasan-error"></div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="aksi" id="aksi-surat" value="add">
                    <input type="hidden" name="kd_surat" id="kd_surat">
                    <input type="hidden" name="_method" id="method" value="POST">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="add-surat">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">
                            Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
    "use strict";

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table;

    // Initialize DataTable
    function initDataTable() {
        table = $('#surat-izin-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.karyawan.surat-izin.index') }}",
                type: 'GET'
            },
            columns: [
                { data: 'nomor', name: 'nomor', orderable: false, searchable: false },
                { data: 'jenis_surat', name: 'jenisSurat.jenis_surat' },
                { data: 'tanggal', name: 'tgl_mulai' },
                { data: 'kategori', name: 'kategoriIzin.kategori' },
                { data: 'alasan', name: 'alasan' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: -1,
                    className: 'text-end'
                },
                {
                    targets: [2, 5],
                    className: 'text-center'
                }
            ],
            order: [[2, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                processing: "Memuat...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang ditemukan",
                emptyTable: "Tidak ada data tersedia",
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
    }

    // Edit surat function
    function editSurat(id) {
        $.ajax({
            url: "{{ route('admin.karyawan.surat-izin.edit', ':id') }}".replace(':id', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#modalTitle').text('Edit Surat Izin');
                    $('#aksi-surat').val('edit');
                    $('#kd_surat').val(response.data.kd_surat);
                    $('#method').val('PUT');
                    
                    $('#jenis_surat').val(response.data.jenis_surat).trigger('change');
                    $('#tgl_mulai').val(response.data.tgl_mulai);
                    $('#tgl_selesai').val(response.data.tgl_selesai);
                    $('#alasan').val(response.data.alasan);
                    
                    // Wait for kategori to load then set value
                    setTimeout(function() {
                        $('#kategori_izin').val(response.data.kategori_izin).trigger('change');
                    }, 500);
                    
                    $('#form-surat').modal('show');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
            }
        });
    }

    // Delete surat function
    function deleteSurat(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data surat izin akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.karyawan.surat-izin.destroy', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Dihapus!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            }
        });
    }

    // Print surat function
    function printSurat(id) {
        window.open("{{ route('admin.karyawan.surat-izin.print', ':id') }}".replace(':id', id), '_blank');
    }

    $(document).ready(function() {
        // Initialize DataTable
        initDataTable();
        
        // Reset form when modal closed
        $('#form-surat').on('hidden.bs.modal', function() {
            resetForm();
        });
        
        // When adding new surat
        $('#tambah-surat').click(function() {
            resetForm();
            $('#modalTitle').text('Buat Surat Izin');
            $('#aksi-surat').val('add');
            $('#method').val('POST');
        });
        
        // When Jenis Surat selected
        $('#jenis_surat').change(function(){
            var kd_jenis_surat = $(this).val();
            
            if(kd_jenis_surat != ''){
                $('#kategori_izin').html('<option>Loading...</option>');
                $.ajax({
                    url: '{{ route("admin.karyawan.surat-izin.get-kategori") }}',
                    type: 'POST',
                    data: { 
                        kd_jenis_surat: kd_jenis_surat,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $('#kategori_izin').html('<option>Loading...</option>');
                    },
                    success: function(response){
                        $('#kategori_izin').html(response);
                        $('#kategori_izin').trigger('change');
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status + " - " + error);
                        $('#kategori_izin').html('<option value="">== PILIH ==</option>');
                    }
                });
            } else {
                $('#kategori_izin').html('<option value="">== PILIH ==</option>');
                $('#kategori_izin').trigger('change');
            }
        });
        
        // Auto uppercase for alasan
        $('#alasan').on('input', function() {
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
        });
        
        // Search functionality
        var searchTimer;
        $('[data-kt-surat-table-filter="search"]').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                table.search(this.value).draw();
            }.bind(this), 500);
        });
        
        // Reset filter
        $('[data-kt-surat-table-filter="reset"]').on('click', function() {
            $('[data-kt-surat-table-filter="search"]').val('');
            table.search('').draw();
        });
        
        // Form submission
        $('#data-surat').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            var url = '';
            // var method = $('#method').val();
            
            if ($('#aksi-surat').val() === 'edit') {
                url = "{{ route('admin.karyawan.surat-izin.update', ':id') }}".replace(':id', $('#kd_surat').val());
                // Tambahkan method spoofing
                formData.append('_method', 'PUT');
            } else {
                url = "{{ route('admin.karyawan.surat-izin.store') }}";
            }

            // Debugging: Log isi FormData
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Show loading state
            $('#add-surat').attr('data-kt-indicator', 'on');
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: url,
                // type: method,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#add-surat').removeAttr('data-kt-indicator');
                    
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#form-surat').modal('hide');
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    $('#add-surat').removeAttr('data-kt-indicator');
                    
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '-error').text(messages[0]);
                        });
                    } else {
                        Swal.fire('Error!', 'Terjadi kesalahan pada server', 'error');
                    }
                }
            });
        });
    });

    function resetForm() {
        $('#data-surat')[0].reset();
        $('#kategori_izin').html('<option value="">== PILIH ==</option>');
        $('#kd_surat').val('');
        $('#method').val('POST');
        
        // Clear validation states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Trigger select2 change event
        $('#jenis_surat').trigger('change');
        $('#kategori_izin').trigger('change');
    }

    // Make functions globally available
    window.editSurat = editSurat;
    window.deleteSurat = deleteSurat;
    window.printSurat = printSurat;
    </script>
@endpush