@extends('layouts.user', ['title' => 'Profil Saya'])

@push('styles')
<style>
    .profile-photo-section {
        position: relative;
        display: inline-block;
    }
    
    .photo-upload-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .photo-upload-overlay:hover {
        background: rgba(0, 0, 0, 0.9);
        transform: scale(1.1);
    }
    
    .info-card .table td {
        border: none;
        padding: 0.75rem 0;
    }
    
    .info-card .table td:first-child {
        font-weight: 600;
        color: #5e6278;
        width: 40%;
    }
    
    .completion-item {
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        justify-content: between;
    }
    
    .completion-filled {
        background-color: #e8f5e8;
        color: #1e7e34;
    }
    
    .completion-empty {
        background-color: #fff5f5;
        color: #dc3545;
    }
    
    /* Photo crop styles */
    .crop-container {
        max-height: 400px;
        overflow: hidden;
        border: 1px solid #e1e5e9;
        border-radius: 4px;
    }
    
    .crop-preview {
        border: 1px solid #e1e5e9;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f8f9fa;
    }
    
    .crop-preview-square {
        width: 150px;
        height: 150px;
    }
    
    .crop-preview-cv {
        width: 150px;
        height: 200px;
    }
    
    .upload-hint {
        text-align: center;
        padding: 20px;
    }
    
    .crop-controls {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .photo-type-tabs .nav-link {
        border-radius: 8px 8px 0 0;
    }
    
    .photo-type-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #f8f9fa;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Profil Saya
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Profil Saya</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('user.profil.print-cv') }}" class="btn btn-light-success" target="_blank">
                    <i class="ki-duotone ki-printer fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Print CV
                </a>
                <a href="{{ route('user.profil.edit') }}" class="btn btn-primary">
                    <i class="ki-duotone ki-pencil fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Edit Profil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        {{-- Profile Header --}}
        <div class="card mb-8">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="profile-photo-section">
                            <div class="symbol symbol-150px symbol-circle">
                                @if($photo)
                                    <img src="{{ $photo }}" alt="Foto Profil" id="profile-photo" />
                                @else
                                    <div class="symbol-label bg-light-primary text-primary fw-bold fs-1">
                                        {{ substr($karyawan->nama, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="photo-upload-overlay" data-bs-toggle="modal" data-bs-target="#photoUploadModal">
                                <i class="ki-duotone ki-pencil fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-gray-900 fw-bold mb-1">
                                @if($karyawan->gelar_depan){{ $karyawan->gelar_depan }} @endif{{ $karyawan->nama }}@if($karyawan->gelar_belakang), {{ $karyawan->gelar_belakang }}@endif
                            </h3>
                            <div class="text-muted fs-6">{{ $karyawan->kd_karyawan }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <div class="symbol-label bg-light-info">
                                                <i class="ki-duotone ki-office-bag text-info fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fs-7 text-muted">Ruangan</div>
                                            <div class="fs-6 fw-bold text-gray-700">{{ $dataKepegawaian['ruangan'] ?? 'Belum diisi' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <div class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-profile-circle text-primary fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fs-7 text-muted">Golongan</div>
                                            <div class="fs-6 fw-bold text-gray-700">{{ $dataKepegawaian['golongan'] ?? 'Belum diisi' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <div class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-briefcase text-success fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fs-7 text-muted">Status</div>
                                            <div class="fs-6 fw-bold text-gray-700">{{ $dataKepegawaian['status_kerja'] ?? 'Belum diisi' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px me-3">
                                            <div class="symbol-label bg-light-warning">
                                                <i class="ki-duotone ki-calendar text-warning fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fs-7 text-muted">TMT</div>
                                            <div class="fs-6 fw-bold text-gray-700">
                                                {{ $dataKepegawaian['tmt'] ? date('d/m/Y', strtotime($dataKepegawaian['tmt'])) : 'Belum diisi' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-6 fw-bold text-gray-800 mb-2">Kelengkapan Profil</div>
                            <div class="progress h-20px">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: {{ $completionData['percentage'] }}%" 
                                     aria-valuenow="{{ $completionData['percentage'] }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="fs-7 text-muted mt-2">{{ $completionData['percentage'] }}% Lengkap</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-6 g-xl-9">
            {{-- Data Personal --}}
            <div class="col-xl-6">
                <div class="card info-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Data Personal</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td>Nama Lengkap</td>
                                <td>: 
                                    @if($karyawan->gelar_depan){{ $karyawan->gelar_depan }} @endif{{ $karyawan->nama }}@if($karyawan->gelar_belakang), {{ $karyawan->gelar_belakang }}@endif
                                </td>
                            </tr>
                            <tr>
                                <td>NIK</td>
                                <td>: {{ $karyawan->no_ktp ?: 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Tempat, Tgl Lahir</td>
                                <td>: 
                                    @if($karyawan->tempat_lahir && $karyawan->tgl_lahir)
                                        {{ $karyawan->tempat_lahir }}, {{ date('d/m/Y', strtotime($karyawan->tgl_lahir)) }}
                                    @else
                                        Belum diisi
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td>: {{ $alamatData['jenis_kelamin'] ?? 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Agama</td>
                                <td>: {{ $alamatData['agama'] ?? 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Status Pernikahan</td>
                                <td>: {{ $alamatData['status_nikah'] ?? 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>: {{ $karyawan->alamat ?: 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Provinsi</td>
                                <td>: {{ $alamatData['provinsi'] ?? 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Kabupaten/Kota</td>
                                <td>: {{ $alamatData['kabupaten'] ?? 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Kecamatan</td>
                                <td>: {{ $alamatData['kecamatan'] ?? 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Kelurahan</td>
                                <td>: {{ $alamatData['kelurahan'] ?? 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr class="my-3"></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong class="text-gray-800">Data Fisik</strong></td>
                            </tr>
                            <tr>
                                <td>Tinggi Badan</td>
                                <td>: {{ $karyawan->tinggi_badan ? $karyawan->tinggi_badan . ' cm' : 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <td>Berat Badan</td>
                                <td>: {{ $karyawan->berat_badan ? $karyawan->berat_badan . ' kg' : 'Belum diisi' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- Kontak & Pendidikan --}}
            <div class="col-xl-6">
                <div class="row g-6">
                    {{-- Kontak --}}
                    <div class="col-12">
                        <div class="card info-card">
                            <div class="card-header">
                                <h3 class="card-title">Kontak</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td style="width: 40%">Email</td>
                                        <td>: {{ $karyawan->email ?: 'Belum diisi' }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. HP</td>
                                        <td>: {{ $karyawan->no_hp ?: 'Belum diisi' }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Telepon</td>
                                        <td>: {{ $karyawan->no_telepon ?: 'Belum diisi' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Pendidikan --}}
                    <div class="col-12">
                        <div class="card info-card">
                            <div class="card-header">
                                <h3 class="card-title">Pendidikan Terakhir</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td style="width: 40%">Tingkat</td>
                                        <td>: {{ $pendidikanData['tingkat'] ?? 'Belum diisi' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Institusi</td>
                                        <td>: {{ $pendidikanData['institusi'] ?? 'Belum diisi' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jurusan</td>
                                        <td>: {{ $pendidikanData['jurusan'] ?? 'Belum diisi' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tahun Lulus</td>
                                        <td>: {{ $pendidikanData['tahun_lulus'] ?? 'Belum diisi' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Kelengkapan Data --}}
        <div class="row g-6 g-xl-9 mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kelengkapan Data</h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-primary">{{ $completionData['percentage'] }}% Lengkap</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($completionData['missing_fields']) > 0)
                            <div class="alert alert-warning">
                                <h5 class="mb-3">Field yang belum diisi:</h5>
                                <div class="row">
                                    @foreach($completionData['missing_fields'] as $field)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="completion-item completion-empty">
                                                <div class="d-flex align-items-center justify-content-between w-100">
                                                    <span class="fs-7">{{ $field }}</span>
                                                    <i class="ki-duotone ki-cross fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('user.profil.edit') }}" class="btn btn-warning">
                                        <i class="ki-duotone ki-pencil fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Lengkapi Data Profil
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-check-circle fs-2x text-success me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div>
                                        <h5 class="mb-1">Data Profil Lengkap!</h5>
                                        <p class="mb-0">Semua field wajib sudah terisi dengan lengkap.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

{{-- Photo Upload Modal --}}
<div class="modal fade" id="photoUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold modal-title">
                    <i class="ki-duotone ki-camera fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>Upload & Crop Foto
                </h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form id="photoUploadForm" class="form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs photo-type-tabs mb-4" id="photoTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="square-tab" data-bs-toggle="tab" data-bs-target="#square-pane" type="button" role="tab">
                                <i class="ki-duotone ki-profile-circle me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>Foto Profil (1:1)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cv-tab" data-bs-toggle="tab" data-bs-target="#cv-pane" type="button" role="tab">
                                <i class="ki-duotone ki-document me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>Foto CV (3:4)
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="photoTypeContent">
                        <!-- Foto Profil (1:1) -->
                        <div class="tab-pane fade show active" id="square-pane" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="ki-duotone ki-cloud-add me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Pilih Foto untuk Profil (Rasio 1:1)
                                        </label>
                                        <input type="file" class="form-control" id="foto_square_input" accept="image/jpeg,image/png,image/jpg">
                                        <div class="invalid-feedback" id="foto_square_error"></div>
                                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                                    </div>
                                    
                                    <div id="square_crop_container" style="display: none;">
                                        <div class="crop-container">
                                            <img id="square_crop_image" style="max-width: 100%;">
                                        </div>
                                        
                                        <div class="crop-controls mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="croppers.square.zoom(0.1)">
                                                <i class="ki-duotone ki-plus fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i> Zoom In
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="croppers.square.zoom(-0.1)">
                                                <i class="ki-duotone ki-minus fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i> Zoom Out
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="croppers.square.rotate(90)">
                                                <i class="ki-duotone ki-rotate-right fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i> Putar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="croppers.square.reset()">
                                                <i class="ki-duotone ki-arrows-circle fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Preview:</label>
                                    <div class="crop-preview crop-preview-square" id="square_preview">
                                        <div class="upload-hint">
                                            <i class="ki-duotone ki-picture fs-2x text-muted mb-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <p class="text-muted mb-0">Preview akan muncul di sini</p>
                                        </div>
                                    </div>
                                    @if($photo)
                                        <small class="text-muted">Foto saat ini:</small>
                                        <img src="{{ $photo }}" class="img-thumbnail mt-1" style="max-width: 100px;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Foto CV (3:4) -->
                        <div class="tab-pane fade" id="cv-pane" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="ki-duotone ki-cloud-add me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Pilih Foto untuk CV (Rasio 3:4)
                                        </label>
                                        <input type="file" class="form-control" id="foto_input" accept="image/jpeg,image/png,image/jpg">
                                        <div class="invalid-feedback" id="foto_error"></div>
                                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                                    </div>
                                    
                                    <div id="cv_crop_container" style="display: none;">
                                        <div class="crop-container">
                                            <img id="cv_crop_image" style="max-width: 100%;">
                                        </div>
                                        
                                        <div class="crop-controls mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="croppers.cv.zoom(0.1)">
                                                <i class="ki-duotone ki-plus fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i> Zoom In
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="croppers.cv.zoom(-0.1)">
                                                <i class="ki-duotone ki-minus fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i> Zoom Out
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="croppers.cv.rotate(90)">
                                                <i class="ki-duotone ki-rotate-right fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i> Putar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="croppers.cv.reset()">
                                                <i class="ki-duotone ki-arrows-circle fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Preview:</label>
                                    <div class="crop-preview crop-preview-cv" id="cv_preview">
                                        <div class="upload-hint">
                                            <i class="ki-duotone ki-picture fs-2x text-muted mb-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <p class="text-muted mb-0">Preview akan muncul di sini</p>
                                        </div>
                                    </div>
                                    @if(PhotoHelper::getPhotoUrl($karyawan, 'foto'))
                                        <small class="text-muted">Foto CV saat ini:</small>
                                        <img src="{{ PhotoHelper::getPhotoUrl($karyawan, 'foto') }}" class="img-thumbnail mt-1" style="max-width: 100px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="uploadSubmitButton">
                        <i class="ki-duotone ki-cloud-add me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
$(document).ready(function() {
    // Photo crop variables
    let croppers = {
        square: null,
        cv: null
    };
    
    // Initialize file inputs
    ['square', 'cv'].forEach(function(type) {
        const inputId = type === 'square' ? 'foto_square_input' : 'foto_input';
        const imageId = type + '_crop_image';
        const containerId = type + '_crop_container';
        const previewId = type + '_preview';
        
        $('#' + inputId).on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Validate file
            if (!file.type.match('image.*')) {
                toastr.error('File harus berupa gambar');
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                toastr.error('Ukuran file maksimal 2MB');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                // Destroy existing cropper
                if (croppers[type]) {
                    croppers[type].destroy();
                }
                
                // Set image source and show container
                $('#' + imageId).attr('src', e.target.result);
                $('#' + containerId).show();
                
                // Initialize cropper
                const aspectRatio = type === 'square' ? 1 : 3/4;
                croppers[type] = new Cropper(document.getElementById(imageId), {
                    aspectRatio: aspectRatio,
                    viewMode: 1,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    preview: '#' + previewId
                });
            };
            reader.readAsDataURL(file);
        });
    });
    
    // Function to get cropped image
    function getCroppedImage(type, callback) {
        if (!croppers[type]) {
            callback(null);
            return;
        }
        
        const canvas = croppers[type].getCroppedCanvas({
            width: type === 'square' ? 300 : 300,
            height: type === 'square' ? 300 : 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });
        
        if (canvas) {
            canvas.toBlob(callback, 'image/jpeg', 0.9);
        } else {
            callback(null);
        }
    }
    
    // Handle upload photo form submission
    $('#photoUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#uploadSubmitButton').prop('disabled', true).text('Memproses...');
        
        const formData = new FormData();
        formData.append('_token', $('input[name="_token"]').val());
        
        let processedCount = 0;
        const totalCroppers = 2;
        let hasImages = false;
        
        // Process setiap cropped image
        ['square', 'cv'].forEach((type, index) => {
            const fieldName = type === 'square' ? 'foto_square' : 'foto';
            
            getCroppedImage(type, function(blob) {
                if (blob) {
                    formData.append(fieldName, blob, `${fieldName}_${Date.now()}.jpg`);
                    hasImages = true;
                }
                
                processedCount++;
                if (processedCount === totalCroppers) {
                    if (!hasImages) {
                        toastr.warning('Pilih minimal satu foto untuk diupload.');
                        $('#uploadSubmitButton').prop('disabled', false).text('Upload Foto');
                        return;
                    }
                    submitForm(formData);
                }
            });
        });
    });
    
    function submitForm(formData) {
        $.ajax({
            url: '{{ route("user.profil.upload-photo") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#photoUploadModal').modal('hide');
                    
                    // Refresh halaman untuk menampilkan foto baru
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Gagal mengupload foto');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan saat mengupload foto';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            },
            complete: function() {
                $('#uploadSubmitButton').prop('disabled', false).text('Upload Foto');
            }
        });
    }
    
    // Reset modal when closed
    $('#photoUploadModal').on('hidden.bs.modal', function() {
        // Reset file inputs
        $('#foto_square_input, #foto_input').val('');
        
        // Hide crop containers
        $('#square_crop_container, #cv_crop_container').hide();
        
        // Destroy croppers
        Object.keys(croppers).forEach(type => {
            if (croppers[type]) {
                croppers[type].destroy();
                croppers[type] = null;
            }
        });
        
        // Reset previews
        $('#square_preview, #cv_preview').html(`
            <div class="upload-hint">
                <i class="ki-duotone ki-picture fs-2x text-muted mb-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <p class="text-muted mb-0">Preview akan muncul di sini</p>
            </div>
        `);
    });
});
</script>
@endpush