@extends('layouts.user', ['title' => 'Riwayat Mutasi'])

@php
    \Carbon\Carbon::setLocale('id');
@endphp

@push('styles')
<style>
    .mutasi-card {
        transition: all 0.3s;
        border: 1px solid #e1e3ea;
    }
    
    .mutasi-card:hover {
        border-color: var(--bs-user-primary);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Riwayat Mutasi
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.kepegawaian.index') }}" class="text-muted text-hover-primary">Data Kepegawaian</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Riwayat Mutasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        {{-- Summary --}}
        <div class="row g-6 g-xl-9 mb-8">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-arrow-right-left fs-3x text-primary mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $totalMutasi }}</h3>
                        <div class="text-muted">Total Mutasi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-office-bag fs-3x text-success mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $ruanganSekarang }}</h3>
                        <div class="text-muted">Ruangan Saat Ini</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-calendar fs-3x text-warning mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $mutasiTerakhir ? \Carbon\Carbon::parse($mutasiTerakhir->tmt_jabatan)->format('m/Y') : '-' }}</h3>
                        <div class="text-muted">Mutasi Terakhir</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Timeline Mutasi --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timeline Riwayat Mutasi</h3>
            </div>
            <div class="card-body p-9">
                @if(count($riwayatMutasi) > 0)
                    <!--begin::Timeline-->
                    <div class="timeline timeline-border-dashed">
                        @foreach($riwayatMutasi as $index => $mutasi)
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon">
                                    <i class="ki-outline ki-arrow-right-left fs-2 text-gray-500"></i>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n1 ps-3">
                                    <!--begin::Timeline heading-->
                                    <div class="pe-3 mb-5">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">
                                            Mutasi ke {{ $mutasi->nama_ruangan ?? 'Ruangan Tidak Diketahui' }}
                                        </div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">TMT Jabatan: {{ \Carbon\Carbon::parse($mutasi->tmt_jabatan)->translatedFormat('d F Y') }}</div>
                                            <!--end::Info-->
                                            
                                            @if($mutasi->jenis_mutasi)
                                                <!--begin::Badge-->
                                                <span class="badge badge-light-primary ms-2">{{ $mutasi->jenis_mutasi }}</span>
                                                <!--end::Badge-->
                                            @endif
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-750px px-8 py-6">
                                            <!--begin::Details-->
                                            <div class="flex-grow-1">
                                                <div class="row">
                                                    @if($mutasi->no_nota)
                                                        <div class="col-md-6 mb-3">
                                                            <strong class="text-muted fs-7">No. Nota:</strong>
                                                            <div class="text-gray-800 fw-semibold">{{ $mutasi->no_nota }}</div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($mutasi->no_sk)
                                                        <div class="col-md-6 mb-3">
                                                            <strong class="text-muted fs-7">No. SK:</strong>
                                                            <div class="text-gray-800 fw-semibold">{{ $mutasi->no_sk }}</div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($mutasi->tgl_sk)
                                                        <div class="col-md-6 mb-3">
                                                            <strong class="text-muted fs-7">Tanggal SK:</strong>
                                                            <div class="text-gray-800 fw-semibold">{{ \Carbon\Carbon::parse($mutasi->tgl_sk)->translatedFormat('d/m/Y') }}</div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($mutasi->tgl_ttd)
                                                        <div class="col-md-6 mb-3">
                                                            <strong class="text-muted fs-7">Tanggal TTD:</strong>
                                                            <div class="text-gray-800 fw-semibold">{{ \Carbon\Carbon::parse($mutasi->tgl_ttd)->translatedFormat('d/m/Y') }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @if($mutasi->isi_nota)
                                                    <div class="mt-3">
                                                        <strong class="text-muted fs-7">Isi Nota:</strong>
                                                        <div class="text-gray-800 mt-1">{{ Str::limit($mutasi->isi_nota, 200) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                            <!--end::Details-->

                                            <!--begin::Status & Action-->
                                            <div class="min-w-125px text-end">
                                                <div class="mb-3">
                                                    <span class="badge badge-light-success">{{ \Carbon\Carbon::parse($mutasi->tmt_jabatan)->format('Y') }}</span>
                                                </div>
                                                
                                                @if($mutasi->path_dokumen)
                                                    <button type="button" class="btn btn-sm btn-light-primary" 
                                                            onclick="downloadMutasi('{{ $mutasi->kd_mutasi }}')">
                                                        <i class="ki-outline ki-down fs-5"></i>
                                                        Download
                                                    </button>
                                                @endif
                                            </div>
                                            <!--end::Status & Action-->
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endforeach
                    </div>
                    <!--end::Timeline-->
                @else
                    <div class="text-center py-10">
                        <i class="ki-outline ki-arrow-right-left fs-3x text-muted mb-5"></i>
                        <h5 class="text-muted mb-3">Belum Ada Riwayat Mutasi</h5>
                        <p class="text-muted">Riwayat perpindahan ruangan akan muncul di sini.</p>
                    </div>
                @endif
            </div>
        </div>    </div>
</div>
@endsection

@push('scripts')
<script>
function downloadMutasi(mutasiId) {
    const downloadUrl = `{{ route('user.kepegawaian.download-mutasi', ':id') }}`.replace(':id', mutasiId);
    window.open(downloadUrl, '_blank');
}
</script>
@endpush