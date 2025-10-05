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
    });
</script>
@endpush