<div class="user-info">
    <div class="user-avatar">
        {{ substr(trim($row->nama_lengkap), 0, 1) }}
    </div>
    <div class="user-details">
        <h6>{{ trim($row->nama_lengkap) }}</h6>
        <small class="text-muted">ID: {{ $row->kd_karyawan }} | NIP: {{ $row->nip }}</small>
    </div>
</div>
