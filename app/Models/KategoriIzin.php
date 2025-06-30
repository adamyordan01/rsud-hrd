<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriIzin extends Model
{
    use HasFactory;

    protected $table = 'hrd_kategori_izin';
    protected $primaryKey = 'kd_kategori';

    protected $fillable = [
        'ketegori',
        'kd_jenis_surat',
    ];

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class, 'kd_jenis_surat', 'kd_jenis_surat');
    }

    public function suratIzin()
    {
        return $this->hasMany(SuratIzin::class, 'kd_kategori', 'kd_kategori');
    }
}
