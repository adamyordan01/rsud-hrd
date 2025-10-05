@extends('errors.layout')

@section('title', 'Layanan Tidak Tersedia')
@section('code', '503')

@section('message')
    Sistem sedang dalam maintenance atau mengalami gangguan sementara. Tim teknis sedang bekerja untuk memulihkan layanan. Silakan coba lagi dalam beberapa saat.
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

