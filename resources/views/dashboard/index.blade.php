@extends('layouts.backend', ['title' => 'Dashboard'])

@inject('DB', 'Illuminate\Support\Facades\DB')

@php
    
@endphp

@push('styles')
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

        /* Pastikan container memiliki overflow visible
        .container-fluid {
            overflow: visible !important;
        }

        .row {
            overflow: visible !important;
        }

        .col-xl-3, .col-lg-6, .col-md-6, .col-sm-12 {
            overflow: visible !important;
        } */
    </style>
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2">

        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
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
                            <a href="#" class="text-muted text-hover-primary">
                                Dashboard 
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
            </div>
        </div>
    </div>
@endsection

@php
    $user = auth()->user()->karyawan;
    $gelar_depan = $user->gelar_depan ? $user->gelar_depan . ' ' : '';
    $gelar_belakang = $user->gelar_belakang ? '' . $user->gelar_belakang : '';
    $nama = $gelar_depan . $user->nama . $gelar_belakang;
@endphp

@section('content')
    <div id="kt_app_content" class="app-content  flex-column-fluid ">
        <div id="kt_app_content_container" class="app-container container-fluid ">
            <div class="text-gray-600 fs-4 fw-bold mb-10">
                Selamat Datang, {{ $nama }}
            </div>

            <div class="text-primary fs-3 fw-bold">
                Pegawai Aktif
            </div>

            <div class="separator separator-dashed my-4"></div>
            {{-- <div class="row gy-5 g-xl-5">
                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card h-lg-100">
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="m-0">
                                <i class="ki-duotone ki-user-square fs-2hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </div>
                            <div class="d-flex flex-column my-2">
                                <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">554 Org</span>
                                <div class="m-0">
                                    <span class="fw-semibold fs-6 text-gray-500">Pegawai PNS</span>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-between">
                                <span class="badge badge-light-success fs-base me-2">
                                    153 Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    401 Perempuan
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row gy-5 g-xl-5">
                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    {{-- https://rsud_hrd.me/admin/karyawan?status=1 --}}
                    <a href="{{ route('admin.karyawan.index', ['status' => 1]) }}" class="card h-lg-100">
                        {{-- <div class="card h-lg-100"> --}}
                            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                                <div class="d-flex justify-content-between w-100">
                                    <div class="d-flex flex-column my-2">
                                        <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['pns']->jumlah }} Org</span>
                                        <div class="m-0">
                                            <span class="fw-semibold fs-6 text-gray-500">Pegawai PNS</span>
                                        </div>
                                    </div>
                                    <div class="m-0">
                                        <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </div>
                                </div>
                                <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                                <div class="d-flex justify-content-start w-100 mt-2">
                                    <span class="badge badge-light-success fs-base me-2">
                                        {{ $pegawai['pns']->laki_laki }} Laki-laki
                                    </span>
                                    <span class="badge badge-light-danger fs-base">
                                        {{ $pegawai['pns']->perempuan }} Perempuan
                                    </span>
                                </div>
                            </div>
                        {{-- </div> --}}
                    </a>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    {{-- https://rsud_hrd.me/admin/karyawan?status=2 --}}
                    <a
                        href="{{ route('admin.karyawan.index', ['status' => 2]) }}"
                        class="card h-lg-100"
                    >
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['honor']->jumlah }} Org</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">Pegawai Honor</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-start w-100 mt-2">
                                <span class="badge badge-light-success fs-base me-2">
                                    {{ $pegawai['honor']->laki_laki }} Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    {{ $pegawai['honor']->perempuan }} Perempuan
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    {{-- https://rsud_hrd.me/admin/karyawan?status=3&jenis_pegawai=2 --}}
                    <a 
                        href="{{ route('admin.karyawan.index', ['status' => 3, 'jenis_pegawai' => 2]) }}"
                        class="card h-lg-100"
                    >
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['kontrak_blud']->jumlah }} Org</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">Peg. Kontrak BLUD</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-start w-100 mt-2">
                                <span class="badge badge-light-success fs-base me-2">
                                    {{ $pegawai['kontrak_blud']->laki_laki }} Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    {{ $pegawai['kontrak_blud']->perempuan }} Perempuan
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    {{-- https://rsud_hrd.me/admin/karyawan?status=3&jenis_pegawai=1 --}}
                    <a 
                        href="{{ route('admin.karyawan.index', ['status' => 3, 'jenis_pegawai' => 1]) }}"
                        class="card h-lg-100"
                    >
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['kontrak_pemko']->jumlah }} Org</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">Peg. Kontrak Pemko</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-start w-100 mt-2">
                                <span class="badge badge-light-success fs-base me-2">
                                    {{ $pegawai['kontrak_pemko']->laki_laki }} Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    {{ $pegawai['kontrak_pemko']->perempuan }} Perempuan
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    {{-- https://rsud_hrd.me/admin/karyawan?status=4 --}}
                    <a
                        href="{{ route('admin.karyawan.index', ['status' => 4]) }}"
                        class="card h-lg-100"
                    >
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['part_time']->jumlah }} Org</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">Pegawai Part Time</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-start w-100 mt-2">
                                <span class="badge badge-light-success fs-base me-2">
                                    {{ $pegawai['part_time']->laki_laki }} Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    {{ $pegawai['part_time']->perempuan }} Perempuan
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    {{-- https://rsud_hrd.me/admin/karyawan?status=6 --}}
                    <a
                        href="{{ route('admin.karyawan.index', ['status' => 6]) }}"
                        class="card h-lg-100"
                    >
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['thl']->jumlah }} Org</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">Pegawai THL</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-start w-100 mt-2">
                                <span class="badge badge-light-success fs-base me-2">
                                    {{ $pegawai['thl']->laki_laki }} Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    {{ $pegawai['thl']->perempuan }} Perempuan
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    {{-- https://rsud_hrd.me/admin/karyawan?status=7 --}}
                    <a
                        href="{{ route('admin.karyawan.index', ['status' => 7]) }}"
                        class="card h-lg-100"
                    >
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['pppk']->jumlah }} Org</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">PPPK</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-start w-100 mt-2">
                                <span class="badge badge-light-success fs-base me-2">
                                    {{ $pegawai['pppk']->laki_laki }} Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    {{ $pegawai['pppk']->perempuan }} Perempuan
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card h-lg-100">
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['luar']['jumlah'] }} Org</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">Total Peg. Luar RS</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-start w-100 mt-2">
                                <span class="badge badge-light-success fs-base me-2">
                                    {{ $pegawai['luar']['laki_laki'] }} Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    {{ $pegawai['luar']['perempuan'] }} Perempuan
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <a 
                        href="{{ route('admin.karyawan.index') }}"
                        class="card h-lg-100"
                    >
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">{{ $pegawai['total']->jumlah }} Org</span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-6 text-gray-500">Total Peg. RS</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user-square fs-5hx text-gray-600"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <!-- buat dua sisi untuk menampilkan jumlah laki laki dan perempuan, 153 Laki-laki    401 Perempuan -->
                            <div class="d-flex justify-content-start w-100 mt-2">
                                <span class="badge badge-light-success fs-base me-2">
                                    {{ $pegawai['total']->laki_laki }} Laki-laki
                                </span>
                                <span class="badge badge-light-danger fs-base">
                                    {{ $pegawai['total']->perempuan }} Perempuan
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            

            {{-- SECTION JENIS TENAGA --}}
            <div class="text-primary fs-3 fw-bold mb-2 mt-10">
                Jenis Tenaga
            </div>
            
            {{-- Alert untuk pegawai yang belum ada jenis tenaga --}}
            @if($jenisTenaga['belum_ada_jenis_tenaga']->jumlah > 0)
                <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1 text-danger">Belum Ada Jenis Tenaga {{ $jenisTenaga['belum_ada_jenis_tenaga']->jumlah }} Orang</h5>
                        <span class="fs-7">
                            {{ $jenisTenaga['belum_ada_jenis_tenaga']->pns }} PNS · 
                            {{ $jenisTenaga['belum_ada_jenis_tenaga']->honor }} Honor · 
                            {{ $jenisTenaga['belum_ada_jenis_tenaga']->kontrak }} Kontrak · 
                            {{ $jenisTenaga['belum_ada_jenis_tenaga']->partime }} PT · 
                            {{ $jenisTenaga['belum_ada_jenis_tenaga']->pppk }} PPPK |
                            {{ $jenisTenaga['belum_ada_jenis_tenaga']->laki }} Laki-laki / 
                            {{ $jenisTenaga['belum_ada_jenis_tenaga']->perem }} Perempuan
                        </span>
                    </div>
                </div>
            @endif

            <div class="separator separator-dashed my-4"></div>
            
            {{-- Cards Jenis Tenaga --}}
            <div class="row gy-5 g-xl-10 mb-10">
                {{-- Card Medis --}}
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-jenis-tenaga h-lg-100" onclick="redirectToPage('{{ route('admin.jenis-tenaga.index', 1) }}')">
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">
                                        {{ $jenisTenaga['medis']['data']->jumlah }} Org
                                    </span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-3 text-gray-500">Medis</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user fs-5hx text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            
                            <hr class="w-100 my-3">
                            
                            <div class="text-center w-100">
                                <p class="mb-2 fs-7">
                                    {{ $jenisTenaga['medis']['data']->pns }} PNS · 
                                    {{ $jenisTenaga['medis']['data']->honor }} Honor · 
                                    {{ $jenisTenaga['medis']['data']->kontrak }} Kontrak<br>
                                    {{ $jenisTenaga['medis']['data']->partime }} Part Time · 
                                    {{ $jenisTenaga['medis']['data']->thl }} THL · 
                                    {{ $jenisTenaga['medis']['data']->pppk }} PPPK
                                </p>
                            </div>

                            <div class="dropdown w-100">
                                <button class="btn btn-info w-100 dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Detail
                                </button>
                                <ul class="dropdown-menu w-100" onclick="event.stopPropagation();">
                                    @foreach($jenisTenaga['medis']['detail'] as $detail)
                                        <li>
                                            <a 
                                                href="{{ route('admin.jenis-tenaga.detail', [$detail->kd_detail, 1]) }}"
                                                class="dropdown-item"
                                            >
                                                <strong class="fs-7">{{ $detail->detail_jenis_tenaga }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $detail->statistik->pns }} PNS · 
                                                    {{ $detail->statistik->honor }} HONOR · 
                                                    {{ $detail->statistik->kontrak }} KONTRAK<br>
                                                    {{ $detail->statistik->partime }} PT · 
                                                    {{ $detail->statistik->thl }} THL · 
                                                    {{ $detail->statistik->pppk }} PPPK
                                                </small>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Perawat-Bidan --}}
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-jenis-tenaga h-lg-100" onclick="redirectToPage('{{ route('admin.jenis-tenaga.index', 2) }}')">
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">
                                        {{ $jenisTenaga['perawat_bidan']['data']->jumlah }} Org
                                    </span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-3 text-gray-500">Perawat-Bidan</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user fs-5hx text-warning">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            
                            <hr class="w-100 my-3">
                            
                            <div class="text-center w-100">
                                <p class="mb-2 fs-7">
                                    {{ $jenisTenaga['perawat_bidan']['data']->pns }} PNS · 
                                    {{ $jenisTenaga['perawat_bidan']['data']->honor }} Honor · 
                                    {{ $jenisTenaga['perawat_bidan']['data']->kontrak }} Kontrak<br>
                                    {{ $jenisTenaga['perawat_bidan']['data']->partime }} Part Time · 
                                    {{ $jenisTenaga['perawat_bidan']['data']->thl }} THL · 
                                    {{ $jenisTenaga['perawat_bidan']['data']->pppk }} PPPK
                                </p>
                            </div>
                            
                            <div class="dropdown w-100" onclick="event.stopPropagation();">
                                <button class="btn btn-info w-100 dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Detail
                                </button>
                                <ul class="dropdown-menu w-100">
                                    @foreach($jenisTenaga['perawat_bidan']['detail'] as $detail)
                                        <li>
                                            <a 
                                                href="{{ route('admin.jenis-tenaga.detail', [$detail->kd_detail, 2]) }}"
                                                class="dropdown-item
                                            ">
                                                <strong class="fs-7">{{ $detail->detail_jenis_tenaga }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $detail->statistik->pns }} PNS · 
                                                    {{ $detail->statistik->honor }} HONOR · 
                                                    {{ $detail->statistik->kontrak }} KONTRAK<br>
                                                    {{ $detail->statistik->partime }} PT · 
                                                    {{ $detail->statistik->thl }} THL · 
                                                    {{ $detail->statistik->pppk }} PPPK
                                                </small>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Penunjang Medis --}}
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-jenis-tenaga h-lg-100" onclick="redirectToPage('{{ route('admin.jenis-tenaga.index', 3) }}')">
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">
                                        {{ $jenisTenaga['penunjang_medis']['data']->jumlah }} Org
                                    </span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-3 text-gray-500">Penunjang Medis</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user fs-5hx text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            
                            <hr class="w-100 my-3">
                            
                            <div class="text-center w-100">
                                <p class="mb-2 fs-7">
                                    {{ $jenisTenaga['penunjang_medis']['data']->pns }} PNS · 
                                    {{ $jenisTenaga['penunjang_medis']['data']->honor }} Honor · 
                                    {{ $jenisTenaga['penunjang_medis']['data']->kontrak }} Kontrak<br>
                                    {{ $jenisTenaga['penunjang_medis']['data']->partime }} Part Time · 
                                    {{ $jenisTenaga['penunjang_medis']['data']->thl }} THL · 
                                    {{ $jenisTenaga['penunjang_medis']['data']->pppk }} PPPK
                                </p>
                            </div>
                            
                            <div class="dropdown w-100" onclick="event.stopPropagation();">
                                <button class="btn btn-info w-100 dropdown-toggle w-100" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Detail
                                </button>
                                <ul class="dropdown-menu w-100">
                                    @foreach($jenisTenaga['penunjang_medis']['detail'] as $detail)
                                        <li>
                                            <a href="" class="dropdown-item">
                                                <strong class="fs-7">{{ $detail->detail_jenis_tenaga }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $detail->statistik->pns }} PNS · 
                                                    {{ $detail->statistik->honor }} HONOR · 
                                                    {{ $detail->statistik->kontrak }} KONTRAK<br>
                                                    {{ $detail->statistik->partime }} PT · 
                                                    {{ $detail->statistik->thl }} THL · 
                                                    {{ $detail->statistik->pppk }} PPPK
                                                </small>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Non-Kesehatan --}}
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-jenis-tenaga h-lg-100" onclick="redirectToPage('{{ route('admin.jenis-tenaga.index', 4) }}')">
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex flex-column my-2">
                                    <span class="fw-semibold fs-2x text-gray-700 fw-bold lh-1 ls-n2 mb-2">
                                        {{ $jenisTenaga['non_kesehatan']['data']->jumlah }} Org
                                    </span>
                                    <div class="m-0">
                                        <span class="fw-semibold fs-3 text-gray-500">Non-Kesehatan</span>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <i class="ki-duotone ki-user fs-5hx text-danger">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            
                            <hr class="w-100 my-3">
                            
                            <div class="text-center w-100">
                                <p class="mb-2 fs-7">
                                    {{ $jenisTenaga['non_kesehatan']['data']->pns }} PNS · 
                                    {{ $jenisTenaga['non_kesehatan']['data']->honor }} Honor · 
                                    {{ $jenisTenaga['non_kesehatan']['data']->kontrak }} Kontrak<br>
                                    {{ $jenisTenaga['non_kesehatan']['data']->partime }} Part Time · 
                                    {{ $jenisTenaga['non_kesehatan']['data']->thl }} THL · 
                                    {{ $jenisTenaga['non_kesehatan']['data']->pppk }} PPPK
                                </p>
                            </div>
                            
                            <!-- PERBAIKAN: Tambahkan div wrapper dengan event handler -->
                            <div class="dropdown w-100" onclick="event.stopPropagation();">
                                <button class="btn btn-info dropdown-toggle w-100" type="button" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    Detail
                                </button>
                                <ul class="dropdown-menu w-100">
                                    @foreach($jenisTenaga['non_kesehatan']['detail'] as $detail)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.jenis-tenaga.detail', [$detail->kd_detail, 4]) }}">
                                            <strong class="fs-7">{{ $detail->detail_jenis_tenaga }}</strong><br>
                                            <small class="text-muted">
                                                {{ $detail->statistik->pns }} PNS · 
                                                {{ $detail->statistik->honor }} HONOR · 
                                                {{ $detail->statistik->kontrak }} KONTRAK<br>
                                                {{ $detail->statistik->partime }} PT · 
                                                {{ $detail->statistik->thl }} THL · 
                                                {{ $detail->statistik->pppk }} PPPK
                                            </small>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="separator separator-dashed my-4"></div>

            {{-- 3 Kolom Layout seperti kode lama --}}
            <div class="row">
                {{-- Kolom 1: Jenjang Pendidikan --}}
                <div class="col-md-4 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Jenjang Pendidikan</h2>
                            <span class="navbar-right">
                                <button class="btn btn-success btn-sm" onclick="window.location.href='{{ route('admin.karyawan-jenjang-pendidikan.index') }}'">
                                    <i class="ki-duotone ki-eye fs-4"></i> Lihat
                                </button>
                            </span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            @foreach($dashboardData['jenjang_pendidikan'] as $jenjang)
                                @php
                                    $kdDidik = $jenjang->kd_pendidikan_terakhir;
                                    
                                    // Hitung jumlah per jenjang
                                    $jumlahJenjang = DB::table('hrd_karyawan')
                                        ->where('kd_pendidikan_terakhir', $kdDidik)
                                        ->where('status_peg', 1)
                                        ->count();
                                    
                                    // Hitung per status kerja
                                    $pns = DB::table('hrd_karyawan')
                                        ->where('kd_pendidikan_terakhir', $kdDidik)
                                        ->where('kd_status_kerja', 1)
                                        ->where('status_peg', 1)
                                        ->count();
                                    
                                    $honor = DB::table('hrd_karyawan')
                                        ->where('kd_pendidikan_terakhir', $kdDidik)
                                        ->where('kd_status_kerja', 2)
                                        ->where('status_peg', 1)
                                        ->count();
                                    
                                    $kontrak = DB::table('hrd_karyawan')
                                        ->where('kd_pendidikan_terakhir', $kdDidik)
                                        ->where('kd_status_kerja', 3)
                                        ->where('status_peg', 1)
                                        ->count();
                                    
                                    $pt = DB::table('hrd_karyawan')
                                        ->where('kd_pendidikan_terakhir', $kdDidik)
                                        ->where('kd_status_kerja', 4)
                                        ->where('status_peg', 1)
                                        ->count();
                                    
                                    $pppk = DB::table('hrd_karyawan')
                                        ->where('kd_pendidikan_terakhir', $kdDidik)
                                        ->where('kd_status_kerja', 7)
                                        ->where('status_peg', 1)
                                        ->count();
                                    
                                    $persentase = $dashboardData['total_pegawai_aktif'] > 0 ? ($jumlahJenjang / $dashboardData['total_pegawai_aktif']) * 100 : 0;
                                @endphp
                                
                                <div class="widget_summary">
                                    <div class="w_left w_25">
                                        <br><span class="fw-semibold">{{ $jenjang->jenjang_didik }}</span>
                                    </div>
                                    <div class="w_center w_55">
                                        <span style="font-size: 8pt;">{{ $pns }} PNS · {{ $honor }} Honor · {{ $kontrak }} Kontrak · {{ $pt }} PT · {{ $pppk }} PPPK</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-green" role="progressbar" style="width: {{ $persentase }}%;"></div>
                                        </div>
                                    </div>
                                    <div class="w_right w_20">
                                        <br><span style="font-size: 10pt;" class="fw-bold">{{ $jumlahJenjang }} Org</span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom 2: Pangkat/Golongan --}}
                <div class="col-md-4 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Pangkat/Golongan (PNS & PPPK)</h2>
                            <span class="navbar-right">
                                <button class="btn btn-success btn-sm" onclick="window.location.href='{{ route('admin.karyawan-golongan.index') }}'">
                                    <i class="ki-duotone ki-eye fs-4"></i> Lihat
                                </button>
                            </span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            @foreach($dashboardData['pangkat_golongan'] as $pangkat)
                                @php
                                    $persentasePangkat = $dashboardData['total_pns_pppk'] > 0 ? ($pangkat->jumlah / $dashboardData['total_pns_pppk']) * 100 : 0;
                                @endphp
                                
                                <div class="widget_summary">
                                    <div class="w_left w_25">
                                        <span class="fw-semibold">{{ $pangkat->kd_gol ?: 'CPNS' }}</span>
                                    </div>
                                    <div class="w_center w_55">
                                        <div class="progress">
                                            <div class="progress-bar bg-green" role="progressbar" style="width: {{ $persentasePangkat }}%;"></div>
                                        </div>
                                    </div>
                                    <div class="w_right w_20">
                                        <span style="font-size: 10pt;" class="fw-bold">{{ $pangkat->jumlah }} Org</span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom 3: Data Pegawai --}}
                <div class="col-md-4 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Data Pegawai</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                {{-- Belum Lengkap --}}
                                <div class="col-12">
                                    <div class="tile-stats" onclick="window.location.href='#'">
                                        <div class="icon">
                                            <i class="ki-duotone ki-minus-square fs-2x text-danger"></i>
                                        </div>
                                        <div class="count kedip text-danger">Belum Lengkap</div>
                                        
                                        <h3 class="text-danger">Jenis Tenaga <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->jenistenaga }}</b> Org</h3>
                                        <h3 class="text-danger">No. KTP <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->ktp }}</b> Org</h3>
                                        <h3 class="text-danger">No. Rek BSI <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->bni_syariah }}</b> Org</h3>
                                        <h3 class="text-danger">No. Rek BPD Aceh <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->bpd }}</b> Org</h3>
                                        <h3 class="text-danger">Jurusan <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->jurusan }}</b> Org</h3>
                                        <h3 class="text-danger">Email <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->email }}</b> Org</h3>
                                        <h3 class="text-danger">Alamat <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->alamat }}</b> Org</h3>
                                        <h3 class="text-danger">No. STR <b>{{ $dashboardData['data_pegawai']['no_str'] }}</b> Org</h3>
                                        <h3 class="text-danger">No. SIP <b>{{ $dashboardData['data_pegawai']['no_sip'] }}</b> Org</h3>
                                        <h3 class="text-danger">T/Tgl. Lahir <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->ttl }}</b> Org</h3>
                                        <h3 class="text-danger">KARPEG <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->nokarpeg }}</b> Org</h3>
                                        <h3 class="text-danger">NPWP <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->npwp }}</b> Org</h3>
                                        <h3 class="text-danger">ASKES/BPJS Kesehatan <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->noaskes }}</b> Org</h3>
                                        <h3 class="text-danger">BPJS Ketenagakerjaan <b>{{ $dashboardData['data_pegawai']['belum_lengkap']->bpjs_ketenagakerjaan }}</b> Org</h3>
                                    </div>
                                </div>

                                {{-- Sudah Mutasi --}}
                                <div class="col-12">
                                    <div class="tile-stats" onclick="window.location.href='#'">
                                        <div class="icon">
                                            <i class="ki-duotone ki-arrow-right-left fs-2x text-primary"></i>
                                        </div>
                                        <div class="count">Sudah Mutasi</div>
                                        <h3><b>{{ $dashboardData['data_pegawai']['mutasi_tahun_ini'] }}</b> Orang</h3>
                                        <h3><b>Tahun</b> {{ date('Y') }}</h3>
                                    </div>
                                </div>

                                {{-- Per-Ruangan --}}
                                <div class="col-12">
                                    <div class="tile-stats" onclick="window.location.href='{{ route('admin.karyawan-ruangan.index') }}'">
                                        <div class="icon">
                                            <i class="ki-duotone ki-office-bag fs-2x text-success"></i>
                                        </div>
                                        <div class="count">Per-Ruangan</div>
                                        <h3><b>{{ $dashboardData['data_pegawai']['total_ruangan'] }}</b> Ruangan Kerja</h3>
                                    </div>
                                </div>
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
        });
    </script>
@endpush