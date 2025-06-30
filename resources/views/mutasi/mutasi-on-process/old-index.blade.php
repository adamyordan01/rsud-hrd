@extends('layouts.backend', ['title' => 'Daftar Mutasi (Proses)'])

@inject('DB', 'Illuminate\Support\Facades\DB')

@php
    $jabatan = Auth::user()->karyawan->kd_jabatan_struktural;
    $ruangan = Auth::user()->karyawan->kd_ruangan;
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
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
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
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content  flex-column-fluid ">
        <div id="kt_app_content_container" class="app-container  container-fluid ">

            <x-navigation-menu :totalMutasiPending="$totalMutasiPending" :totalMutasiOnProcess="$totalMutasiOnProcess" />

            <div class="card mb-5">
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
                </div>
                <div class="card-body ps-lg-12 pt-lg-0 pb-lg-0">
                    <div class="row g-5 mb-5" id="list-mutasi-verifikasi">
                        <div class="table-responsive">
                            <table class="table table-bordered table-stripped align-middle" id="mutasi-on-process-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Kd. Mutasi</th>
                                        <th>Jenis Mutasi</th>
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
                                        <th class="text-center">Status</th>
                                        <th class="min-w-125px text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($getMutasi as $item)
                                        {{-- <tr style="background: #1b84ff;"> --}}
                                        {{-- <tr class="bg-gray-500">
                                            <td style="vertical-align: middle" colspan="6">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-dark fw-bold fs-5">
                                                        Kode Mutasi - {{ $item->kd_mutasi }}
                                                    </span>
                                                    <a
                                                        href="{{ route('admin.mutasi.edit-mutasi-nota-on-process', $item->kd_mutasi) }}"
                                                        class="btn btn-light-dark btn-sm btn-active-light-succes me-2 mb-2"
                                                    >
                                                        <i class="ki-duotone ki-notepad-edit fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                        Edit Mutasi Nota
                                                    </a>
                                                </div>
                                            </td>
                                        </tr> --}}

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
                                                <td class="text-center">{{ $data->kd_mutasi }}</td>
                                                <td>
                                                    @if ($data->kd_jenis_mutasi == 1)
                                                        Mutasi (Nota)
                                                    @elseif($data->kd_jenis_mutasi == 3)
                                                        Tugas Tambahan
                                                    @endif
                                                </td>
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
                                                <td class="text-center">
                                                    @if ($data->verif_1 == null)
                                                    <!--buat terdapat enter kebawah-->
                                                        Menunggu verifikasi Kasubbag. Kepeg.
                                                    @elseif($data->verif_2 == null)
                                                        Menunggu verifikasi Kabag. TU
                                                        {{-- <span class="badge badge-light-info">Menunggu verifikasi <br><br> Kabag. TU</span> --}}
                                                    @elseif($data->verif_3 == null)
                                                        Menunggu verifikasi Wadir ADM dan Umum
                                                        {{-- <span class="badge badge-light-warning">Menunggu verifikasi <br><br> Wadir ADM dan Umum</span> --}}
                                                    @elseif($data->verif_4 == null)
                                                        Menunggu verifikasi Direktur
                                                        {{-- <span class="badge badge-light-success">Menunggu verifikasi <br><br> Direktur</span> --}}
                                                    @endif
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
                                                                data-jenis-mutasi="{{ $data->kd_jenis_mutasi }}"
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
                                                                data-jenis-mutasi="{{ $data->kd_jenis_mutasi }}"
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
                                                                data-jenis-mutasi="{{ $data->kd_jenis_mutasi }}"
                                                                data-url="{{ route('admin.mutasi-on-process.third-verification') }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#kt_modal_verif"
                                                                id="verif3"
                                                            >
                                                                <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
                                                                Menunggu verifikasi Wadir ADM dan Umum
                                                            </a>
                                                        @endif
                                                        @if ($jabatan == 1 || $ruangan == 57)
                                                            <a
                                                                href="javascript:void(0)"
                                                                class="btn btn-success btn-sm d-block mb-2"
                                                                title="Menunggu verifikasi Direktur"
                                                                data-id="{{ $data->kd_mutasi }}"
                                                                data-karyawan="{{ $data->kd_karyawan }}"
                                                                data-jenis-mutasi="{{ $data->kd_jenis_mutasi }}"
                                                                data-url="{{ route('admin.mutasi-on-process.fourth-verification') }}"
                                                                id="verif4"
                                                            >
                                                                <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
                                                                Menunggu verifikasi Direktur
                                                            </a>
                                                        @endif
                                                    @elseif($data->verif_4 == null)
                                                        @if ($jabatan == 1 || $ruangan == 57)
                                                            <a
                                                                href="javascript:void(0)"
                                                                class="btn btn-success btn-sm d-block mb-2"
                                                                title="Menunggu verifikasi Direktur"
                                                                data-id="{{ $data->kd_mutasi }}"
                                                                data-karyawan="{{ $data->kd_karyawan }}"
                                                                data-jenis-mutasi="{{ $data->kd_jenis_mutasi }}"
                                                                data-url="{{ route('admin.mutasi-on-process.fourth-verification') }}"
                                                                id="verif4"
                                                            >
                                                                <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i>
                                                                Menunggu verifikasi Direktur
                                                            </a>
                                                        @endif
                                                    @endif

                                                    @if ($ruangan == 91 || $ruangan == 57)
                                                        @if ($data->kd_tahap_mutasi == 1)
                                                            <a
                                                                href="{{ route('admin.mutasi.edit-mutasi-nota-on-process', ['id' => $data->kd_mutasi, 'jenis_mutasi' => $data->kd_jenis_mutasi]) }}"
                                                                class="btn btn-light-dark btn-sm d-block mb-2"
                                                            >
                                                                <i class="ki-duotone ki-notepad-edit fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                                Edit Mutasi Nota
                                                            </a>
                                                            
                                                        @endif                                                        
                                                    @endif
                                                    <a
                                                        href="{{ route('admin.mutasi-on-process.print-draft-sk', [$data->kd_karyawan, $data->kd_mutasi, $data->kd_jenis_mutasi]) }}"
                                                        target="_blank"
                                                        class="btn btn-primary btn-sm d-block mb-2"
                                                    >
                                                        <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                        Cetak Draft Nota
                                                    </a>
                                                    <!--lihat log-->
                                                    <a
                                                        href="javascript:void(0)"
                                                        class="btn btn-secondary btn-sm d-block mb-2"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#kt_modal_log"
                                                        data-id="{{ $data->kd_mutasi }}"
                                                        data-karyawan="{{ $data->kd_karyawan }}"
                                                        id="log"
                                                    >
                                                    <i class="ki-duotone ki-timer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                        Lihat Log
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
                        <!-- jenis mutasi -->
                        <input type="hidden" name="kd_jenis_mutasi_finalisasi" id="kd_jenis_mutasi_finalisasi">
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

    <!-- Modal Log -->
    <div class="modal fade" id="kt_modal_log" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable mw-800px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_log_header">
                    <h2 class="fw-bold">Log Mutasi</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-log="close" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body p-10 mb-7" id="rincian-log">
                    <div class="timeline timeline-border-dashed" id="logTimeline">
                        <!-- Timeline items will be dynamically inserted here -->
                    </div>
                </div>
                <div class="modal-footer text-end">
                    <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-log="cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Log-->
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // KTSKList.init();

            // var table = $('#mutasi-on-process-table').DataTable({
            //     info: true,
            //     order: [],
            //     pageLength: 10,
            //     lengthChange: true,
            //     columnDefs: [
            //         { orderable: false, targets: [6] }
            //     ],
            // });

            var table = $('#mutasi-on-process-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.mutasi-on-process.datatable') }}", // Adjust route as needed
                    type: 'GET',
                    data: function(d) {
                        // Optional: Add additional data for filtering if needed
                        // d.customFilter = $('#custom-filter').val();
                    }
                },
                columns: [
                    { 
                        data: 'kd_mutasi', 
                        className: 'text-center' 
                    },
                    { 
                        data: 'jenis_mutasi' 
                    },
                    { 
                        data: 'kd_karyawan' 
                    },
                    { 
                        data: 'nama' 
                    },
                    { 
                        data: 'jabatan_lama' 
                    },
                    { 
                        data: 'jabatan_baru' 
                    },
                    { 
                        data: 'status', 
                        className: 'text-center' 
                    },
                    { 
                        data: 'aksi', 
                        orderable: false, 
                        className: 'text-center' 
                    }
                ],
                order: [], // Disable initial sorting
                pageLength: 10,
                lengthChange: true,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: '_INPUT_',
                    searchPlaceholder: 'Cari...'
                },
                drawCallback: function(settings) {
                    // Optional: Add any post-draw callbacks
                    // For example, initializing tooltips or other UI elements
                }
            });

            $('[data-kt-sk-table-filter="search"]').on("keyup", function () {
                table.search($(this).val()).draw();
            });

            $('[data-kt-user-table-filter="reset"]').on("click", function () {
                $('[data-kt-user-table-filter="form"] select').val("").trigger("change");
                table.search("").draw();
            });

            $('[data-kt-user-table-filter="form"] [data-kt-user-table-filter="filter"]').on("click", function () {
                var filterString = "";
                $('[data-kt-user-table-filter="form"] select').each(function (index) {
                    if (this.value) {
                        filterString += (index !== 0 ? " " : "") + this.value;
                    }
                });
                table.search(filterString).draw();
            });

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

            // open modal log
            // function openLogModal(htmlContent) {
            //     const logTimeLine = $('#logTimeline');
            //     logTimeLine.html(htmlContent);

            //     $('#kt_modal_log').modal('show');
            // }

            // handle log button click
            $(document).on('click', '#log', function () {
                var id = $(this).data('id');
                var karyawan = $(this).data('karyawan');

                $.ajax({
                    url: '{{ route("admin.mutasi-on-process.get-log-mutasi") }}',
                    method: 'GET',
                    data: {
                        kd_mutasi: id,
                        kd_karyawan: karyawan
                    },
                    beforeSend: function() {
                        $('#rincian-log').html('<div class="text-center"><span class="spinner-border spinner-border-lg align-center"></span></div>');
                    },
                    success: function(response) {
                        $('#rincian-log').html(response);
                        $('#kt_modal_log').modal('show');
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
        });

        $(document).on('click', '#verif1, #verif2, #verif3', function () {
            var id = $(this).data('id');
            var karyawan = $(this).data('karyawan');
            var jenis_mutasi = $(this).data('jenis-mutasi');
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
                url: '{{ route("admin.mutasi-on-process.rincian") }}',
                method: 'GET',
                data: {
                    kd_mutasi: id,
                    kd_karyawan: karyawan,
                    jenis_mutasi: jenis_mutasi
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
            var jenis_mutasi = $(this).data('jenis-mutasi');

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
                                kd_karyawan: karyawan,
                                jenis_mutasi: jenis_mutasi
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
            

            var kd_mutasi = $(form).find('input[name="kd_mutasi"]').val();
            var kd_karyawan = $(form).find('input[name="kd_karyawan"]').val();
            var jenis_mutasi = $(form).find('input[name="jenis_mutasi"]').val();

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
                            $('#jenis_mutasi').val(jenis_mutasi);
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
            var jenis_mutasi = $('#jenis_mutasi').val();
            var modal = $(this).closest('.modal');
            var form = modal.find('form')[0];
            var data = new FormData(form);

            console.log(`kd_mutasi: ${kd_mutasi}, kd_karyawan: ${kd_karyawan}`);
            
            // masukkan data urut dan tahun ke dalam form data
            data.append('kd_mutasi', kd_mutasi);
            data.append('kd_karyawan', kd_karyawan);
            data.append('jenis_mutasi', jenis_mutasi);

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
                            $('#jenis_mutasi').val(jenis_mutasi);

                            if (response.code == 200) {
                                // console.log('Success execute the sweet alert');
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

                                // swal loading
                                Swal.fire({
                                    title: 'Memproses verifikasi',
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

                                // Swal.close();
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