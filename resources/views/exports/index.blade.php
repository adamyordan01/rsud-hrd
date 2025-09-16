@extends('layouts.backend', ['title' => 'Export Data Pegawai'])

@push('styles')
<style>
    .export-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        height: 100%;
        border: 1px solid #e4e6ea;
        border-radius: 8px;
        background: #fff;
    }
    
    .export-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border-color: #009ef7;
    }
    
    .export-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
        min-height: 120px;
        text-align: center;
    }
    
    .export-card .icon {
        font-size: 2.5rem;
        color: #009ef7;
        margin-bottom: 10px;
    }
    
    .export-card .title {
        font-size: 14px;
        font-weight: 600;
        color: #181c32;
        margin-bottom: 5px;
        line-height: 1.3;
    }
    
    .export-card .count {
        font-size: 18px;
        font-weight: bold;
        color: #009ef7;
        margin-bottom: 8px;
    }
    
    .export-card .description {
        font-size: 11px;
        color: #7e8299;
        margin: 0;
    }
    
    .section-title {
        color: #181c32;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #009ef7;
        display: inline-block;
    }
    
    .section-separator {
        margin: 30px 0;
        border: none;
        height: 1px;
        background: linear-gradient(to right, transparent, #e4e6ea, transparent);
    }
    
    .export-section {
        margin-bottom: 40px;
    }
    
    /* Loading animation */
    .loading-card {
        opacity: 0.7;
        pointer-events: none;
    }
    
    .loading-card .card-body::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #009ef7;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .export-card .card-body {
            min-height: 100px;
            padding: 15px;
        }
        
        .export-card .icon {
            font-size: 2rem;
        }
        
        .export-card .title {
            font-size: 13px;
        }
        
        .export-card .count {
            font-size: 16px;
        }
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Export Data Pegawai
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Export Data Pegawai</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        <!-- Total Pegawai Aktif -->
        <div class="export-section">
            <div class="section-title">
                <i class="ki-outline ki-users fs-3 me-2"></i>
                Total Pegawai Aktif
            </div>
            <div class="row g-4">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                    <div class="export-card" onclick="exportDataPegawaiAktifMaatwebsite(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-users"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['total_aktif']) }}</div>
                            <div class="title">Total Pegawai Aktif</div>
                            <div class="description">Export lengkap dengan 71 kolom data</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="section-separator">

        <!-- Export Berdasarkan Status Kerja -->
        <div class="export-section">
            <div class="section-title">
                <i class="ki-outline ki-profile-user fs-3 me-2"></i>
                Berdasarkan Status Kerja
            </div>
            <div class="row g-4">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                    <div class="export-card" onclick="exportDataDuk(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-medal-star"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['pns']) }}</div>
                            <div class="title">DUK (PNS)</div>
                            <div class="description">Daftar Urut Kepangkatan</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                    <div class="export-card" onclick="exportDataPegawaiHonor(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-handcart"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['honor']) }}</div>
                            <div class="title">Pegawai Honor</div>
                            <div class="description">Pegawai dengan status honor</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiKontrakBlud(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-document"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['kontrak_blud']) }}</div>
                            <div class="title">Kontrak BLUD</div>
                            <div class="description">Pegawai kontrak BLUD</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiKontrakPemko(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-document"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['kontrak_pemko']) }}</div>
                            <div class="title">Kontrak Pemko</div>
                            <div class="description">Pegawai kontrak Pemko</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiPartTime(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-time"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['part_time']) }}</div>
                            <div class="title">Part Time</div>
                            <div class="description">Pegawai part time</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiPppk(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-crown"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['pppk']) }}</div>
                            <div class="title">PPPK</div>
                            <div class="description">Pegawai Pemerintah dengan Perjanjian Kerja</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiThl(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-calendar"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['thl']) }}</div>
                            <div class="title">THL</div>
                            <div class="description">Tenaga Harian Lepas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="section-separator">

        <!-- Export Berdasarkan Jenis Tenaga -->
        <div class="export-section">
            <div class="section-title">
                <i class="ki-outline ki-user-tick fs-3 me-2"></i>
                Berdasarkan Jenis Tenaga
            </div>
            <div class="row g-4">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiMedis(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-heart"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['tenaga_medis']) }}</div>
                            <div class="title">Tenaga Medis</div>
                            <div class="description">Dokter dan tenaga medis</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiPerawatBidan(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-pulse"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['perawat_bidan']) }}</div>
                            <div class="title">Perawat - Bidan</div>
                            <div class="description">Perawat dan bidan</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiPenunjangMedis(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-flask"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['penunjang_medis']) }}</div>
                            <div class="title">Penunjang Medis</div>
                            <div class="description">Tenaga penunjang medis</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiNonKesehatan(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-office-bag"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['non_kesehatan']) }}</div>
                            <div class="title">Non Kesehatan</div>
                            <div class="description">Tenaga non kesehatan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="section-separator">

        <!-- Export Berdasarkan Status Pegawai -->
        <div class="export-section">
            <div class="section-title">
                <i class="ki-outline ki-exit-right fs-3 me-2"></i>
                Berdasarkan Status Pegawai
            </div>
            <div class="row g-4">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiKeluar(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-exit-right"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['pegawai_keluar']) }}</div>
                            <div class="title">Pegawai Keluar</div>
                            <div class="description">Pegawai yang sudah keluar</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiPensiun(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-home"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['pegawai_pensiun']) }}</div>
                            <div class="title">Pegawai Pensiun</div>
                            <div class="description">Pegawai yang sudah pensiun</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataPegawaiTubel(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-abstract-26"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['pegawai_tubel']) }}</div>
                            <div class="title">Pegawai Tubel</div>
                            <div class="description">Pegawai tugas belajar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="section-separator">

        <!-- Export untuk Keperluan Bank -->
        <div class="export-section">
            <div class="section-title">
                <i class="ki-outline ki-bank fs-3 me-2"></i>
                Keperluan Bank
            </div>
            <div class="row g-4">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataBniSyariahKontrak(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-bank"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['bni_syariah_kontrak']) }}</div>
                            <div class="title">BNI Syariah Kontrak</div>
                            <div class="description">Pegawai kontrak untuk bank</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="export-card" onclick="exportDataBniSyariahPns(this)">
                        <div class="card-body">
                            <div class="icon">
                                <i class="ki-outline ki-bank"></i>
                            </div>
                            <div class="count">{{ number_format($exportData['bni_syariah_pns']) }}</div>
                            <div class="title">BNI Syariah PNS</div>
                            <div class="description">PNS, Honor & PPPK untuk bank</div>
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
/**
 * Generate year options for select dropdown
 * @returns {string} HTML options for years
 */
function generateYearOptions() {
    const currentYear = new Date().getFullYear();
    let options = '';
    
    // Generate from 2023 to current year + 1
    for (let year = 2023; year <= currentYear + 1; year++) {
        const selected = year === currentYear ? ' selected' : '';
        options += `<option value="${year}"${selected}>${year}</option>`;
    }
    
    return options;
}

function exportData(type, element) {
    // Show loading state
    const exportCard = element.closest('.export-card');
    exportCard.classList.add('loading-card');
    
    // Determine export URL based on type
    let exportUrl = '';
    
    switch(type) {
        case 'duk':
            exportUrl = '{{ route("admin.export.duk") }}';
            break;
        case 'honor':
            exportUrl = '{{ route("admin.export.honor") }}';
            break;
        case 'kontrak-blud':
            exportUrl = '{{ route("admin.export.kontrak-blud") }}';
            break;
        case 'kontrak-pemko':
            exportUrl = '{{ route("admin.export.kontrak-pemko") }}';
            break;
        case 'part-time':
            exportUrl = '{{ route("admin.export.part-time") }}';
            break;
        case 'pppk':
            exportUrl = '{{ route("admin.export.pppk") }}';
            break;
        case 'thl':
            exportUrl = '{{ route("admin.export.thl") }}';
            break;
        case 'tenaga-medis':
            exportUrl = '{{ route("admin.export.tenaga-medis") }}';
            break;
        case 'perawat-bidan':
            exportUrl = '{{ route("admin.export.perawat-bidan") }}';
            break;
        case 'penunjang-medis':
            exportUrl = '{{ route("admin.export.penunjang-medis") }}';
            break;
        case 'non-kesehatan':
            exportUrl = '{{ route("admin.export.non-kesehatan") }}';
            break;
        case 'pegawai-keluar':
            exportUrl = '{{ route("admin.export.pegawai-keluar") }}';
            break;
        default:
            console.error('Unknown export type:', type);
            exportCard.classList.remove('loading-card');
            return;
    }
    
    // Show initial notification
    Swal.fire({
        title: 'Memproses Export...',
        text: 'Mohon tunggu, data sedang disiapkan untuk download.',
        icon: 'info',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Use fetch API untuk better error handling
    fetch(exportUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is actually Excel file
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('spreadsheet')) {
            throw new Error('Response bukan file Excel yang valid');
        }
        
        return response.blob();
    })
    .then(blob => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        
        // Generate filename
        const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '').replace('T', '_');
        link.download = `Export_${type}_${timestamp}.xlsx`;
        
        // Trigger download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up
        window.URL.revokeObjectURL(url);
        
        // Remove loading state
        exportCard.classList.remove('loading-card');
        
        // Show success notification
        Swal.fire({
            title: 'Berhasil!',
            text: 'File Excel berhasil didownload.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(error => {
        console.error('Export error:', error);
        
        // Remove loading state
        exportCard.classList.remove('loading-card');
        
        // Show error notification
        Swal.fire({
            title: 'Error!',
            text: 'Gagal mengexport data: ' + error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

/**
 * Export Pegawai Aktif dengan maatwebsite/excel
 * Export lengkap dengan 71 kolom data seperti sistem HRD original
 */
function exportDataPegawaiAktifMaatwebsite(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai Aktif',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun" class="form-control" style="flex: 1;">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            const exportCard = element.closest('.export-card');
            exportCard.classList.add('loading-card');
            
            // Show loading notification
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data pegawai aktif periode ${bulan}-${tahun}. Mohon tunggu...`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Perform the export
            const exportUrl = `{{ route("admin.export.pegawai-aktif-maatwebsite") }}?bulan=${bulan}&tahun=${tahun}`;
            
            // Use fetch API untuk better error handling
            fetch(exportUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is actually Excel file
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('spreadsheet')) {
                    throw new Error('Response bukan file Excel yang valid');
                }
                
                return response.blob();
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                
                // Generate filename
                const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '').replace('T', '_');
                link.download = `Pegawai_Aktif_Periode_${bulan}-${tahun}_${timestamp}.xlsx`;
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                window.URL.revokeObjectURL(url);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show success notification
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'File Excel berhasil didownload.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show error notification
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengexport data: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

/**
 * Export Pegawai Honor dengan maatwebsite/excel
 * Export untuk Pegawai dengan status honor sesuai sistem HRD original
 */
function exportDataPegawaiHonor(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai Honor',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_honor" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_honor" class="form-control" style="flex: 1;">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_honor').value;
            const tahun = document.getElementById('tahun_honor').value;
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            const exportCard = element.closest('.export-card');
            exportCard.classList.add('loading-card');
            
            // Show loading notification
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Pegawai Honor periode ${bulan}-${tahun}. Mohon tunggu...`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Perform the export
            const exportUrl = `{{ route("admin.export.honor") }}?bulan=${bulan}&tahun=${tahun}`;
            
            // Use fetch API untuk better error handling
            fetch(exportUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is actually Excel file
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('spreadsheet')) {
                    throw new Error('Response bukan file Excel yang valid');
                }
                
                return response.blob();
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                
                // Generate filename
                const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '').replace('T', '_');
                link.download = `Pegawai_Honor_Periode_${bulan}-${tahun}_${timestamp}.xlsx`;
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                window.URL.revokeObjectURL(url);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show success notification
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'File Excel Pegawai Honor berhasil didownload.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show error notification
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengexport data Pegawai Honor: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

/**
 * Export Pegawai Kontrak BLUD dengan maatwebsite/excel
 * Export untuk Pegawai dengan status kontrak BLUD sesuai sistem HRD original
 */
function exportDataPegawaiKontrakBlud(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai Kontrak BLUD',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_kontrak_blud" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_kontrak_blud" class="form-control" style="flex: 1;">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_kontrak_blud').value;
            const tahun = document.getElementById('tahun_kontrak_blud').value;
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            const exportCard = element.closest('.export-card');
            exportCard.classList.add('loading-card');
            
            // Show loading notification
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Pegawai Kontrak BLUD periode ${bulan}-${tahun}. Mohon tunggu...`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Perform the export
            const exportUrl = `{{ route("admin.export.kontrak-blud") }}?bulan=${bulan}&tahun=${tahun}`;
            
            // Use fetch API untuk better error handling
            fetch(exportUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is actually Excel file
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('spreadsheet')) {
                    throw new Error('Response bukan file Excel yang valid');
                }
                
                return response.blob();
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                
                // Generate filename
                const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '').replace('T', '_');
                link.download = `Pegawai_Kontrak_BLUD_Periode_${bulan}-${tahun}_${timestamp}.xlsx`;
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                window.URL.revokeObjectURL(url);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show success notification
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'File Excel Pegawai Kontrak BLUD berhasil didownload.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show error notification
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengexport data Pegawai Kontrak BLUD: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

/**
 * Export Pegawai Kontrak Pemko dengan maatwebsite/excel
 * Export untuk Pegawai dengan status kontrak Pemko sesuai sistem HRD original
 */
function exportDataPegawaiKontrakPemko(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai Kontrak Pemko',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_kontrak_pemko" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_kontrak_pemko" class="form-control" style="flex: 1;">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_kontrak_pemko').value;
            const tahun = document.getElementById('tahun_kontrak_pemko').value;
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            const exportCard = element.closest('.export-card');
            exportCard.classList.add('loading-card');
            
            // Show loading notification
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Pegawai Kontrak Pemko periode ${bulan}-${tahun}. Mohon tunggu...`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Perform the export
            const exportUrl = `{{ route("admin.export.kontrak-pemko") }}?bulan=${bulan}&tahun=${tahun}`;
            
            // Use fetch API untuk better error handling
            fetch(exportUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is actually Excel file
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('spreadsheet')) {
                    throw new Error('Response bukan file Excel yang valid');
                }
                
                return response.blob();
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                
                // Generate filename
                const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '').replace('T', '_');
                link.download = `Pegawai_Kontrak_Pemko_Periode_${bulan}-${tahun}_${timestamp}.xlsx`;
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                window.URL.revokeObjectURL(url);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show success notification
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'File Excel Pegawai Kontrak Pemko berhasil didownload.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show error notification
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengexport data Pegawai Kontrak Pemko: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

/**
 * Export Pegawai Part Time dengan maatwebsite/excel
 * Export untuk Pegawai dengan status part time sesuai sistem HRD original
 */
function exportDataPegawaiPartTime(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai Part Time',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_part_time" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_part_time" class="form-control" style="flex: 1;">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_part_time').value;
            const tahun = document.getElementById('tahun_part_time').value;
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            const exportCard = element.closest('.export-card');
            exportCard.classList.add('loading-card');
            
            // Show loading notification
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Pegawai Part Time periode ${bulan}-${tahun}. Mohon tunggu...`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Perform the export
            const exportUrl = `{{ route("admin.export.part-time") }}?bulan=${bulan}&tahun=${tahun}`;
            
            // Use fetch API untuk better error handling
            fetch(exportUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is actually Excel file
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('spreadsheet')) {
                    throw new Error('Response bukan file Excel yang valid');
                }
                
                return response.blob();
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                
                // Generate filename
                const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '').replace('T', '_');
                link.download = `Pegawai_Part_Time_Periode_${bulan}-${tahun}_${timestamp}.xlsx`;
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                window.URL.revokeObjectURL(url);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show success notification
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'File Excel Pegawai Part Time berhasil didownload.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show error notification
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengexport data Pegawai Part Time: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

/**
 * Export Pegawai PPPK dengan maatwebsite/excel
 * Export untuk Pegawai Pemerintah dengan Perjanjian Kerja sesuai sistem HRD original
 */
function exportDataPegawaiPppk(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai PPPK',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_pppk" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_pppk" class="form-control" style="flex: 1;">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_pppk').value;
            const tahun = document.getElementById('tahun_pppk').value;
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            const exportCard = element.closest('.export-card');
            exportCard.classList.add('loading-card');
            
            // Show loading notification
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Pegawai PPPK periode ${bulan}-${tahun}. Mohon tunggu...`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Perform the export
            const exportUrl = `{{ route("admin.export.pppk") }}?bulan=${bulan}&tahun=${tahun}`;
            
            // Use fetch API untuk better error handling
            fetch(exportUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is actually Excel file
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('spreadsheet')) {
                    throw new Error('Response bukan file Excel yang valid');
                }
                
                return response.blob();
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                
                // Generate filename
                const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '').replace('T', '_');
                link.download = `Pegawai_PPPK_Periode_${bulan}-${tahun}_${timestamp}.xlsx`;
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                window.URL.revokeObjectURL(url);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show success notification
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'File Excel Pegawai PPPK berhasil didownload.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show error notification
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengexport data Pegawai PPPK: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

/**
 * Export DUK (Daftar Urut Kepangkatan) dengan maatwebsite/excel
 * Export untuk PNS dengan format DUK sesuai sistem HRD original
 */
function exportDataDuk(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export DUK (Daftar Urut Kepangkatan)',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_duk" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_duk" class="form-control" style="flex: 1;">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_duk').value;
            const tahun = document.getElementById('tahun_duk').value;
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            const exportCard = element.closest('.export-card');
            exportCard.classList.add('loading-card');
            
            // Show loading notification
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data DUK periode ${bulan}-${tahun}. Mohon tunggu...`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Perform the export
            const exportUrl = `{{ route("admin.export.duk") }}?bulan=${bulan}&tahun=${tahun}`;
            
            // Use fetch API untuk better error handling
            fetch(exportUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is actually Excel file
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('spreadsheet')) {
                    throw new Error('Response bukan file Excel yang valid');
                }
                
                return response.blob();
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                
                // Generate filename
                const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '').replace('T', '_');
                link.download = `DUK_Periode_${bulan}-${tahun}_${timestamp}.xlsx`;
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                window.URL.revokeObjectURL(url);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show success notification
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'File Excel DUK berhasil didownload.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Export error:', error);
                
                // Remove loading state
                exportCard.classList.remove('loading-card');
                
                // Show error notification
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengexport data DUK: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

/**
 * Export Pegawai THL dengan maatwebsite/excel
 */
function exportDataPegawaiThl(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai THL',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_thl" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_thl" class="form-control" style="flex: 1;">
                            ${Array.from({length: 5}, (_, i) => {
                                const year = new Date().getFullYear() - 2 + i;
                                const selected = year === new Date().getFullYear() ? 'selected' : '';
                                return `<option value="${year}" ${selected}>${year}</option>`;
                            }).join('')}
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_thl').value;
            const tahun = document.getElementById('tahun_thl').value;
            
            if (!bulan || !tahun) {
                Swal.showValidationMessage('Periode harus dipilih');
                return false;
            }
            
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
            
            // Show loading alert
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Pegawai THL periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create download link
            const downloadUrl = `/admin/export/thl?bulan=${bulan}&tahun=${tahun}`;
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Handle download completion
            let downloadCompleted = false;
            
            // Set timeout untuk close loading setelah beberapa detik
            setTimeout(() => {
                if (!downloadCompleted) {
                    Swal.close();
                    
                    // Reset element state
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    // Show success message
                    Swal.fire({
                        title: 'Export Berhasil!',
                        text: 'File Excel Pegawai THL berhasil didownload.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    
                    downloadCompleted = true;
                }
            }, 3000);
            
            // Trigger download
            link.click();
            document.body.removeChild(link);
            
            // Handle error jika terjadi masalah
            setTimeout(() => {
                if (!downloadCompleted) {
                    // Reset element state on error
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    Swal.fire({
                        title: 'Export Gagal!',
                        text: 'Gagal mengexport data Pegawai THL: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }, 10000);
        }
    });
}

/**
 * Export Pegawai Medis (RS Online) dengan maatwebsite/excel
 */
function exportDataPegawaiMedis(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai Medis (RS Online)',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_medis" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_medis" class="form-control" style="flex: 1;">
                            ${Array.from({length: 5}, (_, i) => {
                                const year = new Date().getFullYear() - 2 + i;
                                const selected = year === new Date().getFullYear() ? 'selected' : '';
                                return `<option value="${year}" ${selected}>${year}</option>`;
                            }).join('')}
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_medis').value;
            const tahun = document.getElementById('tahun_medis').value;
            
            if (!bulan || !tahun) {
                Swal.showValidationMessage('Periode harus dipilih');
                return false;
            }
            
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
            
            // Show loading alert
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Pegawai Medis periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create download link
            const downloadUrl = `/admin/export/tenaga-medis?bulan=${bulan}&tahun=${tahun}`;
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Handle download completion
            let downloadCompleted = false;
            
            // Set timeout untuk close loading setelah beberapa detik
            setTimeout(() => {
                if (!downloadCompleted) {
                    Swal.close();
                    
                    // Reset element state
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    // Show success message
                    Swal.fire({
                        title: 'Export Berhasil!',
                        text: 'File Excel Pegawai Medis (RS Online) berhasil didownload.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    
                    downloadCompleted = true;
                }
            }, 3000);
            
            // Trigger download
            link.click();
            document.body.removeChild(link);
            
            // Handle error jika terjadi masalah
            setTimeout(() => {
                if (!downloadCompleted) {
                    // Reset element state on error
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    Swal.fire({
                        title: 'Export Gagal!',
                        text: 'Gagal mengexport data Pegawai Medis: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }, 10000);
        }
    });
}

/**
 * Export Perawat-Bidan (RS Online) dengan maatwebsite/excel
 */
function exportDataPegawaiPerawatBidan(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Perawat-Bidan (RS Online)',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_perawat_bidan" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_perawat_bidan" class="form-control" style="flex: 1;">
                            ${Array.from({length: 5}, (_, i) => {
                                const year = new Date().getFullYear() - 2 + i;
                                const selected = year === new Date().getFullYear() ? 'selected' : '';
                                return `<option value="${year}" ${selected}>${year}</option>`;
                            }).join('')}
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_perawat_bidan').value;
            const tahun = document.getElementById('tahun_perawat_bidan').value;
            
            if (!bulan || !tahun) {
                Swal.showValidationMessage('Periode harus dipilih');
                return false;
            }
            
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
            
            // Show loading alert
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Perawat-Bidan periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create download link
            const downloadUrl = `/admin/export/perawat-bidan?bulan=${bulan}&tahun=${tahun}`;
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Handle download completion
            let downloadCompleted = false;
            
            // Set timeout untuk close loading setelah beberapa detik
            setTimeout(() => {
                if (!downloadCompleted) {
                    Swal.close();
                    
                    // Reset element state
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    // Show success message
                    Swal.fire({
                        title: 'Export Berhasil!',
                        text: 'File Excel Perawat-Bidan (RS Online) berhasil didownload.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    
                    downloadCompleted = true;
                }
            }, 3000);
            
            // Trigger download
            link.click();
            document.body.removeChild(link);
            
            // Handle error jika terjadi masalah
            setTimeout(() => {
                if (!downloadCompleted) {
                    // Reset element state on error
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    Swal.fire({
                        title: 'Export Gagal!',
                        text: 'Gagal mengexport data Perawat-Bidan: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }, 10000);
        }
    });
}

/**
 * Export Penunjang Medis (RS Online) dengan maatwebsite/excel
 */
function exportDataPegawaiPenunjangMedis(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Penunjang Medis (RS Online)',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_penunjang_medis" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_penunjang_medis" class="form-control" style="flex: 1;">
                            ${Array.from({length: 5}, (_, i) => {
                                const year = new Date().getFullYear() - 2 + i;
                                const selected = year === new Date().getFullYear() ? 'selected' : '';
                                return `<option value="${year}" ${selected}>${year}</option>`;
                            }).join('')}
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_penunjang_medis').value;
            const tahun = document.getElementById('tahun_penunjang_medis').value;
            
            if (!bulan || !tahun) {
                Swal.showValidationMessage('Periode harus dipilih');
                return false;
            }
            
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
            
            // Show loading alert
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Penunjang Medis periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create download link
            const downloadUrl = `/admin/export/penunjang-medis?bulan=${bulan}&tahun=${tahun}`;
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Handle download completion
            let downloadCompleted = false;
            
            // Set timeout untuk close loading setelah beberapa detik
            setTimeout(() => {
                if (!downloadCompleted) {
                    Swal.close();
                    
                    // Reset element state
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    // Show success message
                    Swal.fire({
                        title: 'Export Berhasil!',
                        text: 'File Excel Penunjang Medis (RS Online) berhasil didownload.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    
                    downloadCompleted = true;
                }
            }, 3000);
            
            // Trigger download
            link.click();
            document.body.removeChild(link);
            
            // Handle error jika terjadi masalah
            setTimeout(() => {
                if (!downloadCompleted) {
                    // Reset element state on error
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    Swal.fire({
                        title: 'Export Gagal!',
                        text: 'Gagal mengexport data Penunjang Medis.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }, 10000);
        }
    });
}

/**
 * Export Non Kesehatan (RS Online) dengan maatwebsite/excel
 * @param {HTMLElement} element - Card element yang diklik
 */
function exportDataPegawaiNonKesehatan(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Non Kesehatan (RS Online)',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_non_kesehatan" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_non_kesehatan" class="form-control" style="flex: 1;">
                            ${Array.from({length: 5}, (_, i) => {
                                const year = new Date().getFullYear() - 2 + i;
                                const selected = year === new Date().getFullYear() ? 'selected' : '';
                                return `<option value="${year}" ${selected}>${year}</option>`;
                            }).join('')}
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_non_kesehatan').value;
            const tahun = document.getElementById('tahun_non_kesehatan').value;
            
            if (!bulan || !tahun) {
                Swal.showValidationMessage('Periode harus dipilih');
                return false;
            }
            
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
            
            // Show loading alert
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Non Kesehatan periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create download link
            const downloadUrl = `/admin/export/non-kesehatan?bulan=${bulan}&tahun=${tahun}`;
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Handle download completion
            let downloadCompleted = false;
            
            // Set timeout untuk close loading setelah beberapa detik
            setTimeout(() => {
                if (!downloadCompleted) {
                    Swal.close();
                    
                    // Reset element state
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    // Show success message
                    Swal.fire({
                        title: 'Export Berhasil!',
                        text: 'File Excel Non Kesehatan (RS Online) berhasil didownload.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    
                    downloadCompleted = true;
                }
            }, 3000);
            
            // Trigger download
            link.click();
            document.body.removeChild(link);
            
            // Handle error jika terjadi masalah
            setTimeout(() => {
                if (!downloadCompleted) {
                    // Reset element state on error
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    Swal.fire({
                        title: 'Export Gagal!',
                        text: 'Gagal mengexport data Non Kesehatan.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }, 10000);
        }
    });
}

/**
 * Export Pegawai Keluar dengan maatwebsite/excel
 * @param {HTMLElement} element - Card element yang diklik
 */
function exportDataPegawaiKeluar(element) {
    // Show confirmation with periode selection
    Swal.fire({
        title: 'Export Pegawai Keluar',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                
                <div style="margin-top: 20px;">
                    <label style="font-weight: 600;">Pilih Periode:</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select id="bulan_pegawai_keluar" class="form-control" style="flex: 1;">
                            <option value="01" ${new Date().getMonth() === 0 ? 'selected' : ''}>Januari</option>
                            <option value="02" ${new Date().getMonth() === 1 ? 'selected' : ''}>Februari</option>
                            <option value="03" ${new Date().getMonth() === 2 ? 'selected' : ''}>Maret</option>
                            <option value="04" ${new Date().getMonth() === 3 ? 'selected' : ''}>April</option>
                            <option value="05" ${new Date().getMonth() === 4 ? 'selected' : ''}>Mei</option>
                            <option value="06" ${new Date().getMonth() === 5 ? 'selected' : ''}>Juni</option>
                            <option value="07" ${new Date().getMonth() === 6 ? 'selected' : ''}>Juli</option>
                            <option value="08" ${new Date().getMonth() === 7 ? 'selected' : ''}>Agustus</option>
                            <option value="09" ${new Date().getMonth() === 8 ? 'selected' : ''}>September</option>
                            <option value="10" ${new Date().getMonth() === 9 ? 'selected' : ''}>Oktober</option>
                            <option value="11" ${new Date().getMonth() === 10 ? 'selected' : ''}>November</option>
                            <option value="12" ${new Date().getMonth() === 11 ? 'selected' : ''}>Desember</option>
                        </select>
                        <select id="tahun_pegawai_keluar" class="form-control" style="flex: 1;">
                            ${Array.from({length: 5}, (_, i) => {
                                const year = new Date().getFullYear() - 2 + i;
                                const selected = year === new Date().getFullYear() ? 'selected' : '';
                                return `<option value="${year}" ${selected}>${year}</option>`;
                            }).join('')}
                        </select>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        preConfirm: () => {
            const bulan = document.getElementById('bulan_pegawai_keluar').value;
            const tahun = document.getElementById('tahun_pegawai_keluar').value;
            
            if (!bulan || !tahun) {
                Swal.showValidationMessage('Periode harus dipilih');
                return false;
            }
            
            return { bulan, tahun };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { bulan, tahun } = result.value;
            
            // Show loading state
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
            
            // Show loading alert
            Swal.fire({
                title: 'Memproses Export...',
                text: `Sedang memproses data Pegawai Keluar periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create download link
            const downloadUrl = `/admin/export/pegawai-keluar?bulan=${bulan}&tahun=${tahun}`;
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Handle download completion
            let downloadCompleted = false;
            
            // Set timeout untuk close loading setelah beberapa detik
            setTimeout(() => {
                if (!downloadCompleted) {
                    Swal.close();
                    
                    // Reset element state
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    // Show success message
                    Swal.fire({
                        title: 'Export Berhasil!',
                        text: 'File Excel Pegawai Keluar berhasil didownload.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    
                    downloadCompleted = true;
                }
            }, 3000);
            
            // Trigger download
            link.click();
            document.body.removeChild(link);
            
            // Handle error jika terjadi masalah
            setTimeout(() => {
                if (!downloadCompleted) {
                    // Reset element state on error
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                    
                    Swal.fire({
                        title: 'Export Gagal!',
                        text: 'Gagal mengexport data Pegawai Keluar.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }, 10000);
        }
    });
}

/**
 * Export Pegawai Pensiun dengan maatwebsite/excel
 * @param {Element} element - Element yang diklik
 */
function exportDataPegawaiPensiun(element) {
    Swal.fire({
        title: 'Export Pegawai Pensiun',
        html: `
            <div class="export-info">
                <div class="periode-selector mt-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan:</label>
                            <select id="export-bulan-pensiun" class="form-select">
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
                        <div class="col-md-6">
                            <label class="form-label">Tahun:</label>
                            <select id="export-tahun-pensiun" class="form-select">
                                ${generateYearOptions()}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `,
        // confirmButtonText: '<i class="ki-outline ki-file-down"></i> Export Sekarang',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        didOpen: () => {
            // Set default periode (bulan dan tahun sekarang)
            const now = new Date();
            document.getElementById('export-bulan-pensiun').value = String(now.getMonth() + 1).padStart(2, '0');
            document.getElementById('export-tahun-pensiun').value = now.getFullYear();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const bulan = document.getElementById('export-bulan-pensiun').value;
            const tahun = document.getElementById('export-tahun-pensiun').value;
            
            const bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            // Loading state
            Swal.fire({
                title: 'Sedang memproses...',
                text: `Sedang memproses data Pegawai Pensiun periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Animasi progress untuk UX yang lebih baik
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += Math.random() * 15;
                        if (progress >= 90) {
                            clearInterval(interval);
                        }
                    }, 200);
                }
            });

            // Buat form untuk download
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("admin.export.pegawai-pensiun") }}';
            
            const bulanInput = document.createElement('input');
            bulanInput.type = 'hidden';
            bulanInput.name = 'bulan';
            bulanInput.value = bulan;
            
            const tahunInput = document.createElement('input');
            tahunInput.type = 'hidden';
            tahunInput.name = 'tahun';
            tahunInput.value = tahun;
            
            form.appendChild(bulanInput);
            form.appendChild(tahunInput);
            document.body.appendChild(form);
            
            // Submit form untuk download
            form.submit();
            
            // Cleanup form
            document.body.removeChild(form);
            
            // Success message setelah delay
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Berhasil!',
                    text: 'File Excel Pegawai Pensiun berhasil didownload.',
                    confirmButtonText: 'OK'
                });
            }, 2000);
        }
    }).catch((error) => {
        console.error('Error in exportDataPegawaiPensiun:', error);
        
        if (error !== 'cancel') {
            Swal.fire({
                icon: 'error',
                title: 'Export Gagal',
                text: 'Gagal mengexport data Pegawai Pensiun.',
                confirmButtonText: 'OK'
            });
        }
    });
}

/**
 * Export Pegawai Tubel dengan maatwebsite/excel
 * @param {Element} element - Element yang diklik
 */
function exportDataPegawaiTubel(element) {
    Swal.fire({
        title: 'Export Pegawai Tubel',
        html: `
            <div class="export-info">
                <div class="periode-selector mt-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan:</label>
                            <select id="export-bulan-tubel" class="form-select">
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
                        <div class="col-md-6">
                            <label class="form-label">Tahun:</label>
                            <select id="export-tahun-tubel" class="form-select">
                                ${generateYearOptions()}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `,
        // confirmButtonText: '<i class="ki-outline ki-file-down"></i> Export Sekarang',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        didOpen: () => {
            // Set default periode (bulan dan tahun sekarang)
            const now = new Date();
            document.getElementById('export-bulan-tubel').value = String(now.getMonth() + 1).padStart(2, '0');
            document.getElementById('export-tahun-tubel').value = now.getFullYear();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const bulan = document.getElementById('export-bulan-tubel').value;
            const tahun = document.getElementById('export-tahun-tubel').value;
            
            const bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            // Loading state
            Swal.fire({
                title: 'Sedang memproses...',
                text: `Sedang memproses data Pegawai Tubel periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Animasi progress untuk UX yang lebih baik
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += Math.random() * 15;
                        if (progress >= 90) {
                            clearInterval(interval);
                        }
                    }, 200);
                }
            });

            // Buat form untuk download
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("admin.export.pegawai-tubel") }}';
            
            const bulanInput = document.createElement('input');
            bulanInput.type = 'hidden';
            bulanInput.name = 'bulan';
            bulanInput.value = bulan;
            
            const tahunInput = document.createElement('input');
            tahunInput.type = 'hidden';
            tahunInput.name = 'tahun';
            tahunInput.value = tahun;
            
            form.appendChild(bulanInput);
            form.appendChild(tahunInput);
            document.body.appendChild(form);
            
            // Submit form untuk download
            form.submit();
            
            // Cleanup form
            document.body.removeChild(form);
            
            // Success message setelah delay
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Berhasil!',
                    text: 'File Excel Pegawai Tubel berhasil didownload.',
                    confirmButtonText: 'OK'
                });
            }, 2000);
        }
    }).catch((error) => {
        console.error('Error in exportDataPegawaiTubel:', error);
        
        if (error !== 'cancel') {
            Swal.fire({
                icon: 'error',
                title: 'Export Gagal',
                text: 'Gagal mengexport data Pegawai Tubel.',
                confirmButtonText: 'OK'
            });
        }
    });
}

/**
 * Export BNI Syariah Kontrak dengan maatwebsite/excel
 * @param {Element} element - Element yang diklik
 */
function exportDataBniSyariahKontrak(element) {
    Swal.fire({
        title: 'Export BNI Syariah Kontrak',
        html: `
            <div class="export-info">
                <div class="periode-selector mt-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan:</label>
                            <select id="export-bulan-bni-kontrak" class="form-select">
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
                        <div class="col-md-6">
                            <label class="form-label">Tahun:</label>
                            <select id="export-tahun-bni-kontrak" class="form-select">
                                ${generateYearOptions()}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `,
        // confirmButtonText: '<i class="ki-outline ki-file-down"></i> Export Sekarang',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        didOpen: () => {
            // Set default periode (bulan dan tahun sekarang)
            const now = new Date();
            document.getElementById('export-bulan-bni-kontrak').value = String(now.getMonth() + 1).padStart(2, '0');
            document.getElementById('export-tahun-bni-kontrak').value = now.getFullYear();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const bulan = document.getElementById('export-bulan-bni-kontrak').value;
            const tahun = document.getElementById('export-tahun-bni-kontrak').value;
            
            const bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            // Loading state
            Swal.fire({
                title: 'Sedang memproses...',
                text: `Sedang memproses data BNI Syariah Kontrak periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Animasi progress untuk UX yang lebih baik
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += Math.random() * 15;
                        if (progress >= 90) {
                            clearInterval(interval);
                        }
                    }, 200);
                }
            });

            // Buat form untuk download
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("admin.export.bni-syariah-kontrak") }}';
            
            const bulanInput = document.createElement('input');
            bulanInput.type = 'hidden';
            bulanInput.name = 'bulan';
            bulanInput.value = bulan;
            
            const tahunInput = document.createElement('input');
            tahunInput.type = 'hidden';
            tahunInput.name = 'tahun';
            tahunInput.value = tahun;
            
            form.appendChild(bulanInput);
            form.appendChild(tahunInput);
            document.body.appendChild(form);
            
            // Submit form untuk download
            form.submit();
            
            // Cleanup form
            document.body.removeChild(form);
            
            // Success message setelah delay
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Berhasil!',
                    text: 'File Excel BNI Syariah Kontrak berhasil didownload.',
                    confirmButtonText: 'OK'
                });
            }, 2000);
        }
    }).catch((error) => {
        console.error('Error in exportDataBniSyariahKontrak:', error);
        
        if (error !== 'cancel') {
            Swal.fire({
                icon: 'error',
                title: 'Export Gagal',
                text: 'Gagal mengexport data BNI Syariah Kontrak.',
                confirmButtonText: 'OK'
            });
        }
    });
}

/**
 * Export BNI Syariah PNS dengan maatwebsite/excel
 * @param {Element} element - Element yang diklik
 */
function exportDataBniSyariahPns(element) {
    Swal.fire({
        title: 'Export BNI Syariah PNS',
        html: `
            <div class="export-info">
                <div class="periode-selector mt-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan:</label>
                            <select id="export-bulan-bni-pns" class="form-select">
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
                        <div class="col-md-6">
                            <label class="form-label">Tahun:</label>
                            <select id="export-tahun-bni-pns" class="form-select">
                                ${generateYearOptions()}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `,
        // confirmButtonText: '<i class="ki-outline ki-file-down"></i> Export Sekarang',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Export Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        width: 600,
        didOpen: () => {
            // Set default periode (bulan dan tahun sekarang)
            const now = new Date();
            document.getElementById('export-bulan-bni-pns').value = String(now.getMonth() + 1).padStart(2, '0');
            document.getElementById('export-tahun-bni-pns').value = now.getFullYear();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const bulan = document.getElementById('export-bulan-bni-pns').value;
            const tahun = document.getElementById('export-tahun-bni-pns').value;
            
            const bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            // Loading state
            Swal.fire({
                title: 'Sedang memproses...',
                text: `Sedang memproses data BNI Syariah PNS periode ${bulan}-${tahun}. Mohon tunggu...`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Animasi progress untuk UX yang lebih baik
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += Math.random() * 15;
                        if (progress >= 90) {
                            clearInterval(interval);
                        }
                    }, 200);
                }
            });

            // Buat form untuk download
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("admin.export.bni-syariah-pns") }}';
            
            const bulanInput = document.createElement('input');
            bulanInput.type = 'hidden';
            bulanInput.name = 'bulan';
            bulanInput.value = bulan;
            
            const tahunInput = document.createElement('input');
            tahunInput.type = 'hidden';
            tahunInput.name = 'tahun';
            tahunInput.value = tahun;
            
            form.appendChild(bulanInput);
            form.appendChild(tahunInput);
            document.body.appendChild(form);
            
            // Submit form untuk download
            form.submit();
            
            // Cleanup form
            document.body.removeChild(form);
            
            // Success message setelah delay
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Berhasil!',
                    text: 'File Excel BNI Syariah PNS berhasil didownload.',
                    confirmButtonText: 'OK'
                });
            }, 2000);
        }
    }).catch((error) => {
        console.error('Error in exportDataBniSyariahPns:', error);
        
        if (error !== 'cancel') {
            Swal.fire({
                icon: 'error',
                title: 'Export Gagal',
                text: 'Gagal mengexport data BNI Syariah PNS.',
                confirmButtonText: 'OK'
            });
        }
    });
}

// Add tooltip for better UX
$(document).ready(function() {
    $('.export-card').each(function() {
        $(this).attr('title', 'Klik untuk export data ke Excel');
    });
});
</script>
@endpush
