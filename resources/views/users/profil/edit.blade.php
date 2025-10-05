@extends('layouts.user', ['title' => 'Edit Profil'])

@push('styles')
<style>
    .edit-form .form-label {
        font-weight: 600;
        color: #5e6278;
    }
    
    .section-separator {
        border-top: 1px solid #e1e3ea;
        margin: 2rem 0;
    }
    
    .required-field::after {
        content: ' *';
        color: #f1416c;
    }
    
    .form-control:focus {
        border-color: var(--bs-user-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-user-primary-rgb), 0.25);
    }
    
    .select2-container--bootstrap5 .select2-selection {
        border-color: #d6d6e0;
    }
    
    .select2-container--bootstrap5.select2-container--focus .select2-selection {
        border-color: var(--bs-user-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-user-primary-rgb), 0.25);
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Edit Profil
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.profil.index') }}" class="text-muted text-hover-primary">Profil Saya</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Edit Profil</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('user.profil.index') }}" class="btn btn-light">
                    <i class="ki-duotone ki-arrow-left fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        <form id="editProfilForm" class="edit-form">
            @csrf
            @method('PUT')
            
            <div class="row g-6 g-xl-9">
                {{-- Data Personal --}}
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Personal</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-5">
                                    <label class="form-label">Gelar Depan</label>
                                    <input type="text" class="form-control bg-light" name="gelar_depan" 
                                           value="{{ $karyawan->gelar_depan }}" 
                                           placeholder="Dr., Ir., dll" readonly>
                                    <div class="form-text text-muted">
                                        <i class="ki-duotone ki-information fs-7 text-warning me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Hanya dapat diubah oleh bagian kepegawaian
                                    </div>
                                </div>
                                <div class="col-md-4 mb-5">
                                    <label class="form-label required-field">Nama Lengkap</label>
                                    <input type="text" class="form-control bg-light" name="nama" 
                                           value="{{ $karyawan->nama }}" 
                                           placeholder="Nama lengkap" readonly>
                                    <div class="form-text text-muted">
                                        <i class="ki-duotone ki-information fs-7 text-warning me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Hanya dapat diubah oleh bagian kepegawaian
                                    </div>
                                </div>
                                <div class="col-md-4 mb-5">
                                    <label class="form-label">Gelar Belakang</label>
                                    <input type="text" class="form-control bg-light" name="gelar_belakang" 
                                           value="{{ $karyawan->gelar_belakang }}" 
                                           placeholder="S.Kep., Ns., M.Kep., dll" readonly>
                                    <div class="form-text text-muted">
                                        <i class="ki-duotone ki-information fs-7 text-warning me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Hanya dapat diubah oleh bagian kepegawaian
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">NIK</label>
                                    <input type="text" class="form-control" name="no_ktp" 
                                           value="{{ $karyawan->no_ktp }}" 
                                           placeholder="Nomor Induk Kependudukan">
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" name="kd_jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        @foreach($jenisKelaminList as $jk)
                                            <option value="{{ $jk->kode }}" 
                                                    {{ $karyawan->kd_jenis_kelamin == $jk->kode ? 'selected' : '' }}>
                                                {{ $jk->jenis }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" name="tempat_lahir" 
                                           value="{{ $karyawan->tempat_lahir }}" 
                                           placeholder="Kota tempat lahir">
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tgl_lahir" 
                                           value="{{ $karyawan->tgl_lahir }}">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Agama</label>
                                    <select class="form-select" name="kd_agama">
                                        <option value="">Pilih Agama</option>
                                        @foreach($agamaList as $agama)
                                            <option value="{{ $agama->kd_agama }}" 
                                                    {{ $karyawan->kd_agama == $agama->kd_agama ? 'selected' : '' }}>
                                                {{ $agama->agama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Status Pernikahan</label>
                                    <select class="form-select" name="kd_status_nikah">
                                        <option value="">Pilih Status</option>
                                        @foreach($statusNikahList as $status)
                                            <option value="{{ $status->kd_status_nikah }}" 
                                                    {{ $karyawan->kd_status_nikah == $status->kd_status_nikah ? 'selected' : '' }}>
                                                {{ $status->status_nikah }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="section-separator"></div>
                            <h5 class="text-gray-800 mb-4">Data Fisik</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Tinggi Badan (cm)</label>
                                    <input type="number" class="form-control" name="tinggi_badan" 
                                           value="{{ $karyawan->tinggi_badan }}" 
                                           placeholder="Contoh: 170" min="100" max="250">
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Berat Badan (kg)</label>
                                    <input type="number" class="form-control" name="berat_badan" 
                                           value="{{ $karyawan->berat_badan }}" 
                                           placeholder="Contoh: 65" min="30" max="300">
                                </div>
                            </div>
                            
                            <div class="section-separator"></div>
                            <h5 class="text-gray-800 mb-4">Kontak</h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-5">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="{{ $karyawan->email }}" 
                                           placeholder="alamat@email.com">
                                </div>
                                <div class="col-md-4 mb-5">
                                    <label class="form-label">No. HP</label>
                                    <input type="text" class="form-control" name="no_hp" 
                                           value="{{ $karyawan->no_hp }}" 
                                           placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="col-md-4 mb-5">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" name="no_telepon" 
                                           value="{{ $karyawan->no_telepon }}" 
                                           placeholder="021xxxxxxx">
                                </div>
                            </div>
                            
                            <div class="section-separator"></div>
                            <h5 class="text-gray-800 mb-4">Alamat</h5>
                            
                            <div class="mb-5">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" name="alamat" rows="3" 
                                          placeholder="Alamat lengkap">{{ $karyawan->alamat }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Provinsi</label>
                                    <select class="form-select" name="kd_propinsi" id="provinsi_id" data-control="select2" data-placeholder="Pilih Provinsi">
                                        <option value=""></option>
                                        @foreach($provinsiList as $provinsi)
                                            <option value="{{ $provinsi->kd_propinsi }}" 
                                                    {{ $karyawan->kd_propinsi == $provinsi->kd_propinsi ? 'selected' : '' }}>
                                                {{ $provinsi->propinsi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Kabupaten/Kota</label>
                                    <select class="form-select" name="kd_kabupaten" id="kabupaten_id"  data-control="select2" data-placeholder="Pilih Kabupaten/Kota">
                                        <option value=""></option>
                                        @foreach($kabupatenList as $kabupaten)
                                            <option value="{{ $kabupaten->kd_kabupaten }}" 
                                                    {{ $karyawan->kd_kabupaten == $kabupaten->kd_kabupaten ? 'selected' : '' }}>
                                                {{ $kabupaten->kabupaten }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Kecamatan</label>
                                    <select class="form-select" name="kd_kecamatan" id="kecamatan_id" data-control="select2" data-placeholder="Pilih Kecamatan">
                                        <option value=""></option>
                                        @foreach($kecamatanList as $kecamatan)
                                            <option value="{{ $kecamatan->kd_kecamatan }}" 
                                                    {{ $karyawan->kd_kecamatan == $kecamatan->kd_kecamatan ? 'selected' : '' }}>
                                                {{ $kecamatan->kecamatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Kelurahan</label>
                                    <select class="form-select" name="kd_kelurahan" id="kelurahan_id" data-control="select2" data-placeholder="Pilih Kelurahan">
                                        <option value=""></option>
                                        @foreach($kelurahanList as $kelurahan)
                                            <option value="{{ $kelurahan->kd_kelurahan }}" 
                                                    {{ $karyawan->kd_kelurahan == $kelurahan->kd_kelurahan ? 'selected' : '' }}>
                                                {{ $kelurahan->kelurahan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Sidebar Info --}}
                <div class="col-xl-4">
                    {{-- Save Button --}}
                    <div class="card mb-6">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <span class="indicator-label">
                                    <i class="ki-duotone ki-check fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Simpan Perubahan
                                </span>
                                <span class="indicator-progress">
                                    Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Help Card --}}
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="card-title">Panduan Pengisian</h3>
                        </div>
                        <div class="card-body">
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                                <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Tips Pengisian</h4>
                                        <div class="fs-6 text-gray-700">
                                            <ul class="mb-0 ps-3">
                                                <li class="mb-2">Field bertanda <span class="text-danger">*</span> wajib diisi</li>
                                                <li class="mb-2">Pastikan nomor HP aktif untuk notifikasi</li>
                                                <li class="mb-2">Email digunakan untuk komunikasi resmi</li>
                                                <li class="mb-2">Alamat sesuai dengan KTP</li>
                                                <li>Data yang lengkap mempermudah proses administrasi</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Data Restrictions Card --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Penting</h3>
                        </div>
                        <div class="card-body">
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                                <i class="ki-duotone ki-shield-cross fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Data Terbatas</h4>
                                        <div class="fs-6 text-gray-700">
                                            <p class="mb-3">Beberapa data tidak dapat diubah melalui sistem ini:</p>
                                            <ul class="mb-3 ps-3">
                                                <li>Nama dan Gelar</li>
                                                <li>NIK/No. KTP</li>
                                                <li>Tempat & Tanggal Lahir</li>
                                                <li>Data Kepegawaian (NIP, Golongan, dll)</li>
                                                <li>Data Pendidikan Formal</li>
                                                <li>Data Keluarga</li>
                                            </ul>
                                            <div class="alert alert-info d-flex align-items-center p-3 mb-0">
                                                <i class="ki-duotone ki-information-4 fs-3 text-info me-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                <div class="flex-grow-1">
                                                    <strong>Untuk mengubah data tersebut,</strong><br>
                                                    silahkan hubungi <strong>Bagian Kepegawaian</strong> dengan membawa dokumen pendukung yang diperlukan.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Cascading dropdown untuk alamat
    $('#provinsi_id').change(function() {
        var provinsiId = $(this).val();
        loadKabupaten(provinsiId);
        $('#kecamatan_id').html('<option value="">Pilih Kecamatan</option>');
        $('#kelurahan_id').html('<option value="">Pilih Kelurahan</option>');
    });
    
    $('#kabupaten_id').change(function() {
        var kabupatenId = $(this).val();
        loadKecamatan(kabupatenId);
        $('#kelurahan_id').html('<option value="">Pilih Kelurahan</option>');
    });
    
    $('#kecamatan_id').change(function() {
        var kecamatanId = $(this).val();
        loadKelurahan(kecamatanId);
    });
    
    function loadKabupaten(provinsiId) {
        if(provinsiId) {
            $.get('{{ route("user.profil.get-kabupaten") }}', {provinsi_id: provinsiId}, function(response) {
                var options = '<option value="">Pilih Kabupaten/Kota</option>';
                if(response.success && response.data) {
                    $.each(response.data, function(key, value) {
                        options += '<option value="' + value.kd_kabupaten + '">' + value.kabupaten + '</option>';
                    });
                }
                $('#kabupaten_id').html(options);
            });
        } else {
            $('#kabupaten_id').html('<option value="">Pilih Kabupaten/Kota</option>');
        }
    }
    
    function loadKecamatan(kabupatenId) {
        if(kabupatenId) {
            $.get('{{ route("user.profil.get-kecamatan") }}', {kabupaten_id: kabupatenId}, function(response) {
                var options = '<option value="">Pilih Kecamatan</option>';
                if(response.success && response.data) {
                    $.each(response.data, function(key, value) {
                        options += '<option value="' + value.kd_kecamatan + '">' + value.kecamatan + '</option>';
                    });
                }
                $('#kecamatan_id').html(options);
            });
        } else {
            $('#kecamatan_id').html('<option value="">Pilih Kecamatan</option>');
        }
    }
    
    function loadKelurahan(kecamatanId) {
        if(kecamatanId) {
            $.get('{{ route("user.profil.get-kelurahan") }}', {kecamatan_id: kecamatanId}, function(response) {
                var options = '<option value="">Pilih Kelurahan</option>';
                if(response.success && response.data) {
                    $.each(response.data, function(key, value) {
                        options += '<option value="' + value.kd_kelurahan + '">' + value.kelurahan + '</option>';
                    });
                }
                $('#kelurahan_id').html(options);
            });
        } else {
            $('#kelurahan_id').html('<option value="">Pilih Kelurahan</option>');
        }
    }
    
    // Form submission
    $('#editProfilForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        
        // Show loading
        submitBtn.attr('data-kt-indicator', 'on');
        
        $.ajax({
            url: '{{ route("user.profil.update") }}',
            type: 'PUT',
            data: formData,
            success: function(response) {
                if(response.success) {
                    toastr.success('Profil berhasil diperbarui');
                    setTimeout(function() {
                        window.location.href = '{{ route("user.profil.index") }}';
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Gagal memperbarui profil');
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if(errors) {
                    // Clear previous errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                    
                    // Show field errors
                    $.each(errors, function(field, messages) {
                        var input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                    });
                    
                    toastr.error('Periksa kembali form yang Anda isi');
                } else {
                    toastr.error('Terjadi kesalahan saat memperbarui profil');
                }
            },
            complete: function() {
                submitBtn.removeAttr('data-kt-indicator');
            }
        });
    });
    
    // Input validation
    $('input[name="email"]').blur(function() {
        var email = $(this).val();
        if(email && !isValidEmail(email)) {
            $(this).addClass('is-invalid');
            if(!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Format email tidak valid</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    $('input[name="no_hp"]').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    $('input[name="no_ktp"]').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if(this.value.length > 16) {
            this.value = this.value.slice(0, 16);
        }
    });
    
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
</script>
@endpush