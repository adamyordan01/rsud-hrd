@php
    \Carbon\Carbon::setLocale('id');
@endphp

<div class="d-flex flex-column gap-7">
    
    {{-- Header dengan bentuk penghargaan --}}
    <div class="text-center">
        <h4 class="fw-bold text-gray-900 mb-3">{{ $penghargaan->bentuk ?? 'Penghargaan' }}</h4>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            @if($penghargaan->tahun ?? $penghargaan->tgl_sk)
                <span class="badge badge-lg badge-warning">
                    <i class="ki-outline ki-medal fs-5 me-1"></i>
                    {{ $penghargaan->tahun ?? date('Y', strtotime($penghargaan->tgl_sk)) }}
                </span>
            @endif
            @if($penghargaan->pejabat)
                <span class="badge badge-lg badge-info">{{ Str::limit($penghargaan->pejabat, 20) }}</span>
            @endif
        </div>
    </div>
    
    {{-- Informasi Utama --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Informasi Penghargaan</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @if($penghargaan->bentuk)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Bentuk Penghargaan</label>
                        <div class="fw-semibold text-gray-800">
                            <i class="ki-outline ki-award fs-5 text-warning me-2"></i>
                            {{ $penghargaan->bentuk }}
                        </div>
                    </div>
                @endif
                
                @if($penghargaan->pejabat)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Pejabat Pemberi</label>
                        <div class="fw-semibold text-gray-800">
                            <i class="ki-outline ki-user-tick fs-5 text-primary me-2"></i>
                            {{ $penghargaan->pejabat }}
                        </div>
                    </div>
                @endif
                
                @if($penghargaan->tgl_sk)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Tanggal SK</label>
                        <div class="fw-semibold text-gray-800">
                            <i class="ki-outline ki-calendar fs-5 text-success me-2"></i>
                            {{ \Carbon\Carbon::parse($penghargaan->tgl_sk)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                @endif
                
                @if($penghargaan->no_sk)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Nomor SK</label>
                        <div class="fw-semibold text-gray-800">
                            <i class="ki-outline ki-document fs-5 text-info me-2"></i>
                            {{ $penghargaan->no_sk }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Event/Kegiatan --}}
    @if($penghargaan->event)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Event/Kegiatan</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <i class="ki-outline ki-element-11 fs-2x text-primary me-4"></i>
                    <div class="text-gray-800">{{ $penghargaan->event }}</div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Keterangan --}}
    @if($penghargaan->ket)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Keterangan</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <i class="ki-outline ki-notepad-edit fs-2x text-warning me-4"></i>
                    <div class="text-gray-800">{!! nl2br(e($penghargaan->ket)) !!}</div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Timeline Summary --}}
    @if($penghargaan->tgl_sk)
        @php
            $tanggalSK = \Carbon\Carbon::parse($penghargaan->tgl_sk);
            $sekarang = \Carbon\Carbon::now();
            $selisihTahun = $sekarang->diffInYears($tanggalSK);
        @endphp
        
        <div class="alert alert-info d-flex align-items-center">
            <i class="ki-outline ki-information-5 fs-2x me-4"></i>
            <div>
                <strong>Waktu Penghargaan:</strong> 
                @if($selisihTahun == 0)
                    Diterima tahun ini ({{ $tanggalSK->translatedFormat('F Y') }})
                @else
                    Diterima {{ $selisihTahun }} tahun yang lalu ({{ $tanggalSK->translatedFormat('F Y') }})
                @endif
                <br>
                <small class="text-muted">
                    {{ $tanggalSK->translatedFormat('d F Y') }}
                </small>
            </div>
        </div>
    @endif
    
    {{-- Additional Info Card --}}
    <div class="card bg-light-primary">
        <div class="card-body text-center">
            <i class="ki-outline ki-medal-star fs-3x text-primary mb-3"></i>
            <h5 class="text-primary mb-2">Penghargaan Atas Prestasi</h5>
            <p class="text-muted mb-0">
                Penghargaan ini diberikan sebagai bentuk apresiasi atas kontribusi dan dedikasi dalam menjalankan tugas.
            </p>
        </div>
    </div>
    
</div>