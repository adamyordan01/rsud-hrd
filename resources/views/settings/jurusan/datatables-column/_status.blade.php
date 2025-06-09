@if ($row->status_aktif == 1)
    <div class="badge badge-light-success">Aktif</div>
@elseif ($row->status_aktif == 0)
    <div class="badge badge-light-dark">Tidak Aktif</div>
@endif