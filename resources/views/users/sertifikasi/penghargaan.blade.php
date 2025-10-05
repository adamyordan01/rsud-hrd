@extends('layouts.user', ['title' => 'Riwayat Penghargaan'])

@php
    \Carbon\Carbon::setLocale('id');
@endphp

@push('styles')
<style>
    .penghargaan-card {
        transition: all 0.3s;
        border: 1px solid #e1e3ea;
    }
    
    .penghargaan-card:hover {
        border-color: var(--bs-user-primary);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .award-badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
        background: var(--bs-warning);
        color: white;
    }
    
    .filter-card {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .year-section {
        border-left: 4px solid var(--bs-primary);
        padding-left: 1rem;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Riwayat Penghargaan
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.sertifikasi.index') }}" class="text-muted text-hover-primary">Sertifikasi</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Penghargaan</li>
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
                        <i class="ki-outline ki-medal fs-3x text-warning mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $statistik['total_penghargaan'] }}</h3>
                        <div class="text-muted">Total Penghargaan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-calendar fs-3x text-success mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $statistik['penghargaan_tahun_ini'] }}</h3>
                        <div class="text-muted">Penghargaan Tahun Ini</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-award fs-3x text-primary mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ $statistik['total_bentuk'] }}</h3>
                        <div class="text-muted">Jenis Penghargaan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-outline ki-user-tick fs-3x text-info mb-3"></i>
                        <h3 class="text-gray-900 fw-bold">{{ Str::limit($statistik['pejabat_terbanyak'], 10) }}</h3>
                        <div class="text-muted">Pejabat Terbanyak</div>
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
                            @foreach($penghargaanPerTahun->keys()->sort()->reverse() as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Bentuk Penghargaan</label>
                        <select class="form-select" id="filter-bentuk">
                            <option value="">Semua Bentuk</option>
                            @foreach($dataPenghargaan->pluck('bentuk')->unique()->filter() as $bentuk)
                                <option value="{{ $bentuk }}">{{ Str::limit($bentuk, 30) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pencarian</label>
                        <input type="text" class="form-control" id="filter-search" 
                               placeholder="Cari penghargaan, pejabat, atau event...">
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
        
        {{-- Penghargaan List by Year --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Penghargaan</h3>
                <div class="card-toolbar">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm w-150px" id="sort-by">
                            <option value="tgl_desc">Terbaru</option>
                            <option value="tgl_asc">Terlama</option>
                            <option value="bentuk_asc">Bentuk A-Z</option>
                            <option value="bentuk_desc">Bentuk Z-A</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(count($dataPenghargaan) > 0)
                    <div id="penghargaan-container">
                        @foreach($penghargaanPerTahun->sortKeysDesc() as $tahun => $penghargaanList)
                            <div class="year-section" data-tahun="{{ $tahun }}">
                                <h4 class="text-primary fw-bold mb-4">
                                    <i class="ki-outline ki-calendar-2 fs-2 me-2"></i>
                                    Tahun {{ $tahun }}
                                    <span class="badge badge-light-primary ms-2">{{ $penghargaanList->count() }} penghargaan</span>
                                </h4>
                                
                                <div class="row g-6 g-xl-9">
                                    @foreach($penghargaanList->sortByDesc('tgl_sk') as $penghargaan)
                                        <div class="col-xl-6 col-md-12 penghargaan-item" 
                                             data-tahun="{{ $tahun }}" 
                                             data-bentuk="{{ $penghargaan->bentuk }}" 
                                             data-tanggal="{{ $penghargaan->tgl_sk }}">
                                            <div class="card penghargaan-card h-100">
                                                <div class="card-header">
                                                    <div class="d-flex justify-content-between align-items-center w-100">
                                                        <h5 class="card-title text-gray-900 fw-bold mb-0">
                                                            {{ Str::limit($penghargaan->bentuk ?? 'Penghargaan', 40) }}
                                                        </h5>
                                                        <span class="award-badge">{{ $tahun }}</span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    @if($penghargaan->pejabat)
                                                        <div class="mb-3">
                                                            <strong class="text-muted fs-7">Pejabat Pemberi:</strong>
                                                            <div class="text-gray-800 fw-semibold">{{ $penghargaan->pejabat }}</div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($penghargaan->tgl_sk)
                                                        <div class="mb-3">
                                                            <strong class="text-muted fs-7">Tanggal SK:</strong>
                                                            <div class="text-gray-800">
                                                                <i class="ki-outline ki-calendar fs-6 text-primary me-1"></i>
                                                                {{ \Carbon\Carbon::parse($penghargaan->tgl_sk)->translatedFormat('d F Y') }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($penghargaan->no_sk)
                                                        <div class="mb-3">
                                                            <strong class="text-muted fs-7">No. SK:</strong>
                                                            <div class="text-gray-800">{{ $penghargaan->no_sk }}</div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($penghargaan->event)
                                                        <div class="mb-3">
                                                            <strong class="text-muted fs-7">Event/Kegiatan:</strong>
                                                            <div class="text-gray-800">{{ Str::limit($penghargaan->event, 100) }}</div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($penghargaan->ket)
                                                        <div class="mb-3">
                                                            <strong class="text-muted fs-7">Keterangan:</strong>
                                                            <div class="text-gray-800">{{ Str::limit($penghargaan->ket, 150) }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card-footer bg-light">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            <i class="ki-outline ki-time fs-7 me-1"></i>
                                                            Diterima: {{ $penghargaan->tgl_sk ? \Carbon\Carbon::parse($penghargaan->tgl_sk)->translatedFormat('M Y') : '-' }}
                                                        </small>
                                                        <button type="button" class="btn btn-light-info btn-sm" 
                                                                onclick="viewPenghargaanDetail('{{ $penghargaan->urut_peng }}')" title="Lihat Detail">
                                                            <i class="ki-outline ki-eye fs-5"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <i class="ki-outline ki-medal fs-3x text-muted mb-5"></i>
                        <h5 class="text-muted mb-3">Belum Ada Data Penghargaan</h5>
                        <p class="text-muted">Riwayat penghargaan akan muncul di sini setelah diinput oleh admin.</p>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="penghargaanDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penghargaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="penghargaan-detail-content">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
        const bentuk = $('#filter-bentuk').val().toLowerCase();
        const search = $('#filter-search').val().toLowerCase();
        
        // Filter year sections
        $('.year-section').each(function() {
            const sectionTahun = $(this).data('tahun').toString();
            
            if(tahun && sectionTahun !== tahun) {
                $(this).hide();
                return;
            }
            
            let hasVisibleItems = false;
            
            // Filter items within this year
            $(this).find('.penghargaan-item').each(function() {
                const item = $(this);
                const itemBentuk = item.data('bentuk') ? item.data('bentuk').toLowerCase() : '';
                const itemText = item.text().toLowerCase();
                
                let show = true;
                
                if(bentuk && !itemBentuk.includes(bentuk)) show = false;
                if(search && !itemText.includes(search)) show = false;
                
                if(show) {
                    item.show();
                    hasVisibleItems = true;
                } else {
                    item.hide();
                }
            });
            
            // Show/hide year section based on visible items
            if(hasVisibleItems) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        checkNoResults();
    }
    
    function applySorting() {
        const sortBy = $('#sort-by').val();
        
        $('.year-section').each(function() {
            const section = $(this);
            const container = section.find('.row');
            const items = container.children('.penghargaan-item').get();
            
            items.sort(function(a, b) {
                const aItem = $(a);
                const bItem = $(b);
                
                switch(sortBy) {
                    case 'tgl_desc':
                        return new Date(bItem.data('tanggal')) - new Date(aItem.data('tanggal'));
                    case 'tgl_asc':
                        return new Date(aItem.data('tanggal')) - new Date(bItem.data('tanggal'));
                    case 'bentuk_asc':
                        return aItem.data('bentuk').localeCompare(bItem.data('bentuk'));
                    case 'bentuk_desc':
                        return bItem.data('bentuk').localeCompare(aItem.data('bentuk'));
                    default:
                        return 0;
                }
            });
            
            $.each(items, function(idx, item) {
                container.append(item);
            });
        });
    }
    
    function checkNoResults() {
        const visibleSections = $('.year-section:visible').length;
        if(visibleSections === 0) {
            if(!$('#no-results').length) {
                $('#penghargaan-container').append(`
                    <div class="text-center py-10" id="no-results">
                        <i class="ki-outline ki-magnifier fs-3x text-muted mb-5"></i>
                        <h5 class="text-muted mb-3">Tidak Ada Hasil</h5>
                        <p class="text-muted">Tidak ditemukan penghargaan sesuai dengan filter yang dipilih.</p>
                    </div>
                `);
            }
        } else {
            $('#no-results').remove();
        }
    }
});

function viewPenghargaanDetail(urutPeng) {
    $('#penghargaanDetailModal').modal('show');
    
    $.get(`{{ route('user.sertifikasi.penghargaan-detail', ':id') }}`.replace(':id', urutPeng))
        .done(function(response) {
            $('#penghargaan-detail-content').html(response.html);
        })
        .fail(function() {
            $('#penghargaan-detail-content').html(`
                <div class="alert alert-danger">
                    <i class="ki-outline ki-cross-circle fs-2 me-2"></i>
                    Gagal memuat detail penghargaan
                </div>
            `);
        });
}
</script>
@endpush