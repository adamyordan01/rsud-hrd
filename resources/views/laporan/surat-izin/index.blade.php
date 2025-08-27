@extends('layouts.backend', ['title' => 'Laporan Surat Izin'])

@push('styles')
<style>
    #table-surat-izin th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-surat-izin td {
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

    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Laporan Surat Izin
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
                    <li class="breadcrumb-item text-muted">Surat Izin</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-success btn-sm" onclick="exportPDF()">
                    <i class="ki-duotone ki-file-down fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Export PDF
                </button>
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printSuratIzin()">
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
                                <span class="card-label fw-bold fs-3 mb-1">Data Surat Izin</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Filter dan lihat data surat izin karyawan</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="filter-container">
                            <div class="row align-items-end">
                                <div class="col-md-3 mb-3">
                                    <label for="ruangan-surat-izin" class="form-label fw-semibold">Ruangan/Unit Kerja</label>
                                    <select class="form-select" id="ruangan-surat-izin" data-placeholder="Pilih Ruangan">
                                        <option value="">Semua Ruangan</option>
                                        @foreach($ruangan as $item)
                                            <option value="{{ $item->kd_ruangan }}">{{ $item->ruangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="kategori-surat-izin" class="form-label fw-semibold">Kategori Izin</label>
                                    <select class="form-select" id="kategori-surat-izin" data-placeholder="Pilih Kategori">
                                        <option value="">Semua Kategori</option>
                                        @foreach($kategoriIzin as $item)
                                            <option value="{{ $item->kd_kategori }}">{{ $item->kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-2 mb-3">
                                    <label for="bulan-surat-izin" class="form-label fw-semibold">Bulan</label>
                                    <select class="form-select" id="bulan-surat-izin">
                                        <option value="">Semua Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2 mb-3">
                                    <label for="tahun-surat-izin" class="form-label fw-semibold">Tahun</label>
                                    <select class="form-select" id="tahun-surat-izin">
                                        <option value="">Semua Tahun</option>
                                        @for($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                
                                <div class="col-md-2 mb-3">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="lihatSuratIzin()">
                                            <i class="ki-duotone ki-magnifier fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Lihat Data
                                        </button>
                                        <button type="button" class="btn btn-light-warning btn-sm" onclick="resetFilter()">
                                            <i class="ki-duotone ki-arrow-counterclockwise fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Reset
                                        </button>
                                    </div>
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
                            <table class="table table-striped table-hover align-middle gs-7" id="table-surat-izin">
                                <thead class="fw-bold text-gray-800 border-bottom-2 border-gray-200">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th style="width: 180px;">Nama Karyawan</th>
                                        <th style="width: 120px;">Unit Kerja</th>
                                        {{-- <th style="width: 100px;">Tgl Surat</th> --}}
                                        {{-- <th style="width: 120px;">No Surat</th> --}}
                                        <th style="width: 140px;">Periode Izin</th>
                                        <th style="width: 80px;">Lama Izin</th>
                                        <th style="width: 120px;">Jenis Surat</th>
                                        <th style="width: 120px;">Kategori Izin</th>
                                        <th>Alasan</th>
                                    </tr>
                                </thead>
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
    // Set default bulan ke bulan sekarang
    $('#bulan-surat-izin').val('{{ date("n") }}');
    
    // Initialize Select2 untuk dropdown yang dapat dicari
    $('#ruangan-surat-izin').select2({
        placeholder: 'Pilih Ruangan/Unit Kerja',
        allowClear: true,
        width: '100%'
    });
    
    $('#kategori-surat-izin').select2({
        placeholder: 'Pilih Kategori Izin',
        allowClear: true,
        width: '100%'
    });
    
    // Inisialisasi DataTable
    initDataTable();
});

function initDataTable() {
    if (table) {
        table.destroy();
    }
    
    table = $('#table-surat-izin').DataTable({
        processing: true,
        serverSide: true,
        searchDelay: 800,
        ajax: {
            url: "{{ route('admin.laporan.surat-izin.index') }}",
            data: function(d) {
                d.kd_ruangan = $('#ruangan-surat-izin').val();
                d.kd_kategori = $('#kategori-surat-izin').val();
                d.bulan = $('#bulan-surat-izin').val();
                d.tahun = $('#tahun-surat-izin').val();
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
                           '<small class="text-muted">' + (row.nip_display || 'No NIP') + '</small>';
                }
            },
            {
                data: 'unit_kerja',
                name: 'unit_kerja'
            },
            // {
            //     data: 'tanggal_surat',
            //     name: 'tanggal_surat',
            //     className: 'text-center'
            // },
            // {
            //     data: 'no_surat',
            //     name: 'no_surat'
            // },
            {
                data: 'periode_izin',
                name: 'periode_izin',
                className: 'text-center'
            },
            {
                data: 'lama_izin',
                name: 'lama_izin',
                className: 'text-center'
            },
            {
                data: 'jenis_surat',
                name: 'jenis_surat'
            },
            {
                data: 'kategori_izin',
                name: 'kategori_izin'
            },
            {
                data: 'alasan',
                name: 'alasan',
                render: function(data) {
                    return data ? (data.length > 50 ? data.substring(0, 50) + '...' : data) : '-';
                }
            }
        ],
        order: [[3, 'desc'], [1, 'asc']], // Order by tanggal surat desc, then nama asc
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

function lihatSuratIzin() {
    const kdRuangan = $('#ruangan-surat-izin').val();
    const kdKategori = $('#kategori-surat-izin').val();
    const bulan = $('#bulan-surat-izin').val();
    const tahun = $('#tahun-surat-izin').val();

    // Cek data terlebih dahulu
    $.ajax({
        url: '{{ route("admin.laporan.surat-izin.check-data") }}',
        type: 'GET',
        data: {
            kd_ruangan: kdRuangan,
            kd_kategori: kdKategori,
            bulan: bulan,
            tahun: tahun
        },
        success: function(response) {
            if (response.status === 'success') {
                // Reload DataTable dengan filter baru
                table.ajax.reload();
                
                if (response.data_count > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Ditemukan',
                        text: `Ditemukan ${response.data_count} data surat izin`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Data Kosong',
                        text: 'Tidak ada data surat izin sesuai filter yang dipilih'
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

function printSuratIzin() {
    const kdRuangan = $('#ruangan-surat-izin').val();
    const kdKategori = $('#kategori-surat-izin').val();
    const bulan = $('#bulan-surat-izin').val();
    const tahun = $('#tahun-surat-izin').val();

    // Buat parameter URL
    const params = new URLSearchParams();
    if (kdRuangan) params.append('kd_ruangan', kdRuangan);
    if (kdKategori) params.append('kd_kategori', kdKategori);
    if (bulan) params.append('bulan', bulan);
    if (tahun) params.append('tahun', tahun);

    // Buka halaman print dalam window/tab baru
    const printUrl = `{{ route('admin.laporan.surat-izin.print') }}?${params.toString()}`;
    window.open(printUrl, '_blank');
}

function exportPDF() {
    const kdRuangan = $('#ruangan-surat-izin').val();
    const kdKategori = $('#kategori-surat-izin').val();
    const bulan = $('#bulan-surat-izin').val();
    const tahun = $('#tahun-surat-izin').val();

    // Buat parameter URL
    const params = new URLSearchParams();
    if (kdRuangan) params.append('kd_ruangan', kdRuangan);
    if (kdKategori) params.append('kd_kategori', kdKategori);
    if (bulan) params.append('bulan', bulan);
    if (tahun) params.append('tahun', tahun);

    // Download PDF
    const pdfUrl = `{{ route('admin.laporan.surat-izin.pdf') }}?${params.toString()}`;
    window.open(pdfUrl, '_blank');
}

function resetFilter() {
    $('#ruangan-surat-izin').val('').trigger('change');
    $('#kategori-surat-izin').val('').trigger('change');
    $('#bulan-surat-izin').val('{{ date("n") }}');
    $('#tahun-surat-izin').val('{{ date("Y") }}');
    table.ajax.reload();
}

function getMonthName(monthNumber) {
    const months = {
        '1': 'Januari', '2': 'Februari', '3': 'Maret', '4': 'April',
        '5': 'Mei', '6': 'Juni', '7': 'Juli', '8': 'Agustus',
        '9': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
    };
    return months[monthNumber] || '';
}
</script>
@endpush
