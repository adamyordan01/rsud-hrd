@extends('errors.layout')

@section('title', 'Terjadi Kesalahan')
@section('code', $__errorCode ?? 'ERROR')

@section('message')
    @if(isset($exception) && $exception->getMessage())
        {{ $exception->getMessage() }}
    @else
        Maaf, terjadi kesalahan yang tidak terduga. Tim teknis telah diberitahu tentang masalah ini. Silakan coba lagi atau hubungi administrator sistem.
    @endif
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

