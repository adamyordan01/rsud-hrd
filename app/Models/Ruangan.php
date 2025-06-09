<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'hrd_ruangan';
    protected $primaryKey = 'kd_ruangan';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_ruangan',
        'ruangan',
        'status_aktif',
        'kd_unit',
        'index_ruangan'
    ];
}
