@extends('layouts.backend', ['title' => 'Edit Pegawai Luar'])

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Edit Pegawai Luar - {{ $karyawanLuar->nama }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.karyawan-luar.index') }}" class="text-muted text-hover-primary">
                                Data Pegawai Luar
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <span class="text-muted text-hover-primary">Edit Pegawai Luar</span>
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
                                <h3 class="stepper-title">Langkah 1</h3>
                                <div class="stepper-desc">Identitas Pegawai</div>
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
                                <h3 class="stepper-title">Langkah 2</h3>
                                <div class="stepper-desc">Akun Pegawai</div>
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
                <!--end::Nav-->

                <form class="form" novalidate="novalidate" id="edit-karyawan-luar" method="POST" action="{{ route('admin.karyawan-luar.update', $karyawanLuar->kd_peg_luar) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-5">
                        <!--begin::Step 1-->
                        <div class="flex-column current" data-kt-stepper-element="content">
                            <div class="card card-dashed">
                                <div class="card-header">
                                    <h3 class="card-title">Identitas Pegawai Luar</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row g-5 mb-5">
                                        <div class="col-md-6 fv-row">
                                            <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Status Kerja
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih status kerja" data-allow-clear="true"
                                                name="status_kerja" id="status_kerja">
                                                <option></option>
                                                @foreach ($statusKerja as $item)
                                                    <option value="{{ $item->kd_status_kerja }}"
                                                        {{ $karyawanLuar->status_kerja == $item->kd_status_kerja ? 'selected' : '' }}>
                                                        {{ $item->status_kerja }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text status_kerja_error"></div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Jenis Pegawai
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih jenis pegawai" data-allow-clear="true"
                                                name="jenis_pegawai" id="jenis_pegawai">
                                                <option></option>
                                                @foreach ($jenisPegawai as $item)
                                                    <option value="{{ $item->kd_jenis_peg }}"
                                                        {{ $karyawanLuar->jenis_pegawai == $item->kd_jenis_peg ? 'selected' : '' }}>
                                                        {{ $item->jenis_peg }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text jenis_pegawai_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-6 fv-row">
                                            <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Status Pegawai
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih status pegawai" data-allow-clear="true"
                                                name="status_pegawai" id="status_pegawai">
                                                <option></option>
                                                @foreach ($statusPegawai as $item)
                                                    <option value="{{ $item->kd_status_pegawai }}"
                                                        {{ $karyawanLuar->status_pegawai == $item->kd_status_pegawai ? 'selected' : '' }}>
                                                        {{ $item->status_pegawai }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text status_pegawai_error"></div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                TMT
                                            </label>
                                            <input class="form-control form-control-solid" name="tmt" placeholder="Pick a date" 
                                                id="tmt" value="{{ isset($karyawanLuar->tmt) ? date('d/m/Y', strtotime($karyawanLuar->tmt)) : '' }}" />
                                            <div class="fv-plugins-message-container invalid-feedback error-text tmt_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-12 fv-row">
                                            <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                TMT Akhir
                                            </label>
                                            <input class="form-control form-control-solid" name="tmt_akhir" placeholder="Pick a date" 
                                                id="tmt_akhir" value="{{ isset($karyawanLuar->tmt_akhir) ? date('d/m/Y', strtotime($karyawanLuar->tmt_akhir)) : '' }}" />
                                            <div class="fv-plugins-message-container invalid-feedback error-text tmt_akhir_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-auto fw-row">
                                            <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                <input class="form-check-input" type="checkbox" value="" id="no_sk_check"
                                                    {{ $karyawanLuar->no_sk ? 'checked' : '' }}/>
                                                <label class="form-check-label" for="no_sk_check">No. SK</label>
                                            </div>
                                        </div>
                                        <div class="col-auto fw-row">
                                            <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                <input class="form-check-input" type="checkbox" value="" id="keterangan_check"
                                                    {{ $karyawanLuar->keterangan ? 'checked' : '' }}/>
                                                <label class="form-check-label" for="keterangan_check">Keterangan</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5 sk-field {{ $karyawanLuar->no_sk ? '' : 'd-none' }}">
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Nomor SK
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="no_sk" 
                                                id="no_sk" value="{{ $karyawanLuar->no_sk }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text no_sk_error"></div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="tgl_sk">
                                                Tanggal SK
                                            </label>
                                            <input class="form-control form-control-solid" name="tgl_sk" id="tgl_sk" 
                                                value="{{ isset($karyawanLuar->tgl_sk) ? date('d/m/Y', strtotime($karyawanLuar->tgl_sk)) : '' }}" />
                                            <div class="fv-plugins-message-container invalid-feedback error-text tgl_sk_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5 keterangan-field {{ $karyawanLuar->keterangan ? '' : 'd-none' }}">
                                        <div class="col-12 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Keterangan
                                            </label>
                                            <textarea class="form-control form-control-solid" rows="3" name="keterangan" 
                                                id="keterangan">{{ $karyawanLuar->keterangan }}</textarea>
                                            <div class="fv-plugins-message-container invalid-feedback error-text keterangan_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-3 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Gelar Depan
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="gelar_depan" 
                                                id="gelar_depan" placeholder="Gelar depan" value="{{ $karyawanLuar->gelar_depan }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text gelar_depan_error"></div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Nama Lengkap
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="nama" id="nama" 
                                                placeholder="Nama lengkap" onkeyup="this.value = this.value.toUpperCase();"
                                                value="{{ $karyawanLuar->nama }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text nama_error"></div>
                                        </div>
                                        <div class="col-md-3 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Gelar Belakang
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="gelar_belakang" 
                                                id="gelar_belakang" placeholder="Gelar belakang" value="{{ $karyawanLuar->gelar_belakang }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text gelar_belakang_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-6 fv-row">
                                            <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Tempat Lahir
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="tempat_lahir" id="tempat_lahir" 
                                                onkeyup="this.value=this.value.replace(/\b\w/g, l => l.toUpperCase())"
                                                value="{{ $karyawanLuar->tempat_lahir }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text tempat_lahir_error"></div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center" for="tgl_lahir">
                                                Tanggal Lahir
                                            </label>
                                            <input class="form-control form-control-solid" name="tgl_lahir" id="tgl_lahir" 
                                                value="{{ isset($karyawanLuar->tgl_lahir) ? date('d/m/Y', strtotime($karyawanLuar->tgl_lahir)) : '' }}" />
                                            <div class="fv-plugins-message-container invalid-feedback error-text tgl_lahir_error"></div>
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
                                                        <input class="form-check-input" type="radio" value="1" name="sex" id="sex_m"
                                                            {{ $karyawanLuar->sex == '1' ? 'checked' : '' }}/>
                                                        <label class="form-check-label" for="sex_m">Laki-laki</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto fw-row">
                                                    <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                        <input class="form-check-input" type="radio" value="0" name="sex" id="sex_f"
                                                            {{ $karyawanLuar->sex == '0' ? 'checked' : '' }}/>
                                                        <label class="form-check-label" for="sex_f">Perempuan</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="fv-plugins-message-container invalid-feedback error-text sex_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="ktp">
                                                No. KTP
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="ktp" 
                                                id="ktp" placeholder="No. KTP" value="{{ $karyawanLuar->ktp }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text ktp_error"></div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="email">
                                                Email
                                            </label>
                                            <input type="email" class="form-control form-control-solid" name="email" 
                                                id="email" placeholder="Email" value="{{ $karyawanLuar->email }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text email_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-12 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="alamat">
                                                Alamat
                                            </label>
                                            <textarea class="form-control form-control-solid" name="alamat" id="alamat" rows="3" 
                                                onkeyup="this.value=this.value.replace(/\b\w/g, l => l.toUpperCase())">{{ $karyawanLuar->alamat }}</textarea>
                                            <div class="fv-plugins-message-container invalid-feedback error-text alamat_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="provinsi">
                                                Provinsi
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih provinsi" data-allow-clear="true"
                                                name="provinsi" id="provinsi">
                                                <option></option>
                                                @foreach ($provinsi as $item)
                                                    <option value="{{ $item->kd_propinsi }}"
                                                        {{ $karyawanLuar->provinsi == $item->kd_propinsi ? 'selected' : '' }}>
                                                        {{ $item->propinsi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text provinsi_error"></div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="kabupaten">
                                                Kabupaten/Kota
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih kabupaten/kota" data-allow-clear="true"
                                                name="kabupaten" id="kabupaten">
                                                <option></option>
                                                @if(isset($kabupaten) && count($kabupaten) > 0)
                                                    @foreach ($kabupaten as $item)
                                                        <option value="{{ $item->kd_kabupaten }}"
                                                            {{ $karyawanLuar->kabupaten == $item->kd_kabupaten ? 'selected' : '' }}>
                                                            {{ $item->kabupaten }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text kabupaten_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="kecamatan">
                                                Kecamatan
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih kecamatan" data-allow-clear="true"
                                                name="kecamatan" id="kecamatan">
                                                <option></option>
                                                @if(isset($kecamatan) && count($kecamatan) > 0)
                                                    @foreach ($kecamatan as $item)
                                                        <option value="{{ $item->kd_kecamatan }}"
                                                            {{ $karyawanLuar->kecamatan == $item->kd_kecamatan ? 'selected' : '' }}>
                                                            {{ $item->kecamatan }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text kecamatan_error"></div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="kelurahan">
                                                Kelurahan
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih kelurahan" data-allow-clear="true"
                                                name="kelurahan" id="kelurahan">
                                                <option></option>
                                                @if(isset($kelurahan) && count($kelurahan) > 0)
                                                    @foreach ($kelurahan as $item)
                                                        <option value="{{ $item->kd_kelurahan }}"
                                                            {{ $karyawanLuar->kelurahan == $item->kd_kelurahan ? 'selected' : '' }}>
                                                            {{ $item->kelurahan }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text kelurahan_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Step 1-->

                        <!--begin::Step 2-->
                        <div class="flex-column" data-kt-stepper-element="content">
                            <div class="card card-dashed">
                                <div class="card-header">
                                    <h3 class="card-title">Akun Pegawai Luar</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Konten Step 2 sama seperti create dengan value yang sudah diisi -->
                                    <div class="row g-5 mb-5">
                                        <div class="col-md-3 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Warna Kulit
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih warna kulit" data-allow-clear="true"
                                                name="warna_kulit" id="warna_kulit">
                                                <option></option>
                                                @foreach ($warnaKulit as $item)
                                                    <option value="{{ $item->kd_kulit }}"
                                                        {{ $karyawanLuar->warna_kulit == $item->kd_kulit ? 'selected' : '' }}>
                                                        {{ $item->kulit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text warna_kulit_error"></div>
                                        </div>

                                        <div class="col-md-3 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Suku Bangsa
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih suku bangsa" data-allow-clear="true"
                                                name="suku_bangsa" id="suku_bangsa">
                                                <option></option>
                                                @foreach ($sukuBangsa as $item)
                                                    <option value="{{ $item->kd_suku }}"
                                                        {{ $karyawanLuar->suku_bangsa == $item->kd_suku ? 'selected' : '' }}>
                                                        {{ $item->suku }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text suku_bangsa_error"></div>
                                        </div>

                                        <div class="col-md-3 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Kebangsaan
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih kebangsaan" data-allow-clear="true"
                                                name="kebangsaan" id="kebangsaan">
                                                <option></option>
                                                @foreach ($kebangsaan as $item)
                                                    <option value="{{ $item->kd_bangsa }}"
                                                        {{ $karyawanLuar->kebangsaan == $item->kd_bangsa ? 'selected' : '' }}>
                                                        {{ $item->kebangsaan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text kebangsaan_error"></div>
                                        </div>

                                        <div class="col-md-3 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Agama
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih agama" data-allow-clear="true"
                                                name="agama" id="agama">
                                                <option></option>
                                                @foreach ($agama as $item)
                                                    <option value="{{ $item->kd_agama }}"
                                                        {{ $karyawanLuar->agama == $item->kd_agama ? 'selected' : '' }}>
                                                        {{ $item->agama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text agama_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-4 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Tinggi Badan (CM)
                                            </label>
                                            <input type="number" class="form-control form-control-solid" name="tinggi_badan" id="tinggi_badan" 
                                                placeholder="Tinggi (CM)" maxlength="3" value="{{ $karyawanLuar->tinggi_badan }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text tinggi_badan_error"></div>
                                        </div>

                                        <div class="col-md-4 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Berat Badan (KG)
                                            </label>
                                            <input type="number" class="form-control form-control-solid" name="berat_badan" id="berat_badan" 
                                                placeholder="Berat (KG)" maxlength="3" value="{{ $karyawanLuar->berat_badan }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text berat_badan_error"></div>
                                        </div>

                                        <div class="col-md-4 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Golongan Darah
                                            </label>
                                            <select class="form-select form-select-solid" data-control="select2" 
                                                data-placeholder="Pilih golongan darah" data-allow-clear="true"
                                                name="golongan_darah" id="golongan_darah">
                                                <option></option>
                                                @foreach ($golonganDarah as $item)
                                                    <option value="{{ $item->kode }}"
                                                        {{ $karyawanLuar->golongan_darah == $item->kode ? 'selected' : '' }}>
                                                        {{ $item->jenis }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="fv-plugins-message-container invalid-feedback error-text golongan_darah_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-12 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                NPWP
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="npwp" 
                                                id="npwp" placeholder="NPWP" value="{{ $karyawanLuar->npwp }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text npwp_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                No. Kartu Keluarga
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="no_kk" 
                                                id="no_kk" placeholder="No. KK" value="{{ $karyawanLuar->no_kk }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text no_kk_error"></div>
                                        </div>

                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Nama Ibu Kandung
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="nama_ibu" id="nama_ibu" 
                                                placeholder="Nama Ibu Kandung" onkeyup="this.value = this.value.toUpperCase();"
                                                value="{{ $karyawanLuar->nama_ibu }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text nama_ibu_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-6 fv-row">
                                            <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                No. HP/Telepon
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="no_hp" 
                                                id="no_hp" placeholder="No. HP" value="{{ $karyawanLuar->no_hp }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text no_hp_error"></div>
                                        </div>

                                        <div class="col-md-6 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                No. Alternatif
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="hp_alternatif" 
                                                id="hp_alternatif" placeholder="No. Alternatif" value="{{ $karyawanLuar->hp_alternatif }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text hp_alternatif_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-12 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Rekening BSI
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="bsi" id="bsi" 
                                                placeholder="No. Rekening BSI" maxlength="10" value="{{ $karyawanLuar->bsi }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text bsi_error"></div>
                                        </div>
                                    </div>

                                    <div class="row g-5 mb-5">
                                        <div class="col-md-12 fv-row">
                                            <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                                Rekening BPD Aceh
                                            </label>
                                            <input type="text" class="form-control form-control-solid" name="bpd_aceh" id="bpd_aceh" 
                                                placeholder="No. Rekening BPD" value="{{ $karyawanLuar->bpd_aceh }}"/>
                                            <div class="fv-plugins-message-container invalid-feedback error-text bpd_aceh_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Step 2-->
                    </div>
                    <!--end::Group-->

                    <!--begin::Actions-->
                    <div class="d-flex flex-stack">
                        <!--begin::Wrapper-->
                        <div class="me-2">
                            <button type="button" class="btn btn-light btn-active-light-primary fw-semibold fs-6 px-6 me-1" data-kt-stepper-action="previous">
                                Back
                            </button>
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Wrapper-->
                        <div>
                            <button type="button" class="btn btn-primary fw-semibold fs-6 px-8 me-1" data-kt-stepper-action="next" id="kt_stepper_example_basic_next">
                                Continue
                            </button>

                            <button type="submit" class="btn btn-primary fw-semibold fs-6 px-8" data-kt-stepper-action="submit" style="display: none;" id="btn-submit">
                                <span class="indicator-label">Update</span>
                                <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Actions-->
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Stepper element
        var element = document.querySelector("#kt_stepper_example_basic");

        // Initialize Stepper
        var stepper = new KTStepper(element);

        // Handle next step
        stepper.on("kt.stepper.next", function (stepper) {
            stepper.goNext(); // go next step
            
            // Show submit button on step 2 (index 1)
            if (stepper.getCurrentStepIndex() === 2) {
                document.getElementById('kt_stepper_example_basic_next').style.display = 'none';
                document.getElementById('btn-submit').style.display = 'inline-block';
            }
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
            stepper.goPrevious(); // go previous step
            
            // Hide submit button if not on step 2
            if (stepper.getCurrentStepIndex() < 2) {
                document.getElementById('kt_stepper_example_basic_next').style.display = 'inline-block';
                document.getElementById('btn-submit').style.display = 'none';
            }
        });

        // Input masks untuk tanggal
        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",
        }).mask("#tmt, #tmt_akhir, #tgl_sk, #tgl_lahir");

        // Mask untuk KTP
        Inputmask({
            "mask": "9999999999999999",
            "placeholder": "________________",
        }).mask("#ktp");

        // Mask untuk no kartu keluarga
        Inputmask({
            "mask": "9999999999999999",
            "placeholder": "________________",
        }).mask("#no_kk");

        // Mask untuk NPWP
        Inputmask({
            "mask": "99.999.999.9-999.999",
        }).mask("#npwp");

        // Mask untuk nomor HP
        Inputmask({
            "mask": "9999 9999 9999",
        }).mask("#no_hp, #hp_alternatif");

        $(document).ready(function() {
            // Provinsi change event
            $('#provinsi').on('change', function() {
                var kd_propinsi = $(this).val();
                var url = "{{ route('lokasi.kabupaten', ':kd_propinsi') }}";
                if (kd_propinsi) {
                    $.ajax({
                        url: url.replace(':kd_propinsi', kd_propinsi),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#kabupaten').empty().append('<option></option>');
                            $.each(data, function(key, value) {
                                $('#kabupaten').append('<option value="'+ value.kd_kabupaten +'">'+ value.kabupaten +'</option>');
                            });
                            $('#kecamatan').empty().append('<option></option>');
                            $('#kelurahan').empty().append('<option></option>');
                        }
                    });
                } else {
                    $('#kabupaten').empty().append('<option></option>');
                    $('#kecamatan').empty().append('<option></option>');
                    $('#kelurahan').empty().append('<option></option>');
                }
            });

            // Kabupaten change event
            $('#kabupaten').on('change', function() {
                var kd_kabupaten = $(this).val();
                var url = "{{ route('lokasi.kecamatan', ':kd_kabupaten') }}";
                if (kd_kabupaten) {
                    $.ajax({
                        url: url.replace(':kd_kabupaten', kd_kabupaten),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#kecamatan').empty().append('<option></option>');
                            $.each(data, function(key, value) {
                                $('#kecamatan').append('<option value="'+ value.kd_kecamatan +'">'+ value.kecamatan +'</option>');
                            });
                            $('#kelurahan').empty().append('<option></option>');
                        }
                    });
                } else {
                    $('#kecamatan').empty().append('<option></option>');
                    $('#kelurahan').empty().append('<option></option>');
                }
            });

            // Kecamatan change event
            $('#kecamatan').on('change', function() {
                var kd_kecamatan = $(this).val();
                var url = "{{ route('lokasi.kelurahan', ':kd_kecamatan') }}";
                if (kd_kecamatan) {
                    $.ajax({
                        url: url.replace(':kd_kecamatan', kd_kecamatan),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#kelurahan').empty().append('<option></option>');
                            $.each(data, function(key, value) {
                                $('#kelurahan').append('<option value="'+ value.kd_kelurahan +'">'+ value.kelurahan +'</option>');
                            });
                        }
                    });
                } else {
                    $('#kelurahan').empty().append('<option></option>');
                }
            });

            // Toggle SK fields
            $('#no_sk_check').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.sk-field').removeClass('d-none');
                } else {
                    $('.sk-field').addClass('d-none');
                }
            });

            // Toggle Keterangan fields
            $('#keterangan_check').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.keterangan-field').removeClass('d-none');
                } else {
                    $('.keterangan-field').addClass('d-none');
                }
            });            
        });

        // Form submission
        $('#edit-karyawan-luar').on('submit', function(e) {
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
                    $(form).find('#btn-submit .indicator-label').hide();
                },
                complete: function() {
                    $(form).find('#btn-submit .indicator-label').show();
                    loadingIndicator.remove();
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                        }).then(function() {
                            window.location.href = "{{ route('admin.karyawan-luar.index') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan pada server, silakan coba lagi nanti.',
                    });
                }
            });
        });

        // Validation saat tombol next
        $('#kt_stepper_example_basic_next').on('click', function() {
            var status_kerja = $('#status_kerja').val();
            var status_pegawai = $('#status_pegawai').val();
            var nama = $('#nama').val();
            var tempat_lahir = $('#tempat_lahir').val();
            var tgl_lahir = $('#tgl_lahir').val();
            var sex = $('input[name="sex"]:checked').val();

            var isValid = true;

            if (status_kerja == '') {
                $('.status_kerja_error').html('Status kerja tidak boleh kosong');
                isValid = false;
            } else {
                $('.status_kerja_error').html('');
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

            if (tempat_lahir == '') {
                $('.tempat_lahir_error').html('Tempat lahir tidak boleh kosong');
                isValid = false;
            } else {
                $('.tempat_lahir_error').html('');
            }

            if (tgl_lahir == '') {
                $('.tgl_lahir_error').html('Tanggal lahir tidak boleh kosong');
                isValid = false;
            } else {
                $('.tgl_lahir_error').html('');
            }

            if (!sex) {
                $('.sex_error').html('Jenis kelamin harus dipilih');
                isValid = false;
            } else {
                $('.sex_error').html('');
            }

            if (!isValid) {
                Swal.fire({
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
    </script>
@endpush
