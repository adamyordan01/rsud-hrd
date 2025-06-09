@if ($row->status == 1)
    <div class="badge badge-light-warning">Aktif</div>
@elseif ($row->status == 0)
    <div class="badge badge-light-dark">Tidak Aktif</div>
@endif