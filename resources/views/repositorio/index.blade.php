@extends('layouts.app')
@section('title', 'Repositorio Digital de Proyectos')

@section('content')
<div class="min-h-screen bg-gray-50">

  <!-- 🔷 Header -->
  <header class="header-gradient py-6 shadow text-black text-center">
    <h1 class="text-3xl font-bold mb-2">Búsqueda Avanzada de Proyectos</h1>
    <p class="text-blue-100">Encuentra proyectos académicos usando múltiples criterios de búsqueda</p>
  </header>

  <!-- 🔍 Buscador -->
  <div class="container my-5">
    <form method="GET" action="{{ route('repositorio.buscar') }}" class="bg-white shadow-lg p-4 rounded-3">
      <div class="input-group mb-3">
        <input type="text" name="texto" class="form-control" placeholder="Buscar por título, autor, tutor, palabras clave..." value="{{ request('texto') }}">
        <button class="btn btn-success px-4" type="submit">Buscar</button>
        <button class="btn btn-outline-primary px-4" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados">
          <i class="bi bi-sliders"></i> Filtros
        </button>
      </div>

      <!-- 🎛️ Filtros Avanzados (opcionales) -->
      <div class="collapse" id="filtrosAvanzados">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label fw-bold text-primary">Carrera</label>
            <select name="id_carrera" class="form-select">
              <option value="">Todas</option>
              @foreach($carreras as $c)
              <option value="{{ $c->id_carrera }}" {{ request('id_carrera') == $c->id_carrera ? 'selected' : '' }}>
                {{ $c->nombre }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold text-primary">Programa</label>
            <select name="id_programa" class="form-select">
              <option value="">Todos</option>
              @foreach($programas as $p)
              <option value="{{ $p->id_programa }}" {{ request('id_programa') == $p->id_programa ? 'selected' : '' }}>
                {{ $p->nombre }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold text-primary">Año</label>
            <input type="number" name="anio" class="form-control" placeholder="2025" value="{{ request('anio') }}">
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold text-primary">Tutor</label>
            <select name="id_tutor" class="form-select">
              <option value="">Todos</option>
              @foreach($tutores as $t)
              <option value="{{ $t->id_tutor }}" {{ request('id_tutor') == $t->id_tutor ? 'selected' : '' }}>
                {{ $t->usuario->name }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Aplicar Filtros</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- 🗂 Resultados -->
  <div class="container mb-5">
    @if(isset($proyectos) && $proyectos->count())
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
      <div class="alert alert-info text-center mt-4">
        <i class="bi bi-search"></i> No se encontraron proyectos registrados.
      </div>
    @endif
  </div>
</div>
@endsection
