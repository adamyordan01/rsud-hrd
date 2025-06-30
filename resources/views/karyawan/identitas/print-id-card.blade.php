<!DOCTYPE html>
<html lang="id">

@php
    // $photoSmallUrl = '';
    // $baseUrlWrong = config('app.url') . '/storage'; // Misalnya: https://e-rsud.langsakota.go.id/storage
    // $baseUrlCorrect = config('app.url') . '/rsud_hrd/storage'; // Misalnya: https://e-rsud.langsakota.go.id/rsud_hrd/storage

    // if ($karyawan->foto_small) {
    //     $photoSmallUrl = str_replace(
    //         $baseUrlWrong,
    //         $baseUrlCorrect,
    //         Storage::url($karyawan->foto_small)
    //     );
    // } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
    //     $photoSmallUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    // } else {
    //     $photoSmallUrl = str_replace(
    //         $baseUrlWrong,
    //         $baseUrlCorrect,
    //         Storage::url($karyawan->foto)
    //     );
    // }

    $photoSmallUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto_small');
    $photoUrl = PhotoHelper::getPhotoUrl($karyawan, 'foto_square');

    $salt = env('QR_SALT', 'this-is-secret-of-rsud-langsa-salt-2025');
    // $hashedId = md5($karyawan->kd_karyawan . $salt);
    $hashedId = md5($karyawan->kd_karyawan);
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu ID RSUD Langsa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: white;
        }

        /* Kartu Pegawai */
        .id-card {
            height: 262px;
            width: 162px;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        /* Foto Pegawai (di belakang background) */
        .photo-section .profile-photo {
            position: absolute;
            top: 80px; /* Sesuaikan posisi slot background */
            left: 50%;
            transform: translateX(-50%);
            width: 58px;
            height: 77px;
            object-fit: cover;
            border-radius: 5px;
            z-index: 1; /* Di belakang background */
        }

        /* Background (menutupi sebagian foto) */
        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('assets/media/idcard/background.png') }}') no-repeat center/cover;
            z-index: 2; /* Di atas foto */
        }

        /* Konten (logo, nama, QR, dsb) */
        .content {
            position: relative;
            z-index: 3; /* Di atas background */
            padding: 10px 5px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        /* Header Rumah Sakit */
        .header {
            font-family: 'Poppins', sans-serif;
            font-size: 6px;
            font-weight: 600;
        }

        h2.company-name {
            font-size: 7.3px;
            font-weight: 600;
        }

        p.alamat {
            font-size: 4.4px;
            line-height: 1.3;
        }

        .logos {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 4px;
            margin-bottom: 6px;
        }

        .logos img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        /* Info */
        .info h3 {
            font-size: 8px;
            color: black;
            font-weight: 800;
            /* text-transform: uppercase; */
            font-family: 'Poppins', sans-serif;
            text-decoration: underline;
            margin-top: 95px;
        }

        .info p {
            font-size: 7px;
            color: black;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
        }

        /* QR Code */
        .qr-section svg {
            width: 45px;
            height: 45px;
            display: block;
            margin: 0 auto;
        }

        .qr-section p {
            font-size: 7px;
            color: #000;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
            margin-top: 2px;
        }

        /* PRINT MODE */
        @media print {
            body {
                background: none;
            }

            .id-card {
                box-shadow: none;
                transform: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="id-card">

        <!-- Foto Pegawai (Layer 1) -->
        <div class="photo-section">
            <img src="{{ $photoSmallUrl }}" alt="{{ $karyawan->nama }}" class="profile-photo">
        </div>

        <!-- Background di atas foto (Layer 2) -->
        <div class="background-overlay"></div>

        <!-- Konten di atas background (Layer 3) -->
        <div class="content">
            <div class="header">
                <div class="logos">
                    <img src="{{ asset('assets/media/idcard/logo1.png') }}" alt="Logo Kota Langsa">
                    <img src="{{ asset('assets/media/idcard/logo2.png') }}" alt="Logo RSUD Langsa">
                </div>
                <h2 class="company-name">RUMAH SAKIT UMUM DAERAH LANGSA</h2>
                <p class="alamat">Jln. Jend. Ahmad Yani No. 1 Kota Langsa</p>
                <p class="alamat">Telp. Office / Fax (0641) 22051 - Telp. IGD (0641) 22800</p>
                <p class="alamat">Email: rsudlangsa.aceh@gmail.com, rsud@langsakota.go.id</p>
                <p class="alamat">Website: rsud.langsakota.go.id</p>
            </div>

            <div class="info">
                <h3>{{ $nama_lengkap }}</h3>
                @if ($karyawan->kd_status_kerja == 1)
                    <p>NIP: {{ $karyawan->nip_baru }}</p>
                @elseif ($karyawan->kd_status_kerja == 7)
                    <p>NIPPPK: {{ $karyawan->nip_baru }}</p>
                @endif
            </div>

            <div class="qr-section">
                {{-- Route::get('/show-personal/{id}', [KaryawanController::class, 'showPersonal'])->name('show-personal'); --}}
                {!! QrCode::size(40)->generate(url('show-personal/' . $hashedId)); !!}
                <p>ID Peg. {{ $karyawan->kd_karyawan }}</p>
            </div>
        </div>

    </div>

</body>

</html>
