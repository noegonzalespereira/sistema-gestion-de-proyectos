@extends('layouts.app')
@section('title','Revisar Avances')

@section('content')
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Revisar Avances — <span class="text-muted">{{ $proyecto->titulo ?? 'Proyecto' }}</span></h5>
    </div>
    <div class="card-body">
      @forelse($avances as $av)
        <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
          <div>
            <div class="fw-semibold">{{ $av->titulo }}</div>
            <div class="small text-muted">
              Subido {{ $av->created_at?->diffForHumans() }} • por {{ $av->usuario?->name ?? '—' }}
            </div>
          </div>
          <div>
            @if($av->path)
              <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset('storage/'.$av->path) }}">Ver</a>
            @else
              <span class="badge bg-secondary">Sin archivo</span>
            @endif
          </div>
        </div>
      @empty
        <div class="text-muted">Aún no hay avances cargados.</div>
      @endforelse
    </div>
    <div class="card-footer bg-white">
      <a href="{{ route('docente.asignaciones') }}" class="btn btn-light">Volver</a>
    </div>
  </div>
</div>
@endsection
