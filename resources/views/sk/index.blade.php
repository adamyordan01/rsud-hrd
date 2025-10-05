@extends('layouts.backend', ['title' => 'SK Karyawan Kontrak'])

{{-- inject css --}}

{{-- inject DB --}}
@inject('DB', 'Illuminate\Support\Facades\DB')

@push('styles')
    <style>

    </style>    
@endpush

@php
    $jabatan = Auth::user()->kd_jabatan_struktural;
    $ruangan = Auth::user()->kd_ruangan;
@endphp

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
                        SK Karyawan Kontrak
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
                            SK Karyawan Kontrak
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.sk-kontrak.batch-monitor') }}" class="btn btn-flex btn-light-info h-40px fs-7 fw-bold">
                        <i class="ki-duotone ki-pulse fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Monitor Queue
                    </a>

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
            <div class="card mb-5">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5 border-0">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                            <input 
                                type="text"
                                data-kt-sk-table-filter="search"
                                class="form-control form-control-solid w-300px ps-12" 
                                placeholder="Cari SK atau Karyawan">
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <select 
                            class="form-select form-select-solid" 
                            data-kt-sk-table-filter="tahun" 
                            id="tahun-filter">
                            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body ps-lg-12 pt-lg-0 pb-lg-0">
                    <div class="row g-5 mb-5" id="list-sk">
                        <div class="table-responsive">
                            <table class="table table-bordered table-stripped align-middle" id="sk-table">
                                <thead>
                                    <tr>
                                        <th class="min-w-35px text-center">No</th>
                                        <th>Nomor Konsederan</th>
                                        <th class="text-center">Jumlah Pegawai</th>
                                        <th>Identitas Pegawai</th>
                                        <th class="text-center">TMT. Aktif</th>
                                        <th class="text-center">Tgl. SK</th>
                                        <th class="text-center">Status</th>
                                        <th class="min-w-125px text-center">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Modal - Add sk-->
    <div class="modal fade" id="kt_modal_add_sk" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_sk_header">
                    <h2 class="fw-bold">Tambah SK Kontrak</h2>

                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-add-sk="close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body px-5 mb-7">
                    <form
                        action="{{ route('admin.sk-kontrak.store') }}"
                        method="POST" class="form"
                        id="add_sk" 
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <div class="d-flex flex-column px-5 px-lg-10" id="kt_modal_add_sk_scroll">
                            <div class="d-flex flex-column fv-row mb-5">
                                <label
                                    class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                                >
                                    Tujuan SK
                                </label>
                                <select
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-placeholder="Pilih tujuan SK"
                                    data-dropdown-parent="#kt_modal_add_sk"
                                    data-allow-clear="true"
                                    name="tujuan"
                                    id="tujuan"
                                >
                                    <option></option>
                                    <option value="all">Seluruh Karyawan</option>
                                    <option value="single">Per-orangan</option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback error-text tujuan_error"></div>
                            </div>
                            <div class="d-flex flex-column fv-row mb-5 d-none">
                                <label
                                    class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                                >
                                    Nomor Konsederan
                                </label>
                                <input type="text" class="form-control form-control-solid" placeholder="" name="konsederan" id="konsederan">
                                <div class="fv-plugins-message-container invalid-feedback error-text konsederan_error"></div>
                            </div>
                            <div class="d-flex flex-column fv-row mb-5 d-none">
                                <label
                                    class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                                >
                                    Karyawan
                                </label>
                                <select
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    {{-- data-placeholder="Pilih tujuan SK" --}}
                                    data-dropdown-parent="#kt_modal_add_sk"
                                    {{-- data-allow-clear="true" --}}
                                    name="karyawan"
                                    id="karyawan"
                                >
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback error-text karyawan_error"></div>
                            </div>

                            <div class="text-end pt-10">
                                <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-add-sk="cancel">
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Add sk-->

    <div class="modal fade" id="kt_modal_verif" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_verif_header">
                    <h2 class="fw-bold" id="verif-title"></h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-verif="close" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body px-5 mb-7" id="rincian-verif"></div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-verif="cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" data-kt-menu-modal-action-verif="submit" disabled>
                        <span class="indicator-label">Verifikasi</span>
                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Modal - Verifikasi 4-->
    <div class="modal fade" id="kt_modal_verif_4" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_verif_4_header">
                    <h2 class="fw-bold" id="verif-title-4">Verifikasi Direktur</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-verif-4="close" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body px-5 mb-7" id="rincian-verif-4"></div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-verif-4="cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" data-kt-menu-modal-action-verif-4="submit" disabled>
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
                    <h2 class="fw-bold" id="finalisasi-title">Proses TTE SK</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-finalisasi="close" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body px-5 mb-7" id="finalisasi">
                    <form action="#">
                        @csrf
                        <input type="hidden" name="urut_rincian_verif" value="" id="urut_rincian_verif">
                        <input type="hidden" name="tahun_rincian_verif" value="" id="tahun_rincian_verif">
                        <input type="hidden" name="kd_karyawan[]" value="" id="kd_karyawan">
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

                        <div class="d-flex flex-column fv-row mb-5" id="batch-processing-option">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="enable_batch_processing" name="enable_batch_processing" checked>
                                <label class="form-check-label fw-semibold fs-6" for="enable_batch_processing">
                                    Proses Background (Batch Processing)
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <i class="ki-duotone ki-information fs-6 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Dengan opsi ini, proses TTE akan berjalan di background. Anda tidak perlu menunggu sampai selesai dan akan mendapat notifikasi saat proses selesai.
                            </div>
                            <div id="karyawan-info" class="mt-2" style="display: none;">
                                <div class="alert alert-info d-flex align-items-center p-3">
                                    <i class="ki-duotone ki-user-tick fs-2 text-info me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div>
                                        <div class="fw-bold" id="karyawan-count-text">Memproses 0 karyawan</div>
                                        <div class="text-muted fs-7" id="processing-info">Estimasi waktu: ~0 menit</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-finalisasi="cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" data-kt-menu-modal-action-finalisasi="submit" id="btn-finalisasi-normal">
                        <span class="indicator-label">Proses TTE</span>
                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <button type="submit" class="btn btn-success" data-kt-menu-modal-action-finalisasi-batch="submit" id="btn-finalisasi-batch" style="display: none;">
                        <span class="indicator-label">
                            <i class="ki-duotone ki-rocket fs-2 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Proses Batch TTE
                        </span>
                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Finalisasi TTE-->

    @include('sk.batch-progress-modal')

