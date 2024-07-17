@extends('layouts.backend', ['title' => 'Edit Karyawan'])

{{-- carbon --}}
@inject('carbon', 'Carbon\Carbon')

@php
    $nama = $karyawan->nama;
    $gelarDepan = $karyawan->gelar_depan ? $karyawan->gelar_depan . ' ' : '';
    $gelarBelakang = $karyawan->gelar_belakang ? $karyawan->gelar_belakang : '';
    $namaLengkap = $gelarDepan . $nama . $gelarBelakang;

    $idPegawai = $karyawan->kd_karyawan;

    $nip = $karyawan->nip_baru;

    // if nip is null make it empty string
    if ($nip == '') {
        $nip = '';
    } else {
        $nip = ' | NIP.' . $karyawan->nip_baru;
    }

    // $sql = sqlsrv_query($konek, "select NO_SK, KET, TGL_SK from HRD_LOG where KD_LOG = (select max(KD_LOG) as max from HRD_LOG where KD_KARYAWAN = '$kd') and KD_KARYAWAN = '$kd'");
    $hrdLog = DB::table('hrd_log')
        ->select('no_sk', 'ket', 'tgl_sk')
        ->where('kd_karyawan', $karyawan->kd_karyawan)
        ->orderBy('kd_log', 'desc')
        ->first();

    if (empty($hrdLog->no_sk)) {
        $skChecked = '';
        $class = 'sk d-none';
    } else {
        $skChecked = 'checked';
        $class = 'sk';
    }

    if (empty($hrdLog->ket)) {
        $keteranganChecked = '';
        $classKeterangan = 'keterangan d-none';
    } else {
        $keteranganChecked = 'checked';
        $classKeterangan = 'keterangan';
    }

    $tgl_keluar_pensiun = $carbon::parse($karyawan->tgl_keluar_pensiun)->format('d-m-Y');
    $tanggal_lahir = $carbon::parse($karyawan->tgl_lahir)->format('d-m-Y');
    // $tmt_cpns = $carbon::parse($karyawan->tmt_gol_masuk)->format('d-m-Y');
    $tmt_cpns = $karyawan->tmt_gol_masuk == null ? '' : $carbon::parse($karyawan->tmt_gol_masuk)->format('d-m-Y');
    $tmt_pns = $karyawan->tmt_gol_sekarang == null ? '' : $carbon::parse($karyawan->tmt_gol_sekarang)->format('d-m-Y');
    $tmt_jabstruk = $karyawan->tmt_jabatan_struktural == null ? '' : $carbon::parse($karyawan->tmt_jabatan_struktural)->format('d-m-Y');
    $tmt_eselon = $karyawan->tmt_eselon == null ? '' : $carbon::parse($karyawan->tmt_eselon)->format('d-m-Y');
    $tmt_jabfung = $karyawan->tmt_jabfung == null ? '' : $carbon::parse($karyawan->tmt_jabfung)->format('d-m-Y');
    $kgb = $karyawan->kgb == null ? '' : $carbon::parse($karyawan->kgb)->format('d-m-Y');
    $rkp = $karyawan->rencana_kp == null ? '' : $carbon::parse($karyawan->rencana_kp)->format('d-m-Y');

    $tgl_sk = $hrdLog->tgl_sk == null ? '' : $carbon::parse($hrdLog->tgl_sk)->format('d-m-Y');
    
