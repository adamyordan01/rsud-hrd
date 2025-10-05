@php
    \Carbon\Carbon::setLocale('id');
@endphp

<div class="sk-detail-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail SK Kontrak</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Nomor SK:</td>
                                    <td>{{ $sk->no_sk }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal SK:</td>
                                    <td>{{ \Carbon\Carbon::parse($sk->tgl_sk)->translatedFormat('d F Y') }}</td>
                                </tr>
                                @if($sk->tahun_sk)
                                <tr>
                                    <td class="fw-bold">Tahun SK:</td>
                                    <td>{{ $sk->tahun_sk }}</td>
                                </tr>
                                @endif
                                @if($sk->no_per_kerja)
                                <tr>
                                    <td class="fw-bold">No. Per Kerja:</td>
                                    <td>{{ $sk->no_per_kerja }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($sk->tgl_ttd)
                                <tr>
                                    <td class="fw-bold" width="40%">Tanggal TTD:</td>
                                    <td>{{ \Carbon\Carbon::parse($sk->tgl_ttd)->translatedFormat('d F Y') }}</td>
                                </tr>
                                @endif
                                @if($sk->nomor_konsederan)
                                <tr>
                                    <td class="fw-bold">Nomor Konsederan:</td>
                                    <td>{{ $sk->nomor_konsederan }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <span class="badge badge-light-{{ $sk->stt > 0 ? 'success' : 'warning' }}">
                                            {{ $sk->stt > 0 ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">ID Dokumen:</td>
                                    <td>{{ $sk->id_dokumen ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($sk->log)
                    <div class="mt-4">
                        <h6 class="fw-bold">Log Informasi:</h6>
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted">{{ $sk->log }}</small>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <h6 class="fw-bold">Status Verifikasi:</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-{{ $sk->verif_1 ? 'success' : 'secondary' }} me-2">
                                        {{ $sk->verif_1 ? '✓' : '✗' }}
                                    </span>
                                    <span class="text-muted">Verifikasi 1</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-{{ $sk->verif_2 ? 'success' : 'secondary' }} me-2">
                                        {{ $sk->verif_2 ? '✓' : '✗' }}
                                    </span>
                                    <span class="text-muted">Verifikasi 2</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-{{ $sk->verif_3 ? 'success' : 'secondary' }} me-2">
                                        {{ $sk->verif_3 ? '✓' : '✗' }}
                                    </span>
                                    <span class="text-muted">Verifikasi 3</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-{{ $sk->verif_4 ? 'success' : 'secondary' }} me-2">
                                        {{ $sk->verif_4 ? '✓' : '✗' }}
                                    </span>
                                    <span class="text-muted">Verifikasi 4</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>