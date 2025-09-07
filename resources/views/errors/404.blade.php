@extends('errors.index')
@section('title', '404 Not Found')
@section('content')
    <div class="text-center">
        <div class="error-code">404</div>
        <div class="error-message">Oops! Halaman tidak ditemukan.</div>
        <p>Halaman yang Anda cari tidak ada atau telah dihapus.</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-home">
            <i class="bi bi-house-fill"></i> Kembali ke Beranda
        </a>
    </div>
@endsection
