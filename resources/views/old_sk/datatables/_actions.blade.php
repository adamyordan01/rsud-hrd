@php
    $filePathTte = $item->path_dokumen ?? '';
    
    // Generate URL untuk akses dokumen SK melalui route baru
    if ($filePathTte) {
        // Extract year and filename from path_dokumen
        // Expected format: sk-documents/2025/SK_Pegawai_Kontrak_TTE_xxxxx.pdf
        $pathParts = explode('/', $filePathTte);
        if (count($pathParts) >= 3) {
            $year = $pathParts[1]; // sk-documents/2025/filename.pdf
            $filename = $pathParts[2];
            $urlFilePathtte = route('sk.document.show', ['year' => $year, 'filename' => $filename]);
        } else {
            // Fallback untuk format lama
            $urlFilePathtte = url(str_replace('public', 'storage', $filePathTte));
        }
    } else {
        $urlFilePathtte = '';
    }
@endphp

@if($item->verif_1 == 0)
    @if($jabatan == 19 || $ruangan == 57)
        <a href="javascript:void(0)" 
           class="btn btn-info btn-sm d-block mb-2" 
           title="Verifikasi Kasubbag. Kepegawaian" 
           data-urut="{{ $item->urut }}" 
           data-tahun="{{ $item->tahun_sk }}" 
           data-kode="verif1" 
           data-url="{{ route('admin.sk-kontrak.first-verification') }}" 
           data-bs-toggle="modal" 
           data-bs-target="#kt_modal_verif" 
           id="verif1">
            <i class="ki-duotone ki-double-check fs-2"><span class="path1"></span><span class="path2"></span></i> 
            Verifikasi Ka.Sub.Bag. Kepeg.
        </a>
    @endif
@elseif($item->verif_2 == 0)
    @if($jabatan == 7 || $ruangan == 57)
        <a href="javascript:void(0)" 
           class="btn btn-primary btn-sm d-block mb-2" 
           title="Verifikasi Kabag. TU" 
           data-urut="{{ $item->urut }}" 
           data-tahun="{{ $item->tahun_sk }}" 
           data-kode="verif2" 
           data-url="{{ route('admin.sk-kontrak.second-verification') }}" 
           data-bs-toggle="modal" 
           data-bs-target="#kt_modal_verif" 
           id="verif2">
            <i class="ki-duotone ki-double-check fs-2"><span class="path1"></span><span class="path2"></span></i> 
            Verifikasi Ka.Bag. Tata Usaha
        </a>
    @endif
@elseif($item->verif_4 == 0)
    @if($jabatan == 1 || $ruangan == 57)
        <a href="javascript:void(0)" 
           class="btn btn-warning btn-sm d-block mb-2" 
           title="Verifikasi Direktur" 
           data-urut="{{ $item->urut }}" 
           data-tahun="{{ $item->tahun_sk }}" 
           data-kode="verif4" 
           data-url="{{ route('admin.sk-kontrak.fourth-verification') }}" 
           id="verif4">
            <i class="ki-duotone ki-double-check fs-2"><span class="path1"></span><span class="path2"></span></i> 
            Verifikasi Direktur
        </a>
    @endif
@else
    @if($item->nomor_konsederan == "")
        @if($filePathTte)
            <a href="{{ $urlFilePathtte }}" 
               class="btn btn-danger btn-sm d-block mb-2" 
               title="SK" 
               target="_blank">
                <i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> 
                Cetak SK
            </a>
        @else
            <span class="btn btn-secondary btn-sm d-block mb-2" title="Dokumen SK belum tersedia">
                <i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> 
                SK Tidak Tersedia
            </span>
        @endif
    @else
        @if($filePathTte)
            <a href="{{ $urlFilePathtte }}" 
               class="btn btn-danger btn-sm d-block mb-2" 
               title="SK" 
               target="_blank">
                <i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> 
                Cetak SK
            </a>
        @else
            <span class="btn btn-secondary btn-sm d-block mb-2" title="Dokumen SK belum tersedia">
                <i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> 
                SK Tidak Tersedia
            </span>
        @endif
        <a href="module/sk/print_konsederan.php?data={{ $item->urut }}&thn={{ $item->tahun_sk }}" 
           class="btn btn-success btn-sm d-block mb-2" 
           title="Konsederan" 
           target="_blank">
            <i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> 
            Konsederan
        </a>
        <a href="module/sk/print_serahterima.php?data={{ $item->urut }}&thn={{ $item->tahun_sk }}" 
           class="btn btn-info btn-sm d-block mb-2" 
           title="Lembar Serah Terima" 
           target="_blank">
            <i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> 
            Serah Terima
        </a>
    @endif
@endif

<a href="{{ route('admin.sk-kontrak.print-perjanjian-kerja', ['urut' => $item->urut, 'tahun' => $item->tahun_sk]) }}" 
   class="btn btn-warning btn-sm d-block mb-2" 
   title="Perjanjian Kerja" 
   target="_blank">
    <i class="ki-duotone ki-printer fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> 
    Perjanjian Kerja
</a>