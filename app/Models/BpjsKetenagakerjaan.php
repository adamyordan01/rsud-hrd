<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsKetenagakerjaan extends Model
{
    use HasFactory;

    protected $table = 'hrd_r_bpjs_ketenagakerjaan';

    // protected $primaryKey = ['kd_karyawan', 'no_kartu', 'urut'];
    public $incrementing = false;
    public $keyType = 'string';
    public $timestamps = false;
    protected $connection = 'sqlsrv';
    protected $guarded = [];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
