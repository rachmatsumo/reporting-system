<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3 align-items-start">
        <nav aria-label="breadcrumb" class="order-2 order-md-1">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                @php
                    $segments = Request::segments();
                    $url = ''; 
                @endphp

                @if(count($segments) === 1 && $segments[0] === 'dashboard')
                    {{-- Kalau hanya /dashboard → langsung Home active --}}
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                        Dashboard
                    </li>
                @else
                    {{-- Cetak Home dulu --}}
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="{{ url('/') }}">Dashboard</a>
                    </li>

                    {{-- Loop segmen --}}
                    @foreach($segments as $key => $segment)
                        @php
                            $url .= '/'.$segment;
                            $title = ucfirst(str_replace('-', ' ', $segment));
                        @endphp

                        @if(is_numeric($segment))
                            @continue
                        @endif

                        @if($loop->last)
                            <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                                {{ $title }}
                            </li>
                        @else
                            <li class="breadcrumb-item text-sm">
                                <a class="opacity-5 text-dark" href="{{ url($url) }}">{{ $title }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ol>

            {{-- Judul halaman --}}
            <h6 class="font-weight-bolder mb-0 text-capitalize mt-4 d-none">
                {{ ucfirst(str_replace('-', ' ', last($segments))) }}
            </h6>
        </nav>

        <div class="order-1 order-md-2 collapse navbar-collapse mt-sm-0 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar"> 
            {{-- <div class="ms-md-3 pe-md-3 d-flex align-items-center">
                <div class="input-group">
                    <span class="input-group-text text-body"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" placeholder="Type here...">
                </div>
            </div> --}}
            <ul class="navbar-nav justify-content-between align-items-start">
                
                <li class="nav-item d-xl-none d-flex align-items-center py-2">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                    </a>
                </li>
                <li class="dropdown nav-item">
                    <a href="#" class="dropdown-toggle align-itmes-center" data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                        {{ Auth::user()->name }} <img src="{{ Auth::user()->photo_url ?? asset('assets/uploads/avatar/default.png') }}" class="avatar avatar-sm">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-1 " aria-labelledby="navbarDropdownMenuLink2">
                        <li class="p-0">
                            <a href="{{ route('profile.show', Auth()->id())}}" class="dropdown-item border-radius-md">
                                <div class="d-flex p-0">
                                    <div class="col-2 d-flex justify-content-center">
                                        <i class="fs-6 bi bi-person"></i>                         
                                    </div>
                                    <div class="col-10 ps-1">
                                        <span class="text-sm font-weight-normal mb-0">User Profile</span> 
                                    </div>
                                </div> 
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.change-password') }}" class="dropdown-item border-radius-md">
                                <div class="d-flex p-0">
                                    <div class="col-2 d-flex justify-content-center">
                                        <i class="fs-6 bi bi-key"></i>                         
                                    </div>
                                    <div class="col-10 ps-1">
                                        <span class="text-sm font-weight-normal mb-0">Change Password</span> 
                                    </div>
                                </div> 
                            </a>
                        </li>
                        <hr>
                        <li>
                            <a href="{{ url('/logout')}}" class="dropdown-item border-radius-md">
                                <div class="d-flex py-1 text-danger">
                                    <div class="col-2 d-flex justify-content-center">
                                        <i class="fs-6 bi bi-door-open"></i>                         
                                    </div>
                                    <div class="col-10 ps-1">
                                        <span class="text-sm font-weight-normal mb-0">Sign Out</span> 
                                    </div>
                                </div> 
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
</nav>
<!-- End Navbar -->

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Yakin hapus data?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        })
    }
</script>