@endsection

@push('scripts')
    <script>
        "use strict";

        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        function showModal(title) {
            $('#verif-title').text(title);
            // $('#rincian-verif').html($(rincianId).html());
            $('#kt_modal_verif').modal('show');
        }

        var skTable;

        function toggleButtonState(buttonSelector, tableSelector) {
            let isAnyChecked = $(tableSelector + ' .form-check-input:checked').length > 0;
            $(buttonSelector).attr('disabled', !isAnyChecked);
        }
         
        // Fungsi untuk reload tabel setelah verifikasi
        function reloadDataTables() {
            console.log('Reloading DataTables...');
            
            // Reload main SK table
            if (typeof skTable !== 'undefined' && skTable) {
                console.log('Reloading main SK table');
                $('.search-spinner').show();
                skTable.ajax.reload(function() {
                    $('.search-spinner').hide();
                    console.log('Main SK table reloaded successfully');
                }, false); // false agar tidak reset page
            }
            
            // Reload rincian table jika ada
            if (typeof dataTableTerakhir !== 'undefined' && dataTableTerakhir) {
                console.log('Reloading rincian table');
                dataTableTerakhir.ajax.reload(function() {
                    console.log('Rincian table reloaded successfully');
                }, false); // false agar tidak reset page
            }
        }

        // Fungsi untuk force reload dengan reset page (jika diperlukan)
        function forceReloadDataTables() {
            console.log('Force reloading DataTables...');
            
            if (typeof skTable !== 'undefined' && skTable) {
                $('.search-spinner').show();
                skTable.ajax.reload(function() {
                    $('.search-spinner').hide();
                }, true); // true untuk reset ke page 1
            }
            
            if (typeof dataTableTerakhir !== 'undefined' && dataTableTerakhir) {
                dataTableTerakhir.ajax.reload(null, true);
            }
        }

        // Load batch progress modal
        function showBatchProgressModal(batchId, totalKaryawan, estimatedCompletion) {
            // Load modal content
            $.get('{{ route("admin.sk-kontrak.batch-progress-modal") }}', {
                batch_id: batchId,
                total_karyawan: totalKaryawan,
                estimated_completion: estimatedCompletion
            })
            .done(function(html) {
                // Remove existing modal if any
                $('#batch-progress-modal').remove();
                
                // Append new modal to body
                $('body').append(html);
                
                // Show modal
                $('#batch-progress-modal').modal('show');
            })
            .fail(function() {
                toastr.error('Gagal memuat modal progress', 'Error');
            });
        }

        // Handle form errors
        function handleFormErrors(errors) {
            $.each(errors, function(field, messages) {
                var input = $('[name="' + field + '"]');
                input.addClass('is-invalid');
                
                var errorContainer = $('.' + field + '_error');
                if (errorContainer.length > 0) {
                    errorContainer.removeClass('d-none').addClass('d-block').text(messages[0]);
                }
            });
        }

        // Reload data tables
        $(document).ready(function () {
            // Tambahkan indikator loading
            var searchIndicator = $('<span class="spinner-border spinner-border-sm text-primary ms-2 search-spinner" role="status" aria-hidden="true" style="display: none;"></span>');
            $('[data-kt-sk-table-filter="search"]').after(searchIndicator);

            // Fungsi debounce untuk menunda eksekusi
            function debounce(func, wait, immediate) {
                var timeout;
                return function() {
                    var context = this, args = arguments;
                    var later = function() {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    var callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            }


            skTable = $('#sk-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.sk-kontrak.datatable') }}",
                    type: 'GET',
                    data: function(d) {
                        d.tahun = $('#tahun-filter').val();
                    },
                    beforeSend: function() {
                        // Tampilkan loading indicator saat memuat data
                        if (skTable && skTable.context[0].searching) {
                            $('.search-spinner').show();
                        }
                    },
                    complete: function() {
                        // Sembunyikan loading indicator setelah selesai
                        $('.search-spinner').hide();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    { 
                        data: 'no_sk', 
                        name: 'no_sk', 
                        className: 'text-center' 
                    },
                    { 
                        data: 'jumlah_pegawai', 
                        name: 'jumlah_pegawai', 
                        className: 'text-center',
                        searchable: false
                    },
                    { 
                        data: 'nama', 
                        name: 'nama' 
                    },
                    { 
                        data: 'tgl_sk', 
                        name: 'tgl_sk', 
                        className: 'text-center',
                        searchable: false
                    },
                    { 
                        data: 'tgl_ttd', 
                        name: 'tgl_ttd', 
                        className: 'text-center',
                        searchable: false 
                    },
                    { 
                        data: 'status', 
                        name: 'verif_1', 
                        className: 'text-center',
                        searchable: false
                    },
                    { 
                        data: 'aksi', 
                        name: 'aksi', 
                        orderable: false, 
                        searchable: false,
                        className: 'text-center' 
                    }
                ],
                order: [[1, 'desc']], // Urutkan berdasarkan no_sk secara default
                drawCallback: function(settings) {
                    // Callback setelah tabel digambar
                    $('.search-spinner').hide();
                }
            });

            // Gunakan variabel untuk melacak status pencarian terakhir
            var lastSearchValue = '';
            
            // Event untuk pencarian dengan debounce
            var debouncedSearch = debounce(function(value) {
                // Hanya lakukan pencarian jika nilai berubah
                if (value !== lastSearchValue) {
                    lastSearchValue = value;
                    $('.search-spinner').show();
                    skTable.search(value).draw();
                }
            }, 500);

            // Event untuk pencarian
            $('[data-kt-sk-table-filter="search"]').on('keyup', function() {
                var value = $(this).val();
                debouncedSearch(value);
            });

            // Event untuk filter tahun
            $('#tahun-filter').on('change', function() {
                $('.search-spinner').show();
                skTable.ajax.reload(function() {
                    $('.search-spinner').hide();
                });
            });

            // jika #tujuan yang dipilih adalah single maka tampilkan #karyawan, jika memilih all maka tampilkan #konsederan
            $('#tujuan').on('change', function () {
                let isSingle = $(this).val() === 'single';
                $('#karyawan').parent().toggleClass('d-none', !isSingle);
                $('#konsederan').parent().toggleClass('d-none', isSingle);
            });

            // jika allow clear ditekan maka tutup #karyawan maupun pada #konsederan
            $('#tujuan').on('select2:unselect', function () {
                $('#karyawan').parent().addClass('d-none');
                $('#konsederan').parent().addClass('d-none');
            });

            $('#karyawan').select2({
                placeholder: 'Pilih karyawan',
                allowClear: true,
                ajax: {
                    url: '{{ route("admin.sk-kontrak.get-karyawan") }}',
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

            // event listener for buttons
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

            // Event listener for the header checkbox specifically
            $(document).on('click', '#kt_rincian_verif_1_table .form-check-input, .header-checkbox', function() {
                let isChecked = $(this).is(':checked');

                if ($(this).hasClass('header-checkbox')) {
                    $('#kt_rincian_verif_1_table tbody .form-check-input').prop('checked', isChecked);
                }

                toggleButtonState('[data-kt-menu-modal-action-verif="submit"]', '#kt_rincian_verif_1_table');
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

        $('#add_sk').on('submit', function (e) {
            e.preventDefault();
            
            var form = this;
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            var data = new FormData(this);

            var loadingIndicator = $('<span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>');

            $(form).find('.btn-primary').append(loadingIndicator);

            $.ajax({
                url: url,
                method: method,
                contentType: false,
                processData: false,
                dataType: 'json',
                data: data,
                beforeSend: function() {
                    loadingIndicator.show();

                    $(form).find('.btn-primary').attr('disabled', true);
                    $(form).find('.btn-primary .indicator-label').hide();
                },
                complete: function() {
                    loadingIndicator.hide();

                    $(form).find('.btn-primary').attr('disabled', false);
                    $(form).find('.btn-primary .indicator-label').show();
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
                            // lakukan reload datatable dengan delay untuk memastikan data tersimpan
                            setTimeout(function() {
                                reloadDataTables();
                            }, 500);
                            $('#kt_modal_add_sk').modal('hide');
                            $('#add_sk')[0].reset();
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
                }
            })
        })

        function handleFormErrors(errors) {
            if (!$.isEmptyObject(errors)) {
                $('.error-text').remove();

                $.each(errors, function(key, value) {
                    $('#' + key).closest('.fv-row').append('<div class="fv-plugins-message-container invalid-feedback error-text '+ key +'_error">' + value + '</div>');
                })
            }
        }

        // ketika #verif1, 2, 3 ditekan maka tampilkan modal #kt_modal_verif_1
        $(document).on('click', '#verif1, #verif2, #verif3', function () {
            var urut = $(this).data('urut');
            var tahun = $(this).data('tahun');
            var kode = $(this).data('kode');
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
                url: '{{ route("admin.sk-kontrak.rincian-karyawan") }}',
                method: 'GET',
                data: {
                    urut: urut,
                    tahun: tahun,
                    kode: kode
                },
                beforeSend: function() {
                    $('#rincian-verif').html('<div class="text-center"><span class="spinner-border spinner-border-lg align-center"></span></div>');
                },
                success: function(response) {
                    $('#rincian-verif').html(response);
                    $('#urut_rincian_verif').val(urut);
                    $('#tahun_rincian_verif').val(tahun);
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
                                            // reload datatable dengan delay
                                            setTimeout(function() {
                                                reloadDataTables();
                                            }, 500);
                                            $('#kt_modal_verif').modal('hide');
                                            $('#kt_rincian_verif_1_table').html('');
                                        } else {
                                            // reload datatable dengan delay
                                            setTimeout(function() {
                                                reloadDataTables();
                                            }, 500);
                                            $('#kt_modal_verif').modal('hide');
                                            $('#kt_rincian_verif_1_table').html('');
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

        // ketika #tte_sk ditekan maka tampilkan modal #kt_modal_finalisasi
        // $(document).on('click', '#tte_sk', function () {
        //     var urut = $(this).data('urut');
        //     var tahun = $(this).data('tahun');

        //     $.ajax({
        //         url: '{{ route("admin.sk-kontrak.verifikasi-karyawan") }}',
        //         method: 'GET',
        //         data: {
        //             urut: urut,
        //             tahun: tahun
        //         },
        //         beforeSend: function() {
        //             $('#finalisasi').html('<div class="text-center"><span class="spinner-border spinner-border-lg align-center"></span></div>');
        //         },
        //         success: function(response) {
        //             $('#finalisasi').html(response);
        //             $('#urut_finalisasi').val(urut);
        //             $('#tahun_finalisasi').val(tahun);
        //             $('#finalisasi-title').text('Proses TTE SK');
        //             $('#kt_modal_finalisasi').modal('show');
        //             // handleFinalisasiSubmit('[data-kt-menu-modal-action-finalisasi="submit"]', url);
        //         },
        //         error: function(xhr) {
        //             if (xhr.status === 419) {
        //                 refreshCsrfToken().done(function () {
        //                     toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
        //                 })
        //             } else if (xhr.status === 500) {
        //                 toastr.error('Internal Server Error', xhr.statusText);
        //             }
        //         }
        //     });
        // });

        // check dan unchecked all checkbox untuk verif_4
        $(document).on('click', '#kt_rincian_verif_1_table .form-check-input', function() {
            toggleButtonState('[data-kt-menu-modal-action-verif-4="submit"]', '#kt_rincian_verif_1_table');
        });

        // ketika keduanya ditekan maka check all checkbox dan ketika salah satunya ditekan maka unchecked all checkbox
        $(document).on('click', '.header-checkbox', function() {
            let isChecked = $(this).is(':checked');
            $('#kt_rincian_verif_1_table .form-check-input').prop('checked', isChecked);
            toggleButtonState('[data-kt-menu-modal-action-verif-4="submit"]', '#kt_rincian_verif_1_table');
        });

        // ketika verif_4 ditekan maka jangan tampilkan modal #kt_modal_verif_4 kecuali status bsre aktif
        $(document).on('click', '#verif4', function () {
            var urut = $(this).data('urut');
            var tahun = $(this).data('tahun');
            var kode = $(this).data('kode');
            var url = $(this).data('url');

            console.log(urut, tahun, kode, url);

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
                        // show modal verif_4
                        $.ajax({
                            url: '{{ route("admin.sk-kontrak.rincian-karyawan") }}',
                            method: 'GET',
                            data: {
                                urut: urut,
                                tahun: tahun,
                                kode: kode
                            },
                            beforeSend: function() {
                                $('#rincian-verif-4').html('<div class="text-center"><span class="spinner-border spinner-border-lg align-center"></span></div>');
                            },
                            success: function(response) {
                                $('#rincian-verif-4').html(response);
                                $('#urut_rincian_verif').val(urut);
                                $('#tahun_rincian_verif').val(tahun);
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
                        toastr.error(response.message, 'Error');
                    }
                },
                complete: function() {
                    Swal.close();
                }
            })

            // $.ajax({
            //     url: '{{ route("admin.sk-kontrak.rincian-karyawan") }}',
            //     method: 'GET',
            //     data: {
            //         urut: urut,
            //         tahun: tahun,
            //         kode: kode
            //     },
            //     beforeSend: function() {
            //         $('#rincian-verif-4').html('<div class="text-center"><span class="spinner-border spinner-border-lg align-center"></span></div>');
            //     },
            //     success: function(response) {
            //         $('#rincian-verif-4').html(response);
            //         $('#urut_rincian_verif').val(urut);
            //         $('#tahun_rincian_verif').val(tahun);
            //         $('#verif-title-4').text('Verifikasi Direktur');
            //         $('#kt_modal_verif_4').modal('show');
            //     },
            //     error: function(xhr) {
            //         if (xhr.status === 419) {
            //             refreshCsrfToken().done(function () {
            //                 toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
            //             })
            //         } else if (xhr.status === 500) {
            //             toastr.error('Internal Server Error', xhr.statusText);
            //         }
            //     }
            // });
        });

        // ketika tombol data-kt-menu-modal-verif-4="submit" ditekan maka lakukan verifikasi dan tampilkan modal #kt_modal_finalisasi
        $(document).on('click', '[data-kt-menu-modal-action-verif-4="submit"]', function (e) {
            e.preventDefault();
            var modal = $(this).closest('.modal');
            var form = modal.find('form')[0];

            var urut = $('#urut_rincian_verif').val();
            var tahun = $('#tahun_rincian_verif').val();

            var data = new FormData(form)

            // masukkan data urut dan tahun ke dalam form data
            data.append('urut_rincian_verif', urut);
            data.append('tahun_rincian_verif', tahun);

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
                        url: '{{ route("admin.sk-kontrak.fourth-verification") }}',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        data: data,
                        beforeSend: function() {
                            
                        },
                        success: function(response) {
                            $('#urut_rincian_verif').val(urut);
                            $('#tahun_rincian_verif').val(tahun);
                            // kd_karyawan from data
                            $('#kd_karyawan').val(data.get('kd_karyawan[]'));
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
                                        $('#finalisasi-title').text('Proses TTE SK');
                                        $('#kt_modal_finalisasi').modal('show');
                                        
                                        // Auto-enable batch processing untuk multiple karyawan
                                        var kdKaryawanArray = data.get('kd_karyawan[]');
                                        var karyawanCount = 0;
                                        
                                        if (typeof kdKaryawanArray === 'string') {
                                            // Single value
                                            karyawanCount = 1;
                                        } else if (Array.isArray(kdKaryawanArray)) {
                                            // Array
                                            karyawanCount = kdKaryawanArray.length;
                                        } else {
                                            // Multiple values, count via FormData
                                            var karyawanValues = data.getAll('kd_karyawan[]');
                                            karyawanCount = karyawanValues.length;
                                        }
                                        
                                        console.log('Detected karyawan count:', karyawanCount);
                                        
                                        // Update info karyawan
                                        $('#karyawan-count-text').text(`Memproses ${karyawanCount} karyawan`);
                                        $('#processing-info').text(`Estimasi waktu: ~${Math.ceil(karyawanCount * 0.5)} menit`);
                                        $('#karyawan-info').show();
                                        
                                        // Auto-enable batch processing untuk 2+ karyawan
                                        if (karyawanCount > 1) {
                                            $('#enable_batch_processing').prop('checked', true);
                                            $('#btn-finalisasi-normal').hide();
                                            $('#btn-finalisasi-batch').show();
                                            
                                            // Show info about automatic batch selection
                                            toastr.info(`Batch processing otomatis diaktifkan untuk ${karyawanCount} karyawan`, 'Info');
                                        } else {
                                            $('#enable_batch_processing').prop('checked', false);
                                            $('#btn-finalisasi-normal').show();
                                            $('#btn-finalisasi-batch').hide();
                                        }
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
                            // $(this).attr('disabled', false);
                            // $(this + ' .indicator-label').show();
                            // $(this + ' .indicator-progress').hide();
                        }
                    })
                }
            })
        });

        // ketika tombol data-kt-menu-modal-action-finalisasi="submit" ditekan maka lakukan finalisasi
        $(document).on('click', '[data-kt-menu-modal-action-finalisasi="submit"]', function (e) {
            e.preventDefault();
            var urut = $('#urut_rincian_verif').val();
            var tahun = $('#tahun_rincian_verif').val();
            var tanggal = $('#tanggal').val().trim();
            var passphrase = $('#passphrase').val().trim();
            var modal = $(this).closest('.modal');
            var form = modal.find('form')[0];

            // Reset previous errors
            $('.error-text').removeClass('d-block').addClass('d-none').empty();
            $('.form-control').removeClass('is-invalid');

            // Validasi tanggal dan passphrase
            var hasErrors = false;

            if (!tanggal) {
                $('#tanggal').addClass('is-invalid');
                $('.tanggal_error').removeClass('d-none').addClass('d-block').text('Tanggal tanda tangan SK wajib diisi');
                hasErrors = true;
            }

            if (!passphrase) {
                $('#passphrase').addClass('is-invalid');
                $('.passphrase_error').removeClass('d-none').addClass('d-block').text('Passphrase (Password TTE) wajib diisi');
                hasErrors = true;
            }

            if (hasErrors) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Error',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
                return;
            }

            var data = new FormData(form);

            // masukkan data urut dan tahun ke dalam form data
            data.append('urut_rincian_verif', urut);
            data.append('tahun_rincian_verif', tahun);

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
                        url: '{{ route("admin.sk-kontrak.finalisasi") }}',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        data: data,
                        beforeSend: function() {
                            // buat loading indicator
                            Swal.fire({
                                title: 'Proses TTE SK',
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
                            $('#urut_rincian_verif').val(urut);
                            $('#tahun_rincian_verif').val(tahun);

                            if (response.code == 200) {
                                // Check if system redirected to batch processing
                                if (response.redirect_to_batch) {
                                    Swal.close();
                                    $('#kt_modal_finalisasi').modal('hide');
                                    
                                    // Show notification about automatic batch processing
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Menggunakan Batch Processing',
                                        html: `
                                            <p>${response.message}</p>
                                            <p class="text-muted">Jumlah karyawan: ${response.batch_size}</p>
                                            <p class="text-info">Sistem akan menggunakan background processing untuk menghindari timeout.</p>
                                        `,
                                        showCancelButton: true,
                                        confirmButtonText: 'Lanjutkan dengan Batch',
                                        cancelButtonText: 'Batal',
                                        customClass: {
                                            confirmButton: 'btn btn-success',
                                            cancelButton: 'btn btn-secondary'
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Trigger batch processing automatically
                                            $('[data-kt-menu-modal-action-finalisasi-batch="submit"]').trigger('click');
                                        }
                                    });
                                } else {
                                    // Normal single processing success
                                    console.log('Success execute the sweet alert');
                                    toastr.success(response.message, 'Success');

                                    // tutup modal finalisasi, kemudian setelah 1.5 detik reload halaman
                                    setTimeout(() => {
                                        // reload datatable dengan delay
                                        setTimeout(function() {
                                            reloadDataTables();
                                        }, 500);
                                        $('#kt_modal_finalisasi').modal('hide');
                                    }, 1500);
                                }
                            } else {
                                toastr.error(response.message, 'Error');

                                // Swal.fire({
                                //     icon: 'error',
                                //     title: 'Error',
                                //     text: response.message,
                                //     showConfirmButton: false,
                                //     timer: 1500,
                                // });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            // console.log(xhr);

                            if (xhr.status === 419) {
                                refreshCsrfToken().done(function () {
                                    toastr.error('Token CSRF kadaluarsa, silahkan tekan tombol simpan kembali', 'Token CSRF Kadaluarsa');
                                })
                            } else if (xhr.status === 422) {
                                // Handle validation errors
                                var response = xhr.responseJSON;
                                if (response && response.errors) {
                                    handleFormErrors(response.errors);
                                } else {
                                    toastr.error(response.message || 'Validasi gagal, mohon periksa input Anda', 'Error Validasi');
                                }
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
                                            // window.location.reload();
                                            // reload datatable dengan delay
                                            setTimeout(function() {
                                                reloadDataTables();
                                            }, 500);
                                            $('#kt_modal_finalisasi').modal('hide');
                                            $('#kt_rincian_verif_1_table').html('');
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
        

        // buka modal finalisasi ketika di klik
        // $(document).on('click', '#tte_sk', function () {
        //     var urut = $(this).data('urut');
        //     var tahun = $(this).data('tahun');

        //     $('#urut_rincian_verif').val(urut);
        //     $('#tahun_rincian_verif').val(tahun);
        //     $('#finalisasi-title').text('Proses TTE SK');
        //     $('#kt_modal_finalisasi').modal('show');
        // });

        $('#sk-table').on('draw.dt', function () {
            $('#kt_rincian_verif_1_table .form-check-input').prop('checked', false);
            $('#kt_rincian_verif_1_table .header-checkbox').prop('checked', false);

            toggleButtonState('[data-kt-menu-modal-action-verif-1="submit"]', '#kt_rincian_verif_1_table');
            toggleButtonState('[data-kt-menu-modal-action-verif-2="submit"]', '#kt_rincian_verif_1_table');
            toggleButtonState('[data-kt-menu-modal-action-verif-3="submit"]', '#kt_rincian_verif_1_table');
            toggleButtonState('[data-kt-menu-modal-action-verif-4="submit"]', '#kt_rincian_verif_1_table');
        });

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

        // data-kt-menu-modal-add-sk="close"
        $(document).on('click', '[data-kt-menu-modal-add-sk="close"]', function () {
            $('#kt_modal_add_sk').modal('hide');
        });

        // data-kt-menu-modal-add-sk="cancel"
        $(document).on('click', '[data-kt-menu-modal-add-sk="cancel"]', function () {
            $('#kt_modal_add_sk').modal('hide');
        });

        // Validasi real-time untuk field tanggal dan passphrase
        $(document).on('input', '#tanggal', function() {
            var value = $(this).val().trim();
            if (value) {
                $(this).removeClass('is-invalid');
                $('.tanggal_error').removeClass('d-block').addClass('d-none').empty();
            }
        });

        $(document).on('input', '#passphrase', function() {
            var value = $(this).val().trim();
            if (value) {
                $(this).removeClass('is-invalid');
                $('.passphrase_error').removeClass('d-block').addClass('d-none').empty();
            }
        });

        // Reset form saat modal ditutup
        $('#kt_modal_finalisasi').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
            $('.error-text').removeClass('d-block').addClass('d-none').empty();
            $('.form-control').removeClass('is-invalid');
            
            // Reset karyawan info
            $('#karyawan-info').hide();
            $('#karyawan-count-text').text('Memproses 0 karyawan');
            $('#processing-info').text('Estimasi waktu: ~0 menit');
            
            // Reset button visibility
            $('#btn-finalisasi-normal').show();
            $('#btn-finalisasi-batch').hide();
            $('#enable_batch_processing').prop('checked', false);
        });

        // Toggle batch processing UI
        $('#enable_batch_processing').on('change', function() {
            var isChecked = $(this).is(':checked');
            
            if (isChecked) {
                $('#btn-finalisasi-normal').hide();
                $('#btn-finalisasi-batch').show();
            } else {
                $('#btn-finalisasi-normal').show();
                $('#btn-finalisasi-batch').hide();
            }
        });

        // Handler untuk batch processing
        $(document).on('click', '[data-kt-menu-modal-action-finalisasi-batch="submit"]', function (e) {
            e.preventDefault();
            
            var urut = $('#urut_rincian_verif').val();
            var tahun = $('#tahun_rincian_verif').val();
            var tanggal = $('#tanggal').val().trim();
            var passphrase = $('#passphrase').val().trim();
            var modal = $(this).closest('.modal');
            var form = modal.find('form')[0];

            // Reset previous errors
            $('.error-text').removeClass('d-block').addClass('d-none').empty();
            $('.form-control').removeClass('is-invalid');

            // Validasi
            var hasErrors = false;

            if (!tanggal) {
                $('#tanggal').addClass('is-invalid');
                $('.tanggal_error').removeClass('d-none').addClass('d-block').text('Tanggal tanda tangan SK wajib diisi');
                hasErrors = true;
            }

            if (!passphrase) {
                $('#passphrase').addClass('is-invalid');
                $('.passphrase_error').removeClass('d-none').addClass('d-block').text('Passphrase (Password TTE) wajib diisi');
                hasErrors = true;
            }

            if (hasErrors) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Error',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
                return;
            }

            // Get checked karyawan
            var checkedKaryawan = [];
            $('#kt_rincian_verif_1_table input[name="kd_karyawan[]"]:checked').each(function() {
                checkedKaryawan.push($(this).val());
            });

            if (checkedKaryawan.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Error',
                    text: 'Minimal pilih satu karyawan untuk diproses',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
                return;
            }

            var data = new FormData();
            data.append('_token', $('meta[name="csrf-token"]').attr('content'));
            data.append('urut_rincian_verif', urut);
            data.append('tahun_rincian_verif', tahun);
            data.append('tanggal', tanggal);
            data.append('passphrase', passphrase);
            
            // Append all checked karyawan
            checkedKaryawan.forEach(function(karyawan) {
                data.append('kd_karyawan[]', karyawan);
            });

            Swal.fire({
                title: 'Proses Batch TTE?',
                html: `
                    <div class="text-start">
                        <p>Anda akan memproses TTE untuk <strong>${checkedKaryawan.length} karyawan</strong> secara batch.</p>
                        <div class="alert alert-info">
                            <i class="ki-duotone ki-information fs-2 text-info me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Proses akan berjalan di background. Anda akan menerima notifikasi progress dan dapat memantau status real-time.
                        </div>
                        <p class="text-muted">Estimasi waktu: ~${Math.ceil(checkedKaryawan.length * 0.5)} menit</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Proses Batch!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                showLoaderOnConfirm: true,
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.sk-kontrak.finalisasi-batch") }}',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        data: data,
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memulai Proses Batch',
                                text: 'Sedang menyiapkan queue processing...',
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
                                
                                // Close finalisasi modal
                                $('#kt_modal_finalisasi').modal('hide');
                                
                                // Show batch progress modal
                                showBatchProgressModal(
                                    response.batch_id, 
                                    response.total_karyawan, 
                                    response.estimated_completion
                                );
                                
                                toastr.success(response.message, 'Success');
                                
                                // Reload datatable dengan delay
                                setTimeout(function() {
                                    reloadDataTables();
                                }, 500);
                                
                            } else {
                                Swal.close();
                                toastr.error(response.message, 'Error');
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            
                            if (xhr.status === 422) {
                                var response = xhr.responseJSON;
                                if (response && response.errors) {
                                    handleFormErrors(response.errors);
                                } else {
                                    toastr.error(response.message || 'Validasi gagal', 'Error Validasi');
                                }
                            } else if (xhr.status === 500) {
                                var response = xhr.responseJSON;
                                toastr.error(response.message || 'Terjadi kesalahan server', 'Error');
                            } else {
                                toastr.error('Terjadi kesalahan saat memulai proses batch', 'Error');
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush