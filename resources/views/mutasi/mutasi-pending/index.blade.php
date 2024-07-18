@extends('layouts.backend', ['title' => 'Daftar Mutasi (Proses)'])

@inject('DB', 'Illuminate\Support\Facades\DB')

@php
    
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
                            Mutasi (Pending)
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
            <div class="card-rounded bg-light d-flex flex-stack flex-wrap p-5 mb-8">
                <div class="hover-scroll-x">
                    <ul class="nav flex-nowrap border-transparent fw-bold">
                        <li class="nav-item my-1">
                            <a
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi.*') ? 'active' : '' }}"
                                href="{{ route('admin.mutasi.index') }}"
                            >
                                <i class="ki-duotone ki-arrows-loop fs-1"><span class="path1"></span><span class="path2"></span></i>
                                Mutasi (Nota)
                            </a>
                        </li>
                        <li class="nav-item my-1">
                            <a 
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase"
                                href=""
                            >
                            <i class="ki-duotone ki-arrows-loop fs-1"><span class="path1"></span><span class="path2"></span></i>
                            Mutasi (SK)
                            </a>
                        </li>
                        <li class="nav-item my-1">
                            <a
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-pending.*') ? 'active' : '' }}"
                                href="{{ route('admin.mutasi-pending.index') }}"
                            >
                            <i class="ki-duotone ki-abstract-5 fs-1"><span class="path1"></span><span class="path2"></span></i>
                                <span class="menu-title me-2">
                                    Daftar Mutasi (Tetunda)
                                </span>
                                <span class="menu-badge">
                                    <span class="badge badge-sm badge-circle badge-danger count-status-proses">
                                        {{ $totalMutasiPending }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item my-1">
                            <a
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-on-process.*') ? 'active' : '' }}"
                                href="{{ route('admin.mutasi-on-process.index') }}"
                            >
                                <i class="ki-duotone ki-loading fs-1"><span class="path1"></span><span class="path2"></span></i>
                                <span class="menu-title me-2">
                                    Daftar Mutasi (Proses)
                                </span>
                                <span class="menu-badge">
                                    <span class="badge badge-sm badge-circle badge-danger count-status-proses">
                                        {{ $totalMutasiOnProcess }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item my-1">
                            <a
                                class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-6 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase {{ request()->routeIs('admin.mutasi-verifikasi.*') ? 'active' : '' }}"
                                href="{{ route('admin.mutasi-verifikasi.index') }}"
                            >
                                <i class="ki-duotone ki-double-check fs-1"><span class="path1"></span><span class="path2"></span></i>
                                Daftar Mutasi (Verifikasi)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mb-5">
                <div class="card-body p-lg-12">
                    <div class="row g-5 mb-5" id="list-mutasi-pending">
                        <table class="table table-bordered table-stripped align-middle">
                            <thead>
                                <tr>
                                    <th>Kode Mutasi</th>
                                    <th>ID Peg.</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Ruangan</th>
                                    <th>Sub Jenis Tenaga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($getMutasi as $item)
                                    <tr style="background: #1b84ff;">
                                        <td style="vertical-align: middle" colspan="7">
                                            <b class="text-white">
                                                Kode Mutasi - {{ $item->kd_mutasi }}
                                            </b>
                                        </td>
                                    </tr>

                                    @php
                                        // $query = "SELECT HRD_R_MUTASI.KD_KARYAWAN, HRD_R_MUTASI.KD_MUTASI, VIEW_TAMPIL_KARYAWAN.JAB_STRUK, VIEW_TAMPIL_KARYAWAN.GELAR_DEPAN, VIEW_TAMPIL_KARYAWAN.NAMA, VIEW_TAMPIL_KARYAWAN.GELAR_BELAKANG, VIEW_TAMPIL_KARYAWAN.ruangan, VIEW_TAMPIL_KARYAWAN.SUB_DETAIL FROM HRD_R_MUTASI INNER JOIN VIEW_TAMPIL_KARYAWAN ON HRD_R_MUTASI.KD_KARYAWAN = VIEW_TAMPIL_KARYAWAN.KD_KARYAWAN where KD_MUTASI = '".$dataMutasi['KD_MUTASI']."' and KD_TAHAP_MUTASI = 0 and KD_JENIS_MUTASI = 1";
                                        $query = $DB::table('hrd_r_mutasi')
                                            ->join('view_tampil_karyawan', 'hrd_r_mutasi.kd_karyawan', '=', 'view_tampil_karyawan.kd_karyawan')
                                            ->where('kd_mutasi', $item->kd_mutasi)
                                            ->where('kd_tahap_mutasi', 0)
                                            ->where('kd_jenis_mutasi', 1)
                                            ->get();
                                    @endphp

                                    @foreach ($query as $data)
                                        @php
                                            $gelar_depan = $data->gelar_depan ? $data->gelar_depan . ' ' : '';
                                            $gelar_belakang = $data->gelar_belakang ? '' . $data->gelar_belakang : '';
                                            $nama = $gelar_depan . $data->nama . $gelar_belakang;
                                        @endphp
                                        <tr>
                                            <td>{{ $data->kd_mutasi }}</td>
                                            <td>{{ $data->kd_karyawan }}</td>
                                            <td>{{ $nama }}</td>
                                            <td>{{ $data->jab_struk }}</td>
                                            <td>{{ $data->ruangan }}</td>
                                            <td>{{ $data->sub_detail }}</td>
                                            <td>
                                                {{-- Route::prefix('admin')->name('admin.')->group(function () {
                                                    Route::name('mutasi.')->group(function () {
                                                        Route::get('/mutasi/{id}/edit', [MutasiController::class, 'edit'])->name('edit');
                                                        Route::patch('/mutasi/{id}/update', [MutasiController::class, 'update'])->name('update');
                                                    }); --}}
                                                <a
                                                    href="{{ route('admin.mutasi.edit-mutasi-nota-on-pending', $data->kd_mutasi) }}"
                                                    class="btn btn-light btn-sm btn-active-light-primary me-2 mb-2"
                                                >
                                                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                    Lanjutkan Mutasi
                                                </a>
                                                <button
                                                    type="button"
                                                    class="d-block btn btn-light btn-sm btn-active-light-danger me-2"
                                                    data-id="{{ $data->kd_mutasi }}"
                                                    id="btn-delete-mutasi-nota"
                                                >
                                                    <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                                    Hapus
                                                </button>
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
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '#btn-delete-mutasi-nota', function () {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.mutasi.delete-mutasi-nota', '') }}/" + id,
                        type: "DELETE",
                        data: {
                            id: id
                        },
                        cache: false,
                        success: function(response) {
                            if (response.code == 2) {
                                // toast success
                                toastr.success(response.message, 'Success');

                                // reset form #add-mutasi-nota
                                $('#add-mutasi-nota').trigger('reset');

                                // reload halaman setelah 1 detik
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }

                        }
                    });
                }
            });

            // $.ajax({
            //     url: "{{ route('admin.mutasi.delete-mutasi-nota', '') }}/" + id,
            //     type: "DELETE",
            //     data: {
            //         id: id
            //     },
            //     cache: false,
            //     success: function(response) {
            //         if (response.code == 2) {
            //             // toast success
            //             toastr.success(response.message, 'Success');

            //             // reset form #add-mutasi-nota
            //             $('#add-mutasi-nota').trigger('reset');
            //         }

            //         kirimIdMutasi(response.id_mutasi);
            //     }
            // });
        });
    </script>
@endpush