<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKerja extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'hrd_r_kerja';
    protected $primaryKey = ['kd_karyawan', 'urut_kerja'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_karyawan',
        'urut_kerja',
        'pejabat',
        'tgl_sk',
        'no_sk',
        'tmt',
        'perusahaan',
        'ket',
        'sc_berkas',
    ];

    protected $casts = [
        'tgl_sk' => 'date',
        'tmt' => 'date',
    ];

    /**
     * Relasi ke model Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    /**
     * Scope untuk filter berdasarkan karyawan
     */
    public function scopeByKaryawan($query, $kdKaryawan)
    {
        return $query->where('kd_karyawan', $kdKaryawan);
    }

    /**
     * Scope untuk ordering berdasarkan TMT terbaru
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('tmt', 'desc')->orderBy('tgl_sk', 'desc');
    }

    /**
     * Accessor untuk format tanggal SK
     */
    public function getFormattedTglSkAttribute()
    {
        return $this->tgl_sk ? $this->tgl_sk->format('d M Y') : '-';
    }

    /**
     * Accessor untuk format TMT
     */
    public function getFormattedTmtAttribute()
    {
        return $this->tmt ? $this->tmt->format('d M Y') : '-';
    }

    /**
     * Accessor untuk check apakah ada berkas
     */
    public function getHasBerkasAttribute()
    {
        return !empty($this->sc_berkas);
    }

    /**
     * Accessor untuk URL download berkas
     */
    public function getDownloadUrlAttribute()
    {
        if (!$this->sc_berkas) {
            return null;
        }

        return route('admin.karyawan.riwayat-kerja.download', [
            'id' => $this->kd_karyawan,
            'urut' => $this->urut_kerja
        ]);
    }
}