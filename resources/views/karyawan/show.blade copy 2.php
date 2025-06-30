@extends('layouts.backend', ['title' => 'Riwayat'])

@inject('carbon', 'Carbon\Carbon')

@php
    $kd_status_kerja = $karyawan->kd_status_kerja;

    $gelar_depan = $karyawan->gelar_depan ? $karyawan->gelar_depan . " " : "";
    $gelar_belakang = $karyawan->gelar_belakang ? $karyawan->gelar_belakang : "";
    $nama = $karyawan->nama;
    $nama_lengkap = $gelar_depan . $nama . $gelar_belakang;

    // <?php echo $isi['ALAMAT'].", Kel. ".$isi['KELURAHAN'].", <br>Kec. ".$isi['KECAMATAN'].", Kab./Kota ".$isi['KABUPATEN'].", Prov. ".$isi['PROPINSI'];

    $alamat = $karyawan->alamat . ", Kel. " . $karyawan->kelurahan . ", Kec. " . $karyawan->kecamatan . ", Kab./Kota " . $karyawan->kabupaten . ", Prov. " . $karyawan->propinsi;

    // $photoSmallUrl = '';
    // if ($karyawan->foto_small) {
    //     $photoSmallUrl = Storage::url($karyawan->foto_small);
    // } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
    //     $photoSmallUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    // } else {
    //     $photoSmallUrl = Storage::url($karyawan->foto);
    // }

    // $photoUrl = '';
    // if ($karyawan->foto_square) {
    //     $photoUrl = Storage::url($karyawan->foto_square);
    // } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
    //     $photoUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    // }
    $photoSmallUrl = '';
    if ($karyawan->foto_small) {
        // $photoSmallUrl = url(str_replace('public', 'public/storage', $karyawan->foto_small));
        $photoSmallUrl = url(str_replace('public', 'storage', $karyawan->foto_small));
    } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
        $photoSmallUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    } else {
        // $photoSmallUrl = url(str_replace('public', 'public/storage', $karyawan->foto));
        $photoSmallUrl = url(str_replace('public', 'storage', $karyawan->foto));
    }

    $photoUrl = '';
    if ($karyawan->foto_square) {
        // $photoUrl = url(str_replace('public', 'public/storage', $karyawan->foto_square));
        $photoUrl = url(str_replace('public', 'storage', $karyawan->foto_square));
    } elseif ($karyawan->foto && (Str::startsWith($karyawan->foto, 'rsud_') || $karyawan->foto === 'user.png')) {
        $photoUrl = 'https://e-rsud.langsakota.go.id/hrd/user/images/profil/' . $karyawan->foto;
    }

    $salt = env('QR_SALT', 'this-is-secret-of-rsud-langsa-salt-2025');
    // $hashedId = md5($karyawan->kd_karyawan . $salt);
    $hashedId = md5($karyawan->kd_karyawan);
@endphp

