@extends('layouts.user_type.auth')
@section('title', 'Edit Permission')
@section('content')
<div class="container"> 
    <x-page-header route-prefix="permissions" mode="edit" />

    <form action="{{ route('permissions.update', $permission) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Nama Permission</label>
            <input type="text" name="name" value="{{ old('name', $permission->name) }}" class="form-control" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <x-form-buttons route-prefix="permissions" mode="edit" /> 
    </form>
</div>
@endsection
