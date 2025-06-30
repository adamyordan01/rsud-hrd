{{-- _file.blade.php untuk Riwayat Kerja --}}
@if($riwayatKerja->sc_berkas)
    <div class="d-flex align-items-center">
        <i class="ki-duotone ki-file fs-2 text-primary me-2">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        <div class="d-flex flex-column">
            <a href="{{ route('admin.karyawan.riwayat-kerja.download', ['id' => $riwayatKerja->kd_karyawan, 'urut' => $riwayatKerja->urut_kerja]) }}" 
               target="_blank" 
               class="text-primary fw-bold text-hover-primary fs-7"
               title="Lihat berkas">
                Lihat Berkas
            </a>
            <span class="text-muted fs-8">{{ $riwayatKerja->sc_berkas }}</span>
        </div>
    </div>
@else
    <div class="d-flex align-items-center">
        <i class="ki-duotone ki-file-deleted fs-2 text-muted me-2">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        <span class="text-muted fs-7">Tidak ada berkas</span>
    </div>
@endif