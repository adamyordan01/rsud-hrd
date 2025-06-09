<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubunganKeluarga extends Model
{
    use HasFactory;

    protected $table = 'hrd_hub_keluarga';
    protected $primaryKey = 'kd_hub_klrg';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_hub_klrg',
        'hub_klrg',
    ];
}
