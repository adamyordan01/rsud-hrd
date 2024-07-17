@extends('layouts.backend', ['title' => 'Daftar Mutasi (Proses)'])

@inject('DB', 'Illuminate\Support\Facades\DB')

@php
    $jabatan = Auth::user()->kd_jabatan_struktural;
    $ruangan = Auth::user()->kd_ruangan;
@endphp

@push('styles')
    <style>

    </style>    
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Mutasi
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="/metronic8/demo39/index.html" class="text-muted text-hover-primary">
                                Dashboard 
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Mutasi (Proses)
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">

                    <a href="javascript:void(0)" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_add_sk">
                        <i class="ki-duotone ki-plus-square fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Tambah SK Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content  flex-column-fluid ">
        <div id="kt_app_content_container" class="app-container  container-fluid ">
            <div class="card-rounded bg-light d-flex flex-stack flex-wrap p-5 mb-8">
                <div class="hover-scroll-x">
                    <ul class="nav flex-nowrap border-transparent fw-bold">
                        <li class="nav-item my-1">
                            <a
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi.*') ? 'active' : '' }}"
                                href="{{ route('admin.mutasi.index') }}"
                            >
                                <i class="ki-duotone ki-arrows-loop fs-1"><span class="path1"></span><span class="path2"></span></i>
                                Mutasi (Nota)
                            </a>
                        </li>
                        <li class="nav-item my-1">
                            <a 
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase"
                                href=""
                            >
                            <i class="ki-duotone ki-arrows-loop fs-1"><span class="path1"></span><span class="path2"></span></i>
                            Mutasi (SK)
                            </a>
                        </li>
                        <li class="nav-item my-1">
                            <a
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-pending.*') ? 'active' : '' }}"
                                href="{{ route('admin.mutasi-pending.index') }}"
                            >
                            <i class="ki-duotone ki-abstract-5 fs-1"><span class="path1"></span><span class="path2"></span></i>
                                <span class="menu-title me-2">
                                    Daftar Mutasi (Tetunda)
                                </span>
                                <span class="menu-badge">
                                    <span class="badge badge-sm badge-circle badge-danger count-status-proses">
                                        {{ $totalMutasiPending }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item my-1">
                            <a
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-on-process.*') ? 'active' : '' }}"
                                href="{{ route('admin.mutasi-on-process.index') }}"
                            >
                                <i class="ki-duotone ki-loading fs-1"><span class="path1"></span><span class="path2"></span></i>
                                <span class="menu-title me-2">
                                    Daftar Mutasi (Proses)
                                </span>
                                <span class="menu-badge">
                                    <span class="badge badge-sm badge-circle badge-danger count-status-proses">
                                        {{ $totalMutasiOnProcess }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item my-1">
                            <a
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-verifikasi.*') ? 'active' : '' }}"
                                href="{{ route('admin.mutasi-verifikasi.index') }}"
                            >
                                <i class="ki-duotone ki-double-check fs-1"><span class="path1"></span><span class="path2"></span></i>
                                Daftar Mutasi (Verifikasi)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mb-5">
                <div class="card-body p-lg-12">
                    <div class="row g-5 mb-5" id="list-mutasi-verifikasi">
                        <div class="table-responsive">
                            <table class="table table-bordered table-stripped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID Peg.</th>
                                        <th class="text-center vertical-align-middle">
                                            Nama <br>
                                            Tempat, Tanggal Lahir <br>
                                            NIP. / No. KARPEG
                                        </th>
                                        <th>
                                            Jabatan Lama <br>
                                            Jenis Tenaga Lama <br>
                                            Pada Ruangan Lama
                                        </th>
                                        <th>
                                            Jabatan Baru <br>
                                            Jenis Tenaga Baru <br>
                                            Pada Ruangan Baru
                                        </th>
                                        <th class="min-w-125px text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($getMutasi as $item)
                                        <tr style="background: #1b84ff;">
                                            <td style="vertical-align: middle" colspan="5">
                                                {{-- <b class="text-white">
                                                    Kode Mutasi - {{ $item->kd_mutasi }}
                                                </b> --}}
                                                {{-- kode mutasi pada sisi kiri, dan button edit pada sisi kanan --}}
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-white fw-bold fs-5">
                                                        Kode Mutasi - {{ $item->kd_mutasi }}
                                                    </span>
                                                    <a
                                                        href="{{ route('admin.mutasi-on-process.index') }}"
                                                        class="btn btn-success btn-sm btn-active-light-succes me-2 mb-2"
                                                    >
                                                        <i class="ki-duotone ki-notepad-edit fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        @php
                                            // select * from VIEW_PROSES_MUTASI where KD_MUTASI = '".$dataMutasi['KD_MUTASI']."' and KD_TAHAP_MUTASI = 1
                                            $query = $DB::table('view_proses_mutasi')
                                                ->where('kd_mutasi', $item->kd_mutasi)
                                                ->where('kd_tahap_mutasi', 1)
                                                ->get();
                                        @endphp

                                        @foreach ($query as $data)
                                            @php
                                                $gelar_depan = $data->gelar_depan ? $data->gelar_depan . ' ' : '';
                                                $gelar_belakang = $data->gelar_belakang ? '' . $data->gelar_belakang : '';
                                                $nama = $gelar_depan . $data->nama . $gelar_belakang;

                                                if ($data->kd_status_kerja == 1 || $data->kd_status_kerja == 7) {
                                                    $asn = "<br>" . $data->nip_baru . "<br>" . $data->no_karpeg;
                                                } else {
                                                    $asn = "";
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $data->kd_karyawan }}</td>
                                                <td>
                                                    {{ $nama }} <br>
                                                    {{ $data->tempat_lahir }}, {{ date('d-m-Y', strtotime($data->tgl_lahir)) }}
                                                    {!! $asn !!}
                                                </td>
                                                <td>
                                                    {{ $data->jab_struk_lama }} <br>
                                                    {{ $data->sub_detail_lama }} <br>
                                                    {{ $data->ruangan_lama }}
                                                </td>
                                                <td>
                                                    {{ $data->jab_struk_baru }} <br>
                                                    {{ $data->sub_detail_baru }} <br>
                                                    {{ $data->ruangan_baru }}    
                                                </td>
                                                <td>
                                                    @if ($data->verif_1 == null)
                                                        @if ($jabatan == 19 || $ruangan == 57)
                                                            <a
                                                                href="javascript:void(0)"
                                                                class="btn btn-info btn-sm d-block mb-2"
                                                                title="Verifikasi Ka.Sub.Bag. Kepeg."
                                                                data-id="{{ $data->kd_mutasi }}"
                                                                data-karyawan="{{ $data->kd_karyawan }}"
                                                                data-url="{{ route('admin.mutasi-on-process.first-verification') }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#kt_modal_verif"
                                                                id="verif1"
                                                            >
                                                                <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i> 
                                                                Verifikasi Ka.Sub.Bag. Kepeg.
                                                            </a>
                                                        @endif
                                                    @elseif($data->verif_2 == null)
                                                        @if ($jabatan == 7 || $ruangan == 57)
                                                            <a
                                                                href="javascript:void(0)"
                                                                class="btn btn-primary btn-sm d-block mb-2"
                                                                title="Verifikasi Kabag. TU"
                                                                data-id="{{ $data->kd_mutasi }}"
                                                                data-karyawan="{{ $data->kd_karyawan }}"
                                                                data-url="{{ route('admin.mutasi-on-process.second-verification') }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#kt_modal_verif"
                                                                id="verif2"
                                                            >
                                                                <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
                                                                Verifikasi Kabag. TU
                                                            </a>
                                                        @endif
                                                    @elseif($data->verif_3 == null)
                                                        @if ($jabatan == 3 || $ruangan == 57)
                                                            <a
                                                                href="javascript:void(0)"
                                                                class="btn btn-warning btn-sm d-block mb-2"
                                                                title="Menunggu verifikasi Wadir ADM dan Umum"
                                                                data-id="{{ $data->kd_mutasi }}"
                                                                data-karyawan="{{ $data->kd_karyawan }}"
                                                                data-url="{{ route('admin.mutasi-on-process.third-verification') }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#kt_modal_verif"
                                                                id="verif3"
                                                            >
                                                                <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
                                                                Menunggu verifikasi Wadir ADM dan Umum
                                                            </a>
                                                        @endif
                                                    @elseif($data->verif_4 == null)
                                                        @if ($jabatan == 3 || $ruangan == 57)
                                                            <a
                                                                href="javascript:void(0)"
                                                                class="btn btn-success btn-sm d-block mb-2"
                                                                title="Menunggu verifikasi Direktur"
                                                                data-id="{{ $data->kd_mutasi }}"
                                                                data-karyawan="{{ $data->kd_karyawan }}"
                                                                data-url="{{ route('admin.mutasi-on-process.fourth-verification') }}"
                                                                id="verif4"
                                                            >
                                                                <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
                                                                Menunggu verifikasi Direktur
                                                            </a>
                                                        @endif
                                                    @endif

                                                    <a
                                                        href="{{ route('admin.mutasi-on-process.index') }}"
                                                        class="btn btn-primary btn-sm d-block mb-2"
                                                    >
                                                        <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                        Cetak Nota
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi -->
    <div class="modal fade" id="kt_modal_verif" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_verif_header">
                    <h2 class="fw-bold" id="verif-title"></h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-verif="close" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body p-10 mb-7" id="rincian-verif"></div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-verif="cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" data-kt-menu-modal-action-verif="submit">
                        <span class="indicator-label">Verifikasi</span>
                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi 4 -->
    <div class="modal fade" id="kt_modal_verif_4" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_verif_4_header">
                    <h2 class="fw-bold" id="verif-title-4"></h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-verif-4="close" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body p-10 mb-7" id="rincian-verif-4"></div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-verif-4="cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" data-kt-menu-modal-action-verif-4="submit">
                        <span class="indicator-label">Verifikasi</span>
                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Modal - Finalisasi TTE-->
    <div class="modal fade" id="kt_modal_finalisasi" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_finalisasi_header">
                    <h2 class="fw-bold" id="finalisasi-title">Proses TTE Nota Tugas</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-finalisasi="close" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body px-5 mb-7" id="finalisasi">
                    <form action="#">
                        @csrf
                        <input type="hidden" name="kd_mutasi_finalisasi" id="kd_mutasi_finalisasi">
                        <input type="hidden" name="kd_karyawan_finalisasi" id="kd_karyawan_finalisasi">
                        <div class="d-flex flex-column fv-row mb-5">
                            <label
                                class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Tanggal Tanda Tangan SK
                            </label>
                            <input
                                class="form-control form-control-solid"
                                name="tanggal"
                                id="tanggal"
                                placeholder="Pilih Tanggal Tanda Tangan SK"
                            />
                            <div class="fv-plugins-message-container invalid-feedback error-text tanggal_error"></div>
                        </div>

                        <div class="d-flex flex-column fv-row mb-5">
                            <label
                                class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Passphrase (Password TTE)
                            </label>
                            
                            <div class="position-relative">
                                <input
                                    class="form-control form-control-solid"
                                    type="password"
                                    placeholder="Masukkan passphrase"
                                    name="passphrase"
                                    id="passphrase"
                                    autocomplete="off"
                                />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                    <i class="ki-outline ki-eye-slash fs-2"></i>
                                    <i class="ki-outline ki-eye fs-2 d-none"></i>
                                </span>
                            </div>
                            <div class="fv-plugins-message-container invalid-feedback error-text passphrase_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-finalisasi="cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" data-kt-menu-modal-action-finalisasi="submit">
                        <span class="indicator-label">Proses TTE</span>
                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Finalisasi TTE-->
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('[data-kt-menu-modal-verif="close"], [data-kt-menu-modal-verif="cancel"]').on('click', function () {
                $('#kt_modal_verif').modal('hide');
            });

            // cancel button specific to modal verif
            $('[data-kt-menu-modal-verif="cancel"]').on('click', function () {
                $('#kt_modal_verif').modal('hide');
                $('#kt_rincian_verif_1_table').html('');
            });

            // sample buttons to open modal with specific content
            $('#verif1').on('click', function () {
                showModal('Verifikasi Kasubbag. Kepegawaian');
            });

            $('#verif2').on('click', function () {
                showModal('Verifikasi Kabag. TU');
            });

            $('#verif3').on('click', function () {
                showModal('Verifikasi Wadir ADM dan Umum');
            });

            var weekend = [0, 6];
            $('#tanggal').flatpickr({
                altInput: true,
                altFormat: "d-m-Y",
                dateFormat: "Y-m-d",
                disable: [
                    function(date) {
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ],
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        longhand: [
                            'Minggu',
                            'Senin',
                            'Selasa',
                            'Rabu',
                            'Kamis',
                            'Jumat',
                            'Sabtu',
                        ],
                    },
                    months: {
                        shorthand: [
                            'Jan',
                            'Feb',
                            'Mar',
                            'Apr',
                            'Mei',
                            'Jun',
                            'Jul',
                            'Agu',
                            'Sep',
                            'Okt',
                            'Nov',
                            'Des',
                        ],
                        longhand: [
                            'Januari',
                            'Februari',
                            'Maret',
                            'April',
                            'Mei',
                            'Juni',
                            'Juli',
                            'Agustus',
                            'September',
                            'Oktober',
                            'November',
                            'Desember',
                        ],
                    },
                },
            });
            // $('#tgl_ttd_sk').flatpickr();

            // data-kt-password-meter-control="visibility"
            $(document).on('click', '[data-kt-password-meter-control="visibility"]', function () {
                var input = $(this).closest('.position-relative').find('input');
                var icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.toggleClass('d-none');
                } else {
                    input.attr('type', 'password');
                    icon.toggleClass('d-none');
                }
            });
        });

        $(document).on('click', '#verif1, #verif2, #verif3', function () {
            var id = $(this).data('id');
            var karyawan = $(this).data('karyawan');
            var url = $(this).data('url');

            let title;

            if ($(this).attr('id') === 'verif1') {
                title = 'Verifikasi Kasubbag. Kepegawaian';
            } else if ($(this).attr('id') === 'verif2') {
                title = 'Verifikasi Kabag. TU';
            } else if ($(this).attr('id') === 'verif3') {
                title = 'Verifikasi Wadir ADM dan Umum';
            }
            
            $.ajax({
                url: '{{ route("admin.mutasi-on-process.rincian") }}',
                method: 'GET',
                data: {
                    kd_mutasi: id,
                    kd_karyawan: karyawan
                },
                beforeSend: function() {
                    $('#rincian-verif').html('<div class="text-center"><span class="spinner-border spinner-border-lg align-center"></span></div>');
                },
                success: function(response) {
                    $('#rincian-verif').html(response);
                    showModal(title);
                    handleVerificationSubmit('[data-kt-menu-modal-action-verif="submit"]', url);
                },
                error: function(xhr) {
                    if (xhr.status === 419) {
                        refreshCsrfToken().done(function () {
                            toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                        })
                    } else if (xhr.status === 500) {
                        toastr.error('Internal Server Error', xhr.statusText);
                    }
                }
            });
        });

        $(document).on('click', '#verif4', function () {
            var id = $(this).data('id');
            var karyawan = $(this).data('karyawan');
            var url = $(this).data('url');

            $.ajax({
                type: 'get',
                url: '{{ route("check-status-bsre") }}',
                dataType: 'json',
                beforeSend: function() {
                    // buat loading indicator didalam modal
                    Swal.fire({
                        title: 'Memeriksa status BSRE',
                        text: 'Mohon tunggu sebentar',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.code == 200) {
                        Swal.close();

                        $.ajax({
                            url: '{{ route("admin.mutasi-on-process.rincian") }}',
                            method: 'GET',
                            data: {
                                kd_mutasi: id,
                                kd_karyawan: karyawan
                            },
                            beforeSend: function() {
                                $('#rincian-verif').html('<div class="text-center"><span class="spinner-border spinner-border-lg align-center"></span></div>');
                            },
                            success: function(response) {
                                $('#rincian-verif-4').html(response);
                                $('#verif-title-4').text('Verifikasi Direktur');
                                $('#kt_modal_verif_4').modal('show');
                            },
                            error: function(xhr) {
                                if (xhr.status === 419) {
                                    refreshCsrfToken().done(function () {
                                        toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                                    })
                                } else if (xhr.status === 500) {
                                    toastr.error('Internal Server Error', xhr.statusText);
                                }
                            }
                        });
                    } else {
                        toastr.error(`${response.message}`, 'Error');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 419) {
                        refreshCsrfToken().done(function () {
                            toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                        })
                    } else if (xhr.status === 500) {
                        toastr.error('Internal Server Error', xhr.statusText);
                    }
                }
            });
        })

        $(document).on('click', '[data-kt-menu-modal-action-verif-4="submit"]', function (e) {
            e.preventDefault();
            var modal = $(this).closest('.modal');
            var form = modal.find('form')[0];

            var kd_mutasi = $(form).find('input[name="kd_mutasi"]').val();
            var kd_karyawan = $(form).find('input[name="kd_karyawan"]').val();

            // console.log(`kd_mutasi: ${kd_mutasi}, kd_karyawan: ${kd_karyawan}`);

            var data = new FormData(form);

            var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
            $(form).find('.btn-primary').append(loadingIndicator);

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang diverifikasi tidak dapat diubah kembali!",
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, verifikasi!',
                cancelButtonText: 'Batal',
                // buat confirm button disebeleah kanan dan cancel button disebelah kiri
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-light btn-active-danger'
                },
                showLoaderOnConfirm: true,
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.mutasi-on-process.fourth-verification") }}',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        data: data,
                        beforeSend: function() {
                            loadingIndicator.show();

                            $(form).find('.btn-primary').attr('disabled', true);
                            $(form).find('.btn-primary .indicator-label').hide();
                        },
                        success: function(response) {
                            $('#kd_mutasi').val(kd_mutasi);
                            $('#kd_karyawan').val(kd_karyawan);
                            if (response.code == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    allowOutsideClick: false,
                                    showConfirmButton: true,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // close modal verif_4
                                        $('#kt_modal_verif_4').modal('hide');
                                        // show finalisasi modal
                                        $('#finalisasi-title').text('Proses TTE Nota Tugas');
                                        $('#kt_modal_finalisasi').modal('show');
                                    }
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 419) {
                                refreshCsrfToken().done(function () {
                                    toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                                })
                            } else if (xhr.status === 500) {
                                toastr.error('Internal Server Error', xhr.statusText);
                            } else {
                                handleFormErrors(xhr.responseJSON.errors);
                            }
                        },
                        complete: function() {
                            loadingIndicator.hide();

                            $(form).find('.btn-primary').attr('disabled', false);
                            $(form).find('.btn-primary .indicator-label').show();
                        }
                    })
                }
            })
        })

        $(document).on('click', '[data-kt-menu-modal-action-finalisasi="submit"]', function (e) {
            e.preventDefault();
            var kd_mutasi = $('#kd_mutasi').val();
            var kd_karyawan = $('#kd_karyawan').val();
            var modal = $(this).closest('.modal');
            var form = modal.find('form')[0];
            var data = new FormData(form);

            // masukkan data urut dan tahun ke dalam form data
            data.append('kd_mutasi', kd_mutasi);
            data.append('kd_karyawan', kd_karyawan);

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang akan ditanadatangani tidak dapat diubah kembali!",
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, finalisasi!',
                cancelButtonText: 'Batal',
                // buat confirm button disebeleah kanan dan cancel button disebelah kiri
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-light btn-active-danger'
                },
                showLoaderOnConfirm: true,
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.mutasi-on-process.finalisasi") }}',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        data: data,
                        beforeSend: function() {
                            // buat loading indicator
                            Swal.fire({
                                title: 'Proses TTE Nota Tugas',
                                text: 'Mohon tunggu sebentar',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            console.log(`Response: ${response.code} - ${response.message}`);
                            // $('#urut_rincian_verif').val(urut);
                            // $('#tahun_rincian_verif').val(tahun);
                            $('#kd_mutasi').val(kd_mutasi);
                            $('#kd_karyawan').val(kd_karyawan);

                            if (response.code == 200) {
                                console.log('Success execute the sweet alert');
                                toastr.success(response.message, 'Success');

                                // tutup modal finalisasi, kemudian setelah 1.5 detik reload halaman
                                setTimeout(() => {
                                    $('#kt_modal_finalisasi').modal('hide');
                                    window.location.reload();
                                }, 1500);
                            } else {
                                toastr.error(response.message, 'Error');
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            // console.log(xhr);

                            if (xhr.status === 419) {
                                refreshCsrfToken().done(function () {
                                    toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                                })
                            } else if (xhr.status === 500) {
                                var response = xhr.responseJSON;

                                if (response && response.message && response.message.includes('Passphrase anda salah')) {
                                    toastr.error('Passphrase (Kata Sandi TTE) anda salah', 'Error');
                                } else {
                                    toastr.error('Internal Server Error', xhr.statusText);

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message || 'Terjadi kesalahan saat melakukan proses TTE SK',
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                        }
                                    });
                                }
                            } else {
                                handleFormErrors(xhr.responseJSON.errors);
                            }
                        },
                        complete: function() {
                            Swal.close();
                        }
                    })
                }
            })
        });

        function showModal(title) {
            $('#verif-title').text(title);
            // $('#rincian-verif').html($(rincianId).html());
            $('#kt_modal_verif').modal('show');
        }

        function handleFormErrors(errors) {
            if (!$.isEmptyObject(errors)) {
                $('.error-text').remove();

                $.each(errors, function(key, value) {
                    $('#' + key).closest('.fv-row').append('<div class="fv-plugins-message-container invalid-feedback error-text '+ key +'_error">' + value + '</div>');
                })
            }
        }

        function handleVerificationSubmit(buttonSelector, url) {
            $(document).on('click', buttonSelector, function (e) {
                e.preventDefault();
                var modal = $(this).closest('.modal');
                var form = modal.find('form')[0];
                var data = new FormData(form);

                var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
                $(form).find('.btn-primary').append(loadingIndicator);

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data yang diverifikasi tidak dapat diubah kembali!",
                    icon: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: 'Ya, verifikasi!',
                    cancelButtonText: 'Batal',
                    // buat confirm button disebeleah kanan dan cancel button disebelah kiri
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-light btn-active-danger'
                    },
                    showLoaderOnConfirm: true,
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'POST',
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            data: data,
                            beforeSend: function() {
                                loadingIndicator.show();

                                $(form).find('.btn-primary').attr('disabled', true);
                                $(form).find('.btn-primary .indicator-label').hide();
                            },
                            success: function(response) {
                                if (response.code == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.message,
                                        allowOutsideClick: false,
                                        showConfirmButton: true,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                        }
                                    })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500,
                                    });
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 419) {
                                    refreshCsrfToken().done(function () {
                                        toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                                    })
                                } else if (xhr.status === 500) {
                                    toastr.error('Internal Server Error', xhr.statusText);
                                } else {
                                    handleFormErrors(xhr.responseJSON.errors);
                                }
                            },
                            complete: function() {
                                loadingIndicator.hide();

                                $(form).find('.btn-primary').attr('disabled', false);
                                $(form).find('.btn-primary .indicator-label').show();
                            }
                        })
                    }
                })
            })
        }

        function refreshCsrfToken() {
            return $.get('/refresh-csrf').done(function(data) {
                $('meta[name="csrf-token"]').attr('content', data.csrf_token);

                // update @csrf too
                $('input[name="_token"]').val(data.csrf_token);

                // Update the token in the AJAX setup
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': data.csrf_token
                    }
                });
            });
        }
    </script>
@endpush