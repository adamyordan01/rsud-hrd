<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewTampilKaryawan extends Model
{
    use HasFactory;

    protected $table = 'view_tampil_karyawan';
    protected $primaryKey = 'kd_karyawan';
    public $timestamps = false;

    protected $guarded = ['*'];

    public function suratIzin()
    {
        return $this->hasMany(SuratIzin::class, 'kd_karyawan', 'kd_karyawan');
    }

    public function atasan()
    {
        return $this->belongsTo(ViewTampilKaryawan::class, 'penilai', 'kd_karyawan');
    }

    public function getNamaLengkapAttribute()
    {
        // return trim($this->gelar_depan ?? '' . ' ' .
        //         $this->nama . ' ' .
        //         ($this->gelar_belakang ?? ''));
        return trim(($this->gelar_depan ?? '') . ' ' .
            $this->nama . ' ' .
            ($this->gelar_belakang ?? ''));
    }

    // Accessor untuk pangkat dan golongan
    public function getPangkatGolonganAttribute()
    {
        if ($this->pangkat && $this->kd_gol_sekarang) {
            return $this->pangkat . ' / ' . $this->kd_gol_sekarang;
        }

        return $this->pangkat ?? '-';
    }

    // Accessor untuk jabatan lengkap
    public function getJabatanLengkapAttribute()
    {
        return trim(($this->jab_struk ?? '') . ' ' .
            ($this->ruangan ?? ''));
    }
}
