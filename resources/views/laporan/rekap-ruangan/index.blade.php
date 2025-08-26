@extends('layouts.backend', ['title' => 'Rekap Pegawai Per-Ruangan'])

@push('styles')
<style>
    #table-rekap-ruangan {
        border-collapse: collapse;
        border: 1px solid #dee2e6;
    }
    
    #table-rekap-ruangan th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-rekap-ruangan td {
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
    
    #table-rekap-ruangan thead th {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-rekap-ruangan tbody td {
        border: 1px solid #dee2e6;
        padding: 5px;
        font-size: 0.8rem;
    }
    
    #search-container-rekap-ruangan {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }
    
    #search-rekap-ruangan {
        border-radius: 6px;
    }
    
    #clear-search-rekap-ruangan {
        border-radius: 0 6px 6px 0;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Rekap Pegawai Per-Ruangan
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
                    <li class="breadcrumb-item text-muted">Rekap Pegawai Per-Ruangan</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printRekapRuangan()">
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
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Bulan:</label>
                                    <select class="form-select form-select-solid" id="bulan-rekap-ruangan" data-control="select2" data-placeholder="Pilih Bulan">
                                        <option value="">Pilih Bulan</option>
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
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Tahun:</label>
                                    <select class="form-select form-select-solid" id="tahun-rekap-ruangan" data-control="select2" data-placeholder="Pilih Tahun">
                                        <option value="">Pilih Tahun</option>
                                        @for ($i = date('Y'); $i >= 2020; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Ruangan:</label>
                                    <select class="form-select form-select-solid" id="ruangan-rekap-ruangan" data-control="select2" data-placeholder="Pilih Ruangan">
                                        <option value="x">TIDAK ADA RUANGAN</option>
                                        <option value="*">SEMUA RUANGAN</option>
                                        @foreach($ruangans as $ruangan)
                                            <option value="{{ $ruangan->kd_ruangan }}">{{ $ruangan->ruangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">&nbsp;</label>
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="lihatRekapRuangan()">
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
                                    <h4 class="fw-bold text-gray-800" id="header-rekap-ruangan">REKAP PEGAWAI PER-RUANGAN</h4>
                                    <p class="text-muted" id="periode-rekap-ruangan">Silakan pilih bulan, tahun dan ruangan untuk melihat data</p>
                                </div>
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-rekap-ruangan">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Memuat data...</p>
                        </div>

                        <!-- Search Section -->
                        <div class="row mb-4" id="search-container-rekap-ruangan" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pencarian:</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ki-outline ki-magnifier fs-3"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-solid" id="search-rekap-ruangan" placeholder="Cari nama, NIP, ruangan, atau data lainnya...">
                                    <button class="btn btn-light-danger" type="button" id="clear-search-rekap-ruangan">
                                        <i class="ki-outline ki-cross fs-3"></i>
                                    </button>
                                </div>
                                <div class="form-text">Ketik minimal 3 karakter untuk mencari data</div>
                                <div class="mt-2" id="search-info-rekap-ruangan" style="display: none;">
                                    <small class="text-primary fw-bold">
                                        <i class="ki-outline ki-information fs-6"></i>
                                        <span id="search-results-count">0</span> hasil ditemukan untuk pencarian "<span id="search-term"></span>"
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive" id="table-container-rekap-ruangan" style="display: none;">
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="table-rekap-ruangan">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">No.</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG / ID Peg.</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">L/P</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;"><u>NIK</u><br>No. ASKES/BPJS</th>
                                        <th colspan="3" style="text-align:center; vertical-align: middle;">Pendidikan terakhir</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Jenis tenaga</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Sub. Jenis tenaga<br>Ruangan</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Status kerja</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center; vertical-align: middle;">Jenjang</th>
                                        <th style="text-align:center; vertical-align: middle;">Program studi</th>
                                        <th style="text-align:center; vertical-align: middle;">Lulus tahun</th>
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
            // Set default bulan dan tahun ke bulan dan tahun sekarang
            $('#bulan-rekap-ruangan').val('{{ str_pad(date("m"), 2, "0", STR_PAD_LEFT) }}');
            $('#tahun-rekap-ruangan').val('{{ date("Y") }}');
            
            // Set default ruangan ke semua ruangan
            $('#ruangan-rekap-ruangan').val('*');
            
            // Inisialisasi DataTable
            initDataTable();
        });

        function initDataTable() {
            if (table) {
                table.destroy();
            }
            
            table = $('#table-rekap-ruangan').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 800,
                searching: false, // Disable default search to use custom search
                ajax: {
                    url: '{{ route("admin.laporan.rekap-ruangan.index") }}',
                    data: function(d) {
                        d.bulan = $('#bulan-rekap-ruangan').val();
                        d.tahun = $('#tahun-rekap-ruangan').val();
                        d.ruangan = $('#ruangan-rekap-ruangan').val();
                        d.search.value = $('#search-rekap-ruangan').val(); // Custom search value
                    },
                    dataSrc: function(json) {
                        // Update search info
                        updateSearchInfo(json);
                        return json.data;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_lengkap', name: 'nama_lengkap', orderable: false, searchable: true },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin' },
                    { data: 'nik_askes', name: 'nik_askes', orderable: false, searchable: true },
                    { data: 'jenjang_pendidikan', name: 'jenjang_pendidikan', orderable: false, searchable: true },
                    { data: 'program_studi', name: 'program_studi', orderable: false, searchable: true },
                    { data: 'tahun_lulus', name: 'tahun_lulus', orderable: false, searchable: true },
                    { data: 'jenis_tenaga', name: 'jenis_tenaga', searchable: true },
                    { data: 'sub_jenis_ruangan', name: 'sub_jenis_ruangan', orderable: false, searchable: true },
                    { data: 'status_kerja', name: 'status_kerja', orderable: false, searchable: true }
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
                }
            });
            
            // Setup custom search functionality
            setupCustomSearch();
        }

        function lihatRekapRuangan() {
            const bulan = $('#bulan-rekap-ruangan').val();
            const tahun = $('#tahun-rekap-ruangan').val();
            const ruangan = $('#ruangan-rekap-ruangan').val();

            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }

            // Show loading
            $('#loader-rekap-ruangan').show();
            $('#table-container-rekap-ruangan').hide();
            $('#search-container-rekap-ruangan').hide();

            // Update header
            const ruanganNama = getRuanganName(ruangan);
            const bulanNama = getMonthName(bulan);
            $('#header-rekap-ruangan').text('REKAP PEGAWAI PER-RUANGAN');
            $('#periode-rekap-ruangan').text('Ruangan: ' + ruanganNama + ' - Periode: ' + bulanNama + ' ' + tahun);

            // Reload table
            if (table) {
                table.ajax.reload(function() {
                    $('#loader-rekap-ruangan').hide();
                    $('#table-container-rekap-ruangan').show();
                    $('#search-container-rekap-ruangan').show();
                });
            }
        }

        function setupCustomSearch() {
            let searchTimer;
            
            // Search input event
            $('#search-rekap-ruangan').on('keyup', function() {
                const searchValue = $(this).val();
                
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        table.ajax.reload();
                    }
                }, 800);
            });
            
            // Clear search button event
            $('#clear-search-rekap-ruangan').on('click', function() {
                $('#search-rekap-ruangan').val('');
                $('#search-info-rekap-ruangan').hide();
                table.ajax.reload();
            });
            
            // Enter key event
            $('#search-rekap-ruangan').on('keypress', function(e) {
                if (e.which === 13) {
                    const searchValue = $(this).val();
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        table.ajax.reload();
                    }
                }
            });
        }

        function updateSearchInfo(json) {
            const searchValue = $('#search-rekap-ruangan').val();
            
            if (searchValue && searchValue.length >= 3) {
                $('#search-term').text(searchValue);
                $('#search-results-count').text(json.recordsFiltered || 0);
                $('#search-info-rekap-ruangan').show();
            } else {
                $('#search-info-rekap-ruangan').hide();
            }
        }

        function getRuanganName(ruangan) {
            const select = document.getElementById('ruangan-rekap-ruangan');
            const selectedOption = select.options[select.selectedIndex];
            return selectedOption.text;
        }

        function getMonthName(month) {
            const months = [
                '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return months[parseInt(month)];
        }

        function printRekapRuangan() {
            const bulan = $('#bulan-rekap-ruangan').val();
            const tahun = $('#tahun-rekap-ruangan').val();
            const ruangan = $('#ruangan-rekap-ruangan').val();

            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }

            // Buka halaman print dalam window/tab baru
            const printUrl = `{{ route('admin.laporan.rekap-ruangan.print') }}?bln=${bulan}&thn=${tahun}&ruang=${ruangan}`;
            window.open(printUrl, '_blank');
        }

        function getMonthName(monthNumber) {
            const months = {
                '01': 'Januari', '02': 'Februari', '03': 'Maret',
                '04': 'April', '05': 'Mei', '06': 'Juni',
                '07': 'Juli', '08': 'Agustus', '09': 'September',
                '10': 'Oktober', '11': 'November', '12': 'Desember'
            };
            return months[monthNumber] || '';
        }
    </script>
@endpush
