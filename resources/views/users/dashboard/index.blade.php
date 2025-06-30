@extends('layouts.backend', ['title' => 'User Dashboard'])

@inject('DB', 'Illuminate\Support\Facades\DB')
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
    $user = auth()->user()->karyawan;
    // $gelar_depan = $user->gelar_depan ? $user->gelar_depan . ' ' : '';
    // $gelar_belakang = $user->gelar_belakang ? '' . $user->gelar_belakang : '';
    // $nama_lengkap = $gelar_depan . $user->nama . $gelar_belakang;
    $nama_lengkap = trim(($user->gelar_depan ?? '') . ' ' . $user->nama . '' . ($user->gelar_belakang ?? ''));
@endphp

@section('content')
    <div id="kt_app_content" class="app-content  flex-column-fluid ">
        <div id="kt_app_content_container" class="app-container container-fluid ">
            <div class="text-gray-600 fs-4 fw-bold mb-10">
                Selamat Datang, {{ $nama_lengkap }}
            </div>
        </div>
    </div>
@endsection