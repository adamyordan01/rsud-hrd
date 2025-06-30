@extends('layouts.backend', ['title' => 'Riwayat'])

@inject('carbon', 'Carbon\Carbon')

@php
    $kd_status_kerja = $karyawan->kd_status_kerja;
    $gelar_depan = $karyawan->gelar_depan ? $karyawan->gelar_depan . " " : "";
    $gelar_belakang = $karyawan->gelar_belakang ? $karyawan->gelar_belakang : "";
    $nama = $karyawan->nama;
    $nama_lengkap = $gelar_depan . $nama . $gelar_belakang;
    $alamat = $karyawan->alamat . ", Kel. " . $karyawan->kelurahan . ", Kec. " . $karyawan->kecamatan . ", Kab./Kota " . $karyawan->kabupaten . ", Prov. " . $karyawan->propinsi;

    // Generate photo URLs
    $photoSmallUrl = '';
    if ($karyawan->foto_small) {
        $photoSmallUrl = url(str_replace('public', 'storage', $karyawan->foto_small));
    } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
        $photoSmallUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    } else {
        $photoSmallUrl = url(str_replace('public', 'storage', $karyawan->foto));
    }

    $photoUrl = '';
    if ($karyawan->foto_square) {
        $photoUrl = url(str_replace('public', 'storage', $karyawan->foto_square));
    } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
        $photoUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    }

    $salt = env('QR_SALT', 'this-is-secret-of-rsud-langsa-salt-2025');
    $hashedId = md5($karyawan->kd_karyawan);
@endphp

