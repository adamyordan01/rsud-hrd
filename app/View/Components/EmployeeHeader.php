<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Helpers\PhotoHelper;

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
        $gelarBelakang = $karyawan->gelar_belakang ? " " . $karyawan->gelar_belakang : "";
        $this->namaLengkap = $gelarDepan . $karyawan->nama . $gelarBelakang;

        // Alamat
        $this->alamat = $karyawan->alamat . ", Kel. " . $karyawan->kelurahan . ", Kec. " . $karyawan->kecamatan . ", Kab./Kota " . $karyawan->kabupaten . ", Prov. " . $karyawan->propinsi;

        // Menggunakan PhotoHelper untuk mendapatkan URL foto
        $this->photoSmallUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto_small');
        $this->photoUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto_square');

        // Hashed ID
        $this->hashedId = md5($karyawan->kd_karyawan);
    }

    public function render()
    {
        return view('components.employee-header');
    }
}