<?php

namespace App\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;

class EmployeeHeader extends Component
{
    public $karyawan;
    public $namaLengkap;
    public $alamat;
    public $photoSmallUrl;
    public $photoUrl;
    public $hashedId;
    public $missingFields;
    public $persentaseKelengkapan;

    public function __construct($karyawan, $missingFields = [], $persentaseKelengkapan = 0)
    {
        $this->karyawan = $karyawan;
        $this->missingFields = $missingFields;
        $this->persentaseKelengkapan = $persentaseKelengkapan;
        $this->prepareData();
    }

    public function prepareData()
    {
        $karyawan = $this->karyawan;

        // Nama lengkap
        $gelarDepan = $karyawan->gelar_depan ? $karyawan->gelar_depan . " " : "";
        $gelarBelakang = $karyawan->gelar_belakang ? $karyawan->gelar_belakang : "";
        $this->namaLengkap = $gelarDepan . $karyawan->nama . $gelarBelakang;

        // Alamat
        $this->alamat = $karyawan->alamat . ", Kel. " . $karyawan->kelurahan . ", Kec. " . $karyawan->kecamatan . ", Kab./Kota " . $karyawan->kabupaten . ", Prov. " . $karyawan->propinsi;

        // Foto kecil
        $this->photoSmallUrl = '';
        if ($karyawan->foto_small) {
            $this->photoSmallUrl = url(str_replace('public', 'storage', $karyawan->foto_small));
        } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
            $this->photoSmallUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
        } else {
            $this->photoSmallUrl = url(str_replace('public', 'storage', $karyawan->foto));
        }

        // Foto square
        $this->photoUrl = '';
        if ($karyawan->foto_square) {
            $this->photoUrl = url(str_replace('public', 'storage', $karyawan->foto_square));
        } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
            $this->photoUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
        }

        // Hashed ID
        $this->hashedId = md5($karyawan->kd_karyawan);
    }

    public function render()
    {
        return view('components.employee-header');
    }
}
