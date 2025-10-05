@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')

@section('message')
    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator sistem atau login dengan akun yang memiliki hak akses yang sesuai.
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