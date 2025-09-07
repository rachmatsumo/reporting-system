@extends('errors.index')
@section('title', '500 Server Error')
@section('content')
    <div class="text-center">
        <div class="error-code">500</div>
        <div class="error-message">Oops! Terjadi kesalahan pada server.</div>
        <p>Maaf, sesuatu yang tidak terduga terjadi. Silakan coba lagi nanti.</p>
        <a href="{{ url('/') }}" class="btn btn-danger btn-home">
            <i class="bi bi-house-fill"></i> Kembali ke Beranda
        </a>
    </div>
@endsection
