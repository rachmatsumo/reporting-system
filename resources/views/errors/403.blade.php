@extends('errors.index')
@section('title', '403 Access Forbidden')
@section('content')
    <div class="text-center">
        <div class="error-code" style="color:#6f42c1;">403</div>
        <div class="error-message">Forbidden</div>
        <p>Anda tidak diizinkan mengakses halaman ini.</p>
        <a href="{{ url('/') }}" class="btn btn-purple btn-home text-white" style="background-color:#6f42c1; border-color:#6f42c1;">
            <i class="bi bi-house-fill"></i> Kembali ke Beranda
        </a>
    </div>
@endsection
