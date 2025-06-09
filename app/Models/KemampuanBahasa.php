<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KemampuanBahasa extends Model
{
    use HasFactory;

    protected $table = 'hrd_r_bahasa';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';
    protected $guarded = [];

    // relasi ke tabel karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    // relasi ke tabel bahasa
    public function bahasa()
    {
        return $this->belongsTo(Bahasa::class, 'kd_bahasa', 'kd_bahasa');
    }

    // relasi ke tabel tingkat bahasa
    public function tingkatBahasa()
    {
        return $this->belongsTo(TingkatBahasa::class, 'kd_tingkat_bahasa', 'kd_tingkat_bahasa');
    }
}
