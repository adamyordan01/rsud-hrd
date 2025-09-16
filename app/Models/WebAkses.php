<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WebAkses extends Model
{
    protected $table = 'web_akses';
    protected $primaryKey = 'kd_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_karyawan',
        'kd_akses'
    ];

    /**
     * Relasi dengan Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kd_karyawan', 'kd_karyawan');
    }

    /**
     * Relasi dengan WebAplikasi
     */
    public function webAplikasi()
    {
        return $this->belongsTo(WebAplikasi::class, 'kd_akses', 'kd_akses');
    }

    /**
     * Get user access with employee details
     */
    public static function getUsersWithAccess()
    {
        return self::with(['karyawan', 'webAplikasi'])
            ->join('view_tampil_karyawan', 'web_akses.kd_karyawan', '=', 'view_tampil_karyawan.kd_karyawan')
            ->select(
                'web_akses.*', 
                DB::raw("CONCAT(COALESCE(view_tampil_karyawan.gelar_depan, ''), ' ', view_tampil_karyawan.nama, COALESCE(CONCAT(' ', view_tampil_karyawan.gelar_belakang), '')) as nama_lengkap"),
                'view_tampil_karyawan.nip_baru as nip'
            )
            ->get();
    }

    /**
     * Check if employee has access
     */
    public static function hasAccess($kdKaryawan, $kdAkses = null)
    {
        $query = self::where('kd_karyawan', $kdKaryawan);
        
        if ($kdAkses) {
            $query->where('kd_akses', $kdAkses);
        }
        
        return $query->exists();
    }
}