@endphp

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2 ">
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Edit Karyawan
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
                            <span class="text-muted text-hover-primary">Edit Karyawan</span>
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            <span class="text-muted">
                                ID PEG. {{ $idPegawai }} | <b>{{ $namaLengkap }}</b> {{ $nip }}
                            </span>
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
                {{-- Route::put('karyawan/update/{id}', [KaryawanController::class, 'update'])->name('karyawan.update'); --}}
                <form
                    class="form"
                    novalidate="novalidate"
                    id="edit-karyawan"
                    method="POST"
                    action="{{ route('admin.karyawan.update', $karyawan->kd_karyawan) }}"
                >
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="kd_karyawan" id="id" value="{{ $karyawan->kd_karyawan }}">
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
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        ID Pegawai
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="kd_karyawan" id="kd_karyawan" value="{{ $karyawan->kd_karyawan }}" disabled/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text kd_karyawan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_absen">
                                                        No. Absen
                                                    </label>
                                                    <input class="form-control form-control-solid" name="no_absen" id="no_absen" value="{{ $karyawan->no_absen }}" disabled/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_absen_error">
                                                    </div>
                                                </div>
                                            </div>
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
                                                                {{ $item->kd_status_kerja == $karyawan->kd_status_kerja ? 'selected' : '' }}
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
                                                                {{ $item->kd_jenis_peg == $karyawan->kd_jenis_tenaga ? 'selected' : '' }}
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
                                                                {{ $item->kd_status_pegawai == $karyawan->status_peg ? 'selected' : '' }}
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
                                                        placeholder="Pick a date" id="tmt" value="{{ $tgl_keluar_pensiun }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tmt_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5">
                                                <div class="col-auto fw-row">
                                                    <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                        <input class="form-check-input" type="checkbox" value="" id="no_sk_check" {{ $skChecked }}/>
                                                        <label class="form-check-label" for="no_sk_check">
                                                            No. SK {{ $karyawan->status_pegawai }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-auto fw-row">
                                                    <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                        <input class="form-check-input" type="checkbox" value="" id="keterangan_check" {{ $keteranganChecked }}/>
                                                        <label class="form-check-label" for="keterangan_check">
                                                            Keterangan {{ $karyawan->status_pegawai }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5 sk-field {{ $class }}">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Nomor SK
                                                    </label>
                                                    <input type="text"
                                                        class="form-control form-control-solid"
                                                        name="no_sk"
                                                        id="no_sk"
                                                        value="{{ $hrdLog->no_sk }}"
                                                    />
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_sk_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="tgl_sk">
                                                        Tanggal SK
                                                    </label>
                                                    <input class="form-control form-control-solid" name="tgl_sk" id="tgl_sk" value="{{ $tgl_sk }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tgl_sk_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-5 mb-5 keterangan-field {{ $classKeterangan }}">
                                                <div class="col-12 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Keterangan
                                                    </label>
                                                    <textarea class="form-control form-control-solid" name="keterangan" id="keterangan" rows="3">{{ $hrdLog->ket }}</textarea>
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
                                                        name="nip" id="nip" value="{{ $karyawan->nip_baru }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text nip_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="nip_lama">
                                                        NIP Lama
                                                    </label>
                                                    <input class="form-control form-control-solid" name="nip_lama" id="nip_lama" value="{{ $karyawan->nip_lama }}"/>
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
                                                        name="gelar_depan" id="gelar_depan" placeholder="Gelar depan" value="{{ $karyawan->gelar_depan }}"/>
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
                                                        value="{{ $karyawan->nama }}"
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
                                                        name="gelar_belakang" id="gelar_belakang" placeholder="Gelar belakang" value="{{ $karyawan->gelar_belakang }}"/>
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
                                                        name="tempat_lahir" id="tempat_lahir" value="{{ $karyawan->tempat_lahir }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tempat_lahir_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center" for="tgl_lahir">
                                                        Tanggal Lahir
                                                    </label>
                                                    <input class="form-control form-control-solid" name="tgl_lahir" id="tgl_lahir" value="{{ $tanggal_lahir }}"/>
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
                                                                <input
                                                                    class="form-check-input"
                                                                    type="radio"
                                                                    value="1"
                                                                    name="sex"
                                                                    id="sex_m"
                                                                    {{ $karyawan->kd_jenis_kelamin == 1 ? 'checked' : '' }}
                                                                />
                                                                <label class="form-check-label" for="sex_m">
                                                                    Laki-laki
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto fw-row">
                                                            <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="radio"
                                                                    value="0"
                                                                    name="sex"
                                                                    id="sex_f"
                                                                    {{ $karyawan->kd_jenis_kelamin == 0 ? 'checked' : '' }}
                                                                />
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
                                                        name="ktp" id="ktp" placeholder="No. KTP" value="{{ $karyawan->no_ktp }}"/>
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
                                                        name="email" id="email" placeholder="Email" value="{{ $karyawan->email }}"/>
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
                                                    <textarea class="form-control form-control-solid" name="alamat" id="alamat" rows="3">{{ $karyawan->alamat }}</textarea>
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
                                                                {{ $item->kd_propinsi == $karyawan->kd_propinsi ? 'selected' : '' }}
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
                                                        @foreach ($kabupaten as $item)
                                                            <option
                                                                value="{{ $item->kd_kabupaten }}"
                                                                {{ $item->kd_kabupaten == $karyawan->kd_kabupaten ? 'selected' : '' }}
                                                            >
                                                                {{ $item->kabupaten }}
                                                            </option>
                                                        @endforeach
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
                                                        @foreach ($kecamatan as $item)
                                                            <option
                                                                value="{{ $item->kd_kecamatan }}"
                                                                {{ $item->kd_kecamatan == $karyawan->kd_kecamatan ? 'selected' : '' }}
                                                            >
                                                                {{ $item->kecamatan }}
                                                            </option>
                                                        @endforeach
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
                                                        @foreach ($kelurahan as $item)
                                                            <option
                                                                value="{{ $item->kd_kelurahan }}"
                                                                {{ $item->kd_kelurahan == $karyawan->kd_kelurahan ? 'selected' : '' }}
                                                            >
                                                                {{ $item->kelurahan }}
                                                            </option>
                                                        @endforeach
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
                                                                {{ $item->kd_kulit == $karyawan->kd_kulit ? 'selected' : '' }}
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
                                                                {{ $item->kd_suku == $karyawan->kd_suku ? 'selected' : '' }}
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
                                                                {{ $item->kd_bangsa == $karyawan->kd_bangsa ? 'selected' : '' }}
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
                                                                {{ $item->kd_agama == $karyawan->kd_agama ? 'selected' : '' }}
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
                                                        name="tinggi_badan" id="tinggi_badan" placeholder="Tinggi badan" value="{{ $karyawan->tinggi_badan }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text tinggi_badan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Berat Badan
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="berat_badan" id="berat_badan" placeholder="Berat badan" value="{{ $karyawan->berat_badan }}"/>
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
                                                                {{ $item->kode == $karyawan->kode_gol_dar ? 'selected' : '' }}
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
                                                                {{ $item->kd_status_nikah == $karyawan->kd_status_nikah ? 'selected' : '' }}
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
                                                        name="no_kartu" id="no_kartu" placeholder="No. KARIS/KARSU" value="{{ $karyawan->no_karis }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_kartu_error">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_akte">
                                                    No. Akte Kelahiran
                                                </label>
                                                <input class="form-control form-control-solid" name="no_akte" id="no_akte" placeholder="No. Akte Kelahiran" value="{{ $karyawan->no_akte }}"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text no_akte_error">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_bpjs">
                                                    No. ASKES/BPJS
                                                </label>
                                                <input class="form-control form-control-solid" name="no_bpjs" id="no_bpjs" placeholder="No. ASKES/BPJS" value="{{ $karyawan->no_askes }}"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text no_bpjs_error">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="npwp">
                                                    NPWP
                                                </label>
                                                <input class="form-control form-control-solid" name="npwp" id="npwp" placeholder="NPWP" value="{{ $karyawan->no_npwp }}"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text npwp_error">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_karpeg">
                                                    No. KARPEG
                                                </label>
                                                <input class="form-control form-control-solid" name="no_karpeg" id="no_karpeg" placeholder="No. KARPEG" value="{{ $karyawan->no_karpeg }}"/>
                                                <div
                                                    class="fv-plugins-message-container invalid-feedback error-text no_karpeg_error">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="no_taspen">
                                                    No. TASPEN
                                                </label>
                                                <input class="form-control form-control-solid" name="no_taspen" id="no_taspen" placeholder="No. TASPEN" value="{{ $karyawan->no_taspen }}"/>
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
                                                        name="no_kk" id="no_kk" placeholder="No. Kartu Keluarga" value="{{ $karyawan->no_kk }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_kk_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="nama_ibu">
                                                        Nama Ibu Kandung
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="nama_ibu" id="nama_ibu" placeholder="Nama ibu kandung" value="{{ $karyawan->nama_ibu_kandung }}"/>
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
                                                        name="no_hp" id="no_hp" placeholder="No. HP" value="{{ $karyawan->no_hp }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text no_hp_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="hp_alternatif">
                                                        No. HP Alternatif
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="hp_alternatif" id="hp_alternatif" placeholder="No. HP Alternatif" value="{{ $karyawan->no_hp_alternatif }}"/>
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
                                                            {{ $item->kd_status_rmh == $karyawan->kd_status_rmh ? 'selected' : '' }}
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
                                                        name="bsi" id="bsi" placeholder="Rekening BSI" value="{{ $karyawan->rek_bni_syariah }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text bsi_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="bpd_aceh">
                                                        Rekening BPD Aceh
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="bpd_aceh" id="bpd_aceh" placeholder="Rekening BPD Aceh" value="{{ $karyawan->rek_bpd_aceh }}"/>
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
                                                    name="tanggungan" id="tanggungan" placeholder="Tanggungan dalam gaji PNS" value="{{ $karyawan->tanggungan }}"/>
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
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="pendidikan">
                                                        Pend. Terakhir
                                                    </label>
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih pendidikan terakhir"
                                                        data-allow-clear="true"
                                                        name="pendidikan"
                                                        id="pendidikan"
                                                    >
                                                        <option></option>
                                                        @foreach ($pendidikan as $item)
                                                            <option
                                                                data-grup="{{ $item->grup_jurusan }}"
                                                                value="{{ $item->kd_jenjang_didik }}"
                                                                {{ $item->kd_jenjang_didik == $karyawan->kd_pendidikan_terakhir ? 'selected' : '' }}
                                                            >
                                                                {{ $item->jenjang_didik }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text pendidikan_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="jurusan">
                                                        Jurusan
                                                    </label>
                                                    {{-- <select id="jurusan" class="form-control select2" name="jurusan">
                                                        <option value="<?php echo $isi['KD_JURUSAN']; ?>"><?php echo $isi['JURUSAN']; ?></option>
                                                        <?php
                                                          $kd_didik = $isi['KD_PENDIDIKAN_TERAKHIR'];
                                                          $sttgrup = sqlsrv_query($konek, "select GRUP_JURUSAN from HRD_JENJANG_PENDIDIKAN where KD_JENJANG_DIDIK = '$kd_didik'");
                                                          $hasil = sqlsrv_fetch_array($sttgrup);
                                                          $grup = $hasil['GRUP_JURUSAN'];
                                                          $sttjenjang = sqlsrv_query($konek, "select * from HRD_JURUSAN where GRUP_JURUSAN = '$grup' order by KD_JURUSAN ASC");
                                                          while ($data = sqlsrv_fetch_array($sttjenjang)){
                                                            echo "<option value=".$data['KD_JURUSAN'].">".$data['JURUSAN']."</option>";
                                                          }
                                                        ?>
                                                    </select> --}}
                                                    <select
                                                        class="form-select form-select-solid"
                                                        data-control="select2"
                                                        data-placeholder="Pilih jurusan"
                                                        data-allow-clear="true"
                                                        name="jurusan"
                                                        id="jurusan"
                                                    >
                                                        <option value="{{ $karyawan->kd_jurusan }}">{{ $karyawan->jurusan }}</option>
                                                        @php
                                                            $kd_didik = $karyawan->kd_pendidikan_terakhir;
                                                            $grup = DB::select("select grup_jurusan from hrd_jenjang_pendidikan where kd_jenjang_didik = '$kd_didik'");
                                                            $kd_grup = $grup[0]->grup_jurusan;
                                                            $sttjenjang = DB::select("select * from hrd_jurusan where grup_jurusan = '$kd_grup' order by kd_jurusan asc");
                                                        @endphp
                                                        @foreach ($sttjenjang as $item)
                                                            <option
                                                                value="{{ $item->kd_jurusan }}"
                                                            >
                                                                {{ $item->jurusan }}
                                                            </option>
                                                        @endforeach
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
                                                        name="tahun_lulus" id="tahun_lulus" value="{{ $karyawan->tahun_lulus }}"/>
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
                                                    <p>Untuk memindahkan pegawai silahkan menggunakan menu <span class="text-danger"><b>MUTASI</b></span></p>
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
                                                                {{ $item->kd_gol == $karyawan->kd_gol_masuk ? 'selected' : '' }}
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
                                                        name="tmt_cpns" id="tmt_cpns"
                                                        value="{{ $tmt_cpns }}"
                                                    />
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
                                                        name="masa_kerja_tahun_cpns" id="masa_kerja_tahun_cpns" placeholder="Masa kerja tahun" value="{{ $karyawan->masa_kerja_thn_cpns }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text masa_kerja_tahun_cpns_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Masa Kerja Bulan CPNS
                                                    </label>
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="masa_kerja_bulan_cpns" id="masa_kerja_bulan_cpns" placeholder="Masa kerja bulan" value="{{ $karyawan->masa_kerja_bln_cpns }}"/>
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
                                                                {{ $item->kd_gol == $karyawan->kd_gol_sekarang ? 'selected' : '' }}
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
                                                        name="tmt_pns" id="tmt_pns" value="{{ $tmt_pns }}"/>
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
                                                        name="masa_kerja_tahun_pns" id="masa_kerja_tahun_pns" placeholder="Tinggi badan" value="{{ $karyawan->masa_kerja_thn }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text masa_kerja_tahun_pns_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Masa Kerja Bulan
                                                    </label>
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="masa_kerja_bulan_pns" id="masa_kerja_bulan_pns" placeholder="Berat badan" value="{{ $karyawan->masa_kerja_bulan }}"/>
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
                                                                {{ $item->kd_jab_struk == $karyawan->kd_jabatan_struktural ? 'selected' : '' }}
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
                                                        name="tmt_jabstruk" id="tmt_jabstruk" value="{{ $tmt_jabstruk }}"/>
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
                                                                {{ $item->kd_eselon == $karyawan->kd_eselon ? 'selected' : '' }}
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
                                                        name="tmt_eselon" id="tmt_eselon" value="{{ $tmt_eselon }}"/>
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
                                                                {{ $item->kd_jab_fung == $karyawan->kd_jabfung ? 'selected' : '' }}
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
                                                        name="tmt_jabfung" id="tmt_jabfung" value="{{ $tmt_jabfung }}"/>
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
                                                        name="gaji_berkala" id="gaji_berkala" value="{{ $kgb }}"/>
                                                    <div
                                                        class="fv-plugins-message-container invalid-feedback error-text gaji_berkala_error">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                        Rencana Kenaikan Pangkat
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid"
                                                        name="kenaikan_pangkat" id="kenaikan_pangkat" value="{{ $rkp }}"/>
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
                                    Simpan Perubahan
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
        $('#edit-karyawan').on('submit', function(e) {
            e.preventDefault();
            
            var form = this;
            var id = $('#id').val();
            // Route::put('karyawan/update/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
            var url = "{{ route('admin.karyawan.update', ':id') }}";
            url = url.replace(':id', id);
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