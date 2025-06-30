@extends('layouts.backend', ['title' => 'Daftar Tugas Tambahan (Proses)'])

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
                        Daftar Tugas Tambahan
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
                            Tugas Tambahan (Pending)
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
            <x-navigation-menu :totalTugasTambahanOnProcess="$totalTugasTambahanOnProcess" :totalTugasTambahanPending="$totalTugasTambahanPending" />

            <div class="card mb-5">
                <div class="card-body p-lg-12">
                    <div class="row g-5 mb-5 table-responsive" id="list-tugas-tambahan-pending">
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
                                @forelse ($getTugasTambahan as $item)
                                    <tr style="background: #1b84ff;">
                                        <td style="vertical-align: middle" colspan="7">
                                            <b class="text-white">
                                                Kode Mutasi - {{ $item->kd_tugas_tambahan }}
                                            </b>
                                        </td>
                                    </tr>

                                    @php
                                        $query = $DB::table('hrd_r_tugas_tambahan')
                                            ->join('view_tampil_karyawan', 'hrd_r_tugas_tambahan.kd_karyawan', '=', 'view_tampil_karyawan.kd_karyawan')
                                            ->where('kd_tugas_tambahan', $item->kd_tugas_tambahan)
                                            ->where('kd_tahap_tugas_tambahan', 0)
                                            ->where('kd_jenis_tugas_tambahan', 1)
                                            ->get();
                                    @endphp

                                    @foreach ($query as $data)
                                        @php
                                            $gelar_depan = $data->gelar_depan ? $data->gelar_depan . ' ' : '';
                                            $gelar_belakang = $data->gelar_belakang ? '' . $data->gelar_belakang : '';
                                            $nama = $gelar_depan . $data->nama . $gelar_belakang;
                                        @endphp
                                        <tr>
                                            <td>{{ $data->kd_tugas_tambahan }}</td>
                                            <td>{{ $data->kd_karyawan }}</td>
                                            <td>{{ $nama }}</td>
                                            <td>{{ $data->jab_struk }}</td>
                                            <td>{{ $data->ruangan }}</td>
                                            <td>{{ $data->sub_detail }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('admin.tugas-tambahan.edit-tugas-tambahan-on-pending', $data->kd_tugas_tambahan) }}"
                                                    class="btn btn-light btn-sm btn-active-light-primary me-2 mb-2"
                                                >
                                                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i>
                                                    Lanjutkan Mutasi Tugas Tambahan
                                                </a>
                                                <button
                                                    type="button"
                                                    class="d-block btn btn-light btn-sm btn-active-light-danger me-2"
                                                    data-id="{{ $data->kd_tugas_tambahan }}"
                                                    id="btn-delete-tugas-tambahan"
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

        $(document).on('click', '#btn-delete-tugas-tambahan', function () {
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
                        url: "{{ route('admin.tugas-tambahan.delete-tugas-tambahan', '') }}/" + id,
                        type: "DELETE",
                        data: {
                            id: id
                        },
                        cache: false,
                        success: function(response) {
                            if (response.code == 2) {
                                // toast success
                                toastr.success(response.message, 'Success');

                                // reset form #add-tugas-tambahan
                                $('#add-tugas-tambahan').trigger('reset');

                                // reload halaman setelah 1 detik
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }

                        }
                    });
                }
            });
        });
    </script>
@endpush