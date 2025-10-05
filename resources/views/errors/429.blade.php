@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan')
@section('code', '429')

@section('message')
    Anda telah mengirim terlalu banyak permintaan dalam waktu singkat. Untuk melindungi sistem, akses Anda dibatasi sementara. Silakan tunggu beberapa saat sebelum mencoba lagi.
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

