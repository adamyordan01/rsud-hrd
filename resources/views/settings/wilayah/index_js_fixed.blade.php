@push('js')
<script>
$(document).ready(function() {
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

    // Global functions - attach to window object so they can be called from onclick
    window.refreshData = function() {
        location.reload();
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
                                <div class="wilayah-card ms-4" data-kabupaten="${kab.kd_kabupaten}">
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
                                <div class="wilayah-card ms-4" data-kecamatan="${kec.kd_kecamatan}">
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
                        setTimeout(() => location.reload(), 1000);
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
                        setTimeout(() => location.reload(), 1000);
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
                        setTimeout(() => location.reload(), 1000);
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
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

}); // End document.ready
</script>
@endpush
