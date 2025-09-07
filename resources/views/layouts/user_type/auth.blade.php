@extends('layouts.app')

@section('auth')
    @include('layouts.partials.auth.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        @include('layouts.partials.auth.nav')
        <div class="container-fluid py-4">
            <div class="card py-4">
                @yield('content')
            </div>
            @include('layouts.partials.footer')
        </div>
    </main>  

    @stack('scripts')
@endsection
