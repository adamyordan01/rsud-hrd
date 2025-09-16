@extends('layouts.backend', ['title' => $titleBreadcrumb])

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    {{ $titleBreadcrumb }}
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
                        Pegawai
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        {{ $titleBreadcrumb }}
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
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-karyawan-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Pegawai Belum Lengkap" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-karyawan-table-toolbar="base">
                        <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" title="Refresh Data" onclick="refreshTable()">
                            <i class="ki-duotone ki-arrows-circle fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_karyawan_belum_lengkap_table">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-80px">ID Peg.</th>
                                <th class="min-w-200px">Nama<br>Tempat & Tanggal Lahir<br>NIP / No. KARPEG</th>
                                <th class="min-w-50px">L/P</th>
                                <th class="min-w-150px">Alamat</th>
                                <th class="min-w-120px">Jurusan</th>
                                <th class="min-w-100px">Tgl. Aktif Bekerja</th>
                                <th class="min-w-120px">Departemen</th>
                                <th class="min-w-120px">Sub Jenis Tenaga</th>
                                <th class="min-w-120px">Email</th>
                                <th class="min-w-150px">Rekening Bank</th>
                                <th class="min-w-100px">Status Rumah</th>
                                <th class="min-w-120px">Kelengkapan</th>
                                <th class="text-end min-w-100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var dtTable;
    
    $(document).ready(function() {
        initDataTable();
    });

    function initDataTable() {
        dtTable = $('#kt_karyawan_belum_lengkap_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.karyawan-belum-lengkap.index') }}",
                type: "GET"
            },
            columns: [
                { data: 'id_pegawai', name: 'kd_karyawan', orderable: true },
                { data: 'nama_lengkap', name: 'nama', orderable: true },
                { data: 'jenis_kelamin', name: 'kd_jenis_kelamin', orderable: true },
                { data: 'alamat_full', name: 'alamat', orderable: false },
                { data: 'jurusan_field', name: 'jurusan', orderable: true },
                { data: 'tgl_aktif', name: 'tmt_kerja', orderable: true },
                { data: 'departemen_field', name: 'departemen', orderable: true },
                { data: 'sub_jenis_tenaga_field', name: 'sub_jenis_tenaga', orderable: true },
                { data: 'email_field', name: 'email', orderable: true },
                { data: 'rekening_bank', name: 'rek_bni_syariah', orderable: false },
                { data: 'status_rumah_field', name: 'status_rmh', orderable: true },
                { data: 'kelengkapan', name: 'persentase_kelengkapan', orderable: true },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: -1, className: 'text-end' },
                { targets: [2], className: 'text-center' }
            ],
            order: [[11, 'asc']], // Urutkan berdasarkan kelengkapan terendah (index berkurang 1)
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                processing: "Memuat data...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data pegawai belum lengkap ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_ (_TOTAL_ total data)",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                search: "Cari:",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            responsive: true,
            dom: 'rt<"row"<"col-sm-12 col-md-5"l><"col-sm-12 col-md-7"p>>',
        });

        // Search functionality
        $('[data-kt-karyawan-table-filter="search"]').on('keyup', function() {
            dtTable.search(this.value).draw();
        });
    }

    function refreshTable() {
        if (dtTable) {
            dtTable.ajax.reload(null, false);
            toastr.success('Data berhasil direfresh!');
        }
    }
</script>
@endpush
