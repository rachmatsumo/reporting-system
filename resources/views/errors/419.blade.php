@extends('errors.index')
@section('title', '419 Page Expired')
@section('content')
    <div class="text-center">
        <div class="error-code" style="color:#fd7e14;">419</div>
        <div class="error-message">Page Expired</div>
        <p>Sesi Anda telah berakhir atau token CSRF tidak valid. Silakan coba lagi.</p>
        <a href="{{ url()->previous() ?? url('/') }}" class="btn btn-warning btn-home">
            <i class="bi bi-arrow-counterclockwise"></i> Coba Lagi
        </a>
    </div>
@endsection
