@extends('layouts.user', ['title' => 'Dashboard'])

@push('styles')
<style>
    .completion-progress {
        height: 8px;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .notification-item {
        border-left: 4px solid #009ef7;
        transition: all 0.2s;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .notification-warning {
        border-left-color: #ffc700;
    }
    
    .notification-danger {
        border-left-color: #f1416c;
    }
    
    .notification-info {
        border-left-color: #009ef7;
    }
    
    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Dashboard
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">Portal Pegawai</li>
                    <li class="breadcrumb-item text-muted">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        {{-- Welcome Card --}}
        <div class="card user-welcome-card mb-8">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="text-white fw-bold mb-3">
                            Selamat Datang, 
                            @if($karyawan->gelar_depan){{ $karyawan->gelar_depan }} @endif{{ $karyawan->nama }}@if($karyawan->gelar_belakang), {{ $karyawan->gelar_belakang }}@endif
                        </h1>
                        <p class="text-white-75 fs-5 mb-2">
                            <i class="ki-duotone ki-office-bag text-white-50 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ $personalData['ruangan'] }}
                        </p>
                        <p class="text-white-75 fs-6 mb-0">
                            <i class="ki-duotone ki-profile-circle text-white-50 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ $personalData['status_kerja'] }} - {{ $personalData['jenis_tenaga'] }}
                        </p>
                        <small class="text-white-50">
                            <i class="ki-duotone ki-calendar text-white-50 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Login terakhir: {{ auth()->user()->last_login_at ? date('d/m/Y H:i', strtotime(auth()->user()->last_login_at)) : 'Belum pernah login' }}
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="symbol symbol-100px symbol-circle">
                            @if(file_exists(public_path('storage/hrd_files/foto/' . $karyawan->kd_karyawan . '.jpg')))
                                <img src="{{ route('photo.show', ['type' => 'foto', 'id' => $karyawan->kd_karyawan, 'filename' => $karyawan->kd_karyawan . '.jpg']) }}" alt="Foto Profil" />
                            @else
                                <div class="symbol-label bg-light-primary text-primary fw-bold fs-1">
                                    {{ substr($karyawan->nama, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Quick Stats --}}
        <div class="row g-6 g-xl-9 mb-8">
            {{-- Kelengkapan Profil --}}
            <div class="col-md-6 col-xl-3">
                <div class="card user-stats-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-5">
                            <div class="stats-icon bg-light-primary text-primary me-3">
                                <i class="ki-duotone ki-profile-circle">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-bold fs-6">Kelengkapan Profil</span>
                                <div class="completion-progress bg-light mt-2">
                                    <div class="bg-primary" style="width: {{ $personalData['completion_percentage'] }}%; height: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <span class="text-gray-900 fw-bold fs-2x">{{ $personalData['completion_percentage'] }}%</span>
                            <div class="text-muted fs-7">{{ $personalData['completed_fields'] }}/{{ $personalData['total_fields'] }} field lengkap</div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Status Kepegawaian --}}
            <div class="col-md-6 col-xl-3">
                <div class="card user-stats-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-5">
                            <div class="stats-icon bg-light-success text-success me-3">
                                <i class="ki-duotone ki-document">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div>
                                <span class="text-gray-800 fw-bold fs-6">Total SK</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <span class="text-gray-900 fw-bold fs-2x">{{ $kepegawaianData['total_sk'] }}</span>
                            <div class="text-muted fs-7">Dokumen SK</div>
                            @if($kepegawaianData['sk_terbaru'])
                                <div class="text-primary fs-8 mt-1">
                                    Terbaru: {{ date('d/m/Y', strtotime($kepegawaianData['sk_terbaru']->tgl_sk)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Sertifikasi --}}
            <div class="col-md-6 col-xl-3">
                <div class="card user-stats-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-5">
                            <div class="stats-icon bg-light-warning text-warning me-3">
                                <i class="ki-duotone ki-medal-star">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div>
                                <span class="text-gray-800 fw-bold fs-6">Sertifikat Aktif</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="d-flex justify-content-around">
                                <div>
                                    <span class="text-gray-900 fw-bold fs-1x">{{ $sertifikasiData['str_aktif'] }}</span>
                                    <div class="text-muted fs-8">STR</div>
                                </div>
                                <div>
                                    <span class="text-gray-900 fw-bold fs-1x">{{ $sertifikasiData['sip_aktif'] }}</span>
                                    <div class="text-muted fs-8">SIP</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Aktivitas Tahun Ini --}}
            <div class="col-md-6 col-xl-3">
                <div class="card user-stats-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-5">
                            <div class="stats-icon bg-light-info text-info me-3">
                                <i class="ki-duotone ki-calendar">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div>
                                <span class="text-gray-800 fw-bold fs-6">Aktivitas {{ date('Y') }}</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="row">
                                <div class="col-6">
                                    <span class="text-gray-900 fw-bold fs-3">{{ $sertifikasiData['seminar_tahun_ini'] }}</span>
                                    <div class="text-muted fs-8">Seminar</div>
                                </div>
                                <div class="col-6">
                                    <span class="text-gray-900 fw-bold fs-3">{{ $kepegawaianData['surat_izin_tahun_ini'] }}</span>
                                    <div class="text-muted fs-8">Izin</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Information Cards --}}
        <div class="row g-6 g-xl-9">
            {{-- Informasi Personal --}}
            <div class="col-xl-6">
                <div class="card user-info-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Personal</h3>
                        <div class="card-toolbar">
                            <a href="{{ route('user.profil.index') }}" class="btn btn-sm btn-user-primary">
                                <i class="ki-duotone ki-eye fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                <tr>
                                    <td class="w-50">
                                        <span class="text-gray-900 fw-bold d-block fs-7">NIP/ID Pegawai</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-bold d-block fs-7">{{ $karyawan->kd_karyawan }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-gray-900 fw-bold d-block fs-7">Email</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-bold d-block fs-7">{{ $karyawan->email ?: 'Belum diisi' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-gray-900 fw-bold d-block fs-7">No. HP</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-bold d-block fs-7">{{ $karyawan->no_hp ?: 'Belum diisi' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-gray-900 fw-bold d-block fs-7">Golongan</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-bold d-block fs-7">{{ $personalData['golongan'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-gray-900 fw-bold d-block fs-7">Tempat, Tgl Lahir</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-bold d-block fs-7">
                                            @if($karyawan->tempat_lahir && $karyawan->tgl_lahir)
                                                {{ $karyawan->tempat_lahir }}, {{ date('d/m/Y', strtotime($karyawan->tgl_lahir)) }}
                                            @else
                                                Belum diisi
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Notifikasi --}}
            <div class="col-xl-6">
                <div class="card user-info-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Notifikasi Penting</h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-primary">{{ count($notifications) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($notifications) > 0)
                            <div class="scroll-y" data-kt-scroll="true" data-kt-scroll-height="300px">
                                @foreach($notifications as $notification)
                                    <div class="notification-item notification-{{ $notification['type'] }} p-3 mb-3 bg-light rounded">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                @if($notification['type'] == 'warning')
                                                    <i class="ki-duotone ki-warning-2 fs-2 text-warning">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                @elseif($notification['type'] == 'danger')
                                                    <i class="ki-duotone ki-cross-circle fs-2 text-danger">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                @else
                                                    <i class="ki-duotone ki-information fs-2 text-info">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold text-gray-900">{{ $notification['title'] }}</h6>
                                                <p class="mb-0 text-gray-700 fs-7">{{ $notification['message'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ki-duotone ki-check-circle fs-3x text-success mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h6 class="text-gray-600">Tidak ada notifikasi</h6>
                                <p class="text-muted fs-7 mb-0">Semua data Anda dalam kondisi baik</p>
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
    // Script untuk dashboard user
    $(document).ready(function() {
        // Auto refresh notifikasi setiap 5 menit
        setInterval(function() {
            // Implementasi refresh notifikasi jika diperlukan
        }, 300000);
        
        // Animasi progress bar
        $('.completion-progress .bg-primary').each(function() {
            var width = $(this).css('width');
            $(this).css('width', '0').animate({width: width}, 1000);
        });
    });
</script>
@endpush