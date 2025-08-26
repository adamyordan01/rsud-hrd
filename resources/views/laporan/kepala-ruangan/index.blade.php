@extends('layouts.backend', ['title' => 'Laporan Kepala Ruangan'])

@push('styles')
<style>
    #table-kepala th {
        font-size: 11px;
        vertical-align: middle;
        text-align: center;
    }
    
    #table-kepala td {
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
    
    .symbol img {
        border-radius: 50%;
        object-fit: cover;
    }
    
    #table-kepala thead th {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    #table-kepala tbody td {
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
                    Daftar Kepala Ruangan / Instalasi / Unit
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
                    <li class="breadcrumb-item text-muted">Kepala Ruangan</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printKepala()">
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
                            <h3 class="fw-bold">Daftar Kepala Ruangan / Instalasi / Unit {{ date('Y') }}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="filter-container">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Pilih Bulan dan Tahun</label>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="bulan" id="bulan-kepala">
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
                                    <select class="form-select" name="tahun" id="tahun-kepala">
                                        <option value="">-- PILIH TAHUN --</option>
                                        @for ($i = 2019; $i <= date('Y'); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary" onclick="lihatKepala()">
                                        <i class="ki-outline ki-eye fs-2"></i>
                                        Lihat
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Header RSUD -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="100" class="text-center">
                                            <img src="{{ asset('assets/media/images/Langsa.png') }}" width="80" alt="Logo">
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold fs-4 mb-1">PEMERINTAH KOTA LANGSA</div>
                                            <div class="fw-bold fs-2 mb-2">RUMAH SAKIT UMUM DAERAH LANGSA</div>
                                            <div class="fs-6">
                                                Alamat : Jln. Jend. A. Yani No.1 Kota Langsa Provinsi Pemerintah Aceh,<br>
                                                Telp. (0641) 22051 22800 (IGD) Fax. (0641) 22051<br>
                                                E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,<br>
                                                Website : www.rsud.langsakota.go.id<br>
                                                <strong>KOTA LANGSA</strong>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <hr class="border-dark border-2">
                                <hr class="border-secondary">
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-kepala">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive" id="data-kepala">
                            <table class="table table-striped table-bordered" id="table-kepala" width="100%">
                                <thead>
                                    <tr>
                                        <th rowspan="3" class="text-center align-middle">No.</th>
                                        <th rowspan="3" class="text-center align-middle">Foto</th>
                                        <th rowspan="3" class="text-center align-middle">
                                            Nama<br>
                                            Tempat & Tanggal Lahir<br>
                                            NIP / No. KARPEG / ID Peg.
                                        </th>
                                        <th rowspan="3" class="text-center align-middle">L/P</th>
                                        <th colspan="4" class="text-center align-middle">Kepangkatan sekarang</th>
                                        <th rowspan="3" class="text-center align-middle">Eselon</th>
                                        <th rowspan="3" class="text-center align-middle" width="100">TMT</th>
                                        <th rowspan="3" class="text-center align-middle">Jabatan non-struktural</th>
                                        <th rowspan="3" class="text-center align-middle" width="100">TMT</th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">Pangkat / Gol.</th>
                                        <th rowspan="2" class="text-center align-middle" width="100">TMT</th>
                                        <th colspan="2" class="text-center align-middle">Masa kerja</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Thn.</th>
                                        <th class="text-center">Bln.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan dimuat via AJAX -->
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
            $('#bulan-kepala').val('{{ str_pad(date("m"), 2, "0", STR_PAD_LEFT) }}');
            $('#tahun-kepala').val('{{ date("Y") }}');
            
            // Inisialisasi DataTable
            initDataTable();
        });

        function initDataTable() {
            if (table) {
                table.destroy();
            }
            
            table = $('#table-kepala').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 800, // Debounce delay 800ms
                ajax: {
                    url: '{{ route("admin.laporan.kepala-ruangan.index") }}',
                    data: function(d) {
                        d.bulan = $('#bulan-kepala').val();
                        d.tahun = $('#tahun-kepala').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'foto', name: 'foto', orderable: false, searchable: false },
                    { data: 'nama_lengkap', name: 'nama_lengkap', orderable: true, searchable: true },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin', orderable: true, searchable: true },
                    { data: 'pangkat_sekarang', name: 'pangkat_sekarang', orderable: true, searchable: true },
                    { data: 'tmt_gol_sekarang', name: 'tmt_gol_sekarang', orderable: true, searchable: false },
                    { data: 'masa_kerja_thn', name: 'masa_kerja_thn', orderable: true, searchable: false },
                    { data: 'masa_kerja_bulan', name: 'masa_kerja_bulan', orderable: true, searchable: false },
                    { data: 'eselon_info', name: 'eselon_info', orderable: true, searchable: true },
                    { data: 'tmt_eselon', name: 'tmt_eselon', orderable: true, searchable: false },
                    { data: 'jabatan_nonstruktur', name: 'jabatan_nonstruktur', orderable: true, searchable: true },
                    { data: 'tmt_jabatan_struktural', name: 'tmt_jabatan_struktural', orderable: true, searchable: false }
                ],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: '<"top"<"d-flex justify-content-between"<"d-flex align-items-center"l><"d-flex align-items-center"f>>>rt<"bottom"<"d-flex justify-content-between"ip>>',
                language: {
                    processing: "Sedang memproses...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Data tidak tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    search: "Cari:",
                    searchPlaceholder: "Cari nama, NIP, pangkat, dll...",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                initComplete: function() {
                    // Perbaiki placeholder search
                    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Cari nama, NIP, pangkat, dll...');
                    
                    // Tambahan debounce manual untuk search yang lebih responsif
                    let searchTimeout;
                    $('.dataTables_filter input[type="search"]').off('keyup search input').on('keyup search input', function(e) {
                        const searchTerm = this.value;
                        
                        // Clear timeout sebelumnya
                        clearTimeout(searchTimeout);
                        
                        // Set timeout baru untuk debounce
                        searchTimeout = setTimeout(() => {
                            if (table.search() !== searchTerm) {
                                table.search(searchTerm).draw();
                            }
                        }, 500); // 500ms debounce
                        
                        // Jika Enter ditekan, search langsung
                        if (e.keyCode === 13) {
                            clearTimeout(searchTimeout);
                            table.search(searchTerm).draw();
                        }
                    });
                }
            });
        }

        function lihatKepala() {
            const bulan = $('#bulan-kepala').val();
            const tahun = $('#tahun-kepala').val();

            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }

            // Cek data terlebih dahulu
            $.ajax({
                url: '{{ route("admin.laporan.kepala-ruangan.check-data") }}',
                type: 'GET',
                data: {
                    bulan: bulan,
                    tahun: tahun
                },
                success: function(response) {
                    if (response.count > 0) {
                        // Reload DataTable dengan parameter baru
                        table.ajax.reload();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: `Ditemukan ${response.count} data kepala ruangan untuk periode ${getMonthName(bulan)} ${tahun}`
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Informasi',
                            text: `Tidak ada data kepala ruangan untuk periode ${getMonthName(bulan)} ${tahun}`
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengambil data'
                    });
                }
            });
        }

        function printKepala() {
            const bulan = $('#bulan-kepala').val();
            const tahun = $('#tahun-kepala').val();

            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }

            // Buka halaman print dalam window/tab baru
            const printUrl = `{{ route('admin.laporan.kepala-ruangan.print') }}?bln=${bulan}&thn=${tahun}`;
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
    </script>
@endpush
