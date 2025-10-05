@extends('layouts.user', ['title' => 'Riwayat Seminar'])

@php
    \Carbon\Carbon::setLocale('id');
@endphp

@push('styles')
<style>
    .seminar-card {
        transition: all 0.3s;
        border: 1px solid #e1e3ea;
    }
    
    .seminar-card:hover {
        border-color: var(--bs-user-primary);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .jp-badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
        background: var(--bs-primary);
        color: white;
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
                    Riwayat Seminar
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.sertifikasi.index') }}" class="text-muted text-hover-primary">Sertifikasi</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Seminar</li>
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
                        <i class="ki-outline ki-teacher fs-3x text-primary mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $statistik['total_seminar'] }}</h3>
                        <div class="text-muted">Total Seminar</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-calendar fs-3x text-success mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $statistik['seminar_tahun_ini'] }}</h3>
                        <div class="text-muted">Seminar Tahun Ini</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-medal-star fs-3x text-warning mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $statistik['total_jp'] }}</h3>
                        <div class="text-muted">Total Jam</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-chart-line-up fs-3x text-info mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $statistik['jp_tahun_ini'] }}</h3>
                        <div class="text-muted">Jam Tahun Ini</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Filter Card --}}
        <div class="card filter-card mb-8">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <select class="form-select" id="filter-tahun">
                            <option value="">Semua Tahun</option>
                            @foreach($dataSeminar->pluck('tahun')->unique()->sort()->reverse() as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pencarian</label>
                        <input type="text" class="form-control" id="filter-search" 
                               placeholder="Cari nama seminar atau penyelenggara...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold d-block">&nbsp;</label>
                        <button type="button" class="btn btn-user-primary w-100" id="btn-filter">
                            <i class="ki-outline ki-funnel fs-3"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Seminar List --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Seminar</h3>
                <div class="card-toolbar">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm w-150px" id="sort-by">
                            <option value="tgl_mulai_desc">Terbaru</option>
                            <option value="tgl_mulai_asc">Terlama</option>
                            <option value="jp_desc">Jam Tertinggi</option>
                            <option value="jp_asc">Jam Terendah</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(count($dataSeminar) > 0)
                    <div class="row g-6 g-xl-9" id="seminar-container">
                        @foreach($dataSeminar as $seminar)
                            <div class="col-xl-6 col-md-12 seminar-item" data-tahun="{{ $seminar->tahun }}" data-jp="{{ $seminar->jml_jam }}" data-tanggal="{{ $seminar->tgl_mulai }}">
                                <div class="card seminar-card h-100">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <h5 class="card-title text-gray-900 fw-bold mb-0">{{ Str::limit($seminar->nama_seminar ?? 'Seminar', 50) }}</h5>
                                            <div class="d-flex gap-2">
                                                @if($seminar->jml_jam)
                                                    <span class="jp-badge">{{ $seminar->jml_jam }} Jam</span>
                                                @endif
                                                <span class="badge badge-light-primary">{{ $seminar->tahun }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($seminar->penyelenggara)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">Penyelenggara:</strong>
                                                <div class="text-gray-800 fw-semibold">{{ $seminar->penyelenggara }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($seminar->tgl_mulai)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">Tanggal Mulai:</strong>
                                                <div class="text-gray-800">{{ \Carbon\Carbon::parse($seminar->tgl_mulai)->translatedFormat('d F Y') }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($seminar->tgl_akhir && $seminar->tgl_akhir != $seminar->tgl_mulai)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">Tanggal Selesai:</strong>
                                                <div class="text-gray-800">{{ \Carbon\Carbon::parse($seminar->tgl_akhir)->translatedFormat('d F Y') }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($seminar->jml_jam)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">Jumlah Jam:</strong>
                                                <div class="text-gray-800">{{ $seminar->jml_jam }} jam</div>
                                            </div>
                                        @endif
                                        
                                        @if($seminar->ket)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">Keterangan:</strong>
                                                <div class="text-gray-800">{{ Str::limit($seminar->ket, 150) }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($seminar->no_sertifikat)
                                            <div class="mb-3">
                                                <strong class="text-muted fs-7">No. Sertifikat:</strong>
                                                <div class="text-gray-800">{{ $seminar->no_sertifikat }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="ki-outline ki-time fs-7 me-1"></i>
                                                Diikuti: {{ $seminar->tgl_mulai ? \Carbon\Carbon::parse($seminar->tgl_mulai)->translatedFormat('M Y') : '-' }}
                                            </small>
                                            <div class="d-flex gap-2">
                                                @if($seminar->no_sertifikat)
                                                    <button type="button" class="btn btn-light-primary btn-sm" 
                                                            onclick="downloadSertifikat('{{ $seminar->urut_seminar }}')" title="Download Sertifikat">
                                                        <i class="ki-outline ki-down fs-5"></i>
                                                        Sertifikat
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-light-info btn-sm" 
                                                        onclick="viewSeminarDetail('{{ $seminar->urut_seminar }}')" title="Lihat Detail">
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
                        <i class="ki-outline ki-teacher fs-3x text-muted mb-5"></i>
                        <h5 class="text-muted mb-3">Belum Ada Data Seminar</h5>
                        <p class="text-muted">Riwayat seminar dan pelatihan akan muncul di sini setelah diinput oleh admin.</p>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="seminarDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Seminar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="seminar-detail-content">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-user-primary" id="btn-download-sertifikat-from-modal">
                    <i class="ki-outline ki-down fs-3"></i>
                    Download Sertifikat
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSeminarId = null;

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
    
    // Sort functionality
    $('#sort-by').change(function() {
        applySorting();
    });
    
    function applyFilters() {
        const tahun = $('#filter-tahun').val();
        const search = $('#filter-search').val().toLowerCase();
        
        $('.seminar-item').each(function() {
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
        
        checkNoResults();
    }
    
    function applySorting() {
        const sortBy = $('#sort-by').val();
        const container = $('#seminar-container');
        const items = container.children('.seminar-item').get();
        
        items.sort(function(a, b) {
            const aItem = $(a);
            const bItem = $(b);
            
            switch(sortBy) {
                case 'tgl_mulai_desc':
                    return new Date(bItem.data('tanggal')) - new Date(aItem.data('tanggal'));
                case 'tgl_mulai_asc':
                    return new Date(aItem.data('tanggal')) - new Date(bItem.data('tanggal'));
                case 'jp_desc':
                    return parseInt(bItem.data('jp') || 0) - parseInt(aItem.data('jp') || 0);
                case 'jp_asc':
                    return parseInt(aItem.data('jp') || 0) - parseInt(bItem.data('jp') || 0);
                default:
                    return 0;
            }
        });
        
        $.each(items, function(idx, item) {
            container.append(item);
        });
    }
    
    function checkNoResults() {
        const visibleItems = $('.seminar-item:visible').length;
        if(visibleItems === 0) {
            if(!$('#no-results').length) {
                $('#seminar-container').append(`
                    <div class="col-12" id="no-results">
                        <div class="card">
                            <div class="card-body text-center py-10">
                                <i class="ki-outline ki-magnifier fs-3x text-muted mb-5"></i>
                                <h5 class="text-muted mb-3">Tidak Ada Hasil</h5>
                                <p class="text-muted">Tidak ditemukan seminar sesuai dengan filter yang dipilih.</p>
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

function downloadSertifikat(seminarId) {
    const downloadUrl = `{{ route('user.sertifikasi.download-sertifikat-seminar', ':id') }}`.replace(':id', seminarId);
    
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

function viewSeminarDetail(seminarId) {
    currentSeminarId = seminarId;
    $('#seminarDetailModal').modal('show');
    
    $.get(`{{ route('user.sertifikasi.seminar-detail', ':id') }}`.replace(':id', seminarId))
        .done(function(response) {
            $('#seminar-detail-content').html(response.html);
        })
        .fail(function() {
            $('#seminar-detail-content').html(`
                <div class="alert alert-danger">
                    <i class="ki-outline ki-cross-circle fs-2 me-2"></i>
                    Gagal memuat detail seminar
                </div>
            `);
        });
}

// Download from modal
$('#btn-download-sertifikat-from-modal').click(function() {
    if(currentSeminarId) {
        downloadSertifikat(currentSeminarId);
        $('#seminarDetailModal').modal('hide');
    }
});
</script>
@endpush