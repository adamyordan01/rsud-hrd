@php
    \Carbon\Carbon::setLocale('id');
@endphp

<div class="d-flex flex-column gap-7">
    
    {{-- Header dengan nama seminar --}}
    <div class="text-center">
        <h4 class="fw-bold text-gray-900 mb-3">{{ $seminar->nama_seminar ?? 'Seminar' }}</h4>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            @if($seminar->jml_jam)
                <span class="badge badge-lg badge-primary">{{ $seminar->jml_jam }} Jam</span>
            @endif
            @if($seminar->tahun)
                <span class="badge badge-lg badge-info">{{ $seminar->tahun }}</span>
            @endif
        </div>
    </div>
    
    {{-- Informasi Utama --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Informasi Seminar</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @if($seminar->penyelenggara)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Penyelenggara</label>
                        <div class="fw-semibold text-gray-800">{{ $seminar->penyelenggara }}</div>
                    </div>
                @endif
                
                @if($seminar->no_sertifikat)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">No. Sertifikat</label>
                        <div class="fw-semibold text-gray-800">{{ $seminar->no_sertifikat }}</div>
                    </div>
                @endif
                
                @if($seminar->tgl_mulai)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Tanggal Mulai</label>
                        <div class="fw-semibold text-gray-800">
                            <i class="ki-outline ki-calendar fs-5 text-primary me-2"></i>
                            {{ \Carbon\Carbon::parse($seminar->tgl_mulai)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                @endif
                
                @if($seminar->tgl_akhir && $seminar->tgl_akhir != $seminar->tgl_mulai)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Tanggal Selesai</label>
                        <div class="fw-semibold text-gray-800">
                            <i class="ki-outline ki-calendar fs-5 text-success me-2"></i>
                            {{ \Carbon\Carbon::parse($seminar->tgl_akhir)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                @endif
                
                @if($seminar->jml_jam)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Jumlah Jam</label>
                        <div class="fw-semibold text-gray-800">
                            <i class="ki-outline ki-time fs-5 text-warning me-2"></i>
                            {{ $seminar->jml_jam }} jam
                        </div>
                    </div>
                @endif
                
                @if($seminar->tahun)
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Tahun</label>
                        <div class="fw-semibold text-gray-800">
                            <i class="ki-outline ki-calendar-2 fs-5 text-info me-2"></i>
                            {{ $seminar->tahun }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Keterangan --}}
    @if($seminar->ket)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Keterangan</h5>
            </div>
            <div class="card-body">
                <div class="text-gray-800">{!! nl2br(e($seminar->ket)) !!}</div>
            </div>
        </div>
    @endif
    
    {{-- Informasi File --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">File Sertifikat</h5>
        </div>
        <div class="card-body">
            @if($seminar->no_sertifikat)
                <div class="d-flex align-items-center justify-content-between p-4 bg-light-success rounded">
                    <div class="d-flex align-items-center">
                        <i class="ki-outline ki-file-check fs-2x text-success me-4"></i>
                        <div>
                            <div class="fw-bold text-gray-900">Sertifikat Tersedia</div>
                            <div class="text-muted fs-7">No. Sertifikat: {{ $seminar->no_sertifikat }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" 
                            onclick="downloadSertifikat('{{ $seminar->urut_seminar }}')">
                        <i class="ki-outline ki-down fs-5"></i>
                        Download
                    </button>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-center p-4 bg-light-warning rounded">
                    <i class="ki-outline ki-file-deleted fs-2x text-warning me-4"></i>
                    <div class="text-center">
                        <div class="fw-bold text-gray-900">Sertifikat Belum Tersedia</div>
                        <div class="text-muted fs-7">File sertifikat belum diupload atau tidak tersedia</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Ringkasan Durasi --}}
    @if($seminar->tgl_mulai && $seminar->tgl_akhir)
        @php
            $mulai = \Carbon\Carbon::parse($seminar->tgl_mulai);
            $selesai = \Carbon\Carbon::parse($seminar->tgl_akhir);
            $durasi = $mulai->diffInDays($selesai) + 1;
        @endphp
        
        @if($durasi > 1)
            <div class="alert alert-info d-flex align-items-center">
                <i class="ki-outline ki-information-5 fs-2x me-4"></i>
                <div>
                    <strong>Durasi Seminar:</strong> {{ $durasi }} hari
                    <br>
                    <small class="text-muted">
                        {{ $mulai->translatedFormat('d F Y') }} s/d {{ $selesai->translatedFormat('d F Y') }}
                    </small>
                </div>
            </div>
        @endif
    @endif
    
</div>