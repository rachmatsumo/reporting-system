@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">

    <div class="container-fluid"> 
        {{-- <div class="alert alert-secondary mx-4" role="alert">
            <span class="text-white">
                <strong>Add, Edit, Delete features are not functional!</strong> This is a
                <strong>PRO</strong> feature! Click <strong>
                <a href="https://www.creative-tim.com/live/soft-ui-dashboard-pro-laravel" target="_blank" class="text-white">here</a></strong>
                to see the PRO product!
            </span>
        </div> --}}

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">User Management</h5>
                            </div>
                            <a href="{{ route('user-management.create') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+&nbsp; New User</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0 table-hover table-striped ">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ID
                                        </th> 
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Name
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Email
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            role
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Creation Date
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">1</p>
                                        </td> 
                                        <td class="text-center">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->photo_url }}" class="avatar avatar-sm me-3">
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->name }}</p>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ ucwords($user->role) }}</p>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center"> 
                                                <a href="{{ route('user-management.edit', $user->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                    <i class="fas fa-user-edit text-secondary"></i>
                                                </a>
                                              <form action="{{ route('user-management.destroy', $user->id) }}" 
                                                    method="POST" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link p-0 m-0" 
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top" 
                                                            title="Delete">
                                                        <i class="fas fa-trash text-secondary"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr> 
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">No users found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
 
@endsection