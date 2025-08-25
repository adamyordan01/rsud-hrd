@extends('layouts.backend')

@section('title', $pageTitle)

@push('styles')
<style>
    .wilayah-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        overflow: hidden; /* Prevent content from overflowing */
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
        border-radius: 8px 8px 0 0; /* Round top corners only */
        margin: 0; /* Remove any margin */
        box-sizing: border-box; /* Include padding in width calculation */
    }
    /* Special styling for kelurahan cards - no border-bottom */
    .wilayah-card[data-kelurahan] .wilayah-header {
        border-bottom: none;
        border-radius: 8px; /* Round all corners for kelurahan */
    }
    .wilayah-content {
        padding: 15px;
        display: none;
        box-sizing: border-box; /* Include padding in width calculation */
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
            <button class="btn btn-sm btn-primary" onclick="showProvinsiModal()">
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
                                    <button class="btn btn-sm btn-light-success me-2" onclick="event.stopPropagation(); showKabupatenModal({{ $prov->kd_propinsi }})">
                                        <i class="fas fa-plus"></i> Kabupaten
                                    </button>
                                    <button class="btn btn-sm btn-light-warning me-2" onclick="event.stopPropagation(); showProvinsiModal({{ $prov->kd_propinsi }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light-danger me-2" onclick="event.stopPropagation(); deleteProvinsi({{ $prov->kd_propinsi }})">
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
                            <button class="btn btn-primary" onclick="showProvinsiModal()">
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
    
    // Define route URLs using Laravel route() helper
    const routes = {
        load: {
            kabupaten: '{{ route("admin.settings.wilayah.load.kabupaten", ":provinsiId") }}',
            kecamatan: '{{ route("admin.settings.wilayah.load.kecamatan", ":kabupatenId") }}',
            kelurahan: '{{ route("admin.settings.wilayah.load.kelurahan", ":kecamatanId") }}'
        },
        edit: {
            provinsi: '{{ route("admin.settings.wilayah.provinsi.edit", ":id") }}',
            kabupaten: '{{ route("admin.settings.wilayah.kabupaten.edit", [":provinsiId", ":kabupatenId"]) }}',
            kecamatan: '{{ route("admin.settings.wilayah.kecamatan.edit", [":kabupatenId", ":kecamatanId"]) }}',
            kelurahan: '{{ route("admin.settings.wilayah.kelurahan.edit", [":kecamatanId", ":kelurahanId"]) }}'
        },
        store: {
            provinsi: '{{ route("admin.settings.wilayah.provinsi.store") }}',
            kabupaten: '{{ route("admin.settings.wilayah.kabupaten.store") }}',
            kecamatan: '{{ route("admin.settings.wilayah.kecamatan.store") }}',
            kelurahan: '{{ route("admin.settings.wilayah.kelurahan.store") }}'
        },
        update: {
            provinsi: '{{ route("admin.settings.wilayah.provinsi.update", ":id") }}',
            kabupaten: '{{ route("admin.settings.wilayah.kabupaten.update", [":provinsiId", ":kabupatenId"]) }}',
            kecamatan: '{{ route("admin.settings.wilayah.kecamatan.update", [":kabupatenId", ":kecamatanId"]) }}',
            kelurahan: '{{ route("admin.settings.wilayah.kelurahan.update", [":kecamatanId", ":kelurahanId"]) }}'
        },
        destroy: {
            provinsi: '{{ route("admin.settings.wilayah.provinsi.destroy", ":id") }}',
            kabupaten: '{{ route("admin.settings.wilayah.kabupaten.destroy", [":provinsiId", ":kabupatenId"]) }}',
            kecamatan: '{{ route("admin.settings.wilayah.kecamatan.destroy", [":kabupatenId", ":kecamatanId"]) }}',
            kelurahan: '{{ route("admin.settings.wilayah.kelurahan.destroy", [":kecamatanId", ":kelurahanId"]) }}'
        }
    };
    
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
            url: routes.load.kabupaten.replace(':provinsiId', provinsiId),
            type: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    let html = '';
                    if (response.data.length > 0) {
                        response.data.forEach(function(kab) {
                            // Check if this kabupaten was previously open
                            const wasOpen = openCards.kabupaten.has(kab.kd_kabupaten);
                            const wasLoaded = loadedKecamatan[kab.kd_kabupaten];
                            
                            html += `
                                <div class="wilayah-card kabupaten-card ms-4" data-kabupaten="${kab.kd_kabupaten}">
                                    <div class="wilayah-header" onclick="toggleKabupaten(${kab.kd_kabupaten})">
                                        <div>
                                            <span class="level-badge kabupaten">KABUPATEN</span>
                                            <strong>${kab.kabupaten}</strong>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-light-warning me-2" onclick="event.stopPropagation(); showKecamatanModal(${kab.kd_kabupaten})">
                                                <i class="fas fa-plus"></i> Kecamatan
                                            </button>
                                            <button class="btn btn-sm btn-light-primary me-2" onclick="event.stopPropagation(); showKabupatenModal(${provinsiId}, ${kab.kd_kabupaten})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light-danger me-2" onclick="event.stopPropagation(); deleteKabupaten(${provinsiId}, ${kab.kd_kabupaten})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <i class="fas fa-chevron-${wasOpen ? 'up' : 'down'} toggle-icon"></i>
                                        </div>
                                    </div>
                                    <div class="wilayah-content ${wasOpen ? 'show' : ''}" id="kecamatan-${kab.kd_kabupaten}">
                                        <div class="loading-spinner">
                                            <i class="fas fa-spinner fa-spin"></i> Loading kecamatan...
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Restore loaded state if it was previously loaded
                            if (wasLoaded) {
                                loadedKecamatan[kab.kd_kabupaten] = false; // Reset to trigger reload
                            }
                        });
                    } else {
                        html = '<div class="text-muted text-center py-3">Tidak ada data kabupaten</div>';
                    }
                    $(`#kabupaten-${provinsiId}`).html(html);
                    
                    // Reload kecamatan for previously opened kabupaten
                    if (response.data.length > 0) {
                        response.data.forEach(function(kab) {
                            if (openCards.kabupaten.has(kab.kd_kabupaten)) {
                                loadKecamatan(kab.kd_kabupaten);
                            }
                        });
                    }
                } else {
                    $(`#kabupaten-${provinsiId}`).html('<div class="text-danger text-center py-3">Error loading kabupaten</div>');
                }
                loadedKabupaten[provinsiId] = true;
            },
            error: function(xhr) {
                console.error('Error loading kabupaten:', xhr);
                $(`#kabupaten-${provinsiId}`).html('<div class="text-danger text-center py-3">Error loading kabupaten</div>');
                toastr.error('Gagal memuat data kabupaten');
            }
        });
    }
    
    window.loadKecamatan = function(kabupatenId) {
        $.ajax({
            url: routes.load.kecamatan.replace(':kabupatenId', kabupatenId),
            type: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    let html = '';
                    if (response.data.length > 0) {
                        response.data.forEach(function(kec) {
                            // Check if this kecamatan was previously open
                            const wasOpen = openCards.kecamatan.has(kec.kd_kecamatan);
                            const wasLoaded = loadedKelurahan[kec.kd_kecamatan];
                            
                            html += `
                                <div class="wilayah-card kecamatan-card ms-4" data-kecamatan="${kec.kd_kecamatan}">
                                    <div class="wilayah-header" onclick="toggleKecamatan(${kec.kd_kecamatan})">
                                        <div>
                                            <span class="level-badge kecamatan">KECAMATAN</span>
                                            <strong>${kec.kecamatan}</strong>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-light-danger me-2" onclick="event.stopPropagation(); showKelurahanModal(${kec.kd_kecamatan})">
                                                <i class="fas fa-plus"></i> Kelurahan
                                            </button>
                                            <button class="btn btn-sm btn-light-primary me-2" onclick="event.stopPropagation(); showKecamatanModal(${kabupatenId}, ${kec.kd_kecamatan})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light-danger me-2" onclick="event.stopPropagation(); deleteKecamatan(${kabupatenId}, ${kec.kd_kecamatan})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <i class="fas fa-chevron-${wasOpen ? 'up' : 'down'} toggle-icon"></i>
                                        </div>
                                    </div>
                                    <div class="wilayah-content ${wasOpen ? 'show' : ''}" id="kelurahan-${kec.kd_kecamatan}">
                                        <div class="loading-spinner">
                                            <i class="fas fa-spinner fa-spin"></i> Loading kelurahan...
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Restore loaded state if it was previously loaded
                            if (wasLoaded) {
                                loadedKelurahan[kec.kd_kecamatan] = false; // Reset to trigger reload
                            }
                        });
                    } else {
                        html = '<div class="text-muted text-center py-3">Tidak ada data kecamatan</div>';
                    }
                    $(`#kecamatan-${kabupatenId}`).html(html);
                    
                    // Reload kelurahan for previously opened kecamatan
                    if (response.data.length > 0) {
                        response.data.forEach(function(kec) {
                            if (openCards.kecamatan.has(kec.kd_kecamatan)) {
                                loadKelurahan(kec.kd_kecamatan);
                            }
                        });
                    }
                } else {
                    $(`#kecamatan-${kabupatenId}`).html('<div class="text-danger text-center py-3">Error loading kecamatan</div>');
                }
                loadedKecamatan[kabupatenId] = true;
            },
            error: function(xhr) {
                console.error('Error loading kecamatan:', xhr);
                $(`#kecamatan-${kabupatenId}`).html('<div class="text-danger text-center py-3">Error loading kecamatan</div>');
                toastr.error('Gagal memuat data kecamatan');
                loadedKecamatan[kabupatenId] = true; // Mark as loaded even on error to prevent infinite loading
            }
        });
    }
    
    window.loadKelurahan = function(kecamatanId) {
        $.ajax({
            url: routes.load.kelurahan.replace(':kecamatanId', kecamatanId),
            type: 'GET',
            success: function(response) {
                if (response.success && response.data) {
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
                                            <button class="btn btn-sm btn-light-primary me-2" onclick="showKelurahanModal(${kecamatanId}, ${kel.kd_kelurahan})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light-danger me-2" onclick="deleteKelurahan(${kecamatanId}, ${kel.kd_kelurahan})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = '<div class="text-muted text-center py-3">Tidak ada data kelurahan</div>';
                    }
                    $(`#kelurahan-${kecamatanId}`).html(html);
                } else {
                    $(`#kelurahan-${kecamatanId}`).html('<div class="text-danger text-center py-3">Error loading kelurahan</div>');
                }
                loadedKelurahan[kecamatanId] = true;
            },
            error: function(xhr) {
                console.error('Error loading kelurahan:', xhr);
                $(`#kelurahan-${kecamatanId}`).html('<div class="text-danger text-center py-3">Error loading kelurahan</div>');
                toastr.error('Gagal memuat data kelurahan');
                loadedKelurahan[kecamatanId] = true; // Mark as loaded even on error to prevent infinite loading
            }
        });
    }
    
    // Modal functions
    window.showProvinsiModal = function(id = null) {
        if (id) {
            // Edit mode
            $.ajax({
                url: routes.edit.provinsi.replace(':id', id),
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        $('#kd_provinsi_hidden').val(response.data.kd_propinsi);
                        $('#propinsi').val(response.data.propinsi);
                        $('#formProvinsiMethod').val('PATCH');
                        $('#modalProvinsiTitle').text('Edit Provinsi');
                        $('#provinsiModal').modal('show');
                    } else {
                        toastr.error('Gagal memuat data provinsi');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading provinsi:', xhr);
                    toastr.error('Gagal memuat data provinsi');
                }
            });
        } else {
            // Create mode
            $('#provinsiForm')[0].reset();
            $('#kd_provinsi_hidden').val('');
            $('#formProvinsiMethod').val('POST');
            $('#modalProvinsiTitle').text('Tambah Provinsi');
            $('#provinsiModal').modal('show');
        }
    }
    
    window.showKabupatenModal = function(provinsiId, id = null) {
        $('#kabupaten_provinsi_id').val(provinsiId);
        
        if (id) {
            // Edit mode
            $.ajax({
                url: routes.edit.kabupaten.replace(':provinsiId', provinsiId).replace(':kabupatenId', id),
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        $('#kd_kabupaten_hidden').val(response.data.kd_kabupaten);
                        $('#kabupaten').val(response.data.kabupaten);
                        $('#formKabupatenMethod').val('PATCH');
                        $('#modalKabupatenTitle').text('Edit Kabupaten');
                        $('#kabupatenModal').modal('show');
                    } else {
                        toastr.error('Gagal memuat data kabupaten');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading kabupaten:', xhr);
                    toastr.error('Gagal memuat data kabupaten');
                }
            });
        } else {
            // Create mode
            $('#kabupatenForm')[0].reset();
            $('#kabupaten_provinsi_id').val(provinsiId);
            $('#kd_kabupaten_hidden').val('');
            $('#formKabupatenMethod').val('POST');
            $('#modalKabupatenTitle').text('Tambah Kabupaten');
            $('#kabupatenModal').modal('show');
        }
    }
    
    window.showKecamatanModal = function(kabupatenId, id = null) {
        $('#kecamatan_kabupaten_id').val(kabupatenId);
        
        if (id) {
            // Edit mode
            $.ajax({
                url: routes.edit.kecamatan.replace(':kabupatenId', kabupatenId).replace(':kecamatanId', id),
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        $('#kd_kecamatan_hidden').val(response.data.kd_kecamatan);
                        $('#kecamatan').val(response.data.kecamatan);
                        $('#formKecamatanMethod').val('PATCH');
                        $('#modalKecamatanTitle').text('Edit Kecamatan');
                        $('#kecamatanModal').modal('show');
                    } else {
                        toastr.error('Gagal memuat data kecamatan');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading kecamatan:', xhr);
                    toastr.error('Gagal memuat data kecamatan');
                }
            });
        } else {
            // Create mode
            $('#kecamatanForm')[0].reset();
            $('#kecamatan_kabupaten_id').val(kabupatenId);
            $('#kd_kecamatan_hidden').val('');
            $('#formKecamatanMethod').val('POST');
            $('#modalKecamatanTitle').text('Tambah Kecamatan');
            $('#kecamatanModal').modal('show');
        }
    }
    
    window.showKelurahanModal = function(kecamatanId, id = null) {
        $('#kelurahan_kecamatan_id').val(kecamatanId);
        
        if (id) {
            // Edit mode
            $.ajax({
                url: routes.edit.kelurahan.replace(':kecamatanId', kecamatanId).replace(':kelurahanId', id),
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        $('#kd_kelurahan_hidden').val(response.data.kd_kelurahan);
                        $('#kelurahan').val(response.data.kelurahan);
                        $('#kode_pos').val(response.data.kode_pos || '');
                        $('#aktif').val(response.data.aktif || '1');
                        $('#formKelurahanMethod').val('PATCH');
                        $('#modalKelurahanTitle').text('Edit Kelurahan');
                        $('#kelurahanModal').modal('show');
                    } else {
                        toastr.error('Gagal memuat data kelurahan');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading kelurahan:', xhr);
                    toastr.error('Gagal memuat data kelurahan');
                }
            });
        } else {
            // Create mode
            $('#kelurahanForm')[0].reset();
            $('#kelurahan_kecamatan_id').val(kecamatanId);
            $('#kd_kelurahan_hidden').val('');
            $('#formKelurahanMethod').val('POST');
            $('#modalKelurahanTitle').text('Tambah Kelurahan');
            $('#kelurahanModal').modal('show');
        }
    }
    
    // Form submission handlers
    $('#provinsiForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = $('#kd_provinsi_hidden').val();
        const method = $('#formProvinsiMethod').val();
        const url = id ? routes.update.provinsi.replace(':id', id) : routes.store.provinsi;
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#provinsiModal').modal('hide');
                toastr.success(response.message);
                refreshProvinsiData();
            },
            error: function(xhr) {
                console.error('Error response:', xhr.responseJSON);
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`.${key}_error`).text(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    $('#kabupatenForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = $('#kd_kabupaten_hidden').val();
        const provinsiId = $('#kabupaten_provinsi_id').val();
        const method = $('#formKabupatenMethod').val();
        const url = id ? routes.update.kabupaten.replace(':provinsiId', provinsiId).replace(':kabupatenId', id) : routes.store.kabupaten;
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#kabupatenModal').modal('hide');
                toastr.success(response.message);
                
                // Reset nested loaded states for all kecamatan and kelurahan
                Object.keys(loadedKecamatan).forEach(key => {
                    if (loadedKecamatan[key]) {
                        loadedKecamatan[key] = false;
                    }
                });
                Object.keys(loadedKelurahan).forEach(key => {
                    if (loadedKelurahan[key]) {
                        loadedKelurahan[key] = false;
                    }
                });
                
                loadedKabupaten[provinsiId] = false;
                loadKabupaten(provinsiId);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`.${key}_error`).text(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    $('#kecamatanForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = $('#kd_kecamatan_hidden').val();
        const kabupatenId = $('#kecamatan_kabupaten_id').val();
        const method = $('#formKecamatanMethod').val();
        const url = id ? routes.update.kecamatan.replace(':kabupatenId', kabupatenId).replace(':kecamatanId', id) : routes.store.kecamatan;
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#kecamatanModal').modal('hide');
                toastr.success(response.message);
                
                // Reset nested loaded states for all kelurahan in this kabupaten
                Object.keys(loadedKelurahan).forEach(key => {
                    if (loadedKelurahan[key]) {
                        loadedKelurahan[key] = false;
                    }
                });
                
                loadedKecamatan[kabupatenId] = false;
                loadKecamatan(kabupatenId);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`.${key}_error`).text(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    $('#kelurahanForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = $('#kd_kelurahan_hidden').val();
        const kecamatanId = $('#kelurahan_kecamatan_id').val();
        const method = $('#formKelurahanMethod').val();
        const url = id ? routes.update.kelurahan.replace(':kecamatanId', kecamatanId).replace(':kelurahanId', id) : routes.store.kelurahan;
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#kelurahanModal').modal('hide');
                toastr.success(response.message);
                loadedKelurahan[kecamatanId] = false;
                loadKelurahan(kecamatanId);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`.${key}_error`).text(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    
    
    // Delete functions
    window.deleteProvinsi = function(id) {
        if (confirm('Apakah Anda yakin ingin menghapus provinsi ini?')) {
            $.ajax({
                url: routes.destroy.provinsi.replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    location.reload(); // Reload to update the display
                },
                error: function(xhr) {
                    console.error('Error deleting provinsi:', xhr);
                    toastr.error(xhr.responseJSON?.message || 'Gagal menghapus provinsi');
                }
            });
        }
    }
    
    window.deleteKabupaten = function(provinsiId, kabupatenId) {
        if (confirm('Apakah Anda yakin ingin menghapus kabupaten ini?')) {
            $.ajax({
                url: routes.destroy.kabupaten.replace(':provinsiId', provinsiId).replace(':kabupatenId', kabupatenId),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    loadedKabupaten[provinsiId] = false;
                    loadKabupaten(provinsiId);
                },
                error: function(xhr) {
                    console.error('Error deleting kabupaten:', xhr);
                    toastr.error(xhr.responseJSON?.message || 'Gagal menghapus kabupaten');
                }
            });
        }
    }
    
    window.deleteKecamatan = function(kabupatenId, kecamatanId) {
        if (confirm('Apakah Anda yakin ingin menghapus kecamatan ini?')) {
            $.ajax({
                url: routes.destroy.kecamatan.replace(':kabupatenId', kabupatenId).replace(':kecamatanId', kecamatanId),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    loadedKecamatan[kabupatenId] = false;
                    loadKecamatan(kabupatenId);
                },
                error: function(xhr) {
                    console.error('Error deleting kecamatan:', xhr);
                    toastr.error(xhr.responseJSON?.message || 'Gagal menghapus kecamatan');
                }
            });
        }
    }
    
    window.deleteKelurahan = function(kecamatanId, kelurahanId) {
        if (confirm('Apakah Anda yakin ingin menghapus kelurahan ini?')) {
            $.ajax({
                url: routes.destroy.kelurahan.replace(':kecamatanId', kecamatanId).replace(':kelurahanId', kelurahanId),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    loadedKelurahan[kecamatanId] = false;
                    loadKelurahan(kecamatanId);
                },
                error: function(xhr) {
                    console.error('Error deleting kelurahan:', xhr);
                    toastr.error(xhr.responseJSON?.message || 'Gagal menghapus kelurahan');
                }
            });
        }
    }

    // Clear errors when modal is hidden
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.error-text').text('');
    });
    
});
</script>
@endpush
