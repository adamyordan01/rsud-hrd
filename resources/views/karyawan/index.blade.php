@extends('layouts.backend', ['title' => 'Seluruh Karyawan'])

@push('styles')
    <style>
        #karyawan-table th {
            font-size: 12px;
        }

        #karyawan-table td {
            font-size: 12px;
        }

        /* vertical align middle of th */
        #karyawan-table th {
            vertical-align: middle;
        }

        #karyawan-table td {
            vertical-align: top;
        }
    </style>    
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2 ">
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">


                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Karyawan
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted title-breadcrumb">
                            {{ $titleBreadcrumb }}
                        </li>

                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    {{-- <a href="#"
                        class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                        Add Member
                    </a> --}}

                    <a
                        href="{{ route('admin.karyawan.create') }}"
                        class="btn btn-flex btn-primary h-40px fs-7 fw-bold"
                    >
                        Tambah Karyawan
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content  flex-column-fluid ">
        <div id="kt_app_content_container" class="app-container  container-fluid ">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i> <input type="text"
                                data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-12"
                                placeholder="Cari Karyawan ...">
                        </div>
                    </div>

                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                                data-kt-menu-placement="bottom-end">
                                <i class="ki-outline ki-filter fs-2"></i> Filter
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                id="kt-toolbar-filter">
                                <div class="px-7 py-5">
                                    <div class="fs-4 text-gray-900 fw-bold">Filter Options</div>
                                </div>
                                
                                <div class="separator border-gray-200"></div>
                                
                                <div class="px-7 py-5">
                                    <div class="mb-10">
                                        <label class="form-label fs-5 fw-semibold mb-3">Jenis Tenaga:</label>
                                
                                        <div class="d-flex flex-column flex-wrap fw-semibold" data-kt-customer-table-filter="jenis_tenaga">
                                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                                <input 
                                                    class="form-check-input employee-filter-checkbox" 
                                                    type="checkbox" 
                                                    value="1" 
                                                    data-status="1"
                                                >
                                                <span class="form-check-label text-gray-600">
                                                    PNS
                                                </span>
                                            </label>
                                
                                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                                <input 
                                                    class="form-check-input employee-filter-checkbox" 
                                                    type="checkbox" 
                                                    value="7"
                                                >
                                                <span class="form-check-label text-gray-600">
                                                    PPPK
                                                </span>
                                            </label>
                                
                                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                                <input 
                                                    class="form-check-input employee-filter-checkbox" 
                                                    type="checkbox" 
                                                    value="2"
                                                >
                                                <span class="form-check-label text-gray-600">
                                                    Honor
                                                </span>
                                            </label>
                                
                                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                                <input 
                                                    class="form-check-input employee-filter-checkbox" 
                                                    type="checkbox" 
                                                    value="blud"
                                                    data-status="3"
                                                    data-jenis-pegawai="2"
                                                >
                                                <span class="form-check-label text-gray-600">
                                                    Kontrak BLUD
                                                </span>
                                            </label>
                                
                                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                                <input 
                                                    class="form-check-input employee-filter-checkbox" 
                                                    type="checkbox" 
                                                    value="daerah"
                                                    data-status="3"
                                                    data-jenis-pegawai="1"
                                                >
                                                <span class="form-check-label text-gray-600">
                                                    Kontrak Daerah
                                                </span>
                                            </label>
                                
                                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                                <input 
                                                    class="form-check-input employee-filter-checkbox" 
                                                    type="checkbox" 
                                                    value="4"
                                                >
                                                <span class="form-check-label text-gray-600">
                                                    Part Time
                                                </span>
                                            </label>
                                
                                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                                <input 
                                                    class="form-check-input employee-filter-checkbox" 
                                                    type="checkbox" 
                                                    value="6"
                                                >
                                                <span class="form-check-label text-gray-600">
                                                    THL
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                
                                    <div class="d-flex justify-content-end">
                                        <button type="reset" class="btn btn-light btn-active-light-primary me-2"
                                            data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Reset</button>
                                
                                        <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true"
                                            data-kt-customer-table-filter="filter">Apply</button>
                                    </div>
                                </div>
                            </div>

                            {{-- <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal"
                                data-bs-target="#kt_customers_export_modal">
                                <i class="ki-outline ki-exit-up fs-2"></i> Export
                            </button>

                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_add_customer">
                                Add Customer
                            </button> --}}
                        </div>

                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-customer-table-toolbar="selected">
                            <div class="fw-bold me-5">
                                <span class="me-2" data-kt-customer-table-select="selected_count"></span> Selected
                            </div>

                            <button type="button" class="btn btn-danger" data-kt-customer-table-select="delete_selected">
                                Delete Selected
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">

                    <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                            <table id="karyawan-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                                <thead>
                                    <tr>
                                        <th class="w-10px pe-2 sorting_disabled" rowspan="3"
                                        >
                                            ID Peg.
                                        </th>
                                        <th class="min-w-125px text-center sorting" tabindex="0" aria-controls="kt_customers_table"
                                            rowspan="3"
                                        >
                                            Nama <br>
                                            TTL <br>
                                            NIP / No. Karpeg
                                        </th>
                                        <th class="min-w-35px text-center sorting" tabindex="0"
                                            rowspan="3">
                                            L/P
                                        </th>
                                        <th class="min-w-155px text-center sorting" tabindex="0"
                                            colspan="3"
                                        >
                                            Kepangkatan Sekarang
                                        </th>
                                        <th class="min-w-80px text-center sorting" tabindex="0"
                                            rowspan="3"
                                        >
                                            Eselon TMT
                                        </th>
                                        <th class="min-w-125px text-center sorting" tabindex="0"
                                            rowspan="3"
                                        >
                                            Pend. Terakhir
                                        </th>
                                        <th class="min-w-125px text-center sorting" tabindex="0"
                                            rowspan="3"
                                        >
                                            Sub. Jenis tenaga
                                            <br>
                                            Ruangan
                                        </th>
                                        <th class="min-w-75px text-center sorting" tabindex="0"
                                            rowspan="3"
                                        >
                                            Status
                                        </th>
                                        <th class="min-w-100px text-center sorting" tabindex="0"
                                            rowspan="3"
                                        >
                                            Rek. BSI
                                        </th>
                                        <th class="min-w-90px text-center sorting" tabindex="0"
                                            rowspan="3"
                                        >
                                            Action
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="min-w-125px text-center sorting" tabindex="0"
                                            rowspan="2"
                                        >
                                            Pangkat / Gol. <br> TMT
                                        </th>
                                        <th class="min-w-125px text-center sorting" tabindex="0"
                                            colspan="2"
                                        >
                                            Masa Kerja
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="min-w-25px text-center sorting" tabindex="0">
                                            Thn.
                                        </th>
                                        <th class="min-w-25px text-center sorting" tabindex="0">
                                            Bln.
                                        </th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        "use strict";

        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // Fungsi untuk membaca parameter URL
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // Fungsi untuk mengatur checkbox berdasarkan parameter URL
            function setFiltersFromUrl() {
                var status = getUrlParameter('status');
                var jenisPegawai = getUrlParameter('jenis_pegawai');
                
                // Reset semua checkbox terlebih dahulu
                $('.employee-filter-checkbox').prop('checked', false);
                
                if (status) {
                    if (status === '3' && jenisPegawai) {
                        // Handle kontrak BLUD dan kontrak daerah
                        if (jenisPegawai === '2') {
                            // Kontrak BLUD
                            $('.employee-filter-checkbox[value="blud"]').prop('checked', true);
                        } else if (jenisPegawai === '1') {
                            // Kontrak Daerah
                            $('.employee-filter-checkbox[value="daerah"]').prop('checked', true);
                        }
                    } else {
                        // Handle status lainnya
                        $('.employee-filter-checkbox[value="' + status + '"]').prop('checked', true);
                    }
                }
            }

            // Set filter dari URL saat halaman dimuat
            setFiltersFromUrl();

            var table = $('#karyawan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.karyawan.index') }}",
                    type: 'GET',
                    data: function (d) {
                        var selectedStatuses = [];
                        $('.employee-filter-checkbox:checked').each(function() {
                            selectedStatuses.push($(this).val());
                        });
                        d.statuses = selectedStatuses;
                    },
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                columns: [
                    { data: 'id_pegawai', name: 'kd_karyawan' },
                    { data: 'nama_lengkap', name: 'nama' },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin', searchable: false },
                    { data: 'golongan', name: 'golongan' },
                    { data: 'masa_kerja_thn', name: 'masa_kerja_thn' },
                    { data: 'masa_kerja_bulan', name: 'masa_kerja_bulan' },
                    { data: 'eselon', name: 'eselon', orderable: false, searchable: false },
                    { data: 'pendidikan', name: 'pendidikan' },
                    { data: 'sub_detail', name: 'sub_detail' },
                    { data: 'status_kerja', name: 'status_kerja' },
                    { data: 'rek_bni_syariah', name: 'rek_bni_syariah' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [
                    {
                        targets: -1,
                        data: null,
                        orderable: false,
                        className: 'text-center'
                    },
                ],
                searching: true,
                ordering: true,
                paging: true,
                "createdRow": function (row, data, dataIndex) {
                    $(row).find('td:eq(2)').addClass('text-center');
                    $(row).find('td:eq(3)').addClass('text-center');
                    $(row).find('td:eq(4)').addClass('text-center');
                    $(row).find('td:eq(5)').addClass('text-center');
                    $(row).find('td:eq(6)').addClass('text-center');
                    $(row).find('td:eq(9)').addClass('text-center');
                    $(row).find('td:eq(10)').addClass('text-center');
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                initComplete: function() {
                    // Trigger filter setelah DataTable selesai diinisialisasi
                    if ($('.employee-filter-checkbox:checked').length > 0) {
                        table.draw();
                    }
                }
            });

            table.on('draw', function() {
                KTMenu.createInstances();
            });

            // Optimasi search
            var searchTimer;
            $('[data-kt-customer-table-filter="search"]').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.search(this.value).draw();
                }.bind(this), 500);
            });

            // Apply filter
            $('[data-kt-customer-table-filter="filter"]').on('click', function() {
                table.draw();
            });

            // Reset filter
            $('[data-kt-customer-table-filter="reset"]').on('click', function() {
                $('.employee-filter-checkbox').prop('checked', false);
                table.search('').draw();
                
                // Update URL tanpa parameter
                var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.pushState({path: newUrl}, '', newUrl);
            });

            // Update URL ketika filter berubah
            $('.employee-filter-checkbox').on('change', function() {
                var selectedStatuses = [];
                $('.employee-filter-checkbox:checked').each(function() {
                    selectedStatuses.push($(this).val());
                });
                
                // Update URL berdasarkan filter yang dipilih
                var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                if (selectedStatuses.length > 0) {
                    // Untuk saat ini, kita hanya handle satu status
                    var firstStatus = selectedStatuses[0];
                    if (firstStatus === 'blud') {
                        newUrl += '?status=3&jenis_pegawai=2';
                    } else if (firstStatus === 'daerah') {
                        newUrl += '?status=3&jenis_pegawai=1';
                    } else {
                        newUrl += '?status=' + firstStatus;
                    }
                }
                window.history.pushState({path: newUrl}, '', newUrl);
            });
        });
    </script>
@endpush