@extends('layouts.backend', ['title' => $pageTitle])

@push('styles')
<style>
    .wilayah-card {
        margin-bottom: 20px;
        border: 1px solid #e4e6ef;
        border-radius: 10px;
    }
    .wilayah-header {
        background: linear-gradient(90deg, #1e1e2e 0%, #2a2d3a 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 10px 10px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .wilayah-header h5 {
        margin: 0;
        font-weight: 600;
    }
    .wilayah-content {
        padding: 20px;
    }
    .level-item {
        border-left: 3px solid #009ef7;
        padding-left: 15px;
        margin-bottom: 15px;
    }
    .level-2 { border-left-color: #50cd89; }
    .level-3 { border-left-color: #ffc700; }
    .level-4 { border-left-color: #f1416c; }
    
    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .item-title {
        font-weight: 600;
        color: #181c32;
    }
    .item-actions {
        display: flex;
        gap: 5px;
    }
    .sub-items {
        margin-left: 20px;
        margin-top: 10px;
    }
    .nested-item {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .status-badge {
        font-size: 11px;
        padding: 3px 8px;
    }
    .no-data {
        color: #a1a5b7;
        font-style: italic;
        text-align: center;
        padding: 20px;
    }
</style>
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Master Data
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Master Wilayah</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="javascript:;" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" id="add-provinsi">
                        <i class="ki-outline ki-plus fs-2"></i>
                        Tambah Provinsi
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        @if($provinsi->count() > 0)
            @foreach($provinsi as $prov)
                <div class="wilayah-card">
                    <div class="wilayah-header">
                        <h5>
                            <i class="ki-outline ki-geolocation fs-2 me-2"></i>
                            {{ $prov->propinsi }}
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-light-success" onclick="addKabupaten({{ $prov->kd_propinsi }}, '{{ $prov->propinsi }}')">
                                <i class="ki-outline ki-plus fs-6"></i> Kabupaten
                            </button>
                            <button class="btn btn-sm btn-light-warning edit-provinsi" data-id="{{ $prov->kd_propinsi }}">
                                <i class="ki-outline ki-pencil fs-6"></i>
                            </button>
                            <button class="btn btn-sm btn-light-danger delete-provinsi" data-id="{{ $prov->kd_propinsi }}">
                                <i class="ki-outline ki-trash fs-6"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="wilayah-content">
                        @if($prov->kabupaten->count() > 0)
                            @foreach($prov->kabupaten as $kab)
                                <div class="level-item level-2">
                                    <div class="item-header">
                                        <span class="item-title">{{ $kab->kabupaten }}</span>
                                        <div class="item-actions">
                                            <button class="btn btn-sm btn-light-warning" onclick="addKecamatan({{ $kab->kd_kabupaten }}, '{{ $kab->kabupaten }}')">
                                                <i class="ki-outline ki-plus fs-7"></i> Kecamatan
                                            </button>
                                            <button class="btn btn-sm btn-light-primary edit-kabupaten" 
                                                    data-provinsi-id="{{ $prov->kd_propinsi }}" 
                                                    data-id="{{ $kab->kd_kabupaten }}">
                                                <i class="ki-outline ki-pencil fs-7"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light-danger delete-kabupaten" 
                                                    data-provinsi-id="{{ $prov->kd_propinsi }}" 
                                                    data-id="{{ $kab->kd_kabupaten }}">
                                                <i class="ki-outline ki-trash fs-7"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    @if(isset($kab->kecamatan) && $kab->kecamatan->count() > 0)
                                        <div class="sub-items">
                                            @foreach($kab->kecamatan as $kec)
                                                <div class="level-item level-3">
                                                    <div class="item-header">
                                                        <span class="item-title">{{ $kec->kecamatan }}</span>
                                                        <div class="item-actions">
                                                            <button class="btn btn-sm btn-light-danger" onclick="addKelurahan({{ $kec->kd_kecamatan }}, '{{ $kec->kecamatan }}')">
                                                                <i class="ki-outline ki-plus fs-7"></i> Kelurahan
                                                            </button>
                                                            <button class="btn btn-sm btn-light-primary edit-kecamatan" 
                                                                    data-kabupaten-id="{{ $kab->kd_kabupaten }}" 
                                                                    data-id="{{ $kec->kd_kecamatan }}">
                                                                <i class="ki-outline ki-pencil fs-7"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-light-danger delete-kecamatan" 
                                                                    data-kabupaten-id="{{ $kab->kd_kabupaten }}" 
                                                                    data-id="{{ $kec->kd_kecamatan }}">
                                                                <i class="ki-outline ki-trash fs-7"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    @if(isset($kec->kelurahan) && $kec->kelurahan->count() > 0)
                                                        <div class="sub-items">
                                                            @foreach($kec->kelurahan as $kel)
                                                                <div class="nested-item level-4">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <span class="item-title">{{ $kel->kelurahan }}</span>
                                                                            @if($kel->kode_pos)
                                                                                <span class="badge badge-light-info ms-2">{{ $kel->kode_pos }}</span>
                                                                            @endif
                                                                            <span class="badge status-badge {{ $kel->aktif ? 'badge-light-success' : 'badge-light-danger' }} ms-1">
                                                                                {{ $kel->aktif ? 'Aktif' : 'Tidak Aktif' }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="item-actions">
                                                                            <button class="btn btn-sm btn-light-primary edit-kelurahan" 
                                                                                    data-kecamatan-id="{{ $kec->kd_kecamatan }}" 
                                                                                    data-id="{{ $kel->kd_kelurahan }}">
                                                                                <i class="ki-outline ki-pencil fs-7"></i>
                                                                            </button>
                                                                            <button class="btn btn-sm btn-light-danger delete-kelurahan" 
                                                                                    data-kecamatan-id="{{ $kec->kd_kecamatan }}" 
                                                                                    data-id="{{ $kel->kd_kelurahan }}">
                                                                                <i class="ki-outline ki-trash fs-7"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="sub-items">
                                                            <div class="no-data">Belum ada kelurahan</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="sub-items">
                                            <div class="no-data">Belum ada kecamatan</div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="no-data">Belum ada kabupaten</div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="card">
                <div class="card-body text-center py-20">
                    <div class="mb-10">
                        <i class="ki-outline ki-geolocation fs-5x text-muted"></i>
                    </div>
                    <h3 class="text-gray-800 fw-bold mb-3">Belum Ada Data Provinsi</h3>
                    <p class="text-gray-600 mb-8">Mulai dengan menambahkan provinsi pertama untuk sistem wilayah Anda.</p>
                    <button class="btn btn-primary" id="add-provinsi-empty">
                        <i class="ki-outline ki-plus fs-2"></i>
                        Tambah Provinsi
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modals -->
@include('settings.wilayah.modals._provinsi')
@include('settings.wilayah.modals._kabupaten')
@include('settings.wilayah.modals._kecamatan')
@include('settings.wilayah.modals._kelurahan')

@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        // Event handlers for add buttons
        $('#add-provinsi, #add-provinsi-empty').on('click', function() {
            openProvinsiModal();
        });

        // Event handlers for edit buttons
        $('.edit-provinsi').on('click', function() {
            var id = $(this).data('id');
            editProvinsi(id);
        });

        $('.edit-kabupaten').on('click', function() {
            var provinsiId = $(this).data('provinsi-id');
            var id = $(this).data('id');
            editKabupaten(provinsiId, id);
        });

        $('.edit-kecamatan').on('click', function() {
            var kabupatenId = $(this).data('kabupaten-id');
            var id = $(this).data('id');
            editKecamatan(kabupatenId, id);
        });

        $('.edit-kelurahan').on('click', function() {
            var kecamatanId = $(this).data('kecamatan-id');
            var id = $(this).data('id');
            editKelurahan(kecamatanId, id);
        });

        // Event handlers for delete buttons
        $('.delete-provinsi').on('click', function() {
            var id = $(this).data('id');
            deleteProvinsi(id);
        });

        $('.delete-kabupaten').on('click', function() {
            var provinsiId = $(this).data('provinsi-id');
            var id = $(this).data('id');
            deleteKabupaten(provinsiId, id);
        });

        $('.delete-kecamatan').on('click', function() {
            var kabupatenId = $(this).data('kabupaten-id');
            var id = $(this).data('id');
            deleteKecamatan(kabupatenId, id);
        });

        $('.delete-kelurahan').on('click', function() {
            var kecamatanId = $(this).data('kecamatan-id');
            var id = $(this).data('id');
            deleteKelurahan(kecamatanId, id);
        });
    });

    // Functions to handle modals and CRUD operations
    function openProvinsiModal() {
        $('#modalProvinsiTitle').text('Tambah Provinsi');
        $('#formProvinsiMethod').val('POST');
        $('#provinsiForm')[0].reset();
        $('.error-text').text('');
        $('#provinsiModal').modal('show');
    }

    function addKabupaten(provinsiId, provinsiName) {
        $('#modalKabupatenTitle').text('Tambah Kabupaten - ' + provinsiName);
        $('#formKabupatenMethod').val('POST');
        $('#kabupaten_provinsi_id').val(provinsiId);
        $('#kabupatenForm')[0].reset();
        $('.error-text').text('');
        $('#kabupatenModal').modal('show');
    }

    function addKecamatan(kabupatenId, kabupatenName) {
        $('#modalKecamatanTitle').text('Tambah Kecamatan - ' + kabupatenName);
        $('#formKecamatanMethod').val('POST');
        $('#kecamatan_kabupaten_id').val(kabupatenId);
        $('#kecamatanForm')[0].reset();
        $('.error-text').text('');
        $('#kecamatanModal').modal('show');
    }

    function addKelurahan(kecamatanId, kecamatanName) {
        $('#modalKelurahanTitle').text('Tambah Kelurahan - ' + kecamatanName);
        $('#formKelurahanMethod').val('POST');
        $('#kelurahan_kecamatan_id').val(kecamatanId);
        $('#kelurahanForm')[0].reset();
        $('.error-text').text('');
        $('#kelurahanModal').modal('show');
    }

    function editProvinsi(id) {
        $.ajax({
            url: '{{ route("admin.settings.wilayah.provinsi.edit", ":id") }}'.replace(':id', id),
            type: 'GET',
            success: function(response) {
                $('#modalProvinsiTitle').text('Edit Provinsi');
                $('#formProvinsiMethod').val('PATCH');
                $('#kd_provinsi_hidden').val(response.data.kd_propinsi);
                $('#propinsi').val(response.data.propinsi);
                $('.error-text').text('');
                $('#provinsiModal').modal('show');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Gagal mengambil data.');
            }
        });
    }

    function editKabupaten(provinsiId, id) {
        $.ajax({
            url: '{{ route("admin.settings.wilayah.kabupaten.edit", [":provinsiId", ":id"]) }}'.replace(':provinsiId', provinsiId).replace(':id', id),
            type: 'GET',
            success: function(response) {
                $('#modalKabupatenTitle').text('Edit Kabupaten');
                $('#formKabupatenMethod').val('PATCH');
                $('#kabupaten_provinsi_id').val(response.data.kd_propinsi);
                $('#kd_kabupaten_hidden').val(response.data.kd_kabupaten);
                $('#kabupaten').val(response.data.kabupaten);
                $('.error-text').text('');
                $('#kabupatenModal').modal('show');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Gagal mengambil data.');
            }
        });
    }

    function editKecamatan(kabupatenId, id) {
        $.ajax({
            url: '{{ route("admin.settings.wilayah.kecamatan.edit", [":kabupatenId", ":id"]) }}'.replace(':kabupatenId', kabupatenId).replace(':id', id),
            type: 'GET',
            success: function(response) {
                $('#modalKecamatanTitle').text('Edit Kecamatan');
                $('#formKecamatanMethod').val('PATCH');
                $('#kecamatan_kabupaten_id').val(response.data.kd_kabupaten);
                $('#kd_kecamatan_hidden').val(response.data.kd_kecamatan);
                $('#kecamatan').val(response.data.kecamatan);
                $('.error-text').text('');
                $('#kecamatanModal').modal('show');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Gagal mengambil data.');
            }
        });
    }

    function editKelurahan(kecamatanId, id) {
        $.ajax({
            url: '{{ route("admin.settings.wilayah.kelurahan.edit", [":kecamatanId", ":id"]) }}'.replace(':kecamatanId', kecamatanId).replace(':id', id),
            type: 'GET',
            success: function(response) {
                $('#modalKelurahanTitle').text('Edit Kelurahan');
                $('#formKelurahanMethod').val('PATCH');
                $('#kelurahan_kecamatan_id').val(response.data.kd_kecamatan);
                $('#kd_kelurahan_hidden').val(response.data.kd_kelurahan);
                $('#kelurahan').val(response.data.kelurahan);
                $('.error-text').text('');
                $('#kelurahanModal').modal('show');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Gagal mengambil data.');
            }
        });
    }

    function deleteProvinsi(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data provinsi dan semua wilayah di dalamnya akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.settings.wilayah.provinsi.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Dihapus!', response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    function deleteKabupaten(provinsiId, id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data kabupaten dan semua wilayah di dalamnya akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.settings.wilayah.kabupaten.destroy", [":provinsiId", ":id"]) }}'.replace(':provinsiId', provinsiId).replace(':id', id),
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Dihapus!', response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    function deleteKecamatan(kabupatenId, id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data kecamatan dan semua kelurahan di dalamnya akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.settings.wilayah.kecamatan.destroy", [":kabupatenId", ":id"]) }}'.replace(':kabupatenId', kabupatenId).replace(':id', id),
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Dihapus!', response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    function deleteKelurahan(kecamatanId, id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data kelurahan akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.settings.wilayah.kelurahan.destroy", [":kecamatanId", ":id"]) }}'.replace(':kecamatanId', kecamatanId).replace(':id', id),
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Dihapus!', response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    // Add similar functions for edit and delete other levels
</script>
@endpush
