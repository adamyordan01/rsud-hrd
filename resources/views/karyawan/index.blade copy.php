@extends('layouts.backend', ['title' => 'Seluruh Karyawan'])

@push('styles')
    <style>
        #karyawan-table th {
            font-size: 12px;
        }

        #karyawan-table td {
            font-size: 12px;
        }

        /* vertical align middle of th */
        #karyawan-table th {
            vertical-align: middle;
        }

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
                        Karyawan
                    </h1>
                    <!--end::Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->

                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            Seluruh Karyawan </li>
                        <!--end::Item-->

                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    {{-- <a href="#"
                        class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                        Add Member
                    </a> --}}

                    <a
                        href="{{ route('admin.karyawan.create') }}"
                        class="btn btn-flex btn-primary h-40px fs-7 fw-bold"
                    >
                        Tambah Karyawan
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
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-end">
                            <i class="ki-outline ki-filter fs-2"></i> Filter
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                            id="kt-toolbar-filter">
                            <div class="px-7 py-5">
                                <div class="fs-4 text-gray-900 fw-bold">Filter Options</div>
                            </div>

                            <div class="separator border-gray-200"></div>

                            <div class="px-7 py-5">
                                <div class="mb-10">
                                    <label class="form-label fs-5 fw-semibold mb-3">Jenis Tenaga:</label>

                                    <div class="d-flex flex-column flex-wrap fw-semibold"
                                        data-kt-customer-table-filter="jenis_tenaga">
                                        <label
                                            class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga" value="all"
                                                checked="checked">
                                            <span class="form-check-label text-gray-600">
                                                Seluruh Pegawai
                                            </span>
                                        </label>

                                        <label
                                            class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga"
                                                value="PNS">
                                            <span class="form-check-label text-gray-600">
                                                PNS
                                            </span>
                                        </label>

                                        <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                            <input class="form-check-input" type="radio" name="jenis_tenaga"
                                                value="HONOR">
                                            <span class="form-check-label text-gray-600">
                                                Honor
                                            </span>
                                        </label>

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
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light btn-active-light-primary me-2"
                                        data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Reset</button>

                                    <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true"
                                        data-kt-customer-table-filter="filter">Apply</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal"
                            data-bs-target="#kt_customers_export_modal">
                            <i class="ki-outline ki-exit-up fs-2"></i> Export
                        </button>

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_customer">
                            Add Customer
                        </button>
                    </div>

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
                        <table id="karyawan-table" class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold">
                            <thead>
                                <tr>
                                    <th class="w-10px pe-2 sorting_disabled" rowspan="3"
                                    >
                                        ID Peg.
                                    </th>
                                    <th class="min-w-125px text-center sorting" tabindex="0" aria-controls="kt_customers_table"
                                        rowspan="3"
                                    >
                                        Nama <br>
                                        TTL <br>
                                        NIP / No. Karpeg
                                    </th>
                                    <th class="min-w-35px text-center sorting" tabindex="0"
                                        rowspan="3">
                                        L/P
                                    </th>
                                    <th class="min-w-155px text-center sorting" tabindex="0"
                                        colspan="3"
                                    >
                                        Kepangkatan Sekarang
                                    </th>
                                    <th class="min-w-80px text-center sorting" tabindex="0"
                                        rowspan="3"
                                    >
                                        Eselon TMT
                                    </th>
                                    <th class="min-w-125px text-center sorting" tabindex="0"
                                        rowspan="3"
                                    >
                                        Pend. Terakhir
                                    </th>
                                    <th class="min-w-125px text-center sorting" tabindex="0"
                                        rowspan="3"
                                    >
                                        Sub. Jenis tenaga
                                        <br>
                                        Ruangan
                                    </th>
                                    <th class="min-w-75px text-center sorting" tabindex="0"
                                        rowspan="3"
                                    >
                                        Status
                                    </th>
                                    <th class="min-w-100px text-center sorting" tabindex="0"
                                        rowspan="3"
                                    >
                                        Rek. BSI
                                    </th>
                                    <th class="min-w-90px text-center sorting" tabindex="0"
                                        rowspan="3"
                                    >
                                        Action
                                    </th>
                                </tr>
                                <tr>
                                    <th class="min-w-125px text-center sorting" tabindex="0"
                                        rowspan="2"
                                    >
                                        Pangkat / Gol. <br> TMT
                                    </th>
                                    <th class="min-w-125px text-center sorting" tabindex="0"
                                        colspan="2"
                                    >
                                        Masa Kerja
                                    </th>
                                </tr>
                                <tr>
                                    <th class="min-w-25px text-center sorting" tabindex="0">
                                        Thn.
                                    </th>
                                    <th class="min-w-25px text-center sorting" tabindex="0">
                                        Bln.
                                    </th>
                            </thead>
                        </table>
                        {{-- {{ $dataTable->table() }} --}}
                        {{-- <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer"
                            id="kt_customers_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2 sorting_disabled" rowspan="1" colspan="1" aria-label=""
                                        style="width: 29.9px;">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#kt_customers_table .form-check-input" value="1">
                                        </div>
                                    </th>
                                    <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                        rowspan="1" colspan="1"
                                        aria-label="Customer Name: activate to sort column ascending"
                                        style="width: 145.163px;">Customer Name</th>
                                    <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                        rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending"
                                        style="width: 179.95px;">Email</th>
                                    <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                        rowspan="1" colspan="1" aria-label="Company: activate to sort column ascending"
                                        style="width: 159.688px;">Company</th>
                                    <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                        rowspan="1" colspan="1"
                                        aria-label="Payment Method: activate to sort column ascending"
                                        style="width: 147.688px;">Payment Method</th>
                                    <th class="min-w-125px sorting" tabindex="0" aria-controls="kt_customers_table"
                                        rowspan="1" colspan="1"
                                        aria-label="Created Date: activate to sort column ascending"
                                        style="width: 188.962px;">Created Date</th>
                                    <th class="text-end min-w-70px sorting_disabled" rowspan="1" colspan="1"
                                        aria-label="Actions" style="width: 111.15px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                <tr class="odd">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Emma Smith</a>
                                    </td>
                                    <td>
                                        <a href="#" class="text-gray-600 text-hover-primary mb-1">smith@kpmg.com</a>
                                    </td>
                                    <td>
                                        - </td>
                                    <td data-filter="mastercard">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/mastercard.svg"
                                            class="w-35px me-3" alt="">
                                        **** 2277 </td>
                                    <td data-order="2020-12-14T20:43:00+07:00">
                                        14 Dec 2020, 8:43 pm </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Melody Macy</a>
                                    </td>
                                    <td>
                                        <a href="#" class="text-gray-600 text-hover-primary mb-1">melody@altbox.com</a>
                                    </td>
                                    <td>
                                        Google </td>
                                    <td data-filter="visa">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/visa.svg"
                                            class="w-35px me-3" alt="">
                                        **** 2807 </td>
                                    <td data-order="2020-12-01T10:12:00+07:00">
                                        01 Dec 2020, 10:12 am </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Max Smith</a>
                                    </td>
                                    <td>
                                        <a href="#" class="text-gray-600 text-hover-primary mb-1">max@kt.com</a>
                                    </td>
                                    <td>
                                        Bistro Union </td>
                                    <td data-filter="mastercard">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/mastercard.svg"
                                            class="w-35px me-3" alt="">
                                        **** 2884 </td>
                                    <td data-order="2020-11-12T14:01:00+07:00">
                                        12 Nov 2020, 2:01 pm </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Sean Bean</a>
                                    </td>
                                    <td>
                                        <a href="#" class="text-gray-600 text-hover-primary mb-1">sean@dellito.com</a>
                                    </td>
                                    <td>
                                        Astro Limited </td>
                                    <td data-filter="american_express">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/american-express.svg"
                                            class="w-35px me-3" alt="">
                                        **** 3433 </td>
                                    <td data-order="2020-10-21T17:54:00+07:00">
                                        21 Oct 2020, 5:54 pm </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Brian Cox</a>
                                    </td>
                                    <td>
                                        <a href="#" class="text-gray-600 text-hover-primary mb-1">brian@exchange.com</a>
                                    </td>
                                    <td>
                                        - </td>
                                    <td data-filter="visa">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/visa.svg"
                                            class="w-35px me-3" alt="">
                                        **** 9115 </td>
                                    <td data-order="2020-10-19T07:32:00+07:00">
                                        19 Oct 2020, 7:32 am </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Mikaela Collins</a>
                                    </td>
                                    <td>
                                        <a href="#" class="text-gray-600 text-hover-primary mb-1">mik@pex.com</a>
                                    </td>
                                    <td>
                                        Keenthemes </td>
                                    <td data-filter="american_express">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/american-express.svg"
                                            class="w-35px me-3" alt="">
                                        **** 4164 </td>
                                    <td data-order="2020-09-23T00:37:00+07:00">
                                        23 Sep 2020, 12:37 am </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Francis Mitcham</a>
                                    </td>
                                    <td>
                                        <a href="#" class="text-gray-600 text-hover-primary mb-1">f.mit@kpmg.com</a>
                                    </td>
                                    <td>
                                        Paypal </td>
                                    <td data-filter="mastercard">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/mastercard.svg"
                                            class="w-35px me-3" alt="">
                                        **** 3425 </td>
                                    <td data-order="2020-09-11T15:15:00+07:00">
                                        11 Sep 2020, 3:15 pm </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Olivia Wild</a>
                                    </td>
                                    <td>
                                        <a href="#"
                                            class="text-gray-600 text-hover-primary mb-1">olivia@corpmail.com</a>
                                    </td>
                                    <td>
                                        - </td>
                                    <td data-filter="american_express">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/american-express.svg"
                                            class="w-35px me-3" alt="">
                                        **** 7955 </td>
                                    <td data-order="2020-09-03T01:08:00+07:00">
                                        03 Sep 2020, 1:08 am </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Neil Owen</a>
                                    </td>
                                    <td>
                                        <a href="#"
                                            class="text-gray-600 text-hover-primary mb-1">owen.neil@gmail.com</a>
                                    </td>
                                    <td>
                                        Paramount </td>
                                    <td data-filter="visa">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/visa.svg"
                                            class="w-35px me-3" alt="">
                                        **** 7949 </td>
                                    <td data-order="2020-09-01T16:58:00+07:00">
                                        01 Sep 2020, 4:58 pm </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                <tr class="even">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="/metronic8/demo39/apps/customers/view.html"
                                            class="text-gray-800 text-hover-primary mb-1">Dan Wilson</a>
                                    </td>
                                    <td>
                                        <a href="#" class="text-gray-600 text-hover-primary mb-1">dam@consilting.com</a>
                                    </td>
                                    <td>
                                        Trinity Studio </td>
                                    <td data-filter="visa">
                                        <img src="/metronic8/demo39/assets/media/svg/card-logos/visa.svg"
                                            class="w-35px me-3" alt="">
                                        **** 6496 </td>
                                    <td data-order="2020-08-18T15:34:00+07:00">
                                        18 Aug 2020, 3:34 pm </td>
                                    <td class="text-end">
                                        <a href="#"
                                            class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <i class="ki-outline ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/metronic8/demo39/apps/customers/view.html"
                                                    class="menu-link px-3">
                                                    View
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-customer-table-filter="delete_row">
                                                    Delete
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                            </tbody>
                        </table> --}}
                    </div>
                </div>
                <!--end::Table-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->

        <!--begin::Modals-->
        <!--end::Modals-->
    </div>
    <!--end::Content container-->
