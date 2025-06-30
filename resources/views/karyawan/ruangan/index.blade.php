@extends('layouts.backend', ['title' => 'Data Pegawai per Ruangan'])

@push('styles')
    <style>
        #karyawan-ruangan-table th {
            font-size: 12px;
        }

        #karyawan-ruangan-table td {
            font-size: 12px;
        }

        #karyawan-ruangan-table th {
            vertical-align: middle;
        }

        #karyawan-ruangan-table td {
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
                        Data Pegawai {{ $kdRuangan ? 'Per-Ruangan' : 'Belum Ada Ruangan' }}
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
                            {{ $kdRuangan ? 'Per Ruangan' : 'Belum Ada Ruangan' }}
                        </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.karyawan.create') }}" class="btn btn-flex btn-success h-40px fs-7 fw-bold">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Tambah Karyawan
                    </a>
                    
                    @if($kdRuangan)
                        <button class="btn btn-flex btn-secondary h-40px fs-7 fw-bold" onclick="printSesuaiJabatan()">
                            <i class="ki-duotone ki-printer fs-2"></i>
                            Print Sesuai Jabatan
                        </button>
                        
                        <button class="btn btn-flex btn-secondary h-40px fs-7 fw-bold" onclick="printSesuaiRekBNI()">
                            <i class="ki-duotone ki-printer fs-2"></i>
                            Print Sesuai Rek BNI
                        </button>
                        
                        <button class="btn btn-flex btn-secondary h-40px fs-7 fw-bold" onclick="printDataPegawai()">
                            <i class="ki-duotone ki-printer fs-2"></i>
                            Print Data Pegawai
                        </button>
                        
                        <button class="btn btn-flex btn-warning h-40px fs-7 fw-bold" onclick="exportToFP()">
                            <i class="ki-duotone ki-file-down fs-2"></i>
                            Export ke FP
                        </button>
                        
                        <button class="btn btn-flex btn-info h-40px fs-7 fw-bold" onclick="exportExcel()">
                            <i class="ki-duotone ki-file-down fs-2"></i>
                            Export Data
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
            
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                            <input type="text" data-kt-ruangan-table-filter="search" 
                                   class="form-control form-control-solid w-250px ps-12" 
                                   placeholder="Cari Karyawan ...">
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    {{-- Filter Container --}}
                    <div class="filter-container">
                        <div class="row align-items-end">
                            {{-- Status Kerja Filter (Paling Kiri) --}}
                            <div class="col-md-6 order-md-1">
                                <label class="form-label fs-6 fw-semibold mb-3">Filter Status Kerja:</label>
                                <div class="d-flex flex-wrap align-items-center">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                            value="1" id="kerja1" name="pns" checked>
                                        <label class="form-check-label" for="kerja1">PNS</label>
                                    </div>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                            value="7" id="kerja5" name="pppk" checked>
                                        <label class="form-check-label" for="kerja5">PPPK</label>
                                    </div>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                            value="2" id="kerja2" name="honor" checked>
                                        <label class="form-check-label" for="kerja2">HONOR</label>
                                    </div>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                            value="3" id="kerja3" name="kontrak" checked>
                                        <label class="form-check-label" for="kerja3">KONTRAK</label>
                                    </div>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-4 mb-2">
                                        <input class="form-check-input status-filter-checkbox" type="checkbox" 
                                            value="4" id="kerja4" name="partime" checked>
                                        <label class="form-check-label" for="kerja4">PT</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Spacer untuk Mengisi Ruang Kosong (Opsional) --}}
                            <div class="col-md-3 order-md-2"></div>

                            {{-- Pilih Ruangan (Paling Kanan) --}}
                            <div class="col-md-3 order-md-3">
                                <label class="form-label fs-6 fw-semibold mb-3">Pilih Ruangan:</label>
                                <select class="form-control form-select" name="ruangan" id="pilih-ruangan" onchange="gantiRuangan(this.value)" data-control="select2">
                                    <option value="" disabled selected>
                                        Pilih Ruangan
                                    </option>
                                    {{-- <option value="0" {{ !$kdRuangan || $kdRuangan == '0' ? 'selected' : '' }}>
                                        -- BELUM ADA RUANGAN --
                                    </option> --}}
                                    <option value="0" {{ $kdRuangan == '0' ? 'selected' : '' }}>
                                        -- BELUM ADA RUANGAN --
                                    </option>
                                    @foreach($ruanganList as $ruangan)
                                        <option value="{{ $ruangan->kd_ruangan }}" 
                                                {{ $kdRuangan == $ruangan->kd_ruangan ? 'selected' : '' }}>
                                            {{ $ruangan->ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    @if($kdRuangan !== null)
                        <div class="table-responsive">
                            <table id="karyawan-ruangan-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                                {{-- Struktur table sama seperti sebelumnya --}}
                                <thead>
                                    <tr>
                                        <th class="w-10px pe-2 sorting_disabled" rowspan="3">
                                            ID Peg.
                                        </th>
                                        <th class="min-w-125px text-center sorting" tabindex="0" aria-controls="kt_customers_table" rowspan="3">
                                            Nama <br>
                                            TTL <br>
                                            NIP / No. Karpeg
                                        </th>
                                        <th class="min-w-35px text-center sorting" tabindex="0" rowspan="3">
                                            L/P
                                        </th>
                                        <th class="min-w-155px text-center sorting" tabindex="0" colspan="3">
                                            Kepangkatan Sekarang
                                        </th>
                                        <th class="min-w-80px text-center sorting" tabindex="0" rowspan="3">
                                            Eselon TMT
                                        </th>
                                        <th class="min-w-125px text-center sorting" tabindex="0" rowspan="3">
                                            Pend. Terakhir
                                        </th>
                                        <th class="min-w-125px text-center sorting" tabindex="0" rowspan="3">
                                            Sub. Jenis tenaga
                                            <br>
                                            Ruangan
                                        </th>
                                        <th class="min-w-75px text-center sorting" tabindex="0" rowspan="3">
                                            Status
                                        </th>
                                        <th class="min-w-100px text-center sorting" tabindex="0" rowspan="3">
                                            Rek. BSI
                                        </th>
                                        <th class="min-w-90px text-center sorting" tabindex="0" rowspan="3">
                                            Action
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="min-w-125px text-center sorting" tabindex="0" rowspan="2">
                                            Pangkat / Gol. <br> TMT
                                        </th>
                                        <th class="min-w-125px text-center sorting" tabindex="0" colspan="2">
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
                    @else
                        <div class="text-center py-10">
                            <div class="alert alert-info">
                                <h4>Silakan pilih ruangan terlebih dahulu</h4>
                                <p>Gunakan dropdown "Pilih Ruangan" di atas untuk melihat data pegawai per ruangan, atau pilih "BELUM ADA RUANGAN" untuk melihat pegawai yang belum memiliki ruangan.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden input untuk menyimpan kd ruangan --}}
    <input type="hidden" id="kd-ruangan" value="{{ $kdRuangan }}">
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

        // Function untuk ganti ruangan - dimodifikasi
        function gantiRuangan(kdRuangan) {
            if (kdRuangan && kdRuangan !== '0') {
                window.location.href = "{{ route('admin.karyawan-ruangan.index', '') }}/" + kdRuangan;
            } else {
                // Jika pilih "BELUM ADA RUANGAN" (value = 0)
                window.location.href = "{{ route('admin.karyawan-ruangan.index', '0') }}";
            }
        }


        // Modifikasi semua function print dan export untuk handle kasus kdRuangan = 0
        function printSesuaiJabatan() {
            var kdRuangan = $("#kd-ruangan").val();
            
            // Jika kdRuangan adalah 0 atau kosong, berikan peringatan
            if (!kdRuangan || kdRuangan === '0') {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Print tidak tersedia untuk pegawai yang belum memiliki ruangan.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            var data1 = document.getElementById("kerja1").checked ? 1 : null;
            var data2 = document.getElementById("kerja2").checked ? 2 : null;
            var data3 = document.getElementById("kerja3").checked ? 3 : null;
            var data4 = document.getElementById("kerja4").checked ? 4 : null;
            var data5 = document.getElementById("kerja5").checked ? 7 : null;

            if (!data1 && !data2 && !data3 && !data4 && !data5) {
                data1 = "a";
            }

            var url = "{{ route('admin.karyawan-ruangan.print-jabatan', '') }}/" + kdRuangan;
            var params = new URLSearchParams({
                data1: data1, data2: data2, data3: data3, data4: data4, data5: data5
            });

            window.open(url + '?' + params.toString(), '_blank');
        }

        function printSesuaiRekBNI() {
            var kdRuangan = $("#kd-ruangan").val();
            
            if (!kdRuangan || kdRuangan === '0') {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Print tidak tersedia untuk pegawai yang belum memiliki ruangan.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            var data1 = document.getElementById("kerja1").checked ? 1 : null;
            var data2 = document.getElementById("kerja2").checked ? 2 : null;
            var data3 = document.getElementById("kerja3").checked ? 3 : null;
            var data4 = document.getElementById("kerja4").checked ? 4 : null;
            var data5 = document.getElementById("kerja5").checked ? 7 : null;

            if (!data1 && !data2 && !data3 && !data4 && !data5) {
                data1 = "a";
            }

            var url = "{{ route('admin.karyawan-ruangan.print-rek-bni', '') }}/" + kdRuangan;
            var params = new URLSearchParams({
                data1: data1, data2: data2, data3: data3, data4: data4, data5: data5
            });

            window.open(url + '?' + params.toString(), '_blank');
        }

        function printDataPegawai() {
            var kdRuangan = $("#kd-ruangan").val();
            
            if (!kdRuangan || kdRuangan === '0') {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Print tidak tersedia untuk pegawai yang belum memiliki ruangan.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            var data1 = document.getElementById("kerja1").checked ? 1 : null;
            var data2 = document.getElementById("kerja2").checked ? 2 : null;
            var data3 = document.getElementById("kerja3").checked ? 3 : null;
            var data4 = document.getElementById("kerja4").checked ? 4 : null;
            var data5 = document.getElementById("kerja5").checked ? 7 : null;

            if (!data1 && !data2 && !data3 && !data4 && !data5) {
                data1 = "a";
            }

            var url = "{{ route('admin.karyawan-ruangan.print-data', '') }}/" + kdRuangan;
            var params = new URLSearchParams({
                data1: data1, data2: data2, data3: data3, data4: data4, data5: data5
            });

            window.open(url + '?' + params.toString(), '_blank');
        }

        function exportToFP() {
            var kdRuangan = $("#kd-ruangan").val();
            
            if (!kdRuangan || kdRuangan === '0') {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Export tidak tersedia untuk pegawai yang belum memiliki ruangan.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            var data1 = document.getElementById("kerja1").checked ? 1 : null;
            var data2 = document.getElementById("kerja2").checked ? 2 : null;
            var data3 = document.getElementById("kerja3").checked ? 3 : null;
            var data4 = document.getElementById("kerja4").checked ? 4 : null;
            var data5 = document.getElementById("kerja5").checked ? 7 : null;

            if (!data1 && !data2 && !data3 && !data4 && !data5) {
                data1 = "a";
            }

            var url = "{{ route('admin.karyawan-ruangan.export-fp', '') }}/" + kdRuangan;
            var params = new URLSearchParams({
                data1: data1, data2: data2, data3: data3, data4: data4, data5: data5
            });

            window.open(url + '?' + params.toString(), '_blank');
        }

        function exportExcel() {
            var kdRuangan = $("#kd-ruangan").val();
            
            if (!kdRuangan || kdRuangan === '0') {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Export tidak tersedia untuk pegawai yang belum memiliki ruangan.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            var data1 = document.getElementById("kerja1").checked ? 1 : null;
            var data2 = document.getElementById("kerja2").checked ? 2 : null;
            var data3 = document.getElementById("kerja3").checked ? 3 : null;
            var data4 = document.getElementById("kerja4").checked ? 4 : null;
            var data5 = document.getElementById("kerja5").checked ? 7 : null;

            if (!data1 && !data2 && !data3 && !data4 && !data5) {
                data1 = "a";
            }

            var url = "{{ route('admin.karyawan-ruangan.export-excel', '') }}/" + kdRuangan;
            var params = new URLSearchParams({
                data1: data1, data2: data2, data3: data3, data4: data4, data5: data5
            });

            window.open(url + '?' + params.toString(), '_blank');
        }

        $(document).ready(function() {
            // Initialize DataTable jika ada ruangan yang dipilih
            @if($kdRuangan !== null)
                var ajaxUrl = "{{ route('admin.karyawan-ruangan.index', $kdRuangan ?: '0') }}";
                
                table = $('#karyawan-ruangan-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: ajaxUrl,
                        type: 'GET',
                        data: function (d) {
                            // Ambil filter status kerja
                            if ($('#kerja1').is(':checked')) d.pns = '1';
                            if ($('#kerja2').is(':checked')) d.honor = '2';
                            if ($('#kerja3').is(':checked')) d.kontrak = '3';
                            if ($('#kerja4').is(':checked')) d.partime = '4';
                            if ($('#kerja5').is(':checked')) d.pppk = '7';
                        },
                        dataSrc: function(json) {
                            return json.data;
                        }
                    },
                    columns: [
                        { data: 'id_pegawai', name: 'KD_KARYAWAN' },
                        { data: 'nama_lengkap', name: 'NAMA' },
                        { data: 'jenis_kelamin', name: 'JENIS_KELAMIN', searchable: false },
                        { data: 'golongan', name: 'PANGKAT' },
                        { data: 'masa_kerja_thn', name: 'MASA_KERJA_THN' },
                        { data: 'masa_kerja_bulan', name: 'MASA_KERJA_BULAN' },
                        { data: 'eselon', name: 'eselon', orderable: false, searchable: false },
                        { data: 'pendidikan', name: 'JENJANG_DIDIK' },
                        { data: 'sub_detail', name: 'SUB_DETAIL' },
                        { data: 'status_kerja', name: 'STATUS_KERJA' },
                        { data: 'rek_bni_syariah', name: 'REK_BNI_SYARIAH' },
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
                    lengthMenu: [[10, 25, 50, 100, 200, 500, 1000, {{ $totalKaryawan }}], [10, 25, 50, 100, 200, 500, 1000, "Semua"]],
                });

                table.on('draw', function() {
                    KTMenu.createInstances();
                });
            @endif

            // Search functionality
            var searchTimer;
            $('[data-kt-ruangan-table-filter="search"]').on('keyup', function() {
                if (table) {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(function() {
                        table.search(this.value).draw();
                    }.bind(this), 500);
                }
            });

            // Page length change
            $('#page-length').on('change', function() {
                if (table) {
                    table.page.len(this.value).draw();
                }
            });

            // Status filter change - auto apply when checkbox changes
            $('.status-filter-checkbox').on('change', function() {
                if (table) {
                    table.draw();
                }
            });
        });
    </script>
@endpush