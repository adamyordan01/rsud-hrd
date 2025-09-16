<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebAplikasi extends Model
{
    protected $table = 'web_aplikasi';
    protected $primaryKey = 'kd_akses';
    public $timestamps = false;

    protected $fillable = [
        'kd_akses',
        'akses'
    ];

    /**
     * Relasi dengan WebAkses
     */
    public function webAkses()
    {
        return $this->hasMany(WebAkses::class, 'kd_akses', 'kd_akses');
    }

    /**
     * Get access levels for dropdown
     */
    public static function getAccessLevels()
    {
        return self::orderBy('kd_akses')->get();
    }
}
