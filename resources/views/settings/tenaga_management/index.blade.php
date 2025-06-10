@extends('layouts.backend', ['title' => $pageTitle])

@push('styles')
<style>
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #0d6efd;
}

.detail-card {
    border: 1px solid #e4e6ef;
    border-radius: 0.575rem;
    margin-bottom: 20px;
    box-shadow: 0 0.1rem 1rem 0.25rem rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.detail-card:hover {
    box-shadow: 0 0.5rem 2rem 0.5rem rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.detail-header {
    background-color: #f8fafb;
    padding: 20px 25px;
    border-bottom: 1px solid #e4e6ef;
    border-radius: 0.575rem 0.575rem 0 0;
}

.sub-detail-table {
    margin-bottom: 0;
}

.sub-detail-table thead th {
    background-color: #f9f9f9;
    border-bottom: 2px solid #e4e6ef;
    padding: 15px 20px !important;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #5e6278;
}

.sub-detail-table tbody td {
    padding: 18px 20px !important;
    vertical-align: middle;
    border-bottom: 1px solid #f1f1f4;
}

.sub-detail-row:hover {
    background-color: #f8fafb;
}

.action-btn {
    padding: 8px 12px;
    margin: 0 3px;
    border-radius: 0.475rem;
    transition: all 0.3s ease;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.badge-count {
    font-size: 0.75rem;
    padding: 6px 12px;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.jenis-tenaga-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 30px;
    border-radius: 0.575rem 0.575rem 0 0;
    margin-bottom: 0;
    position: relative;
    overflow: hidden;
}

.jenis-tenaga-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.jenis-tenaga-header .d-flex {
    position: relative;
    z-index: 1;
}

.jenis-tenaga-name {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.jenis-tenaga-item {
    box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.08);
    border: 1px solid #e4e6ef;
    border-radius: 0.575rem;
    margin-bottom: 25px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.jenis-tenaga-item:hover {
    box-shadow: 0 1rem 3rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-3px);
}

.empty-state {
    text-align: center;
    padding: 60px 30px;
    color: #a1a5b7;
}

.empty-state i {
    margin-bottom: 20px;
    opacity: 0.7;
}

.empty-state h5,
.empty-state h6 {
    margin-bottom: 15px;
    font-weight: 500;
}

.empty-state p {
    margin-bottom: 25px;
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Card Content Spacing */
.card-body {
    padding: 25px 30px;
}

.jenis-tenaga-item .card-body {
    padding: 0;
}

.jenis-tenaga-item .collapse .card-body {
    padding: 25px 30px 30px 30px;
}

/* Search & Filter Card */
.search-filter-card {
    margin-bottom: 25px;
    border-radius: 0.575rem;
    box-shadow: 0 0.1rem 1rem 0.25rem rgba(0, 0, 0, 0.05);
}

.search-filter-card .card-body {
    padding: 20px 25px;
}

/* Button Spacing */
.btn-group-actions {
    gap: 8px;
}

.btn-group-actions .btn {
    min-width: 40px;
    height: 40px;
    border-radius: 0.475rem;
}

/* Modal Enhancements */
.modal-content {
    border-radius: 0.575rem;
    box-shadow: 0 2rem 5rem rgba(0, 0, 0, 0.2);
}

.modal-header {
    padding: 25px 30px 20px 30px;
    border-bottom: 1px solid #e4e6ef;
}

.modal-body {
    padding: 20px 30px;
}

.modal-footer {
    padding: 20px 30px 25px 30px;
    border-top: 1px solid #e4e6ef;
}

/* Form Spacing */
.form-group {
    margin-bottom: 20px;
}

.form-control {
    padding: 12px 15px;
    border-radius: 0.475rem;
}

/* Badge Enhancements */
.badge {
    font-size: 0.75rem;
    padding: 6px 12px;
    border-radius: 0.375rem;
    font-weight: 500;
}

/* Icon Spacing */
.fs-2x {
    margin-right: 15px;
}

.fs-3 {
    margin-right: 12px;
}

/* Table Responsive */
.table-responsive {
    border-radius: 0 0 0.575rem 0.575rem;
    overflow: hidden;
}

/* Detail Info Spacing */
.detail-info {
    margin-bottom: 8px;
}

.detail-info small {
    font-size: 0.8rem;
    color: #a1a5b7;
    font-weight: 500;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .jenis-tenaga-header {
        padding: 20px;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .jenis-tenaga-item .collapse .card-body {
        padding: 20px;
    }
    
    .detail-header {
        padding: 15px 20px;
    }
    
    .sub-detail-table thead th,
    .sub-detail-table tbody td {
        padding: 12px 15px;
    }
    
    .btn-group-actions {
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .btn-group-actions .btn {
        min-width: 35px;
        height: 35px;
        font-size: 0.8rem;
    }
    
    .badge-count {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding-left: 20px;
        padding-right: 20px;
    }
}

@media (max-width: 576px) {
    .jenis-tenaga-header {
        padding: 15px;
    }
    
    .jenis-tenaga-name {
        font-size: 1.1rem;
    }
    
    .empty-state {
        padding: 40px 20px;
    }
    
    .detail-card {
        margin-bottom: 15px;
    }
}

/* Loading States */
.loading-state {
    opacity: 0.6;
    pointer-events: none;
}

/* Animation for smooth transitions */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Toggle Icon Animation */
.toggle-icon {
    transition: transform 0.3s ease;
}

.toggle-icon.rotated {
    transform: rotate(180deg);
}

/* Enhanced Focus States */
.form-control:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.25);
}

.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.25);
}
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Manajemen Jenis Tenaga Kerja
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Settings</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Tenaga Kerja</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-primary btn-lg" id="add-jenis-tenaga">
                    <i class="ki-outline ki-plus fs-2"></i>
                    Tambah Jenis Tenaga
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <!-- Search & Filter -->
            <div class="card search-filter-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="position-relative">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4 mt-3 text-muted"></i>
                                <input type="text" id="search-input" class="form-control form-control-solid ps-12" 
                                    placeholder="Cari jenis tenaga, detail, atau sub detail...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="status-filter" class="form-select form-select-solid">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-light-primary w-100" id="expand-all">
                                <i class="ki-outline ki-arrow-down me-2"></i> Expand All
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            @if($jenisTenaga->isEmpty())
                <div class="card">
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="ki-outline ki-information-2 fs-3x text-muted"></i>
                            <h5 class="text-muted">Belum ada data jenis tenaga</h5>
                            <p class="text-muted">Klik tombol "Tambah Jenis Tenaga" untuk memulai mengelola data tenaga kerja di sistem ini.</p>
                            <button type="button" class="btn btn-primary btn-lg" id="add-jenis-tenaga-empty">
                                <i class="ki-outline ki-plus me-2"></i> Tambah Jenis Tenaga
                            </button>
                        </div>
                    </div>
                </div>
            @else
                @foreach($jenisTenaga as $jenis)
                    <div class="card jenis-tenaga-item fade-in" data-jenis-id="{{ $jenis->kd_jenis_tenaga }}">
                        <!-- Jenis Tenaga Header -->
                        <div class="jenis-tenaga-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="ki-outline ki-people fs-2x me-4"></i>
                                    <div>
                                        <h4 class="jenis-tenaga-name">{{ $jenis->jenis_tenaga }}</h4>
                                        <div class="d-flex gap-3 mt-2">
                                            <span class="badge badge-light-info badge-count">
                                                <i class="ki-outline ki-document me-1"></i>
                                                {{ $jenis->details->count() }} Detail
                                            </span>
                                            @php
                                                $totalSubDetails = 0;
                                                $activeSubDetails = 0;
                                                foreach($jenis->details as $detail) {
                                                    if(isset($detail->subDetails)) {
                                                        $totalSubDetails += $detail->subDetails->count();
                                                        $activeSubDetails += $detail->subDetails->where('status', '1')->count();
                                                    }
                                                }
                                            @endphp
                                            <span class="badge badge-light-success badge-count">
                                                <i class="ki-outline ki-star me-1"></i>
                                                {{ $totalSubDetails }} Sub Detail
                                            </span>
                                            @if($activeSubDetails > 0)
                                            <span class="badge badge-light-primary badge-count">
                                                <i class="ki-outline ki-check-circle me-1"></i>
                                                {{ $activeSubDetails }} Aktif
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex btn-group-actions">
                                    <button type="button" class="btn btn-light btn-sm edit-jenis-tenaga" 
                                            data-id="{{ $jenis->kd_jenis_tenaga }}"
                                            data-name="{{ $jenis->jenis_tenaga }}"
                                            title="Edit Jenis Tenaga">
                                        <i class="ki-outline ki-pencil fs-6"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm add-detail" 
                                            data-jenis-id="{{ $jenis->kd_jenis_tenaga }}"
                                            data-jenis-name="{{ $jenis->jenis_tenaga }}"
                                            title="Tambah Detail">
                                        <i class="ki-outline ki-plus fs-6"></i>
                                    </button>
                                    @if($jenis->details->isEmpty())
                                    <button type="button" class="btn btn-light-danger btn-sm delete-jenis-tenaga" 
                                            data-id="{{ $jenis->kd_jenis_tenaga }}"
                                            title="Hapus Jenis Tenaga">
                                        <i class="ki-outline ki-trash fs-6"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-light btn-sm toggle-accordion" 
                                            data-target="#details-{{ $jenis->kd_jenis_tenaga }}"
                                            title="Toggle Details">
                                        <i class="ki-outline ki-arrow-down toggle-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="collapse show" id="details-{{ $jenis->kd_jenis_tenaga }}">
                            <div class="card-body">
                                @if($jenis->details->isEmpty())
                                    <div class="empty-state">
                                        <i class="ki-outline ki-information fs-3x text-muted"></i>
                                        <h6 class="text-muted">Belum ada detail untuk {{ $jenis->jenis_tenaga }}</h6>
                                        <p class="text-muted">Tambahkan detail untuk mengategorikan jenis tenaga ini lebih spesifik.</p>
                                        <button type="button" class="btn btn-primary add-detail" 
                                                data-jenis-id="{{ $jenis->kd_jenis_tenaga }}"
                                                data-jenis-name="{{ $jenis->jenis_tenaga }}">
                                            <i class="ki-outline ki-plus me-2"></i> Tambah Detail
                                        </button>
                                    </div>
                                @else
                                    @foreach($jenis->details as $detail)
                                    <div class="detail-card" data-detail-id="{{ $detail->kd_detail }}">
                                        <!-- Detail Header -->
                                        <div class="detail-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="ki-outline ki-user fs-3 text-success me-4"></i>
                                                    <div>
                                                        <h6 class="detail-name fw-bold text-gray-800 mb-1">{{ $detail->detail_jenis_tenaga }}</h6>
                                                        <div class="detail-info">
                                                            <small class="text-muted">
                                                                @if(isset($detail->subDetails))
                                                                    {{ $detail->subDetails->count() }} sub detail
                                                                    @if($detail->subDetails->where('status', '1')->count() > 0)
                                                                        • {{ $detail->subDetails->where('status', '1')->count() }} aktif
                                                                    @endif
                                                                @else
                                                                    0 sub detail
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex btn-group-actions">
                                                    <button type="button" class="btn btn-light-warning btn-sm action-btn edit-detail" 
                                                            data-jenis-id="{{ $jenis->kd_jenis_tenaga }}"
                                                            data-detail-id="{{ $detail->kd_detail }}"
                                                            data-name="{{ $detail->detail_jenis_tenaga }}"
                                                            title="Edit Detail">
                                                        <i class="ki-outline ki-pencil fs-6"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-light-primary btn-sm action-btn add-sub-detail" 
                                                            data-jenis-id="{{ $jenis->kd_jenis_tenaga }}"
                                                            data-detail-id="{{ $detail->kd_detail }}"
                                                            data-jenis-name="{{ $jenis->jenis_tenaga }}"
                                                            data-detail-name="{{ $detail->detail_jenis_tenaga }}"
                                                            title="Tambah Sub Detail">
                                                        <i class="ki-outline ki-plus fs-6"></i>
                                                    </button>
                                                    @if(!isset($detail->subDetails) || $detail->subDetails->isEmpty())
                                                    <button type="button" class="btn btn-light-danger btn-sm action-btn delete-detail" 
                                                            data-jenis-id="{{ $jenis->kd_jenis_tenaga }}"
                                                            data-detail-id="{{ $detail->kd_detail }}"
                                                            title="Hapus Detail">
                                                        <i class="ki-outline ki-trash fs-6"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Sub Details -->
                                        @if(isset($detail->subDetails) && $detail->subDetails->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-row-bordered sub-detail-table">
                                                    <thead>
                                                        <tr class="fw-bold text-gray-800">
                                                            <th class="min-w-200px">Sub Detail</th>
                                                            <th class="min-w-120px">Kode SDMK</th>
                                                            <th class="min-w-120px">Kelompok</th>
                                                            <th class="min-w-100px">Status</th>
                                                            <th class="min-w-140px text-end">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($detail->subDetails as $subDetail)
                                                        <tr class="sub-detail-row" data-sub-detail-id="{{ $subDetail->kd_sub_detail }}">
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="ki-outline ki-star text-warning fs-5 me-3"></i>
                                                                    <span class="sub-detail-name fw-semibold text-gray-800">{{ $subDetail->sub_detail }}</span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @if($subDetail->kd_sdmk)
                                                                    <span class="badge badge-light-primary fs-7">{{ $subDetail->kd_sdmk }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="text-gray-700 fw-semibold">{{ $subDetail->kelompok_spesialis ?: '-' }}</span>
                                                            </td>
                                                            <td>
                                                                @if($subDetail->status == '1')
                                                                    <span class="badge badge-light-success fs-7">
                                                                        <i class="ki-outline ki-check-circle me-1"></i>Aktif
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-light-danger fs-7">
                                                                        <i class="ki-outline ki-cross-circle me-1"></i>Tidak Aktif
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="d-flex justify-content-end btn-group-actions">
                                                                    <button type="button" class="btn btn-light-warning btn-sm action-btn edit-sub-detail" 
                                                                            data-jenis-id="{{ $jenis->kd_jenis_tenaga }}"
                                                                            data-detail-id="{{ $detail->kd_detail }}"
                                                                            data-sub-detail-id="{{ $subDetail->kd_sub_detail }}"
                                                                            data-sub-detail="{{ $subDetail->sub_detail }}"
                                                                            data-kd-sdmk="{{ $subDetail->kd_sdmk }}"
                                                                            data-kelompok="{{ $subDetail->kelompok_spesialis }}"
                                                                            data-status="{{ $subDetail->status }}"
                                                                            title="Edit Sub Detail">
                                                                        <i class="ki-outline ki-pencil fs-6"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-light-danger btn-sm action-btn delete-sub-detail" 
                                                                            data-jenis-id="{{ $jenis->kd_jenis_tenaga }}"
                                                                            data-detail-id="{{ $detail->kd_detail }}"
                                                                            data-sub-detail-id="{{ $subDetail->kd_sub_detail }}"
                                                                            title="Hapus Sub Detail">
                                                                        <i class="ki-outline ki-trash fs-6"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                        <div class="empty-state">
                                            <i class="ki-outline ki-information fs-3x text-muted"></i>
                                            <p class="text-muted">Belum ada sub detail untuk "{{ $detail->detail_jenis_tenaga }}"</p>
                                            <p class="text-muted">Tambahkan sub detail untuk detail ini agar lebih spesifik.</p>
                                            <button type="button" class="btn btn-primary add-sub-detail" 
                                                    data-jenis-id="{{ $jenis->kd_jenis_tenaga }}"
                                                    data-detail-id="{{ $detail->kd_detail }}"
                                                    data-jenis-name="{{ $jenis->jenis_tenaga }}"
                                                    data-detail-name="{{ $detail->detail_jenis_tenaga }}">
                                                <i class="ki-outline ki-plus me-2"></i> Tambah Sub Detail
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Modals -->
        <!-- Modal: Jenis Tenaga -->
        <div class="modal fade" id="jenisTenagaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog mw-550px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold modal-title" id="jenisTenagaModalTitle">Tambah Jenis Tenaga</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <form id="jenisTenagaForm">
                        <input type="hidden" id="jenis_tenaga_id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="fw-semibold fs-6 mb-2 required">Nama Jenis Tenaga</label>
                                <input type="text" class="form-control form-control-solid" id="jenis_tenaga_name" 
                                    placeholder="Contoh: MEDIS" required>
                                <div class="invalid-feedback jenis-tenaga-error"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">
                                    Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Detail Jenis Tenaga -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog mw-550px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold modal-title" id="detailModalTitle">Tambah Detail</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <form id="detailForm">
                        <input type="hidden" id="detail_jenis_id">
                        <input type="hidden" id="detail_id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="fw-semibold fs-6 mb-2">Jenis Tenaga</label>
                                <input type="text" class="form-control form-control-solid" id="detail_jenis_name" readonly>
                            </div>
                            <div class="form-group">
                                <label class="fw-semibold fs-6 mb-2 required">Nama Detail</label>
                                <input type="text" class="form-control form-control-solid" id="detail_name" 
                                    placeholder="Contoh: DOKTER SPESIALIS" required>
                                <div class="invalid-feedback detail-error"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">
                                    Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Sub Detail -->
        <div class="modal fade" id="subDetailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog mw-700px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold modal-title" id="subDetailModalTitle">Tambah Sub Detail</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <form id="subDetailForm">
                        <input type="hidden" id="sub_detail_jenis_id">
                        <input type="hidden" id="sub_detail_detail_id">
                        <input type="hidden" id="sub_detail_id">
                        <div class="modal-body">
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label class="fw-semibold fs-6 mb-2">Jenis Tenaga</label>
                                    <input type="text" class="form-control form-control-solid" id="sub_detail_jenis_name" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold fs-6 mb-2">Detail</label>
                                    <input type="text" class="form-control form-control-solid" id="sub_detail_detail_name" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="fw-semibold fs-6 mb-2 required">Nama Sub Detail</label>
                                <input type="text" class="form-control form-control-solid" id="sub_detail_name" 
                                    placeholder="Contoh: SPESIALIS MATA" required>
                                <div class="invalid-feedback sub-detail-error"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label class="fw-semibold fs-6 mb-2">Kode SDMK</label>
                                    <input type="text" class="form-control form-control-solid" id="kd_sdmk" 
                                        placeholder="Contoh: 1010334">
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold fs-6 mb-2">Kelompok Spesialis</label>
                                    <input type="text" class="form-control form-control-solid" id="kelompok_spesialis" 
                                        placeholder="Contoh: 2.1">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="fw-semibold fs-6 mb-2">Status</label>
                                <select class="form-select form-select-solid" id="sub_detail_status">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">
                                    Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // CSRF Token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Toggle accordion functionality with enhanced animation
            $('.toggle-accordion').on('click', function() {
                let target = $(this).data('target');
                let icon = $(this).find('.toggle-icon');
                
                $(target).collapse('toggle');
                
                $(target).on('shown.bs.collapse', function() {
                    icon.removeClass('ki-arrow-down').addClass('ki-arrow-up').addClass('rotated');
                    $(this).off('shown.bs.collapse'); // Remove event listener after use
                });
                
                $(target).on('hidden.bs.collapse', function() {
                    icon.removeClass('ki-arrow-up rotated').addClass('ki-arrow-down');
                    $(this).off('hidden.bs.collapse'); // Remove event listener after use
                });
            });

            // Enhanced search functionality
            $('#search-input').on('keyup', function() {
                let searchTerm = $(this).val().toLowerCase();
                let visibleCount = 0;
                
                $('.jenis-tenaga-item').each(function() {
                    let item = $(this);
                    let jenisName = item.find('.jenis-tenaga-name').text().toLowerCase();
                    let detailNames = item.find('.detail-name').map(function() {
                        return $(this).text().toLowerCase();
                    }).get().join(' ');
                    let subDetailNames = item.find('.sub-detail-name').map(function() {
                        return $(this).text().toLowerCase();
                    }).get().join(' ');
                    
                    let allText = jenisName + ' ' + detailNames + ' ' + subDetailNames;
                    
                    if (allText.includes(searchTerm) || searchTerm === '') {
                        item.show().addClass('fade-in');
                        visibleCount++;
                    } else {
                        item.hide().removeClass('fade-in');
                    }
                });
                
                // Show "no results" message if needed
                if (visibleCount === 0 && searchTerm !== '') {
                    if ($('#no-results-message').length === 0) {
                        $('.app-container').append(`
                            <div id="no-results-message" class="card">
                                <div class="card-body">
                                    <div class="empty-state">
                                        <i class="ki-outline ki-magnifier fs-3x text-muted"></i>
                                        <h5 class="text-muted">Tidak ada hasil ditemukan</h5>
                                        <p class="text-muted">Coba gunakan kata kunci yang berbeda atau periksa ejaan.</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    }
                } else {
                    $('#no-results-message').remove();
                }
            });

            // Enhanced expand all functionality
            $('#expand-all').on('click', function() {
                let isExpanded = $(this).data('expanded') || false;
                let button = $(this);
                
                if (isExpanded) {
                    $('.collapse').collapse('hide');
                    $('.toggle-icon').removeClass('ki-arrow-up rotated').addClass('ki-arrow-down');
                    button.html('<i class="ki-outline ki-arrow-down me-2"></i> Expand All');
                    button.removeClass('btn-light-warning').addClass('btn-light-primary');
                } else {
                    $('.collapse').collapse('show');
                    $('.toggle-icon').removeClass('ki-arrow-down').addClass('ki-arrow-up rotated');
                    button.html('<i class="ki-outline ki-arrow-up me-2"></i> Collapse All');
                    button.removeClass('btn-light-primary').addClass('btn-light-warning');
                }
                $(this).data('expanded', !isExpanded);
            });

            // Status filter functionality
            $('#status-filter').on('change', function() {
                let selectedStatus = $(this).val();
                
                $('.jenis-tenaga-item').each(function() {
                    let item = $(this);
                    let hasMatchingStatus = false;
                    
                    if (selectedStatus === '') {
                        hasMatchingStatus = true;
                    } else {
                        // Check sub details for matching status
                        item.find('.sub-detail-row').each(function() {
                            let statusBadge = $(this).find('.badge');
                            let isActive = statusBadge.hasClass('badge-light-success');
                            
                            if ((selectedStatus === '1' && isActive) || (selectedStatus === '0' && !isActive)) {
                                hasMatchingStatus = true;
                                return false; // Break loop
                            }
                        });
                    }
                    
                    if (hasMatchingStatus) {
                        item.show().addClass('fade-in');
                    } else {
                        item.hide().removeClass('fade-in');
                    }
                });
            });

            // Add Jenis Tenaga
            $('#add-jenis-tenaga, #add-jenis-tenaga-empty').on('click', function() {
                resetForm('#jenisTenagaForm');
                $('#jenisTenagaModalTitle').text('Tambah Jenis Tenaga');
                $('#jenis_tenaga_id').val('');
                $('#jenis_tenaga_name').val('').focus();
                $('#jenisTenagaModal').modal('show');
            });

            // Edit Jenis Tenaga
            $(document).on('click', '.edit-jenis-tenaga', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                
                resetForm('#jenisTenagaForm');
                $('#jenisTenagaModalTitle').text('Edit Jenis Tenaga');
                $('#jenis_tenaga_id').val(id);
                $('#jenis_tenaga_name').val(name).focus();
                $('#jenisTenagaModal').modal('show');
            });

            // Submit Jenis Tenaga Form
            $('#jenisTenagaForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let id = $('#jenis_tenaga_id').val();
                let isEdit = id !== '';
                let url = isEdit 
                    ? '{{ route("admin.settings.tenaga-management.jenis-tenaga.update", ":id") }}'.replace(':id', id)
                    : '{{ route("admin.settings.tenaga-management.jenis-tenaga.store") }}';
                let method = isEdit ? 'PATCH' : 'POST';

                submitForm(form, url, method, {
                    jenis_tenaga: $('#jenis_tenaga_name').val()
                }, '.jenis-tenaga-error', '#jenisTenagaModal');
            });

            // Add Detail
            $(document).on('click', '.add-detail', function() {
                let jenisId = $(this).data('jenis-id');
                let jenisName = $(this).data('jenis-name');
                
                resetForm('#detailForm');
                $('#detailModalTitle').text('Tambah Detail untuk ' + jenisName);
                $('#detail_jenis_id').val(jenisId);
                $('#detail_id').val('');
                $('#detail_jenis_name').val(jenisName);
                $('#detail_name').val('').focus();
                $('#detailModal').modal('show');
            });

            // Edit Detail
            $(document).on('click', '.edit-detail', function() {
                let jenisId = $(this).data('jenis-id');
                let detailId = $(this).data('detail-id');
                let name = $(this).data('name');
                let jenisName = $(this).closest('.jenis-tenaga-item').find('.jenis-tenaga-name').text();
                
                resetForm('#detailForm');
                $('#detailModalTitle').text('Edit Detail');
                $('#detail_jenis_id').val(jenisId);
                $('#detail_id').val(detailId);
                $('#detail_jenis_name').val(jenisName);
                $('#detail_name').val(name).focus();
                $('#detailModal').modal('show');
            });

            // Submit Detail Form
            $('#detailForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let jenisId = $('#detail_jenis_id').val();
                let detailId = $('#detail_id').val();
                let isEdit = detailId !== '';
                let url = isEdit 
                    ? '{{ route("admin.settings.tenaga-management.detail.update", [":jenisId", ":detailId"]) }}'.replace(':jenisId', jenisId).replace(':detailId', detailId)
                    : '{{ route("admin.settings.tenaga-management.detail.store") }}';
                let method = isEdit ? 'PATCH' : 'POST';

                submitForm(form, url, method, {
                    kd_jenis_tenaga: jenisId,
                    detail_jenis_tenaga: $('#detail_name').val()
                }, '.detail-error', '#detailModal');
            });

            // Add Sub Detail
            $(document).on('click', '.add-sub-detail', function() {
                let jenisId = $(this).data('jenis-id');
                let detailId = $(this).data('detail-id');
                let jenisName = $(this).data('jenis-name');
                let detailName = $(this).data('detail-name');
                
                resetForm('#subDetailForm');
                $('#subDetailModalTitle').text('Tambah Sub Detail');
                $('#sub_detail_jenis_id').val(jenisId);
                $('#sub_detail_detail_id').val(detailId);
                $('#sub_detail_id').val('');
                $('#sub_detail_jenis_name').val(jenisName);
                $('#sub_detail_detail_name').val(detailName);
                $('#sub_detail_name').val('').focus();
                $('#kd_sdmk, #kelompok_spesialis').val('');
                $('#sub_detail_status').val('1');
                $('#subDetailModal').modal('show');
            });

            // Edit Sub Detail
            $(document).on('click', '.edit-sub-detail', function() {
                let jenisId = $(this).data('jenis-id');
                let detailId = $(this).data('detail-id');
                let subDetailId = $(this).data('sub-detail-id');
                let subDetail = $(this).data('sub-detail');
                let kdSdmk = $(this).data('kd-sdmk');
                let kelompok = $(this).data('kelompok');
                let status = $(this).data('status');
                let jenisName = $(this).closest('.jenis-tenaga-item').find('.jenis-tenaga-name').text();
                let detailName = $(this).closest('.detail-card').find('.detail-name').text();
                
                resetForm('#subDetailForm');
                $('#subDetailModalTitle').text('Edit Sub Detail');
                $('#sub_detail_jenis_id').val(jenisId);
                $('#sub_detail_detail_id').val(detailId);
                $('#sub_detail_id').val(subDetailId);
                $('#sub_detail_jenis_name').val(jenisName);
                $('#sub_detail_detail_name').val(detailName);
                $('#sub_detail_name').val(subDetail).focus();
                $('#kd_sdmk').val(kdSdmk || '');
                $('#kelompok_spesialis').val(kelompok || '');
                $('#sub_detail_status').val(status);
                $('#subDetailModal').modal('show');
            });

            // Submit Sub Detail Form
            $('#subDetailForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let jenisId = $('#sub_detail_jenis_id').val();
                let detailId = $('#sub_detail_detail_id').val();
                let subDetailId = $('#sub_detail_id').val();
                let isEdit = subDetailId !== '';
                let url = isEdit 
                    ? '{{ route("admin.settings.tenaga-management.sub-detail.update", [":jenisId", ":detailId", ":subDetailId"]) }}'.replace(':jenisId', jenisId).replace(':detailId', detailId).replace(':subDetailId', subDetailId)
                    : '{{ route("admin.settings.tenaga-management.sub-detail.store") }}';
                let method = isEdit ? 'PATCH' : 'POST';

                submitForm(form, url, method, {
                    kd_jenis_tenaga: jenisId,
                    kd_detail: detailId,
                    sub_detail: $('#sub_detail_name').val(),
                    kd_sdmk: $('#kd_sdmk').val(),
                    kelompok_spesialis: $('#kelompok_spesialis').val(),
                    status: $('#sub_detail_status').val()
                }, '.sub-detail-error', '#subDetailModal');
            });

            // Enhanced Delete functions with better SweetAlert styling
            $(document).on('click', '.delete-jenis-tenaga', function() {
                let id = $(this).data('id');
                let name = $(this).closest('.jenis-tenaga-item').find('.jenis-tenaga-name').text();
                
                showDeleteConfirmation(
                    'Hapus Jenis Tenaga?',
                    `Apakah Anda yakin ingin menghapus "${name}"? Data yang sudah dihapus tidak dapat dikembalikan!`,
                    function() {
                        deleteItem('{{ route("admin.settings.tenaga-management.jenis-tenaga.destroy", ":id") }}'.replace(':id', id));
                    }
                );
            });

            $(document).on('click', '.delete-detail', function() {
                let jenisId = $(this).data('jenis-id');
                let detailId = $(this).data('detail-id');
                let name = $(this).closest('.detail-card').find('.detail-name').text();
                
                showDeleteConfirmation(
                    'Hapus Detail?',
                    `Apakah Anda yakin ingin menghapus "${name}"? Data yang sudah dihapus tidak dapat dikembalikan!`,
                    function() {
                        deleteItem('{{ route("admin.settings.tenaga-management.detail.destroy", [":jenisId", ":detailId"]) }}'.replace(':jenisId', jenisId).replace(':detailId', detailId));
                    }
                );
            });

            $(document).on('click', '.delete-sub-detail', function() {
                let jenisId = $(this).data('jenis-id');
                let detailId = $(this).data('detail-id');
                let subDetailId = $(this).data('sub-detail-id');
                let name = $(this).closest('.sub-detail-row').find('.sub-detail-name').text();
                
                showDeleteConfirmation(
                    'Hapus Sub Detail?',
                    `Apakah Anda yakin ingin menghapus "${name}"? Data yang sudah dihapus tidak dapat dikembalikan!`,
                    function() {
                        deleteItem('{{ route("admin.settings.tenaga-management.sub-detail.destroy", [":jenisId", ":detailId", ":subDetailId"]) }}'.replace(':jenisId', jenisId).replace(':detailId', detailId).replace(':subDetailId', subDetailId));
                    }
                );
            });

            // Helper Functions
            function resetForm(formSelector) {
                $(formSelector)[0].reset();
                $(formSelector).find('.is-invalid').removeClass('is-invalid');
                $(formSelector).find('.invalid-feedback').text('');
            }

            function submitForm(form, url, method, data, errorSelector, modalSelector) {
                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    beforeSend: function() {
                        setLoadingState(form, true);
                    },
                    success: function(response) {
                        showSuccessMessage(response.message);
                        $(modalSelector).modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    $(errorSelector).text(errors[field][0]);
                                    break;
                                }
                            }
                        } else {
                            showErrorMessage('Terjadi kesalahan saat menyimpan data');
                        }
                    },
                    complete: function() {
                        setLoadingState(form, false);
                    }
                });
            }

            function setLoadingState(form, isLoading) {
                const submitBtn = form.find('button[type="submit"]');
                const indicator = submitBtn.find('.indicator-label, .indicator-progress');
                
                if (isLoading) {
                    submitBtn.attr('disabled', true);
                    indicator.filter('.indicator-label').hide();
                    indicator.filter('.indicator-progress').show();
                } else {
                    submitBtn.attr('disabled', false);
                    indicator.filter('.indicator-label').show();
                    indicator.filter('.indicator-progress').hide();
                }
            }

            function showDeleteConfirmation(title, text, confirmCallback) {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirmCallback();
                    }
                });
            }

            function deleteItem(url) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        showSuccessMessage(response.message);
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        showErrorMessage(xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data');
                    }
                });
            }

            function showSuccessMessage(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: message,
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            function showErrorMessage(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }

            // Auto-expand first item on page load with animation
            setTimeout(() => {
                $('.collapse:first').collapse('show');
                $('.toggle-icon:first').removeClass('ki-arrow-down').addClass('ki-arrow-up rotated');
            }, 300);

            // Add fade-in animation to existing items
            $('.jenis-tenaga-item').addClass('fade-in');
        });
    </script>
@endpush