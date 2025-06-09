@if ($str->status == 1)
    <div class="badge badge-light-warning">Pending</div>
@elseif ($str->status == 2)
    <div class="badge badge-light-success">Disetujui</div>
@elseif ($str->status == 3)
    <div class="badge badge-light-danger">Ditolak</div>
@endif