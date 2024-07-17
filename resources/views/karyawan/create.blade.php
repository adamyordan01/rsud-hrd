@extends('layouts.backend', ['title' => 'Tambah Karyawan'])

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2 ">
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Tambah Karyawan
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="/metronic8/demo39/index.html" class="text-muted text-hover-primary">
                                List Karyawan    
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            <span class="text-muted text-hover-primary">Tambah Karyawan</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content  flex-column-fluid ">
        <div id="kt_app_content_container" class="app-container  container-fluid">
            <div class="stepper stepper-pills" id="kt_stepper_example_basic">
                <!--begin::Nav-->
                <div class="stepper-nav flex-center flex-wrap mb-10">
                    <!--begin::Step 1-->
                    <div class="stepper-item mx-8 my-4 current" data-kt-stepper-element="nav">
                        <!--begin::Wrapper-->
                        <div class="stepper-wrapper d-flex align-items-center">
                            <!--begin::Icon-->
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check"></i>
                                <span class="stepper-number">1</span>
                            </div>
                            <!--end::Icon-->

                            <!--begin::Label-->
                            <div class="stepper-label">
                                <h3 class="stepper-title">
                                    Langkah 1
                                </h3>

                                <div class="stepper-desc">
                                    Data Pribadi
                                </div>
                            </div>
                            <!--end::Label-->
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Line-->
                        <div class="stepper-line h-40px"></div>
                        <!--end::Line-->
                    </div>
                    <!--end::Step 1-->

                    <!--begin::Step 2-->
                    <div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                        <!--begin::Wrapper-->
                        <div class="stepper-wrapper d-flex align-items-center">
                            <!--begin::Icon-->
                            <div class="stepper-icon w-40px h-40px">
                                <i class="stepper-check fas fa-check"></i>
                                <span class="stepper-number">2</span>
                            </div>
                            <!--begin::Icon-->

                            <!--begin::Label-->
                            <div class="stepper-label">
                                <h3 class="stepper-title">
                                    Langkah 2
                                </h3>

                                <div class="stepper-desc">
                                    Data Pekerjaan
                                </div>
                            </div>
                            <!--end::Label-->
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Line-->
                        <div class="stepper-line h-40px"></div>
                        <!--end::Line-->
                    </div>
                    <!--end::Step 2-->
                </div>
                <form class="form" novalidate="novalidate" id="add-karyawan" method="POST" action="{{ route('admin.karyawan.store') }}">
                    @csrf
                    <div class="mb-5">
                        <!--begin::Step 1-->
                        <div class="flex-column current" data-kt-stepper-element="content">
                            <div class="row g-10 mb-5">
                                <div class="col-md-6 fv-row">
                                    <div class="card card-dashed">
                                        <div class="card-header">
                                            <h3 class="card-title">Identitas Pegawai</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Status Kerja
                                                    </label>
                                                    {{-- <input type="text" class="form-control form-control-solid"
                                                        name="status_kerja" id="status_kerja"/> --}}
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih status kerja"
                                                        data-allow-clear="true"
                                                        name="status_kerja"
                                                        id="status_kerja"
                                                    >
                                                        <option></option>
                                                        @foreach ($statusKerja as $item)
                                                            <option
                                                                value="{{ $item->kd_status_kerja }}"
                                                            >
                                                                {{ $item->status_kerja }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text status_kerja_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Jenis Pegawai
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih jenis pegawai"
                                                        data-allow-clear="true"
                                                        name="jenis_pegawai"
                                                        id="jenis_pegawai"
                                                    >
                                                        <option></option>
                                                        @foreach ($jenisPegawai as $item)
                                                            <option
                                                                value="{{ $item->kd_jenis_peg }}"
                                                            >
                                                                {{ $item->jenis_peg }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text jenis_pegawai_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Status Pegawai
                                                    </label>
                                                    {{-- <input type="text" class="form-control form-control-solid"
                                                        name="status_kerja" id="status_kerja"/> --}}
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih status pegawai"
                                                        data-allow-clear="true"
                                                        name="status_pegawai"
                                                        id="status_pegawai"
                                                    >
                                                        <option></option>
                                                        @foreach ($statusPegawai as $item)
                                                            <option
                                                                value="{{ $item->kd_status_pegawai }}"
                                                            >
                                                                {{ $item->status_pegawai }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text status_pegawai_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        TMT
                                                    </label>
                                                    <input class="form-control form-control-solid" name="tmt"
                                                        placeholder="Pick a date" id="tmt" />
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tmt_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-auto fw-row">
                                                    <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                        <input class="form-check-input" type="checkbox" value="" id="no_sk_check"/>
                                                        <label class="form-check-label" for="no_sk_check">
                                                            No. SK
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-auto fw-row">
                                                    <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                        <input class="form-check-input" type="checkbox" value="" id="keterangan_check"/>
                                                        <label class="form-check-label" for="keterangan_check">
                                                            Keterangan
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5 sk-field d-none">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Nomor SK
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="no_sk" id="no_sk"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_sk_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="tgl_sk">
                                                        Tanggal SK
                                                    </label>
                                                    <input class="form-control form-control-solid" name="tgl_sk" id="tgl_sk" />
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tgl_sk_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5 keterangan-field d-none">
                                                <div class="col-12 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Keterangan
                                                    </label>
                                                    <textarea
                                                        class="form-control form-control-solid"
                                                        rows="3"
                                                        name="keterangan" id="keterangan"></textarea>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text keterangan_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        NIP Baru
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="nip" id="nip"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text nip_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="nip_lama">
                                                        NIP Lama
                                                    </label>
                                                    <input class="form-control form-control-solid" name="nip_lama" id="nip_lama" />
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text nip_lama_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-3 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Gelar Depan
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="gelar_depan" id="gelar_depan" placeholder="Gelar depan"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text gelar_depan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Nama Lengkap
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="nama" id="nama" placeholder="Nama lengkap anda"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                    />
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text nama_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Gelar Belakang
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="gelar_belakang" id="gelar_belakang" placeholder="Gelar belakang"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text gelar_belakang_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Tempat Lahir
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="tempat_lahir" id="tempat_lahir"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tempat_lahir_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center" for="tgl_lahir">
                                                        Tanggal Lahir
                                                    </label>
                                                    <input class="form-control form-control-solid" name="tgl_lahir" id="tgl_lahir" />
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tgl_lahir_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-12 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Jenis Kelamin
                                                    </label>
                                                    <div class="row g-5 mb-5">
                                                        <div class="col-auto fw-row">
                                                            <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                                <input class="form-check-input" type="radio" value="1" name="sex" id="sex_m"/>
                                                                <label class="form-check-label" for="sex_m">
                                                                    Laki-laki
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto fw-row">
                                                            <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                                <input class="form-check-input" type="radio" value="0" name="sex" id="sex_f"/>
                                                                <label class="form-check-label" for="sex_f">
                                                                    Perempuan
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text sex_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-12 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="ktp">
                                                        No. KTP
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="ktp" id="ktp" placeholder="No. KTP"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text ktp_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-12 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="email">
                                                        Email
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="email" id="email" placeholder="Email"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text email_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-12 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="alamat">
                                                        Alamat
                                                    </label>
                                                    <textarea class="form-control form-control-solid" name="alamat" id="alamat" rows="3"></textarea>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text alamat_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="provinsi">
                                                        Provinsi
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih provinsi"
                                                        data-allow-clear="true"
                                                        name="provinsi"
                                                        id="provinsi"
                                                    >
                                                        <option></option>
                                                        @foreach ($provinsi as $item)
                                                            <option
                                                                value="{{ $item->kd_propinsi }}"
                                                            >
                                                                {{ $item->propinsi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text provinsi_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="kabupaten">
                                                        Kabupaten/Kota
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih kabupaten/kota"
                                                        data-allow-clear="true"
                                                        name="kabupaten"
                                                        id="kabupaten"
                                                    >
                                                        <option></option>
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text kabupaten_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="kecamatan">
                                                        Kecamatan
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih kecamatan"
                                                        data-allow-clear="true"
                                                        name="kecamatan"
                                                        id="kecamatan"
                                                    >
                                                        <option></option>
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text kecamatan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="kelurahan">
                                                        Kelurahan
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih kelurahan"
                                                        data-allow-clear="true"
                                                        name="kelurahan"
                                                        id="kelurahan"
                                                    >
                                                        <option></option>
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text kelurahan_error">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <div class="card card-dashed">
                                        <div class="card-header">
                                            <h3 class="card-title">Akun Pegawai</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-3 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Warna Kulit
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih warna kulit"
                                                        data-allow-clear="true"
                                                        name="warna_kulit"
                                                        id="warna_kulit"
                                                    >
                                                        <option></option>
                                                        @foreach ($warnaKulit as $item)
                                                            <option
                                                                value="{{ $item->kd_kulit }}"
                                                            >
                                                                {{ $item->kulit }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text warna_kulit_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Suku Bangsa
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih suku bangsa"
                                                        data-allow-clear="true"
                                                        name="suku_bangsa"
                                                        id="suku_bangsa"
                                                    >
                                                        <option></option>
                                                        @foreach ($sukuBangsa as $item)
                                                            <option
                                                                value="{{ $item->kd_suku }}"
                                                            >
                                                                {{ $item->suku }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text suku_bangsa_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Kebangsaan
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih kebangsaan"
                                                        data-allow-clear="true"
                                                        name="kebangsaan"
                                                        id="kebangsaan"
                                                    >
                                                        <option></option>
                                                        @foreach ($kebangsaan as $item)
                                                            <option
                                                                value="{{ $item->kd_bangsa }}"
                                                            >
                                                                {{ $item->kebangsaan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text kebangsaan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Agama
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih agama"
                                                        data-allow-clear="true"
                                                        name="agama"
                                                        id="agama"
                                                    >
                                                        <option></option>
                                                        @foreach ($agama as $item)
                                                            <option
                                                                value="{{ $item->kd_agama }}"
                                                            >
                                                                {{ $item->agama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text agama_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-4 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Tinggi Badan
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="tinggi_badan" id="tinggi_badan" placeholder="Tinggi badan"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tinggi_badan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Berat Badan
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="berat_badan" id="berat_badan" placeholder="Berat badan"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text berat_badan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Golongan Darah
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih golongan darah"
                                                        data-allow-clear="true"
                                                        name="golongan_darah"
                                                        id="golongan_darah"
                                                    >
                                                        <option></option>
                                                        @foreach ($golonganDarah as $item)
                                                            <option
                                                                value="{{ $item->kode }}"
                                                            >
                                                                {{ $item->jenis }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text golongan_darah_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Status Nikah
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih status nikah"
                                                        data-allow-clear="true"
                                                        name="status_nikah"
                                                        id="status_nikah"
                                                    >
                                                        <option></option>
                                                        @foreach ($statusNikah as $item)
                                                            <option
                                                                value="{{ $item->kd_status_nikah }}"
                                                            >
                                                                {{ $item->status_nikah }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text status_nikah_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        No. KARIS/KARSU
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="no_kartu" id="no_kartu" placeholder="No. KARIS/KARSU"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_kartu_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_akte">
                                                    No. Akte Kelahiran
                                                </label>
                                                <input class="form-control form-control-solid" name="no_akte" id="no_akte" placeholder="No. Akte Kelahiran"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text no_akte_error">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_bpjs">
                                                    No. ASKES/BPJS
                                                </label>
                                                <input class="form-control form-control-solid" name="no_bpjs" id="no_bpjs" placeholder="No. ASKES/BPJS"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text no_bpjs_error">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="npwp">
                                                    NPWP
                                                </label>
                                                <input class="form-control form-control-solid" name="npwp" id="npwp" placeholder="NPWP"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text npwp_error">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_karpeg">
                                                    No. KARPEG
                                                </label>
                                                <input class="form-control form-control-solid" name="no_karpeg" id="no_karpeg" placeholder="No. KARPEG"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text no_karpeg_error">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_taspen">
                                                    No. TASPEN
                                                </label>
                                                <input class="form-control form-control-solid" name="no_taspen" id="no_taspen" placeholder="No. TASPEN"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text no_taspen_error">
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_kk">
                                                        No. Kartu Keluarga
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="no_kk" id="no_kk" placeholder="No. Kartu Keluarga"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_kk_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="nama_ibu">
                                                        Nama Ibu Kandung
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="nama_ibu" id="nama_ibu" placeholder="Nama ibu kandung"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text nama_ibu_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_hp">
                                                        No. HP
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="no_hp" id="no_hp" placeholder="No. HP"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_hp_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="hp_alternatif">
                                                        No. HP Alternatif
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="hp_alternatif" id="hp_alternatif" placeholder="No. HP Alternatif"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text hp_alternatif_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="status_rumah">
                                                    Status Rumah
                                                </label>
                                                <select
                                                    class="form-select form-select-solid"
                                                    data-control="select2"
                                                    data-placeholder="Pilih status rumah"
                                                    data-allow-clear="true"
                                                    name="status_rumah"
                                                    id="status_rumah"
                                                >
                                                    <option></option>
                                                    @foreach ($statusRumah as $item)
                                                        <option
                                                            value="{{ $item->kd_status_rmh }}"
                                                        >
                                                            {{ $item->status_rmh }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text status_rumah_error">
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="bsi">
                                                        Rekening BSI
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="bsi" id="bsi" placeholder="Rekening BSI"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text bsi_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="bpd_aceh">
                                                        Rekening BPD Aceh
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="bpd_aceh" id="bpd_aceh" placeholder="Rekening BPD Aceh"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text bpd_aceh_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="tanggungan">
                                                    Tanggungan Dalam Gaji PNS
                                                </label>
                                                <input type="number" class="form-control form-control-solid"
                                                    name="tanggungan" id="tanggungan" placeholder="Tanggungan dalam gaji PNS"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text tanggungan_error">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Step 1-->

                        <!--begin::Step 2-->
                        <div class="flex-column" data-kt-stepper-element="content">
                            <div class="row g-10 mb-5">
                                <div class="col-md-6 fv-row">
                                    <div class="card card-dashed">
                                        <div class="card-header">
                                            <h3 class="card-title">Data pendidikan dan pekerjaan</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-4 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                                                        for="pendidikan">
                                                        Pend. Terakhir
                                                    </label>
                                                    <select class="form-select form-select-solid" data-control="select2"
                                                        data-placeholder="Pilih pendidikan terakhir"
                                                        data-allow-clear="true" name="pendidikan" id="pendidikan">
                                                        <option></option>
                                                        @foreach ($pendidikan as $item)
                                                        <option data-grup="{{ $item->grup_jurusan }}"
                                                            value="{{ $item->kd_jenjang_didik }}">
                                                            {{ $item->jenjang_didik }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text pendidikan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                                                        for="jurusan">
                                                        Jurusan
                                                    </label>
                                                    <select class="form-select form-select-solid" data-control="select2"
                                                        data-placeholder="Pilih jurusan" data-allow-clear="true"
                                                        name="jurusan" id="jurusan">
                                                        <option></option>
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text jenis_pegawai_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Thn. Lulus
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="tahun_lulus" id="tahun_lulus" />
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tahun_lulus_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mt-14 mb-5">
                                                <div class="col-md-12 text-center">
                                                    <h4>
                                                        <u>PERHATIAN</u>
                                                    </h4>
                                                    <p>Untuk memindahkan pegawai silahkan menggunakan menu <span
                                                            class="text-danger"><b>MUTASI</b></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <div class="card card-dashed">
                                        <div class="card-header">
                                            <h3 class="card-title">Data Jabatan Pegawai</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                                                        for="golongan_pns"
                                                    >
                                                        Golongan CPNS
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih golongan CPNS"
                                                        data-allow-clear="true"
                                                        name="golongan_cpns"
                                                        id="golongan_cpns"
                                                    >
                                                        <option></option>
                                                        @foreach ($golongan as $item)
                                                            <option
                                                                value="{{ $item->kd_gol }}"
                                                            >
                                                                {{ $item->kd_gol }}
                                                            </option>
                                                        @endforeach   
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text golongan_cpns_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        TMT CPNS
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="tmt_cpns" id="tmt_cpns"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tmt_cpns_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Masa Kerja Tahun CPNS
                                                    </label>
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="masa_kerja_tahun_cpns" id="masa_kerja_tahun_cpns" placeholder="Masa kerja tahun"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text masa_kerja_tahun_cpns_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Masa Kerja Bulan CPNS
                                                    </label>
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="masa_kerja_bulan_cpns" id="masa_kerja_bulan_cpns" placeholder="Masa kerja bulan"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text masa_kerja_bulan_cpns_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                                                        for="golongan_pns"
                                                    >
                                                        Golongan PNS (Sekarang)
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih golongan PNS"
                                                        data-allow-clear="true"
                                                        name="golongan_pns"
                                                        id="golongan_pns"
                                                    >
                                                        <option></option>
                                                        @foreach ($golongan as $item)
                                                            <option
                                                                value="{{ $item->kd_gol }}"
                                                            >
                                                                {{ $item->kd_gol }}
                                                            </option>
                                                        @endforeach   
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text golongan_pns_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        TMT PNS (Sekarang)
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="tmt_pns" id="tmt_pns"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tmt_pns_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Masa Kerja Tahun
                                                    </label>
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="masa_kerja_tahun_pns" id="masa_kerja_tahun_pns" placeholder="Tinggi badan"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text masa_kerja_tahun_pns_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Masa Kerja Bulan
                                                    </label>
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="masa_kerja_bulan_pns" id="masa_kerja_bulan_pns" placeholder="Berat badan"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text masa_kerja_bulan_pns_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                                                        for="jabatan_struktural"
                                                    >
                                                        Jabatan Struktural / Non-Struktural
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih jabatan struktural / non-struktural"
                                                        data-allow-clear="true"
                                                        name="jabatan_struktural"
                                                        id="jabatan_struktural"
                                                    >
                                                        <option></option>
                                                        @foreach ($jabatanStruktural as $item)
                                                            <option
                                                                value="{{ $item->kd_jab_struk }}"
                                                            >
                                                                {{ $item->jab_struk }}
                                                            </option>
                                                        @endforeach   
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text jabatan_struktural_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        TMT
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="tmt_jabstruk" id="tmt_jabstruk"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tmt_jabstruk_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                                                        for="jabatan_eselon"
                                                    >
                                                        Jabatan Eselon
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih jabatan eselon"
                                                        data-allow-clear="true"
                                                        name="jabatan_eselon"
                                                        id="jabatan_eselon"
                                                    >
                                                        <option></option>
                                                        @foreach ($eselon as $item)
                                                            <option
                                                                value="{{ $item->kd_eselon }}"
                                                            >
                                                                {{ $item->eselon }}
                                                            </option>
                                                        @endforeach   
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text jabatan_eselon_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        TMT
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="tmt_eselon" id="tmt_eselon"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tmt_eselon_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center"
                                                        for="jabatan_fungsional"
                                                    >
                                                        Jabatan Fungsional
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih jabatan fungsional"
                                                        data-allow-clear="true"
                                                        name="jabatan_fungsional"
                                                        id="jabatan_fungsional"
                                                    >
                                                        <option></option>
                                                        @foreach ($fungsional as $item)
                                                            <option
                                                                value="{{ $item->kd_jab_fung }}"
                                                            >
                                                                {{ $item->jab_fung }}
                                                            </option>
                                                        @endforeach   
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text jabatan_fungsional_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        TMT
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="tmt_jabfung" id="tmt_jabfung"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tmt_jabfung_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Kenaikan Gaji Berkala
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="gaji_berkala" id="gaji_berkala"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text gaji_berkala_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Rencana Kenaikan Pangkat
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="kenaikan_pangkat" id="kenaikan_pangkat"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text kenaikan_pangkat_error">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-stack">
                        <!--begin::Wrapper-->
                        <div class="me-2">
                            <button type="button" class="btn btn-light btn-active-light-primary"
                                data-kt-stepper-action="previous">
                                Kembali
                            </button>
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Wrapper-->
                        <div>
                            <button type="submit" class="btn btn-primary" data-kt-stepper-action="submit" id="btn-submit">
                                <span class="indicator-label">
                                    Simpan
                                </span>
                                <span class="indicator-progress">
                                    Please wait... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>

                            <button type="button" id="kt_stepper_example_basic_next" class="btn btn-primary" data-kt-stepper-action="next">
                                Selanjutnya
                            </button>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Stepper lement
        var element = document.querySelector("#kt_stepper_example_basic");

        // Initialize Stepper
        var stepper = new KTStepper(element);

        // Handle next step
        stepper.on("kt.stepper.next", function (stepper) {
            stepper.goNext(); // go next step
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
            stepper.goPrevious(); // go previous step
        });

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#tmt");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#tgl_sk");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#tgl_lahir");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#tmt_cpns");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#tmt_pns");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#tmt_jabstruk");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#tmt_eselon");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#tmt_jabfung");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#gaji_berkala");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#kenaikan_pangkat");
        
        // mask untuk tahun lulus
        Inputmask({
            "mask": "9999",
            "placeholder": "____",
        }).mask("#tahun_lulus");

        // mask untuk ktp
        Inputmask({
            "mask": "9999999999999999",
            "placeholder": "________________",
        }).mask("#ktp");

        // mask untuk no kartu keluarga
        Inputmask({
            "mask": "9999999999999999",
            "placeholder": "________________",
        }).mask("#no_kk");

        $(document).ready(function() {
            // provinsi change event
            $('#provinsi').on('change', function() {
                var kd_propinsi = $(this).val();
                // Route::get('/lokasi/kabupaten/{id}', [LokasiController::class, 'getKabupaten'])->name('lokasi.kabupaten');
                var url = "{{ route('lokasi.kabupaten', ':kd_propinsi') }}";
                if (kd_propinsi) {
                    $.ajax({
                        url: url.replace(':kd_propinsi', kd_propinsi),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#kabupaten').empty();
                            $('#kabupaten').append(
                                '<option value="" selected disabled>Pilih kabupaten/kota</option>'
                            );
                            $.each(data, function(key, value) {
                                $('#kabupaten').append(
                                    '<option value="' + value.kd_kabupaten + '">' + value.kabupaten +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('#kabupaten').empty();
                    $('#kecamatan').empty();
                    $('#kelurahan').empty();
                }
            });

            // kabupaten change event
            $('#kabupaten').on('change', function() {
                var kd_kabupaten = $(this).val();
                // Route::get('/lokasi/kecamatan/{id}', [LokasiController::class, 'getKecamatan'])->name('lokasi.kecamatan');
                var url = "{{ route('lokasi.kecamatan', ':kd_kabupaten') }}";
                if (kd_kabupaten) {
                    $.ajax({
                        url: url.replace(':kd_kabupaten', kd_kabupaten),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#kecamatan').empty();
                            $('#kecamatan').append(
                                '<option value="" selected disabled>Pilih kecamatan</option>'
                            );
                            $.each(data, function(key, value) {
                                $('#kecamatan').append(
                                    '<option value="' + value.kd_kecamatan + '">' + value.kecamatan +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('#kecamatan').empty();
                    $('#kelurahan').empty();
                }
            });

            // kecamatan change event
            $('#kecamatan').on('change', function() {
                var kd_kecamatan = $(this).val();
                // Route::get('/lokasi/kelurahan/{id}', [LokasiController::class, 'getKelurahan'])->name('lokasi.kelurahan');
                var url = "{{ route('lokasi.kelurahan', ':kd_kecamatan') }}";
                if (kd_kecamatan) {
                    $.ajax({
                        url: url.replace(':kd_kecamatan', kd_kecamatan),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#kelurahan').empty();
                            $('#kelurahan').append(
                                '<option value="" selected disabled>Pilih kelurahan</option>'
                            );
                            $.each(data, function(key, value) {
                                $('#kelurahan').append(
                                    '<option value="' + value.kd_kelurahan + '">' + value.kelurahan +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('#kelurahan').empty();
                }
            });

            // pendidikan change event and the value is data-grup
            $('#pendidikan').on('change', function() {
                var grup_jurusan = $(this).find(':selected').data('grup');
                // Route::get('/karyawan/jurusan/{id}', [KaryawanController::class, 'getJurusan'])->name('karyawan.jurusan');
                var url = "{{ route('admin.karyawan.jurusan', ':grup_jurusan') }}";
                if (grup_jurusan) {
                    $.ajax({
                        url: url.replace(':grup_jurusan', grup_jurusan),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#jurusan').empty();
                            $('#jurusan').append(
                                '<option value="" selected disabled>Pilih jurusan</option>'
                            );
                            $.each(data, function(key, value) {
                                $('#jurusan').append(
                                    '<option value="' + value.kd_jurusan + '">' + value.jurusan +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('#jurusan').empty();
                }
            });


            // when #no_sk_check is checked then show sk-field
            $('#no_sk_check').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.sk-field').removeClass('d-none');
                } else {
                    $('.sk-field').addClass('d-none');
                }
            });

            // when #keterangan_check is checked then show keterangan-field
            $('#keterangan_check').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.keterangan-field').removeClass('d-none');
                } else {
                    $('.keterangan-field').addClass('d-none');
                }
            });            
        });

        // when form is submitted
        $('#add-karyawan').on('submit', function(e) {
            e.preventDefault();
            
            var form = this;
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            var formData = new FormData(form);

            var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
            $(form).find('#btn-submit').append(loadingIndicator);

            $.ajax({
                url: url,
                method: method,
                contentType: false,
                processData: false,
                dataType: 'json',
                data: formData,
                beforeSend: function() {
                    loadingIndicator.show();

                    $(form).find('#btn-submit').attr('disabled', true);
                    $(form).find('#btn-submit .indicator-label').hide();
                },
                complete: function() {
                    loadingIndicator.hide();

                    $(form).find('#btn-submit').attr('disabled', false);
                    $(form).find('#btn-submit .indicator-label').show();
                },
                success: function(response) {
                    console.log(response.code);
                    if (response.code == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                        }).then(function() {
                            // return to Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
                            window.location.href = "{{ route('admin.karyawan.index') }}";
                        });
                    } else if (response.code == 400) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                },
                // error: function(jqXHR, textStatus, errorThrown) {
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Oops...',
                //         text: 'Terjadi kesalahan pada server, silakan coba lagi nanti.',
                //     });
                // }
            })
        })

        // saat tombol selanjutnya di klik periksa apakah form sudah valid
        $('#kt_stepper_example_basic_next').on('click', function() {
            var status_kerja = $('#status_kerja').val();
            var jenis_pegawai = $('#jenis_pegawai').val();
            var status_pegawai = $('#status_pegawai').val();
            var nama = $('#nama').val();
            var ktp = $('#ktp').val().toString();
            var sex = $('input[name="sex"]:checked').val();

            var isValid = true;

            if (status_kerja == '') {
                $('.status_kerja_error').html('Status kerja tidak boleh kosong');
                isValid = false;
            } else {
                $('.status_kerja_error').html('');
            }

            if (jenis_pegawai == '') {
                $('.jenis_pegawai_error').html('Jenis pegawai tidak boleh kosong');
                isValid = false;
            } else {
                $('.jenis_pegawai_error').html('');
            }

            if (status_pegawai == '') {
                $('.status_pegawai_error').html('Status pegawai tidak boleh kosong');
                isValid = false;
            } else {
                $('.status_pegawai_error').html('');
            }

            if (nama == '') {
                $('.nama_error').html('Nama tidak boleh kosong');
                isValid = false;
            } else {
                $('.nama_error').html('');
            }

            if (ktp == '') {
                $('.ktp_error').html('Nomor KTP tidak boleh kosong');
                isValid = false;
            } else {
                $('.ktp_error').html('');
            }

            if (!sex) {
                $('.sex_error').html('Jenis kelamin harus dipilih');
                isValid = false;
            } else {
                $('.sex_error').html('');
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Form belum lengkap, silahkan lengkapi form yang wajib diisi!',
                }).then(function () {
                    KTUtil.scrollTop();
                })
                stepper.goPrevious();
                return false;
            } else {
                stepper.goNext();
            }
        });



        
        // "use strict";

        // var KTStepperBasicForm = (function () {
        //     var form, stepper, submitButton, nextButton, validators = [];

        //     return {
        //         init: function () {
        //             form = document.querySelector("#kt_stepper_example_basic_form");
        //             if (!form) return;
        //             stepper = new KTStepper(form);
        //             submitButton = form.querySelector('[data-kt-stepper-action="submit"]');
        //             nextButton = form.querySelector('[data-kt-stepper-action="next"]');

        //             stepper.on("kt.stepper.changed", function (event) {
        //                 var currentStepIndex = stepper.getCurrentStepIndex();
        //                 if (currentStepIndex === stepper.totalSteps) {
        //                     submitButton.classList.remove("d-none");
        //                     submitButton.classList.add("d-inline-block");
        //                     nextButton.classList.add("d-none");
        //                 } else {
        //                     submitButton.classList.remove("d-inline-block");
        //                     submitButton.classList.add("d-none");
        //                     nextButton.classList.remove("d-none");
        //                 }
        //             });

        //             stepper.on("kt.stepper.next", function (event) {
        //                 var validator = validators[event.getCurrentStepIndex() - 1];
        //                 console.log(validator);
        //                 if (validator) {
        //                     validator.validate().then(function (status) {
        //                         console.log(status);
        //                         if (status === "Valid") {
        //                             event.goNext();
        //                             KTUtil.scrollTop();
        //                         } else {
        //                             Swal.fire({
        //                                 text: "Sorry, looks like there are some errors detected, please try again.",
        //                                 icon: "error",
        //                                 buttonsStyling: false,
        //                                 confirmButtonText: "Ok, got it!",
        //                                 customClass: { confirmButton: "btn btn-light" }
        //                             }).then(function () {
        //                                 KTUtil.scrollTop();
        //                             });
        //                         }
        //                     });
        //                 } else {
        //                     event.goNext();
        //                     KTUtil.scrollTop();
        //                 }
        //             });

        //             stepper.on("kt.stepper.previous", function (event) {
        //                 event.goPrevious();
        //                 KTUtil.scrollTop();
        //             });

        //             submitButton.addEventListener("click", function (event) {
        //                 var validator = validators[validators.length - 1];
        //                 validator.validate().then(function (status) {
        //                     if (status === "Valid") {
        //                         event.preventDefault();
        //                         submitButton.disabled = true;
        //                         submitButton.setAttribute("data-kt-indicator", "on");

        //                         setTimeout(function () {
        //                             submitButton.removeAttribute("data-kt-indicator");
        //                             submitButton.disabled = false;
        //                             form.submit();
        //                         }, 2000);
        //                     } else {
        //                         Swal.fire({
        //                             text: "Sorry, looks like there are some errors detected, please try again.",
        //                             icon: "error",
        //                             buttonsStyling: false,
        //                             confirmButtonText: "Ok, got it!",
        //                             customClass: { confirmButton: "btn btn-light" }
        //                         }).then(function () {
        //                             KTUtil.scrollTop();
        //                         });
        //                     }
        //                 });
        //             });

        //             validators.push(FormValidation.formValidation(
        //                 form,
        //                 {
        //                     fields: {
        //                         status_kerja: {
        //                             validators: {
        //                                 notEmpty: { message: "Status kerja is required" }
        //                             }
        //                         },
        //                         jenis_pegawai: {
        //                             validators: {
        //                                 notEmpty: { message: "Jenis pegawai is required" }
        //                             }
        //                         },
        //                         status_pegawai: {
        //                             validators: {
        //                                 notEmpty: { message: "Status pegawai is required" }
        //                             }
        //                         },
        //                         tmt: {
        //                             validators: {
        //                                 notEmpty: { message: "TMT is required" }
        //                             }
        //                         }
        //                     },
        //                     plugins: {
        //                         trigger: new FormValidation.plugins.Trigger(),
        //                         bootstrap: new FormValidation.plugins.Bootstrap5({
        //                             rowSelector: ".fv-row",
        //                             eleInvalidClass: "",
        //                             eleValidClass: ""
        //                         })
        //                     }
        //                 }
        //             ));

        //             // Add more validators for other steps as needed
        //         }
        //     };
        // })();

        // KTUtil.onDOMContentLoaded(function () {
        //     KTStepperBasicForm.init();
        // });
    </script>
@endpush