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
        } else {
            content.addClass('show');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            
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
        } else {
            content.addClass('show');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            
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
        } else {
            content.addClass('show');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            
            // Load kelurahan if not already loaded
            if (!loadedKelurahan[kecamatanId]) {
                loadKelurahan(kecamatanId);
            }
        }
    }

    // Load functions
    function loadKabupaten(provinsiId) {
        const container = $(`#kabupaten-${provinsiId}`);
        
        $.ajax({
            url: `/admin/settings/wilayah/load/kabupaten/${provinsiId}`,
            type: 'GET',
            success: function(response) {
                let html = '';
                if (response.data && response.data.length > 0) {
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
                                    <div class="text-center py-3">
                                        <i class="fas fa-spinner fa-spin"></i> Loading kecamatan...
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="text-muted text-center py-3">Tidak ada data kabupaten</div>';
                }
                
                container.html(html);
                loadedKabupaten[provinsiId] = true;
            },
            error: function(xhr) {
                container.html('<div class="text-danger text-center py-3">Error loading kabupaten</div>');
            }
        });
    }

    function loadKecamatan(kabupatenId) {
        const container = $(`#kecamatan-${kabupatenId}`);
        
        $.ajax({
            url: `/admin/settings/wilayah/load/kecamatan/${kabupatenId}`,
            type: 'GET',
            success: function(response) {
                let html = '';
                if (response.data && response.data.length > 0) {
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
                                    <div class="text-center py-3">
                                        <i class="fas fa-spinner fa-spin"></i> Loading kelurahan...
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="text-muted text-center py-3">Tidak ada data kecamatan</div>';
                }
                
                container.html(html);
                loadedKecamatan[kabupatenId] = true;
            },
            error: function(xhr) {
                container.html('<div class="text-danger text-center py-3">Error loading kecamatan</div>');
            }
        });
    }

    function loadKelurahan(kecamatanId) {
        const container = $(`#kelurahan-${kecamatanId}`);
        
        $.ajax({
            url: `/admin/settings/wilayah/load/kelurahan/${kecamatanId}`,
            type: 'GET',
            success: function(response) {
                let html = '';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(kel) {
                        html += `
                            <div class="wilayah-card ms-4" data-kelurahan="${kel.kd_kelurahan}">
                                <div class="wilayah-header">
                                    <div>
                                        <span class="level-badge kelurahan">KELURAHAN</span>
                                        <strong>${kel.kelurahan}</strong>
                                        ${kel.kode_pos ? ` <span class="badge badge-light-info">${kel.kode_pos}</span>` : ''}
                                        <span class="badge badge-${kel.aktif ? 'success' : 'danger'}">${kel.aktif ? 'Aktif' : 'Tidak Aktif'}</span>
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
                    html = '<div class="text-muted text-center py-3">Tidak ada data kelurahan</div>';
                }
                
                container.html(html);
                loadedKelurahan[kecamatanId] = true;
            },
            error: function(xhr) {
                container.html('<div class="text-danger text-center py-3">Error loading kelurahan</div>');
            }
        });
    }

    // Add functions
    window.addProvinsi = function() {
        $('#modalProvinsiTitle').text('Tambah Provinsi');
        $('#formProvinsiMethod').val('POST');
        $('#provinsiForm')[0].reset();
        $('.error-text').text('');
        $('#provinsiModal').modal('show');
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

    // AJAX Form handlers will be added here...

});
