@extends('layouts.user_type.auth')
@section('title', 'Role')
@section('content')

<div class="container"> 
    <x-page-header route-prefix="roles" mode="index" />

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Role</th>
                    <th>Permissions</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td class="text-wrap">
                        @foreach($role->permissions as $perm)
                            <span class="badge bg-secondary">{{ $perm->name }}</span>
                        @endforeach
                    </td>
                    <td> 
                        <x-action-dropdown :model="$role" :show="['edit', 'delete']"/>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
</div>
@endsection
