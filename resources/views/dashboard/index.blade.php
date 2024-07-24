@extends('layouts.backend', ['title' => 'Dashboard'])

@inject('DB', 'Illuminate\Support\Facades\DB')

@php
    
@endphp

@push('styles')
    <style>

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
    $user = auth()->user();
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
                    <div class="card h-lg-100">
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
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card h-lg-100">
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
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card h-lg-100">
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
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card h-lg-100">
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
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card h-lg-100">
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
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card h-lg-100">
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
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card h-lg-100">
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
                    </div>
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
                    <div class="card h-lg-100">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection