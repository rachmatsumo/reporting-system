@extends('layouts.user_type.auth')
@section('title', 'Edit Profile')
@section('content')
<div class="container"> 
    {{-- <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3 py-3 px-1">
        <h5 class="mb-0">Edit Profile</h5>  
    </div> --}}
    <x-page-header route-prefix="profile" mode="editNoIndex" />

    <form action="{{ route('profile.update', Auth::id()) }}" 
          method="POST" 
          enctype="multipart/form-data"> {{-- penting untuk upload file --}}
        @csrf @method('PUT')

        <div class="row">
            <div class="col-md-6">
                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" 
                           value="{{ old('name', Auth::user()->name) }}" 
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" 
                           value="{{ old('email', Auth::user()->email) }}" 
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Gender --}}
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">-- Select Gender --</option>
                        <option value="male" {{ old('gender', Auth::user()->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', Auth::user()->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="col-md-6">
                {{-- Phone --}}
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" 
                           value="{{ old('phone', Auth::user()->phone) }}" 
                           class="form-control @error('phone') is-invalid @enderror">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Address --}}
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="3" 
                              class="form-control @error('address') is-invalid @enderror">{{ old('address', Auth::user()->address) }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div> 

                {{-- Photo --}}
                <div class="mb-3">
                    <label class="form-label">Profile Photo</label>
                    <div class="d-flex align-items-center gap-3">
                        {{-- Foto existing --}}
                        <img src="{{ Auth::user()->photo_url }}" 
                             alt="Profile Photo" 
                             class="img-thumbnail" 
                             style="width: 80px; height: 80px; object-fit: cover;">
                        
                        {{-- Upload baru --}}
                        <input type="file" name="photo" 
                               class="form-control @error('photo') is-invalid @enderror">
                    </div>
                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
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
