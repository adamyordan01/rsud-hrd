@extends('layouts.backend', ['title' => 'Riwayat'])

@inject('carbon', 'Carbon\Carbon')

@php
    $kd_status_kerja = $karyawan->kd_status_kerja;

    $gelar_depan = $karyawan->gelar_depan ? $karyawan->gelar_depan . " " : "";
    $gelar_belakang = $karyawan->gelar_belakang ? $karyawan->gelar_belakang : "";
    $nama = $karyawan->nama;
    $nama_lengkap = $gelar_depan . $nama . $gelar_belakang;

    // Menggunakan PhotoHelper untuk konsistensi
    $photoSmallUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto_small');
    $photoUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto_square');
@endphp

@section('content')
<div id="kt_app_content" class="app-content  flex-column-fluid ">
    <div id="kt_app_content_container" class="app-container  container-fluid ">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-15 px-0">
                <div class="d-flex flex-column text-center mb-9 px-9">
                    <div class="symbol symbol-80px symbol-lg-150px mb-4">
                        <img src="/metronic8/demo39/assets/media/avatars/300-3.jpg" class="" alt="" />
                    </div>
        
                    <div class="text-center">
                        <a href="/metronic8/demo39/pages/user-profile/overview.html"
                            class="text-gray-800 fw-bold text-hover-primary fs-4">Jerry Kane</a>
        
                        <span class="text-muted d-block fw-semibold">Grade 8, AE3 Student</span>
                    </div>
                </div>
        
                <div class="row px-9 mb-4">
                    <div class="col-md-4 text-center">
                        <div class="text-gray-800 fw-bold fs-3">
                            <span class="m-0" data-kt-countup="true" data-kt-countup-value="642">0</span>
                        </div>
        
                        <span class="text-gray-500 fs-8 d-block fw-bold">POSTS</span>
                    </div>
        
                    <div class="col-md-4 text-center">
                        <div class="text-gray-800 fw-bold fs-3">
                            <span class="m-0" data-kt-countup="true" data-kt-countup-value="24">0</span> K
                        </div>
        
                        <span class="text-gray-500 fs-8 d-block fw-bold">FOLLOWERS</span>
                    </div>
        
                    <div class="col-md-4 text-center">
                        <div class="text-gray-800 fw-bold fs-3">
                            <span class="m-0" data-kt-countup="true" data-kt-countup-value="12">0</span> K
                        </div>
        
                        <span class="text-gray-500 fs-8 d-block fw-bold">FOLLOWING</span>
                    </div>
                </div>
        
                <div class="m-0">
                    <ul class="nav nav-pills nav-pills-custom flex-column border-transparent fs-5 fw-bold">
                        <li class="nav-item mt-5">
                            <a class="nav-link text-muted text-active-primary ms-0 py-0 me-10 ps-9 border-0 "
                                href="/metronic8/demo39/pages/social/feeds.html">
                                <i class="ki-outline ki-row-horizontal fs-3 text-muted me-3"></i>
        
                                Feeds
                                <span
                                    class="bullet-custom position-absolute start-0 top-0 w-3px h-100 bg-primary rounded-end"></span>
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link text-muted text-active-primary ms-0 py-0 me-10 ps-9 border-0 "
                                href="/metronic8/demo39/pages/social/activity.html">
                                <i class="ki-outline ki-chart-simple-2 fs-3 text-muted me-3"></i>
        
                                Activity
                                <span
                                    class="bullet-custom position-absolute start-0 top-0 w-3px h-100 bg-primary rounded-end"></span>
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link text-muted text-active-primary ms-0 py-0 me-10 ps-9 border-0 "
                                href="/metronic8/demo39/pages/social/followers.html">
                                <i class="ki-outline ki-profile-circle fs-3 text-muted me-3"></i>
        
                                Followers
                                <span
                                    class="bullet-custom position-absolute start-0 top-0 w-3px h-100 bg-primary rounded-end"></span>
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link text-muted text-active-primary ms-0 py-0 me-10 ps-9 border-0 active"
                                href="/metronic8/demo39/pages/social/settings.html">
                                <i class="ki-outline ki-setting-2 fs-3 text-muted me-3"></i>
        
                                Settings
                                <span
                                    class="bullet-custom position-absolute start-0 top-0 w-3px h-100 bg-primary rounded-end"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection