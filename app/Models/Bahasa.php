<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahasa extends Model
{
    use HasFactory;

    protected $table = 'hrd_bahasa';
    protected $primaryKey = 'kd_bahasa';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_bahasa',
        'bahasa',
    ];
}
