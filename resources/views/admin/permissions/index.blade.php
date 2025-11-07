@extends('layouts.user_type.auth')
@section('title', 'Permission')
@section('content')
<div class="container"> 
    <x-page-header route-prefix="permissions" mode="index" />

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Permission</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody> 
                @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permissions->firstItem() + $loop->index }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->created_at }}</td>
                    <td>{{ $permission->updated_at }}</td>
                    <td> 
                        <x-action-dropdown :model="$permission" :show="['edit', 'delete']"/>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $permissions->links() }}
        </div>
    </div>
</div>
@endsection
