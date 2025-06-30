@extends('layouts.backend', ['title' => 'Tugas Tambahan'])

@inject('DB', 'Illuminate\Support\Facades\DB')

@php
    $kd_ruangan = Auth::user()->karyawan->kd_ruangan;
@endphp

@push('styles')
    <style>

    </style>    
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2 ">

        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
            <!--begin::Toolbar wrapper-->
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">


                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Tugas Tambahan
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
                            Tugas Tambahan
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    {{-- <a href="#"
                        class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                        Add Member
                    </a> --}}
                    {{-- <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_anggota">
                        <i class="ki-duotone ki-plus fs-2"></i>Tambah Anggota
                    </button> --}}

                    @if ($kd_ruangan == 91)
                        <a href="javascript:void(0)" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_tugas_tambahan">
                            <i class="ki-duotone ki-plus-square fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Tambah Nota Tugas Tambahan
                        </a>
                    @endif
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar wrapper-->
        </div>
        <!--end::Toolbar container-->
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content  flex-column-fluid ">
        <div id="kt_app_content_container" class="app-container  container-fluid ">
            <div class="card">
                
                <div class="card-header align-items-center py-5 gap-2 gap-md-5 border-0">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                            <input 
                                type="text"
                                data-kt-sk-table-filter="search"
                                class="form-control form-control-solid w-300px ps-12" placeholder="Cari Karyawan">
                        </div>
                    </div>

                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <div class="w-800 mw-850px">
                            {{-- <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}"> --}}
                            <div class="d-flex">
                                <input id="date" type="text" class="form-control form-control-solid me-3 flex-grow-1"
                                name="date" placeholder="Pilih tanggal" />
                        
                                <button id="btn-filter" class="btn btn-primary fw-bold flex-shrink-0">
                                    <i class="ki-duotone ki-filter fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Filter
                                </button>
    
                                <!--reset filter-->
                                <button id="btn-reset" class="btn btn-light-primary fw-bold flex-shrink-0 ms-2">
                                    <i class="ki-duotone ki-arrows-circle fs-3"><span class="path1"></span><span class="path2"></span>
                                    </i>
                                    Reset
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle" id="table-tugas-tambahan">
                            <thead>
                                <tr>
                                    <th class="min-w-20px">
                                        Kd. Tugas Tambahan
                                    </th>
                                    <th>
                                        ID. Peg
                                    </th>
                                    <th class="text-center vertical-align-middle min-w-200px">
                                        Nama <br>
                                        Tempat, Tanggal Lahir <br>
                                        NIP. / No. KARPEG
                                    </th>
                                    <th>
                                        Tugas Tambahan
                                    </th>
                                    <th>
                                        TMT
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Modal - Add tugas tambahan-->
    <div class="modal fade" id="kt_modal_add_tugas_tambahan" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog  mw-800px">
            <div class="modal-content">
                <form
                    action="{{ route('admin.tugas-tambahan.store') }}"
                    method="POST" class="form"
                    id="add_tugas_tambahan" 
                    enctype="multipart/form-data"
                >
                @csrf
                    <div class="modal-header" id="kt_modal_add_tugas_tambahan_header">
                        <h2 class="fw-bold">Tambah SK Kontrak</h2>

                        <div 
                            class="btn btn-icon btn-sm btn-active-icon-primary" 
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <div class="modal-body mb-7">
                        <div class="d-flex flex-column" id="kt_modal_add_tugas_tambahan_scroll">
                            <div class="d-flex flex-column fv-row mb-5">
                                <label
                                    class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                                >
                                    Nama Karyawan
                                </label>
                                <select
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-placeholder="Pilih karyawan"
                                    data-dropdown-parent="#kt_modal_add_tugas_tambahan"
                                    data-allow-clear="true"
                                    name="karyawan"
                                    id="karyawan"
                                >
                                    <option></option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback error-text karyawan_error"></div>
                            </div>

                            <div class="d-flex flex-column fv-row mb-5">
                                <label
                                    class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                                >
                                    Nama Tugas Tambahan
                                </label>
                                <input type="text" class="form-control form-control-solid" placeholder="" name="tugas" id="tugas">
                                <div class="fv-plugins-message-container invalid-feedback error-text tugas_error"></div>
                            </div>

                            <div class="row g-5 mb-8">
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Jabatan Struktural / Non Struktural (Opsional)</label>
                                    <select class="form-select form-select-solid"
                                        name="jab_struk"
                                        id="jab_struk"
                                        data-control="select2"
                                        data-dropdown-parent="#kt_modal_add_tugas_tambahan"
                                    >
                                        <option value="">Pilih Jabatan</option>
                                        @foreach ($jabatanStruktural as $item)
                                            <option value="{{ $item->kd_jab_struk }}">{{ $item->jab_struk }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="fv-plugins-message-container invalid-feedback error-text jab_struk_error">
                                    </div>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                        Sub Jenis Tenaga (Opsional)
                                    </label>
                                    <select class="form-select form-select-solid"
                                        name="sub_jenis_tenaga"
                                        id="sub_jenis_tenaga"
                                        data-control="select2"
                                        data-dropdown-parent="#kt_modal_add_tugas_tambahan"
                                    >
                                        <option value="">Pilih Sub Jenis Tenaga Opsional</option>
                                        @foreach ($subDetail as $item)
                                            <option value="{{ $item->sub_detail }}">{{ $item->sub_detail }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="fv-plugins-message-container invalid-feedback error-text sub_jenis_tenaga_error">
                                    </div>
                                </div>
                            </div>

                            
                            <div class="d-flex flex-column fv-row mb-5">
                                <label class="fw-semibold fs-6 mb-2 d-flex align-items-center">
                                    Ruangan (Opsional)
                                </label>
                                <select class="form-select form-select-solid"
                                    name="ruangan"
                                    id="ruangan"
                                    data-control="select2"
                                    data-placeholder="Pilih Ruangan"
                                    data-dropdown-parent="#kt_modal_add_tugas_tambahan"
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

                            <div class="row g-5 mb-5">
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2 required">TMT Awal</label>
                                    <input type="text" name="awal" id="awal" class="form-control form-control-solid">
                                    <div class="fv-plugins-message-container invalid-feedback error-text awal_error"></div>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2 required">TMT Akhir</label>
                                    <input type="text" name="akhir" id="akhir" class="form-control form-control-solid">
                                    <div class="fv-plugins-message-container invalid-feedback error-text awal_error"></div>
                                </div>
                            </div>

                            <div class="d-flex flex-column fv-row mb-5">
                                <label class="required fs-6 fw-semibold mb-2">Isi Nota No. 1 :</label>
                                <textarea name="isi_nota" class="form-control form-control-solid" rows="4" id="isi_nota"></textarea>
                                <div class="fv-plugins-message-container invalid-feedback error-text isi_nota_error"></div>
                            </div>

                            <div class="d-flex flex-column fv-row mb-5">
                                <label class="required fs-6 fw-semibold mb-2">Isi Nota No. 2 :</label>
                                <textarea name="isi_nota_2" class="form-control form-control-solid" rows="4" id="isi_nota_2">Nota Tugas Tambahan ini berlaku sejak tanggal ditetapkan.</textarea>
                                <div class="fv-plugins-message-container invalid-feedback error-text isi_nota_2_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary" data-kt-menu-modal-action="submit">
                            <span class="indicator-label">
                                Simpan
                            </span>
                            <span class="indicator-progress">
                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Modal - Add tugas tambahan-->

    <!--begin::Modal - Verifikasi-->
    <div class="modal fade" id="kt_modal_verif" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_verif_header">
                    <h2 class="fw-bold" id="verif-title"></h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"
                    aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body p-10 mb-7" id="rincian-verif"></div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">
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
    <!--end::Modal - Verifikasi-->

    <!-- Modal Verifikasi 4 -->
    <div class="modal fade" id="kt_modal_verif_4" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_verif_4_header">
                    <h2 class="fw-bold" id="verif-title-4"></h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body p-10 mb-7" id="rincian-verif-4"></div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">
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
    <!--end::Modal - Verifikasi 4-->

    <!--begin::Modal - Finalisasi TTE-->
    <div class="modal fade" id="kt_modal_finalisasi" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_finalisasi_header">
                    <h2 class="fw-bold" id="finalisasi-title">Proses TTE Nota Tugas</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body px-5 mb-7" id="finalisasi">
                    <form action="#">
                        @csrf
                        <input type="hidden" name="kd_mutasi_finalisasi" id="kd_mutasi_finalisasi">
                        <input type="hidden" name="kd_karyawan_finalisasi" id="kd_karyawan_finalisasi">
                        <!-- jenis mutasi -->
                        <input type="hidden" name="kd_jenis_mutasi_finalisasi" id="kd_jenis_mutasi_finalisasi">
                        <div class="d-flex flex-column fv-row mb-5">
                            <label
                                class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                            >
                                Tanggal Tanda Tangan Nota Tugas Tambahan
                            </label>
                            <input
                                class="form-control form-control-solid"
                                name="tanggal"
                                id="tanggal"
                                placeholder="Pilih Tanggal Tanda Tangan Nota Tugas Tambahan"
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
                    <button type="reset" class="btn btn-light batal-finalisasi me-3" data-bs-dismiss="modal">
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
        "use strict";
        
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        // var dateStart = moment().format('YYYY-MM-DD');
        // var dateEnd = moment().format('YYYY-MM-DD');
        // buat default value untuk flatpickr adalah awal dan akhir bulan
        var flatPickrStartDate = moment().startOf('month').format('YYYY-MM-DD');
        var flatPickrEndDate = moment().endOf('month').format('YYYY-MM-DD');

        // var flatPickrStartDate = $('#start_date').val();
        // var flatPickrEndDate = $('#end_date').val();

        $(document).ready(function () {
            $('#btn-filter').on('click', function(e) {
                e.preventDefault();
                var date = $('#date').val();
                var dateSplit = date.split(' to ');

                // Pastikan ada dua tanggal yang terpisah
                if (dateSplit.length === 2) {
                    var startDate = dateSplit[0];
                    var endDate = dateSplit[1];

                    // Reload DataTable with new date filters
                    table.ajax.reload(null, false); // false to not reset paging
                } else {
                    alert('Silakan pilih rentang tanggal yang valid.');
                }
                // table.ajax.url('{{ route("admin.tugas-tambahan.index") }}?start_date=' + startDate + '&end_date=' + endDate).load();
            });

            var table = $('#table-tugas-tambahan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.tugas-tambahan.index") }}',
                    data: function (d) {
                        var date = $('#date').val();
                        if (date) {
                            var dateSplit = date.split(' to ');
                            if (dateSplit.length === 2) {
                                d.start_date = dateSplit[0]; // Kirim start_date
                                d.end_date = dateSplit[1];   // Kirim end_date
                            }
                        }
                    }
                },
                columns: [
                    { data: 'kd_tugas_tambahan', name: 'kd_tugas_tambahan' },
                    { data: 'kd_karyawan', name: 'kd_karyawan' },
                    { data: 'karyawan', name: 'karyawan' },
                    { data: 'nama_tugas_tambahan', name: 'nama_tugas_tambahan' },
                    { data: 'tmt', name: 'tmt' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                columnDefs: [
                    {
                        targets: -1,
                        data: null,
                        orderable: false,
                        className: 'text-center',
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 75, 100],
            });

            // ketika btn-reset di klik maka reset filter
            $('#btn-reset').on('click', function(e) {
                e.preventDefault();
                $('#date').val(''); // Clear the date input
                table.ajax.reload(); // Reload DataTable with the reset filter
            });

            // search datatable
            $('[data-kt-sk-table-filter="search"]').on('keyup', function () {
                table.search($(this).val()).draw();
            });

            $('#karyawan').select2({
                placeholder: 'Pilih karyawan',
                allowClear: true,
                ajax: {
                    url: '{{ route("admin.tugas-tambahan.get-karyawan") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term, // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            })

            $('#add_tugas_tambahan').on('submit', function (e) {
                e.preventDefault();

                var form = this;
                var url = $(form).attr('action');
                var data = new FormData(form);

                var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');

                $(form).find('.btn-primary').append(loadingIndicator);

                $.ajax({
                    url: url,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    data: data,
                    beforeSend: function () {
                        loadingIndicator.show();

                        $(form).find('.btn-primary').attr('disabled', true);
                        $(form).find('.btn-primary .indicator-label').hide();
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: response.code == 200 ? 'success' : 'error',
                            title: response.code == 200 ? 'Success' : 'Error',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then((result) => {
                            if (response.code == 200) {
                                window.location.reload();
                            }
                        });
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
                    complete: function () {
                        loadingIndicator.hide();

                        $(form).find('.btn-primary').attr('disabled', false);
                        $(form).find('.btn-primary .indicator-label').show();
                    }
                })
            })

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

            $('#date').flatpickr({
                altInput: true,
                altFormat: "d-m-Y",
                dateFormat: "Y-m-d",
                mode: "range",
                defaultDate: [flatPickrStartDate, flatPickrEndDate],
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

            var form = this;

            let title;

            if ($(this).attr('id') === 'verif1') {
                title = 'Verifikasi Kasubbag. Kepegawaian';
            } else if ($(this).attr('id') === 'verif2') {
                title = 'Verifikasi Kabag. TU';
            } else if ($(this).attr('id') === 'verif3') {
                title = 'Verifikasi Wadir ADM dan Umum';
            }

            var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');

            $(form).find('.btn-primary').append(loadingIndicator);
            
            $.ajax({
                url: '{{ route("admin.tugas-tambahan.rincian") }}',
                method: 'GET',
                data: {
                    kd_tugas_tambahan: id,
                    kd_karyawan: karyawan
                },
                beforeSend: function() {
                    // $('#rincian-verif').html('<div class="text-center"><span class="spinner-border spinner-border-lg align-center"></span></div>');
                    loadingIndicator.show();
                    $(form).find('.btn-primary').attr('disabled', true);
                    $(form).find('.btn-primary .indicator-label').hide();

                    // swal loading
                    Swal.fire({
                        title: 'Memuat data rincian',
                        text: 'Mohon tunggu sebentar',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    $('#rincian-verif').html(response);

                    // close swal
                    Swal.close();

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
                },
                complete: function() {
                    loadingIndicator.hide();

                    $(form).find('.btn-primary').attr('disabled', false);
                    $(form).find('.btn-primary .indicator-label').show();

                    Swal.close();
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
                            url: '{{ route("admin.tugas-tambahan.rincian") }}',
                            method: 'GET',
                            data: {
                                kd_tugas_tambahan: id,
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
                        // toastr.error(`${response.message}`, 'Error');
                        toastr.error('Tidak dapat terhubung ke server BSRE, Silahkahkan hubungi penanggung jawab aplikasi HRD', 'Error');
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
                },
                complete: function() {
                    Swal.close();
                }
            });
        })

        $(document).on('click', '[data-kt-menu-modal-action-verif-4="submit"]', function (e) {
            e.preventDefault();
            var modal = $(this).closest('.modal');
            var form = modal.find('form')[0];
            

            var kd_tugas_tambahan = $(form).find('input[name="kd_tugas_tambahan"]').val();
            var kd_karyawan = $(form).find('input[name="kd_karyawan"]').val();

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
                        url: '{{ route("admin.tugas-tambahan.fourth-verification") }}',
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
                            $('#kd_tugas_tambahan').val(kd_tugas_tambahan);
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
            var kd_tugas_tambahan = $('#kd_tugas_tambahan').val();
            var kd_karyawan = $('#kd_karyawan').val();
            var modal = $(this).closest('.modal');
            var form = modal.find('form')[0];
            var data = new FormData(form);

            console.log(`kd_tugas_tambahan: ${kd_tugas_tambahan}, kd_karyawan: ${kd_karyawan}`);
            
            // masukkan data urut dan tahun ke dalam form data
            data.append('kd_tugas_tambahan', kd_tugas_tambahan);
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
                        url: '{{ route("admin.tugas-tambahan.finalisasi") }}',
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
                            $('#kd_tugas_tambahan').val(kd_tugas_tambahan);
                            $('#kd_karyawan').val(kd_karyawan);

                            if (response.code == 200) {
                                // console.log('Success execute the sweet alert');
                                toastr.success(response.message, 'Success');

                                // ajax reload datatable
                                $('#kt_modal_finalisasi').modal('hide');
                                $('#table-tugas-tambahan').DataTable().ajax.reload();

                                // tutup modal finalisasi, kemudian setelah 1.5 detik reload halaman
                                // setTimeout(() => {
                                //     $('#kt_modal_finalisasi').modal('hide');
                                //     window.location.reload();
                                // }, 1500);
                            } else {
                                toastr.error(response.message, 'Error');
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            // console.log(xhr);
                            var response = xhr.responseJSON;

                            if (xhr.status === 419) {
                                refreshCsrfToken().done(function () {
                                    toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                                })
                            } else if (xhr.status === 400) {
                                if (response && response.message && response.message.includes('Passphrase anda salah')) {
                                    toastr.error('Passphrase (Kata Sandi TTE) anda salah', 'Error');
                                } else {
                                    toastr.error(response.message || 'Terjadi kesalahan saat melakukan proses TTE SK', 'Error');
                                }
                            } else if (xhr.status === 500) {
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

        // handle untuk batal finalisasi, ini akan mengirim kd_tugas_tambahan dan kd_karyawan dan hit ke route batal-finalisasi
        $(document).on('click', '.batal-finalisasi', function (e) {
            e.preventDefault();
            var kd_tugas_tambahan = $('#kd_tugas_tambahan').val();
            var kd_karyawan = $('#kd_karyawan').val();

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang akan dibatalkan tidak dapat diubah kembali!",
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, batalkan!',
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
                        url: '{{ route("admin.tugas-tambahan.batal-finalisasi") }}',
                        method: 'POST',
                        data: {
                            kd_tugas_tambahan: kd_tugas_tambahan,
                            kd_karyawan: kd_karyawan
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Membatalkan proses TTE Nota Tugas',
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
                                toastr.success(response.message, 'Success');

                                // setTimeout(() => {
                                //     $('#kt_modal_finalisasi').modal('hide');
                                //     window.location.reload();
                                // }, 1500);
                                // ajax reload datatable
                                $('#kt_modal_finalisasi').modal('hide');
                                $('#table-tugas-tambahan').DataTable().ajax.reload();

                            } else {
                                toastr.error(response.message, 'Error');
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            var response = xhr.responseJSON;

                            if (xhr.status === 419) {
                                refreshCsrfToken().done(function () {
                                    toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                                })
                            } else if (xhr.status === 500) {
                                toastr.error('Internal Server Error', xhr.statusText);
                            } else {
                                toastr.error(response.message, 'Error');
                            }
                        },
                        complete: function() {
                            Swal.close();
                        }
                    })
                } else {
                    Swal.close();
                    // tampikan kembali modal finalisasi
                    $('#kt_modal_finalisasi').modal('show');
                }
            })
        });

        function handleVerificationSubmit(buttonSelector, url) {
            $(document).on('click', buttonSelector, function (e) {
                e.preventDefault();
                var modal = $(this).closest('.modal');
                var form = modal.find('form')[0];
                var data = new FormData(form);

                // console.log(data);

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
                                $(buttonSelector).attr('disabled', true);
                                $(buttonSelector + ' .indicator-label').hide();
                                $(buttonSelector + ' .indicator-progress').show();
                                // $(form).find('.btn-primary').attr('disabled', true).append('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');
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
                                            // window.location.reload();
                                            // ajax reload datatable
                                            $('#kt_modal_verif').modal('hide');
                                            $('#table-tugas-tambahan').DataTable().ajax.reload();
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
                                $(buttonSelector).attr('disabled', false);
                                $(buttonSelector + ' .indicator-label').show();
                                $(buttonSelector + ' .indicator-progress').hide();
                            }
                        })
                    }
                })
            })
        }

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
        

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#awal");

        Inputmask({
            "mask": "99/99/9999",
            "placeholder": "dd/mm/yyyy",
            "separator": "/",
            "leapday": "29/02/",
            "leapdayOn": "dd",
            "leapdayOn2": "dd",

        }).mask("#akhir");
    </script>
@endpush