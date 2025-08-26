@extends('layouts.backend', ['title' => 'Rekap Pegawai Aktif'])

@push('styles')
<style>
    #table-rekap-pegawai {
        border-collapse: collapse;
        border: 1px solid #dee2e6;
    }
    
    #table-rekap-pegawai th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-rekap-pegawai td {
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
    
    #table-rekap-pegawai thead th {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-rekap-pegawai tbody td {
        border: 1px solid #dee2e6;
        padding: 5px;
        font-size: 0.8rem;
    }
    
    .pns-header {
        display: table-header-group;
    }
    
    .nonpns-header {
        display: none;
    }
    
    .pangkat-column {
        display: table-cell;
    }
    
    /* Style untuk status non-PNS */
    .status-non-pns .pns-header {
        display: none !important;
    }
    
    .status-non-pns .nonpns-header {
        display: table-header-group !important;
    }
    
    .status-non-pns .pangkat-column {
        display: none !important;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Rekap Pegawai Aktif
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
                    <li class="breadcrumb-item text-muted">Rekap Pegawai Aktif</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printRekapPegawai()">
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
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Bulan:</label>
                                    <select class="form-select form-select-solid" id="bulan-rekap-pegawai">
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
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tahun:</label>
                                    <select class="form-select form-select-solid" id="tahun-rekap-pegawai">
                                        <option value="">Pilih Tahun</option>
                                        @for ($i = date('Y'); $i >= 2020; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Status Pegawai:</label>
                                    <select class="form-select form-select-solid" id="status-rekap-pegawai">
                                        <option value="">Semua Pegawai</option>
                                        <option value="1">PNS</option>
                                        <option value="2">Honor</option>
                                        <option value="3">Kontrak</option>
                                        <option value="4">Part Time</option>
                                        <option value="7">PPPK</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">&nbsp;</label>
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="lihatRekapPegawai()">
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
                                    <h4 class="fw-bold text-gray-800" id="header-rekap-pegawai">REKAP PEGAWAI AKTIF</h4>
                                    <p class="text-muted" id="periode-rekap-pegawai">Silakan pilih bulan, tahun dan status pegawai untuk melihat data</p>
                                </div>
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-rekap-pegawai">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Memuat data...</p>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive" id="table-container-rekap-pegawai" style="display: none;">
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="table-rekap-pegawai">
                                <thead class="pns-header">
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
                                <thead class="nonpns-header" style="display: none;">
                                    <tr>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">No.</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama<br>Tempat & Tanggal Lahir<br>ID Peg.</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">L/P</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;"><u>NIK</u><br>No. ASKES/BPJS</th>
                                        <th colspan="3" style="text-align:center; vertical-align: middle;">Pendidikan terakhir</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Jenis tenaga</th>
                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Sub. Jenis tenaga<br>Ruangan</th>
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
            $('#bulan-rekap-pegawai').val('{{ str_pad(date("m"), 2, "0", STR_PAD_LEFT) }}');
            $('#tahun-rekap-pegawai').val('{{ date("Y") }}');
            
            // Set default status ke PNS
            $('#status-rekap-pegawai').val('1');
            
            // Inisialisasi DataTable
            initDataTable();
        });

        function initDataTable() {
            if (table) {
                table.destroy();
            }
            
            table = $('#table-rekap-pegawai').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 800,
                ajax: {
                    url: '{{ route("admin.laporan.rekap-pegawai.index") }}',
                    data: function(d) {
                        d.bulan = $('#bulan-rekap-pegawai').val();
                        d.tahun = $('#tahun-rekap-pegawai').val();
                        d.status = $('#status-rekap-pegawai').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_lengkap', name: 'nama_lengkap', orderable: false, searchable: false },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin' },
                    { data: 'nik_askes', name: 'nik_askes', orderable: false, searchable: false },
                    { data: 'pangkat_golongan', name: 'pangkat_golongan', className: 'pangkat-column' },
                    { data: 'tmt_pangkat', name: 'tmt_pangkat', className: 'pangkat-column' },
                    { data: 'masa_kerja_thn', name: 'masa_kerja_thn', className: 'pangkat-column' },
                    { data: 'masa_kerja_bln', name: 'masa_kerja_bln', className: 'pangkat-column' },
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
                }
            });
            
            // Update table headers setelah DataTable diinisialisasi
            setTimeout(function() {
                updateTableHeaders();
            }, 100);
        }

        function updateTableHeaders() {
            const status = $('#status-rekap-pegawai').val();
            
            // Pastikan table sudah diinisialisasi
            if (!table) {
                return;
            }
            
            if (status == '2' || status == '3' || status == '4' || status == '') {
                // Honor, Kontrak, Part Time, atau Semua - gunakan header non-PNS
                $('#table-container-rekap-pegawai').addClass('status-non-pns');
                $('.pns-header').hide();
                $('.nonpns-header').show();
                $('.pangkat-column').hide();
                
                // Update kolom DataTable untuk non-PNS
                if (table.column) {
                    table.column(4).visible(false); // Pangkat
                    table.column(5).visible(false); // TMT
                    table.column(6).visible(false); // Masa Kerja Thn
                    table.column(7).visible(false); // Masa Kerja Bln
                }
            } else {
                // PNS dan PPPK - gunakan header PNS
                $('#table-container-rekap-pegawai').removeClass('status-non-pns');
                $('.pns-header').show();
                $('.nonpns-header').hide();
                $('.pangkat-column').show();
                
                // Update kolom DataTable untuk PNS/PPPK
                if (table.column) {
                    table.column(4).visible(true); // Pangkat
                    table.column(5).visible(true); // TMT
                    table.column(6).visible(true); // Masa Kerja Thn
                    table.column(7).visible(true); // Masa Kerja Bln
                }
            }
        }

        function lihatRekapPegawai() {
            const bulan = $('#bulan-rekap-pegawai').val();
            const tahun = $('#tahun-rekap-pegawai').val();
            const status = $('#status-rekap-pegawai').val();

            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }

            // Show loading
            $('#loader-rekap-pegawai').show();
            $('#table-container-rekap-pegawai').hide();

            // Update header
            const statusNama = getStatusName(status);
            const bulanNama = getMonthName(bulan);
            $('#header-rekap-pegawai').text('REKAP PEGAWAI AKTIF ' + statusNama);
            $('#periode-rekap-pegawai').text('Periode: ' + bulanNama + ' ' + tahun);

            // Update column visibility
            updateColumnVisibility(status);

            // Reload table
            if (table) {
                table.ajax.reload(function() {
                    $('#loader-rekap-pegawai').hide();
                    $('#table-container-rekap-pegawai').show();
                });
            }
        }

        function updateColumnVisibility(status) {
            if (table) {
                var pangkatColumns = [4, 5, 6, 7]; // Pangkat, TMT, Masa Kerja Thn, Masa Kerja Bln
                
                if (status === '1' || status === '7') { // PNS atau PPPK
                    pangkatColumns.forEach(function(colIndex) {
                        table.column(colIndex).visible(true);
                    });
                    $('#table-container-rekap-pegawai').removeClass('status-non-pns');
                    $('.pns-header').show();
                    $('.nonpns-header').hide();
                    $('.pangkat-column').show();
                } else { // Non-PNS (Honor, Kontrak, Part Time)
                    pangkatColumns.forEach(function(colIndex) {
                        table.column(colIndex).visible(false);
                    });
                    $('#table-container-rekap-pegawai').addClass('status-non-pns');
                    $('.pns-header').hide();
                    $('.nonpns-header').show();
                    $('.pangkat-column').hide();
                }
            }
        }

        function getStatusName(status) {
            switch(status) {
                case '1': return 'PNS';
                case '2': return 'Honor';
                case '3': return 'Kontrak';
                case '4': return 'Part Time';
                case '7': return 'PPPK';
                default: return 'Semua Pegawai';
            }
        }

        function getMonthName(month) {
            const months = [
                '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return months[parseInt(month)];
        }

        function printRekapPegawai() {
            const bulan = $('#bulan-rekap-pegawai').val();
            const tahun = $('#tahun-rekap-pegawai').val();
            const status = $('#status-rekap-pegawai').val();

            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }

            // Buka halaman print dalam window/tab baru
            const printUrl = `{{ route('admin.laporan.rekap-pegawai.print') }}?bln=${bulan}&thn=${tahun}&status=${status}`;
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

        function getStatusName(statusCode) {
            const statuses = {
                '': 'Semua Pegawai',
                '1': 'PNS',
                '2': 'Honor',
                '3': 'Kontrak',
                '4': 'Part Time',
                '7': 'PPPK'
            };
            return statuses[statusCode] || '';
        }

        // Event listener untuk perubahan status - DIHAPUS AGAR TIDAK AUTO RELOAD
        // $('#status-rekap-pegawai').on('change', function() {
        //     updateTableHeaders();
        // });
    </script>
@endpush
