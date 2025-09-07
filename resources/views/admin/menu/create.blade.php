@extends('layouts.user_type.auth') 
@section('title', 'Tambah Menu')
@section('content')
<div class="container"> 
    <x-page-header route-prefix="menus" mode="create" />

    <form action="{{ route('menus.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="mb-2">
            <label class="block font-semibold">Title</label>
            <input type="text" class="form-control" name="title" value="{{ old('title') }}" class="border p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label class="block font-semibold">Parent</label>
            <select name="parent_id" class="form-select border p-2 w-full">
                <option value="">-- None --</option>
                @foreach($parents as $id => $title)
                    <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                        {{ $title }}
                    </option>
                @endforeach
            </select>

        </div>
        <div class="mb-2">
            <label class="block font-semibold">Route</label>
            <input type="text" class="form-control" name="route" value="{{ old('route') }}" class="border p-2 w-full">
        </div>
        <div class="mb-2">
            <label class="block font-semibold">Permission</label>
            <input type="text" class="form-control" name="permission" value="{{ old('permission') }}" class="border p-2 w-full">
            @error('permission') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
        <div class="mb-2">
            <label class="block font-semibold">Icon</label>
            <input type="text" class="form-control" name="icon" value="{{ old('icon') }}" class="border p-2 w-full">
        </div>
        <div class="mb-2">
            <label class="block font-semibold">Order</label>
            <input type="number" class="form-control" name="order" value="{{ old('order') }}" class="border p-2 w-full" value="0">
        </div> 

        <x-form-buttons route-prefix="menus" mode="create" />

    </form>
</div>
@endsection
