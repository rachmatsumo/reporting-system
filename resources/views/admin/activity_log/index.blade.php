@extends('layouts.user_type.auth')
@section('title', 'Activity Log')
@section('content')

<div class="container">   

    <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3 py-3 px-1">
        <h5 class="mb-0">Activity Log</h5>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Properties</th>
                    <th>Log</th> 
                    <th>Time</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;            
                @endphp
                @foreach($activities as $a)
                @php
                    $log = $a->description.' '.$a->log_name.' ['.$a->subject_id.']';
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>  
                    <td class="text-wrap"><span class="badge bg-dark text-wrap text-lowercase text-start fw-normal">{{ $a->properties }}</span></td> 
                    <td>{{ ucwords($log) }}</td>  
                    <td>{{ $a->created_at }}</td> 
                    <td>{{ $a->causer?->name }}</td> 
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Pagination links -->
    </div>
    <div class="mt-3">
        {{ $activities->links() }}
    </div>
</div>
@endsection