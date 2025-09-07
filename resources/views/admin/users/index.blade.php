@extends('layouts.user_type.auth')
@section('title', 'User')
@section('content')
<div class="container">  
    <x-page-header route-prefix="users" mode="index" />

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $users->firstItem() + $loop->index  }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->gender_label }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                        <span class="badge bg-secondary">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $user->is_active_label }}</td>
                    <td>
                        <x-action-dropdown :model="$user" :show="['view', 'edit']"/> 
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
