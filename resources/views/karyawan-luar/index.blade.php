@extends('layouts.backend', ['title' => $titleBreadcrumb])

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        {{ $titleBreadcrumb }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <span class="text-muted text-hover-primary">{{ $titleBreadcrumb }}</span>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.karyawan-luar.create') }}" class="btn btn-sm fw-bold btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>Tambah Pegawai Luar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-user-table-filter="search"
                                class="form-control form-control-solid w-250px ps-13" placeholder="Cari pegawai luar..." />
                        </div>
                    </div>
                </div>

                <div class="card-body py-4">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_karyawan_luar">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">ID Pegawai</th>
                                <th class="min-w-200px">Nama Lengkap</th>
                                <th class="min-w-50px">L/P</th>
                                <th class="min-w-125px">Jenis Pegawai</th>
                                <th class="min-w-125px">Rekening BPD Aceh</th>
                                <th class="min-w-125px">Rekening BSI</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        "use strict";

        var KTUsersList = function() {
            var table = document.getElementById('kt_table_karyawan_luar');
            var dt;
            var filterSearch;

            var initUserTable = function() {
                if (!table) {
                    console.error('Table element not found');
                    return;
                }

                try {
                    dt = $('#kt_table_karyawan_luar').DataTable({
                        searchDelay: 500,
                        processing: true,
                        serverSide: true,
                        order: [[1, 'asc']],
                        stateSave: false,
                        ajax: {
                            url: "{{ route('admin.karyawan-luar.index') }}",
                            type: "GET",
                            error: function(xhr, error, code) {
                                console.error('AJAX Error:', error);
                                console.error('Response:', xhr.responseText);
                                console.error('Status Code:', xhr.status);
                                
                                // Show user-friendly error message
                                if (xhr.status === 403) {
                                    toastr.error('Anda tidak memiliki izin untuk mengakses data ini.');
                                } else if (xhr.status === 500) {
                                    toastr.error('Terjadi kesalahan pada server. Silakan coba lagi nanti.');
                                } else {
                                    toastr.error('Terjadi kesalahan saat memuat data.');
                                }
                            }
                        },
                        columns: [
                            { 
                                data: 'id_pegawai', 
                                name: 'kd_peg_luar', 
                                orderable: false,
                                searchable: false
                            },
                            { 
                                data: 'nama_lengkap', 
                                name: 'nama', 
                                orderable: true,
                                searchable: true
                            },
                            { 
                                data: 'jenis_kelamin', 
                                name: 'sex', 
                                orderable: false,
                                searchable: false
                            },
                            { 
                                data: 'jenis_pegawai', 
                                name: 'jenis_pegawai', 
                                orderable: false,
                                searchable: false
                            },
                            { 
                                data: 'rekening_bpd', 
                                name: 'bpd_aceh', 
                                orderable: false,
                                searchable: false
                            },
                            { 
                                data: 'rekening_bsi', 
                                name: 'bsi', 
                                orderable: false,
                                searchable: false
                            },
                            { 
                                data: 'action', 
                                name: 'action', 
                                orderable: false, 
                                searchable: false,
                                className: 'text-end'
                            },
                        ],
                        responsive: true,
                        language: {
                            processing: "Sedang memproses...",
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            },
                            emptyTable: "Tidak ada data yang tersedia",
                            zeroRecords: "Tidak ditemukan data yang sesuai"
                        },
                        drawCallback: function(settings) {
                            // Re-init tooltips and other components after each draw
                            if (typeof KTComponents !== 'undefined' && KTComponents.init) {
                                KTComponents.init();
                            }
                            // Re-init menu instances for dropdown actions
                            if (typeof KTMenu !== 'undefined' && KTMenu.createInstances) {
                                KTMenu.createInstances();
                            }
                        }
                    });

                    console.log('DataTable initialized successfully');

                    // Search functionality with debounce
                    var searchTimer;
                    filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
                    if (filterSearch) {
                        filterSearch.addEventListener('keyup', function(e) {
                            clearTimeout(searchTimer);
                            searchTimer = setTimeout(function() {
                                if (dt) {
                                    dt.search(e.target.value).draw();
                                }
                            }, 500); // 500ms delay
                        });
                    }

                    // Also add event listener for DataTable's built-in search if available
                    var tableSearchInput = document.querySelector('input[type="search"]');
                    if (tableSearchInput && tableSearchInput !== filterSearch) {
                        tableSearchInput.addEventListener('keyup', function(e) {
                            clearTimeout(searchTimer);
                            searchTimer = setTimeout(function() {
                                if (dt) {
                                    dt.search(e.target.value).draw();
                                }
                            }, 500); // 500ms delay
                        });
                    }

                    // Add event listener for when DataTable is drawn
                    dt.on('draw', function() {
                        console.log('DataTable drawn, reinitializing menu instances');
                        if (typeof KTMenu !== 'undefined' && KTMenu.createInstances) {
                            KTMenu.createInstances();
                        }
                    });

                    // Initialize menu instances for the first time
                    setTimeout(function() {
                        if (typeof KTMenu !== 'undefined' && KTMenu.createInstances) {
                            KTMenu.createInstances();
                            console.log('Menu instances initialized');
                        }
                    }, 100);

                } catch (error) {
                    console.error('Error initializing DataTable:', error);
                    toastr.error('Gagal menginisialisasi tabel data. Silakan refresh halaman.');
                }
            }

            return {
                init: function() {
                    initUserTable();
                }
            };
        }();

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready, initializing components...');
            console.log('KTMenu available:', typeof KTMenu !== 'undefined');
            console.log('KTComponents available:', typeof KTComponents !== 'undefined');
            
            KTUsersList.init();
            
            // Also initialize KTMenu components
            if (typeof KTMenu !== 'undefined' && KTMenu.createInstances) {
                KTMenu.createInstances();
                console.log('KTMenu initialized on DOM ready');
            } else {
                console.log('KTMenu not available');
            }
        });
    </script>
@endpush
