@extends('layouts.user', ['title' => 'Surat Izin Praktik (SIP)'])

@php
    \Carbon\Carbon::setLocale('id');
@endphp

@push('styles')
<style>
    .sip-card {
        transition: all 0.3s;
        border: 1px solid #e1e3ea;
    }
    
    .sip-card:hover {
        border-color: var(--bs-user-primary);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .sip-status {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
    }
    
    .filter-card {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Surat Izin Praktik (SIP)
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.sertifikasi.index') }}" class="text-muted text-hover-primary">Sertifikasi</a>
                    </li>
                    <li class="breadcrumb-item text-muted">SIP</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        {{-- Summary Stats --}}
        <div class="row g-6 g-xl-9 mb-8">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-document fs-3x text-primary mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ count($dataSip) }}</h3>
                        <div class="text-muted">Total SIP</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-check-circle fs-3x text-success mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ collect($dataSip)->filter(function($sip) { return $sip->status['status'] == 'active'; })->count() }}</h3>
                        <div class="text-muted">SIP Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-warning fs-3x text-warning mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ collect($dataSip)->filter(function($sip) { return $sip->status['status'] == 'warning'; })->count() }}</h3>
                        <div class="text-muted">Akan Berakhir</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-cross-circle fs-3x text-danger mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ collect($dataSip)->filter(function($sip) { return $sip->status['status'] == 'expired'; })->count() }}</h3>
                        <div class="text-muted">Kadaluarsa</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- SIP List --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar SIP</h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="filterSip()">
                        <i class="ki-outline ki-funnel fs-5"></i>
                        Filter
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(count($dataSip) > 0)
                    <div class="row g-6 g-xl-9">
                        @foreach($dataSip as $sip)
                            <div class="col-xl-4 col-md-6">
                                <div class="card sip-card h-100">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <h5 class="card-title text-gray-900 fw-bold mb-0">SIP</h5>
                                            <span class="sip-status badge {{ $sip->status['class'] }}">
                                                {{ $sip->status['text'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong class="text-muted fs-7">No. SIP:</strong>
                                            <div class="text-gray-800 fw-semibold">{{ $sip->no_sip ?? '-' }}</div>
                                        </div>
                                        
                                        @if($sip->tgl_sip)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">Tanggal Mulai:</strong>
                                                <div class="text-gray-800">{{ \Carbon\Carbon::parse($sip->tgl_sip)->translatedFormat('d F Y') }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($sip->tgl_kadaluarsa)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">Tanggal Berakhir:</strong>
                                                <div class="text-gray-800">{{ \Carbon\Carbon::parse($sip->tgl_kadaluarsa)->translatedFormat('d F Y') }}</div>
                                            </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <strong class="text-muted fs-7">Masa Berlaku:</strong>
                                            <div class="text-gray-800">{{ $sip->masa_berlaku }}</div>
                                        </div>
                                        
                                        @if($sip->ket)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">Keterangan:</strong>
                                                <div class="text-gray-800">{{ Str::limit($sip->ket, 100) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <!-- Info tambahan bisa ditambahkan di sini jika diperlukan -->
                                            </div>
                                            <div class="d-flex gap-2">
                                                @if($sip->sc_berkas)
                                                    <button type="button" class="btn btn-light-primary btn-sm" 
                                                            onclick="downloadSip('{{ $sip->urut_sip }}')" title="Download SIP">
                                                        <i class="ki-outline ki-down fs-5"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-light-info btn-sm" 
                                                        onclick="viewSipDetail('{{ $sip->urut_sip }}')" title="Lihat Detail">
                                                    <i class="ki-outline ki-eye fs-5"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <i class="ki-outline ki-document fs-3x text-muted mb-5"></i>
                        <h5 class="text-muted mb-3">Belum Ada Data SIP</h5>
                        <p class="text-muted">Data Surat Izin Praktik akan muncul di sini setelah diinput oleh admin.</p>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="sipDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail SIP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="sip-detail-content">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-user-primary" id="btn-download-from-modal">
                    <i class="ki-outline ki-down fs-3"></i>
                    Download SIP
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSipId = null;

function downloadSip(urutSip) {
    const downloadUrl = `{{ route('user.sertifikasi.download-sip', ':id') }}`.replace(':id', urutSip);
    
    // Show loading
    toastr.info('Mempersiapkan download...');
    
    // Create hidden link and trigger download
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    setTimeout(() => {
        toastr.success('File berhasil didownload');
    }, 1000);
}

function viewSipDetail(urutSip) {
    currentSipId = urutSip;
    $('#sipDetailModal').modal('show');
    
    $.get(`{{ route('user.sertifikasi.sip-detail', ':id') }}`.replace(':id', urutSip))
        .done(function(response) {
            $('#sip-detail-content').html(response.html);
        })
        .fail(function() {
            $('#sip-detail-content').html(`
                <div class="alert alert-danger">
                    <i class="ki-outline ki-cross-circle fs-2 me-2"></i>
                    Gagal memuat detail SIP
                </div>
            `);
        });
}

function filterSip() {
    // Implement filter functionality if needed
    console.log('Filter SIP clicked');
}

// Download from modal
$('#btn-download-from-modal').click(function() {
    if(currentSipId) {
        downloadSip(currentSipId);
        $('#sipDetailModal').modal('hide');
    }
});
</script>
@endpush