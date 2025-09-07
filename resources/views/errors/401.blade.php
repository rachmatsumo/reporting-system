@extends('errors.index')
@section('title', '401 Unauthorized')
@section('content')
    <div class="text-center">
        <div class="error-code" style="color:#fd7e14;">401</div>
        <div class="error-message">Unauthorized</div>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ url('/') }}" class="btn btn-orange btn-home" style="background-color:#fd7e14; border-color:#fd7e14;">
            <i class="bi bi-house-fill"></i> Kembali ke Beranda
        </a>
    </div>
@endsection
