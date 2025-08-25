<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    protected $primaryKey = 'kd_kecamatan';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_kecamatan',
        'kd_kabupaten',
        'kecamatan',
    ];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kd_kabupaten', 'kd_kabupaten');
    }

    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class, 'kd_kecamatan', 'kd_kecamatan');
    }
}
