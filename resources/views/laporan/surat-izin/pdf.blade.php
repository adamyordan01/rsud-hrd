@extends('layouts.backend', ['title' => 'Laporan Surat Izin PDF'])

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                        <div class="card-title">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Export PDF Surat Izin</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Generate laporan dalam format PDF</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <form id="form-pdf-surat-izin" method="GET" action="{{ route('admin.laporan.surat-izin.pdf') }}">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="kd_ruangan" class="form-label fw-semibold">Ruangan/Unit Kerja</label>
                                    <select class="form-select" name="kd_ruangan" id="kd_ruangan">
                                        <option value="">Semua Ruangan</option>
                                        @foreach($ruangan as $item)
                                            <option value="{{ $item->kd_ruangan }}">{{ $item->ruangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="kd_kategori" class="form-label fw-semibold">Kategori Izin</label>
                                    <select class="form-select" name="kd_kategori" id="kd_kategori">
                                        <option value="">Semua Kategori</option>
                                        @foreach($kategoriIzin as $item)
                                            <option value="{{ $item->kd_kategori }}">{{ $item->kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="bulan" class="form-label fw-semibold">Bulan</label>
                                    <select class="form-select" name="bulan" id="bulan">
                                        <option value="">Semua Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="tahun" class="form-label fw-semibold">Tahun</label>
                                    <select class="form-select" name="tahun" id="tahun">
                                        <option value="">Semua Tahun</option>
                                        @for($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-file-down fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Generate PDF
                                </button>
                                <a href="{{ route('admin.laporan.surat-izin.index') }}" class="btn btn-light">
                                    <i class="ki-duotone ki-arrow-left fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Kembali
                                </a>
                            </div>
                        </form>
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
    // Set default bulan dan tahun ke sekarang
    $('#bulan').val('{{ date("n") }}');
    $('#tahun').val('{{ date("Y") }}');
});
</script>
@endpush
