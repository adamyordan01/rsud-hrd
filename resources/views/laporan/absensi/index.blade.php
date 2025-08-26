@extends('layouts.backend', ['title' => 'Laporan Absensi Pegawai'])

@push('styles')
<style>
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
    
    .preview-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: #fff;
        padding: 15px;
        margin-top: 20px;
        display: none;
    }
    
    .special-buttons {
        display: none;
        /* margin-top: 10px; */
    }
    
    .absen-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
    }
    
    .absen-table th,
    .absen-table td {
        border: 1px solid #dee2e6;
        padding: 3px;
        text-align: center;
        vertical-align: middle;
    }
    
    .absen-table th {
        background-color: #f8f9fa;
        font-weight: bold;
        font-size: 8px;
    }
    
    .absen-table .employee-name {
        font-weight: bold;
        text-align: left;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Laporan Absensi Pegawai
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
                    <li class="breadcrumb-item text-muted">Absensi Pegawai</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printAbsensi()" id="btn-print-normal">
                    <i class="ki-outline ki-printer fs-2"></i>
                    Print
                </button>
                <div id="btn-print-special" class="special-buttons">
                    <button type="button" class="btn btn-light-success btn-sm" onclick="printRM()">
                        <i class="ki-outline ki-printer fs-2"></i>
                        Print RM
                    </button>
                    <button type="button" class="btn btn-light-warning btn-sm" onclick="printIGD()">
                        <i class="ki-outline ki-printer fs-2"></i>
                        Print IGD
                    </button>
                </div>
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
                            <h3 class="fw-bold text-gray-800">Filter Absensi</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="filter-container">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Ruangan:</label>
                                    <select class="form-select form-select-solid" id="ruangan-absensi" data-control="select2" data-placeholder="Pilih Ruangan">
                                        <option value="">-- PILIH RUANGAN --</option>
                                        @foreach($ruangans as $ruangan)
                                            <option value="{{ $ruangan->kd_ruangan }}">{{ $ruangan->ruangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Bulan:</label>
                                    <select class="form-select form-select-solid" id="bulan-absensi" data-control="select2" data-placeholder="Pilih Bulan">
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
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Tahun:</label>
                                    <select class="form-select form-select-solid" id="tahun-absensi" data-control="select2" data-placeholder="Pilih Tahun">
                                        <option value="">-- PILIH TAHUN --</option>
                                        @php
                                            $currentYear = date('Y');
                                            $currentMonth = date('m');
                                        @endphp
                                        <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                                        <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                                        @if($currentMonth >= 11)
                                            <option value="{{ $currentYear + 1 }}">{{ $currentYear + 1 }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">&nbsp;</label>
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="lihatAbsensi()">
                                            <i class="ki-outline ki-magnifier fs-3"></i>
                                            Lihat Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-absensi">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Memuat data absensi...</p>
                        </div>

                        <!-- Preview Container -->
                        <div class="preview-container" id="preview-container-absensi">
                            <div id="preview-content-absensi">
                                <!-- Content will be loaded here -->
                            </div>
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
    $(document).ready(function() {
        // Set default bulan dan tahun ke bulan dan tahun sekarang
        const currentMonth = '{{ str_pad(date("m"), 2, "0", STR_PAD_LEFT) }}';
        const currentYear = '{{ date("Y") }}';
        
        $('#bulan-absensi').val(currentMonth);
        $('#tahun-absensi').val(currentYear);
        
        // Handle ruangan change untuk menampilkan tombol khusus RM
        $('#ruangan-absensi').on('change', function() {
            const ruanganValue = $(this).val();
            if (ruanganValue == '19') { // Kode ruangan RM
                $('#btn-print-special').show();
            } else {
                $('#btn-print-special').hide();
            }
        });
    });

    function lihatAbsensi() {
        const bulan = $('#bulan-absensi').val();
        const tahun = $('#tahun-absensi').val();
        const ruangan = $('#ruangan-absensi').val();

        if (!bulan || !tahun || !ruangan) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih ruangan, bulan dan tahun terlebih dahulu!'
            });
            return;
        }

        // Show loading
        $('#loader-absensi').show();
        $('#preview-container-absensi').hide();

        // Load preview via AJAX
        $.ajax({
            url: '{{ route("admin.laporan.absensi.preview") }}',
            method: 'GET',
            data: {
                bulan: bulan,
                tahun: tahun,
                ruangan: ruangan
            },
            success: function(response) {
                $('#preview-content-absensi').html(response);
                $('#loader-absensi').hide();
                $('#preview-container-absensi').show();
            },
            error: function(xhr) {
                $('#loader-absensi').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data!'
                });
            }
        });
    }

    function printAbsensi() {
        const bulan = $('#bulan-absensi').val();
        const tahun = $('#tahun-absensi').val();
        const ruangan = $('#ruangan-absensi').val();

        if (!bulan || !tahun || !ruangan) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih ruangan, bulan dan tahun terlebih dahulu!'
            });
            return;
        }

        // Buka halaman print dalam window/tab baru
        const printUrl = `{{ route('admin.laporan.absensi.print') }}?ruang=${ruangan}&bln=${bulan}&thn=${tahun}`;
        window.open(printUrl, '_blank');
    }

    function printRM() {
        const bulan = $('#bulan-absensi').val();
        const tahun = $('#tahun-absensi').val();
        const ruangan = $('#ruangan-absensi').val();

        if (!bulan || !tahun || !ruangan) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih ruangan, bulan dan tahun terlebih dahulu!'
            });
            return;
        }

        // Print khusus RM
        const printUrl = `{{ route('admin.laporan.absensi.print') }}?ruang=${ruangan}&bln=${bulan}&thn=${tahun}&kd=RM`;
        window.open(printUrl, '_blank');
    }

    function printIGD() {
        const bulan = $('#bulan-absensi').val();
        const tahun = $('#tahun-absensi').val();
        const ruangan = $('#ruangan-absensi').val();

        if (!bulan || !tahun || !ruangan) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih ruangan, bulan dan tahun terlebih dahulu!'
            });
            return;
        }

        // Print khusus IGD
        const printUrl = `{{ route('admin.laporan.absensi.print') }}?ruang=${ruangan}&bln=${bulan}&thn=${tahun}&kd=IGD`;
        window.open(printUrl, '_blank');
    }
</script>
@endpush
