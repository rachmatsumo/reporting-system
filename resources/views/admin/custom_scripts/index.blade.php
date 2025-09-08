@extends('layouts.user_type.auth')
@section('title', 'Custom Script')
@section('content')

<div class="container">
    <x-page-header route-prefix="custom-scripts" mode="index" />
 
    <div class="table-responsive p-0">
        <table class="table align-items-center mb-0 table-hover table-striped ">
            <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                        No
                    </th> 
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                        Script Name
                    </th> 
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                        Status
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                        Creation Date
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($scripts as $script)
                {{-- @php dd($script); @endphp --}}
                <tr>
                    <td class="ps-4">
                        <p class="text-xs font-weight-bold mb-0">{{ $no++ }}</p>
                    </td>  
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $script->name }}</p>
                    </td> 
                    <td class="text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ $script->is_active == 1 ? 'Active' : 'Non-Active' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ $script->created_at }}</span>
                    </td>
                    <td class="text-center"> 
                        <x-action-dropdown :model="$script" :show="['edit', 'delete']"/>
                    </td>
                </tr> 
                @empty
                <tr>
                    <td colspan="5" class="text-center">
                        <p class="text-xs font-weight-bold mb-0">No scripts found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
                

</div>
 
 
@endsection