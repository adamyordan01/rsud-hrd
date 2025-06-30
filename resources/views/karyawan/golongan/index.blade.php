@extends('layouts.backend', ['title' => 'Golongan'])

@push('styles')
    <style>
        #karyawan-golongan-table th {
            font-size: 12px;
        }

        #karyawan-golongan-table td {
            font-size: 12px;
        }

        #karyawan-golongan-table th {
            vertical-align: middle;
        }

        #karyawan-golongan-table td {
            vertical-align: top;
        }

        .filter-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-check-input:checked {
            background-color: #009ef7;
            border-color: #009ef7;
        }

        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>    
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Golongan
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
                            <a href="#" class="text-muted text-hover-primary">
                                Karyawan
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Golongan
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.karyawan.create') }}" class="btn btn-flex btn-primary h-40px fs-7 fw-bold">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Tambah Karyawan
                    </a>
                    <button class="btn btn-flex btn-secondary h-40px fs-7 fw-bold" onclick="printGolongan()">
                        <i class="ki-duotone ki-printer fs-2"></i>
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
            
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                            <input type="text" data-kt-golongan-table-filter="search" 
                                   class="form-control form-control-solid w-250px ps-12" 
                                   placeholder="Cari Karyawan ...">
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    {{-- Filter Container --}}
                    <div class="filter-container">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label fs-6 fw-semibold mb-3">Pilih Golongan:</label>
                                <select class="form-control form-select" name="golongan" id="golongan" data-control="select2">
                                    <option value="">-- PILIH --</option>
                                    @foreach($golongan as $gol)
                                        <option value="{{ $gol->kd_gol }}">{{ $gol->kd_gol }} - {{ $gol->pangkat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1">
                                <label class="form-label fs-6 fw-semibold mb-3">&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block w-100" data-kt-golongan-table-filter="apply">
                                    <i class="ki-duotone ki-eye fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Lihat
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Loading Indicator --}}
                    <div class="text-center" id="loader-golongan" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive" id="data-golongan" style="display: none;">
                        <table id="karyawan-golongan-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
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
@endsection

@push('scripts')
    <script>
        "use strict";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table;

        // Function untuk melakukan print laporan golongan
        function printGolongan() {
            var golongan = $("#golongan").val();

            if (golongan == "") {
                Swal.fire("Perhatian!", "Harap isikan data yang benar.", "warning");
                return;
            }

            var url = "{{ route('admin.karyawan-golongan.print') }}";
            
            var params = new URLSearchParams({
                golongan: golongan
            });

            window.open(url + '?' + params.toString(), '_blank');
        }

        // Function untuk load data karyawan berdasarkan golongan
        function lihatGolongan() {
            var golongan = $("#golongan").val();
            
            if (golongan == "") {
                Swal.fire("Perhatian!", "Harap isikan data yang benar.", "warning");
                return;
            }

            // Show loader
            $("#data-golongan").hide();
            $("#loader-golongan").show();

            // Destroy existing DataTable if exists
            if (table) {
                table.destroy();
            }

            // Initialize new DataTable
            table = $('#karyawan-golongan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.karyawan-golongan.index') }}",
                    type: 'GET',
                    data: function (d) {
                        d.golongan = $('#golongan').val();
                    },
                    dataSrc: function(json) {
                        // Hide loader and show table
                        $("#loader-golongan").hide();
                        $("#data-golongan").show();
                        return json.data;
                    }
                },
                columns: [
                    { data: 'id_pegawai', name: 'kd_karyawan' },
                    { data: 'nama_lengkap', name: 'nama' },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin', searchable: false },
                    { data: 'golongan', name: 'golongan' },
                    { data: 'masa_kerja', name: 'masa_kerja_thn' },
                    { data: 'masa_kerja_bulan', name: 'masa_kerja_bulan' },
                    { data: 'eselon', name: 'eselon', orderable: false, searchable: false },
                    { data: 'pendidikan', name: 'pendidikan' },
                    { data: 'sub_detail', name: 'sub_detail' },
                    { data: 'status_kerja', name: 'status_kerja' },
                    { data: 'rek_bsi', name: 'rek_bni_syariah' },
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
                    // Apply color coding berdasarkan kelengkapan data
                    var kelengkapan = data.kelengkapan_data;
                    var persen = parseInt(kelengkapan.replace(/\D/g, ''));
                    
                    if (persen <= 50) {
                        $(row).addClass('merah');
                    } else if (persen < 100 && persen > 50) {
                        $(row).addClass('kuning');
                    } else {
                        $(row).addClass('hijau');
                    }

                    // Center align specific columns
                    $(row).find('td:eq(2)').addClass('text-center');
                    $(row).find('td:eq(3)').addClass('text-center');
                    $(row).find('td:eq(4)').addClass('text-center');
                    $(row).find('td:eq(5)').addClass('text-center');
                    $(row).find('td:eq(6)').addClass('text-center');
                    $(row).find('td:eq(9)').addClass('text-center');
                    $(row).find('td:eq(10)').addClass('text-center');
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, 200, 500, 1000, -1], [10, 25, 50, 100, 200, 500, 1000, "Semua"]],
            });

            table.on('draw', function() {
                KTMenu.createInstances();
            });
        }

        $(document).ready(function() {
            // Search functionality
            var searchTimer;
            $('[data-kt-golongan-table-filter="search"]').on('keyup', function() {
                if (table) {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(function() {
                        table.search(this.value).draw();
                    }.bind(this), 500);
                }
            });

            // Apply filter
            $('[data-kt-golongan-table-filter="apply"]').on('click', function() {
                lihatGolongan();
            });

            // Reset filter
            $('[data-kt-golongan-table-filter="reset"]').on('click', function() {
                $('#golongan').val('');
                
                // trigger select2 change event
                $('#golongan').trigger('change');
                
                if (table) {
                    table.destroy();
                    table = null;
                }
                
                $("#data-golongan").hide();
                $('[data-kt-golongan-table-filter="search"]').val('');
            });
        });
    </script>
@endpush