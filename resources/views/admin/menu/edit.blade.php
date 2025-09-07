@extends('layouts.user_type.auth')
@section('title', 'Edit Menu')
@section('content')
<div class="container"> 
    <x-page-header route-prefix="menus" mode="edit" />

    <form action="{{ route('menus.update', $menu) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="mb-2">
            <label class="block font-semibold">Title</label>
            <input type="text" class="form-control" name="title" value="{{ old('title', $menu->title) }}" class="border p-2 w-full" required>
            @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-2">
            <label class="block font-semibold">Parent</label>
            <select class="form-select" name="parent_id" class="border p-2 w-full">
                <option value="">-- None --</option>
                @foreach($parents as $id => $title)
                    <option value="{{ $id }}" {{ old('parent_id', $menu->parent_id) == $id ? 'selected' : '' }}>
                        {{ $title }}
                    </option>
                @endforeach
            </select>
            @error('parent_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-2">
            <label class="block font-semibold">Route</label>
            <input type="text" class="form-control" name="route" value="{{ old('route', $menu->route) }}" class="border p-2 w-full">
            @error('route') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-2">
            <label class="block font-semibold">Permission</label>
            <input type="text" class="form-control" name="permission" value="{{ old('permission', $menu->permission) }}" class="border p-2 w-full">
            @error('permission') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-2">
            <label class="block font-semibold">Icon</label>
            <input type="text" class="form-control" name="icon" value="{{ old('icon', $menu->icon) }}" class="border p-2 w-full">
            @error('icon') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-2">
            <label class="block font-semibold">Order</label>
            <input type="number" class="form-control" name="order" value="{{ old('order', $menu->order) }}" class="border p-2 w-full">
            @error('order') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
 
        <x-form-buttons route-prefix="menus" mode="edit" />
    </form>
</div>
@endsection
