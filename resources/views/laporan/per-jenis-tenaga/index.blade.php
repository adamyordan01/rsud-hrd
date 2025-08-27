@extends('layouts.backend', ['title' => 'Laporan Jumlah Pegawai Per-Jenis Tenaga'])

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
    
    .data-table .group-header {
        background-color: #e9ecef;
        font-weight: bold;
        text-align: left;
        font-size: 11pt;
    }
    
    .data-table .subtotal-row {
        background-color: #f1f3f4;
        font-weight: bold;
    }
    
    .data-table .total-row {
        background-color: #fff3cd;
        font-weight: bold;
        color: #856404;
    }
    
    .data-table .grand-total-row {
        background-color: #d4edda;
        font-weight: bold;
        color: #155724;
    }
    
    .jenis-tenaga-text {
        text-align: left;
        font-size: 11px;
    }
    
    .no-data {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 50px;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Laporan Jumlah Pegawai Per-Jenis Tenaga
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
                    <li class="breadcrumb-item text-muted">Jumlah Pegawai Per-Jenis Tenaga</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printPerJenisTenaga()">
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
                            <h3 class="fw-bold text-gray-800">Filter Data</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="filter-container">
                            <div class="row">
                                <div class="col-md-2 text-end">
                                    <label class="form-label">Pilih Bulan dan Tahun:</label>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="bulan" id="bulan-pjt">
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
                                    <select class="form-select" name="tahun" id="tahun-pjt">
                                        <option value="">-- PILIH TAHUN --</option>
                                        @for ($i = 2019; $i <= date('Y'); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-info" onclick="lihatPerJenisTenaga()">
                                        <i class="ki-outline ki-eye fs-2"></i>
                                        Lihat
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Header RSUD -->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <table class="text-center w-100">
                                    <tr>
                                        <td width="100">
                                            <img src="{{ asset('assets/media/images/Langsa.png') }}" width="80" alt="Logo RSUD Langsa">
                                        </td>
                                        <td>
                                            <p>
                                                <b style="font-size: 14pt; margin-bottom: 0px;">PEMERINTAH KOTA LANGSA</b><br>
                                                <b style="font-size: 20pt; margin-top: 0px; margin-bottom: 0px;">RUMAH SAKIT UMUM DAERAH LANGSA</b><br>
                                                <b style="font-size: 10pt; margin-top: 0px;">Alamat : Jln. Jend. A. Yani No.1 Kota Langsa Provinsi Pemerintah Aceh,</b>
                                                <b style="font-size: 8pt; margin-top: 0px;">Telp. (0641) 22051 22800 (IGD) Fax. (0641) 22051</b><br>
                                                <b style="font-size: 10pt; margin-top: 0px;">E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id,</b>
                                                <b style="font-size: 10pt; margin-top: 0px;">Website : www.rsud.langsakota.go.id</b><br>
                                                <b style="font-size: 12pt; margin-top: 0px;">KOTA LANGSA</b>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                <hr style="background: #282828; height: 3px; margin-top: 10px;">
                                <hr style="background: #282828; height: 1px; margin-bottom: 5px;">
                                <div class="text-center">
                                    <h4 class="fw-bold">JUMLAH PEGAWAI PER-JENIS TENAGA</h4>
                                    <h5 id="periode-pjt" class="text-uppercase">-</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-pjt">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Sedang memuat data...</p>
                        </div>

                        <!-- Data Container -->
                        <div id="data-container-pjt" style="display: none;">
                            <table class="data-table" id="table-pjt">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No.</th>
                                        <th rowspan="2">JENIS TENAGA</th>
                                        <th colspan="6">STATUS</th>
                                        <th colspan="2">JENIS KELAMIN</th>
                                    </tr>
                                    <tr>
                                        <th>PNS</th>
                                        <th>PPPK</th>
                                        <th>PART TIME</th>
                                        <th>KONTRAK DAERAH</th>
                                        <th>KONTRAK BLUD</th>
                                        <th>THL</th>
                                        <th>LAKI-LAKI</th>
                                        <th>PEREMPUAN</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body-pjt">
                                    <!-- Data akan dimuat di sini -->
                                </tbody>
                            </table>
                        </div>

                        <!-- No Data -->
                        <div id="no-data-pjt" class="no-data" style="display: none;">
                            <i class="ki-outline ki-information-2 fs-3x text-muted"></i>
                            <p class="mt-3">Tidak ada data untuk periode yang dipilih</p>
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
            // Set default ke bulan dan tahun saat ini
            $('#bulan-pjt').val('{{ sprintf("%02d", date("m")) }}');
            $('#tahun-pjt').val('{{ date("Y") }}');
        });

        function lihatPerJenisTenaga() {
            const bulan = $('#bulan-pjt').val();
            const tahun = $('#tahun-pjt').val();
            
            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }
            
            // Show loading
            $('#loader-pjt').show();
            $('#data-container-pjt').hide();
            $('#no-data-pjt').hide();
            
            // Update header periode
            const bulanNama = getBulanNama(bulan);
            $('#periode-pjt').text(`PERIODE ${bulanNama} ${tahun}`);
            
            // AJAX request
            $.ajax({
                url: '{{ route("admin.laporan.per-jenis-tenaga.index") }}',
                type: 'GET',
                data: {
                    bulan: bulan,
                    tahun: tahun
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#loader-pjt').hide();
                    
                    if (response.data && response.data.length > 0) {
                        buildTablePerJenisTenaga(response.data, response.grand_total);
                        $('#data-container-pjt').show();
                    } else {
                        $('#no-data-pjt').show();
                    }
                },
                error: function() {
                    $('#loader-pjt').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memuat data. Silakan coba lagi!'
                    });
                }
            });
        }

        function buildTablePerJenisTenaga(data, grandTotal) {
            let html = '';
            
            data.forEach(function(jenisTenaga, index) {
                // Header jenis tenaga
                html += `
                    <tr class="group-header">
                        <td colspan="10">TENAGA ${jenisTenaga.jenis_tenaga}</td>
                    </tr>
                `;
                
                let no = 1;
                jenisTenaga.details.forEach(function(item) {
                    html += `
                        <tr>
                            <td>${no++}</td>
                            <td class="jenis-tenaga-text">${item.sub_detail}</td>
                            <td>${item.pns}</td>
                            <td>${item.pppk}</td>
                            <td>${item.part_time}</td>
                            <td>${item.kontrak_daerah}</td>
                            <td>${item.kontrak_blud}</td>
                            <td>${item.thl}</td>
                            <td>${item.lk}</td>
                            <td>${item.pr}</td>
                        </tr>
                    `;
                });
                
                // Subtotal per jenis tenaga
                html += `
                    <tr class="subtotal-row">
                        <td colspan="2">JUMLAH</td>
                        <td>${jenisTenaga.subtotal.pns}</td>
                        <td>${jenisTenaga.subtotal.pppk}</td>
                        <td>${jenisTenaga.subtotal.part_time}</td>
                        <td>${jenisTenaga.subtotal.kontrak_daerah}</td>
                        <td>${jenisTenaga.subtotal.kontrak_blud}</td>
                        <td>${jenisTenaga.subtotal.thl}</td>
                        <td>${jenisTenaga.subtotal.lk}</td>
                        <td>${jenisTenaga.subtotal.pr}</td>
                    </tr>
                `;
            });
            
            // Total row
            html += `
                <tr class="total-row">
                    <td colspan="2">TOTAL</td>
                    <td>${grandTotal.pns}</td>
                    <td>${grandTotal.pppk}</td>
                    <td>${grandTotal.part_time}</td>
                    <td>${grandTotal.kontrak_daerah}</td>
                    <td>${grandTotal.kontrak_blud}</td>
                    <td>${grandTotal.thl}</td>
                    <td>${grandTotal.lk}</td>
                    <td>${grandTotal.pr}</td>
                </tr>
            `;
            
            // Grand total
            html += `
                <tr class="grand-total-row">
                    <td colspan="2">GRAND TOTAL</td>
                    <td colspan="6">${grandTotal.total_status}</td>
                    <td colspan="2">${grandTotal.total_gender}</td>
                </tr>
            `;
            
            $('#table-body-pjt').html(html);
        }

        function getBulanNama(bulan) {
            const bulanNama = {
                '01': 'JANUARI', '02': 'FEBRUARI', '03': 'MARET',
                '04': 'APRIL', '05': 'MEI', '06': 'JUNI',
                '07': 'JULI', '08': 'AGUSTUS', '09': 'SEPTEMBER',
                '10': 'OKTOBER', '11': 'NOVEMBER', '12': 'DESEMBER'
            };
            return bulanNama[bulan] || '';
        }

        function printPerJenisTenaga() {
            const bulan = $('#bulan-pjt').val();
            const tahun = $('#tahun-pjt').val();
            
            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }
            
            // Buka halaman print dalam window/tab baru
            const printUrl = `{{ route('admin.laporan.per-jenis-tenaga.print') }}?bulan=${bulan}&tahun=${tahun}`;
            window.open(printUrl, '_blank');
        }
    </script>
@endpush
