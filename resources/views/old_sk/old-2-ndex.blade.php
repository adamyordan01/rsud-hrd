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
                    {{-- <a href="#"
                        class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                        Add Member
                    </a> --}}
                    {{-- <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_anggota">
                        <i class="ki-duotone ki-plus fs-2"></i>Tambah Anggota
                    </button> --}}

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
                    <div class="w-100 mw-150px">
                        <select class="form-select form-select-solid" id="tahun_sk" data-control="select2" data-placeholder="Pilih Tahun SK" data-allow-clear="true">
                            <option></option>
                            @for ($i = date('Y'); $i >= 2021; $i--)
                                <option
                                    value="{{ $i }}"
                                    {{ request()->get('tahun_sk') == $i ? 'selected' : '' }}
                                >
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">

                <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table id="sk-table" class="table align-middle table-row-dashed table-bordered fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                            <thead>
                                <tr>
                                    <th class="min-w35px text-center">
                                        No
                                    </th>
                                    <th>Nomor Konsederan</th>
                                    <th>Jumlah Pegawai</th>
                                    <th>Identitas Pegawai</th>
                                    <th>TMT. Aktif</th>
                                    <th>Tgl. SK</th>
                                    <th>Status</th>
                                    <th class="min-w-125px text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($skKontrak as $item)
                                @php
                                    if (empty($item->nomor_konsederan)) {
                                        $urut = $item->urut;
                                        $getKaryawan = DB::table('view_tampil_karyawan as vtk')
                                            ->select('vtk.kd_karyawan', 'vtk.gelar_depan', 'vtk.nama', 'vtk.gelar_belakang')
                                            ->where('vtk.kd_karyawan', function ($query) use ($urut) {
                                                $query->select('hspk.kd_karyawan')
                                                    ->from('hrd_sk_pegawai_kontrak as hspk')
                                                    ->where('hspk.urut', $urut)
                                                    ->limit(1);
                                            })
                                            ->first();
                                        $nama = $getKaryawan ? $getKaryawan->kd_karyawan."<br>".$getKaryawan->gelar_depan." ".$getKaryawan->nama.$getKaryawan->gelar_belakang : "~";
                                    } else {
                                        $nama = "~";
                                    }
                    
                                    $datas = "";
                                    $status = "";
                                    $button = "";
                    
                                    if ($item->verif_1 == 0) {
                                        $status = "Menunggu verifikasi Kasubbag. Kepegawaian";
                                        $datas = "data-urut='$item->urut' data-tahun='$item->tahun_sk' data-kode='verif1' data-url='".route('admin.sk-kontrak.first-verification')."'";
                    
                                        if ($jabatan == 19 || $ruangan == 57) {
                                            $button = "
                                                <a href='javascript:void(0)' class='btn btn-info btn-sm d-block mb-2' title='Verifikasi Kasubbag. Kepegawaian' $datas data-bs-toggle='modal' data-bs-target='#kt_modal_verif' id='verif1'>
                                                    <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i> 
                                                    Verifikasi Ka.Sub.Bag. Kepeg.
                                                </a>";
                                        }
                                    } elseif ($item->verif_2 == 0) {
                                        $status = "Menunggu verifikasi Kabag. TU";
                                        $datas = "data-urut='$item->urut' data-tahun='$item->tahun_sk' data-kode='verif2' data-url='".route('admin.sk-kontrak.second-verification')."'";
                    
                                        if ($jabatan == 7 || $ruangan == 57) {
                                            $button = "
                                                <a href='javascript:void(0)'
                                                    class='btn btn-primary btn-sm d-block mb-2'
                                                    title='Verifikasi Kabag. TU'
                                                    $datas 
                                                    data-bs-toggle='modal' data-bs-target='#kt_modal_verif' id='verif2'
                                                >
                                                    <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i> 
                                                    Verifikasi Ka.Bag. Tata Usaha
                                                </a>";
                                        }
                                    } elseif ($item->verif_3 == 0) {
                                        $status = "Menunggu verifikasi Wadir ADM dan Umum";
                                        $datas = "data-urut='$item->urut' data-tahun='$item->tahun_sk' data-kode='verif3' data-url='".route('admin.sk-kontrak.third-verification')."'";
                    
                                        if ($jabatan == 3 || $ruangan == 57) {
                                            $button = "
                                                <a 
                                                    href='javascript:void(0)'
                                                    class='btn btn-success btn-sm d-block mb-2'
                                                    title='Verifikasi Wadir ADM dan Umum' 
                                                    $datas 
                                                    data-bs-toggle='modal' data-bs-target='#kt_modal_verif' id='verif3'
                                                >
                                                    <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i> 
                                                    Verifikasi Wadir ADM dan Umum
                                                </a>";
                                        }
                                    } elseif ($item->verif_4 == 0) {
                                        $status = "Menunggu verifikasi Direktur";
                                        $datas = "data-urut='$item->urut' data-tahun='$item->tahun_sk' data-kode='verif4' data-url='".route('admin.sk-kontrak.fourth-verification')."'";
                    
                                        if ($jabatan == 1 || $ruangan == 57) {
                                            $button = "
                                                <a href='javascript:void(0)'
                                                    class='btn btn-warning btn-sm d-block mb-2'
                                                    title='Verifikasi Direktur'
                                                    $datas
                                                    data-bs-toggle='modal'
                                                    data-bs-target='#kt_modal_verif'
                                                    id='verif4'
                                                >
                                                    <i class='ki-duotone ki-double-check fs-2'><span class='path1'></span><span class='path2'></span></i> 
                                                    Verifikasi Direktur
                                                </a>";
                                        }
                                    } else {
                                        $status = "Telah diverifikasi";
                                        $tte_sk = "
                                            <a
                                                href='javascript:void(0)'
                                                class='btn btn-success btn-sm d-block mb-2'
                                                title='TTE SK'
                                                data-bs-toggle='modal'
                                                data-bs-target='#kt_modal_finalisasi'
                                                id='tte_sk'
                                            >
                                                <i class='ki-duotone ki-fingerprint-scanning fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i> Tanda Tangan Elektronik SK
                                            </a>
                                        ";
                                        $sk = "<a href='module/sk/print_sk.php?data=$item->urut&thn=$item->tahun_sk' class='btn btn-danger btn-sm d-block mb-2' title='SK' target='_blank'>
                                                    <i class='ki-duotone ki-printer fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i> SK
                                                </a>";
                                        $konsederan = "<a href='module/sk/print_konsederan.php?data=$item->urut&thn=$item->tahun_sk' class='btn btn-success btn-sm d-block mb-2' title='Konsederan' target='_blank'>
                                                            <i class='ki-duotone ki-printer fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i> Konsederan
                                                        </a>";
                                        $serahTerima = "<a href='module/sk/print_serahterima.php?data=$item->urut&thn=$item->tahun_sk' class='btn btn-info btn-sm d-block mb-2' title='Lembar Serah Terima' target='_blank'>
                                                            <i class='ki-duotone ki-printer fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i> Serah Terima
                                                        </a>";
                                        $button = $tte_sk.$sk.$konsederan.$serahTerima;
                                    }
                                @endphp

                                    <tr>
                                        <td class="text-center">
                                            {{-- loop iteration with increment when page change --}}
                                            {{-- {{ $loop->iteration + ($skKontrak->perPage() * ($skKontrak->currentPage() - 1)) }} --}}
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="text-center">
                                            {{ ($item->no_sk == "") ? "-" : "Peg. 445/".$item->no_sk."/SK/".$item->tahun_sk }}
                                        </td>
                                        <td class="text-center">
                                            {{ $item->jumlah_pegawai }}
                                        </td>
                                        <td>
                                            {!! $nama !!}
                                        </td>
                                        <td class="text-center">
                                            {{ date('d-m-Y', strtotime($item->tgl_sk)) }}
                                        </td>
                                        <td class="text-center">
                                            {{ date('d-m-Y', strtotime($item->tgl_ttd)) }}
                                        </td>
                                        <td class="text-center">
                                            {{ $status }}
                                        </td>
                                        <td class="">
                                            {!! $button !!}
                                            <a href='module/sk/print_pkerja.php?data={{ $item->urut }}&thn={{ $item->tahun_sk }}' class='btn btn-warning btn-sm d-block mb-2' title='Perjanjian Kerja' target='_blank'>
                                                <i class='ki-duotone ki-printer fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i>
                                                Perjanjian Kerja
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <div class="d-flex flex-stack flex-wrap pt-10">
                    <div class="fs-6 fw-semibold text-gray-700">
                        Menampilkan {{ $skKontrak->firstItem() }} sampai {{ $skKontrak->lastItem() }} dari {{ $skKontrak->total() }} data
                    </div>
                    {{ $skKontrak->links() }}
                </div> --}}
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Add sk-->
<div class="modal fade" id="kt_modal_add_sk" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-800px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_sk_header">
                <h2 class="fw-bold">Tambah SK Kontrak</h2>

                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-action="close">
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
                            <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-action="cancel">
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

