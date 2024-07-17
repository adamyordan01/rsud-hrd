<?php

if (!function_exists('tanggal_indo')) {
    function tanggal_indo($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Pastikan format tanggal sesuai dengan 'Y-m-d'
        if (isset($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $pecahkan = explode('-', $tanggal);
            if (count($pecahkan) == 3) {
                return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
            }
        }

        // Jika format tidak sesuai, kembalikan string kosong atau pesan kesalahan
        return '-';
    }
}

if (!function_exists('formatNIP')) {
    function formatNIP($nip) {
        return substr($nip, 0, 8) . ' ' .
               substr($nip, 8, 6) . ' ' .
               substr($nip, 14, 1) . ' ' .
               substr($nip, 15, 3);
    }
}

if (!function_exists('makeInt')) {
    function makeIntHelper($res)
    {
        return (int) $res;
    }
}

if (!function_exists('convertToHijriah')) {
    function convertToHijriah($tanggal)
    {
        $array_bulan = [
            "Muharram", "Safar", "Rabi'ul Awwal", "Rabi'ul Akhir", "Jumadil Awal", "Jumadil Akhir",
            "Rajab", "Sya'ban", "Ramadhan", "Syawal", "Dzulqo'dah", "Dzulhijjah"
        ];

        $date = makeIntHelper(substr($tanggal, 8, 2));
        $month = makeIntHelper(substr($tanggal, 5, 2));
        $year = makeIntHelper(substr($tanggal, 0, 4));

        if (($year > 1582) || (($year == 1582) && ($month > 10)) || (($year == 1582) && ($month == 10) && ($date > 14))) {
            $jd = makeIntHelper((1461 * ($year + 4800 + makeIntHelper(($month - 14) / 12))) / 4) +
                makeIntHelper((367 * ($month - 2 - 12 * (makeIntHelper(($month - 14) / 12)))) / 12) -
                makeIntHelper((3 * (makeIntHelper(($year + 4900 + makeIntHelper(($month - 14) / 12)) / 100))) / 4) +
                $date - 32075;
        } else {
            $jd = 367 * $year - makeIntHelper((7 * ($year + 5001 + makeIntHelper(($month - 9) / 7))) / 4) +
                makeIntHelper((275 * $month) / 9) + $date + 1729777;
        }

        $wd = $jd % 7;
        $l = $jd - 1948440 + 10632;
        $n = makeIntHelper(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $z = (makeIntHelper((10985 - $l) / 5316)) * (makeIntHelper((50 * $l) / 17719)) + (makeIntHelper($l / 5670)) * (makeIntHelper((43 * $l) / 15238));
        $l = $l - (makeIntHelper((30 - $z) / 15)) * (makeIntHelper((17719 * $z) / 50)) - (makeIntHelper($z / 16)) * (makeIntHelper((15238 * $z) / 43)) + 29;
        $m = makeIntHelper((24 * $l) / 709);
        $d = $l - makeIntHelper((709 * $m) / 24);
        $y = 30 * $n + $z - 30;
        $g = $m - 1;

        if ($g >= 0 && $g < count($array_bulan)) {
            return sprintf('%02s', $d + 1) . " " . $array_bulan[$g] . " " . $y . " H";
        }

        return '-';
    }
}