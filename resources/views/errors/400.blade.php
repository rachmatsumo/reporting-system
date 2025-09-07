@extends('errors.index')
@section('title', '400 Bad Request')
@section('content')
    <div class="text-center">
        <div class="error-code" style="color:#ffc107;">400</div>
        <div class="error-message">Bad Request</div>
        <p>Permintaan yang Anda kirim tidak dapat diproses.</p>
        <a href="{{ url('/') }}" class="btn btn-warning btn-home">
            <i class="bi bi-house-fill"></i> Kembali ke Beranda
        </a>
    </div>
@endsection
