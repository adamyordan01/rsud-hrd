<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'hrd_r_keluarga';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';
    protected $guarded = [];

    // cast tgl_lahir to date
    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    public function hubungan()
    {
        return $this->belongsTo(HubunganKeluarga::class, 'kd_hub_klrg', 'kd_hub_klrg');
    }

    public function pendidikan()
    {
        return $this->belongsTo(JenjangPendidikan::class, 'kd_jenjang_didik', 'kd_jenjang_didik');
    }

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'kd_pekerjaan', 'kd_pekerjaan');
    }
}
