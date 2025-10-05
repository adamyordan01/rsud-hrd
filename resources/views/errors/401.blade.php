@extends('errors.layout')

@section('title', 'Unauthorized')
@section('code', '401')

@section('message')
    Anda tidak memiliki otorisasi untuk mengakses halaman ini. Silakan login dengan akun yang valid atau hubungi administrator sistem.
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