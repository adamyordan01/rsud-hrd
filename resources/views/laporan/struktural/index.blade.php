@extends('layouts.backend', ['title' => 'Daftar Struktural'])

@push('styles')
<style>
    #struktural-table th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
    }
    
    #struktural-table td {
        font-size: 11px;
        vertical-align: top;
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
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Daftar Struktural
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
                    <li class="breadcrumb-item text-muted">Struktural</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button class="btn btn-flex btn-secondary h-40px fs-7 fw-bold" onclick="printStruktural()">
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
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Data Struktural</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Kelola data jabatan struktural pegawai</span>
                    </h3>
                </div>
            </div>

            <div class="card-body pt-0">
                {{-- Filter Container --}}
                <div class="filter-container">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fs-6 fw-semibold mb-3">Pilih Bulan dan Tahun</label>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-select" name="bulan" id="bulan-struktural" data-control="select2">
                                <option value="">-- PILIH BULAN --</option>
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
                            <select class="form-control form-select" name="tahun" id="tahun-struktural" data-control="select2">
                                <option value="">-- PILIH TAHUN --</option>
                                @for ($i = 2019; $i <= date("Y"); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-info me-2" onclick="lihatStruktural()">
                                <i class="ki-duotone ki-eye fs-2"></i>
                                Lihat
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Header Kop Surat --}}
                <div class="mb-5">
                    <table class="table table-borderless text-center" width="100%">
                        <tr>
                            <td width="100">
                                <img src="{{ asset('assets/media/images/Langsa.png') }}" width="80" alt="Logo">
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold fs-5 mb-1">PEMERINTAH KOTA LANGSA</div>
                                    <div class="fw-bold fs-3 mb-2">RUMAH SAKIT UMUM DAERAH LANGSA</div>
                                    <div class="fs-7 text-muted">
                                        Alamat : Jln. Jend. A. Yani No.1 Kota Langsa Provinsi Pemerintah Aceh,<br>
                                        Telp. (0641) 22051 22800 (IGD) Fax. (0641) 22051<br>
                                        E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,<br>
                                        Website : www.rsud.langsakota.go.id<br>
                                        <div class="fw-bold">KOTA LANGSA</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <hr class="border-dark border-2">
                    <hr class="border-secondary">
                </div>

                {{-- Loader --}}
                <div class="loader-container" id="loader-struktural">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-3">Memuat data...</div>
                </div>

                {{-- DataTable --}}
                <div id="data-struktural">
                    <div class="table-responsive">
                        <table id="struktural-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th rowspan="3" class="min-w-25px">No.</th>
                                    <th rowspan="3" class="min-w-50px">Foto</th>
                                    <th rowspan="3" class="min-w-200px">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG / ID Peg.</th>
                                    <th rowspan="3" class="min-w-25px">L/P</th>
                                    <th colspan="4" class="min-w-150px">Kepangkatan Sekarang</th>
                                    <th rowspan="3" class="min-w-75px">Eselon</th>
                                    <th rowspan="3" class="min-w-75px">TMT</th>
                                    <th rowspan="3" class="min-w-150px">Jabatan Struktural</th>
                                    <th rowspan="3" class="min-w-75px">TMT</th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="min-w-100px">Pangkat / Gol.</th>
                                    <th rowspan="2" class="min-w-75px">TMT</th>
                                    <th colspan="2" class="min-w-75px">Masa Kerja</th>
                                </tr>
                                <tr>
                                    <th class="min-w-35px">Thn.</th>
                                    <th class="min-w-35px">Bln.</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <input type="hidden" name="hasil" id="hasil" value="1">
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

        function initDataTable(bulan = null, tahun = null) {
            if (table) {
                table.destroy();
            }
            
            table = $('#struktural-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.laporan.struktural.index') }}",
                    type: 'GET',
                    data: {
                        bulan: bulan,
                        tahun: tahun
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'foto', name: 'foto', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'nama_lengkap', name: 'nama', orderable: false },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'pangkat_sekarang', name: 'pangkat', orderable: false, searchable: false },
                    { data: 'tmt_gol_sekarang', name: 'tmt_gol_sekarang', orderable: false, searchable: false },
                    { data: 'masa_kerja_thn', name: 'masa_kerja_thn', orderable: false, searchable: false },
                    { data: 'masa_kerja_bulan', name: 'masa_kerja_bulan', orderable: false, searchable: false },
                    { data: 'eselon_info', name: 'eselon', orderable: false, searchable: false },
                    { data: 'tmt_eselon', name: 'tmt_eselon', orderable: false, searchable: false },
                    { data: 'jabatan_struktural', name: 'jab_struk', orderable: false, searchable: false },
                    { data: 'tmt_jabatan_struktural', name: 'tmt_jabatan_struktural', orderable: false, searchable: false }
                ],
                order: [], // Kosongkan order karena sudah dihandle di backend
                ordering: false, // Disable ordering karena sudah diurutkan di backend
                searching: true, // Enable searching
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                language: {
                    processing: "Memuat...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir", 
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }

        function lihatStruktural() {
            var bulan = $("#bulan-struktural").val();
            var tahun = $("#tahun-struktural").val();
            
            if (bulan == "" || tahun == "") {
                Swal.fire("Perhatian!", "Pilih bulan dan tahun terlebih dahulu.", "warning");
                return;
            }
            
            $("#data-struktural").hide();
            $("#loader-struktural").show();
            
            // Check if data exists
            $.ajax({
                url: "{{ route('admin.laporan.struktural.check-data') }}",
                type: 'GET',
                data: {
                    bulan: bulan,
                    tahun: tahun
                },
                success: function(response) {
                    if (response.count > 0) {
                        $("#hasil").val(1);
                        initDataTable(bulan, tahun);
                    } else {
                        $("#hasil").val(0);
                        $("#data-struktural").html('<div class="alert alert-warning text-center">Tidak Ada Data</div>');
                    }
                    $("#data-struktural").show();
                    $("#loader-struktural").hide();
                },
                error: function() {
                    $("#hasil").val(0);
                    $("#data-struktural").html('<div class="alert alert-danger text-center">Terjadi kesalahan saat memuat data</div>');
                    $("#data-struktural").show();
                    $("#loader-struktural").hide();
                }
            });
        }

        function printStruktural() {
            var bulan = $("#bulan-struktural").val();
            var tahun = $("#tahun-struktural").val();
            var hasil = $("#hasil").val();
            var today = new Date();
            var bln = ("0" + (today.getMonth() + 1)).slice(-2);
            var thn = today.getFullYear();
            
            if (bulan == "" || tahun == "") {
                window.open("{{ route('admin.laporan.struktural.print') }}?bln=" + bln + "&thn=" + thn, "_blank");
            } else {
                if (hasil == "1") {
                    window.open("{{ route('admin.laporan.struktural.print') }}?bln=" + bulan + "&thn=" + tahun, "_blank");
                } else {
                    Swal.fire("Perhatian", "Gak ada datanya !!!", "warning");
                }
            }
        }

        $(document).ready(function() {
            // Initialize with current data
            initDataTable();
        });
    </script>
@endpush