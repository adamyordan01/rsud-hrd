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
    
    // Simple refresh function
    window.refreshProvinsiData = function() {
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
                
                toastr.success('Data berhasil diperbarui');
            },
            error: function(xhr) {
                console.error('Error refreshing data:', xhr);
                toastr.error('Gagal memuat ulang data');
            }
        });
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
    
    // State persistence functions
    function restoreCardStates(cardStates) {
        console.log('Restoring card states:', cardStates);
        
        // Restore provinsi cards
        if (cardStates.provinsi) {
            cardStates.provinsi.forEach(function(provinsiId) {
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
        }
        
        // Restore kabupaten cards after a delay
        setTimeout(function() {
            if (cardStates.kabupaten) {
                cardStates.kabupaten.forEach(function(kabupatenId) {
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
            }
            
            // Restore kecamatan cards after another delay
            setTimeout(function() {
                if (cardStates.kecamatan) {
                    cardStates.kecamatan.forEach(function(kecamatanId) {
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
                }
            }, 1000);
        }, 500);
    }
    
    // Modal functions for CRUD operations
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
                if (provinsiCard.length) {
                    toggleProvinsi(provinsiId);
                }
            });
            
            // Restore kabupaten cards (dengan delay untuk memastikan data sudah load)
            setTimeout(function() {
                openCards.kabupaten.forEach(function(kabupatenId) {
                    var kabupatenCard = $(`.kabupaten-card[data-kabupaten="${kabupatenId}"]`);
                    if (kabupatenCard.length) {
                        toggleKabupaten(kabupatenId);
                    }
                });
            }, 200);
            
            // Restore kecamatan cards (dengan delay lebih lama)
            setTimeout(function() {
                openCards.kecamatan.forEach(function(kecamatanId) {
                    var kecamatanCard = $(`.kecamatan-card[data-kecamatan="${kecamatanId}"]`);
                    if (kecamatanCard.length) {
                        toggleKecamatan(kecamatanId);
                    }
                });
            }, 400);
        }
                    
                    toastr.success('Data berhasil diperbarui');
                },
                error: function(xhr) {
                    toastr.error('Gagal memperbarui data');
                }
            });
        }

        window.toggleProvinsi = function(provinsiId) {
            const content = $(`#kabupaten-${provinsiId}`);
            const icon = content.prev().find('.toggle-icon');
            
            if (content.hasClass('show')) {
                content.removeClass('show');
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                content.addClass('show');
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                
                // Load kabupaten if not already loaded
                if (!loadedKabupaten[provinsiId]) {
                    loadKabupaten(provinsiId);
                }
            }
        }

        function loadKabupaten(provinsiId) {
            const container = $(`#kabupaten-${provinsiId}`);
            
            $.ajax({
                url: `{{ route('admin.settings.wilayah.load.kabupaten', ':id') }}`.replace(':id', provinsiId),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        
                        if (response.data.length > 0) {
                            response.data.forEach(function(kab) {
                                html += `
                                    <div class="wilayah-card kabupaten-card ms-4" data-kabupaten="${kab.kd_kabupaten}">
                                        <div class="wilayah-header" onclick="toggleKabupaten(${kab.kd_kabupaten})">
                                            <div>
                                                <span class="level-badge kabupaten">KABUPATEN</span>
                                                <strong>${kab.kabupaten}</strong>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-light-warning me-2" onclick="event.stopPropagation(); addKecamatan(${kab.kd_kabupaten}, '${kab.kabupaten}')">
                                                    <i class="fas fa-plus"></i> Kecamatan
                                                </button>
                                                <button class="btn btn-sm btn-light-primary me-2" onclick="event.stopPropagation(); editKabupaten(${provinsiId}, ${kab.kd_kabupaten})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light-danger" onclick="event.stopPropagation(); deleteKabupaten(${provinsiId}, ${kab.kd_kabupaten})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </div>
                                        </div>
                                        <div class="wilayah-content" id="kecamatan-${kab.kd_kabupaten}">
                                            <div class="loading-spinner">
                                                <i class="fas fa-spinner fa-spin"></i> Loading kecamatan...
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html = '<div class="text-center py-3 text-muted">Belum ada kabupaten</div>';
                        }
                        
                        container.html(html);
                    loadedKabupaten[provinsiId] = true;
                } else {
                    container.html('<div class="alert alert-danger">Error loading data</div>');
                }
            },
            error: function() {
                container.html('<div class="alert alert-danger">Error loading kabupaten</div>');
            }
        });
    }

    window.toggleKabupaten = function(kabupatenId) {
        const content = $(`#kecamatan-${kabupatenId}`);
        const icon = content.prev().find('.toggle-icon');
        
        if (content.hasClass('show')) {
            content.removeClass('show');
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            content.addClass('show');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            
            // Load kecamatan if not already loaded
            if (!loadedKecamatan[kabupatenId]) {
                loadKecamatan(kabupatenId);
            }
        }
    }

    function loadKecamatan(kabupatenId) {
        const container = $(`#kecamatan-${kabupatenId}`);
        
        $.ajax({
            url: `{{ route('admin.settings.wilayah.load.kecamatan', ':id') }}`.replace(':id', kabupatenId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let html = '';
                    
                    if (response.data.length > 0) {
                        response.data.forEach(function(kec) {
                            html += `
                                <div class="wilayah-card kecamatan-card ms-4" data-kecamatan="${kec.kd_kecamatan}">
                                    <div class="wilayah-header" onclick="toggleKecamatan(${kec.kd_kecamatan})">
                                        <div>
                                            <span class="level-badge kecamatan">KECAMATAN</span>
                                            <strong>${kec.kecamatan}</strong>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-light-danger me-2" onclick="event.stopPropagation(); addKelurahan(${kec.kd_kecamatan}, '${kec.kecamatan}')">
                                                <i class="fas fa-plus"></i> Kelurahan
                                            </button>
                                            <button class="btn btn-sm btn-light-primary me-2" onclick="event.stopPropagation(); editKecamatan(${kabupatenId}, ${kec.kd_kecamatan})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light-danger" onclick="event.stopPropagation(); deleteKecamatan(${kabupatenId}, ${kec.kd_kecamatan})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <i class="fas fa-chevron-down toggle-icon"></i>
                                        </div>
                                    </div>
                                    <div class="wilayah-content" id="kelurahan-${kec.kd_kecamatan}">
                                        <div class="loading-spinner">
                                            <i class="fas fa-spinner fa-spin"></i> Loading kelurahan...
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = '<div class="text-center py-3 text-muted">Belum ada kecamatan</div>';
                    }
                    
                    container.html(html);
                    loadedKecamatan[kabupatenId] = true;
                } else {
                    container.html('<div class="alert alert-danger">Error loading data</div>');
                }
            },
            error: function() {
                container.html('<div class="alert alert-danger">Error loading kecamatan</div>');
            }
        });
    }

    window.toggleKecamatan = function(kecamatanId) {
        const content = $(`#kelurahan-${kecamatanId}`);
        const icon = content.prev().find('.toggle-icon');
        
        if (content.hasClass('show')) {
            content.removeClass('show');
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            content.addClass('show');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            
            // Load kelurahan if not already loaded
            if (!loadedKelurahan[kecamatanId]) {
                loadKelurahan(kecamatanId);
            }
        }
    }

    function loadKelurahan(kecamatanId) {
        const container = $(`#kelurahan-${kecamatanId}`);
        
        $.ajax({
            url: `{{ route('admin.settings.wilayah.load.kelurahan', ':id') }}`.replace(':id', kecamatanId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let html = '';
                    
                    if (response.data.length > 0) {
                        response.data.forEach(function(kel) {
                            html += `
                                <div class="wilayah-card ms-4" data-kelurahan="${kel.kd_kelurahan}">
                                    <div class="wilayah-header">
                                        <div>
                                            <span class="level-badge kelurahan">KELURAHAN</span>
                                            <strong>${kel.kelurahan}</strong>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-light-primary me-2" onclick="editKelurahan(${kecamatanId}, ${kel.kd_kelurahan})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light-danger" onclick="deleteKelurahan(${kecamatanId}, ${kel.kd_kelurahan})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = '<div class="text-center py-3 text-muted">Belum ada kelurahan</div>';
                    }
                    
                    container.html(html);
                    loadedKelurahan[kecamatanId] = true;
                } else {
                    container.html('<div class="alert alert-danger">Error loading data</div>');
                }
            },
            error: function() {
                container.html('<div class="alert alert-danger">Error loading kelurahan</div>');
            }
        });
    }

    // Modal functions
    window.clearProvinsiForm = function() {
        $('#modalProvinsiTitle').text('Tambah Provinsi');
        $('#formProvinsiMethod').val('POST');
        $('#provinsiForm')[0].reset();
        $('.error-text').text('');
    }

    window.addKabupaten = function(provinsiId, provinsiName) {
        $('#modalKabupatenTitle').text('Tambah Kabupaten - ' + provinsiName);
        $('#formKabupatenMethod').val('POST');
        $('#kabupaten_provinsi_id').val(provinsiId);
        $('#kabupatenForm')[0].reset();
        $('.error-text').text('');
        $('#kabupatenModal').modal('show');
    }

    window.addKecamatan = function(kabupatenId, kabupatenName) {
        $('#modalKecamatanTitle').text('Tambah Kecamatan - ' + kabupatenName);
        $('#formKecamatanMethod').val('POST');
        $('#kecamatan_kabupaten_id').val(kabupatenId);
        $('#kecamatanForm')[0].reset();
        $('.error-text').text('');
        $('#kecamatanModal').modal('show');
    }

    window.addKelurahan = function(kecamatanId, kecamatanName) {
        $('#modalKelurahanTitle').text('Tambah Kelurahan - ' + kecamatanName);
        $('#formKelurahanMethod').val('POST');
        $('#kelurahan_kecamatan_id').val(kecamatanId);
        $('#kelurahanForm')[0].reset();
        $('.error-text').text('');
        $('#kelurahanModal').modal('show');
    }

    // Edit functions
    window.editProvinsi = function(id) {
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

    window.editKabupaten = function(provinsiId, id) {
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

    window.editKecamatan = function(kabupatenId, id) {
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

    window.editKelurahan = function(kecamatanId, id) {
        $.ajax({
            url: '{{ route("admin.settings.wilayah.kelurahan.edit", [":kecamatanId", ":id"]) }}'.replace(':kecamatanId', kecamatanId).replace(':id', id),
            type: 'GET',
            success: function(response) {
                $('#modalKelurahanTitle').text('Edit Kelurahan');
                $('#formKelurahanMethod').val('PATCH');
                $('#kelurahan_kecamatan_id').val(response.data.kd_kecamatan);
                $('#kd_kelurahan_hidden').val(response.data.kd_kelurahan);
                $('#kelurahan').val(response.data.kelurahan);
                $('#kode_pos').val(response.data.kode_pos || '');
                $('#aktif').val(response.data.aktif);
                $('.error-text').text('');
                $('#kelurahanModal').modal('show');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Gagal mengambil data.');
            }
        });
    }

    // Delete functions
    window.deleteProvinsi = function(id) {
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
                        refreshProvinsiData(); // Gunakan refresh tanpa reload
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    window.deleteKabupaten = function(provinsiId, id) {
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
                        refreshProvinsiData(); // Gunakan refresh tanpa reload
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    window.deleteKecamatan = function(kabupatenId, id) {
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
                        refreshProvinsiData(); // Gunakan refresh tanpa reload
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    window.deleteKelurahan = function(kecamatanId, id) {
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
                        refreshProvinsiData(); // Gunakan refresh tanpa reload
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    // Form Submissions - tambahkan sebelum closing function
    // Provinsi Form
    $(document).on('submit', '#provinsiForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var method = $('#formProvinsiMethod').val();
        var url = method === 'POST' 
            ? '{{ route("admin.settings.wilayah.provinsi.store") }}' 
            : '{{ route("admin.settings.wilayah.provinsi.update", ":id") }}'.replace(':id', $('#kd_provinsi_hidden').val());
        
        var formData = new FormData(form[0]);
        if (method === 'PATCH') {
            formData.append('_method', 'PATCH');
        }

        var loadingIndicator = form.find('.indicator-progress');
        var submitBtn = form.find('.submit-btn');
        var indicatorLabel = form.find('.indicator-label');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                loadingIndicator.show();
                indicatorLabel.hide();
                submitBtn.attr('disabled', true);
                $('.error-text').text('');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#provinsiModal').modal('hide');
                    form[0].reset();
                    
                    // Refresh data tanpa reload halaman
                    refreshProvinsiData();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan.');
                }
            },
            complete: function() {
                loadingIndicator.hide();
                indicatorLabel.show();
                submitBtn.attr('disabled', false);
            }
        });
    });

    // Kabupaten Form
    $(document).on('submit', '#kabupatenForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var method = $('#formKabupatenMethod').val();
        var url = method === 'POST' 
            ? '{{ route("admin.settings.wilayah.kabupaten.store") }}' 
            : '{{ route("admin.settings.wilayah.kabupaten.update", [":provinsiId", ":id"]) }}'
                .replace(':provinsiId', $('#kabupaten_provinsi_id').val())
                .replace(':id', $('#kd_kabupaten_hidden').val());
        
        var formData = new FormData(form[0]);
        if (method === 'PATCH') {
            formData.append('_method', 'PATCH');
        }

        var loadingIndicator = form.find('.indicator-progress');
        var submitBtn = form.find('.submit-btn');
        var indicatorLabel = form.find('.indicator-label');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                loadingIndicator.show();
                indicatorLabel.hide();
                submitBtn.attr('disabled', true);
                $('.error-text').text('');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#kabupatenModal').modal('hide');
                    form[0].reset();
                    
                    // Refresh data tanpa reload halaman
                    refreshProvinsiData();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan.');
                }
            },
            complete: function() {
                loadingIndicator.hide();
                indicatorLabel.show();
                submitBtn.attr('disabled', false);
            }
        });
    });

    // Kecamatan Form
    // Kecamatan Form with AJAX
    $(document).on('submit', '#kecamatanForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var method = $('#formKecamatanMethod').val();
        var url = method === 'POST' 
            ? '{{ route("admin.settings.wilayah.kecamatan.store") }}' 
            : '{{ route("admin.settings.wilayah.kecamatan.update", [":kabupatenId", ":kecamatanId"]) }}'
                .replace(':kabupatenId', $('#kecamatan_kabupaten_id').val())
                .replace(':kecamatanId', $('#kd_kecamatan_hidden').val());
        
        var formData = new FormData(form[0]);
        if (method === 'PATCH') {
            formData.append('_method', 'PATCH');
        }

        var loadingIndicator = form.find('.indicator-progress');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                loadingIndicator.show();
                $('.error-text').text('');
                form.find('.submit-btn').attr('disabled', true);
                form.find('.indicator-label').hide();
            },
            success: function(response) {
                toastr.success(response.message, 'Success');
                $('#kecamatanModal').modal('hide');
                form[0].reset();
                
                // Gunakan fungsi refresh tanpa reload halaman
                refreshProvinsiData();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.');
                }
            },
            complete: function() {
                loadingIndicator.hide();
                form.find('.submit-btn').attr('disabled', false);
                form.find('.indicator-label').show();
            }
        });
    });

    // Kelurahan Form with AJAX
    $(document).on('submit', '#kelurahanForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var method = $('#formKelurahanMethod').val();
        var url = method === 'POST' 
            ? '{{ route("admin.settings.wilayah.kelurahan.store") }}' 
            : '{{ route("admin.settings.wilayah.kelurahan.update", [":kecamatanId", ":kelurahanId"]) }}'
                .replace(':kecamatanId', $('#kelurahan_kecamatan_id').val())
                .replace(':kelurahanId', $('#kd_kelurahan_hidden').val());
        
        var formData = new FormData(form[0]);
        if (method === 'PATCH') {
            formData.append('_method', 'PATCH');
        }

        var loadingIndicator = form.find('.indicator-progress');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                loadingIndicator.show();
                $('.error-text').text('');
                form.find('.submit-btn').attr('disabled', true);
                form.find('.indicator-label').hide();
            },
            success: function(response) {
                toastr.success(response.message, 'Success');
                $('#kelurahanModal').modal('hide');
                form[0].reset();
                
                // Gunakan fungsi refresh tanpa reload halaman
                refreshProvinsiData();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.');
                }
            },
            complete: function() {
                loadingIndicator.hide();
                form.find('.submit-btn').attr('disabled', false);
                form.find('.indicator-label').show();
            }
        });
    });

    // Reset form handlers for modals
    $('#kecamatanModal').on('hidden.bs.modal', function() {
        $('#kecamatanForm')[0].reset();
        $('.error-text').text('');
    });

    $('#kelurahanModal').on('hidden.bs.modal', function() {
        $('#kelurahanForm')[0].reset();
        $('.error-text').text('');
    });

    // Reset forms on modal close
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.error-text').text('');
    });

    } // End initializeWilayahFunctions

    // Wait for DOM and initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeWilayahFunctions);
    } else {
        initializeWilayahFunctions();
    }
})();
</script>
@endpush
