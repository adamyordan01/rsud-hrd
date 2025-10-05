@extends('layouts.user', ['title' => 'Riwayat SK'])

@php
    \Carbon\Carbon::setLocale('id');
@endphp

@push('styles')
<style>
    .sk-card {
        transition: all 0.3s;
        border: 1px solid #e1e3ea;
    }
    
    .sk-card:hover {
        border-color: var(--bs-user-primary);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .sk-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #e1e3ea;
    }
    
    .sk-type-badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
    }
    
    .sk-detail-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f1f1f2;
    }
    
    .sk-detail-item:last-child {
        border-bottom: none;
    }
    
    .sk-detail-label {
        font-weight: 600;
        color: #5e6278;
        min-width: 120px;
    }
    
    .sk-detail-value {
        color: #181c32;
        text-align: right;
        flex: 1;
    }
    
    .filter-card {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .download-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Riwayat SK
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.kepegawaian.index') }}" class="text-muted text-hover-primary">Data Kepegawaian</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Riwayat SK</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        {{-- Filter Card --}}
        <div class="card filter-card mb-8">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <select class="form-select" id="filter-tahun">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunList as $tahun)
                                <option value="{{ $tahun->tahun }}" {{ request('tahun') == $tahun->tahun ? 'selected' : '' }}>
                                    {{ $tahun->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pencarian</label>
                        <input type="text" class="form-control" id="filter-search" 
                               placeholder="Cari nomor SK atau keterangan..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold d-block">&nbsp;</label>
                        <button type="button" class="btn btn-primary w-100" id="btn-filter">
                            <i class="ki-duotone ki-funnel fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Summary Stats --}}
        <div class="row g-6 g-xl-9 mb-8">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-duotone ki-document fs-3x text-primary mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <h3 class="text-gray-900 fw-bold">{{ $totalSk }}</h3>
                        <div class="text-muted">Total SK</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-duotone ki-calendar fs-3x text-success mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <h3 class="text-gray-900 fw-bold">{{ date('Y') }}</h3>
                        <div class="text-muted">{{ $skTahunIni }} SK Tahun Ini</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-duotone ki-chart-line-up fs-3x text-warning mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <h3 class="text-gray-900 fw-bold">{{ $skTerbaru ? \Carbon\Carbon::parse($skTerbaru->tgl_sk)->translatedFormat('M Y') : '-' }}</h3>
                        <div class="text-muted">SK Terakhir</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- SK List --}}
        <div class="row g-6 g-xl-9" id="sk-container">
            @forelse($riwayatSk as $sk)
                <div class="col-xl-6 sk-item" data-tahun="{{ \Carbon\Carbon::parse($sk->tgl_sk)->format('Y') }}">
                    <div class="card sk-card h-100">
                        <div class="card-header sk-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h5 class="card-title text-gray-900 fw-bold mb-0">SK Kontrak</h5>
                                <div>
                                    <span class="sk-type-badge badge badge-light-primary">{{ $sk->status ?? 'Aktif' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="sk-detail-item">
                                <span class="sk-detail-label">Nomor SK</span>
                                <span class="sk-detail-value">{{ $sk->no_sk }}</span>
                            </div>
                            <div class="sk-detail-item">
                                <span class="sk-detail-label">Tanggal SK</span>
                                <span class="sk-detail-value">{{ \Carbon\Carbon::parse($sk->tgl_sk)->translatedFormat('d F Y') }}</span>
                            </div>
                            @if($sk->tahun_sk)
                                <div class="sk-detail-item">
                                    <span class="sk-detail-label">Tahun SK</span>
                                    <span class="sk-detail-value">{{ $sk->tahun_sk }}</span>
                                </div>
                            @endif
                            @if($sk->no_per_kerja)
                                <div class="sk-detail-item">
                                    <span class="sk-detail-label">No. Per Kerja</span>
                                    <span class="sk-detail-value">{{ $sk->no_per_kerja }}</span>
                                </div>
                            @endif
                            @if($sk->tgl_ttd)
                                <div class="sk-detail-item">
                                    <span class="sk-detail-label">Tanggal TTD</span>
                                    <span class="sk-detail-value">{{ \Carbon\Carbon::parse($sk->tgl_ttd)->translatedFormat('d F Y') }}</span>
                                </div>
                            @endif
                            @if($sk->nomor_konsederan)
                                <div class="sk-detail-item">
                                    <span class="sk-detail-label">Nomor Konsederan</span>
                                    <span class="sk-detail-value">{{ $sk->nomor_konsederan }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="ki-duotone ki-time fs-7 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Dibuat: {{ \Carbon\Carbon::parse($sk->tgl_sk)->translatedFormat('d F Y') }}
                                </small>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-icon btn-primary download-btn" 
                                            onclick="downloadSk('{{ $sk->kd_index }}')" title="Download SK">
                                        <i class="ki-duotone ki-cloud-download fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-info download-btn" 
                                            onclick="viewSk('{{ $sk->kd_index }}')" title="Lihat Detail">
                                        <i class="ki-duotone ki-eye fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-10">
                            <i class="ki-duotone ki-document fs-3x text-muted mb-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h5 class="text-muted mb-3">Belum Ada Riwayat SK</h5>
                            <p class="text-muted">Dokumen Surat Keputusan akan muncul di sini setelah diproses oleh admin.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="skDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail SK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="sk-detail-content">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-user-primary" id="btn-download-from-modal">
                    <i class="ki-duotone ki-down fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Download SK
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSkId = null;

$(document).ready(function() {
    // Filter functionality
    $('#btn-filter').click(function() {
        applyFilters();
    });
    
    // Enter key on search
    $('#filter-search').keypress(function(e) {
        if(e.which == 13) {
            applyFilters();
        }
    });
    
    function applyFilters() {
        const tahun = $('#filter-tahun').val();
        const search = $('#filter-search').val().toLowerCase();
        
        $('.sk-item').each(function() {
            const item = $(this);
            const itemTahun = item.data('tahun').toString();
            const itemText = item.text().toLowerCase();
            
            let show = true;
            
            if(tahun && itemTahun !== tahun) show = false;
            if(search && !itemText.includes(search)) show = false;
            
            if(show) {
                item.show();
            } else {
                item.hide();
            }
        });
        
        // Check if no results
        const visibleItems = $('.sk-item:visible').length;
        if(visibleItems === 0) {
            if(!$('#no-results').length) {
                $('#sk-container').append(`
                    <div class="col-12" id="no-results">
                        <div class="card">
                            <div class="card-body text-center py-10">
                                <i class="ki-duotone ki-magnifier fs-3x text-muted mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h5 class="text-muted mb-3">Tidak Ada Hasil</h5>
                                <p class="text-muted">Tidak ditemukan SK sesuai dengan filter yang dipilih.</p>
                            </div>
                        </div>
                    </div>
                `);
            }
        } else {
            $('#no-results').remove();
        }
    }
});

function viewSk(skId) {
    currentSkId = skId;
    $('#skDetailModal').modal('show');
    
    $.get(`{{ route('user.kepegawaian.sk-detail', ':id') }}`.replace(':id', skId))
        .done(function(response) {
            $('#sk-detail-content').html(response.html);
        })
        .fail(function() {
            $('#sk-detail-content').html(`
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Gagal memuat detail SK
                </div>
            `);
        });
}

function downloadSk(skId) {
    const downloadUrl = `{{ route('user.kepegawaian.download-sk', ':id') }}`.replace(':id', skId);
    
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

// Download from modal
$('#btn-download-from-modal').click(function() {
    if(currentSkId) {
        downloadSk(currentSkId);
        $('#skDetailModal').modal('hide');
    }
});
</script>
@endpush