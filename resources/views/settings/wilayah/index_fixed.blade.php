@extends('layouts.backend')

@section('title', $pageTitle)

@push('styles')
<style>
    .wilayah-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    .wilayah-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .wilayah-header {
        background: #f8f9fa;
        padding: 10px 15px;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    .wilayah-content {
        padding: 15px;
        display: none;
    }
    .wilayah-content.show {
        display: block;
    }
    .loading-spinner {
        text-align: center;
        padding: 20px;
    }
    .level-badge {
        font-size: 0.8em;
        padding: 2px 8px;
        border-radius: 12px;
    }
    .provinsi { background: #e3f2fd; color: #1976d2; }
    .kabupaten { background: #f3e5f5; color: #7b1fa2; }
    .kecamatan { background: #e8f5e8; color: #388e3c; }
    .kelurahan { background: #fff3e0; color: #f57c00; }
</style>
@endpush

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">{{ $pageTitle }}</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Settings</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Master Wilayah</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#provinsiModal" onclick="clearProvinsiForm()">
                <i class="fas fa-plus"></i> Tambah Provinsi
            </button>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h3>Master Wilayah - Optimized</h3>
                    <p class="text-muted mb-0">Data dimuat secara dinamis untuk performa yang lebih baik</p>
                </div>
            </div>
            <div class="card-body">
                
                <!-- Search Bar -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <input type="text" id="searchWilayah" class="form-control" placeholder="Cari provinsi...">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary" onclick="refreshData()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Provinsi List -->
                <div id="provinsiContainer">
                    @if($provinsi->count() > 0)
                        @foreach($provinsi as $prov)
                        <div class="wilayah-card provinsi-card" data-provinsi="{{ $prov->kd_propinsi }}">
                            <div class="wilayah-header" onclick="toggleProvinsi({{ $prov->kd_propinsi }})">
                                <div>
                                    <span class="level-badge provinsi">PROVINSI</span>
                                    <strong>{{ $prov->propinsi }}</strong>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-light-success me-2" onclick="event.stopPropagation(); addKabupaten({{ $prov->kd_propinsi }}, '{{ $prov->propinsi }}')">
                                        <i class="fas fa-plus"></i> Kabupaten
                                    </button>
                                    <button class="btn btn-sm btn-light-warning me-2" onclick="event.stopPropagation(); editProvinsi({{ $prov->kd_propinsi }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light-danger" onclick="event.stopPropagation(); deleteProvinsi({{ $prov->kd_propinsi }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <i class="fas fa-chevron-down toggle-icon"></i>
                                </div>
                            </div>
                            <div class="wilayah-content" id="kabupaten-{{ $prov->kd_propinsi }}">
                                <div class="loading-spinner">
                                    <i class="fas fa-spinner fa-spin"></i> Loading kabupaten...
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">Belum ada data provinsi.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#provinsiModal" onclick="clearProvinsiForm()">
                                <i class="fas fa-plus"></i> Tambah Provinsi Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Content-->

<!-- Include all modals -->
@include('settings.wilayah.modals._provinsi')
@include('settings.wilayah.modals._kabupaten')
@include('settings.wilayah.modals._kecamatan')
@include('settings.wilayah.modals._kelurahan')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    'use strict';
    
    console.log('Initializing wilayah management');
    
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Tracking loaded data to prevent re-loading
    let loadedKabupaten = {};
    let loadedKecamatan = {};
    let loadedKelurahan = {};
    
    // State persistence for open cards
    let openCards = {
        provinsi: new Set(),
        kabupaten: new Set(),
        kecamatan: new Set()
    };

    // Search functionality
    $('#searchWilayah').on('keyup', function() {
        let searchText = $(this).val().toLowerCase();
        $('.provinsi-card').each(function() {
            let provinsiName = $(this).find('.wilayah-header strong').text().toLowerCase();
            if (provinsiName.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Global functions
    window.refreshData = function() {
        location.reload();
    }
    
    // Simple refresh function with state persistence
    window.refreshProvinsiData = function() {
        // Collect currently open cards
        openCards.provinsi.clear();
        openCards.kabupaten.clear();
        openCards.kecamatan.clear();
        
        $('.provinsi-card').each(function() {
            var provinsiId = $(this).data('provinsi');
            var isOpen = $(this).find('.wilayah-content.show').length > 0;
            if (isOpen && provinsiId) {
                openCards.provinsi.add(provinsiId);
            }
        });
        
        $('.kabupaten-card').each(function() {
            var kabupatenId = $(this).data('kabupaten');
            var isOpen = $(this).find('.wilayah-content.show').length > 0;
            if (isOpen && kabupatenId) {
                openCards.kabupaten.add(kabupatenId);
            }
        });
        
        $('.kecamatan-card').each(function() {
            var kecamatanId = $(this).data('kecamatan');
            var isOpen = $(this).find('.wilayah-content.show').length > 0;
            if (isOpen && kecamatanId) {
                openCards.kecamatan.add(kecamatanId);
            }
        });
        
        console.log('Saving card states:', openCards);
        
        $.ajax({
            url: window.location.href,
            type: 'GET',
            success: function(response) {
                var tempDiv = $('<div>').html(response);
                var newProvinsiContainer = tempDiv.find('#provinsiContainer').html();
                $('#provinsiContainer').html(newProvinsiContainer);
                
                // Reset loaded data tracking
                loadedKabupaten = {};
                loadedKecamatan = {};
                loadedKelurahan = {};
                
                // Restore card states after DOM is ready
                setTimeout(function() {
                    restoreCardStates();
                }, 100);
                
                toastr.success('Data berhasil diperbarui');
            },
            error: function(xhr) {
                console.error('Error refreshing data:', xhr);
                toastr.error('Gagal memuat ulang data');
            }
        });
    }
    
    // Restore card states function
    function restoreCardStates() {
        console.log('Restoring card states:', openCards);
        
        // Restore provinsi cards
        openCards.provinsi.forEach(function(provinsiId) {
            var element = $(`#kabupaten-${provinsiId}`);
            if (element.length) {
                element.addClass('show');
                element.prev().find('.toggle-icon')
                    .removeClass('fa-chevron-down')
                    .addClass('fa-chevron-up');
                
                // Load data if not loaded
                if (!loadedKabupaten[provinsiId]) {
                    loadKabupaten(provinsiId);
                }
            }
        });
        
        // Restore kabupaten cards after a delay
        setTimeout(function() {
            openCards.kabupaten.forEach(function(kabupatenId) {
                var element = $(`#kecamatan-${kabupatenId}`);
                if (element.length) {
                    element.addClass('show');
                    element.prev().find('.toggle-icon')
                        .removeClass('fa-chevron-down')
                        .addClass('fa-chevron-up');
                    
                    // Load data if not loaded
                    if (!loadedKecamatan[kabupatenId]) {
                        loadKecamatan(kabupatenId);
                    }
                }
            });
        }, 500);
        
        // Restore kecamatan cards after another delay
        setTimeout(function() {
            openCards.kecamatan.forEach(function(kecamatanId) {
                var element = $(`#kelurahan-${kecamatanId}`);
                if (element.length) {
                    element.addClass('show');
                    element.prev().find('.toggle-icon')
                        .removeClass('fa-chevron-down')
                        .addClass('fa-chevron-up');
                    
                    // Load data if not loaded
                    if (!loadedKelurahan[kecamatanId]) {
                        loadKelurahan(kecamatanId);
                    }
                }
            });
        }, 1000);
    }
    
    // Toggle functions
    window.toggleProvinsi = function(provinsiId) {
        const content = $(`#kabupaten-${provinsiId}`);
        const icon = content.prev().find('.toggle-icon');
        
        if (content.hasClass('show')) {
            content.removeClass('show');
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            openCards.provinsi.delete(provinsiId);
        } else {
            content.addClass('show');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            openCards.provinsi.add(provinsiId);
            
            // Load kabupaten if not already loaded
            if (!loadedKabupaten[provinsiId]) {
                loadKabupaten(provinsiId);
            }
        }
    }
    
    window.toggleKabupaten = function(kabupatenId) {
        const content = $(`#kecamatan-${kabupatenId}`);
        const icon = content.prev().find('.toggle-icon');
        
        if (content.hasClass('show')) {
            content.removeClass('show');
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            openCards.kabupaten.delete(kabupatenId);
        } else {
            content.addClass('show');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            openCards.kabupaten.add(kabupatenId);
            
            // Load kecamatan if not already loaded
            if (!loadedKecamatan[kabupatenId]) {
                loadKecamatan(kabupatenId);
            }
        }
    }
    
    window.toggleKecamatan = function(kecamatanId) {
        const content = $(`#kelurahan-${kecamatanId}`);
        const icon = content.prev().find('.toggle-icon');
        
        if (content.hasClass('show')) {
            content.removeClass('show');
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            openCards.kecamatan.delete(kecamatanId);
        } else {
            content.addClass('show');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            openCards.kecamatan.add(kecamatanId);
            
            // Load kelurahan if not already loaded
            if (!loadedKelurahan[kecamatanId]) {
                loadKelurahan(kecamatanId);
            }
        }
    }
    
    // Load functions
    window.loadKabupaten = function(provinsiId) {
        $.ajax({
            url: `/settings/wilayah/kabupaten/${provinsiId}`,
            type: 'GET',
            success: function(response) {
                $(`#kabupaten-${provinsiId}`).html(response);
                loadedKabupaten[provinsiId] = true;
            },
            error: function(xhr) {
                console.error('Error loading kabupaten:', xhr);
                toastr.error('Gagal memuat data kabupaten');
            }
        });
    }
    
    window.loadKecamatan = function(kabupatenId) {
        $.ajax({
            url: `/settings/wilayah/kecamatan/${kabupatenId}`,
            type: 'GET',
            success: function(response) {
                $(`#kecamatan-${kabupatenId}`).html(response);
                loadedKecamatan[kabupatenId] = true;
            },
            error: function(xhr) {
                console.error('Error loading kecamatan:', xhr);
                toastr.error('Gagal memuat data kecamatan');
            }
        });
    }
    
    window.loadKelurahan = function(kecamatanId) {
        $.ajax({
            url: `/settings/wilayah/kelurahan/${kecamatanId}`,
            type: 'GET',
            success: function(response) {
                $(`#kelurahan-${kecamatanId}`).html(response);
                loadedKelurahan[kecamatanId] = true;
            },
            error: function(xhr) {
                console.error('Error loading kelurahan:', xhr);
                toastr.error('Gagal memuat data kelurahan');
            }
        });
    }
    
    // Modal functions
    window.showProvinsiModal = function(id = null) {
        if (id) {
            // Edit mode
            $.ajax({
                url: `/settings/wilayah/provinsi/${id}`,
                type: 'GET',
                success: function(data) {
                    $('#provinsi_id').val(data.id);
                    $('#provinsi_nama').val(data.nama);
                    $('#provinsiModalLabel').text('Edit Provinsi');
                    $('#provinsiModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Gagal memuat data provinsi');
                }
            });
        } else {
            // Create mode
            $('#provinsiForm')[0].reset();
            $('#provinsi_id').val('');
            $('#provinsiModalLabel').text('Tambah Provinsi');
            $('#provinsiModal').modal('show');
        }
    }
    
    window.showKabupatenModal = function(provinsiId, id = null) {
        $('#kabupaten_provinsi_id').val(provinsiId);
        
        if (id) {
            // Edit mode
            $.ajax({
                url: `/settings/wilayah/kabupaten/${id}`,
                type: 'GET',
                success: function(data) {
                    $('#kabupaten_id').val(data.id);
                    $('#kabupaten_nama').val(data.nama);
                    $('#kabupatenModalLabel').text('Edit Kabupaten');
                    $('#kabupatenModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Gagal memuat data kabupaten');
                }
            });
        } else {
            // Create mode
            $('#kabupatenForm')[0].reset();
            $('#kabupaten_provinsi_id').val(provinsiId);
            $('#kabupaten_id').val('');
            $('#kabupatenModalLabel').text('Tambah Kabupaten');
            $('#kabupatenModal').modal('show');
        }
    }
    
    window.showKecamatanModal = function(kabupatenId, id = null) {
        $('#kecamatan_kabupaten_id').val(kabupatenId);
        
        if (id) {
            // Edit mode
            $.ajax({
                url: `/settings/wilayah/kecamatan/${id}`,
                type: 'GET',
                success: function(data) {
                    $('#kecamatan_id').val(data.id);
                    $('#kecamatan_nama').val(data.nama);
                    $('#kecamatanModalLabel').text('Edit Kecamatan');
                    $('#kecamatanModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Gagal memuat data kecamatan');
                }
            });
        } else {
            // Create mode
            $('#kecamatanForm')[0].reset();
            $('#kecamatan_kabupaten_id').val(kabupatenId);
            $('#kecamatan_id').val('');
            $('#kecamatanModalLabel').text('Tambah Kecamatan');
            $('#kecamatanModal').modal('show');
        }
    }
    
    window.showKelurahanModal = function(kecamatanId, id = null) {
        $('#kelurahan_kecamatan_id').val(kecamatanId);
        
        if (id) {
            // Edit mode
            $.ajax({
                url: `/settings/wilayah/kelurahan/${id}`,
                type: 'GET',
                success: function(data) {
                    $('#kelurahan_id').val(data.id);
                    $('#kelurahan_nama').val(data.nama);
                    $('#kelurahanModalLabel').text('Edit Kelurahan');
                    $('#kelurahanModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Gagal memuat data kelurahan');
                }
            });
        } else {
            // Create mode
            $('#kelurahanForm')[0].reset();
            $('#kelurahan_kecamatan_id').val(kecamatanId);
            $('#kelurahan_id').val('');
            $('#kelurahanModalLabel').text('Tambah Kelurahan');
            $('#kelurahanModal').modal('show');
        }
    }
    
    // Form submission handlers
    $('#provinsiForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const id = $('#provinsi_id').val();
        const url = id ? `/settings/wilayah/provinsi/${id}` : '/settings/wilayah/provinsi';
        const method = id ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#provinsiModal').modal('hide');
                toastr.success(response.message);
                refreshProvinsiData();
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`#${key}_error`).text(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    $('#kabupatenForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const id = $('#kabupaten_id').val();
        const url = id ? `/settings/wilayah/kabupaten/${id}` : '/settings/wilayah/kabupaten';
        const method = id ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#kabupatenModal').modal('hide');
                toastr.success(response.message);
                const provinsiId = $('#kabupaten_provinsi_id').val();
                loadedKabupaten[provinsiId] = false;
                loadKabupaten(provinsiId);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`#${key}_error`).text(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    $('#kecamatanForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const id = $('#kecamatan_id').val();
        const url = id ? `/settings/wilayah/kecamatan/${id}` : '/settings/wilayah/kecamatan';
        const method = id ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#kecamatanModal').modal('hide');
                toastr.success(response.message);
                const kabupatenId = $('#kecamatan_kabupaten_id').val();
                loadedKecamatan[kabupatenId] = false;
                loadKecamatan(kabupatenId);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`#${key}_error`).text(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    $('#kelurahanForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const id = $('#kelurahan_id').val();
        const url = id ? `/settings/wilayah/kelurahan/${id}` : '/settings/wilayah/kelurahan';
        const method = id ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#kelurahanModal').modal('hide');
                toastr.success(response.message);
                const kecamatanId = $('#kelurahan_kecamatan_id').val();
                loadedKelurahan[kecamatanId] = false;
                loadKelurahan(kecamatanId);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`#${key}_error`).text(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    // Clear errors when modal is hidden
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.error-text').text('');
    });
    
});
</script>
@endpush
