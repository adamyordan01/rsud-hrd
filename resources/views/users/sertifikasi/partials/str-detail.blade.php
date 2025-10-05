@php
    \Carbon\Carbon::setLocale('id');
@endphp

<div class="str-detail-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail STR</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">No. STR:</td>
                                    <td>{{ $str->no_str ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Mulai:</td>
                                    <td>{{ $str->tgl_str ? \Carbon\Carbon::parse($str->tgl_str)->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Berakhir:</td>
                                    <td>{{ $str->tgl_kadaluarsa ? \Carbon\Carbon::parse($str->tgl_kadaluarsa)->translatedFormat('d F Y') : '-' }}</td>
                                </tr>
                                @if($str->tgl_kadaluarsa)
                                <tr>
                                    <td class="fw-bold">Tanggal Kadaluarsa:</td>
                                    <td>{{ \Carbon\Carbon::parse($str->tgl_kadaluarsa)->translatedFormat('d F Y') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Status:</td>
                                    <td>
                                        <span class="badge {{ $str->status['class'] }}">
                                            {{ $str->status['text'] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Masa Berlaku:</td>
                                    <td>{{ $str->masa_berlaku }}</td>
                                </tr>
                                @if($str->sc_berkas)
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
                    
                    @if($str->ket)
                    <div class="mt-4">
                        <h6 class="fw-bold">Keterangan:</h6>
                        <div class="bg-light p-3 rounded">
                            <div class="text-gray-800">{{ $str->ket }}</div>
                        </div>
                    </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>