@extends('layouts.user')

@section('title', 'KGB')
@section('page-title', 'Kenaikan Gaji Berkala (KGB)')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-docs-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari data KGB..." />
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--begin::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_datatable_example_1">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">Tanggal TMT</th>
                                <th class="min-w-125px">Golongan</th>
                                <th class="min-w-100px">Masa Kerja (Tahun)</th>
                                <th class="min-w-100px">Masa Kerja (Bulan)</th>
                                <th class="min-w-100px">SK Nomor</th>
                                <th class="min-w-100px">SK Tanggal</th>
                                <th class="text-end min-w-70px">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($dataKgb as $item)
                            <tr>
                                <td>
                                    {{ $item->tmt_kgb ? date('d/m/Y', strtotime($item->tmt_kgb)) : '-' }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold">{{ $item->nm_golongan ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>{{ $item->masa_kerja_tahun ?? '-' }}</td>
                                <td>{{ $item->masa_kerja_bulan ?? '-' }}</td>
                                <td>{{ $item->nomor_sk ?? '-' }}</td>
                                <td>{{ $item->tanggal_sk ? date('d/m/Y', strtotime($item->tanggal_sk)) : '-' }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                    </a>
                                    <!--begin::Menu-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3">View Details</a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu-->
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="d-flex flex-column flex-center">
                                        <img src="{{ asset('assets/media/illustrations/sketchy-1/5.png') }}" alt="" class="mw-400px">
                                        <div class="fs-1 fw-bolder text-dark mb-4">No items found.</div>
                                        <div class="fs-6">Tidak ada data KGB yang ditemukan untuk anda.</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection