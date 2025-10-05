@extends('layouts.backend', ['title' => 'Daftar Mutasi (Proses)'])

@inject('DB', 'Illuminate\Support\Facades\DB')

@php
    $jabatan = Auth::user()->kd_jabatan_struktural;
    $ruangan = Auth::user()->kd_ruangan;
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
                            Mutasi (Verifikasi)
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

            <div class="row g-4 g-xl-4 mb-8">
                {{-- make filter tanggal awal sampai dengan tanggal akhir with input date --}}
                <div class="col-xl-6">
                    <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">
                    <label class="form-label fw-bold mb-0">Filter Tanggal</label>
                    {{-- <input class="form-control form-control-solid" name="date" id="date" type="text" placeholder="Pilih tanggal" /> --}}
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

            <div class="card mb-5">
                <div class="card-header align-items-center py-5 px-lg-12 gap-2 gap-md-5 border-0">
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
                <div class="card-body p-lg-12 pt-lg-0">
                    <div class="row g-5 mb-5" id="list-mutasi-pending">
                        <table class="table table-bordered table-stripped align-middle" id="mutasi-table">
                            <thead>
                                <tr>
                                    <th
                                        class="text-center min-w50px"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="text-center min-w150px"
                                    >
                                        Kode Mutasi
                                    </th>
                                    <th
                                        class="text-center min-w150px"
                                    >
                                        Identitas Pegawai
                                    </th>
                                    <th
                                        class="text-center min-w150px"
                                    >
                                        Sub Jenis Tenaga <br>
                                        Jabatan Fungsional
                                    </th>
                                    <th
                                        class="text-center min-w150px"
                                    >
                                        Jabatan <br>
                                        Ruangan
                                    </th>
                                    <th
                                        class="text-center min-w150px"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($getMutasiVerifikasi as $item)
                                @php
                                    $gelar_depan = $item->gelar_depan ? $item->gelar_depan . ' ' : '';
                                    $gelar_belakang = $item->gelar_belakang ? ' ' . $item->gelar_belakang : '';
                                    $nama = $gelar_depan . $item->nama . $gelar_belakang;

                                    $tanggal_lahir = date('d-m-Y', strtotime($item->tgl_lahir));
                                    $tempat_tanggal_lahir = $item->tempat_lahir . ', ' . $tanggal_lahir;

                                    $filePathTte = $item->path_dokumen ?? '';
                                    
                                    // Gunakan route download yang aman untuk hrd_files disk
                                    if (!empty($item->path_dokumen)) {
                                        // Gunakan method downloadMutasiDocumentByPath yang sudah ada di MutasiOnProcessController
                                        $urlFilePathtte = route('admin.mutasi-on-process.download-document-by-path', [
                                            'kd_mutasi' => $item->kd_mutasi, 
                                            'kd_karyawan' => $item->kd_karyawan
                                        ]);
                                    } else {
                                        // Fallback untuk file lama yang belum ada path_dokumen
                                        $urlFilePathtte = '';
                                    }
                                    
                                @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $item->kd_mutasi }}</td>
                                        <td>
                                            <b>{{ $nama }}</b> <br>
                                            {{ $tempat_tanggal_lahir }}
                                        </td>
                                        <td>
                                            {{ $item->sub_detail }} <br>
                                            {{ $item->jab_fung }}
                                        </td>
                                        <td>
                                            {{ $item->jab_struk }} Pada <br>
                                            {{ $item->ruangan }}
                                        </td>
                                        <td class="text-center">
                                            <!-- jika ada path dokumen maka tampilkan button cetak nota, jika tidak maka tampilkan tulisan cetak nota pada aplikasi HRD yang lama -->
                                            @if ($item->path_dokumen)
                                                <a
                                                    href="{{ $urlFilePathtte }}"
                                                    target="_blank"
                                                    class="btn btn-primary btn-sm"
                                                >
                                                    <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                    Cetak Nota
                                                </a>
                                            @else
                                                <div class="text-center"
                                                    style="word-wrap: break-word; width: 150px; margin: 0 auto;"
                                                >
                                                    Harap Cetak Nota pada Aplikasi HRD yang lama, Karena Nota ini belum terdapat Tanda Tangan Elektronik (TTE)
                                                </div>
                                            @endif
                                            {{-- <a
                                                href="{{ $urlFilePathtte }}"
                                                class="btn btn-primary btn-sm"
                                            >
                                                <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                Cetak Nota
                                            </a> --}}
                                        </td>
                                    </tr>
                                @endforeach
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
        "use strict";

        var dateStart = moment().format('YYYY-MM-DD');
        var dateEnd = moment().format('YYYY-MM-DD');

        var flatPickrStartDate = $('#start_date').val();
        var flatPickrEndDate = $('#end_date').val();

        var KTSKList = (function () {
            var table,
            $table = $('#mutasi-table');

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
                                { orderable: false, targets: [0, 5] }
                            ],
                        });

                        $('[data-kt-sk-table-filter="search"]').on("keyup", function () {
                            // table.search($(this).val()).draw();
                            table.column(2).search($(this).val()).draw();
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
                    }
                },
            };
        })();
        
        $(document).ready(function() {
            KTSKList.init();

            // aktifkan 

            // ketika btn-filter di klik maka kirim request ke server dan render data ke table
            $('#btn-filter').on('click', function() {
                var date = $('#date').val();
                var dateSplit = date.split(' to ');
                var startDate = dateSplit[0];
                var endDate = dateSplit[1];

                var url = "{{ route('admin.mutasi-verifikasi.index') }}";
                var urlWithParams = url + '?start_date=' + startDate + '&end_date=' + endDate;
                window.location.href = urlWithParams;
            });

            // ketika btn-reset di klik maka reset filter
            $('#btn-reset').on('click', function() {
                var url = "{{ route('admin.mutasi-verifikasi.index') }}";
                window.location.href = url;
            });

            var weekend = [0, 6];
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
        });
    </script>
@endpush
