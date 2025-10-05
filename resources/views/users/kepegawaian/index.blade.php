@extends('layouts.user', ['title' => 'Data Kepegawaian'])

@push('styles')
<style>
    .kepegawaian-card {
        transition: all 0.3s;
    }
    
    .kepegawaian-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 20px;
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: bold;
        line-height: 1;
    }
    
    .recent-item {
        border-left: 4px solid #009ef7;
        padding-left: 15px;
        margin-bottom: 15px;
    }
    
    .document-type-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Data Kepegawaian
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Data Kepegawaian</li>
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
                <div class="card text-center kepegawaian-card h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-light-primary text-primary mx-auto">
                            <i class="ki-duotone ki-document">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="stats-number text-gray-900">{{ $stats['total_sk'] }}</div>
                        <div class="text-muted fw-semibold">Total SK</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center kepegawaian-card h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-light-success text-success mx-auto">
                            <i class="ki-duotone ki-arrow-right-left">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="stats-number text-gray-900">{{ $stats['total_mutasi'] }}</div>
                        <div class="text-muted fw-semibold">Riwayat Mutasi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center kepegawaian-card h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-light-warning text-warning mx-auto">
                            <i class="ki-duotone ki-calendar">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <div class="stats-number text-gray-900">{{ $stats['surat_izin_tahun_ini'] }}</div>
                        <div class="text-muted fw-semibold">Izin {{ date('Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center kepegawaian-card h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-light-info text-info mx-auto">
                            <i class="ki-duotone ki-chart-line-up">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                        <div class="stats-number text-gray-900">{{ $stats['tahun_pengalaman'] }}</div>
                        <div class="text-muted fw-semibold">Tahun Pengalaman</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Menu Cards --}}
        <div class="row g-6 g-xl-9 mb-8">
            {{-- Riwayat SK --}}
            <div class="col-md-6 col-xl-4">
                <div class="card kepegawaian-card h-100">
                    <div class="card-body text-center">
                        <div class="feature-icon bg-light-primary text-primary mx-auto">
                            <i class="ki-duotone ki-document">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h4 class="text-gray-900 fw-bold mb-3">Riwayat SK</h4>
                        <p class="text-muted mb-4">Lihat semua dokumen Surat Keputusan yang pernah Anda terima</p>
                        <a href="{{ route('user.kepegawaian.riwayat-sk') }}" class="btn btn-user-primary">
                            Lihat Riwayat SK
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Riwayat Mutasi --}}
            <div class="col-md-6 col-xl-4">
                <div class="card kepegawaian-card h-100">
                    <div class="card-body text-center">
                        <div class="feature-icon bg-light-success text-success mx-auto">
                            <i class="ki-duotone ki-arrow-right-left">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h4 class="text-gray-900 fw-bold mb-3">Riwayat Mutasi</h4>
                        <p class="text-muted mb-4">Histori perpindahan ruangan dan perubahan jabatan</p>
                        <a href="{{ route('user.kepegawaian.riwayat-mutasi') }}" class="btn btn-user-primary">
                            Lihat Riwayat Mutasi
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Surat Izin --}}
            <div class="col-md-6 col-xl-4">
                <div class="card kepegawaian-card h-100">
                    <div class="card-body text-center">
                        <div class="feature-icon bg-light-warning text-warning mx-auto">
                            <i class="ki-duotone ki-calendar">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h4 class="text-gray-900 fw-bold mb-3">Surat Izin</h4>
                        <p class="text-muted mb-4">Riwayat cuti, izin sakit, dan izin lainnya</p>
                        <a href="{{ route('user.kepegawaian.surat-izin') }}" class="btn btn-user-primary">
                            Lihat Surat Izin
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Recent Activities --}}
        <div class="row g-6 g-xl-9">
            {{-- SK Terbaru --}}
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">SK Terbaru</h3>
                        <div class="card-toolbar">
                            <a href="{{ route('user.kepegawaian.riwayat-sk') }}" class="btn btn-sm btn-user-primary">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($recentSk) > 0)
                            @foreach($recentSk as $sk)
                                <div class="recent-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="text-gray-900 fw-bold mb-1">{{ $sk->jenis_sk }}</h6>
                                            <p class="text-muted fs-7 mb-1">No: {{ $sk->no_sk }}</p>
                                            <p class="text-muted fs-8 mb-0">
                                                <i class="ki-duotone ki-calendar fs-8 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                {{ date('d/m/Y', strtotime($sk->tgl_sk)) }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="badge document-type-badge badge-light-primary">{{ $sk->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="ki-duotone ki-document fs-3x text-muted mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h6 class="text-muted">Belum ada SK</h6>
                                <p class="text-muted fs-7 mb-0">Dokumen SK akan muncul di sini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Aktivitas Terbaru --}}
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Aktivitas Terbaru</h3>
                    </div>
                    <div class="card-body">
                        @if(count($recentActivities) > 0)
                            @foreach($recentActivities as $activity)
                                <div class="recent-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="text-gray-900 fw-bold mb-1">{{ $activity['title'] }}</h6>
                                            <p class="text-muted fs-7 mb-1">{{ $activity['description'] }}</p>
                                            <p class="text-muted fs-8 mb-0">
                                                <i class="ki-duotone ki-time fs-8 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                {{ $activity['date'] }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="badge document-type-badge badge-light-{{ $activity['type'] }}">{{ $activity['category'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="ki-duotone ki-chart-line-up fs-3x text-muted mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <h6 class="text-muted">Belum ada aktivitas</h6>
                                <p class="text-muted fs-7 mb-0">Aktivitas kepegawaian akan muncul di sini</p>
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
    $('.kepegawaian-card').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({
            opacity: 1
        }, 600);
    });
});
</script>
@endpush