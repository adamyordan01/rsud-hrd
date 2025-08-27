@extends('layouts.backend', ['title' => 'Laporan Satya Lencana'])

@push('styles')
<style>
    .loader-container {
        text-align: center;
        padding: 50px;
        display: none;
    }
    
    .data-table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }
    
    .data-table th,
    .data-table td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: center;
        vertical-align: middle;
        font-size: 12px;
    }
    
    .data-table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .data-table .category-header {
        background-color: #d1ecf1;
        font-weight: bold;
        font-size: 12pt;
        text-align: center;
        font-family: "Times New Roman";
    }
    
    .data-table .group-header {
        background-color: #e9ecef;
        font-weight: bold;
        text-align: left;
        font-size: 11pt;
    }
    
    .nama-pegawai {
        text-align: left;
        font-weight: bold;
    }
    
    .no-data {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 50px;
    }
    
    .line1 {
        background: #282828;
        height: 3px;
        margin: 0px;
        border: none;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    
    .line2 {
        background: #282828;
        margin: 0px;
        margin-bottom: 5px;
        height: 1px !important;
        border: none;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Laporan Satya Lencana
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
                    <li class="breadcrumb-item text-muted">Satya Lencana</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printSatyaLencana()">
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
                            <h3 class="fw-bold text-gray-800">
                                <button type="button" class="btn btn-primary btn-sm me-3" onclick="loadSatyaLencana()">
                                    <i class="ki-outline ki-magnifier fs-3"></i>
                                    Lihat Data
                                </button>
                                Penghargaan Tanda Kehormatan Satya Lencana
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Header RSUD -->
                        <div class="row mb-3" id="header-rsud" style="display: none;">
                            <div class="col-md-12">
                                <table class="text-center" width="100%" style="margin-bottom: 20px;">
                                    <tr>
                                        <td width="140">
                                            <img src="{{ asset('assets/media/images/Langsa.png') }}" width="80" alt="Logo RSUD Langsa">
                                        </td>
                                        <td>
                                            <p style="margin: 0;">
                                                <b style="font-size: 14pt;">PEMERINTAH KOTA LANGSA</b><br>
                                                <b style="font-size: 20pt;">RUMAH SAKIT UMUM DAERAH LANGSA</b><br>
                                                <b style="font-size: 10pt;">Alamat : Jln. Jend. A. Yani No.1 Kota Langsa Provinsi Pemerintah Aceh,</b><br>
                                                <b style="font-size: 8pt;">Telp. (0641) 22051 22800 (IGD) Fax. (0641) 22051</b><br>
                                                <b style="font-size: 10pt;">E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,</b><br>
                                                <b style="font-size: 10pt;">Website : www.rsud.langsakota.go.id</b><br>
                                                <b style="font-size: 12pt;">KOTA LANGSA</b>
                                            </p>
                                        </td>
                                        <td width="140"></td>
                                    </tr>
                                </table>
                                <hr class="line1">
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-sl">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Sedang memuat data...</p>
                        </div>

                        <!-- Data Container -->
                        <div id="data-container-sl" style="display: none;">
                            <table class="data-table" id="table-sl">
                                <!-- Data akan diisi melalui JavaScript -->
                            </table>
                        </div>

                        <!-- No Data -->
                        <div id="no-data-sl" class="no-data" style="display: none;">
                            <i class="ki-outline ki-information-2 fs-3x text-muted"></i>
                            <p class="mt-3">Tidak ada data pegawai yang memenuhi kriteria Satya Lencana</p>
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
    function loadSatyaLencana() {
        // Show loading
        $('#loader-sl').show();
        $('#data-container-sl').hide();
        $('#no-data-sl').hide();
        $('#header-rsud').hide();
        
        // AJAX request
        $.ajax({
            url: '{{ route("admin.laporan.satya-lencana.index") }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#loader-sl').hide();
                
                if (response.success && response.data && response.data.length > 0) {
                    buildTableSatyaLencana(response.data);
                    $('#header-rsud').show();
                    $('#data-container-sl').show();
                } else {
                    $('#no-data-sl').show();
                }
            },
            error: function() {
                $('#loader-sl').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memuat data. Silakan coba lagi!'
                });
            }
        });
    }

    function buildTableSatyaLencana(data) {
        let html = '';
        let globalNo = 0;
        
        data.forEach(function(category) {
            // Header kategori
            html += `
                <tr class="category-header">
                    <td colspan="10">PENGHARGAAN TANDA KEHORMATAN SATYA LENCANA ${category.title}</td>
                </tr>
                <tr>
                    <th>NO.</th>
                    <th>NIP LAMA</th>
                    <th>NIP BARU</th>
                    <th>NAMA</th>
                    <th>GOLONGAN</th>
                    <th>MASA KERJA THN</th>
                    <th>MASA KERJA BLN</th>
                    <th>PENDIDIKAN TERAKHIR</th>
                    <th>JURUSAN</th>
                    <th>TAHUN LULUS</th>
                </tr>
            `;
            
            // Loop jenis tenaga dalam kategori
            category.jenis_tenaga_groups.forEach(function(group) {
                // Header jenis tenaga
                html += `
                    <tr class="group-header">
                        <td colspan="10">${group.jenis_tenaga}</td>
                    </tr>
                `;
                
                // Detail pegawai
                group.pegawai_list.forEach(function(pegawai) {
                    globalNo++;
                    const namaLengkap = `${pegawai.gelar_depan || ''} ${pegawai.nama} ${pegawai.gelar_belakang || ''}`.trim();
                    
                    html += `
                        <tr>
                            <td>${globalNo}</td>
                            <td>${pegawai.nip_lama || '-'}</td>
                            <td>${pegawai.nip_baru || '-'}</td>
                            <td class="nama-pegawai">${namaLengkap}</td>
                            <td>${pegawai.kd_gol_sekarang || '-'}</td>
                            <td>${pegawai.masa_kerja_thn || 0}</td>
                            <td>${pegawai.masa_kerja_bulan || 0}</td>
                            <td>${pegawai.jenjang_didik || '-'}</td>
                            <td>${pegawai.jurusan || '-'}</td>
                            <td>${pegawai.tahun_lulus || '-'}</td>
                        </tr>
                    `;
                });
            });
        });
        
        $('#table-sl').html(html);
    }

    function printSatyaLencana() {
        // Buka halaman print dalam window/tab baru
        const printUrl = `{{ route('admin.laporan.satya-lencana.print') }}`;
        window.open(printUrl, '_blank');
    }
</script>
@endpush
