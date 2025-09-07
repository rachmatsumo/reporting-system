{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.user_type.auth')
@section('title', $user->name)
@section('content')
<div class="container">
    <x-page-header route-prefix="users" mode="show" />
 
    <div class="row">
        <div class="col-12 col-md-4">
            <img src="{{ $user->photo_url }}" alt="User Photo" class="img-thumbnail w-100" width="120">
        </div>
        <div class="col-12 col-md-8">
            <dl class="row mb-0">
                <h5 class="card-title mb-3">{{ $user->name }}</h5>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>

                <dt class="col-sm-3">Gender</dt>
                <dd class="col-sm-9">{{ $user->gender_label }}</dd>

                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $user->phone ?? '-' }}</dd>

                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $user->address ?? '-' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ $user->is_active_label }}</dd>

                <dt class="col-sm-3">Created at</dt>
                <dd class="col-sm-9">{{ $user->created_at }}</dd>

                <dt class="col-sm-3">Updatet at</dt>
                <dd class="col-sm-9">{{ $user->updated_at }}</dd>

                <dt class="col-sm-3">Roles</dt>
                <dd class="col-sm-9">
                    @forelse($user->roles as $role)
                        <span class="badge bg-primary">{{ $role->name }}</span>
                    @empty
                        <span class="text-muted">-</span>
                    @endforelse
                </dd>
            </dl> 
        </div>
    </div>
</div>
@endsection