@push('styles')
    <style>
        /* Reset internal */
        .id-card * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Kartu Pegawai */
        .id-card {
            height: 262px;
            width: 162px;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        /* Foto Pegawai (di bawah background) */
        .photo-section .profile-photo {
            position: absolute;
            top: 75px; /* Sesuaikan dengan slot background */
            left: 50%;
            transform: translateX(-50%);
            width: 58px;
            height: 77px;
            object-fit: cover;
            border-radius: 5px;
            z-index: 1; /* Di bawah background */
        }

        /* Background Kartu (menutupi sebagian foto) */
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
            z-index: 3;
            padding: 10px 5px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        /* Header Rumah Sakit */
        .id-card .header {
            font-family: 'Poppins', sans-serif;
            font-size: 6px;
        }

        .id-card h2.company-name {
            font-size: 7.3px;
            font-weight: 600;
        }

        .id-card p.alamat {
            font-size: 4.4px;
            line-height: 1.3;
        }

        /* Logo */
        .id-card .logos {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 4px;
            margin-bottom: 6px;
        }

        .id-card .logos img {
            width: 20px;
            height: 20px;
        }

        /* Info Pegawai */
        .id-card .info h3 {
            font-size: 8px;
            margin-top: 135px;
            color: black;
            font-weight: 800;
            text-transform: uppercase;
            font-family: 'Poppins', sans-serif;;
            text-decoration: underline;
        }

        .id-card .info p {
            font-size: 7px;
            color: black;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;;
        }

        /* QR Code */
        .id-card .qr-section {
            margin: 0;
        }

        .qr-section svg {
            width: 45px;
            height: 45px;
            margin: 0 auto;
        }

        .id-card .qr-section p {
            font-size: 7px;
            color: black;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;;
        }

        /* ============================= */
        /* PERBESAR UNTUK TAMPILAN LAYAR */
        /* ============================= */
        @media screen {
            .id-card {
                height: 350px;
                width: 216px;
                transform: scale(1.3);
                transform-origin: top center;
            }

            .id-card h2.company-name { font-size: 10px; color: #fff; }
            .id-card p.alamat { font-size: 6px; }
            .id-card .logos img { width: 20px; height: 20px; object-fit: contain; }
            .photo-section .profile-photo { width: 80px; height: 106px; top: 110px; }
            .id-card .info h3 { font-size: 10px; }
            .id-card .info p { font-size: 9px; }
            .id-card .qr-section p { font-size: 8px; }
            .qr-section svg { width: 50px; height: 50px; }
        }

        /* ============================= */
        /* UKURAN CETAK (PRINT ASLI) */
        /* ============================= */
        @media print {
            .id-card {
                height: 262px;
                width: 162px;
                box-shadow: none;
                transform: none;
            }

            .id-card h2.company-name { font-size: 7.3px; }
            .id-card p.alamat { font-size: 4.4px; }
            .id-card .logos img { width: 20px; height: 20px; }
            .photo-section .profile-photo { width: 58px; height: 77px; top: 75px; }
            .id-card .info h3 { font-size: 8px; }
            .id-card .info p { font-size: 7px; }
            .id-card .qr-section p { font-size: 6px; }
            .qr-section svg { width: 40px; height: 40px; }
        }
    </style>
@endpush


@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar  pt-6 pb-2 ">
        <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch ">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Karyawan
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">
                                Dashboard </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Riwayat
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content  flex-column-fluid ">
    <div id="kt_app_content_container" class="app-container  container-fluid ">
        <x-employee-header :karyawan="$karyawan" :missing-fields="$missing_fields" :persentase-kelengkapan="$persentase_kelengkapan" />

        <div class="row g-10 g-xl-10">
            <div class="col-md-8">
                <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
                    <div class="card-header cursor-pointer">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Detail Data Diri</h3>
                        </div>
        
                        <a href="#"
                            class="btn btn-sm btn-primary align-self-center">Edit Data Diri</a>
                    </div>
        
                    <div class="card-body p-9">
                        @if ($karyawan->kd_status_kerja == 1)
                            <div class="row mb-7">
                                <label class="col-lg-3 fw-semibold text-muted">NIP</label>
            
                                <div class="col-lg-6">
                                    <span class="fw-bold fs-6 text-gray-800">
                                        {{ $karyawan->nip_baru }}
                                    </span>
                                </div>
                            </div>
                        @elseif ($karyawan->kd_status_kerja == 7)
                            <div class="row mb-7">
                                <label class="col-lg-3 fw-semibold text-muted">NIPPPK</label>
            
                                <div class="col-lg-6">
                                    <span class="fw-bold fs-6 text-gray-800">
                                        {{ $karyawan->nip_baru }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">Nama Lengkap</label>
        
                            <div class="col-lg-6">
                                <span class="fw-bold fs-6 text-gray-800">
                                    {{ $nama_lengkap }}
                                </span>
                            </div>
                        </div>
        
                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">Jenis kelamin</label>
        
                            <div class="col-lg-6 fv-row">
                                <span class="fw-semibold text-gray-800 fs-6">
                                    {{ $karyawan->jenis_kelamin }}
                                </span>
                            </div>
                        </div>
        
                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Tempat, Tanggal lahir
                            </label>
        
                            <div class="col-lg-6 d-flex align-items-center">
                                <span class="fw-bold fs-6 text-gray-800 me-2">
                                    <!-- format indonesia 01 Februari 1998 -->
                                    {{ $karyawan->tempat_lahir }}, {{ $carbon::parse($karyawan->tgl_lahir)->locale('id')->isoFormat('LL') }}
                                </span>
                            </div>
                        </div>
        
                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Agama
                            </label>
        
                            <div class="col-lg-6 d-flex align-items-center">
                                <span class="fw-bold fs-6 text-gray-800 me-2">
                                    {{ $karyawan->agama }}
                                </span>
                            </div>
                        </div>

                        <!-- jika bukan PNS atau PPPK, maka tidak perlu menampilkan golongan dan pangkat, jika PNS maka tampikan Pangkat/Golongan, jika PPPK maka tampilkan Golongan saja -->
                        @if ($karyawan->kd_status_kerja == 1 || $karyawan->kd_status_kerja == 7)
                            @if ($karyawan->kd_status_kerja == 1)
                                <div class="row mb-7">
                                    <label class="col-lg-3 fw-semibold text-muted">
                                        Golongan/Pangkat
                                    </label>
                
                                    <div class="col-lg-6 d-flex align-items-center">
                                        <span class="fw-bold fs-6 text-gray-800 me-2">
                                            {{ $karyawan->golongan }} / {{ $karyawan->pangkat }}
                                        </span>
                                    </div>
                                </div>
                            @elseif ($karyawan->kd_status_kerja == 7)
                                <div class="row mb-7">
                                    <label class="col-lg-3 fw-semibold text-muted">
                                        Golongan/Pangkat
                                    </label>
                
                                    <div class="col-lg-6 d-flex align-items-center">
                                        <span class="fw-bold fs-6 text-gray-800 me-2">
                                            {{ $karyawan->pangkat }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Eselon
                            </label>
        
                            <div class="col-lg-6 d-flex align-items-center">
                                <span class="fw-bold fs-6 text-gray-800 me-2">
                                    {{ $karyawan->eselon }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-7">
                            <label class="col-lg-3 fw-semibold text-muted">
                                Tempat Tugas
                            </label>
        
                            <div class="col-lg-6 d-flex align-items-center">
                                <span class="fw-bold fs-6 text-gray-800 me-2">
                                    {{ $karyawan->ruangan }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="id-card ms-lg-10">
            
                    {{-- Foto Pegawai (di bawah background) --}}
                    <div class="photo-section">
                        <img src="{{ $photoSmallUrl }}" alt="{{ $karyawan->nama }}" class="profile-photo">
                    </div>
            
                    {{-- Background di atas foto --}}
                    <div class="background-overlay"></div>
            
                    {{-- Konten lain --}}
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
                            {{-- {!! QrCode::size(60)->generate(url('/pegawai/' . $karyawan->kd_karyawan)) !!} --}}
                            {!! QrCode::size(40)->generate(url('show-personal/' . $hashedId)); !!}
                            <p>ID Peg. {{ $karyawan->kd_karyawan }}</p>
                        </div>
                    </div>
            
                </div>
            </div>
            
        </div>
    </div>

    <div class="modal fade" id="printIdCardModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title">Cetak ID Card</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form id="printIdCardForm" class="form">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Alasan Pembuatan ID Card</label>
                            <select class="form-select" name="alasan" id="alasan">
                                <option value="">Pilih Alasan</option>
                                <option value="1">Belum pernah buat</option>
                                <option value="2">Hilang</option>
                                <option value="3">Rusak</option>
                            </select>
                            <div class="invalid-feedback">
                                Alasan harus dipilih
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="printSubmitButton">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true" data-bs-focus="false" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold modal-title">Upload Foto Profil</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form id="uploadPhotoForm" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Foto Profil (1:1)</label>
                            <input type="file" class="form-control" name="foto_square" id="foto_square" accept="image/jpeg,image/png,image/jpg">
                            <div class="invalid-feedback" id="foto_square_error"></div>
                            <img id="foto_square_preview" src="{{ $karyawan->foto_square ? Storage::url($karyawan->foto_square) : '' }}" class="mt-2" style="max-width: 100px; display: {{ $karyawan->foto_square ? 'block' : 'none' }};">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto CV (3:4)</label>
                            <input type="file" class="form-control" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg">
                            <div class="invalid-feedback" id="foto_error"></div>
                            <img id="foto_preview" src="{{ $karyawan->foto ? Storage::url($karyawan->foto) : '' }}" class="mt-2" style="max-width: 100px; display: {{ $karyawan->foto ? 'block' : 'none' }};">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Kecil (PNG tanpa background)</label>
                            <input type="file" class="form-control" name="foto_small" id="foto_small" accept="image/png">
                            <div class="invalid-feedback" id="foto_small_error"></div>
                            <img id="foto_small_preview" src="{{ $karyawan->foto_small ? Storage::url($karyawan->foto_small) : '' }}" class="mt-2" style="max-width: 100px; display: {{ $karyawan->foto_small ? 'block' : 'none' }};">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="uploadSubmitButton">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // Cek pesan error dari session
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            let karyawanId;
            $('#printIdCardButton').on('click', function(e) {
                e.preventDefault();
                karyawanId = $(this).data('id');

                // Cek apakah ada session yang valid
                $.ajax({
                    url: '{{ route("admin.karyawan.generate-print-token") }}',
                    method: 'POST',
                    data: {
                        check_session: true, // Tambahan untuk cek session saja
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.session_valid) {
                            // Session ada dan valid, langsung buka print-id-card
                            let token = response.token;
                            let printUrl = '{{ url("admin/karyawan/identitas/print-id-card") }}' + '/' + karyawanId + '?token=' + token;
                            window.open(printUrl, '_blank');
                        } else {
                            // Tidak ada session atau kadaluarsa, tampilkan modal
                            $('#printIdCardModal').modal('show');
                        }
                    },
                    error: function() {
                        // Jika ada error, tampilkan modal sebagai fallback
                        $('#printIdCardModal').modal('show');
                    }
                });
            });

            $('#printIdCardForm').on('submit', function(e) {
                e.preventDefault();

                let alasan = $('#alasan').val();
                if (!alasan) {
                    $('#alasan').addClass('is-invalid');
                    toastr.error('Pilih alasan terlebih dahulu!');
                    return false;
                }

                $('#alasan').removeClass('is-invalid');
                $('#printSubmitButton').prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: '{{ route("admin.karyawan.generate-print-token") }}',
                    method: 'POST',
                    data: {
                        alasan: alasan,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            let token = response.token;
                            $('#printIdCardModal').modal('hide');
                            let printUrl = '{{ url("admin/karyawan/identitas/print-id-card") }}' + '/' + karyawanId + '?token=' + token;
                            window.open(printUrl, '_blank');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Terjadi kesalahan saat memproses permintaan.');
                    },
                    complete: function() {
                        $('#printSubmitButton').prop('disabled', false).text('Cetak');
                    }
                });
            });

            $('#alasan').on('change', function() {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                }
            });

            // Preview gambar saat file dipilih
            function previewImage(input, previewId) {
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewId).attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#foto_square').on('change', function() {
                previewImage(this, '#foto_square_preview');
                $(this).removeClass('is-invalid');
                $('#foto_square_error').text('');
            });

            $('#foto').on('change', function() {
                previewImage(this, '#foto_preview');
                $(this).removeClass('is-invalid');
                $('#foto_error').text('');
            });

            $('#foto_small').on('change', function() {
                previewImage(this, '#foto_small_preview');
                $(this).removeClass('is-invalid');
                $('#foto_small_error').text('');
            });

            $('#uploadPhotoForm').on('submit', function(e) {
                e.preventDefault();
                // karyawanId = $('#uploadPhotoModal').data('id') || '{{ $karyawan->kd_karyawan }}';
                // Pastikan karyawanId sudah di-set
                if (!karyawanId) {
                    karyawanId = '{{ $karyawan->kd_karyawan }}'; // Fallback jika tidak di-set dari tombol
                }

                let formData = new FormData(this);
                $('#uploadSubmitButton').prop('disabled', true).text('Mengunggah...');

                $.ajax({
                    url: '{{ url("admin/karyawan/upload-photo") }}' + '/' + karyawanId, // Perbaikan: Gunakan base URL + karyawanId
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#uploadPhotoModal').modal('hide');

                            // Update preview dengan gambar yang baru diupload
                            if (response.photos.foto_square) {
                                $('#foto_square_preview').attr('src', response.photos.foto_square).show();
                            }
                            if (response.photos.foto) {
                                $('#foto_preview').attr('src', response.photos.foto).show();
                            }
                            if (response.photos.foto_small) {
                                $('#foto_small_preview').attr('src', response.photos.foto_small).show();
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.foto_square) {
                                $('#foto_square').addClass('is-invalid');
                                $('#foto_square_error').text(errors.foto_square[0]);
                            }
                            if (errors.foto) {
                                $('#foto').addClass('is-invalid');
                                $('#foto_error').text(errors.foto[0]);
                            }
                            if (errors.foto_small) {
                                $('#foto_small').addClass('is-invalid');
                                $('#foto_small_error').text(errors.foto_small[0]);
                            }
                        } else {
                            toastr.error('Terjadi kesalahan saat mengunggah foto.');
                        }
                    },
                    complete: function() {
                        $('#uploadSubmitButton').prop('disabled', false).text('Upload');
                    }
                });
            });
        });
    </script>
@endpush