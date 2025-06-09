<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TingkatBahasa extends Model
{
    use HasFactory;

    protected $table = 'hrd_tingkat_bahasa';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';
    protected $guarded = [];
}
