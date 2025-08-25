<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propinsi extends Model
{
    protected $table = 'propinsi';
    protected $primaryKey = 'kd_propinsi';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_propinsi',
        'propinsi',
    ];

    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class, 'kd_propinsi', 'kd_propinsi');
    }
}
