@extends('layouts.user_type.auth')

@section('content')


<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header border-bottom">
                        <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Add New User</h5>
                        </div> 
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user-management.store') }}">
                            @csrf 

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                        value="" required> 
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div> 
                            </div>

                            <div class="row mb-3"> 
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                        value="" required> 
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                        value=""> 
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>

                            <div class="row mb-3"> 
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <select type="role" name="role" class="form-select @error('role') is-invalid @enderror" required> 
                                        <option value="" disabled selected>Select Role</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="observer" {{ old('role') == 'observer' ? 'selected' : '' }}>Observer</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> 
                            </div>

                            <div class="text-end mt-5">
                                <a href="{{ route('user-management.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</main>
@endsection