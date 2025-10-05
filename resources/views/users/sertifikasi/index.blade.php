@extends('layouts.user', ['title' => 'Sertifikasi & Pelatihan'])

@push('styles')
<style>
    .cert-card {
        transition: all 0.3s;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .cert-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    .cert-header {
        background: linear-gradient(135deg, var(--bs-user-primary) 0%, #0066cc 100%);
        color: white;
        padding: 20px;
        position: relative;
    }
    
    .cert-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.8) 50%, rgba(255,255,255,0.3) 100%);
    }
    
    .status-active { color: #50cd89; }
    .status-expired { color: #f1416c; }
    .status-soon { color: #ffc700; }
    
    .feature-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    .training-item {
        border-left: 4px solid #009ef7;
        padding-left: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    
    .training-item:hover {
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 10px 15px;
        margin-left: -10px;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Sertifikasi & Pelatihan
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Sertifikasi & Pelatihan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        {{-- Quick Stats --}}
        <div class="row g-6 g-xl-9 mb-8">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="feature-icon bg-light-primary text-primary mx-auto">
                            <i class="ki-duotone ki-medal-star">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h3 class="text-gray-900 fw-bold">{{ $stats['str_aktif'] }}</h3>
                        <div class="text-muted">STR Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="feature-icon bg-light-success text-success mx-auto">
                            <i class="ki-duotone ki-award">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                        <h3 class="text-gray-900 fw-bold">{{ $stats['sip_aktif'] }}</h3>
                        <div class="text-muted">SIP Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="feature-icon bg-light-warning text-warning mx-auto">
                            <i class="ki-duotone ki-book">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </div>
                        <h3 class="text-gray-900 fw-bold">{{ $stats['seminar_tahun_ini'] }}</h3>
                        <div class="text-muted">Seminar {{ date('Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="feature-icon bg-light-info text-info mx-auto">
                            <i class="ki-duotone ki-chart-line-up">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                        <h3 class="text-gray-900 fw-bold">{{ $stats['total_sertifikat'] }}</h3>
                        <div class="text-muted">Total Sertifikat</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Menu Cards --}}
        <div class="row g-6 g-xl-9 mb-8">
            {{-- STR --}}
            <div class="col-md-6 col-xl-4">
                <div class="card cert-card h-100">
                    <div class="cert-header text-center">
                        <i class="ki-duotone ki-medal-star fs-3x mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <h4 class="fw-bold mb-2">Surat Tanda Registrasi (STR)</h4>
                        <p class="mb-0 opacity-75">Lihat status dan riwayat STR Anda</p>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <span class="badge badge-light-{{ $stats['str_aktif'] > 0 ? 'success' : 'danger' }} fs-6 px-3 py-2">
                                {{ $stats['str_aktif'] > 0 ? 'Ada STR Aktif' : 'Tidak Ada STR Aktif' }}
                            </span>
                        </div>
                        <a href="{{ route('user.sertifikasi.str') }}" class="btn btn-user-primary">
                            <i class="ki-duotone ki-eye fs-3 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Lihat STR
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- SIP --}}
            <div class="col-md-6 col-xl-4">
                <div class="card cert-card h-100">
                    <div class="cert-header text-center">
                        <i class="ki-duotone ki-award fs-3x mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <h4 class="fw-bold mb-2">Surat Izin Praktik (SIP)</h4>
                        <p class="mb-0 opacity-75">Lihat status dan riwayat SIP Anda</p>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <span class="badge badge-light-{{ $stats['sip_aktif'] > 0 ? 'success' : 'danger' }} fs-6 px-3 py-2">
                                {{ $stats['sip_aktif'] > 0 ? 'Ada SIP Aktif' : 'Tidak Ada SIP Aktif' }}
                            </span>
                        </div>
                        <a href="{{ route('user.sertifikasi.sip') }}" class="btn btn-user-primary">
                            <i class="ki-duotone ki-eye fs-3 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Lihat SIP
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Seminar & Pelatihan --}}
            <div class="col-md-6 col-xl-4">
                <div class="card cert-card h-100">
                    <div class="cert-header text-center">
                        <i class="ki-duotone ki-book fs-3x mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        <h4 class="fw-bold mb-2">Seminar & Pelatihan</h4>
                        <p class="mb-0 opacity-75">Riwayat kegiatan pengembangan diri</p>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <span class="badge badge-light-primary fs-6 px-3 py-2">
                                {{ $stats['seminar_tahun_ini'] }} Kegiatan {{ date('Y') }}
                            </span>
                        </div>
                        <a href="{{ route('user.sertifikasi.seminar') }}" class="btn btn-user-primary">
                            <i class="ki-duotone ki-eye fs-3 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Recent Activities --}}
        <div class="row g-6 g-xl-9">
            {{-- Sertifikat Akan Berakhir --}}
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-duotone ki-warning-2 text-warning me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Sertifikat Akan Berakhir
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(count($sertifikatExpiringSoon) > 0)
                            @foreach($sertifikatExpiringSoon as $cert)
                                <div class="d-flex align-items-center mb-4">
                                    <div class="symbol symbol-40px me-4">
                                        <div class="symbol-label bg-light-warning text-warning">
                                            <i class="ki-duotone ki-time fs-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="text-gray-900 fw-bold mb-1">{{ $cert['type'] }}</h6>
                                        <p class="text-muted fs-7 mb-0">
                                            Berakhir: {{ $cert['expired_date'] }} ({{ $cert['days_left'] }} hari lagi)
                                        </p>
                                    </div>
                                    <div>
                                        <span class="badge badge-light-warning">{{ $cert['days_left'] }} hari</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="ki-duotone ki-check-circle fs-3x text-success mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h6 class="text-muted">Semua sertifikat masih berlaku</h6>
                                <p class="text-muted fs-7 mb-0">Tidak ada yang akan berakhir dalam 30 hari ke depan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Kegiatan Terbaru --}}
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kegiatan Pelatihan Terbaru</h3>
                        <div class="card-toolbar">
                            <a href="{{ route('user.sertifikasi.seminar') }}" class="btn btn-sm btn-user-primary">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($recentTrainings) > 0)
                            @foreach($recentTrainings as $training)
                                <div class="training-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="text-gray-900 fw-bold mb-1">{{ $training['title'] }}</h6>
                                            <p class="text-muted fs-7 mb-1">{{ $training['organizer'] }}</p>
                                            <p class="text-muted fs-8 mb-0">
                                                <i class="ki-duotone ki-calendar fs-8 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                {{ $training['date'] }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="badge badge-light-primary fs-8">{{ $training['type'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="ki-duotone ki-book fs-3x text-muted mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <h6 class="text-muted">Belum ada kegiatan</h6>
                                <p class="text-muted fs-7 mb-0">Riwayat pelatihan akan muncul di sini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Animasi untuk cards
    $('.cert-card').each(function(index) {
        $(this).css('opacity', '0').delay(index * 150).animate({
            opacity: 1
        }, 600);
    });
    
    // Tooltip untuk badge status
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush