<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'hrd_karyawan';
    protected $primaryKey = 'kd_karyawan';
    // primary key auto increment tidak ada
    public $incrementing = false;
    // primary key berupa string
    protected $keyType = 'string';
    // created_at dan updated_at tidak ada
    public $timestamps = false;
}
