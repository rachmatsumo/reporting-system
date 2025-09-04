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
                  <h5 class="mb-0">Report Design</h5>
              </div>
              <a href="{{ route('report-design.create') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+&nbsp; New Design</a> 
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive p-0">
              <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        {{-- <th>Jumlah Field</th> --}}
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportDesigns as $design)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $design->name }}</td>
                        <td>{{ $design->description ?? '-' }}</td>
                        {{-- <td>{{ $design->fields->count() }}</td> --}}
                        <td>
                            <span class="badge bg-{{ $design->is_active ? 'success' : 'secondary' }}">
                                {{ $design->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>{{ $design->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('report-design.edit', $design) }}" 
                                   data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                   <i class="fas fa-edit text-secondary"></i>
                                </a> 

                                <form action="{{ route('report-design.destroy', $design) }}" 
                                    method="POST" 
                                    class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
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
                        <td colspan="6" class="text-center">Belum ada report design</td>
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
