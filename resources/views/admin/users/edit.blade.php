@extends('layouts.user_type.auth')
@section('title', 'Edit User')
@section('content')
<div class="container"> 
    <x-page-header route-prefix="users" mode="edit" />

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')

        <div class="row">
            <div class="col-md-6">
                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" 
                           value="{{ old('name', $user->name) }}" 
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" 
                           value="{{ old('email', $user->email) }}" 
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Gender --}}
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">-- Select Gender --</option>
                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                {{-- Phone --}}
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" 
                           value="{{ old('phone', $user->phone) }}" 
                           class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="3" 
                              class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check">
                    <input type="checkbox" id="is_active_input" name="is_active" value="1" class="form-check-input" {{ $user->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active_input">Active</label>
                </div>

                {{-- Roles --}}
            </div>
        </div>

        <div class="row">
        
            <div class="col-6 col-md-6 mb-3">
                <label class="form-label">Roles</label>
                <div class="d-flex flex-wrap">
                    @foreach($roles as $role)
                        <div class="form-check me-3">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                class="form-check-input"
                                {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }} id="role_input_{{ $role }}">
                            <label class="form-check-label" for="role_input_{{ $role }}">{{ $role->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="col-6 col-md-6 mb-3">
                <label class="form-label">Permission</label>
                <div class="d-flex flex-wrap">
                    @foreach($permissions as $permission)
                        <div class="form-check me-3">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                class="form-check-input"
                                {{ in_array($permission->name, old('permissions', $user->permissions->pluck('name')->toArray())) ? 'checked' : '' }} id="permission_input_{{ $permission->name }}">
                            <label class="form-check-label" for="permission_input_{{ $permission->name }}">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            
        </div>
        
        <x-form-buttons route-prefix="users" mode="edit" />
    </form>
</div>
@endsection
