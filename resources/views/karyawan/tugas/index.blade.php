@extends('layouts.backend', ['title' => 'Tugas'])

@inject('carbon', 'Carbon\Carbon')

@push('styles')
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Riwayat Tugas
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
                            <a href="{{ route('admin.karyawan.show', $karyawan->kd_karyawan) }}" class="text-muted text-hover-primary">
                                Karyawan
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Riwayat Tugas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <x-employee-header :karyawan="$karyawan" :missing-fields="$missing_fields" :persentase-kelengkapan="$persentase_kelengkapan" />

        <div class="row g-10 g-xl-10">
            <div class="col-md-12">
                <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
                    <div class="card-header cursor-pointer">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Riwayat Penempatan & Tugas Kerja</h3>
                        </div>
                        {{-- <div class="card-toolbar">
                            <div class="text-muted fs-7">
                                <i class="ki-outline ki-information-2 fs-6 text-primary me-1"></i>
                                Riwayat penempatan kerja dari tabel view_tempat_kerja
                            </div>
                        </div> --}}
                    </div>
        
                    <div class="card-body p-9">
                        <div class="table-responsive">
                            <table class="table table-striped table-row-bordered gy-5 gs-7" id="tugas-table">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>No</th>
                                        <th>Periode</th>
                                        <th>Divisi & Unit Kerja</th>
                                        <th>Sub Unit & Ruangan</th>
                                        <th>Jabatan Struktural</th>
                                        <th>Jenis & Detail Tenaga</th>
                                        <th>No. Nota</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        // Initialize DataTable
        table = $('#tugas-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.karyawan.tugas.data', $karyawan->kd_karyawan) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'periode', name: 'periode', orderable: false },
                { data: 'unit_kerja_info', name: 'unit_kerja_info', orderable: false },
                { data: 'sub_unit_ruangan', name: 'sub_unit_ruangan', orderable: false },
                { data: 'jabatan_info', name: 'jabatan_info', orderable: false },
                { data: 'detail_tenaga', name: 'detail_tenaga', orderable: false },
                { data: 'no_nota', name: 'no_nota', orderable: false },
                { data: 'status', name: 'status', orderable: false },
            ],
            order: [[0, 'asc']], // Order by DT_RowIndex ascending
            language: {
                emptyTable: "Belum ada data riwayat tugas",
                zeroRecords: "Data tidak ditemukan",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                search: "Cari:",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        table.on('draw', function() {
            KTMenu.createInstances();
        });
    });
</script>
@endpush
