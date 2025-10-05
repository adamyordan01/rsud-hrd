@extends('layouts.backend', ['title' => 'Seluruh Karyawan'])

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
                        Projects Dashboard
                    </h1>
                    <!--end::Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="/metronic8/demo39/index.html" class="text-muted text-hover-primary">
                                Home </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            Dashboards </li>
                        <!--end::Item-->

                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="#"
                        class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                        Add Member
                    </a>

                    <a href="#" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_create_campaign">
                        New Campaign
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


    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container  container-fluid ">
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <!--begin::Card title-->
                <div class="card-title">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i> <input type="text"
                            data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-12"
                            placeholder="Cari Karyawan ...">
                    </div>
                    <!--end::Search-->
                </div>
                <!--begin::Card title-->

                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                        <!--begin::Filter-->
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-end">
                            <i class="ki-outline ki-filter fs-2"></i> Filter
                        </button>
                        <!--begin::Menu 1-->
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                            id="kt-toolbar-filter">
                            <!--begin::Header-->
                            <div class="px-7 py-5">
                                <div class="fs-4 text-gray-900 fw-bold">Filter Options</div>
                            </div>
                            <!--end::Header-->

                            <!--begin::Separator-->
                            <div class="separator border-gray-200"></div>
                            <!--end::Separator-->

                            <!--begin::Content-->
                            <div class="px-7 py-5">

                                <!--begin::Input group-->
                                <div class="mb-10">
                                    <!--begin::Label-->
                                    <label class="form-label fs-5 fw-semibold mb-3">Jenis Tenaga:</label>
                                    <!--end::Label-->

                                    <!--begin::Options-->
                                    <div class="d-flex flex-column flex-wrap fw-semibold"
                                        data-kt-customer-table-filter="jenis_tenaga">
                                        <!--begin::Option-->
                                        <label
                                            class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga" value="all"
                                                checked="checked">
                                            <span class="form-check-label text-gray-600">
                                                Seluruh Pegawai
                                            </span>
                                        </label>
                                        <!--end::Option-->

                                        <!--begin::Option-->
                                        <label
                                            class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga"
                                                value="PNS">
                                            <span class="form-check-label text-gray-600">
                                                PNS
                                            </span>
                                        </label>
                                        <!--end::Option-->

                                        <!--begin::Option-->
                                        <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga"
                                                value="HONOR">
                                            <span class="form-check-label text-gray-600">
                                                Honor
                                            </span>
                                        </label>
                                        <!--end::Option-->

                                        <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga"
                                                value="KONTRAK">
                                            <span class="form-check-label text-gray-600">
                                                Kontrak BLUD
                                            </span>
                                        </label>

                                        <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga"
                                                value="KONTRAK">
                                            <span class="form-check-label text-gray-600">
                                                Kontrak PEMKO
                                            </span>
                                        </label>

                                        <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga"
                                                value="PART TIME">
                                            <span class="form-check-label text-gray-600">
                                                Part Time
                                            </span>
                                        </label>

                                        <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga"
                                                value="TENAGA HARIAN LEPAS">
                                            <span class="form-check-label text-gray-600">
                                                THL
                                            </span>
                                        </label>
                                    </div>
                                    <!--end::Options-->
                                </div>
                                <!--end::Input group-->

                                <!--begin::Actions-->
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light btn-active-light-primary me-2"
                                        data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Reset</button>

                                    <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true"
                                        data-kt-customer-table-filter="filter">Apply</button>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Menu 1-->
                        <!--end::Filter-->

                        <!--begin::Export-->
                        <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal"
                            data-bs-target="#kt_customers_export_modal">
                            <i class="ki-outline ki-exit-up fs-2"></i> Export
                        </button>
                        <!--end::Export-->

                        <!--begin::Add customer-->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_customer">
                            Add Customer
                        </button>
                        <!--end::Add customer-->
                    </div>
                    <!--end::Toolbar-->

                    <!--begin::Group actions-->
                    <div class="d-flex justify-content-end align-items-center d-none"
                        data-kt-customer-table-toolbar="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-customer-table-select="selected_count"></span> Selected
                        </div>

                        <button type="button" class="btn btn-danger" data-kt-customer-table-select="delete_selected">
                            Delete Selected
                        </button>
                    </div>
                    <!--end::Group actions-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-0">

                <!--begin::Table-->
                <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table id="karyawan-table" class="table align-middle table-row-dashed table-bordered fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
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
                                    


                                    @if ($item->nomor_konsederan == "")
                                        {{-- $query = sqlsrv_query($konek, "select KD_KARYAWAN, GELAR_DEPAN, NAMA, GELAR_BELAKANG from VIEW_TAMPIL_KARYAWAN where KD_KARYAWAN = (select KD_KARYAWAN from HRD_SK_PEGAWAI_KONTRAK where URUT = '".$r['URUT']."')");
                                        $hasil = sqlsrv_fetch_array($query);
                                        $nama = $hasil['KD_KARYAWAN']."<br>".$hasil['GELAR_DEPAN']." ".$hasil['NAMA'].$hasil['GELAR_BELAKANG']; --}}
                                        @php
                                            // $getKaryawan = $DB::table('view_tampil_karyawan as vtk')
                                            //     ->select('vtk.kd_karyawan', 'vtk.gelar_depan', 'vtk.nama', 'vtk.gelar_belakang')
                                            //     ->where('vtk.kd_karyawan', $item->kd_karyawan)
                                            //     ->first();
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
                                            $nama = $getKaryawan->kd_karyawan."<br>".$getKaryawan->gelar_depan." ".$getKaryawan->nama.$getKaryawan->gelar_belakang;
                                        @endphp
                                    @else
                                        @php
                                            $nama = "~";
                                        @endphp
                                    @endif

                                    @php
                                        $datas = "";
                                    @endphp

                                    @if ($item->verif_1 == 0)
                                        @php
                                            $status = "Menunggu verifikasi Kasubbag. Kepegawaian";
                                            // $datas = "data-urut='".$r['URUT']."' data-tahun='".$r['TAHUN_SK']."' data-kode='verif1'";
                                            $datas = "data-urut='".$item->urut."' data-tahun='".$item->tahun_sk."' data-kode='verif1'";
                                        @endphp
                                        @if ($jabatan == 19 || $ruangan == 57)
                                            @php
                                                $button = "
                                                    <a href='javascript:void(0)' class='btn btn-info btn-sm d-block mb-2' title='Verifikasi Kasubbag. Kepegawaian' ".$datas." onclick='detail(this)'>
                                                        <i class='ki-duotone ki-double-check fs-2'>
                                                            <span class='path1'></span>
                                                            <span class='path2'></span>
                                                        </i> Verifikasi <br> Ka.Sub.Bag. Kepeg.
                                                    </a>
                                                ";
                                            @endphp
                                        @else
                                            @php
                                                $button = "";
                                            @endphp
                                        @endif
                                    @elseif($item->verif_2 == 0)
                                        @php
                                            $status = "Menunggu verifikasi Kabag. TU";
                                            // $datas = "data-urut='".$r['URUT']."' data-tahun='".$r['TAHUN_SK']."' data-kode='verif2'";
                                            $datas = "data-urut='".$item->urut."' data-tahun='".$item->tahun_sk."' data-kode='verif2'";
                                        @endphp
                                        @if ($jabatan == 7 || $ruangan == 57)
                                            @php
                                                $button = "
                                                    <a href='javascript:void(0)' class='btn btn-primary btn-sm d-block mb-2' title='Verifikasi Kabag. TU' ".$datas." onclick='detail(this)'>
                                                        <i class='ki-duotone ki-double-check fs-2'>
                                                            <span class='path1'></span>
                                                            <span class='path2'></span>
                                                        </i> Verifikasi <br> Ka.Bag. Tata Usaha
                                                    </a>
                                                ";
                                            @endphp
                                        @else
                                            @php
                                                $button = "";
                                            @endphp
                                        @endif
                                    @elseif($item->verif_3 == 0)
                                        @php
                                            $status = "Menunggu verifikasi Wadir ADM dan Umum";
                                            // $datas = "data-urut='".$r['URUT']."' data-tahun='".$r['TAHUN_SK']."' data-kode='verif3'";
                                            $datas = "data-urut='".$item->urut."' data-tahun='".$item->tahun_sk."' data-kode='verif3'";
                                        @endphp
                                        @if ($jabatan == 3 || $ruangan == 57)
                                            @php
                                                $button = "
                                                    <a href='javascript:void(0)' class='btn btn-success btn-sm d-block mb-2' title='Verifikasi Wadir ADM dan Umum' ".$datas." onclick='detail(this)'>
                                                        <i class='ki-duotone ki-double-check fs-2'>
                                                            <span class='path1'></span>
                                                            <span class='path2'></span>
                                                        </i> Verifikasi <br> Wadir ADM dan Umum
                                                    </a>
                                                ";
                                            @endphp
                                        @else
                                            @php
                                                $button = "";
                                            @endphp
                                        @endif
                                    @elseif($item->verif_4 == 0)
                                        @if ($jabatan == 1 || $ruangan == 57)
                                            @php
                                                $datas = "data-urut='".$item->urut."' data-tahun='".$item->tahun_sk."' data-kode='verif4'";
                                            // <a href="#" class="btn btn-sm d-block mb-2 btn-flex btn-secondary fw-bold" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                //     <i class="ki-duotone ki-filter fs-6 text-muted me-1"><span class="path1"></span><span class="path2"></span></i>               
                                                //     Filter
                                                // </a>
                                                $button = "
                                                    <a href='javascript:void(0)' class='btn btn-warning btn-sm d-block mb-2' title='Verifikasi Direktur' ".$datas." onclick='detail(this)'>
                                                        <i class='ki-duotone ki-double-check fs-2'>
                                                            <span class='path1'></span>
                                                            <span class='path2'></span>
                                                        </i> Verifikasi <br> Direktur
                                                    </a>
                                                ";
                                            @endphp
                                        @else
                                            @php
                                                $button = "";
                                            @endphp
                                        @endif
                                        @php
                                            $status = "Menunggu verifikasi Direktur";
                                        @endphp
                                    @else
                                         @php
                                             $status = "Telah diverifikasi";
                                            $datas = "";
                                         @endphp
                                        @if ($item->nomor_konsederan == "")
                                            @php
                                                $button = "
                                                    <a href='module/sk/print_sk.php?data=".$item->urut."&thn=".$item->tahun_sk."' class='btn btn-primary btn-sm d-block mb-2' title='SK' target='_blank' style='width:120px;text-align:left;'><i class='ki-duotone ki-printer fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i> SK</a>
                                                ";
                                            @endphp
                                        @else
                                            @php
                                                // $button = "<a href='module/sk/print_sk.php?data=".$item->urut."&thn=".$item->tahun_sk."' class='btn btn-danger btn-xs' title='SK' target='_blank' style='width:120px;text-align:left;'><i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> SK</a>
                                                // <a href='module/sk/print_konsederan.php?data=".$item->urut."&thn=".$item->tahun_sk."' class='btn btn-success btn-xs' title='Konsederan' target='_blank' style='width:120px;text-align:left;'><i class='fa fa-print'></i> Konsederan</a>
                                                // <a href='module/sk/print_serahterima.php?data=".$item->urut."&thn=".$item->tahun_sk."' class='btn btn-info btn-xs' title='Lembar Serah Terima' target='_blank' style='width:120px;text-align:left;'><i class='fa fa-print'></i> Serah Terima</a>";
                                                $sk = "
                                                    <a href='module/sk/print_sk.php?data=".$item->urut."&thn=".$item->tahun_sk."' class='btn btn-primary btn-sm d-block mb-2' title='SK' target='_blank'><i class='ki-duotone ki-printer fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i> SK</a>
                                                ";

                                                $konsederan = "
                                                    <a href='module/sk/print_konsederan.php?data=".$item->urut."&thn=".$item->tahun_sk."' class='btn btn-primary btn-sm d-block mb-2' title='Konsederan' target='_blank'><i class='ki-duotone ki-printer fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i> Konsederan</a>
                                                ";

                                                $serahTerima = "
                                                    <a href='module/sk/print_serahterima.php?data=".$item->urut."&thn=".$item->tahun_sk."' class='btn btn-primary btn-sm d-block mb-2' title='Lembar Serah Terima' target='_blank'><i class='ki-duotone ki-printer fs-2'><span class='path1'></span><span class='path2'></span><span class='path3'></span><span class='path4'></span><span class='path5'></span></i> Serah Terima</a>
                                                ";

                                                $button = $sk.$konsederan.$serahTerima;
                                            @endphp
                                        @endif
                                    @endif

                                    <tr>
                                        <td class="text-center">
                                            {{-- loop iteration with increment when page change --}}
                                            {{ $loop->iteration + ($skKontrak->perPage() * ($skKontrak->currentPage() - 1)) }}
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
                                            <a href='module/sk/print_pkerja.php?data={{ $item->urut }}&thn={{ $item->tahun_sk }}' class='btn btn-success btn-sm' title='Perjanjian Kerja' target='_blank'>
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
                <div class="d-flex flex-stack flex-wrap pt-10">
                    <div class="fs-6 fw-semibold text-gray-700">
                        {{-- Showing 1 to 10 of 50 entries --}}
                        Menampilkan {{ $skKontrak->firstItem() }} sampai {{ $skKontrak->lastItem() }} dari {{ $skKontrak->total() }} data
                    </div>
                
                    <!--begin::Pages-->
                    {{ $skKontrak->links() }}
                    <!--end::Pages-->
                </div>
                <!--end::Table-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Content container-->
</div>
@endsection

@push('scripts')
    <script>
    </script>
@endpush