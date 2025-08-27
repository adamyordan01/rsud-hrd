@extends('layouts.backend', ['title' => 'Laporan Jabatan Fungsional'])

@push('styles')
<style>
    #table-jabatan-fungsional th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-jabatan-fungsional td {
        font-size: 11px;
        vertical-align: top;
        border: 1px solid #dee2e6;
        padding: 5px;
    }
    
    .filter-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .loader-container {
        text-align: center;
        padding: 50px;
        display: none;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Laporan Jabatan Fungsional
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Laporan</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Jabatan Fungsional</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printJabatanFungsional()">
                    <i class="ki-duotone ki-printer fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                        <div class="card-title">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Filter Laporan</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="filter-container">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Bulan:</label>
                                    <select class="form-select" id="bulan-jabatan-fungsional" name="bulan">
                                        <option value="">Semua Bulan</option>
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tahun:</label>
                                    <select class="form-select" id="tahun-jabatan-fungsional" name="tahun">
                                        <option value="">Semua Tahun</option>
                                        @for($i = date('Y'); $i >= 2020; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary" onclick="lihatJabatanFungsional()">
                                        <i class="ki-duotone ki-magnifier fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Lihat Data
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2" onclick="resetFilter()">
                                        <i class="ki-duotone ki-arrows-circle fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="loader-container" id="loader">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat data...</p>
                        </div>
                        
                        <div class="table-responsive">
                            <table id="table-jabatan-fungsional" class="table table-striped table-row-bordered gy-5 gs-7">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800">
                                        <th style="width: 40px;">No</th>
                                        <th style="width: 200px;">Nama Lengkap<br>NIP / ID Pegawai</th>
                                        <th style="width: 60px;">L/P</th>
                                        <th style="width: 150px;">Tempat & Tanggal Lahir</th>
                                        <th style="width: 100px;">Pangkat / Gol</th>
                                        <th style="width: 80px;">TMT</th>
                                        <th style="width: 150px;">Jabatan Fungsional</th>
                                        <th style="width: 120px;">Pendidikan Terakhir<br>Jurusan</th>
                                        <th style="width: 150px;">Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan dimuat via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let table;

$(document).ready(function() {
    // Set default bulan dan tahun ke bulan dan tahun sekarang
    $('#bulan-jabatan-fungsional').val('{{ str_pad(date("m"), 2, "0", STR_PAD_LEFT) }}');
    $('#tahun-jabatan-fungsional').val('{{ date("Y") }}');
    
    // Inisialisasi DataTable
    initDataTable();
});

function initDataTable() {
    if (table) {
        table.destroy();
    }
    
    table = $('#table-jabatan-fungsional').DataTable({
        processing: true,
        serverSide: true,
        searchDelay: 800,
        ajax: {
            url: "{{ route('admin.laporan.jabatan-fungsional.index') }}",
            data: function(d) {
                d.bulan = $('#bulan-jabatan-fungsional').val();
                d.tahun = $('#tahun-jabatan-fungsional').val();
            },
            beforeSend: function() {
                $('#loader').show();
            },
            complete: function() {
                $('#loader').hide();
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'nama_lengkap',
                name: 'nama_lengkap',
                render: function(data, type, row) {
                    return '<strong>' + (row.nama_lengkap || '-') + '</strong><br>' +
                           '<small class="text-muted">' + (row.nip || 'No NIP') + '</small>';
                }
            },
            {
                data: 'jenis_kelamin',
                name: 'jenis_kelamin',
                className: 'text-center',
                render: function(data) {
                    return data === 'L' ? 'L' : (data === 'P' ? 'P' : '-');
                }
            },
            {
                data: 'tempat_lahir',
                name: 'tempat_lahir',
                render: function(data, type, row) {
                    let tempat = row.tempat_lahir || '-';
                    let tanggal = row.tanggal_lahir ? new Date(row.tanggal_lahir).toLocaleDateString('id-ID') : '-';
                    return tempat + '<br><small>' + tanggal + '</small>';
                }
            },
            {
                data: 'pangkat',
                name: 'pangkat',
                render: function(data, type, row) {
                    return (row.pangkat || '-') + '<br><small>(' + (row.golongan || '-') + ')</small>';
                }
            },
            {
                data: 'tmt_golongan',
                name: 'tmt_golongan',
                className: 'text-center',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            {
                data: 'jab_fung',
                name: 'jab_fung'
            },
            {
                data: 'pendidikan',
                name: 'pendidikan',
                render: function(data, type, row) {
                    return (row.pendidikan || '-') + '<br><small>' + (row.jurusan || '-') + '</small>';
                }
            },
            {
                data: 'ruangan',
                name: 'ruangan'
            }
        ],
        order: [[8, 'asc'], [6, 'asc']], // Order by ruangan, then jab_fung
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        dom: '<"top"<"d-flex justify-content-between"<"d-flex align-items-center"l><"d-flex align-items-center"f>>>rt<"bottom"<"d-flex justify-content-between"ip>>',
        language: {
            processing: "Memproses...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            loadingRecords: "Memuat...",
            zeroRecords: "Tidak ada data yang ditemukan",
            emptyTable: "Tidak ada data tersedia",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Selanjutnya",
                last: "Terakhir"
            }
        },
        initComplete: function() {
            console.log('DataTable initialized successfully');
        }
    });
}

function lihatJabatanFungsional() {
    const bulan = $('#bulan-jabatan-fungsional').val();
    const tahun = $('#tahun-jabatan-fungsional').val();

    if (!bulan || !tahun) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
        });
        return;
    }

    // Cek data terlebih dahulu
    $.ajax({
        url: '{{ route("admin.laporan.jabatan-fungsional.check-data") }}',
        type: 'GET',
        data: {
            bulan: bulan,
            tahun: tahun
        },
        success: function(response) {
            if (response.status === 'success') {
                if (response.data_count > 0) {
                    // Reload DataTable dengan filter baru
                    table.ajax.reload();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Ditemukan',
                        text: `Ditemukan ${response.data_count} data jabatan fungsional untuk periode ${getMonthName(bulan)} ${tahun}`
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Data Kosong',
                        text: `Tidak ada data jabatan fungsional untuk periode ${getMonthName(bulan)} ${tahun}`
                    });
                }
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengecek data!'
            });
        }
    });
}

function printJabatanFungsional() {
    const bulan = $('#bulan-jabatan-fungsional').val();
    const tahun = $('#tahun-jabatan-fungsional').val();

    if (!bulan || !tahun) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
        });
        return;
    }

    // Buka halaman print dalam window/tab baru
    const printUrl = `{{ route('admin.laporan.jabatan-fungsional.print') }}?bulan=${bulan}&tahun=${tahun}`;
    window.open(printUrl, '_blank');
}

function resetFilter() {
    $('#bulan-jabatan-fungsional').val('{{ str_pad(date("m"), 2, "0", STR_PAD_LEFT) }}');
    $('#tahun-jabatan-fungsional').val('{{ date("Y") }}');
    table.ajax.reload();
}

function getMonthName(monthNumber) {
    const months = {
        '01': 'Januari', '02': 'Februari', '03': 'Maret', '04': 'April',
        '05': 'Mei', '06': 'Juni', '07': 'Juli', '08': 'Agustus',
        '09': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
    };
    return months[monthNumber] || '';
}
</script>
@endpush
