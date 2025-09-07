@extends('layouts.user_type.auth')
@section('title', 'Ubah Kata Sandi')
@section('content')
<div class="container mt-4"> 
    <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3 py-3 px-1">
        <h5 class="mb-0">Change Password</h5>  
    </div>

    <form action="{{ route('profile.update-password') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control">
            @error('current_password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="password" class="form-control">
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">  
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i>
                Update
            </button>
        </div> 
    </form>
</div>
@endsection
