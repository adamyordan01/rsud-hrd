@extends('layouts.backend', ['title' => 'Laporan Data Taspen'])

@push('styles')
<style>
    #table-data-taspen {
        border-collapse: collapse;
        border: 1px solid #dee2e6;
    }
    
    #table-data-taspen th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-data-taspen td {
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
    
    #table-data-taspen thead th {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-data-taspen tbody td {
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
                    Laporan Data Taspen
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
                    <li class="breadcrumb-item text-muted">Data Taspen</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printDataTaspen()">
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
                            <h3 class="fw-bold text-gray-800">Laporan Data Taspen</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Header RSUD -->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <table class="text-center" width="100%">
                                    <tr>
                                        <td width="140">
                                            <img src="{{ asset('assets/media/images/Langsa.png') }}" width="80" alt="Logo RSUD Langsa">
                                        </td>
                                        <td>
                                            <p>
                                                <b style="font-size: 14pt; margin-bottom: 0px;">PEMERINTAH KOTA LANGSA</b><br>
                                                <b style="font-size: 20pt; margin-top: 0px; margin-bottom: 0px;">RUMAH SAKIT UMUM DAERAH LANGSA</b><br>
                                                <b style="font-size: 10pt; margin-top: 0px;">Alamat : Jln. Jend. A. Yani No.1 Kota Langsa – Provinsi Pemerintah Aceh,</b>
                                                <b style="font-size: 8pt; margin-top: 0px;">Telp. (0641) 22051 – 22800 (IGD) Fax. (0641) 22051</b><br>
                                                <b style="font-size: 10pt; margin-top: 0px;">E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,</b>
                                                <b style="font-size: 10pt; margin-top: 0px;">Website : www.rsud.langsakota.go.id</b><br>
                                                <b style="font-size: 12pt; margin-top: 0px;">KOTA LANGSA</b>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                <hr style="background: #282828; height: 2px; margin: 10px 0;">
                                <hr style="background: #282828; height: 1px; margin: 5px 0;">
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-data-taspen">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Memuat data...</p>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive" id="table-container-data-taspen" style="display: none;">
                            <!-- Search Box -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <input type="text" class="form-control form-control-sm" id="search-data-taspen" placeholder="Cari nama, NIP, KTP, atau HP..." style="width: 300px;">
                                </div>
                                <div>
                                    <span id="total-data-taspen" class="badge badge-light-primary">Total: 0 data</span>
                                </div>
                            </div>
                            
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="table-data-taspen">
                                <thead>
                                    <tr>
                                        <th style="text-align:center; vertical-align: middle;">No.</th>
                                        <th style="text-align:center; vertical-align: middle;">Nama</th>
                                        <th style="text-align:center; vertical-align: middle;">NIP</th>
                                        <th style="text-align:center; vertical-align: middle;">No. KTP</th>
                                        <th style="text-align:center; vertical-align: middle;">No. HP</th>
                                        <th style="text-align:center; vertical-align: middle; width:200px;">Ket</th>
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
            // Auto load data on ready
            lihatDataTaspen();
        });

        function initDataTable() {
            if (table) {
                table.destroy();
            }
            
            table = $('#table-data-taspen').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 400, // Kurangi delay dari 800 ke 400
                searching: false, // Disable default search, use custom
                ajax: {
                    url: '{{ route("admin.laporan.data-taspen.index") }}',
                    data: function(d) {
                        d.search = {
                            value: $('#search-data-taspen').val()
                        };
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_lengkap', name: 'nama_lengkap', orderable: false, searchable: false },
                    { data: 'nip', name: 'nip' },
                    { data: 'no_ktp', name: 'no_ktp' },
                    { data: 'no_hp', name: 'no_hp' },
                    { data: 'keterangan', name: 'keterangan', orderable: false, searchable: false }
                ],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]], // Remove "All" option
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
                    $('#total-data-taspen').text(`Total: ${totalData} data`);
                },
                initComplete: function(settings, json) {
                    // Update total data counter on initial load
                    const info = this.api().page.info();
                    const totalData = info.recordsFiltered || 0;
                    $('#total-data-taspen').text(`Total: ${totalData} data`);
                }
            });
            
            // Custom search functionality with debounce
            let searchTimeout;
            $('#search-data-taspen').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    table.ajax.reload(function() {
                        // Update counter after search
                        updateDataCounter();
                    });
                }, 300); // Debounce 300ms
            });
            
            // Function to update data counter
            function updateDataCounter() {
                if (table) {
                    const info = table.page.info();
                    const totalData = info.recordsFiltered || 0;
                    $('#total-data-taspen').text(`Total: ${totalData} data`);
                }
            }
        }

        function lihatDataTaspen() {
            // Show loading
            $('#loader-data-taspen').show();
            $('#table-container-data-taspen').hide();

            // Initialize table
            if (!table) {
                initDataTable();
                // Show table after initialization
                setTimeout(function() {
                    $('#loader-data-taspen').hide();
                    $('#table-container-data-taspen').show();
                }, 500); // Kurangi timeout
            } else {
                table.ajax.reload(function() {
                    $('#loader-data-taspen').hide();
                    $('#table-container-data-taspen').show();
                });
            }
        }

        function printDataTaspen() {
            // Buka halaman print dalam window/tab baru
            const printUrl = `{{ route('admin.laporan.data-taspen.print') }}`;
            window.open(printUrl, '_blank');
        }
    </script>
@endpush
