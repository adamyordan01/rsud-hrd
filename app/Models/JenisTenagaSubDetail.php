<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTenagaSubDetail extends Model
{
    use HasFactory;

    protected $table = 'hrd_jenis_tenaga_sub_detail';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_jenis_tenaga',
        'kd_detail',
        'kd_sub_detail',
        'sub_detail',
        'kd_sdmk',
        'kelompok_spesialis',
        'status',
    ];

    public function jenisTenaga()
    {
        return $this->belongsTo(JenisTenaga::class, 'kd_jenis_tenaga', 'kd_jenis_tenaga');
    }

    // public function detail()
    // {
    //     return $this->belongsTo(JenisTenagaDetail::class, 'kd_detail', 'kd_detail')
    //         ->where('kd_jenis_tenaga', $this->kd_jenis_tenaga);
    // }
    public function detail()
    {
        return $this->belongsTo(JenisTenagaDetail::class, 'kd_detail', 'kd_detail');
    }

    public function getRouteKeyName()
    {
        return 'kd_sub_detail';   
    }

    // ✅ TAMBAHAN: Scope untuk filter berdasarkan jenis tenaga dan detail
    public function scopeForDetail($query, $kdJenisTenaga, $kdDetail)
    {
        return $query->where('kd_jenis_tenaga', $kdJenisTenaga)
                     ->where('kd_detail', $kdDetail);
    }

}
