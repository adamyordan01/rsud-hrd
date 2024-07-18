@extends('layouts.backend', ['title' => 'Mutasi'])

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
                        Mutasi Karyawan
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a
                                href="{{ route('admin.dashboard.index') }}"
                                class="text-muted text-hover-primary"
                            >
                                Dashboard 
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Mutasi Nota
                        </li>
                    </ul>
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

            <form class="form" novalidate="novalidate" id="add-mutasi-nota" method="POST" action="{{ route('admin.mutasi.store') }}">
                @csrf
                <input type="hidden" name="kd_mutasi_form" id="kd_mutasi_form" value="">
                <input type="hidden" name="kd_karyawan_form" id="kd_karyawan_form" value="">
                <div class="card mb-5">
                    <div class="card-body p-lg-12">
                        <div class="row g-5 mb-5">
                            <div class="col-6 fv-row ps-0">
                                <input
                                    type="text"
                                    class="form-control form-control-solid"
                                    name="kd_karyawan"
                                    id="kd_karyawan"
                                    placeholder="Masukkan ID Pegawai"
                                    maxlength="6"
                                    {{-- onkeyup="getKaryawan(this.value)" --}}
                                />
                                <small style="font-style: italic">Tekan [Enter] untuk menambahkan pegawai yang akan dimutasi</small>
                                <div
                                    class="fv-plugins-message-container invalid-feedback error-text ktp_error">
                                </div>
                            </div>
                            <div class="col-6 fv-row">
                                <input type="text" class="form-control form-control-transparent fw-bold"
                                    id="load_pegawai" value=""
                                    readonly
                                />
                            </div>
                        </div>
                        <div class="row g-5 mb-5" id="list-mutasi-nota">
                            <table class="table table-bordered table-stripped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID Peg.</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Ruangan</th>
                                        <th>Sub Jenis Tenaga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada pegawai yang dipilih.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row g-5 mb-5" id="input-mutasi-nota">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body p-lg-12">
                                <div class="row g-5 mb-5">
                                    <div class="col-12 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Isi Nota No. 1 : 
                                        </label>
                                        <textarea class="form-control form-control-solid" name="isi_nota" id="isi_nota" rows="3"></textarea>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text isi_nota_error">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-5">
                                    <div class="col-12 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Isi Nota No. 2 : 
                                        </label>
                                        <textarea class="form-control form-control-solid" name="isi_nota_2" id="isi_nota_2" rows="3">Nota Tugas ini berlaku sejak tanggal ditetapkan.</textarea>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text isi_nota_2_error">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-5">
                                    <div class="col-md-8 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Jabatan Struktural / Non Struktural
                                        </label>
                                        <select class="form-select form-select-solid"
                                            name="jab_struk"
                                            id="jab_struk"
                                            data-control="select2"
                                        >
                                            <option value="">Pilih Jabatan</option>
                                            @foreach ($jabatanStruktural as $item)
                                                <option value="{{ $item->kd_jab_struk }}">{{ $item->jab_struk }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text nip_error">
                                        </div>
                                    </div>
                                    <div class="col-md-4 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center" for="nip_lama">
                                            TMT Jabatan
                                        </label>
                                        <input class="form-control form-control-solid" name="tmt_jabatan" id="tmt_jabatan" />
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text tmt_jabatan_error">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-5">
                                    <div class="col-12 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Sub Jenis Tenaga
                                        </label>
                                        <select class="form-select form-select-solid"
                                            name="sub_jenis_tenaga"
                                            id="sub_jenis_tenaga"
                                            data-control="select2"
                                        >
                                            <option value="">Pilih Sub Jenis Tenaga</option>
                                            @foreach ($subDetail as $item)
                                                <option value="{{ $item->sub_detail }}">{{ $item->sub_detail }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text sub_jenis_tenaga_error">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body p-lg-12">
                                <div class="row g-5 mb-5">
                                    <div class="col-12 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Departement
                                        </label>
                                        <select class="form-select form-select-solid"
                                            name="divisi"
                                            id="divisi"
                                            data-control="select2"
                                        >
                                            <option value="">Pilih Sub Jenis Tenaga</option>
                                            @foreach ($divisi as $item)
                                                <option value="{{ $item->kd_divisi }}">{{ $item->divisi }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text divisi_error">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-5">
                                    <div class="col-12 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Bagian / Bidang
                                        </label>
                                        <select 
                                            class="form-select form-select-solid"
                                            data-placeholder="Pilih Bagian / Bidang"
                                            name="unit"
                                            id="unit"
                                            data-control="select2"
                                        >
                                            <option value=""></option>
                                        </select>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text unit_error">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-5">
                                    <div class="col-12 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Sub Bag. / Sub Bid. / Instalasi / Unit
                                        </label>
                                        <select class="form-select form-select-solid"
                                            name="sub_unit"
                                            id="sub_unit"
                                            data-control="select2"
                                            data-placeholder="Pilih Sub Bag. / Sub Bid. / Instalasi / Unit"
                                        >
                                            <option value=""></option>
                                        </select>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text sub_unit_error">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-5">
                                    <div class="col-12 fv-row">
                                        <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Ruangan
                                        </label>
                                        <select class="form-select form-select-solid"
                                            name="ruangan"
                                            id="ruangan"
                                            data-control="select2"
                                            data-placeholder="Pilih Ruangan"
                                        >
                                            <option value="">Pilih Ruangan</option>
                                            @foreach ($ruangan as $item)
                                                <option value="{{ $item->kd_ruangan }}">{{ $item->ruangan }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text ruangan_error">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-5 mb-5">
                                    <div class="col-6 fv-row">
                                        <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Tanggal Tanda Tangan
                                        </label>
                                        <div class="row g-5 mb-5">
                                            <div class="col-auto fw-row">
                                                <div class="form-check form-check-custom form-check-solid form-check-sm me-6">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        value="1"
                                                        name="check_tgl_ttd"
                                                        id="check_tgl_ttd"
                                                        checked
                                                    />
                                                    <label class="form-check-label" for="check_tgl_ttd">
                                                        Sama dengan TMT jabatan
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text check_tgl_ttd_error">
                                        </div>
                                    </div>
                                    {{-- jika ceck_tgl_ttd di uncheck maka tampilkan div dibawah --}}
                                    <div class="col-6 fv-row d-none tgl-ttd">
                                        <label class="required fw-semibold fs-6 mb-2 d-flex align-items-center">
                                            Tanggal Tanda Tangan
                                        </label>
                                        <input type="text" class="form-control form-control-solid" name="tgl_ttd" id="tgl_ttd" />
                                        <div
                                            class="fv-plugins-message-container invalid-feedback error-text tgl_ttd_error">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- button submit dan reset --}}
                <div class="row g-5 mb-5">
                    <div class="col-12">
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-light fw-bold me-4">
                                Reset
                                <i class="ki-duotone ki-arrow-circle-left fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button type="submit" class="btn btn-primary fw-bold">
                                Proses
                                <i class="ki-duotone ki-send fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        // input mask for tmt_jabatan
        Inputmask("datetime", {
            inputFormat: "dd-mm-yyyy",
            separator: "-",
        }).mask("#tmt_jabatan");

        // input mask for tgl_ttd
        Inputmask("datetime", {
            inputFormat: "dd-mm-yyyy",
            separator: "-",
        }).mask("#tgl_ttd");

        $(document).ready(function() {
            // divisi on change
            $('#divisi').change(function() {
                var divisi = $(this).val();

                let url = "{{ route('admin.mutasi.get-unit-kerja', ['id' => ':divisi']) }}";
                url = url.replace(':divisi', divisi);

                $.ajax({
                    url: "{{ route('admin.mutasi.get-unit-kerja', '') }}/" + divisi,
                    type: "GET",
                    dataType: "json",
                    data: {
                        id: divisi
                    },
                    cache: false,
                    success: function(data) {
                        $('#unit').empty();
                        $('#unit').append('<option value="" selected disable>Pilih Bagian / Bidang</option>');
                        $.each(data, function(key, value) {
                            $('#unit').append('<option value="' + value.kd_unit_kerja + '">' + value.unit_kerja + '</option>');
                        });
                    }
                });
            });

            // unit on change, get divisi and kd unit kerja
            $('#unit').change(function() {
                var unit = $(this).val();
                var divisi = $('#divisi').val();
                
                let url = "{{ route('admin.mutasi.get-sub-unit-kerja', ['id' => ':unit', 'divisi' => ':divisi']) }}";
                url = url.replace(':unit', unit).replace(':divisi', divisi);

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    data: {
                        id: unit,
                        divisi: divisi
                    },
                    cache: false,
                    success: function(data) {
                        $('#sub_unit').empty();
                        $('#sub_unit').append('<option value="" selected disable>Pilih Sub Bag. / Sub Bid. / Instalasi / Unit</option>');
                        $.each(data, function(key, value) {
                            $('#sub_unit').append('<option value="' + value.kd_sub_unit_kerja + '">' + value.sub_unit_kerja + '</option>');
                        });
                    }
                });
            });

            // saat tmt_jabatan di isi maka masukkan value ke tgl_ttd
            $('#tmt_jabatan').change(function() {
                var tmt_jabatan = $(this).val();
                $('#tgl_ttd').val(tmt_jabatan);
            });

            // jika check_tgl_ttd di uncheck maka tampilkan input tgl_ttd
            // $('#check_tgl_ttd').change(function() {
            //     if ($(this).is(':checked')) {
            //         $('.tgl-ttd').addClass('d-none');
            //     } else {
            //         // set value tgl_ttd menjadi kosong
            //         $('#tgl_ttd').val('');
            //         $('.tgl-ttd').removeClass('d-none');
            //     }
            // });
            $('#check_tgl_ttd').change(function() {
                if ($(this).is(':checked')) {
                    $('.tgl-ttd').addClass('d-none');
                    $('#tgl_ttd').val($('#tmt_jabatan').val());
                } else {
                    $('#tgl_ttd').val('');
                    $('.tgl-ttd').removeClass('d-none');
                }
            });
        });

        $('#kd_karyawan').keyup(function (e) {
            const length = e.target.value.length;
            console.log(e.which == 13);

            if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
                return false;
            }

            if (length == 6) {
                console.log("get karyawan");
                $.ajax({
                    url: "{{ route('admin.mutasi.check-pegawai', '') }}/" + e.target.value,
                    type: "GET",
                    dataType: "json",
                    data: {
                        id: e.target.value
                    },
                    cache: false,
                    success: function(response) {
                        if (response.code == 4) {
                            $('#load_pegawai').val(`${response.nama} - ${response.nip}`);
                        } else {
                            toastr.error(response.message, 'Error');
                            $('#load_pegawai').val('');
                        }
                    }
                });
            } else if (length < 6) {
                $('#load_pegawai').val('');
            }
        })
        
        $('#kd_karyawan').keypress(function (e) {
            if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
                var kd_karyawan = $(this).val();
                e.preventDefault();
                console.log("keypressed");

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.mutasi.store-mutasi-nota') }}",
                    dataType: "json",
                    data: {
                        kd_karyawan: kd_karyawan
                    },
                    success: function (response) {
                        console.log(response);
                        handleResponse(response, kd_karyawan);

                        // update nilai didalam count-status-proses dengan response.total-mutasi-pending
                        $('.count-status-proses').text(response.total_mutasi_pending);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        toastr.error('Error', 'Error');
                    }
                });
                return false;
            }
        });

        function kirimIdMutasi(id) {
            $.ajax({
                url: "{{ route('admin.mutasi.list-mutasi-nota', '') }}/" + id,
                type: "GET",
                data: {
                    id: id
                },
                cache: false,
                success: function(response) {
                    $('#list-mutasi-nota').html(response);
                }
            });
        }

        // ketika form #add-mutasi-nota di submit
        $('#add-mutasi-nota').submit(function (e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');
            var data = form.serialize();

            // ambil kd_mutasi dan kd_karyawan kemudian masukkan kedalam #kd_mutasi dan #kd_karyawan
            var kd_mutasi = $('#kd_mutasi').val();
            var kd_karyawan = $('#kd_karyawan').val();
            
            var redirect = "{{ route('admin.mutasi-on-process.index') }}";
            $.ajax({
                url: url,
                type: method,
                data: data,
                cache: false,
                success: function(response) {
                    if (response.code == 1) {
                        // toast success
                        toastr.success(response.message, 'Success');
                    }

                    kirimIdMutasi(response.id_mutasi);

                    // reset form #add-mutasi-nota
                    $('#add-mutasi-nota').trigger('reset');

                    // redirect
                    window.location.href = redirect;
                }
            });
        });

        $(document).on('click', '#btn-delete-mutasi-nota', function () {
            var id = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.mutasi.delete-mutasi-nota', '') }}/" + id,
                type: "DELETE",
                data: {
                    id: id
                },
                cache: false,
                success: function(response) {
                    if (response.code == 2) {
                        // toast success
                        toastr.success(response.message, 'Success');

                        // reset form #add-mutasi-nota
                        $('#add-mutasi-nota').trigger('reset');

                        // update nilai didalam count-status-proses dengan response.total_mutasi_pending
                        $('.count-status-proses').text(response.total_mutasi_pending);
                    }

                    kirimIdMutasi(response.id_mutasi);
                }
            });
        });

        function handleResponse(response, kd_karyawan) {
            switch (response.code) {
                case 1:
                    toastr.error(response.message, 'Error');
                    break;
                case 2:
                    toastr.error(response.message, 'Error');
                    break;
                case 3:
                    toastr.success(response.message, 'Success');
                    $('#kd_mutasi_form').val(response.id_mutasi);
                    $('#kd_karyawan_form').val(kd_karyawan);
                    kirimIdMutasi(response.id_mutasi);
                    $('#kd_karyawan').val('');
                    break;
                case 4:
                    toastr.success(response.message, 'Success');
                    break;
                default:
                    toastr.error('Kode respon tidak dikenal.', 'Error');
                    break;
            }
        }
    </script>
@endpush