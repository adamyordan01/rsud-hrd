@extends('layouts.backend', ['title' => 'Detail Pegawai Luar'])

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Detail Pegawai Luar - {{ $karyawanLuar->nama }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.karyawan-luar.index') }}" class="text-muted text-hover-primary">
                                Data Pegawai Luar
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <span class="text-muted text-hover-primary">Detail Pegawai Luar</span>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.karyawan-luar.edit', $karyawanLuar->kd_peg_luar) }}" 
                        class="btn btn-sm fw-bold btn-primary">
                        Edit Data
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row g-5 g-xl-10">
                <!-- Profile Card -->
                <div class="col-xl-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-body pt-15">
                            <!-- Avatar -->
                            <div class="d-flex flex-center flex-column mb-5">
                                <div class="symbol symbol-100px symbol-circle mb-7">
                                    @if(isset($karyawanLuar->foto) && $karyawanLuar->foto && $karyawanLuar->foto !== 'user.png')
                                        <img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/{{ $karyawanLuar->foto }}" alt="Avatar" />
                                    @else
                                        <div class="symbol-label fs-2 fw-semibold text-primary bg-light-primary">
                                            {{ substr($karyawanLuar->nama, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <h1 class="fw-bold mb-3 text-center">
                                    @php
                                        $namaLengkap = '';
                                        if (!empty($karyawanLuar->gelar_depan)) {
                                            $namaLengkap .= $karyawanLuar->gelar_depan . ' ';
                                        }
                                        $namaLengkap .= $karyawanLuar->nama;
                                        if (!empty($karyawanLuar->gelar_belakang)) {
                                            $namaLengkap .= $karyawanLuar->gelar_belakang;
                                        }
                                    @endphp
                                    {{ $namaLengkap }}
                                </h1>
                                <span class="badge badge-lg badge-light-primary d-inline">{{ $karyawanLuar->kd_peg_luar }}</span>
                            </div>

                            <!-- Basic Info -->
                            <div class="separator separator-dashed my-5"></div>
                            <div class="pb-5 fs-6">
                                <div class="fw-bold mt-5">Jenis Kelamin</div>
                                <div class="text-gray-600">{{ $karyawanLuar->sex == '1' ? 'Laki-laki' : 'Perempuan' }}</div>
                                
                                <div class="fw-bold mt-5">Tempat & Tanggal Lahir</div>
                                <div class="text-gray-600">
                                    {{ $karyawanLuar->tempat_lahir }}, 
                                    {{ \Carbon\Carbon::parse($karyawanLuar->tgl_lahir)->format('d F Y') }}
                                </div>
                                
                                <div class="fw-bold mt-5">No. HP</div>
                                <div class="text-gray-600">{{ $karyawanLuar->no_hp ?? '-' }}</div>
                                
                                <div class="fw-bold mt-5">Email</div>
                                <div class="text-gray-600">{{ $karyawanLuar->email ?? '-' }}</div>
                                
                                <div class="fw-bold mt-5">Alamat</div>
                                <div class="text-gray-600">{{ $karyawanLuar->alamat ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Information -->
                <div class="col-xl-8">
                    <!-- Informasi Kepegawaian -->
                    <div class="card mb-5">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title fw-bold fs-3">Informasi Kepegawaian</h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Kode Pegawai Luar</div>
                                        <div class="text-gray-600">{{ $karyawanLuar->kd_peg_luar }}</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Status Kerja</div>
                                        <div class="text-gray-600">Pegawai Luar</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Jenis Pegawai</div>
                                        <div class="text-gray-600">Pegawai Luar RS</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Status Pegawai</div>
                                        <div class="text-gray-600">
                                            {{ $karyawanLuar->status_peg == '1' ? 'Aktif' : 'Tidak Aktif' }}
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">TMT</div>
                                        <div class="text-gray-600">
                                            {{ $karyawanLuar->tmt ? \Carbon\Carbon::parse($karyawanLuar->tmt)->format('d F Y') : '-' }}
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">TMT Akhir</div>
                                        <div class="text-gray-600">
                                            {{ $karyawanLuar->tmt_akhir ? \Carbon\Carbon::parse($karyawanLuar->tmt_akhir)->format('d F Y') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Identitas Pribadi -->
                    <div class="card mb-5">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title fw-bold fs-3">Identitas Pribadi</h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Nama Lengkap</div>
                                        <div class="text-gray-600">
                                            @php
                                                $namaLengkap = '';
                                                if (!empty($karyawanLuar->gelar_depan)) {
                                                    $namaLengkap .= $karyawanLuar->gelar_depan . ' ';
                                                }
                                                $namaLengkap .= $karyawanLuar->nama;
                                                if (!empty($karyawanLuar->gelar_belakang)) {
                                                    $namaLengkap .= $karyawanLuar->gelar_belakang;
                                                }
                                            @endphp
                                            {{ $namaLengkap }}
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">No. KTP</div>
                                        <div class="text-gray-600">{{ $karyawanLuar->ktp ?? '-' }}</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">No. Kartu Keluarga</div>
                                        <div class="text-gray-600">{{ $karyawanLuar->no_kk ?? '-' }}</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Nama Ibu Kandung</div>
                                        <div class="text-gray-600">{{ $karyawanLuar->nama_ibu ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">NPWP</div>
                                        <div class="text-gray-600">{{ $karyawanLuar->npwp ?? '-' }}</div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Tinggi / Berat Badan</div>
                                        <div class="text-gray-600">
                                            {{ $karyawanLuar->tinggi_badan ? $karyawanLuar->tinggi_badan . ' cm' : '-' }} / 
                                            {{ $karyawanLuar->berat_badan ? $karyawanLuar->berat_badan . ' kg' : '-' }}
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">No. HP Alternatif</div>
                                        <div class="text-gray-600">{{ $karyawanLuar->hp_alternatif ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rekening Bank -->
                    <div class="card">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title fw-bold fs-3">Rekening Bank</h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Rekening BSI</div>
                                        <div class="text-gray-600">{{ $karyawanLuar->bsi ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div class="fw-bold text-gray-800 fs-6">Rekening BPD Aceh</div>
                                        <div class="text-gray-600">{{ $karyawanLuar->bpd_aceh ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
