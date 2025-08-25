<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';
    protected $primaryKey = 'kd_kabupaten';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_kabupaten',
        'kd_propinsi',
        'kabupaten',
    ];

    public function propinsi()
    {
        return $this->belongsTo(Propinsi::class, 'kd_propinsi', 'kd_propinsi');
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'kd_kabupaten', 'kd_kabupaten');
    }
}