{{-- IMPORTANT: Add these meta tags to the head section --}}
@push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="karyawan-id" content="{{ $karyawan->kd_karyawan }}">
    
    {{-- Session messages as meta tags --}}
    @if (session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif
    
    @if (session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppr/2.3.0/croppr.min.css">
    <style>
        /* ID Card Styles */
        .id-card * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .id-card {
            height: 262px;
            width: 162px;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .photo-section .profile-photo {
            position: absolute;
            top: 75px;
            left: 50%;
            transform: translateX(-50%);
            width: 58px;
            height: 77px;
            object-fit: cover;
            border-radius: 5px;
            z-index: 1;
        }

        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('assets/media/idcard/background.png') }}') no-repeat center/cover;
            z-index: 2;
        }

        .content {
            position: relative;
            z-index: 3;
            padding: 10px 5px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        /* Cropper specific improvements */
        .crop-container {
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            position: relative;
            overflow: hidden;
            padding: 20px;
            text-align: center;
            min-height: 300px;
        }
        
        .crop-preview {
            max-width: 100%;
            max-height: 400px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: block;
        }
        
        .image-upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafafa;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .image-upload-area:hover {
            border-color: #007bff;
            background: #f0f8ff;
        }
        
        .image-upload-area.dragover {
            border-color: #007bff;
            background: #e3f2fd;
            transform: scale(1.02);
        }
        
        .crop-controls {
            margin-top: 15px;
            text-align: center;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
        }
        
        .crop-controls button {
            margin: 2px;
            font-size: 12px;
        }
        
        .croppr-container {
            margin: 20px 0;
            text-align: center;
            position: relative;
        }
        
        .croppr-container .croppr {
            max-width: 100%;
            max-height: 400px;
        }
        
        .tab-pane {
            min-height: 400px;
        }
        
        .image-info {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        
        .hidden {
            display: none !important;
        }
        
        .file-input {
            display: none;
        }
        
        .upload-icon {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }

        .invalid-feedback.show {
            display: block;
        }

        /* ID Card styling */
        .id-card .header {
            font-family: 'Poppins', sans-serif;
            font-size: 6px;
        }

        .id-card h2.company-name {
            font-size: 7.3px;
            font-weight: 600;
        }

        .id-card p.alamat {
            font-size: 4.4px;
            line-height: 1.3;
        }

        .id-card .logos {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 4px;
            margin-bottom: 6px;
        }

        .id-card .logos img {
            width: 20px;
            height: 20px;
        }

        .id-card .info h3 {
            font-size: 8px;
            margin-top: 135px;
            color: black;
            font-weight: 800;
            text-transform: uppercase;
            font-family: 'Poppins', sans-serif;
            text-decoration: underline;
        }

        .id-card .info p {
            font-size: 7px;
            color: black;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
        }

        .id-card .qr-section {
            margin: 0;
        }

        .qr-section svg {
            width: 45px;
            height: 45px;
            margin: 0 auto;
        }

        .id-card .qr-section p {
            font-size: 7px;
            color: black;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
        }

        /* Responsive improvements */
        @media screen {
            .id-card {
                height: 350px;
                width: 216px;
                transform: scale(1.3);
                transform-origin: top center;
            }

            .id-card h2.company-name { font-size: 10px; color: #fff; }
            .id-card p.alamat { font-size: 6px; }
            .id-card .logos img { width: 20px; height: 20px; object-fit: contain; }
            .photo-section .profile-photo { width: 80px; height: 106px; top: 110px; }
            .id-card .info h3 { font-size: 10px; }
            .id-card .info p { font-size: 9px; }
            .id-card .qr-section p { font-size: 8px; }
            .qr-section svg { width: 50px; height: 50px; }
        }

        @media print {
            .id-card {
                height: 262px;
                width: 162px;
                box-shadow: none;
                transform: none;
            }

            .id-card h2.company-name { font-size: 7.3px; }
            .id-card p.alamat { font-size: 4.4px; }
            .id-card .logos img { width: 20px; height: 20px; }
            .photo-section .profile-photo { width: 58px; height: 77px; top: 75px; }
            .id-card .info h3 { font-size: 8px; }
            .id-card .info p { font-size: 7px; }
            .id-card .qr-section p { font-size: 6px; }
            .qr-section svg { width: 40px; height: 40px; }
        }
    </style>
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Karyawan
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Riwayat</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <x-employee-header 
                :karyawan="$karyawan" 
                :missing-fields="$missing_fields" 
                :persentase-kelengkapan="$persentase_kelengkapan" 
            />

            <div class="row g-10 g-xl-10">
                <div class="col-md-8">
                    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
                        <div class="card-header cursor-pointer">
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Detail Data Diri</h3>
                            </div>
                            <a href="#" class="btn btn-sm btn-primary align-self-center">Edit Data Diri</a>
                        </div>

                        <div class="card-body p-9">
                            @if ($karyawan->kd_status_kerja == 1)
                                <div class="row mb-7">
                                    <label class="col-lg-3 fw-semibold text-muted">NIP</label>
                                    <div class="col-lg-6">
                                        <span class="fw-bold fs-6 text-gray-800">
                                            {{ $karyawan->nip_baru }}
                                        </span>
                                    </div>
                                </div>
                            @elseif ($karyawan->kd_status_kerja == 7)
                                <div class="row mb-7">
                                    <label class="col-lg-3 fw-semibold text-muted">NIPPPK</label>
                                    <div class="col-lg-6">
                                        <span class="fw-bold fs-6 text-gray-800">
                                            {{ $karyawan->nip_baru }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-semibold text-muted">Nama Lengkap</label>
                                <div class="col-lg-6">
                                    <span class="fw-bold fs-6 text-gray-800">
                                        {{ $nama_lengkap }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-semibold text-muted">Jenis kelamin</label>
                                <div class="col-lg-6 fv-row">
                                    <span class="fw-semibold text-gray-800 fs-6">
                                        {{ $karyawan->jenis_kelamin }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-semibold text-muted">Tempat, Tanggal lahir</label>
                                <div class="col-lg-6 d-flex align-items-center">
                                    <span class="fw-bold fs-6 text-gray-800 me-2">
                                        {{ $karyawan->tempat_lahir }}, {{ $carbon::parse($karyawan->tgl_lahir)->locale('id')->isoFormat('LL') }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-semibold text-muted">Agama</label>
                                <div class="col-lg-6 d-flex align-items-center">
                                    <span class="fw-bold fs-6 text-gray-800 me-2">
                                        {{ $karyawan->agama }}
                                    </span>
                                </div>
                            </div>

                            @if ($karyawan->kd_status_kerja == 1 || $karyawan->kd_status_kerja == 7)
                                @if ($karyawan->kd_status_kerja == 1)
                                    <div class="row mb-7">
                                        <label class="col-lg-3 fw-semibold text-muted">Golongan/Pangkat</label>
                                        <div class="col-lg-6 d-flex align-items-center">
                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                {{ $karyawan->golongan }} / {{ $karyawan->pangkat }}
                                            </span>
                                        </div>
                                    </div>
                                @elseif ($karyawan->kd_status_kerja == 7)
                                    <div class="row mb-7">
                                        <label class="col-lg-3 fw-semibold text-muted">Golongan/Pangkat</label>
                                        <div class="col-lg-6 d-flex align-items-center">
                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                {{ $karyawan->pangkat }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-semibold text-muted">Eselon</label>
                                <div class="col-lg-6 d-flex align-items-center">
                                    <span class="fw-bold fs-6 text-gray-800 me-2">
                                        {{ $karyawan->eselon }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-semibold text-muted">Tempat Tugas</label>
                                <div class="col-lg-6 d-flex align-items-center">
                                    <span class="fw-bold fs-6 text-gray-800 me-2">
                                        {{ $karyawan->ruangan }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="id-card ms-lg-10">
                        {{-- Foto Pegawai --}}
                        <div class="photo-section">
                            <img src="{{ $photoSmallUrl }}" alt="{{ $karyawan->nama }}" class="profile-photo">
                        </div>

                        {{-- Background di atas foto --}}
                        <div class="background-overlay"></div>

                        {{-- Konten lain --}}
                        <div class="content">
                            <div class="header">
                                <div class="logos">
                                    <img src="{{ asset('assets/media/idcard/logo1.png') }}" alt="Logo Kota Langsa">
                                    <img src="{{ asset('assets/media/idcard/logo2.png') }}" alt="Logo RSUD Langsa">
                                </div>
                                <h2 class="company-name">RUMAH SAKIT UMUM DAERAH LANGSA</h2>
                                <p class="alamat">Jln. Jend. Ahmad Yani No. 1 Kota Langsa</p>
                                <p class="alamat">Telp. Office / Fax (0641) 22051 - Telp. IGD (0641) 22800</p>
                                <p class="alamat">Email: rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id</p>
                                <p class="alamat">Website: rsud.langsakota.go.id</p>
                            </div>

                            <div class="info">
                                <h3>{{ $nama_lengkap }}</h3>
                                @if ($karyawan->kd_status_kerja == 1)
                                    <p>NIP: {{ $karyawan->nip_baru }}</p>
                                @elseif ($karyawan->kd_status_kerja == 7)
                                    <p>NIPPPK: {{ $karyawan->nip_baru }}</p>
                                @endif
                            </div>

                            <div class="qr-section">
                                {!! QrCode::size(40)->generate(url('show-personal/' . $hashedId)); !!}
                                <p>ID Peg. {{ $karyawan->kd_karyawan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal untuk Upload Photo --}}
        <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true" 
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold modal-title">Upload Foto Profil</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" 
                            data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    
                    <form id="uploadPhotoForm" class="form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            {{-- Tab Navigation --}}
                            <ul class="nav nav-tabs" id="photoTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="square-tab" data-bs-toggle="tab" 
                                            data-bs-target="#square-pane" type="button" role="tab">
                                        Foto Profil (1:1)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="cv-tab" data-bs-toggle="tab" 
                                            data-bs-target="#cv-pane" type="button" role="tab">
                                        Foto CV (3:4)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="small-tab" data-bs-toggle="tab" 
                                            data-bs-target="#small-pane" type="button" role="tab">
                                        Foto Kecil (PNG)
                                    </button>
                                </li>
                            </ul>

                            {{-- Tab Content --}}
                            <div class="tab-content mt-3" id="photoTabContent">
                                {{-- Foto Square (1:1) --}}
                                <div class="tab-pane fade show active" id="square-pane" role="tabpanel">
                                    <div class="crop-container">
                                        <input type="file" id="square-file" class="file-input" accept="image/*">
                                        
                                        <div id="square-upload-area" class="image-upload-area">
                                            <div class="upload-icon">📷</div>
                                            <h5>Foto Profil (1:1)</h5>
                                            <p class="mb-2">Klik atau drag & drop gambar di sini</p>
                                            <button type="button" class="btn btn-outline-primary btn-sm">Pilih Gambar</button>
                                            <div class="image-info">Format: JPG, PNG | Max: 2MB | Ratio: 1:1 (Square)</div>
                                        </div>

                                        <div id="square-croppr-container" class="croppr-container hidden">
                                            <img id="square-image" class="crop-preview" alt="Square Preview">
                                            <div class="crop-controls">
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="rotateCropper('square', -90)">↺ Putar Kiri</button>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="rotateCropper('square', 90)">↻ Putar Kanan</button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="resetCropper('square')">🔄 Reset</button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeCropper('square')">🗑️ Hapus</button>
                                            </div>
                                        </div>
                                        
                                        <div class="invalid-feedback" id="foto_square_error"></div>
                                    </div>
                                </div>

                                {{-- Foto CV (3:4) --}}
                                <div class="tab-pane fade" id="cv-pane" role="tabpanel">
                                    <div class="crop-container">
                                        <input type="file" id="cv-file" class="file-input" accept="image/*">
                                        
                                        <div id="cv-upload-area" class="image-upload-area">
                                            <div class="upload-icon">📄</div>
                                            <h5>Foto CV (3:4)</h5>
                                            <p class="mb-2">Klik atau drag & drop gambar di sini</p>
                                            <button type="button" class="btn btn-outline-primary btn-sm">Pilih Gambar</button>
                                            <div class="image-info">Format: JPG, PNG | Max: 2MB | Ratio: 3:4 (Portrait)</div>
                                        </div>

                                        <div id="cv-croppr-container" class="croppr-container hidden">
                                            <img id="cv-image" class="crop-preview" alt="CV Preview">
                                            <div class="crop-controls">
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="rotateCropper('cv', -90)">↺ Putar Kiri</button>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="rotateCropper('cv', 90)">↻ Putar Kanan</button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="resetCropper('cv')">🔄 Reset</button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeCropper('cv')">🗑️ Hapus</button>
                                            </div>
                                        </div>
                                        
                                        <div class="invalid-feedback" id="foto_error"></div>
                                    </div>
                                </div>

                                {{-- Foto Small (PNG) --}}
                                <div class="tab-pane fade" id="small-pane" role="tabpanel">
                                    <div class="crop-container">
                                        <input type="file" id="small-file" class="file-input" accept="image/png">
                                        
                                        <div id="small-upload-area" class="image-upload-area">
                                            <div class="upload-icon">🖼️</div>
                                            <h5>Foto Kecil (PNG)</h5>
                                            <p class="mb-2">Klik atau drag & drop gambar PNG di sini</p>
                                            <button type="button" class="btn btn-outline-primary btn-sm">Pilih Gambar PNG</button>
                                            <div class="image-info">Format: PNG only | Max: 2MB | Transparent background preferred</div>
                                        </div>

                                        <div id="small-croppr-container" class="croppr-container hidden">
                                            <img id="small-image" class="crop-preview" alt="Small Preview">
                                            <div class="crop-controls">
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="rotateCropper('small', -90)">↺ Putar Kiri</button>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="rotateCropper('small', 90)">↻ Putar Kanan</button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="resetCropper('small')">🔄 Reset</button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeCropper('small')">🗑️ Hapus</button>
                                            </div>
                                        </div>
                                        
                                        <div class="invalid-feedback" id="foto_small_error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary" id="printSubmitButton">Cetak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppr/2.3.0/croppr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add meta tags if not present
            if (!$('meta[name="csrf-token"]').length) {
                $('head').append('<meta name="csrf-token" content="{{ csrf_token() }}">');
            }
            if (!$('meta[name="karyawan-id"]').length) {
                $('head').append('<meta name="karyawan-id" content="{{ $karyawan->kd_karyawan }}">');
            }

            // Session messages
            @if (session('error'))
                console.log('Session error found');
                setTimeout(() => showToast('error', "{{ session('error') }}"), 500);
            @endif

            @if (session('success'))
                console.log('Session success found');
                setTimeout(() => showToast('success', "{{ session('success') }}"), 500);
            @endif

            // Ensure Croppr is loaded before initializing
            function waitForCroppr() {
                return new Promise((resolve) => {
                    function checkCroppr() {
                        if (typeof Croppr !== 'undefined') {
                            resolve();
                        } else {
                            setTimeout(checkCroppr, 100);
                        }
                    }
                    checkCroppr();
                });
            }

            // Initialize after Croppr is loaded
            waitForCroppr().then(() => {
                console.log('Croppr loaded successfully, initializing...');
                initializeApp();
            });
        });

        function initializeApp() {
            // Setup AJAX headers
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Global variables - use window object to ensure global access
            window.karyawanId = $('meta[name="karyawan-id"]').attr('content') || '{{ $karyawan->kd_karyawan }}';
            window.croppers = {
                square: null,
                cv: null,
                small: null
            };
            window.originalImages = {
                square: null,
                cv: null,
                small: null
            };

            // Initialize components
            initializeUploadHandlers();
            setupModalHandlers();
            setupToastr();
            setupExistingHandlers();

            console.log('App initialized with karyawan ID:', window.karyawanId);
        }

        function setupToastr() {
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut"
                };
            }
        }

        function initializeUploadHandlers() {
            const types = ['square', 'cv', 'small'];
            
            types.forEach(type => {
                console.log(`Initializing handlers for ${type}`);
                
                // Remove ALL existing handlers to prevent conflicts
                $(`#${type}-file`).off();
                $(`#${type}-upload-area`).off();
                $(`#${type}-upload-area button`).off();
                
                // File input change handler
                $(`#${type}-file`).on('change', function(e) {
                    console.log(`File input changed for ${type}`);
                    handleFileSelect(e, type);
                });

                // FIXED: Use event delegation for upload area click
                $(document).off(`click.upload-${type}`).on(`click.upload-${type}`, `#${type}-upload-area`, function(e) {
                    // Only trigger if clicking the upload area itself, not buttons inside
                    if (e.target === this || $(e.target).hasClass('upload-icon') || $(e.target).is('h5, p, .image-info')) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log(`Upload area clicked for ${type}`);
                        triggerFileInput(type);
                    }
                });

                // FIXED: Specific button click handler with event delegation
                $(document).off(`click.button-${type}`).on(`click.button-${type}`, `#${type}-upload-area button`, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log(`Button clicked for ${type}`);
                    triggerFileInput(type);
                });

                // Setup drag and drop
                setupDragAndDrop(type);
                
                console.log(`Handlers initialized for ${type}`);
            });
        }

        // FIXED: Separate function to trigger file input (more reliable)
        function triggerFileInput(type) {
            console.log(`Triggering file input for ${type}`);
            
            // Method 1: Direct click (most reliable)
            const fileInput = document.getElementById(`${type}-file`);
            if (fileInput) {
                // Reset the input value first
                fileInput.value = '';
                
                // Use setTimeout to ensure it's called from user gesture
                setTimeout(() => {
                    fileInput.click();
                }, 10);
            } else {
                console.error(`File input not found: ${type}-file`);
            }
        }

        function setupDragAndDrop(type) {
            const uploadArea = document.getElementById(`${type}-upload-area`);
            if (!uploadArea) {
                console.warn(`Upload area not found: ${type}-upload-area`);
                return;
            }
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Clean up existing listeners
            const events = ['dragenter', 'dragover', 'dragleave', 'drop'];
            events.forEach(eventName => {
                uploadArea.removeEventListener(eventName, preventDefaults);
            });

            // Add event listeners
            events.forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    $(uploadArea).addClass('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    $(uploadArea).removeClass('dragover');
                }, false);
            });

            uploadArea.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    console.log(`File dropped for ${type}:`, files[0].name);
                    handleFileSelect({ target: { files: files } }, type);
                }
            }, false);
        }

        function handleFileSelect(event, type) {
            const file = event.target.files[0];
            if (!file) {
                console.log('No file selected');
                return;
            }

            console.log(`File selected for ${type}:`, {
                name: file.name,
                type: file.type,
                size: file.size
            });

            // Clear previous errors
            clearErrors();

            // Validate file type
            if (type === 'small' && !file.type.includes('png')) {
                showToast('error', 'Foto kecil harus berformat PNG');
                return;
            }
            
            if (!file.type.startsWith('image/')) {
                showToast('error', 'File harus berupa gambar');
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                showToast('error', 'Ukuran file maksimal 2MB');
                return;
            }

            // Read file and display
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log(`File loaded for ${type}, displaying image...`);
                displayImage(e.target.result, type);
            };
            reader.onerror = function(e) {
                console.error('Error reading file:', e);
                showToast('error', 'Error membaca file');
            };
            reader.readAsDataURL(file);
        }

        function displayImage(imageSrc, type) {
            console.log(`Displaying image for ${type}`);
            
            // Store original image
            window.originalImages[type] = imageSrc;
            
            // Hide upload area and show cropper
            $(`#${type}-upload-area`).addClass('hidden');
            $(`#${type}-croppr-container`).removeClass('hidden');
            
            // Set image source
            const img = document.getElementById(`${type}-image`);
            if (!img) {
                console.error(`Image element not found: ${type}-image`);
                showToast('error', `Image element tidak ditemukan: ${type}`);
                return;
            }
            
            // Destroy existing cropper first
            if (window.croppers[type]) {
                try {
                    window.croppers[type].destroy();
                    window.croppers[type] = null;
                    console.log(`Existing cropper destroyed for ${type}`);
                } catch (error) {
                    console.warn(`Error destroying existing cropper for ${type}:`, error);
                }
            }
            
            // Set image source and wait for load
            img.onload = function() {
                console.log(`Image loaded for ${type}, initializing cropper...`);
                // Small delay to ensure DOM is ready
                setTimeout(() => {
                    initializeCropper(type);
                }, 150);
            };
            
            img.onerror = function() {
                console.error(`Error loading image for ${type}`);
                showToast('error', 'Error loading image');
            };
            
            img.src = imageSrc;
        }

        function initializeCropper(type) {
            const img = document.getElementById(`${type}-image`);
            if (!img) {
                console.error(`Image element not found: ${type}-image`);
                return;
            }
            
            // Check if image is loaded
            if (!img.complete || img.naturalWidth === 0) {
                console.warn(`Image not fully loaded for ${type}, waiting...`);
                setTimeout(() => initializeCropper(type), 100);
                return;
            }
            
            // Check if Croppr is available
            if (typeof Croppr === 'undefined') {
                console.error('Croppr is not loaded');
                showToast('error', 'Cropper library tidak dapat dimuat');
                return;
            }

            // Set aspect ratio based on type
            let aspectRatio;
            switch(type) {
                case 'square':
                    aspectRatio = 1; // 1:1
                    break;
                case 'cv':
                    aspectRatio = 3/4; // 3:4
                    break;
                case 'small':
                    aspectRatio = 1; // 1:1 for small images
                    break;
                default:
                    aspectRatio = 1;
            }

            // Initialize Croppr with error handling
            try {
                console.log(`Initializing Croppr for ${type} with aspect ratio ${aspectRatio}`);
                
                window.croppers[type] = new Croppr(img, {
                    aspectRatio: aspectRatio,
                    startSize: [80, 80, '%'],
                    minSize: [50, 50, 'px'],
                    maxSize: [800, 800, 'px'],
                    returnMode: 'real',
                    responsive: true
                });

                console.log(`Cropper successfully initialized for ${type}`);
                
            } catch (error) {
                console.error(`Error initializing cropper for ${type}:`, error);
                showToast('error', `Error initializing cropper: ${error.message}`);
                
                // Fallback: show image without cropper
                $(`#${type}-croppr-container .crop-controls`).hide();
            }
        }

        // Global functions for cropper controls
        function rotateCropper(type, degrees) {
            console.log(`Rotating cropper ${type} by ${degrees} degrees`);
            
            if (!window.croppers[type] || !window.originalImages[type]) {
                console.warn(`Cannot rotate: cropper or original image not found for ${type}`);
                return;
            }
            
            // Create canvas for rotation
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = function() {
                // Calculate new dimensions
                const isRotated90 = Math.abs(degrees) === 90;
                canvas.width = isRotated90 ? img.height : img.width;
                canvas.height = isRotated90 ? img.width : img.height;
                
                // Rotate and draw
                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate(degrees * Math.PI / 180);
                ctx.drawImage(img, -img.width / 2, -img.height / 2);
                
                // Update image source
                const rotatedSrc = canvas.toDataURL();
                window.originalImages[type] = rotatedSrc;
                displayImage(rotatedSrc, type);
            };
            
            img.src = window.originalImages[type];
        }

        function resetCropper(type) {
            console.log(`Resetting cropper for ${type}`);
            if (window.originalImages[type]) {
                displayImage(window.originalImages[type], type);
            }
        }

        function removeCropper(type) {
            console.log(`Removing cropper for ${type}`);
            
            // Destroy cropper
            if (window.croppers[type]) {
                try {
                    window.croppers[type].destroy();
                } catch (error) {
                    console.warn(`Error destroying cropper for ${type}:`, error);
                }
                window.croppers[type] = null;
            }
            
            // Clear original image
            window.originalImages[type] = null;
            
            // Show upload area and hide cropper
            $(`#${type}-croppr-container`).addClass('hidden');
            $(`#${type}-upload-area`).removeClass('hidden');
            
            // Clear file input
            $(`#${type}-file`).val('');
            
            // Clear errors
            const errorId = type === 'cv' ? 'foto_error' : `foto_${type}_error`;
            $(`#${errorId}`).text('').hide();
        }

        function setupModalHandlers() {
            // Remove existing handlers
            $('#uploadPhotoModal').off('.croppr');
            $('#photoTabs button[data-bs-toggle="tab"]').off('.croppr');
            $('#uploadPhotoForm').off('.croppr');

            // Modal show handler
            $('#uploadPhotoModal').on('show.bs.modal.croppr', function(e) {
                const triggerId = $(e.relatedTarget).data('id');
                window.karyawanId = triggerId || window.karyawanId || $('meta[name="karyawan-id"]').attr('content');
                
                console.log('Modal opened for karyawan:', window.karyawanId);
                
                // Reset all croppers when modal opens
                resetAllCroppers();
                
                // Re-initialize upload handlers after modal is shown
                setTimeout(() => {
                    initializeUploadHandlers();
                }, 200);
            });

            // Modal hide handler
            $('#uploadPhotoModal').on('hide.bs.modal.croppr', function(e) {
                console.log('Modal closing, cleaning up...');
                resetAllCroppers();
            });

            // Tab change handler
            $('#photoTabs button[data-bs-toggle="tab"]').on('shown.bs.tab.croppr', function(e) {
                clearErrors();
                console.log('Tab changed to:', $(e.target).attr('data-bs-target'));
                
                // Re-initialize handlers for current tab
                setTimeout(() => {
                    initializeUploadHandlers();
                }, 100);
            });

            // Form submit handler
            $('#uploadPhotoForm').on('submit.croppr', handleFormSubmit);
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            
            console.log('Form submitted');
            
            // Disable submit button
            const submitBtn = $('#uploadSubmitButton');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Memproses...');
            
            try {
                // Get cropped images
                const croppedImages = getCroppedImages();
                
                // Log what we got
                console.log('Cropped images:', {
                    foto_square: !!croppedImages.foto_square,
                    foto: !!croppedImages.foto,
                    foto_small: !!croppedImages.foto_small
                });
                
                // Validate that at least one image is provided
                if (!croppedImages.foto_square && !croppedImages.foto && !croppedImages.foto_small) {
                    showToast('warning', 'Pilih minimal satu foto untuk diupload');
                    submitBtn.prop('disabled', false).text(originalText);
                    return;
                }

                // Prepare form data
                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                // Add cropped images
                if (croppedImages.foto_square) {
                    formData.append('foto_square', croppedImages.foto_square, 'foto_square.jpg');
                    console.log('Added foto_square to form data');
                }
                if (croppedImages.foto) {
                    formData.append('foto', croppedImages.foto, 'foto.jpg');
                    console.log('Added foto to form data');
                }
                if (croppedImages.foto_small) {
                    formData.append('foto_small', croppedImages.foto_small, 'foto_small.png');
                    console.log('Added foto_small to form data');
                }

                // Submit form
                submitForm(formData);
                
            } catch (error) {
                console.error('Error in form submit:', error);
                showToast('error', 'Error memproses form: ' + error.message);
                submitBtn.prop('disabled', false).text(originalText);
            }
        }

        function getCroppedImages() {
            const images = {};
            
            try {
                // Get square image
                if (window.croppers.square && window.originalImages.square) {
                    console.log('Processing square image...');
                    const croppedData = window.croppers.square.getValue();
                    const canvas = getCroppedCanvas('square-image', croppedData);
                    if (canvas) {
                        images.foto_square = dataURLtoBlob(canvas.toDataURL('image/jpeg', 0.9));
                        console.log('Square image processed successfully');
                    }
                }
                
                // Get CV image
                if (window.croppers.cv && window.originalImages.cv) {
                    console.log('Processing CV image...');
                    const croppedData = window.croppers.cv.getValue();
                    const canvas = getCroppedCanvas('cv-image', croppedData);
                    if (canvas) {
                        images.foto = dataURLtoBlob(canvas.toDataURL('image/jpeg', 0.9));
                        console.log('CV image processed successfully');
                    }
                }
                
                // Get small image
                if (window.croppers.small && window.originalImages.small) {
                    console.log('Processing small image...');
                    const croppedData = window.croppers.small.getValue();
                    const canvas = getCroppedCanvas('small-image', croppedData);
                    if (canvas) {
                        images.foto_small = dataURLtoBlob(canvas.toDataURL('image/png'));
                        console.log('Small image processed successfully');
                    }
                }
            } catch (error) {
                console.error('Error getting cropped images:', error);
                showToast('error', 'Error processing images: ' + error.message);
            }
            
            return images;
        }

        function getCroppedCanvas(imageId, cropData) {
            try {
                const img = document.getElementById(imageId);
                if (!img) {
                    console.error(`Image not found: ${imageId}`);
                    return null;
                }
                
                console.log(`Creating cropped canvas for ${imageId}:`, cropData);
                
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                // Set canvas size to crop size
                canvas.width = Math.round(cropData.width);
                canvas.height = Math.round(cropData.height);
                
                // Draw cropped image
                ctx.drawImage(
                    img,
                    Math.round(cropData.x), 
                    Math.round(cropData.y), 
                    Math.round(cropData.width), 
                    Math.round(cropData.height),
                    0, 0, 
                    Math.round(cropData.width), 
                    Math.round(cropData.height)
                );
                
                return canvas;
            } catch (error) {
                console.error('Error creating cropped canvas:', error);
                return null;
            }
        }

        function dataURLtoBlob(dataURL) {
            try {
                const arr = dataURL.split(',');
                const mime = arr[0].match(/:(.*?);/)[1];
                const bstr = atob(arr[1]);
                let n = bstr.length;
                const u8arr = new Uint8Array(n);
                while (n--) {
                    u8arr[n] = bstr.charCodeAt(n);
                }
                return new Blob([u8arr], { type: mime });
            } catch (error) {
                console.error('Error converting dataURL to blob:', error);
                return null;
            }
        }

        function submitForm(formData) {
            const submitBtn = $('#uploadSubmitButton');
            submitBtn.prop('disabled', true).text('Mengunggah...');
            clearErrors();

            // Ensure karyawan ID is set
            if (!window.karyawanId) {
                window.karyawanId = $('meta[name="karyawan-id"]').attr('content');
            }
            
            if (!window.karyawanId) {
                showToast('error', 'ID Karyawan tidak ditemukan');
                submitBtn.prop('disabled', false).text('Upload');
                return;
            }
            
            const uploadUrl = `${window.location.origin}/admin/karyawan/upload-photo/${window.karyawanId}`;
            console.log('Upload URL:', uploadUrl);

            $.ajax({
                url: uploadUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 30000,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            submitBtn.text(`Mengunggah... ${percentComplete}%`);
                            console.log('Upload progress:', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    console.log('Upload success:', response);
                    
                    if (response.success) {
                        showToast('success', response.message || 'Foto berhasil diupload');
                        $('#uploadPhotoModal').modal('hide');
                        
                        // Reload page to show new photos
                        setTimeout(() => {
                            console.log('Reloading page...');
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast('error', response.message || 'Upload gagal');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Upload error:', { xhr, status, error });
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};
                        console.log('Validation errors:', errors);
                        
                        if (errors.foto_square) {
                            $('#foto_square_error').text(errors.foto_square[0]).show();
                        }
                        if (errors.foto) {
                            $('#foto_error').text(errors.foto[0]).show();
                        }
                        if (errors.foto_small) {
                            $('#foto_small_error').text(errors.foto_small[0]).show();
                        }
                        showToast('error', 'Periksa kembali file yang diupload');
                    } else if (xhr.status === 0) {
                        showToast('error', 'Koneksi terputus. Periksa koneksi internet Anda.');
                    } else if (status === 'timeout') {
                        showToast('error', 'Upload timeout. File terlalu besar atau koneksi lambat.');
                    } else {
                        showToast('error', `Terjadi kesalahan saat mengunggah foto (${xhr.status})`);
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('Upload');
                }
            });
        }

        function resetAllCroppers() {
            console.log('Resetting all croppers...');
            ['square', 'cv', 'small'].forEach(type => {
                removeCropper(type);
            });
        }

        function clearErrors() {
            $('.invalid-feedback').text('').hide();
            $('.is-invalid').removeClass('is-invalid');
        }

        function showToast(type, message) {
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            } else {
                console.log(`Toast ${type}: ${message}`);
                alert(`${type.toUpperCase()}: ${message}`);
            }
        }

        function setupExistingHandlers() {
            // Print ID Card handlers
            $('#printIdCardButton').off('.existing').on('click.existing', function(e) {
                e.preventDefault();
                window.karyawanId = $(this).data('id') || window.karyawanId;

                $.ajax({
                    url: '{{ route("admin.karyawan.generate-print-token") }}',
                    method: 'POST',
                    data: {
                        check_session: true,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.session_valid) {
                            let token = response.token;
                            let printUrl = `{{ url("admin/karyawan/identitas/print-id-card") }}/${window.karyawanId}?token=${token}`;
                            window.open(printUrl, '_blank');
                        } else {
                            $('#printIdCardModal').modal('show');
                        }
                    },
                    error: function() {
                        $('#printIdCardModal').modal('show');
                    }
                });
            });

            $('#printIdCardForm').off('.existing').on('submit.existing', function(e) {
                e.preventDefault();

                let alasan = $('#alasan').val();
                if (!alasan) {
                    $('#alasan').addClass('is-invalid');
                    showToast('error', 'Pilih alasan terlebih dahulu!');
                    return false;
                }

                $('#alasan').removeClass('is-invalid');
                $('#printSubmitButton').prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: '{{ route("admin.karyawan.generate-print-token") }}',
                    method: 'POST',
                    data: {
                        alasan: alasan,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            let token = response.token;
                            $('#printIdCardModal').modal('hide');
                            let printUrl = `{{ url("admin/karyawan/identitas/print-id-card") }}/${window.karyawanId}?token=${token}`;
                            window.open(printUrl, '_blank');
                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        showToast('error', 'Terjadi kesalahan saat memproses permintaan.');
                    },
                    complete: function() {
                        $('#printSubmitButton').prop('disabled', false).text('Cetak');
                    }
                });
            });

            $('#alasan').off('.existing').on('change.existing', function() {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                }
            });
        }

        // Make functions globally accessible for HTML onclick handlers
        window.rotateCropper = rotateCropper;
        window.resetCropper = resetCropper;
        window.removeCropper = removeCropper;

        // Debug functions
        window.debugCroppers = function() {
            console.log('=== Cropper Debug Info ===');
            console.log('Croppers status:', window.croppers);
            console.log('Original images:', window.originalImages);
            console.log('Croppr available:', typeof Croppr !== 'undefined');
            console.log('Current karyawan ID:', window.karyawanId);
            console.log('Upload handlers attached:');
            ['square', 'cv', 'small'].forEach(type => {
                const element = $(`#${type}-upload-area`)[0];
                const events = $._data(element, 'events');
                console.log(`${type}: ${events?.click?.length || 0} click handlers`);
            });
            console.log('========================');
        };

        window.resetCroppers = resetAllCroppers;

        // Force file dialog function for testing
        window.forceFileDialog = function(type) {
            console.log(`Forcing file dialog for ${type}`);
            document.getElementById(`${type}-file`).click();
        };
    </script>
@endpush