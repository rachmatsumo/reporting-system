@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">

    <div class="container-fluid">  

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">Custom Script</h5>
                            </div>
                            <a href="{{ route('custom-script.create') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+&nbsp; New Script</a>
                        </div>
                    </div>
                    <div class="card-body">
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
                                            <div class="d-flex gap-2 justify-content-center"> 
                                                <a href="{{ route('custom-script.edit', $script->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                    <i class="fas fa-edit text-secondary"></i>
                                                </a>
                                              <form action="{{ route('custom-script.destroy', $script->id) }}" 
                                                    method="POST" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link p-0 m-0" 
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top" 
                                                            title="Delete">
                                                        <i class="fas fa-trash text-secondary"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr> 
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">No scripts found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</main>
 
@endsection