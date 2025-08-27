@extends('layouts.backend', ['title' => 'Laporan Berdasarkan Golongan'])

@push('styles')
<style>
    #table-golongan {
        border-collapse: collapse;
        border: 1px solid #dee2e6;
    }
    
    #table-golongan th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-golongan td {
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
    
    .symbol img {
        border-radius: 50%;
        object-fit: cover;
    }
    
    #table-golongan thead th {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-golongan tbody td {
        border: 1px solid #dee2e6;
        padding: 5px;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Laporan Berdasarkan Golongan
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
                    <li class="breadcrumb-item text-muted">Berdasarkan Golongan</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printGolongan()">
                    <i class="ki-outline ki-printer fs-2"></i>
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
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="fw-bold text-gray-800">Filter Data</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="filter-container">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Golongan:</label>
                                    <select class="form-select form-select-solid" id="golongan" data-control="select2" data-placeholder="Pilih Golongan" data-allow-clear="true">
                                        <option value="">Semua Golongan</option>
                                        @foreach($golonganList as $gol)
                                            <option value="{{ $gol->kd_gol }}">{{ $gol->kd_gol }} - {{ $gol->pangkat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">&nbsp;</label>
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="lihatGolongan()">
                                            <i class="ki-outline ki-magnifier fs-3"></i>
                                            Lihat Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Header RSUD -->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <h4 class="fw-bold text-gray-800" id="header-golongan">LAPORAN BERDASARKAN GOLONGAN</h4>
                                    <p class="text-muted" id="periode-golongan">Silakan pilih filter untuk melihat data</p>
                                </div>
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-golongan">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Memuat data...</p>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive" id="table-container-golongan" style="display: none;">
                            <!-- Search Box -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <label class="form-label fw-semibold me-2">Pencarian:</label>
                                    <input type="text" class="form-control form-control-sm" id="search-golongan" placeholder="Cari nama, NIP, ruangan..." style="width: 300px;">
                                </div>
                                <div class="text-muted">
                                    <small id="total-data-golongan">Total: 0 data</small>
                                </div>
                            </div>
                            
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="table-golongan">
                                <thead>
                                    <tr>
                                        <th rowspan="3" style="text-align:center; vertical-align: middle;">No.</th>
                                        <th rowspan="3" style="text-align:center; vertical-align: middle;">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG / ID Peg.</th>
                                        <th rowspan="3" style="text-align:center; vertical-align: middle;">L/P</th>
                                        <th rowspan="3" style="text-align:center; vertical-align: middle;"><u>NIK</u><br>No. ASKES/BPJS</th>
                                        <th colspan="4" style="text-align:center; vertical-align: middle;">Kepangkatan sekarang</th>
                                        <th colspan="3" style="text-align:center; vertical-align: middle;">Pendidikan terakhir</th>
                                        <th rowspan="3" style="text-align:center; vertical-align: middle;">Jenis tenaga</th>
                                        <th rowspan="3" style="text-align:center; vertical-align: middle;">Sub. Jenis tenaga<br>Ruangan</th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Pangkat / Gol.</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;" width="100">TMT</th>
                                        <th colspan="2" style="text-align:center; vertical-align: middle;">Masa kerja</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Jenjang</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Program studi</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Lulus tahun</th>
                                    </tr>
                                    <tr>
                                        <th>Thn.</th>
                                        <th>Bln.</th>
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
@endsection

@push('scripts')
    <script>
        let table;
        
        $(document).ready(function() {
            // Ready
        });

        function initDataTable() {
            if (table) {
                table.destroy();
            }
            
            table = $('#table-golongan').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 800,
                searching: false, // Disable default search, use custom
                ajax: {
                    url: '{{ route("admin.laporan.golongan.index") }}',
                    data: function(d) {
                        d.golongan = $('#golongan').val();
                        d.search = {
                            value: $('#search-golongan').val()
                        };
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_lengkap', name: 'nama_lengkap', orderable: false, searchable: false },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin' },
                    { data: 'nik_askes', name: 'nik_askes', orderable: false, searchable: false },
                    { data: 'pangkat_golongan', name: 'pangkat_golongan' },
                    { data: 'tmt_pangkat', name: 'tmt_pangkat' },
                    { data: 'masa_kerja_thn', name: 'masa_kerja_thn' },
                    { data: 'masa_kerja_bln', name: 'masa_kerja_bln' },
                    { data: 'jenjang_pendidikan', name: 'jenjang_pendidikan', orderable: false, searchable: false },
                    { data: 'program_studi', name: 'program_studi', orderable: false, searchable: false },
                    { data: 'tahun_lulus', name: 'tahun_lulus', orderable: false, searchable: false },
                    { data: 'jenis_tenaga', name: 'jenis_tenaga' },
                    { data: 'sub_jenis_ruangan', name: 'sub_jenis_ruangan', orderable: false, searchable: false }
                ],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    processing: "Sedang memproses...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Data tidak tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                drawCallback: function(settings) {
                    // Update total data counter
                    const info = this.api().page.info();
                    const totalData = info.recordsFiltered || 0;
                    $('#total-data-golongan').text(`Total: ${totalData} data`);
                },
                initComplete: function(settings, json) {
                    // Update total data counter on initial load
                    const info = this.api().page.info();
                    const totalData = info.recordsFiltered || 0;
                    $('#total-data-golongan').text(`Total: ${totalData} data`);
                }
            });
            
            // Custom search functionality
            $('#search-golongan').on('keyup', function() {
                table.ajax.reload(function() {
                    // Update counter after search
                    updateDataCounter();
                });
            });
            
            // Function to update data counter
            function updateDataCounter() {
                if (table) {
                    const info = table.page.info();
                    const totalData = info.recordsFiltered || 0;
                    $('#total-data-golongan').text(`Total: ${totalData} data`);
                }
            }
        }

        function lihatGolongan() {
            // Show loading
            $('#loader-golongan').show();
            $('#table-container-golongan').hide();

            // Update header
            const golonganNama = $('#golongan option:selected').text();
            
            $('#header-golongan').text('LAPORAN BERDASARKAN GOLONGAN PNS DAN PPPK');
            let periodeText = 'Golongan: ' + golonganNama;
            $('#periode-golongan').text(periodeText);

            // Initialize or reload table
            if (!table) {
                initDataTable();
                // Show table after initialization
                setTimeout(function() {
                    $('#loader-golongan').hide();
                    $('#table-container-golongan').show();
                    
                    // Update counter after initial load
                    setTimeout(function() {
                        const info = table.page.info();
                        const totalData = info.recordsFiltered || 0;
                        $('#total-data-golongan').text(`Total: ${totalData} data`);
                    }, 500);
                }, 1000);
            } else {
                table.ajax.reload(function() {
                    $('#loader-golongan').hide();
                    $('#table-container-golongan').show();
                    
                    // Update counter after reload
                    setTimeout(function() {
                        const info = table.page.info();
                        const totalData = info.recordsFiltered || 0;
                        $('#total-data-golongan').text(`Total: ${totalData} data`);
                    }, 500);
                });
            }
        }

        function printGolongan() {
            const golongan = $('#golongan').val();

            // Buka halaman print dalam window/tab baru
            const printUrl = `{{ route('admin.laporan.golongan.print') }}?golongan=${golongan}`;
            window.open(printUrl, '_blank');
        }
    </script>
@endpush
