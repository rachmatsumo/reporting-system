<!-- Sidebar -->
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
            {{-- <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="..."> --}}
            <img src="{{ asset('assets/img/icon.png') }}" class="navbar-brand-img h-100" alt="Logo"> 
            {{-- <i class="fs-3 fa fa-gear"></i> --}}
            <span class="ms-2 font-weight-bold">{{ ENV('APP_NAME')}}</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main"> 
        {!! sideBarMenu() !!} 
    </div>
</aside>

