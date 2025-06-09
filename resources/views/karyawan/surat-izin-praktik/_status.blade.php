@if ($sip->status == 1)
    <div class="badge badge-light-warning">Pending</div>
@elseif ($sip->status == 2)
    <div class="badge badge-light-success">Disetujui</div>
@elseif ($sip->status == 3)
    <div class="badge badge-light-danger">Ditolak</div>
@endif