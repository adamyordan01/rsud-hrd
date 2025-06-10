<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTenagaDetail extends Model
{
    use HasFactory;

    protected $table = 'hrd_jenis_tenaga_detail';
    protected $primaryKey = 'kd_detail';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_detail',
        'kd_jenis_tenaga',
        'detail_jenis_tenaga',
    ];

    public function jenisTenaga()
    {
        return $this->belongsTo(JenisTenaga::class, 'kd_jenis_tenaga', 'kd_jenis_tenaga');
    }

    public function subDetails()
    {
        return $this->hasMany(JenisTenagaSubDetail::class, 'kd_detail', 'kd_detail')
                    ->where('kd_jenis_tenaga', function($query) {
                        $query->select('kd_jenis_tenaga')
                              ->from('hrd_jenis_tenaga_detail')
                              ->whereColumn('hrd_jenis_tenaga_detail.kd_detail', 'hrd_jenis_tenaga_sub_detail.kd_detail')
                              ->limit(1);
                    });
    }

    // public function subDetails()
    // {
    //     return $this->hasMany(JenisTenagaSubDetail::class, 'kd_detail', 'kd_detail')
    //         ->where('kd_jenis_tenaga', $this->kd_jenis_tenaga);
    // }
}
