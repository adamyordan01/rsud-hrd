<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    use HasFactory;

    protected $table = 'hrd_jenis_surat';
    protected $primaryKey = 'kd_jenis_surat';

    protected $fillable = [
        'jenis_surat',
    ];

    public function kategoriIzin()
    {
        return $this->hasMany(KategoriIzin::class, 'kd_jenis_surat', 'kd_jenis_surat');
    }

    public function suratIzin()
    {
        return $this->hasMany(SuratIzin::class, 'kd_jenis_surat', 'kd_jenis_surat');
    }
}
