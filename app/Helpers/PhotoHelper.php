<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoHelper
{
    /**
     * Mendapatkan URL foto karyawan
     */
    public static function getPhotoUrl($karyawan, $type)
    {
        if (!$karyawan->{$type}) {
            return self::getDefaultPhotoUrl($type);
        }
        
        // Cek apakah foto menggunakan sistem lama
        if (Str::startsWith($karyawan->{$type}, 'rsud_') || $karyawan->{$type} === 'user.png') {
            return 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->{$type};
        }
        
        // Cek apakah foto ada di disk hrd_files
        if (Storage::disk('hrd_files')->exists($karyawan->{$type})) {
            return route('photo.show', [
                'type' => $type,
                'id' => $karyawan->kd_karyawan,
                'filename' => basename($karyawan->{$type})
            ]);
        }
        
        // Fallback ke storage public jika ada
        if (Storage::disk('public')->exists($karyawan->{$type})) {
            return Storage::url($karyawan->{$type});
        }
        
        return self::getDefaultPhotoUrl($type);
    }
    
    /**
     * Mendapatkan URL foto default
     */
    private static function getDefaultPhotoUrl($type)
    {
        switch($type) {
            case 'foto_square':
                return asset('assets/media/avatars/blank.png');
            case 'foto':
                return asset('assets/media/avatars/blank-cv.png');
            case 'foto_small':
                return asset('assets/media/avatars/blank-id.png');
            default:
                return asset('assets/media/avatars/blank.png');
        }
    }
    
    /**
     * Cek apakah karyawan memiliki foto
     */
    public static function hasPhoto($karyawan, $type)
    {
        if (!$karyawan->{$type}) {
            return false;
        }
        
        // Cek sistem lama
        if (Str::startsWith($karyawan->{$type}, 'rsud_') || $karyawan->{$type} === 'user.png') {
            return true;
        }
        
        // Cek disk hrd_files
        if (Storage::disk('hrd_files')->exists($karyawan->{$type})) {
            return true;
        }
        
        // Cek disk public
        if (Storage::disk('public')->exists($karyawan->{$type})) {
            return true;
        }
        
        return false;
    }
}