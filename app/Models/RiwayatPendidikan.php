<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    protected $table = 'hrd_r_pendidikan';
    protected $primaryKey = ['kd_karyawan', 'urut_didik'];
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_karyawan',
        'urut_didik',
        'kd_jenjang_didik',
        'kd_jurusan',
        'nama_lembaga',
        'tahun_lulus',
        'no_ijazah',
        'tempat',
    ];

    // Relasi ke JenjangPendidikan
    public function jenjangPendidikan()
    {
        return $this->belongsTo(JenjangPendidikan::class, 'kd_jenjang_didik', 'kd_jenjang_didik');
    }

    // Relasi ke Jurusan
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'kd_jurusan', 'kd_jurusan');
    }

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }
}
