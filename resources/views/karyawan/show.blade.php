@extends('layouts.backend', ['title' => 'Riwayat'])

@inject('carbon', 'Carbon\Carbon')

@php
    $kd_status_kerja = $karyawan->kd_status_kerja;

    $gelar_depan = $karyawan->gelar_depan ? $karyawan->gelar_depan . " " : "";
    $gelar_belakang = $karyawan->gelar_belakang ? $karyawan->gelar_belakang : "";
    $nama = $karyawan->nama;
    $nama_lengkap = $gelar_depan . $nama . $gelar_belakang;

    // <?php echo $isi['ALAMAT'].", Kel. ".$isi['KELURAHAN'].", <br>Kec. ".$isi['KECAMATAN'].", Kab./Kota ".$isi['KABUPATEN'].", Prov. ".$isi['PROPINSI'];

    $alamat = $karyawan->alamat . ", Kel. " . $karyawan->kelurahan . ", Kec. " . $karyawan->kecamatan . ", Kab./Kota " . $karyawan->kabupaten . ", Prov. " . $karyawan->propinsi;

    // $photoSmallUrl = '';
    // if ($karyawan->foto_small) {
    //     $photoSmallUrl = Storage::url($karyawan->foto_small);
    // } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
    //     $photoSmallUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    // } else {
    //     $photoSmallUrl = Storage::url($karyawan->foto);
    // }

    // $photoUrl = '';
    // if ($karyawan->foto_square) {
    //     $photoUrl = Storage::url($karyawan->foto_square);
    // } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
    //     $photoUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    // }
    // $photoSmallUrl = '';
    // if ($karyawan->foto_small) {
    //     // $photoSmallUrl = url(str_replace('public', 'public/storage', $karyawan->foto_small));
    //     $photoSmallUrl = url(str_replace('public', 'storage', $karyawan->foto_small));
    // } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
    //     $photoSmallUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    // } else {
    //     // $photoSmallUrl = url(str_replace('public', 'public/storage', $karyawan->foto));
    //     $photoSmallUrl = url(str_replace('public', 'storage', $karyawan->foto));
    // }

    // $photoUrl = '';
    // if ($karyawan->foto_square) {
    //     // $photoUrl = url(str_replace('public', 'public/storage', $karyawan->foto_square));
    //     $photoUrl = url(str_replace('public', 'storage', $karyawan->foto_square));
    // } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
    //     $photoUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    // }

    $photoSmallUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto_small');
    $photoUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto_square');

    $salt = env('QR_SALT', 'this-is-secret-of-rsud-langsa-salt-2025');
    // $hashedId = md5($karyawan->kd_karyawan . $salt);
    $hashedId = md5($karyawan->kd_karyawan);
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <style>
        /* Reset internal */
        .id-card * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Kartu Pegawai */
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

        /* Foto Pegawai (di bawah background) */
        .photo-section .profile-photo {
            position: absolute;
            top: 75px; /* Sesuaikan dengan slot background */
            left: 50%;
            transform: translateX(-50%);
            width: 58px;
            height: 77px;
            object-fit: cover;
            border-radius: 5px;
            z-index: 1; /* Di bawah background */
        }

        /* Background Kartu (menutupi sebagian foto) */
        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('assets/media/idcard/background.png') }}') no-repeat center/cover;
            z-index: 2; /* Di atas foto */
        }

        /* Konten (logo, nama, QR, dsb) */
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

        /* Header Rumah Sakit */
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

        /* Logo */
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

        /* Info Pegawai */
        .id-card .info h3 {
            font-size: 8px;
            margin-top: 135px;
            color: black;
            font-weight: 800;
            text-transform: uppercase;
            font-family: 'Poppins', sans-serif;;
            text-decoration: underline;
        }

        .id-card .info p {
            font-size: 7px;
            color: black;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;;
        }

        /* QR Code */
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
            font-family: 'Poppins', sans-serif;;
        }

        /* Cropper */
        .crop-container {
            max-height: 400px;
            overflow: hidden;
        }
        
        .crop-preview {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .crop-preview-square {
            width: 100px;
            height: 100px;
        }
        
        .crop-preview-cv {
            width: 75px;
            height: 100px;
        }
        
        .crop-controls {
            margin: 15px 0;
        }
        
        .crop-controls .btn {
            margin: 2px;
        }
        
        .photo-type-tabs .nav-link {
            border-radius: 10px 10px 0 0;
        }
        
        .photo-type-tabs .nav-link.active {
            background-color: #1e40af;
            color: white;
            border-color: #1e40af;
        }

        .upload-hint {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 10px 0;
        }

        .upload-hint.dragover {
            border-color: #1e40af;
            background: #eff6ff;
        }
        /* End Cropper */

        /* ============================= */
        /* PERBESAR UNTUK TAMPILAN LAYAR */
        /* ============================= */
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

        /* ============================= */
        /* UKURAN CETAK (PRINT ASLI) */
        /* ============================= */
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
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2 ">
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Karyawan
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Riwayat
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content  flex-column-fluid ">
    <div id="kt_app_content_container" class="app-container  container-fluid ">
        <x-employee-header :karyawan="$karyawan" :missing-fields="$missing_fields" :persentase-kelengkapan="$persentase_kelengkapan" />

        <div class="row g-10 g-xl-10">
            <div class="col-md-8">
                <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
                    <div class="card-header cursor-pointer">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Detail Data Diri</h3>
                        </div>
        
                        <a href="#"
                            class="btn btn-sm btn-primary align-self-center">Edit Data Diri</a>
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
                            <label class="col-lg-3 fw-semibold text-muted">
                                Tempat, Tanggal lahir
                            </label>
        
                            <div class="col-lg-6 d-flex align-items-center">
                                <span class="fw-bold fs-6 text-gray-800 me-2">
                                    <!-- format indonesia 01 Februari 1998 -->
                                    {{ $karyawan->tempat_lahir }}, {{ $carbon::parse($karyawan->tgl_lahir)->locale('id')->isoFormat('LL') }}
                                </span>
                            </div>
                        </div>
        
                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Agama
                            </label>
        
                            <div class="col-lg-6 d-flex align-items-center">
                                <span class="fw-bold fs-6 text-gray-800 me-2">
                                    {{ $karyawan->agama }}
                                </span>
                            </div>
                        </div>

                        <!-- jika bukan PNS atau PPPK, maka tidak perlu menampilkan golongan dan pangkat, jika PNS maka tampikan Pangkat/Golongan, jika PPPK maka tampilkan Golongan saja -->
                        @if ($karyawan->kd_status_kerja == 1 || $karyawan->kd_status_kerja == 7)
                            @if ($karyawan->kd_status_kerja == 1)
                                <div class="row mb-7">
                                    <label class="col-lg-3 fw-semibold text-muted">
                                        Golongan/Pangkat
                                    </label>
                
                                    <div class="col-lg-6 d-flex align-items-center">
                                        <span class="fw-bold fs-6 text-gray-800 me-2">
                                            {{ $karyawan->golongan }} / {{ $karyawan->pangkat }}
                                        </span>
                                    </div>
                                </div>
                            @elseif ($karyawan->kd_status_kerja == 7)
                                <div class="row mb-7">
                                    <label class="col-lg-3 fw-semibold text-muted">
                                        Golongan/Pangkat
                                    </label>
                
                                    <div class="col-lg-6 d-flex align-items-center">
                                        <span class="fw-bold fs-6 text-gray-800 me-2">
                                            {{ $karyawan->pangkat }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Eselon
                            </label>
        
                            <div class="col-lg-6 d-flex align-items-center">
                                <span class="fw-bold fs-6 text-gray-800 me-2">
                                    {{ $karyawan->eselon }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Tempat Tugas
                            </label>
        
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
            
                    {{-- Foto Pegawai (di bawah background) --}}
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
                            {{-- {!! QrCode::size(60)->generate(url('/pegawai/' . $karyawan->kd_karyawan)) !!} --}}
                            {!! QrCode::size(40)->generate(url('show-personal/' . $hashedId)); !!}
                            <p>ID Peg. {{ $karyawan->kd_karyawan }}</p>
                        </div>
                    </div>
            
                </div>
            </div>
            
        </div>
    </div>

    <div class="modal fade" id="printIdCardModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title">Cetak ID Card</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form id="printIdCardForm" class="form">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Alasan Pembuatan ID Card</label>
                            <select class="form-select" name="alasan" id="alasan">
                                <option value="">Pilih Alasan</option>
                                <option value="1">Belum pernah buat</option>
                                <option value="2">Hilang</option>
                                <option value="3">Rusak</option>
                            </select>
                            <div class="invalid-feedback">
                                Alasan harus dipilih
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

    <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title">
                        <i class="fas fa-camera me-2"></i>Upload & Crop Foto Profil
                    </h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form id="uploadPhotoForm" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs photo-type-tabs mb-4" id="photoTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="square-tab" data-bs-toggle="tab" data-bs-target="#square-pane" type="button" role="tab">
                                    <i class="fas fa-user-circle me-1"></i>Foto Profil (1:1)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cv-tab" data-bs-toggle="tab" data-bs-target="#cv-pane" type="button" role="tab">
                                    <i class="fas fa-file-user me-1"></i>Foto CV (3:4)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="small-tab" data-bs-toggle="tab" data-bs-target="#small-pane" type="button" role="tab">
                                    <i class="fas fa-id-card me-1"></i>Foto ID Card
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
                                                <i class="fas fa-upload me-1"></i>Pilih Foto untuk Profil (Rasio 1:1)
                                            </label>
                                            <input type="file" class="form-control" id="foto_square_input" accept="image/jpeg,image/png,image/jpg">
                                            <div class="invalid-feedback" id="foto_square_error"></div>
                                            <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                                        </div>
                                        
                                        <div id="square_crop_container" style="display: none;">
                                            <div class="crop-container">
                                                <img id="square_crop_image" style="max-width: 100%;">
                                            </div>
                                            
                                            <div class="crop-controls">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.square.zoom(0.1)">
                                                    <i class="fas fa-search-plus"></i> Zoom In
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.square.zoom(-0.1)">
                                                    <i class="fas fa-search-minus"></i> Zoom Out
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.square.rotate(90)">
                                                    <i class="fas fa-redo"></i> Putar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="croppers.square.reset()">
                                                    <i class="fas fa-undo"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Preview:</label>
                                        <div class="crop-preview crop-preview-square" id="square_preview">
                                            <div class="upload-hint">
                                                <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">Preview akan muncul di sini</p>
                                            </div>
                                        </div>
                                        @if($karyawan->foto_square)
                                            <small class="text-muted">Foto saat ini:</small>
                                            <img src="{{ app('App\Http\Controllers\KaryawanController')->getPhotoUrl($karyawan, 'foto_square') }}" 
                                                class="img-thumbnail mt-1" style="max-width: 100px;">
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
                                                <i class="fas fa-upload me-1"></i>Pilih Foto untuk CV (Rasio 3:4)
                                            </label>
                                            <input type="file" class="form-control" id="foto_input" accept="image/jpeg,image/png,image/jpg">
                                            <div class="invalid-feedback" id="foto_error"></div>
                                            <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                                        </div>
                                        
                                        <div id="cv_crop_container" style="display: none;">
                                            <div class="crop-container">
                                                <img id="cv_crop_image" style="max-width: 100%;">
                                            </div>
                                            
                                            <div class="crop-controls">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.cv.zoom(0.1)">
                                                    <i class="fas fa-search-plus"></i> Zoom In
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.cv.zoom(-0.1)">
                                                    <i class="fas fa-search-minus"></i> Zoom Out
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.cv.rotate(90)">
                                                    <i class="fas fa-redo"></i> Putar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="croppers.cv.reset()">
                                                    <i class="fas fa-undo"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Preview:</label>
                                        <div class="crop-preview crop-preview-cv" id="cv_preview">
                                            <div class="upload-hint">
                                                <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">Preview akan muncul di sini</p>
                                            </div>
                                        </div>
                                        @if($karyawan->foto)
                                            <small class="text-muted">Foto saat ini:</small>
                                            <img src="{{ app('App\Http\Controllers\KaryawanController')->getPhotoUrl($karyawan, 'foto') }}" 
                                                class="img-thumbnail mt-1" style="max-width: 75px;">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Foto ID Card -->
                            <div class="tab-pane fade" id="small-pane" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-upload me-1"></i>Pilih Foto untuk ID Card (Rasio 3:4)
                                            </label>
                                            <input type="file" class="form-control" id="foto_small_input" accept="image/png,image/jpeg,image/jpg">
                                            <div class="invalid-feedback" id="foto_small_error"></div>
                                            <small class="text-muted">Format: PNG (untuk background transparan), JPG. Maksimal 2MB.</small>
                                        </div>
                                        
                                        <div id="small_crop_container" style="display: none;">
                                            <div class="crop-container">
                                                <img id="small_crop_image" style="max-width: 100%;">
                                            </div>
                                            
                                            <div class="crop-controls">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.small.zoom(0.1)">
                                                    <i class="fas fa-search-plus"></i> Zoom In
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.small.zoom(-0.1)">
                                                    <i class="fas fa-search-minus"></i> Zoom Out
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="croppers.small.rotate(90)">
                                                    <i class="fas fa-redo"></i> Putar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="croppers.small.reset()">
                                                    <i class="fas fa-undo"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Preview:</label>
                                        <div class="crop-preview crop-preview-cv" id="small_preview">
                                            <div class="upload-hint">
                                                <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">Preview akan muncul di sini</p>
                                            </div>
                                        </div>
                                        @if($karyawan->foto_small)
                                            <small class="text-muted">Foto saat ini:</small>
                                            <img src="{{ app('App\Http\Controllers\KaryawanController')->getPhotoUrl($karyawan, 'foto_small') }}" 
                                                class="img-thumbnail mt-1" style="max-width: 75px;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="uploadSubmitButton">
                            <i class="fas fa-upload me-1"></i>Upload Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Variabel untuk croppers
        let croppers = {
            square: null,
            cv: null,
            small: null
        };

        // Initialize cropper untuk setiap tipe foto
        function initializeCropper(inputId, imageId, containerId, previewId, type) {
            const input = document.getElementById(inputId);
            const image = document.getElementById(imageId);
            const container = document.getElementById(containerId);
            const preview = document.getElementById(previewId);
            
            input.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    
                    // Validasi ukuran file (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error('Ukuran file terlalu besar. Maksimal 2MB.');
                        input.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        // Destroy existing cropper
                        if (croppers[type]) {
                            croppers[type].destroy();
                        }
                        
                        // Set image source dan show container
                        image.src = event.target.result;
                        container.style.display = 'block';
                        
                        // Initialize cropper dengan aspect ratio yang sesuai
                        let aspectRatio;
                        switch(type) {
                            case 'square':
                                aspectRatio = 1; // 1:1
                                break;
                            case 'cv':
                            case 'small':
                                aspectRatio = 3/4; // 3:4
                                break;
                        }
                        
                        croppers[type] = new Cropper(image, {
                            aspectRatio: aspectRatio,
                            viewMode: 1,
                            autoCropArea: 0.8,
                            responsive: true,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            crop: function(event) {
                                updatePreview(type, preview);
                            }
                        });
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        }

        // Update preview
        function updatePreview(type, previewElement) {
            if (!croppers[type]) return;
            
            const canvas = croppers[type].getCroppedCanvas({
                width: type === 'square' ? 200 : 150,
                height: type === 'square' ? 200 : 200,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });
            
            previewElement.innerHTML = '';
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.style.objectFit = 'cover';
            previewElement.appendChild(canvas);
        }

        // Get cropped image sebagai blob
        function getCroppedImage(type, callback) {
            if (!croppers[type]) {
                callback(null);
                return;
            }
            
            const canvas = croppers[type].getCroppedCanvas({
                width: type === 'square' ? 400 : 300,
                height: type === 'square' ? 400 : 400,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });
            
            const format = type === 'small' ? 'image/png' : 'image/jpeg';
            const quality = type === 'small' ? 0.95 : 0.9;
            
            canvas.toBlob(callback, format, quality);
        }

        $(document).ready(function() {
            // Cek pesan error dari session
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            // Initialize semua croppers
            initializeCropper('foto_square_input', 'square_crop_image', 'square_crop_container', 'square_preview', 'square');
            initializeCropper('foto_input', 'cv_crop_image', 'cv_crop_container', 'cv_preview', 'cv');
            initializeCropper('foto_small_input', 'small_crop_image', 'small_crop_container', 'small_preview', 'small');
            
            let karyawanId = '{{ $karyawan->kd_karyawan }}';

            // Handle print ID card
            $('#printIdCardButton').on('click', function(e) {
                e.preventDefault();
                karyawanId = $(this).data('id');

                $.ajax({
                    url: '{{ route("admin.karyawan.generate-print-token") }}',
                    method: 'POST',
                    data: {
                        check_session: true,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.session_valid) {
                            let token = response.token;
                            let printUrl = '{{ url("admin/karyawan/identitas/print-id-card") }}' + '/' + karyawanId + '?token=' + token;
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

            // Handle print form submission
            $('#printIdCardForm').on('submit', function(e) {
                e.preventDefault();

                let alasan = $('#alasan').val();
                if (!alasan) {
                    $('#alasan').addClass('is-invalid');
                    toastr.error('Pilih alasan terlebih dahulu!');
                    return false;
                }

                $('#alasan').removeClass('is-invalid');
                $('#printSubmitButton').prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: '{{ route("admin.karyawan.generate-print-token") }}',
                    method: 'POST',
                    data: {
                        alasan: alasan,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            let token = response.token;
                            $('#printIdCardModal').modal('hide');
                            let printUrl = '{{ url("admin/karyawan/identitas/print-id-card") }}' + '/' + karyawanId + '?token=' + token;
                            window.open(printUrl, '_blank');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Terjadi kesalahan saat memproses permintaan.');
                    },
                    complete: function() {
                        $('#printSubmitButton').prop('disabled', false).text('Cetak');
                    }
                });
            });

            $('#alasan').on('change', function() {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                }
            });

            // Handle upload photo form submission
            $('#uploadPhotoForm').on('submit', function(e) {
                e.preventDefault();
                
                $('#uploadSubmitButton').prop('disabled', true).text('Memproses...');
                
                const formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                
                let processedCount = 0;
                const totalCroppers = 3;
                let hasImages = false;
                
                // Process setiap cropped image
                ['square', 'cv', 'small'].forEach((type, index) => {
                    const fieldName = type === 'square' ? 'foto_square' : (type === 'cv' ? 'foto' : 'foto_small');
                    
                    getCroppedImage(type, function(blob) {
                        if (blob) {
                            const extension = type === 'small' ? 'png' : 'jpg';
                            formData.append(fieldName, blob, `${fieldName}_${Date.now()}.${extension}`);
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
                    url: '{{ route("admin.karyawan.upload-photo", $karyawan->kd_karyawan) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#uploadPhotoModal').modal('hide');
                            
                            // Refresh halaman untuk menampilkan foto baru
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(field => {
                                $(`#${field}_error`).text(errors[field][0]);
                                $(`#${field.replace('_', '_')}_input`).addClass('is-invalid');
                            });
                        } else {
                            toastr.error('Terjadi kesalahan saat mengunggah foto.');
                        }
                    },
                    complete: function() {
                        $('#uploadSubmitButton').prop('disabled', false).text('Upload Foto');
                    }
                });
            }
            
            // Reset croppers ketika modal ditutup
            $('#uploadPhotoModal').on('hidden.bs.modal', function() {
                Object.keys(croppers).forEach(type => {
                    if (croppers[type]) {
                        croppers[type].destroy();
                        croppers[type] = null;
                    }
                });
                
                // Reset form dan hide containers
                $('#uploadPhotoForm')[0].reset();
                $('#square_crop_container, #cv_crop_container, #small_crop_container').hide();
                
                // Reset previews
                $('#square_preview, #cv_preview, #small_preview').html(`
                    <div class="upload-hint">
                        <i class="fas fa-image fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Preview akan muncul di sini</p>
                    </div>
                `);
                
                // Reset error states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            });

            // Clear error ketika user mengubah input
            $('input[type="file"]').on('change', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('');
            });
        });
    </script>
@endpush