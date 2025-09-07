<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title> 

    <link rel="icon" href="{{ asset('assets/img/icon.png') }}" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" /> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.0.3') }}" rel="stylesheet" /> 
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 

    @stack('styles')
</head>
<body class="g-sidenav-show  bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">
    
    @auth
        @yield('auth')
    @endauth
    @guest
        @yield('guest')
    @endguest 

    @if(session()->has('success'))
        <div x-data="{ show: true}"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            class="position-fixed alert alert-success rounded text-sm py-2 px-4 bottom-1 right-1" style="right:10px">
        <p class="m-0 text-white">{{ session('success')}}</p>
        </div>
    @endif 

    @if ($errors->any())
        <div x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="position-fixed alert alert-danger rounded text-sm py-2 px-4 bottom-1 right-1"
            style="right:10px">
            <ul class="m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($errors->has('error'))
        <div x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="position-fixed alert alert-warning rounded text-sm py-2 px-4 bottom-1 right-1"
            style="right:10px">
            {{ $errors->first('error') }}
        </div>
    @endif

    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>
</html>
