@extends('errors.layout')

@section('title', 'Halaman Kedaluwarsa')
@section('code', '419')

@section('message')
    Sesi Anda telah kedaluwarsa. Hal ini biasanya terjadi jika halaman dibiarkan terbuka terlalu lama atau token CSRF tidak valid. Silakan muat ulang halaman atau kembali ke dashboard.
@endsection

@section('actions')
    <a href="{{ route('admin.dashboard.index') }}" class="btn-error-primary">
        <i class="ki-duotone ki-home fs-4">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        Kembali ke Dashboard
    </a>
@endsection