@extends('layouts.user_type.auth')
@section('title', 'Tambah Permission')
@section('content')
<div class="container"> 
    <x-page-header route-prefix="permissions" mode="create" />

    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nama Permission</label>
            <input type="text" name="name" class="form-control" required placeholder="contoh: menu.view">
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
 
        <x-form-buttons route-prefix="permissions" mode="create" /> 
    </form>
</div>
@endsection
