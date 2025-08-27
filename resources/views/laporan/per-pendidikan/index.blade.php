@extends('layouts.backend', ['title' => 'Laporan Jumlah Pegawai Per-Pendidikan'])

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
    }
    
    .data-table .subtotal-row {
        background-color: #f1f3f4;
        font-weight: bold;
    }
    
    .data-table .total-row {
        background-color: #d4edda;
        font-weight: bold;
        color: #155724;
    }
    
    .pendidikan-text {
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
                    Laporan Jumlah Pegawai Per-Pendidikan
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
                    <li class="breadcrumb-item text-muted">Jumlah Pegawai Per-Pendidikan</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-light-primary btn-sm" onclick="printPerPendidikan()">
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
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Pilih Bulan:</label>
                                    <select class="form-select form-select-solid" id="bulan-perpendidikan" data-control="select2" data-placeholder="-- PILIH BULAN --">
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
                                    <label class="form-label fw-semibold">Pilih Tahun:</label>
                                    <select class="form-select form-select-solid" id="tahun-perpendidikan" data-control="select2" data-placeholder="-- PILIH TAHUN --">
                                        <option value="">-- PILIH TAHUN --</option>
                                        @for($i = 2019; $i <= date('Y'); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">&nbsp;</label>
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="lihatPerPendidikan()">
                                            <i class="ki-outline ki-eye fs-3"></i>
                                            Lihat Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Header RSUD -->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                        <img src="{{ asset('assets/media/images/Langsa.png') }}" alt="Logo RSUD Langsa" style="width: 80px; height: 90px; margin-right: 20px;">
                                        <div>
                                            <h4 class="fw-bold text-gray-800 mb-1">PEMERINTAH KOTA LANGSA</h4>
                                            <h3 class="fw-bold text-gray-800 mb-1">RUMAH SAKIT UMUM DAERAH LANGSA</h3>
                                            <p class="text-muted mb-0" style="font-size: 10pt;">
                                                Alamat : Jln. Jend. A. Yani No.1 Kota Langsa Provinsi Pemerintah Aceh<br>
                                                Telp. (0641) 22051 22800 (IGD) Fax. (0641) 22051<br>
                                                E-mail : rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id<br>
                                                Website : www.rsud.langsakota.go.id
                                            </p>
                                            <h5 class="fw-bold text-gray-800 mt-2">KOTA LANGSA</h5>
                                        </div>
                                    </div>
                                    <hr style="border: 2px solid #333; margin: 10px 0;">
                                    <hr style="border: 1px solid #333; margin: 5px 0 20px 0;">
                                    <h4 class="fw-bold text-gray-800" id="header-perpendidikan">JUMLAH PEGAWAI PER-PENDIDIKAN</h4>
                                    <p class="text-muted" id="periode-perpendidikan">Silakan pilih bulan dan tahun untuk melihat data</p>
                                </div>
                            </div>
                        </div>

                        <!-- Loading -->
                        <div class="loader-container" id="loader-perpendidikan">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Sedang memuat data...</p>
                        </div>

                        <!-- Data Container -->
                        <div id="data-container-perpendidikan" style="display: none;">
                            <table class="data-table" id="table-perpendidikan">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No.</th>
                                        <th rowspan="2">JENJANG DAN JURUSAN PENDIDIKAN</th>
                                        <th colspan="5">STATUS</th>
                                        <th colspan="2">JENIS KELAMIN</th>
                                    </tr>
                                    <tr>
                                        <th>PNS</th>
                                        <th>PPPK</th>
                                        <th>PART TIME</th>
                                        <th>HONOR</th>
                                        <th>KONTRAK</th>
                                        <th>LAKI-LAKI</th>
                                        <th>PEREMPUAN</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body-perpendidikan">
                                    <!-- Data akan diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- No Data -->
                        <div id="no-data-perpendidikan" class="no-data" style="display: none;">
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
            $('#bulan-perpendidikan').val('{{ sprintf("%02d", date("m")) }}');
            $('#tahun-perpendidikan').val('{{ date("Y") }}');
        });

        function lihatPerPendidikan() {
            const bulan = $('#bulan-perpendidikan').val();
            const tahun = $('#tahun-perpendidikan').val();
            
            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }
            
            // Show loading
            $('#loader-perpendidikan').show();
            $('#data-container-perpendidikan').hide();
            $('#no-data-perpendidikan').hide();
            
            // Update header periode
            const bulanNama = getBulanNama(bulan);
            $('#periode-perpendidikan').text(`PERIODE ${bulanNama} ${tahun}`);
            
            // AJAX request
            $.ajax({
                url: '{{ route("admin.laporan.per-pendidikan.index") }}',
                type: 'GET',
                data: {
                    bulan: bulan,
                    tahun: tahun
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#loader-perpendidikan').hide();
                    
                    if (response.data && response.data.length > 0) {
                        buildTable(response.data, response.grand_total);
                        $('#data-container-perpendidikan').show();
                    } else {
                        $('#no-data-perpendidikan').show();
                    }
                },
                error: function() {
                    $('#loader-perpendidikan').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memuat data. Silakan coba lagi!'
                    });
                }
            });
        }

        function buildTable(data, grandTotal) {
            let html = '';
            let overallNo = 1;
            
            data.forEach(function(group) {
                // Header jenis tenaga
                html += `
                    <tr class="group-header">
                        <td colspan="9">TENAGA ${group.jenis_tenaga}</td>
                    </tr>
                `;
                
                let no = 1;
                group.details.forEach(function(item) {
                    html += `
                        <tr>
                            <td>${no++}</td>
                            <td class="pendidikan-text">${item.jenjang_didik} ${item.jurusan}</td>
                            <td>${item.pns}</td>
                            <td>${item.pppk}</td>
                            <td>${item.part_time}</td>
                            <td>${item.honor}</td>
                            <td>${item.kontrak}</td>
                            <td>${item.lk}</td>
                            <td>${item.pr}</td>
                        </tr>
                    `;
                });
                
                // Subtotal per jenis tenaga
                html += `
                    <tr class="subtotal-row">
                        <td colspan="2">JUMLAH</td>
                        <td>${group.subtotal.pns}</td>
                        <td>${group.subtotal.pppk}</td>
                        <td>${group.subtotal.part_time}</td>
                        <td>${group.subtotal.honor}</td>
                        <td>${group.subtotal.kontrak}</td>
                        <td>${group.subtotal.lk}</td>
                        <td>${group.subtotal.pr}</td>
                    </tr>
                `;
            });
            
            // Grand total
            html += `
                <tr class="total-row">
                    <td colspan="2">TOTAL</td>
                    <td>${grandTotal.pns}</td>
                    <td>${grandTotal.pppk}</td>
                    <td>${grandTotal.part_time}</td>
                    <td>${grandTotal.honor}</td>
                    <td>${grandTotal.kontrak}</td>
                    <td>${grandTotal.lk}</td>
                    <td>${grandTotal.pr}</td>
                </tr>
            `;
            
            $('#table-body-perpendidikan').html(html);
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

        function printPerPendidikan() {
            const bulan = $('#bulan-perpendidikan').val();
            const tahun = $('#tahun-perpendidikan').val();
            
            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih bulan dan tahun terlebih dahulu!'
                });
                return;
            }
            
            // Buka halaman print dalam window/tab baru
            const printUrl = `{{ route('admin.laporan.per-pendidikan.print') }}?bulan=${bulan}&tahun=${tahun}`;
            window.open(printUrl, '_blank');
        }
    </script>
@endpush
