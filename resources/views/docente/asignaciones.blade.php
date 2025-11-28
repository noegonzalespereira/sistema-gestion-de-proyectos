@extends('layouts.app')
@section('title','Mis Asignaciones')

@section('content')
<style>
  :root {
    --color-primary: #2b6cb0;
    --color-primary-dark: #1a365d;
    --color-primary-light:#90cdf4;
    --color-accent:   #38b2ac;
    --color-bg:       #edf2f7;
    --color-gray:     #718096;
    --color-gray-light:#e2e8f0;
    --color-white:    #ffffff;
  }

  .page-header {
    background: var(--color-primary-dark);
    color: var(--color-white);
  }
  .card-soft {
    border: 1px solid #edf2f7;
    border-radius: 16px;
  }
  .progress-slim{ height:8px; }
  .small-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: var(--color-gray);
  }
  .btn-ghost {
    background:#f8fafc;
    border:1px solid #e2e8f0;
  }
</style>

<div class="min-h-screen" style="background:var(--color-bg);">
  <header class="page-header py-5 text-center shadow-sm">
    <h2 class="fw-bold mb-1">Mis Estudiantes</h2>
    <div class="text-white-50">
      Selecciona un proyecto para gestionar sus módulos y avances
    </div>
  </header>

  <div class="container py-4">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @forelse($asignaciones as $asignacion)
      @php
        $titulo = $asignacion->titulo_proyecto ?? 'Proyecto sin título';
        $estu   = $asignacion->estudiante?->usuario?->name
                  ?? $asignacion->estudiante?->usuario?->nombre ?? '—';
        $carr   = $asignacion->carrera?->nombre ?? '—';
        $prog   = $asignacion->programa?->nombre ?? '—';
        $plazo  = $asignacion->fecha_asignacion
                  ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y')
                  : '—';

        $modsTotal = $asignacion->modulos?->count() ?? 0;
        $modsOk    = $asignacion->modulos?->where('estado','aprobado')->count() ?? 0;
        $progress  = $modsTotal ? intval($modsOk * 100 / $modsTotal) : 0;
      @endphp

      <div class="card card-soft shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between flex-wrap gap-3">
            <div>
              <div class="small-label mb-1">Proyecto</div>
              <h5 class="mb-1">{{ $titulo }}</h5>
              <div class="small text-muted">
                <span class="text-success">●</span> {{ $estu }}
              </div>
              <div class="small text-secondary">
                Carrera: {{ $carr }} – {{ $prog }}
              </div>
              <div class="small">
                Asignado: <strong>{{ $plazo }}</strong>
              </div>
            </div>

            <div class="text-end">
              <span class="small-label d-block mb-1">Avance global</span>
              <span class="badge rounded-pill bg-warning-subtle text-dark border">
                {{ $progress }}% completado
              </span>
              <div class="progress progress-slim my-2" style="width:220px;">
                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%"></div>
              </div>
              <div class="small text-muted">
                {{ $modsOk }}/{{ $modsTotal }} módulos aprobados
              </div>
            </div>
          </div>

          {{-- BOTONES DE ACCIÓN --}}
          <div class="mt-3 d-flex gap-2 flex-wrap">

            {{-- GESTIONAR MÓDULOS --}}
            <a class="btn btn-primary"
               href="{{ route('docente.asignaciones.show', $asignacion->id_asignacion) }}">
              <i class="bi bi-columns-gap me-1"></i>
              Gestionar módulos y avances
            </a>

            {{-- VER AVANCES AGRUPADOS --}}
            <a class="btn btn-ghost"
               href="{{ route('docente.avances.index', $asignacion->id_asignacion) }}">
              <i class="bi bi-folder2-open me-1"></i>
              Ver avances agrupados
            </a>

            {{-- NUEVO: VER FALTAS DEL ESTUDIANTE --}}
            <a class="btn btn-danger"
               href="{{ route('docente.faltas.asignacion', $asignacion->id_asignacion) }}">
              <i class="bi bi-exclamation-circle me-1"></i>
              Ver faltas del estudiante
            </a>

          </div>
        </div>
      </div>
    @empty
      <div class="text-center text-muted py-5">
        <i class="bi bi-archive fs-3 d-block mb-2"></i>
        No tienes asignaciones todavía.
      </div>
    @endforelse

    @if(method_exists($asignaciones,'links'))
      <div class="mt-3">
        {{ $asignaciones->links() }}
      </div>
    @endif

  </div>
</div>
@endsection
