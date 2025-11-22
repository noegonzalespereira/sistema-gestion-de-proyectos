@extends('layouts.app')
@section('title', 'Resultados de Búsqueda')

@section('content')
<div class="min-h-screen bg-gray-50">
  <!-- 🔷 Header -->
  <header class="header-gradient py-6 shadow text-white text-center">
    <h1 class="text-3xl font-bold mb-2">Resultados de Búsqueda</h1>
    <p class="text-blue-100">Encuentra proyectos académicos relevantes</p>
  </header>

  <div class="container py-5">
    @if($proyectos->count() > 0)
      <h5 class="text-muted mb-4">Se encontraron {{ $proyectos->count() }} proyecto(s)</h5>

      @foreach($proyectos as $p)
      <div class="card shadow-sm mb-4 border-0">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <span class="badge bg-success me-2">{{ $p->programa->nombre }}</span>
            <span class="badge bg-info text-dark">{{ $p->carrera->nombre }}</span>
            <h5 class="fw-bold mt-2 text-primary">{{ $p->titulo }}</h5>
            <p class="text-muted mb-1">
              <strong>Autor:</strong> {{ $p->estudiante->usuario->name ?? 'No registrado' }} |
              <strong>Tutor:</strong> {{ $p->tutor->usuario->name ?? 'No asignado' }} |
              <strong>Defensa:</strong> {{ $p->fecha_defensa }}
            </p>
          </div>
          <div class="text-end">
            <span class="badge bg-warning text-dark fs-6">{{ $p->calificacion ?? '—' }}</span><br>
            <a href="{{ asset('storage/'.$p->link_pdf) }}" target="_blank" class="btn btn-outline-success btn-sm mt-2">
              <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $p->id_proyecto }}">
              Ver Detalles
            </button>
          </div>
        </div>
      </div>

      <!-- 📋 Modal Detalle -->
      <div class="modal fade" id="modalDetalle{{ $p->id_proyecto }}" tabindex="-1" aria-labelledby="detalleLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="detalleLabel"><i class="bi bi-info-circle"></i> Detalles del Proyecto</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <p><strong>Título del Proyecto:</strong><br>{{ $p->titulo }}</p>
              <p><strong>Tipo de Proyecto:</strong> {{ $p->programa->nombre }}</p>
              <p><strong>Carrera:</strong> {{ $p->carrera->nombre }}</p>
              <p><strong>Autor(es):</strong> {{ $p->estudiante->usuario->name ?? 'No registrado' }}</p>
              <p><strong>Tutor:</strong> {{ $p->tutor->usuario->name ?? 'No asignado' }}</p>
              <p><strong>Fecha de Defensa:</strong> {{ $p->fecha_defensa }}</p>
              <p><strong>Calificación Final:</strong> {{ $p->calificacion ?? '—' }}/100</p>
              <p><strong>Resumen:</strong><br>{{ $p->resumen }}</p>
              <p><strong>Palabras Clave:</strong> {{ $p->palabras_clave ?? 'No registradas' }}</p>
            </div>
            <div class="modal-footer">
              <a href="{{ asset('storage/'.$p->link_pdf) }}" target="_blank" class="btn btn-success">
                <i class="bi bi-file-earmark-arrow-down"></i> Descargar PDF
              </a>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    @else
      <div class="alert alert-info text-center py-4 shadow-sm rounded-3">
        <i class="bi bi-search text-primary fs-4"></i>
        <p class="mt-2 mb-0">No se encontraron proyectos con los criterios seleccionados.</p>
      </div>
    @endif

    <div class="text-center mt-4">
      <a href="{{ route('repositorio.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Volver al Buscador
      </a>
    </div>
  </div>
</div>
@endsection
