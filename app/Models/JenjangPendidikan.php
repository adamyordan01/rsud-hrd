<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenjangPendidikan extends Model
{
    use HasFactory;

    protected $table = 'hrd_jenjang_pendidikan';
    protected $primaryKey = 'kd_jenjang_didik';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_jenjang_didik',
        'jenjang_didik',
        'urutan',
        'nilaiIndex',
    ];
}
