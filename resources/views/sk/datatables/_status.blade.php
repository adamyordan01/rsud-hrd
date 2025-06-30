@if($item->verif_1 == 0)
    Menunggu verifikasi Kasubbag. Kepegawaian
@elseif($item->verif_2 == 0)
    Menunggu verifikasi Kabag. TU
@elseif($item->verif_4 == 0)
    Menunggu verifikasi Direktur
@else
    Telah diverifikasi
@endif