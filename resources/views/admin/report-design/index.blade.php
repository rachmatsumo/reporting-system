@extends('layouts.user_type.auth')
@section('title', 'Report Design')
@section('content')

<div class="container">
  <x-page-header route-prefix="report-designs" mode="index" />
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
                <x-action-dropdown :model="$design" :show="['edit', 'delete']"/>              
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
        
@endsection