<div class="modal fade" id="kt_modal_verif" tabindex="-1" aria-hidden="true">
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

<!--begin::Modal - Verifikasi-->
<div class="modal fade" id="kt_modal_finalisasi" data-bs-focus="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
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
                    <div class="d-flex flex-column fv-row mb-5">
                        <label
                            class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
                        >
                            Tanggal Tanda Tangan SK
                        </label>
                        <input
                            class="form-control form-control-solid"
                            name="tgl_ttd_sk"
                            id="tgl_ttd_sk"
                            placeholder="Pilih Tanggal Tanda Tangan SK"
                        />
                        <div class="fv-plugins-message-container invalid-feedback error-text tujuan_error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer text-end">
                <button type="reset" class="btn btn-light me-3" data-kt-menu-modal-finalisasi="cancel" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary" data-kt-menu-modal-action-finalisasi="submit" disabled>
                    <span class="indicator-label">Proses TTE</span>
                    <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Verifikasi-->

@endsection

@push('scripts')
    <script>
        "use strict";

        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });


        var KTSKList = (function () {
            var table,
            $table = $('#sk-table');

            return {
                init: function () {
                    if ($table.length) {
                        table = $table.DataTable({
                            info: true,
                            order: [],
                            // pageLength: 10,
                            displayLength: 10,
                            lengthChange: true,
                            columnDefs: [
                                { orderable: false, targets: [0, 7] }
                                // { orderable: false, targets: 7 },
                            ],
                        });

                        $('[data-kt-sk-table-filter="search"]').on("keyup", function () {
                            // table.search($(this).val()).draw();
                            table.column(3).search($(this).val()).draw();
                        });

                        $('[data-kt-user-table-filter="reset"]').on("click", function () {
                            $('[data-kt-user-table-filter="form"] select').val("").trigger("change");
                            table.search("").draw();
                        });

                        $('[data-kt-user-table-filter="form"] [data-kt-user-table-filter="filter"]').on("click", function () {
                            var filterString = "";
                            $('[data-kt-user-table-filter="form"] select').each(function (index) {
                                // if (this.value && this.value !== "") {
                                //     if (index !== 0) {
                                //         filterString += " ";
                                //     }
                                //     filterString += this.value;
                                // }
                                if (this.value) {
                                    filterString += (index !== 0 ? " " : "") + this.value;
                                }
                            });
                            table.search(filterString).draw();
                        });
                    }
                },
            };
        })();

        function toggleButtonState(buttonSelector, tableSelector) {
            let isAnyChecked = $(tableSelector + ' .form-check-input:checked').length > 0;
            $(buttonSelector).attr('disabled', !isAnyChecked);
        }

        function showModal(title) {
            $('#verif-title').text(title);
            // $('#rincian-verif').html($(rincianId).html());
            $('#kt_modal_verif').modal('show');
        }

        $(document).ready(function () {
            KTSKList.init();

            $('#tahun_sk').on('change', function () {
                var tahun = $(this).val();
                let url = '{{ route("admin.sk-kontrak.index") }}' + (tahun ? '?tahun_sk=' + tahun : '');

                window.location.href = url;
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

            $('#verif4').on('click', function () {
                showModal('Verifikasi Direktur');
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
            $('#tgl_ttd_sk').flatpickr({
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

        // ketika #verif1, 2, 3, 4 ditekan maka tampilkan modal #kt_modal_verif_1
        $(document).on('click', '#verif1, #verif2, #verif3, #verif4', function () {
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
            } else if ($(this).attr('id') === 'verif4') {
                title = 'Verifikasi Direktur';
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

        // buka modal finalisasi ketika di klik
        $(document).on('click', '#tte_sk', function () {
            var urut = $(this).data('urut');
            var tahun = $(this).data('tahun');

            $('#urut_rincian_verif').val(urut);
            $('#tahun_rincian_verif').val(tahun);
            $('#finalisasi-title').text('Proses TTE SK');
            $('#kt_modal_finalisasi').modal('show');
        });

        $('#sk-table').on('draw.dt', function () {
            $('#kt_rincian_verif_1_table .form-check-input').prop('checked', false);
            $('#kt_rincian_verif_1_table .header-checkbox').prop('checked', false);

            toggleButtonState('[data-kt-menu-modal-action-verif-1="submit"]', '#kt_rincian_verif_1_table');
            toggleButtonState('[data-kt-menu-modal-action-verif-2="submit"]', '#kt_rincian_verif_1_table');
            toggleButtonState('[data-kt-menu-modal-action-verif-3="submit"]', '#kt_rincian_verif_1_table');
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
    </script>
@endpush