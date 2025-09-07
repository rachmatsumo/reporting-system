@extends('layouts.user_type.auth')
@section('title', 'Menu')
@section('content')
<div class="container">
    <x-page-header route-prefix="menus" mode="index" />
 
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="10%">Order</th>
                    <th>Title</th>
                    <th>Route</th>
                    <th>Permission</th>
                    <th>Icon</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($menus as $menu)
                    {{-- Parent --}}
                    <tr class="table-success fw-semibold">
                        <td>{{ $menu->order }}</td>
                        <td>{{ $menu->title }}</td>
                        <td>{{ $menu->route }}</td>
                        <td>{{ $menu->permission }}</td>
                        <td><i class="bi bi-{{ $menu->icon }}"></i></td>
                        <td>
                            <x-action-dropdown :model="$menu" :show="['edit', 'delete']"/>
                        </td>
                    </tr>

                    {{-- Children --}}
                    @foreach($menu->children as $child)
                        <tr>
                            <td class="ps-4">— {{ $child->order }}</td>
                            <td>{{ $child->title }}</td>
                            <td>{{ $child->route }}</td>
                            <td>{{ $child->permission }}</td>
                            <td><i class="bi bi-{{ $child->icon }}"></i></td>
                            <td>
                                <x-action-dropdown :model="$child" :show="['edit', 'delete']"/>
                            </td>
                        </tr>

                        {{-- Subchildren --}}
                        @foreach($child->children as $subchild)
                            <tr>
                                <td class="ps-5">—— {{ $subchild->order }}</td>
                                <td>{{ $subchild->title }}</td>
                                <td>{{ $subchild->route }}</td>
                                <td>{{ $subchild->permission }}</td>
                                <td><i class="bi bi-{{ $subchild->icon }}"></i></td>
                                <td>
                                    <x-action-dropdown :model="$subchild" :show="['edit', 'delete']"/>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div> 
</div>
@endsection
