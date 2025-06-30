@extends('layouts.backend', ['title' => 'Dashboard Jenis Tenaga'])

@push('styles')
    <style>
        .card-jenis-tenaga {
            transition: transform 0.2s;
            cursor: pointer;
        }
        
        .card-jenis-tenaga:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 25px 0 rgba(0,0,0,.1);
        }

        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }

        .badge-belum-lengkap {
            background-color: #f1416c;
            color: white;
            cursor: pointer;
        }

        .badge-belum-lengkap:hover {
            background-color: #d9214e;
        }
    </style>    
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Jenis Tenaga
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
                        <li class="breadcrumb-item text-muted">
                            Jenis Tenaga
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            {{-- Alert untuk pegawai yang belum ada jenis tenaga --}}
            @if($jenisTenaga['belum_ada_jenis_tenaga']->jumlah > 0)
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">Belum Ada Jenis Tenaga {{ $jenisTenaga['belum_ada_jenis_tenaga']->jumlah }} Orang</h4>
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

            <div class="row gy-5 g-xl-10">
                {{-- Card Medis --}}
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card card-jenis-tenaga h-lg-100" onclick="window.location='{{ route('admin.jenis-tenaga.index', 1) }}'">
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
                                <button class="btn btn-info dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                    Detail <i class="ki-duotone ki-down fs-3"></i>
                                </button>
                                <ul class="dropdown-menu w-100">
                                    @foreach($jenisTenaga['medis']['detail'] as $detail)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.jenis-tenaga.detail', [$detail->kd_detail, 1]) }}">
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
                    <div class="card card-jenis-tenaga h-lg-100" onclick="window.location='{{ route('admin.jenis-tenaga.index', 2) }}'">
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
                            
                            <div class="dropdown w-100">
                                <button class="btn btn-info dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                    Detail <i class="ki-duotone ki-down fs-3"></i>
                                </button>
                                <ul class="dropdown-menu w-100">
                                    @foreach($jenisTenaga['perawat_bidan']['detail'] as $detail)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.jenis-tenaga.detail', [$detail->kd_detail, 2]) }}">
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
                    <div class="card card-jenis-tenaga h-lg-100" onclick="window.location='{{ route('admin.jenis-tenaga.index', 3) }}'">
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
                            
                            <div class="dropdown w-100">
                                <button class="btn btn-info dropdown-toggle w-100" type="type" data-bs-toggle="dropdown">
                                    Detail <i class="ki-duotone ki-down fs-3"></i>
                                </button>
                                <ul class="dropdown-menu w-100">
                                    @foreach($jenisTenaga['penunjang_medis']['detail'] as $detail)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.jenis-tenaga.detail', [$detail->kd_detail, 3]) }}">
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
                    <div class="card card-jenis-tenaga h-lg-100" onclick="window.location='{{ route('admin.jenis-tenaga.index', 4) }}'">
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
                            
                            <div class="dropdown w-100">
                                <button class="btn btn-info dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                    Detail <i class="ki-duotone ki-down fs-3"></i>
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
        </div>
    </div>
@endsection