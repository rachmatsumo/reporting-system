@extends('layouts.user_type.auth')
@section('title', 'Tambah Role')
@section('content')
<div class="container"> 
    <x-page-header route-prefix="roles" mode="create" />

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nama Role</label>
            <input type="text" name="name" class="form-control" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div> 

        <div class="row mb-4">
            <label>Permissions</label>
            @php 
                $parents = $menus->whereNull('parent_id');
            @endphp
            @foreach($parents as $parent)
            @php
                $secondLevels = $menus->where('parent_id', $parent->id);
            @endphp
            <div class="col-12 col-md-4 permission-card d-flex" style="animation-delay: {{ $loop->index * 0.1 }}s">
                <div class="card w-100">
                    <div class="card-header py-1 bg-secondary mb-2">
                        <h6 class="mb-0 text-white">{{ $parent->title }}</h6>
                    </div>
                    <div class="card-body py-0">
                        {{-- <button type="button" class="btn btn-primary btn-sm select-all-btn" onclick="selectAll()">
                            Select All
                        </button> --}}
                        <div class="d-flex flex-column">
                            @php
                                $prefix = explode('.', $parent->permission);
                                $prefix = $prefix[0];
                                $lists = $permissions->filter(function($permission) use ($prefix) {
                                    $module = explode('.', $permission->name)[0]; // ambil 'user' dari 'user.edit'
                                    return $module === $prefix;
                                });
                            @endphp
                            @foreach($lists as $list)
                                @php 
                                    $listName = explode('.', $list->name);
                                    $permissionName = ucwords($listName[0]) . ' ' . ucwords($listName[1]);
                                @endphp 
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="permissions[]" value="{{ $list->name }}" class="form-check-input" id="{{ $list->name }}">
                                    <label class="form-check-label" for="{{ $list->name }}">{{ $permissionName }}</label>
                                </div>
                            @endforeach 
                            @foreach($secondLevels as $secondLevel)
                                @php
                                    $prefix = explode('.', $secondLevel->permission)[0] ?? '';
                                    $lists = $permissions->filter(function($permission) use ($prefix) {
                                        $module = explode('.', $permission->name)[0];
                                        return $module === $prefix;
                                    });
                                @endphp
                                <div class="d-flex flex-column mb-2">
                                    <span>{{ $secondLevel->title }}</span>
                                    @foreach($lists as $list)
                                        @php 
                                            $listName = explode('.', $list->name);
                                            $permissionName = ucwords($listName[0]) . ' ' . ucwords($listName[1] ?? '');
                                        @endphp 
                                        <div class="form-check form-check-inline mb-0">
                                            <input type="checkbox" name="permissions[]" value="{{ $list->name }}" class="form-check-input" id="{{ $list->name }}">
                                            <label class="form-check-label" for="{{ $list->name }}">{{ $permissionName }}</label>
                                        </div>
                                    @endforeach

                                    {{-- Level 3 --}}
                                    @php
                                        $thirdLevels = $menus->where('parent_id', $secondLevel->id);
                                    @endphp
                                    @foreach($thirdLevels as $thirdLevel)
                                        @php
                                            $prefix = explode('.', $thirdLevel->permission)[0] ?? '';
                                            $lists = $permissions->filter(function($permission) use ($prefix) {
                                                $module = explode('.', $permission->name)[0];
                                                return $module === $prefix;
                                            });
                                        @endphp
                                        <div class="d-flex flex-column ms-3">
                                            <span>{{ $thirdLevel->title }}</span>
                                            @foreach($lists as $list)
                                                @php 
                                                    $listName = explode('.', $list->name);
                                                    $permissionName = ucwords($listName[0]) . ' ' . ucwords($listName[1] ?? '');
                                                @endphp 
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" name="permissions[]" value="{{ $list->name }}" class="form-check-input" id="{{ $list->name }}">
                                                    <label class="form-check-label" for="{{ $list->name }}">{{ $permissionName }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach

                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div> 
 
        <x-form-buttons route-prefix="roles" mode="create" /> 
    </form>
</div>
@endsection
