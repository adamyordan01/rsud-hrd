<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    // protected $table = 'hrd_karyawan';
    // protected $primaryKey = 'kd_karyawan';
    // // primary key auto increment tidak ada
    // public $incrementing = false;
    // // primary key berupa string
    // protected $keyType = 'string';
    // // created_at dan updated_at tidak ada
    // public $timestamps = false;

    // protected $connection = 'sqlsrv_hrd';
    protected $table = 'view_tampil_karyawan';
    protected $primaryKey = 'kd_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    // field yang tidak boleh dilihat ketika diambil response
    protected $hidden = ['password', 'remember_token', 'rek_bni_syariah'];

    public function user()
    {
        return $this->hasOne(User::class, 'kd_karyawan', 'kd_karyawan');
    }
}
