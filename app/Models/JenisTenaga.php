<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTenaga extends Model
{
    use HasFactory;

    protected $table = 'hrd_jenis_tenaga';
    protected $primaryKey = 'kd_jenis_tenaga';
    public $incrementing = false;
    public $timestamps = false;
    protected $connection = 'sqlsrv';

    protected $fillable = [
        'kd_jenis_tenaga',
        'jenis_tenaga',
    ];

    public function details()
    {
        return $this->hasMany(JenisTenagaDetail::class, 'kd_jenis_tenaga', 'kd_jenis_tenaga');
    }

    public function subDetails()
    {
        return $this->hasManyThrough(
            JenisTenagaSubDetail::class,
            JenisTenagaDetail::class,
            'kd_jenis_tenaga', // Foreign key on JenisTenagaDetail
            'kd_jenis_tenaga', // Foreign key on JenisTenagaSubDetail
            'kd_jenis_tenaga', // Local key on JenisTenaga
            'kd_jenis_tenaga' // Local key on JenisTenagaDetail
        );
    }

    // Accessor untuk mendapatkan total sub detail count
    public function getSubDetailCountAttribute()
    {
        return $this->subDetails()->count();
    }

    // Scope untuk filter active
    public function scopeWithActiveCounts($query)
    {
        return $query->withCount([
            'details',
            'subDetails',
            'subDetails as active_sub_details_count' => function ($query) {
                $query->where('status', '1');
            }
        ]);
    }
}