</div>
@endsection

@push('scripts')
    <script>
        "use strict";

        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        // class definition
        var KTDatatablesServerSide = function () {
            var table;
            var dt;
            var filterPayment;

            // private functions
            var initDatatable = function () {
                dt = $('#karyawan-table').DataTable({
                    // responsive: true,
                    searchDelay: 300,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.karyawan.index') }}",
                        type: 'GET',
                        dataTyp: 'json'
                    },
                    columns: [
                        { data: 'id_pegawai', name: 'kd_karyawan' },
                        { data: 'nama_lengkap', name: 'nama' },
                        { data: 'jenis_kelamin', name: 'jenis_kelamin', searchable: false },
                        { data: 'golongan', name: 'golongan' },
                        { data: 'masa_kerja_thn', name: 'masa_kerja_thn' },
                        { data: 'masa_kerja_bulan', name: 'masa_kerja_bulan' },
                        { data: 'eselon', name: 'eselon', orderable: false, searchable: false },
                        { data: 'pendidikan', name: 'pendidikan' },
                        { data: 'sub_detail', name: 'sub_detail' },
                        { data: 'status_kerja', name: 'status_kerja' },
                        { data: 'rek_bni_syariah', name: 'rek_bni_syariah' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    columnDefs: [
                        {
                            targets: -1,
                            data: null,
                            orderable: false,
                            className: 'text-center'
                        },
                    ],
                    "createdRow": function (row, data, dataIndex) {
                        $(row).find('td:eq(2)').addClass('text-center');
                        $(row).find('td:eq(3)').addClass('text-center');
                        $(row).find('td:eq(4)').addClass('text-center');
                        $(row).find('td:eq(5)').addClass('text-center');
                        $(row).find('td:eq(6)').addClass('text-center');
                        $(row).find('td:eq(9)').addClass('text-center');
                        // $(row).find('td:eq(9)').attr('data-filter', data.status_kerja);
                        $(row).find('td:eq(10)').addClass('text-center');
                    },
                });

                // $('#search').on('click', function () {
                //     dt.search($('#searchValue').val()).draw();
                // });

                table = dt.$;

                dt.on('draw', function () {
                    KTMenu.createInstances();
                });
            }

            var handleSearchDatatable = function () {
                const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
                filterSearch.addEventListener('keyup', function (e) {
                // filterSearch.addEventListener('keypress', function (e) {
                    dt.search(e.target.value).draw();
                });
            }

            // Filter Datatable
            var handleFilterDatatable = () => {
                // Select filter options
                filterPayment = document.querySelectorAll('[data-kt-customer-table-filter="jenis_tenaga"] [name="jenis_tenaga"]');
                const filterButton = document.querySelector('[data-kt-customer-table-filter="filter"]');
                
                // $(document).on('click', '[data-kt-customer-table-filter="filter"]', function() {
                //     alert('ok');
                // });

                // Filter datatable on submit
                filterButton.addEventListener('click', function () {
                    // alert('ok');
                    // Get filter values
                    let paymentValue = '';

                    // Get payment value
                    filterPayment.forEach(r => {
                        if (r.checked) {
                            paymentValue = r.value;
                        }

                        // Reset payment value if "All" is selected
                        if (paymentValue === 'all') {
                            paymentValue = '';
                        }
                    });

                    // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
                    dt.search(paymentValue).draw();
                });
            }

            // Delete customer
            var handleDeleteRows = () => {
                // Select all delete buttons
                const deleteButtons = document.querySelectorAll('[data-kt-docs-table-filter="delete_row"]');

                deleteButtons.forEach(d => {
                    // Delete button on click
                    d.addEventListener('click', function (e) {
                        e.preventDefault();

                        // Select parent row
                        const parent = e.target.closest('tr');

                        // Get customer name
                        const customerName = parent.querySelectorAll('td')[1].innerText;

                        // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                        Swal.fire({
                            text: "Are you sure you want to delete " + customerName + "?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Yes, delete!",
                            cancelButtonText: "No, cancel",
                            customClass: {
                                confirmButton: "btn fw-bold btn-danger",
                                cancelButton: "btn fw-bold btn-active-light-primary"
                            }
                        }).then(function (result) {
                            if (result.value) {
                                // Simulate delete request -- for demo purpose only
                                Swal.fire({
                                    text: "Deleting " + customerName,
                                    icon: "info",
                                    buttonsStyling: false,
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(function () {
                                    Swal.fire({
                                        text: "You have deleted " + customerName + "!.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    }).then(function () {
                                        // delete row data from server and re-draw datatable
                                        dt.draw();
                                    });
                                });
                            } else if (result.dismiss === 'cancel') {
                                Swal.fire({
                                    text: customerName + " was not deleted.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        });
                    })
                });
            }

            // Reset Filter
            var handleResetForm = () => {
                // Select reset button
                const resetButton = document.querySelector('[data-kt-docs-table-filter="reset"]');

                // Reset datatable
                resetButton.addEventListener('click', function () {
                    // Reset payment type
                    filterPayment[0].checked = true;

                    // Reset datatable --- official docs reference: https://datatables.net/reference/api/search()
                    dt.search('').draw();
                });
            }

            // Init toggle toolbar
            var initToggleToolbar = function () {
                // Toggle selected action toolbar
                // Select all checkboxes
                // const container = document.querySelector('#kt_datatable_example_1');
                // const checkboxes = container.querySelectorAll('[type="checkbox"]');

                // // Select elements
                // const deleteSelected = document.querySelector('[data-kt-docs-table-select="delete_selected"]');

                // // Toggle delete selected toolbar
                // checkboxes.forEach(c => {
                //     // Checkbox on click event
                //     c.addEventListener('click', function () {
                //         setTimeout(function () {
                //             toggleToolbars();
                //         }, 50);
                //     });
                // });

                // // Deleted selected rows
                // deleteSelected.addEventListener('click', function () {
                //     // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                //     Swal.fire({
                //         text: "Are you sure you want to delete selected customers?",
                //         icon: "warning",
                //         showCancelButton: true,
                //         buttonsStyling: false,
                //         showLoaderOnConfirm: true,
                //         confirmButtonText: "Yes, delete!",
                //         cancelButtonText: "No, cancel",
                //         customClass: {
                //             confirmButton: "btn fw-bold btn-danger",
                //             cancelButton: "btn fw-bold btn-active-light-primary"
                //         },
                //     }).then(function (result) {
                //         if (result.value) {
                //             // Simulate delete request -- for demo purpose only
                //             Swal.fire({
                //                 text: "Deleting selected customers",
                //                 icon: "info",
                //                 buttonsStyling: false,
                //                 showConfirmButton: false,
                //                 timer: 2000
                //             }).then(function () {
                //                 Swal.fire({
                //                     text: "You have deleted all selected customers!.",
                //                     icon: "success",
                //                     buttonsStyling: false,
                //                     confirmButtonText: "Ok, got it!",
                //                     customClass: {
                //                         confirmButton: "btn fw-bold btn-primary",
                //                     }
                //                 }).then(function () {
                //                     // delete row data from server and re-draw datatable
                //                     dt.draw();
                //                 });

                //                 // Remove header checked box
                //                 const headerCheckbox = container.querySelectorAll('[type="checkbox"]')[0];
                //                 headerCheckbox.checked = false;
                //             });
                //         } else if (result.dismiss === 'cancel') {
                //             Swal.fire({
                //                 text: "Selected customers was not deleted.",
                //                 icon: "error",
                //                 buttonsStyling: false,
                //                 confirmButtonText: "Ok, got it!",
                //                 customClass: {
                //                     confirmButton: "btn fw-bold btn-primary",
                //                 }
                //             });
                //         }
                //     });
                // });
            }

            // Toggle toolbars
            var toggleToolbars = function () {
                // Define variables
                // const container = document.querySelector('#kt_datatable_example_1');
                // const toolbarBase = document.querySelector('[data-kt-docs-table-toolbar="base"]');
                // const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
                // const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');

                // // Select refreshed checkbox DOM elements
                // const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

                // // Detect checkboxes state & count
                // let checkedState = false;
                // let count = 0;

                // // Count checked boxes
                // allCheckboxes.forEach(c => {
                //     if (c.checked) {
                //         checkedState = true;
                //         count++;
                //     }
                // });

                // // Toggle toolbars
                // if (checkedState) {
                //     selectedCount.innerHTML = count;
                //     toolbarBase.classList.add('d-none');
                //     toolbarSelected.classList.remove('d-none');
                // } else {
                //     toolbarBase.classList.remove('d-none');
                //     toolbarSelected.classList.add('d-none');
                // }
            }

            // public methods
            return {
                init: function () {
                    initDatatable();
                    handleSearchDatatable();
                    initToggleToolbar();
                    handleFilterDatatable();
                    handleDeleteRows();
                    handleResetForm();
                }
            }
        }()

        // on document ready
        KTUtil.onDOMContentLoaded(function () {
            KTDatatablesServerSide.init();
        });
        
    </script>
@endpush