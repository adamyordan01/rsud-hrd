@extends('layouts.backend', ['title' => $titleBreadcrumb])

@push('styles')
    <style>
        #karyawan-jenis-table th {
            font-size: 12px;
        }

        #karyawan-jenis-table td {
            font-size: 12px;
        }

        #karyawan-jenis-table th {
            vertical-align: middle;
        }

        #karyawan-jenis-table td {
            vertical-align: top;
        }

        .btn-navigation {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .status-filter-container {
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
                            <a href="#" class="text-muted text-hover-primary">
                                Jenis Tenaga
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            {{ $titleBreadcrumb }}
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.karyawan.create') }}" class="btn btn-flex btn-primary h-40px fs-7 fw-bold">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Tambah Karyawan
                    </a>
                    <button class="btn btn-flex btn-secondary h-40px fs-7 fw-bold" onclick="printing()">
                        <i class="ki-duotone ki-printer fs-2"></i>
                        Print
                    </button>
                    @if($jenisTenaga == 1)
                        <button class="btn btn-flex btn-info h-40px fs-7 fw-bold" onclick="printingSpesialis()">
                            <i class="ki-duotone ki-printer fs-2"></i>
                            Print RS Online
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            {{-- Navigation Buttons --}}
            <div class="card mb-5">
                <div class="card-body text-center">
                    <a href="{{ route('admin.jenis-tenaga.index', 1) }}" 
                       class="btn btn-navigation {{ $jenisTenaga == 1 ? 'btn-info' : 'btn-outline-info' }}">
                        Medis
                    </a>
                    <a href="{{ route('admin.jenis-tenaga.index', 2) }}" 
                       class="btn btn-navigation {{ $jenisTenaga == 2 ? 'btn-warning' : 'btn-outline-warning' }}">
                        Paramedis
                    </a>
                    <a href="{{ route('admin.jenis-tenaga.index', 3) }}" 
                       class="btn btn-navigation {{ $jenisTenaga == 3 ? 'btn-success' : 'btn-outline-success' }}">
                        Penunjang Medis
                    </a>
                    <a href="{{ route('admin.jenis-tenaga.index', 4) }}" 
                       class="btn btn-navigation {{ $jenisTenaga == 4 ? 'btn-danger' : 'btn-outline-danger' }}">
                        Non-Kesehatan
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                            <input type="text" data-kt-jenis-table-filter="search" 
                                   class="form-control form-control-solid w-250px ps-12" 
                                   placeholder="Cari Karyawan ...">
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    {{-- Filter Status Kerja --}}
                    <div class="status-filter-container">
                        <div class="row">
                            <div class="col-md-9">
                                <label class="form-label fs-6 fw-semibold mb-3">Filter Status Kerja:</label>
                                <div class="d-flex flex-wrap">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                               value="1" id="pns" name="pns" checked>
                                        <label class="form-check-label" for="pns">PNS</label>
                                    </div>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                               value="2" id="honor" name="honor" checked>
                                        <label class="form-check-label" for="honor">HONOR</label>
                                    </div>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                               value="3" id="kontrak" name="kontrak" checked>
                                        <label class="form-check-label" for="kontrak">KONTRAK</label>
                                    </div>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                               value="4" id="partime" name="partime" checked>
                                        <label class="form-check-label" for="partime">PT</label>
                                    </div>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                               value="7" id="pppk" name="pppk" checked>
                                        <label class="form-check-label" for="pppk">PPPK</label>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-primary me-4" data-kt-jenis-table-filter="apply">
                                        Apply Filter
                                    </button>

                                    <button type="button" class="btn btn-sm btn-secondary" data-kt-jenis-table-filter="reset">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                @if(!empty($detailJenisTenaga) && $detailJenisTenaga->count() > 0)
                                <label class="form-label fs-6 fw-semibold mb-3">Detail Jenis Tenaga:</label>
                                <div class="dropdown w-100">
                                    <button class="btn btn-info dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                        Detail
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                        @foreach($detailJenisTenaga as $detail)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.jenis-tenaga.detail', [$detail->kd_detail, $jenisTenaga]) }}">
                                                <strong class="fs-7">{{ $detail->detail_jenis_tenaga }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $detail->statistik->pns }} PNS · 
                                                    {{ $detail->statistik->honor }} HONOR · 
                                                    {{ $detail->statistik->kontrak }} KONTRAK ·<br>
                                                    {{ $detail->statistik->partime }} PT · 
                                                    {{ $detail->statistik->thl }} THL · 
                                                    {{ $detail->statistik->pppk }} PPPK
                                                </small>
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="karyawan-jenis-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
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

    {{-- Hidden input untuk menyimpan jenis tenaga --}}
    <input type="hidden" id="jenis-tenaga" value="{{ $jenisTenaga }}">
@endsection

@push('scripts')
    <script>
        "use strict";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Function untuk melakukan print laporan pegawai
        function printing() {
            var jenis = $("#jenis-tenaga").val();
            var data1 = document.getElementById("pns").checked ? 1 : null; // PNS
            var data2 = document.getElementById("honor").checked ? 2 : null; // HONOR
            var data3 = document.getElementById("kontrak").checked ? 3 : null; // KONTRAK
            var data4 = document.getElementById("partime").checked ? 4 : null; // PT
            var data7 = document.getElementById("pppk").checked ? 7 : null; // PPPK

            if (!data1 && !data2 && !data3 && !data4 && !data7) {
                data1 = "a";
            }

            var url = "{{ route('admin.jenis-tenaga.print-pegawai', ['jenisTenaga' => ':jenis']) }}"
                .replace(':jenis', jenis);
            
            var params = new URLSearchParams({
                data1: data1,
                data2: data2,
                data3: data3,
                data4: data4,
                data7: data7
            });

            window.open(url + '?' + params.toString(), '_blank');
        }

        // Function untuk melakukan print laporan pegawai spesialis
        function printingSpesialis() {
            var jenis = $("#jenis-tenaga").val();
            var data1 = document.getElementById("pns").checked ? 1 : null; // PNS
            var data2 = document.getElementById("honor").checked ? 2 : null; // HONOR
            var data3 = document.getElementById("kontrak").checked ? 3 : null; // KONTRAK
            var data4 = document.getElementById("partime").checked ? 4 : null; // PT
            var data7 = document.getElementById("pppk").checked ? 7 : null; // PPPK

            // Jika tidak ada yang dicentang, kirim data1 = "a"
            if (!data1 && !data2 && !data3 && !data4 && !data7) {
                data1 = "a";
            }

            var url = "{{ route('admin.jenis-tenaga.print-pegawai-spesialis', ['jenisTenaga' => ':jenis']) }}"
                .replace(':jenis', jenis);
            
            var params = new URLSearchParams({
                data1: data1,
                data2: data2,
                data3: data3,
                data4: data4,
                data7: data7
            });

            window.open(url + '?' + params.toString(), '_blank');
        }

        $(document).ready(function() {
            var table = $('#karyawan-jenis-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.jenis-tenaga.index', $jenisTenaga) }}",
                    type: 'GET',
                    data: function (d) {
                        // Ambil filter status kerja
                        if ($('#pns').is(':checked')) d.pns = '1';
                        if ($('#honor').is(':checked')) d.honor = '2';
                        if ($('#kontrak').is(':checked')) d.kontrak = '3';
                        if ($('#partime').is(':checked')) d.partime = '4';
                        if ($('#pppk').is(':checked')) d.pppk = '7';
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
                lengthMenu: [[10, 25, 50, 100, 200, 500, 1000, -1], [10, 25, 50, 100, 200, 500, 1000, "Semua"]],
            });

            table.on('draw', function() {
                KTMenu.createInstances();
            });

            // Search functionality
            var searchTimer;
            $('[data-kt-jenis-table-filter="search"]').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.search(this.value).draw();
                }.bind(this), 500);
            });

            // Page length change
            $('[data-kt-jenis-table-filter="length"]').on('change', function() {
                table.page.len(this.value).draw();
            });

            // Apply filter
            $('[data-kt-jenis-table-filter="apply"]').on('click', function() {
                table.draw();
            });

            // Reset filter
            $('[data-kt-jenis-table-filter="reset"]').on('click', function() {
                $('.status-filter-checkbox').prop('checked', true);
                table.search('').draw();
            });

            // Status filter change
            $('.status-filter-checkbox').on('change', function() {
                // Auto apply when checkbox changes
                table.draw();
            });
        });
    </script>
@endpush