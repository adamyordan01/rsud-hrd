@extends('errors.layout')

@section('title', 'Internal Server Error')
@section('code', '500')

@section('message')
    Maaf, terjadi kesalahan pada server kami. Tim teknis telah diberitahu tentang masalah ini dan sedang bekerja untuk memperbaikinya. Silakan coba lagi dalam beberapa saat.
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

