@extends('layouts.user_type.auth')
@section('title', 'Permission')
@section('content')
<div class="container"> 
    <x-page-header route-prefix="permissions" mode="index" />

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Permission</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td> 
                        <x-action-inline :model="$permission" :show="['edit', 'delete']"/>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
