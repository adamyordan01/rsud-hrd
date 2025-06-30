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

    $photoSmallUrl = '';
    if ($karyawan->foto_small) {
        $photoSmallUrl = Storage::url($karyawan->foto_small);
    } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
        $photoSmallUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    } else {
        $photoSmallUrl = Storage::url($karyawan->foto);
    }

    $photoUrl = '';
    if ($karyawan->foto_square) {
        $photoUrl = Storage::url($karyawan->foto_square);
    } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
        $photoUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    }
@endphp

@push('styles')
    <style>
        /* Reset khusus untuk elemen dalam id-card, supaya tidak ganggu global */
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
            /* background: url('background.png') no-repeat center/cover; */
            /* background berada di public/assets/media/idcard/ */
            background: url({{ asset('assets/media/idcard/background.png') }}) no-repeat center/cover;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 10px 5px;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        /* Header Rumah Sakit */
        .id-card .header {
            font-family: 'Poppins', sans-serif;
            font-size: 6px;
            color: white;
        }

        .id-card h2.company-name {
            font-size: 7.3px;
            color: #fff;
            font-weight: 600;
        }

        .id-card p.alamat {
            font-size: 4.4px;
            color: #fff;
            font-weight: 400;
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
            object-fit: contain;
        }

        /* Foto */
        .id-card .photo-section .profile-photo {
            width: 58px;
            height: 77px;
            object-fit: cover;
            border-radius: 5px;
            margin-top: 5px;
            margin-bottom: 60px;
        }

        /* Nama dan NIP */
        .id-card .info h3 {
            font-size: 8px;
            margin-top: -34px;
            color: black;
            font-weight: bold;
            text-transform: uppercase;
            font-family: 'Times New Roman', sans-serif;
            text-decoration: underline;
            text-decoration-thickness: 0.01rem;
            text-decoration-color: #000;
        }

        .id-card .info p {
            font-size: 7px;
            color: black;
            font-weight: bold;
            font-family: 'Times New Roman', sans-serif;
        }

        /* QR Code */
        .id-card .qr-section {
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .id-card .qr-code {
            width: 40px;
            height: 40px;
        }

        .qr-section svg {
            width: 40px;
            height: 40px;
            margin: 0 auto;
            display: block;
        }

        .id-card .qr-section p {
            font-size: 6px;
            color: black;
            font-weight: bold;
            font-family: 'Times New Roman', sans-serif;
        }

        /* ============================= */
        /* PERBESAR KHUSUS UNTUK TAMPILAN SCREEN (Layar) */
        /* ============================= */
        @media screen {
            .id-card {
                height: 350px; /* Memperbesar tinggi */
                width: 216px;  /* Memperbesar lebar */
                transform: scale(1.3); /* Sedikit scaling tambahan */
                transform-origin: top center;
            }

            .id-card h2.company-name {
                font-size: 10px;
            }

            .id-card p.alamat {
                font-size: 6px;
            }

            .id-card .logos img {
                width: 28px;
                height: 28px;
            }

            .id-card .photo-section .profile-photo {
                width: 80px;
                height: 106px;
                margin-top: 10px;
                margin-bottom: 70px;
            }

            .id-card .info h3 {
                font-size: 10px;
                margin-top: -30px;
            }

            .id-card .info p {
                font-size: 9px;
            }

            .id-card .qr-section p {
                font-size: 8px;
            }

            .qr-section svg {
                width: 50px;
                height: 50px;
            }
        }

        /* ============================= */
        /* KHUSUS UNTUK CETAK (PRINT), UKURAN ASLI */
        /* ============================= */
        @media print {
            body {
                background: none;
            }

            .id-card {
                height: 262px;
                width: 162px;
                box-shadow: none;
                transform: none;
            }

            .id-card h2.company-name {
                font-size: 7.3px;
            }

            .id-card p.alamat {
                font-size: 4.4px;
            }

            .id-card .logos img {
                width: 20px;
                height: 20px;
            }

            .id-card .photo-section .profile-photo {
                width: 58px;
                height: 77px;
                margin-top: 5px;
                margin-bottom: 60px;
            }

            .id-card .info h3 {
                font-size: 8px;
                margin-top: -34px;
            }

            .id-card .info p {
                font-size: 7px;
            }

            .id-card .qr-section p {
                font-size: 6px;
            }

            .qr-section svg {
                width: 40px;
                height: 40px;
            }
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
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">
                <!-- Alert untuk data yang belum terisi -->
                @if (!empty($missing_fields))
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-5">
                        <i class="ki-outline ki-information fs-2tx text-warning me-4 self-start"></i>
                        <div class="d-flex flex-column flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Perhatian!</h4>
                                <div class="fs-6 text-gray-700">
                                    Harap lengkapi data berikut: {{ implode(', ', $missing_fields) }}.
                                    Silakan <a class="fw-bold" href="#">perbarui profil Anda</a>.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                            {{-- <img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/{{ $karyawan->foto }}" alt="{{ $karyawan->kd_karyawan }}" /> --}}
                            <img src="{{ $photoUrl }}" alt="{{ $karyawan->kd_karyawan }}" />
                            <div
                                class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px">
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">
                                        {{ $nama_lengkap }}
                                    </a>
                                </div>

                                <div class="d-flex align-items-center fw-semibold mb-2">
                                    <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary">
                                        <i class="ki-duotone ki-dots-circle-vertical fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                            {{ $karyawan->kd_karyawan }}
                                    </a>
                                </div>

                                <div class="d-flex align-items-center fw-semibold mb-2">
                                    <a href="#"
                                        class="d-flex align-items-center text-gray-500 text-hover-primary">
                                        <i class="ki-duotone ki-profile-circle fs-4 me-1"><span class="path1"></span><span
                                                class="path2"></span><span class="path3"></span></i> {{ $karyawan->status_kerja }}
                                    </a>
                                </div>

                                <div class="d-flex align-items-center fw-semibold mb-2">
                                    <a href="#"
                                        class="d-flex align-items-center text-gray-500 text-hover-primary">
                                        <i class="ki-duotone ki-geolocation fs-4 me-1"><span class="path1"></span><span
                                                class="path2"></span></i>
                                        {{ $alamat }}
                                    </a>
                                </div>

                                <div class="d-flex align-items-center fw-semibold mb-2">
                                    <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary">
                                        <i class="ki-duotone ki-sms fs-4 me-1"><span class="path1"></span><span
                                                class="path2"></span></i> 
                                            {{ $karyawan->email ?? 'Belum diisi' }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-semibold fs-6 text-gray-500">Kelengkapan Data</span>
                                    <span class="fw-bold fs-6">{{ $persentase_kelengkapan }}%</span>
                                </div>

                                <div class="h-5px mx-3 w-100 bg-light mb-3">
                                    <div class="bg-success rounded h-5px" role="progressbar" style="width: {{ $persentase_kelengkapan }}%;"
                                        aria-valuenow="{{ $persentase_kelengkapan }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>

                        <!-- button cetak id card, upload foto, dan cetak kartu identitas rata kanan -->
                        <div class="d-flex flex-wrap justify-content-end mt-5">
                            <div class="d-flex align-items-center flex-column mt-3">
                                <a href="#" class="btn btn-sm btn-primary" id="printIdCardButton" data-id="{{ $karyawan->kd_karyawan }}">
                                    <i class="ki-duotone ki-printer fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Cetak ID Card
                                </a>
                            </div>

                            <div class="d-flex align-items-center flex-column ms-2 mt-3">
                                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal" data-id="{{ $karyawan->kd_karyawan }}">Upload Foto</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hover-scroll-x">
                    <ul class="nav flex-nowrap nav-stretch nav-line-tabs  border-transparent fs-5 fw-bold">
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 active"
                                href="{{ route('admin.karyawan.show', $karyawan->kd_karyawan) }}"
                            >
                                Data Pribadi
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 "
                                href="/metronic8/demo1/account/overview.html">
                                BPJS Ketenagakerjaan
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5"
                                href="/metronic8/demo1/account/settings.html">
                                Keluarga
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 "
                                href="/metronic8/demo1/account/security.html">
                                Bahasa
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 "
                                href="/metronic8/demo1/account/activity.html">
                                Pendidikan 
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 "
                                href="/metronic8/demo1/account/billing.html">
                                Riwayat Pekerjaan 
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 "
                                href="/metronic8/demo1/account/statements.html">
                                Riwayat Organisasi 
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 "
                                href="/metronic8/demo1/account/referrals.html">
                                Penghargaan 
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 "
                                href="/metronic8/demo1/account/api-keys.html">
                                Seminar 
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 " href="/metronic8/demo1/account/logs.html">
                                Tugas 
                            </a>
                        </li>
    
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 " href="/metronic8/demo1/account/logs.html">
                                STR 
                            </a>
                        </li>
    
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 " href="/metronic8/demo1/account/logs.html">
                                SIP 
                            </a>
                        </li>
    
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 " href="/metronic8/demo1/account/logs.html">
                                Cuti 
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

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
                            <label class="col-lg-3 fw-semibold text-muted">Company</label>
        
                            <div class="col-lg-6 fv-row">
                                <span class="fw-semibold text-gray-800 fs-6">Keenthemes</span>
                            </div>
                        </div>
        
                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Contact Phone
        
                                <span class="ms-1" data-bs-toggle="tooltip" title="Phone number must be active">
                                    <i class="ki-outline ki-information fs-7"></i> </span>
                            </label>
        
                            <div class="col-lg-6 d-flex align-items-center">
                                <span class="fw-bold fs-6 text-gray-800 me-2">044 3276 454 935</span>
                                <span class="badge badge-success">Verified</span>
                            </div>
                        </div>
        
                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">Company Site</label>
        
                            <div class="col-lg-6">
                                <a href="#" class="fw-semibold fs-6 text-gray-800 text-hover-primary">keenthemes.com</a>
                            </div>
                        </div>
        
                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Country
        
                                <span class="ms-1" data-bs-toggle="tooltip" title="Country of origination">
                                    <i class="ki-outline ki-information fs-7"></i> </span>
                            </label>
        
                            <div class="col-lg-6">
                                <span class="fw-bold fs-6 text-gray-800">Germany</span>
                            </div>
                        </div>
        
                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">Communication</label>
        
                            <div class="col-lg-6">
                                <span class="fw-bold fs-6 text-gray-800">Email, Phone</span>
                            </div>
                        </div>
        
                        <div class="row mb-10">
                            <label class="col-lg-3 fw-semibold text-muted">Allow Changes</label>
        
                            <div class="col-lg-6">
                                <span class="fw-semibold fs-6 text-gray-800">Yes</span>
                            </div>
                        </div>
        
        
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed  p-6">
                            <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
        
                            <div class="d-flex flex-stack flex-grow-1 ">
                                <div class=" fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">We need your attention!</h4>
        
                                    <div class="fs-6 text-gray-700 ">Your payment was declined. To start using tools,
                                        please <a class="fw-bold" href="/metronic8/demo39/account/billing.html">Add
                                            Payment Method</a>.</div>
                                </div>
        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="id-card ms-lg-10">
                    <div class="content">
                        <div class="header">
                            <div class="logos">
                                {{-- <img src="logo1.png" alt="Logo Kota Langsa"> --}}
                                <img src="{{ asset('assets/media/idcard/logo1.png') }}" alt="Logo Kota Langsa">
                                {{-- <img src="logo2.png" alt="Logo RSUD Langsa"> --}}
                                <img src="{{ asset('assets/media/idcard/logo2.png') }}" alt="Logo RSUD Langsa">
                            </div>
                            <h2 class="company-name">RUMAH SAKIT UMUM DAERAH LANGSA</h2>
                            <p class="alamat">Jln. Jend. Ahmad Yani No. 1 Kota Langsa</p>
                            <p class="alamat">Telp. Office / Fax (0641) 22051 - Telp. IGD (0641) 22800</p>
                            <p class="alamat">Email: rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id</p>
                            <p class="alamat">Website: rsud.langsakota.go.id</p>
                        </div>
            
                        <div class="photo-section">
                            {{-- <img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/{{ $karyawan->foto }}"
                                alt="Foto Pegawai" class="profile-photo"
                            > --}}
                            <!-- ambil dari photoSmallUrl -->
                            <img src="{{ $photoSmallUrl }}" alt="{{ $karyawan->nama }}" class="profile-photo">
                        </div>
            
                        <div class="info">
                            <h3>
                                {{ $nama_lengkap }}
                            </h3>

                            <!-- cek terlebih dahulu kd_status_kerja, jika 1 = NIP, jika 7 = PPPK selain itu tidak perlu menggunakan NIPPPK maupun NIP -->
                            @if ($karyawan->kd_status_kerja == 1)
                                <p>NIP: {{ $karyawan->nip_baru }}</p>
                            @elseif ($karyawan->kd_status_kerja == 7)
                                <p>NIPPPK: {{ $karyawan->nip_baru }}</p>
                            @endif
                        </div>
            
                        <div class="qr-section">
                            {{-- <img src="qrcode.png" alt="QR Code" class="qr-code"> --}}
                            {!! QrCode::size(60)->generate(url('/pegawai/' . $karyawan->kd_karyawan)) !!}
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title">Upload Foto Profil</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form id="uploadPhotoForm" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Foto Profil (1:1)</label>
                            <input type="file" class="form-control" name="foto_square" id="foto_square" accept="image/jpeg,image/png,image/jpg">
                            <div class="invalid-feedback" id="foto_square_error"></div>
                            <img id="foto_square_preview" src="{{ $karyawan->foto_square ? Storage::url($karyawan->foto_square) : '' }}" class="mt-2" style="max-width: 100px; display: {{ $karyawan->foto_square ? 'block' : 'none' }};">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto CV (3:4)</label>
                            <input type="file" class="form-control" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg">
                            <div class="invalid-feedback" id="foto_error"></div>
                            <img id="foto_preview" src="{{ $karyawan->foto ? Storage::url($karyawan->foto) : '' }}" class="mt-2" style="max-width: 100px; display: {{ $karyawan->foto ? 'block' : 'none' }};">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Kecil (PNG tanpa background)</label>
                            <input type="file" class="form-control" name="foto_small" id="foto_small" accept="image/png">
                            <div class="invalid-feedback" id="foto_small_error"></div>
                            <img id="foto_small_preview" src="{{ $karyawan->foto_small ? Storage::url($karyawan->foto_small) : '' }}" class="mt-2" style="max-width: 100px; display: {{ $karyawan->foto_small ? 'block' : 'none' }};">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="uploadSubmitButton">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // Cek pesan error dari session
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            let karyawanId;
            $('#printIdCardButton').on('click', function(e) {
                e.preventDefault();
                karyawanId = $(this).data('id');

                // Cek apakah ada session yang valid
                $.ajax({
                    url: '{{ route("admin.karyawan.generate-print-token") }}',
                    method: 'POST',
                    data: {
                        check_session: true, // Tambahan untuk cek session saja
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.session_valid) {
                            // Session ada dan valid, langsung buka print-id-card
                            let token = response.token;
                            let printUrl = '{{ url("admin/karyawan/identitas/print-id-card") }}' + '/' + karyawanId + '?token=' + token;
                            window.open(printUrl, '_blank');
                        } else {
                            // Tidak ada session atau kadaluarsa, tampilkan modal
                            $('#printIdCardModal').modal('show');
                        }
                    },
                    error: function() {
                        // Jika ada error, tampilkan modal sebagai fallback
                        $('#printIdCardModal').modal('show');
                    }
                });
            });

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

            // Preview gambar saat file dipilih
            function previewImage(input, previewId) {
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewId).attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#foto_square').on('change', function() {
                previewImage(this, '#foto_square_preview');
                $(this).removeClass('is-invalid');
                $('#foto_square_error').text('');
            });

            $('#foto').on('change', function() {
                previewImage(this, '#foto_preview');
                $(this).removeClass('is-invalid');
                $('#foto_error').text('');
            });

            $('#foto_small').on('change', function() {
                previewImage(this, '#foto_small_preview');
                $(this).removeClass('is-invalid');
                $('#foto_small_error').text('');
            });

            $('#uploadPhotoForm').on('submit', function(e) {
                e.preventDefault();
                // karyawanId = $('#uploadPhotoModal').data('id') || '{{ $karyawan->kd_karyawan }}';
                // Pastikan karyawanId sudah di-set
                if (!karyawanId) {
                    karyawanId = '{{ $karyawan->kd_karyawan }}'; // Fallback jika tidak di-set dari tombol
                }

                let formData = new FormData(this);
                $('#uploadSubmitButton').prop('disabled', true).text('Mengunggah...');

                $.ajax({
                    url: '{{ url("admin/karyawan/upload-photo") }}' + '/' + karyawanId, // Perbaikan: Gunakan base URL + karyawanId
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#uploadPhotoModal').modal('hide');

                            // Update preview dengan gambar yang baru diupload
                            if (response.photos.foto_square) {
                                $('#foto_square_preview').attr('src', response.photos.foto_square).show();
                            }
                            if (response.photos.foto) {
                                $('#foto_preview').attr('src', response.photos.foto).show();
                            }
                            if (response.photos.foto_small) {
                                $('#foto_small_preview').attr('src', response.photos.foto_small).show();
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.foto_square) {
                                $('#foto_square').addClass('is-invalid');
                                $('#foto_square_error').text(errors.foto_square[0]);
                            }
                            if (errors.foto) {
                                $('#foto').addClass('is-invalid');
                                $('#foto_error').text(errors.foto[0]);
                            }
                            if (errors.foto_small) {
                                $('#foto_small').addClass('is-invalid');
                                $('#foto_small_error').text(errors.foto_small[0]);
                            }
                        } else {
                            toastr.error('Terjadi kesalahan saat mengunggah foto.');
                        }
                    },
                    complete: function() {
                        $('#uploadSubmitButton').prop('disabled', false).text('Upload');
                    }
                });
            });
        });
    </script>
@endpush