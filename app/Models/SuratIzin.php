<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratIzin extends Model
{
    use HasFactory;

    protected $table = 'hrd_surat_izin';
    protected $primaryKey = 'kd_surat';
    public $timestamps = false;

    protected $fillable = [
        'kd_surat',
        'kd_karyawan',
        'kd_jenis_surat',
        'kd_kategori',
        'tgl_mulai',
        'tgl_akhir',
        'alasan',
    ];

    protected $dates = [
        'tgl_mulai',
        'tgl_akhir',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class, 'kd_jenis_surat', 'kd_jenis_surat');
    }

    public function kategoriIzin()
    {
        return $this->belongsTo(KategoriIzin::class, 'kd_kategori', 'kd_kategori');
    }

    // Accessor untuk format tanggal
    public function getTglMulaiFormattedAttribute()
    {
        return Carbon::parse($this->tgl_mulai)->format('d-m-Y');
    }

    public function getTglAkhirFormattedAttribute()
    {
        return Carbon::parse($this->tgl_akhir)->format('d-m-Y');
    }

    // Scopes untuk filter berdasarkan karyawan
    public function scopeByKaryawan($query, $kd_karyawan)
    {
        return $query->where('kd_karyawan', $kd_karyawan);
    }


}
