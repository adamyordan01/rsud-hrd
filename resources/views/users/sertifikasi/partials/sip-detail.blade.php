@php
    \Carbon\Carbon::setLocale('id');
@endphp

<div class="sip-detail-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail SIP</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">No. SIP:</td>
                                    <td>{{ $sip->no_sip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Mulai:</td>
                                    <td>{{ $sip->tgl_sip ? \Carbon\Carbon::parse($sip->tgl_sip)->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Berakhir:</td>
                                    <td>{{ $sip->tgl_kadaluarsa ? \Carbon\Carbon::parse($sip->tgl_kadaluarsa)->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                                @if($sip->tgl_kadaluarsa)
                                <tr>
                                    <td class="fw-bold">Tanggal Kadaluarsa:</td>
                                    <td>{{ \Carbon\Carbon::parse($sip->tgl_kadaluarsa)->translatedFormat('d F Y') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Status:</td>
                                    <td>
                                        <span class="badge {{ $sip->status['class'] }}">
                                            {{ $sip->status['text'] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Masa Berlaku:</td>
                                    <td>{{ $sip->masa_berlaku }}</td>
                                </tr>
                                @if($sip->sc_berkas)
                                <tr>
                                    <td class="fw-bold">File:</td>
                                    <td>
                                        <span class="badge badge-light-success">Tersedia</span>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($sip->ket)
                    <div class="mt-4">
                        <h6 class="fw-bold">Keterangan:</h6>
                        <div class="bg-light p-3 rounded">
                            <div class="text-gray-800">{{ $sip->ket }}</div>
                        </div>
                    </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>