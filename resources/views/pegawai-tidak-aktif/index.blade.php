@extends('layouts.backend', ['title' => 'Pegawai Tidak Aktif'])

@push('styles')
    <style>
        #pegawai-tidak-aktif-table th {
            font-size: 12px;
        }

        #pegawai-tidak-aktif-table td {
            font-size: 12px;
        }

        /* vertical align middle of th */
        #pegawai-tidak-aktif-table th {
            vertical-align: middle;
        }

        #pegawai-tidak-aktif-table td {
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
                        {{ $titleBreadcrumb }}
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.karyawan.index') }}" class="text-muted text-hover-primary">
                                Karyawan </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted title-breadcrumb">
                            {{ $titleBreadcrumb }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content  flex-column-fluid ">
        <div id="kt_app_content_container" class="app-container  container-fluid ">
            <!-- Statistics Cards -->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-125px mb-5 mb-xl-10" style="background-color: #F1416C;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $statistics['keluar'] ?? 0 }}</span>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Pegawai Keluar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-125px mb-5 mb-xl-10" style="background-color: #7239EA;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $statistics['pensiun'] ?? 0 }}</span>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Pegawai Pensiun</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-125px mb-5 mb-xl-10" style="background-color: #17C653;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $statistics['tugas_belajar'] ?? 0 }}</span>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Tugas Belajar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-125px mb-5 mb-xl-10" style="background-color: #A3A3C2;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $statistics['meninggal'] ?? 0 }}</span>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Meninggal Dunia</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i> 
                            <input type="text" data-kt-customer-table-filter="search" 
                                   class="form-control form-control-solid w-300px ps-12"
                                   placeholder="Cari {{ $titleBreadcrumb }} ...">
                        </div>
                    </div>

                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <!-- Filter Jenis Pegawai -->
                            <div class="position-relative me-3">
                                <select class="form-select form-select-solid w-150px" id="jenis-pegawai-filter">
                                    <option value="">Semua Jenis</option>
                                    <option value="1">PNS</option>
                                    <option value="7">PPPK</option>
                                    <option value="2">Honor</option>
                                    <option value="blud">Kontrak BLUD</option>
                                    <option value="pemko">Kontrak PEMKO</option>
                                    <option value="4">Part Time</option>
                                    <option value="6">THL</option>
                                </select>
                            </div>

                            <!-- Reset Filter -->
                            <button type="button" class="btn btn-light btn-active-light-primary me-3" id="reset-filter">
                                <i class="ki-outline ki-arrows-circle fs-2"></i>
                                Reset
                            </button>

                            @php
                                $currentRoute = request()->route()->getName();
                                $statusMap = [
                                    'admin.pegawai-tidak-aktif.pensiun' => 'pensiun',
                                    'admin.pegawai-tidak-aktif.keluar' => 'keluar',
                                    'admin.pegawai-tidak-aktif.tugas-belajar' => 'tugas-belajar',
                                    'admin.pegawai-tidak-aktif.meninggal' => 'meninggal'
                                ];
                                $currentStatus = $statusMap[$currentRoute] ?? null;
                            @endphp

                            @if($currentStatus)
                            <!-- Tombol Cetak -->
                            <a href="{{ route('admin.pegawai-tidak-aktif.cetak', $currentStatus) }}" 
                               class="btn btn-primary" target="_blank">
                                <i class="ki-outline ki-printer fs-2"></i>
                                Cetak Laporan
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="pegawai-tidak-aktif-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                            <thead>
                                <tr>
                                    <th class="w-10px pe-2 sorting_disabled" rowspan="3">
                                        ID Peg.
                                    </th>
                                    <th class="min-w-125px text-center sorting" tabindex="0" 
                                        rowspan="3">
                                        Nama <br>
                                        TTL <br>
                                        NIP / No. Karpeg
                                    </th>
                                    <th class="min-w-35px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        L/P
                                    </th>
                                    <th class="min-w-155px text-center sorting" tabindex="0"
                                        colspan="3">
                                        Kepangkatan Sekarang
                                    </th>
                                    <th class="min-w-80px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        Eselon TMT
                                    </th>
                                    <th class="min-w-125px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        Pend. Terakhir
                                    </th>
                                    <th class="min-w-125px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        Sub. Jenis tenaga
                                        <br>
                                        Ruangan
                                    </th>
                                    <th class="min-w-75px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        Status
                                    </th>
                                    <th class="min-w-80px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        Tgl. Keluar/Pensiun
                                    </th>
                                    <th class="min-w-100px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        Rekening Bank
                                    </th>
                                    <th class="min-w-90px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        Action
                                    </th>
                                </tr>
                                <tr>
                                    <th class="min-w-125px text-center sorting" tabindex="0"
                                        rowspan="2">
                                        Pangkat / Gol. <br> TMT
                                    </th>
                                    <th class="min-w-125px text-center sorting" tabindex="0"
                                        colspan="2">
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
                                </tr>
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
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            var table = $('#pegawai-tidak-aktif-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: window.location.href,
                    type: 'GET',
                    data: function (d) {
                        // Filter jenis pegawai
                        var jenisPegawai = $('#jenis-pegawai-filter').val();
                        if (jenisPegawai) {
                            d.jenis_pegawai = jenisPegawai;
                        }
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
                    { data: 'tanggal_keluar', name: 'tanggal_keluar' },
                    { data: 'rekening_bank', name: 'rekening_bank' },
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
                    $(row).find('td:eq(11)').addClass('text-center');
                },
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                language: {
                    processing: "Memproses...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            table.on('draw', function() {
                KTMenu.createInstances();
            });

            // Filter Jenis Pegawai
            $('#jenis-pegawai-filter').on('change', function() {
                table.draw();
            });

            // Optimasi search
            var searchTimer;
            $('[data-kt-customer-table-filter="search"]').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.search(this.value).draw();
                }.bind(this), 500);
            });

            // Reset filter
            $('#reset-filter').on('click', function() {
                $('#jenis-pegawai-filter').val('');
                table.search('').draw();
            });

            // Print functionality
            window.print = function() {
                var pageTitle = $('h1.page-heading').text().trim();
                var printWindow = window.open('', '_blank');
                var tableHtml = $('#pegawai-tidak-aktif-table').clone();
                
                // Remove action column for printing
                tableHtml.find('th:last-child, td:last-child').remove();
                
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>${pageTitle}</title>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                            <style>
                                body { font-size: 12px; }
                                table { width: 100% !important; }
                                th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                                .header { text-align: center; margin-bottom: 20px; }
                                @media print {
                                    body { print-color-adjust: exact; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h3>RSUD LANGSA KOTA</h3>
                                <h4>${pageTitle}</h4>
                                <p>Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}</p>
                            </div>
                            <table class="table table-bordered">
                                ${tableHtml.html()}
                            </table>
                        </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.print();
            };
        });
    </script>
@endpush
