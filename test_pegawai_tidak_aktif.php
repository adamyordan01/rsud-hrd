<?php
// Test direct access untuk melihat apakah routing dan controller berfungsi
echo "Testing Pegawai Tidak Aktif Routes:\n\n";

$routes = [
    'Pegawai Pensiun' => 'http://localhost/rsud_hrd/public/admin/pegawai-pensiun',
    'Pegawai Keluar' => 'http://localhost/rsud_hrd/public/admin/pegawai-keluar', 
    'Pegawai Tugas Belajar' => 'http://localhost/rsud_hrd/public/admin/pegawai-tugas-belajar',
    'Pegawai Meninggal' => 'http://localhost/rsud_hrd/public/admin/pegawai-meninggal',
];

foreach ($routes as $name => $url) {
    echo "{$name}: {$url}\n";
}

echo "\nTest kd_status_kerja mapping:\n";
echo "PNS = 1\n";
echo "Honor = 2\n";
echo "Kontrak BLUD = 3 (dengan kd_jenis_peg = 2)\n";
echo "Part Time = 4\n";
echo "THL = 6\n";
echo "PPPK = 7\n";
echo "Kontrak PEMKO = 3 (dengan kd_jenis_peg != 2)\n";
?>
