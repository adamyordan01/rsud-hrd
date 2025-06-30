@extends('layouts.backend', ['title' => 'Daftar PNS Per-Usia'])

@push('styles')
<style>
    #pns-usia-table th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
    }
    
    #pns-usia-table td {
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
                        Daftar PNS Per-Usia
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
                        <li class="breadcrumb-item text-muted">PNS Per-Usia</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button class="btn btn-flex btn-secondary h-40px fs-7 fw-bold" onclick="printPnsUsia()">
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
                            <span class="card-label fw-bold fs-3 mb-1">Data PNS Per-Usia</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Kelola data PNS berdasarkan rentang usia</span>
                        </h3>
                    </div>
                </div>

                <div class="card-body pt-0">
                    {{-- Filter Container --}}
                    <div class="filter-container">
                        <div class="row align-items-end">
                            <div class="col-md-2">
                                <label class="form-label fs-6 fw-semibold mb-3">Pilih Rentang Usia</label>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control form-select" name="awal" id="awal" data-control="select2">
                                    <option value="">-- AWAL --</option>
                                    @for ($i = 1; $i <= 100; $i++)
                                        <option value="{{ sprintf('%02s', $i) }}">{{ sprintf('%02s', $i) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1 text-center" style="margin-top: 10px;">
                                Tahun s/d
                            </div>
                            <div class="col-md-2">
                                <select class="form-control form-select" name="akhir" id="akhir" data-control="select2">
                                    <option value="">-- AKHIR --</option>
                                    @for ($j = 1; $j <= 100; $j++)
                                        <option value="{{ sprintf('%02s', $j) }}">{{ sprintf('%02s', $j) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-1 text-center" style="margin-top: 10px;">
                                Tahun
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-info me-2" onclick="lihatPnsUsia()">
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
                    <div class="loader-container" id="loader-pns-usia">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-3">Memuat data...</div>
                    </div>

                    {{-- DataTable --}}
                    <div id="data-pns-usia">
                        <div class="table-responsive">
                            <table id="pns-usia-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th rowspan="3" class="min-w-25px">No.</th>
                                        <th rowspan="3" class="min-w-200px">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG</th>
                                        <th rowspan="3" class="min-w-25px">L/P</th>
                                        <th rowspan="3" class="min-w-50px">Usia Thn.</th>
                                        <th colspan="2" class="min-w-100px">Kepangkatan CPNS</th>
                                        <th colspan="4" class="min-w-150px">Kepangkatan Sekarang</th>
                                        <th colspan="2" class="min-w-100px">Eselon</th>
                                        <th rowspan="3" class="min-w-150px">Pendidikan Terakhir<br>Jurusan</th>
                                        <th rowspan="3" class="min-w-75px">Lulus Tahun</th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2" class="min-w-100px">Pangkat / Gol.</th>
                                        <th rowspan="2" class="min-w-75px">TMT</th>
                                        <th rowspan="2" class="min-w-100px">Pangkat / Gol.</th>
                                        <th rowspan="2" class="min-w-75px">TMT</th>
                                        <th colspan="2" class="min-w-75px">Masa Kerja</th>
                                        <th rowspan="2" class="min-w-100px">Nama</th>
                                        <th rowspan="2" class="min-w-75px">TMT</th>
                                    </tr>
                                    <tr>
                                        <th class="min-w-35px">Thn.</th>
                                        <th class="min-w-35px">Bln.</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <input type="hidden" name="hasil_usia" id="hasil_usia" value="0">
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

        function initDataTable(awal = null, akhir = null) {
            if (table) {
                table.destroy();
            }
            
            table = $('#pns-usia-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.laporan.pns-usia.index') }}",
                    type: 'GET',
                    data: {
                        awal: awal,
                        akhir: akhir
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_lengkap', name: 'NAMA', orderable: false },
                    { data: 'jenis_kelamin', name: 'JENIS', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'usia', name: 'umur', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'pangkat_cpns', name: 'PANGKAT_MASUK', orderable: false, searchable: false },
                    { data: 'tmt_cpns', name: 'TMT_GOL_MASUK', orderable: false, searchable: false },
                    { data: 'pangkat_sekarang', name: 'PANGKAT_SEKARANG', orderable: false, searchable: false },
                    { data: 'tmt_gol_sekarang', name: 'TMT_GOL_SEKARANG', orderable: false, searchable: false },
                    { data: 'masa_kerja_thn', name: 'MASA_KERJA_THN', orderable: false, searchable: false },
                    { data: 'masa_kerja_bulan', name: 'MASA_KERJA_BULAN', orderable: false, searchable: false },
                    { data: 'eselon_info', name: 'eselon', orderable: false, searchable: false },
                    { data: 'tmt_eselon', name: 'TMT_ESELON', orderable: false, searchable: false },
                    { data: 'pendidikan', name: 'JENJANG_DIDIK', orderable: false, searchable: false },
                    { data: 'tahun_lulus', name: 'TAHUN_LULUS', orderable: false, searchable: false }
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

        function lihatPnsUsia() {
            var awal = $("#awal").val();
            var akhir = $("#akhir").val();
            
            if (awal == "" || akhir == "") {
                Swal.fire("Perhatian!", "Pilih rentang usia terlebih dahulu.", "warning");
                return;
            }

            if (parseInt(awal) > parseInt(akhir)) {
                Swal.fire("Perhatian!", "Harap isi rentang usia yang benar. " + awal + " dan " + akhir, "warning");
                return;
            }
            
            $("#data-pns-usia").hide();
            $("#loader-pns-usia").show();
            
            // Check if data exists
            $.ajax({
                url: "{{ route('admin.laporan.pns-usia.check-data') }}",
                type: 'GET',
                data: {
                    awal: awal,
                    akhir: akhir
                },
                success: function(response) {
                    if (response.count > 0) {
                        $("#hasil_usia").val(1);
                        initDataTable(awal, akhir);
                    } else {
                        $("#hasil_usia").val(0);
                        $("#data-pns-usia").html('<div class="alert alert-warning text-center">Tidak Ada Data untuk rentang usia ini</div>');
                    }
                    $("#data-pns-usia").show();
                    $("#loader-pns-usia").hide();
                },
                error: function() {
                    $("#hasil_usia").val(0);
                    $("#data-pns-usia").html('<div class="alert alert-danger text-center">Terjadi kesalahan saat memuat data</div>');
                    $("#data-pns-usia").show();
                    $("#loader-pns-usia").hide();
                }
            });
        }

        function printPnsUsia() {
            var awal = $("#awal").val();
            var akhir = $("#akhir").val();
            var hasil = $("#hasil_usia").val();
            
            if (awal == "" || akhir == "") {
                Swal.fire("Perhatian", "Harap isi rentang usia terlebih dahulu.", "warning");
            } else {
                if (hasil == "1") {
                    window.open("{{ route('admin.laporan.pns-usia.print') }}?awal=" + awal + "&akhir=" + akhir, "_blank");
                } else {
                    Swal.fire("Perhatian", "Tidak ada data untuk rentang usia ini", "warning");
                }
            }
        }

        $(document).ready(function() {
            // Inisialisasi awal tanpa data
            // initDataTable();
        });
    </script>
@endpush