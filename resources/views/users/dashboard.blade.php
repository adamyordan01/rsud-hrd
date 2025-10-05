@extends('layouts.user')

@section('title', 'Dashboard')

@push('styles')
<!--begin::Vendor Stylesheets(used for this page only)-->
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<!--end::Vendor Stylesheets-->

<style>
    .card-jenis-tenaga {
        transition: transform 0.2s;
        cursor: pointer;
        position: relative;
    }
    
    .card-jenis-tenaga:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px 0 rgba(0,0,0,.1);
    }

    /* Perbaikan untuk dropdown positioning */
    .dropdown-menu {
        width: 100% !important;
        max-height: 300px !important;
        overflow-y: auto !important;
    }

    .badge-belum-lengkap {
        background-color: #f1416c;
        color: white;
        cursor: pointer;
    }

    .badge-belum-lengkap:hover {
        background-color: #d9214e;
    }

    /* Style untuk widget summary seperti kode lama */
    .widget_summary {
        border-bottom: 1px solid #E4E7EA;
        padding: 10px 0;
        margin-bottom: 10px;
    }

    .widget_summary:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .w_left {
        float: left;
    }

    .w_center {
        float: left;
        text-align: center;
    }

    .w_right {
        float: right;
        text-align: right;
    }

    .w_25 {
        width: 25%;
    }

    .w_55 {
        width: 55%;
    }

    .w_20 {
        width: 20%;
    }

    .clearfix::after {
        content: "";
        display: table;
        clear: both;
    }

    .progress {
        height: 10px;
        margin: 5px 0;
        background-color: #f0f0f0;
        border-radius: 5px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background-color: #1bc5bd;
        transition: width 0.3s ease;
    }

    .bg-green {
        background-color: #1bc5bd !important;
    }

    /* Tile stats styling */
    .tile-stats {
        background: #fff;
        border: 1px solid #E4E7EA;
        border-radius: 8px;
        padding: 15px;
        position: relative;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 100px;
    }

    .tile-stats:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .tile-stats .icon {
        position: absolute;
        top: 15px;
        right: 15px;
        color: #009ef7;
    }

    .tile-stats .count {
        font-size: 18px;
        font-weight: bold;
        color: #181c32;
        margin-bottom: 10px;
    }

    .tile-stats h3 {
        margin: 3px 0;
        font-size: 12px;
        font-weight: 500;
    }

    /* Kedip animation */
    @keyframes kedip {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0.3; }
    }

    .kedip {
        animation: kedip 1s infinite;
    }

    /* Panel styling */
    .x_panel {
        background: #fff;
        border: 1px solid #E4E7EA;
        border-radius: 8px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .x_title {
        background: #f8f9fa;
        border-bottom: 1px solid #E4E7EA;
        padding: 15px 20px;
        position: relative;
    }

    .x_title h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #181c32;
    }

    .x_content {
        padding: 20px;
    }

    .navbar-right {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
    }

    /* Pastikan container memiliki overflow visible */
    .container-fluid {
        overflow: visible !important;
    }

    .row {
        overflow: visible !important;
    }

    .col-xl-3, .col-lg-6, .col-md-6, .col-sm-12 {
        overflow: visible !important;
    }
    
    /* Quick Actions Hover Effects */
    .btn.btn-light-primary:hover, 
    .btn.btn-light-success:hover,
    .btn.btn-light-warning:hover,
    .btn.btn-light-info:hover,
    .btn.btn-light-secondary:hover,
    .btn.btn-light-dark:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    /* Timeline Styling */
    .timeline {
        position: relative;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 3rem;
    }
    
    .timeline-line {
        position: absolute;
        left: 20px;
        top: 40px;
        bottom: -3rem;
        border-left: 1px dashed #E4E6EA;
    }
    
    .timeline-item:last-child .timeline-line {
        display: none;
    }
    
    /* Welcome Section Animation */
    .card.bg-primary {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
    }
    
    /* Progress Bar Animation */
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    
    /* Notice Cards */
    .notice {
        transition: all 0.3s ease;
    }
    
    .notice:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    /* Card Hover Effects */
    .card:hover {
        box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
</style>
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <!--begin::Toolbar wrapper-->
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Dashboard
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">
                                Home 
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Dashboard
                        </li>
                    </ul>
                </div>
                <!--end::Page title-->
            </div>
            <!--end::Toolbar wrapper-->
        </div>
        <!--end::Toolbar container-->
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <!--begin::Welcome Section-->
            <div class="row mb-8">
                <div class="col-xl-12">
                    <div class="card card-flush bg-primary">
                        <div class="card-body d-flex align-items-center py-8">
                            <div class="symbol symbol-60px symbol-circle me-5">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=ffffff&color=1e40af&size=128" 
                                     alt="{{ Auth::user()->name }}" class="rounded-circle" />
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-column">
                                    <h2 class="text-white fw-bold mb-1">Selamat Datang, {{ Auth::user()->name }}</h2>
                                    <div class="text-white opacity-75">
                                        <span class="me-3">
                                            <i class="ki-duotone ki-badge fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            NIP: {{ Auth::user()->nip ?? 'Belum diisi' }}
                                        </span>
                                        @if(Auth::user()->karyawan && Auth::user()->karyawan->ruangan)
                                        <span class="me-3">
                                            <i class="ki-duotone ki-home fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{ Auth::user()->karyawan->ruangan->nm_ruangan ?? 'Belum diisi' }}
                                        </span>
                                        @endif
                                        <span>
                                            <i class="ki-duotone ki-calendar fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            {{ date('d F Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('user.profil.index') }}" class="btn btn-light btn-sm">
                                    <i class="ki-duotone ki-pencil fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Update Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Welcome Section-->

            <!--begin::Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-sm-6 col-xl-2 mb-xl-10">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->
                            <div class="m-0">
                                <i class="ki-duotone ki-document fs-2hx text-gray-600">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ $kepegawaianData['total_sk'] ?? 0 }}</span>
                                <!--end::Number-->
                                <!--begin::Follower-->
                                <div class="m-0">
                                    <span class="fw-semibold fs-6 text-gray-500">SK Kontrak</span>
                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-sm-6 col-xl-2 mb-xl-10">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->
                            <div class="m-0">
                                <i class="ki-duotone ki-arrow-right fs-2hx text-gray-600">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ $kepegawaianData['mutasi_terbaru'] ? 1 : 0 }}</span>
                                <!--end::Number-->
                                <!--begin::Follower-->
                                <div class="m-0">
                                    <span class="fw-semibold fs-6 text-gray-500">Mutasi</span>
                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-sm-6 col-xl-2 mb-xl-10">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->
                            <div class="m-0">
                                <i class="ki-duotone ki-award fs-2hx text-gray-600">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ $sertifikasiData['str_aktif'] ?? 0 }}</span>
                                <!--end::Number-->
                                <!--begin::Follower-->
                                <div class="m-0">
                                    <span class="fw-semibold fs-6 text-gray-500">STR Aktif</span>
                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-sm-6 col-xl-2 mb-xl-10">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->
                            <div class="m-0">
                                <i class="ki-duotone ki-certificate fs-2hx text-gray-600">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ $sertifikasiData['sip_aktif'] ?? 0 }}</span>
                                <!--end::Number-->
                                <!--begin::Follower-->
                                <div class="m-0">
                                    <span class="fw-semibold fs-6 text-gray-500">SIP Aktif</span>
                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-sm-6 col-xl-2 mb-xl-10">
                    <!--begin::Card widget 2-->
                    <div class="card h-lg-100">
                        <!--begin::Body-->
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <!--begin::Icon-->
                            <div class="m-0">
                                <i class="ki-duotone ki-book fs-2hx text-gray-600">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                            <!--begin::Section-->
                            <div class="d-flex flex-column my-7">
                                <!--begin::Number-->
                                <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">{{ $sertifikasiData['seminar_tahun_ini'] ?? 0 }}</span>
                                <!--end::Number-->
                                <!--begin::Follower-->
                                <div class="m-0">
                                    <span class="fw-semibold fs-6 text-gray-500">Seminar</span>
                                </div>
                                <!--end::Follower-->
                            </div>
                            <!--end::Section-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card widget 2-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Quick Actions & Notifications Row-->
            <div class="row gy-5 g-xl-10 mb-8">
                <!--begin::Quick Actions-->
                <div class="col-xl-8">
                    <div class="card card-flush h-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Quick Actions</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Akses cepat ke fitur yang sering digunakan</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            <div class="row g-4">
                                <div class="col-sm-6 col-md-4">
                                    <a href="{{ route('user.profil.index') }}" class="btn btn-light-primary w-100 h-80px d-flex flex-column justify-content-center">
                                        <i class="ki-duotone ki-user fs-2x mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-semibold">Update Profil</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <a href="{{ route('user.sertifikasi.str') }}" class="btn btn-light-success w-100 h-80px d-flex flex-column justify-content-center">
                                        <i class="ki-duotone ki-award fs-2x mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <span class="fw-semibold">Upload STR</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <a href="{{ route('user.sertifikasi.sip') }}" class="btn btn-light-warning w-100 h-80px d-flex flex-column justify-content-center">
                                        <i class="ki-duotone ki-certificate fs-2x mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-semibold">Upload SIP</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <a href="{{ route('user.kepegawaian.mutasi') }}" class="btn btn-light-info w-100 h-80px d-flex flex-column justify-content-center">
                                        <i class="ki-duotone ki-arrow-right fs-2x mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-semibold">Lihat Mutasi</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <a href="{{ route('user.kepegawaian.sk-kontrak') }}" class="btn btn-light-secondary w-100 h-80px d-flex flex-column justify-content-center">
                                        <i class="ki-duotone ki-document fs-2x mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-semibold">SK Kontrak</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <a href="{{ route('user.sertifikasi.seminar') }}" class="btn btn-light-dark w-100 h-80px d-flex flex-column justify-content-center">
                                        <i class="ki-duotone ki-book fs-2x mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <span class="fw-semibold">Data Seminar</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Quick Actions-->

                <!--begin::Data Completeness-->
                <div class="col-xl-4">
                    <div class="card card-flush h-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Kelengkapan Data</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Status kelengkapan profil Anda</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            @php
                                $percentage = $personalData['completion_percentage'];
                                $missingFields = $personalData['missing_fields'];
                                $totalMissingFields = $personalData['total_missing_fields'];
                                $progressColor = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                            @endphp
                            
                            <div class="d-flex align-items-center mb-5">
                                <div class="progress h-8px w-100 me-3">
                                    <div class="progress-bar bg-{{ $progressColor }}" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="fw-bold fs-4">{{ $percentage }}%</span>
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ki-duotone ki-check-circle fs-1 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="text-gray-800 fw-semibold">
                                        @if($totalMissingFields > 0)
                                            {{ $totalMissingFields }} data perlu dilengkapi
                                        @else
                                            Semua data sudah lengkap
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            @if($percentage < 100)
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                                <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-6 text-gray-700">
                                            <strong>Data yang perlu dilengkapi:</strong><br>
                                            @foreach($missingFields as $field)
                                                • {{ $field }}<br>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4">
                                <i class="ki-duotone ki-check-circle fs-2tx text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-6 text-gray-700">
                                            <strong>Selamat!</strong><br>
                                            Data profil Anda sudah lengkap.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!--end::Data Completeness-->
            </div>
            <!--end::Quick Actions & Notifications Row-->

            <!--begin::Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-6 mb-5 mb-xl-10">
                    <!--begin::Table Widget 4-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Card header-->
                        <div class="card-header pt-7">
                            <!--begin::Title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">SK Kontrak Terbaru</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Data kontrak pegawai terbaru</span>
                            </h3>
                            <!--end::Title-->
                            <!--begin::Actions-->
                            <div class="card-toolbar">
                                <a href="{{ route('user.kepegawaian.sk-kontrak') }}" class="btn btn-sm btn-light">View All</a>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-2">
                            <!--begin::Table-->
                            <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="ps-0 min-w-200px">No. SK</th>
                                        <th class="min-w-100px">Tanggal SK</th>
                                        <th class="text-end min-w-100px pe-0">Status</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody>
                                    @if($kepegawaianData['sk_terbaru'])
                                    <tr>
                                        <td class="ps-0">
                                            <span class="text-gray-900 fw-bold d-block fs-6">
                                                {{ $kepegawaianData['sk_terbaru']->no_sk ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-gray-500 fw-semibold d-block fs-6">
                                                {{ $kepegawaianData['sk_terbaru']->tgl_sk ? date('d/m/Y', strtotime($kepegawaianData['sk_terbaru']->tgl_sk)) : '-' }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="badge badge-light-success">Aktif</span>
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="3" class="text-center text-gray-500">Tidak ada data</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Table Widget 4-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-6 mb-5 mb-xl-10">
                    <!--begin::Table Widget 5-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Card header-->
                        <div class="card-header pt-7">
                            <!--begin::Title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Sertifikat Terbaru</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">STR dan SIP yang akan expired</span>
                            </h3>
                            <!--end::Title-->
                            <!--begin::Actions-->
                            <div class="card-toolbar">
                                <a href="{{ route('user.sertifikasi.index') }}" class="btn btn-sm btn-light">View All</a>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-2">
                            <!--begin::Table-->
                            <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="ps-0 min-w-100px">Jenis</th>
                                        <th class="min-w-150px">No. Sertifikat</th>
                                        <th class="text-end min-w-100px pe-0">Expired</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody>
                                    @php
                                        $sertifikatNotifications = collect($notifications)->where('type', 'warning')->filter(function($n) { 
                                            return isset($n['jenis']) && in_array($n['jenis'], ['STR', 'SIP']); 
                                        });
                                    @endphp
                                    
                                    @if($sertifikatNotifications->count() > 0)
                                        @foreach($sertifikatNotifications->take(5) as $notif)
                                        <tr>
                                            <td class="ps-0">
                                                <span class="badge badge-light-primary">{{ $notif['jenis'] }}</span>
                                            </td>
                                            <td>
                                                <span class="text-gray-900 fw-semibold d-block fs-6">{{ $notif['no_sertifikat'] ?? '-' }}</span>
                                            </td>
                                            <td class="text-end pe-0">
                                                <span class="text-gray-500 fw-semibold d-block fs-6">
                                                    {{ date('d/m/Y', strtotime($notif['date'])) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="3" class="text-center text-gray-500">Tidak ada sertifikat yang akan expired</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Table Widget 5-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Recent Activities Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Recent Activities-->
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Aktivitas Terbaru</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Riwayat perubahan data terbaru</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            <div class="timeline">
                                <!--begin::Timeline item-->
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-document text-success fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="timeline-content ms-3">
                                        <div class="mb-1">
                                            <span class="fs-6 text-gray-800 fw-bold">Login ke Portal</span>
                                            <span class="fs-7 text-muted ms-2">{{ now()->diffForHumans() }}</span>
                                        </div>
                                        <div class="overflow-auto pb-5">
                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                Anda berhasil login ke portal HRD
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Timeline item-->

                                @if($kepegawaianData['sk_terbaru'])
                                <!--begin::Timeline item-->
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-document text-primary fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="timeline-content ms-3">
                                        <div class="mb-1">
                                            <span class="fs-6 text-gray-800 fw-bold">SK Kontrak Terbaru</span>
                                            <span class="fs-7 text-muted ms-2">
                                                {{ $kepegawaianData['sk_terbaru']->tgl_sk ? \Carbon\Carbon::parse($kepegawaianData['sk_terbaru']->tgl_sk)->diffForHumans() : '-' }}
                                            </span>
                                        </div>
                                        <div class="overflow-auto pb-5">
                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                SK No: {{ $kepegawaianData['sk_terbaru']->no_sk ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Timeline item-->
                                @endif

                                <!--begin::Timeline item-->
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-award text-warning fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="timeline-content ms-3">
                                        <div class="mb-1">
                                            <span class="fs-6 text-gray-800 fw-bold">Reminder Sertifikasi</span>
                                            <span class="fs-7 text-muted ms-2">Otomatis</span>
                                        </div>
                                        <div class="overflow-auto pb-5">
                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                Periksa secara berkala masa berlaku STR dan SIP Anda
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Timeline item-->
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Recent Activities-->

                <!--begin::Notifications & Alerts-->
                <div class="col-xl-6">
                    <div class="card card-flush h-xl-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Notifikasi & Pengingat</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Informasi penting untuk Anda</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            @php
                                $hasExpiringSertifikat = collect($notifications ?? [])->where('type', 'warning')->count() > 0;
                                $dataIncomplete = $percentage < 100;
                            @endphp

                            @if($hasExpiringSertifikat)
                            <!--begin::Notice-->
                            <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed mb-4 p-4">
                                <i class="ki-duotone ki-information-5 fs-2tx text-danger me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Sertifikat Akan Expired!</h4>
                                        <div class="fs-6 text-gray-700">
                                            Anda memiliki sertifikat yang akan segera expired. 
                                            <a href="{{ route('user.sertifikasi.str') }}" class="fw-bold">Lihat detail</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Notice-->
                            @endif

                            @if($dataIncomplete)
                            <!--begin::Notice-->
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-4 p-4">
                                <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Data Belum Lengkap</h4>
                                        <div class="fs-6 text-gray-700">
                                            Silakan lengkapi data profil Anda untuk mendapatkan layanan optimal.
                                            <a href="{{ route('user.profil.index') }}" class="fw-bold">Update sekarang</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Notice-->
                            @endif

                            <!--begin::Notice-->
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed mb-4 p-4">
                                <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Tips Penggunaan Portal</h4>
                                        <div class="fs-6 text-gray-700">
                                            Gunakan menu di sebelah kiri untuk mengakses berbagai fitur kepegawaian dan sertifikasi Anda.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Notice-->

                            <!--begin::Notice-->
                            <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4">
                                <i class="ki-duotone ki-check-circle fs-2tx text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Sistem Aktif</h4>
                                        <div class="fs-6 text-gray-700">
                                            Portal HRD berfungsi normal. Semua fitur dapat digunakan dengan lancar.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Notice-->
                        </div>
                    </div>
                </div>
                <!--end::Notifications & Alerts-->
            </div>
            <!--end::Recent Activities Row-->

        </div>
        <!--end::Content container-->
    </div>
</div>
@endsection

@push('scripts')
<!--begin::Vendors Javascript(used for this page only)-->
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Vendors Javascript-->

<script>
    // Fungsi untuk redirect yang aman
    function redirectToPage(url) {
        window.location.href = url;
    }

    // TAMBAHAN: Event handler untuk mencegah bubble pada dropdown
    $(document).ready(function() {
        // Mencegah event bubbling pada semua dropdown di card
        $('.card-jenis-tenaga .dropdown').on('click', function(e) {
            e.stopPropagation();
        });
        
        // Mencegah event bubbling khusus untuk dropdown button
        $('.card-jenis-tenaga .dropdown-toggle').on('click', function(e) {
            e.stopPropagation();
        });
        
        // Mencegah event bubbling untuk dropdown menu
        $('.card-jenis-tenaga .dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Animate progress bars on page load
        $('.progress-bar').each(function() {
            var progressWidth = $(this).css('width');
            $(this).css('width', '0%');
            $(this).animate({
                width: progressWidth
            }, 1500);
        });

        // Animate counter numbers
        $('.fs-3x').each(function() {
            var $this = $(this);
            var countTo = parseInt($this.text());
            
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'linear',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });

        // Add loading animation to quick action buttons
        $('.btn[href]').on('click', function() {
            var $btn = $(this);
            var originalText = $btn.html();
            
            $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Loading...');
            $btn.prop('disabled', true);
            
            // Restore button after navigation starts
            setTimeout(function() {
                $btn.html(originalText);
                $btn.prop('disabled', false);
            }, 1000);
        });

        // Timeline items fade in animation
        $('.timeline-item').each(function(index) {
            $(this).css('opacity', '0').delay(index * 200).animate({
                opacity: 1
            }, 500);
        });

        // Welcome card subtle animation
        $('.card.bg-primary').css('transform', 'scale(0.98)').animate({
            transform: 'scale(1)'
        }, 800);
    });
</script>
@endpush