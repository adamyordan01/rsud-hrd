<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTandaRegistrasi extends Model
{
    use HasFactory;

    protected $table = 'hrd_r_str';
    // composite primary key
    protected $primaryKey = ['kd_karyawan', 'urut_str'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $connection = 'sqlsrv';
    protected $guarded = [];

    protected $casts = [
        'tgl_str' => 'date',
        'tgl_kadaluarsa' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
