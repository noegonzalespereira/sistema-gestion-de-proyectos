@extends('layouts.app')
@section('title','Faltas y Retrasos')

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

  body{
    background: var(--color-bg);
  }

  .page-header {
    background: var(--color-primary-dark);
    color: white;
  }

  .card-soft {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: var(--color-white);
    box-shadow: 0 2px 6px rgba(15,23,42,0.10);
  }

  .small-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: var(--color-gray);
  }

  .pill {
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
  }
  .pill-danger {
    background:#fed7d7;
    color:#822727;
  }
  .pill-success {
    background:#c6f6d5;
    color:#22543d;
  }
  .pill-warning {
    background:#fefcbf;
    color:#744210;
  }
</style>

<header class="page-header py-4 shadow-sm mb-4">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
      <div class="small-label">Panel docente</div>
      <h4 class="mb-0">Faltas y retrasos del estudiante</h4>
      <div class="small text-white-50">Control de entregas fuera de fecha y módulos bloqueados</div>
    </div>
    <a href="{{ route('docente.asignaciones') }}" class="btn btn-light btn-sm">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>
</header>

<div class="container pb-5">

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="card-soft p-4">
    <div class="small-label mb-2">Listado de faltas</div>

    @if($faltas->isEmpty())
      <div class="text-muted py-4 text-center">
        <i class="bi bi-clipboard-x fs-2 mb-2 d-block"></i>
        No existen faltas registradas.
      </div>
    @else
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th>Módulo</th>
              <th>Estudiante</th>
              <th>Fecha límite</th>
              <th>Estado</th>
              <th>Motivo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($faltas as $falta)
            <tr>
              <td>
                <strong>{{ $falta->modulo->titulo }}</strong><br>
                <span class="small text-muted">
                  Asignación: #{{ $falta->asignacion->id_asignacion }}
                </span>
              </td>
              <td>
                {{ $falta->estudiante->usuario->name }}
              </td>
              <td>
                {{ \Carbon\Carbon::parse($falta->fecha_limite_original)->format('d/m/Y') }}
              </td>
              <td>
                @if($falta->bloqueado)
                  <span class="pill pill-danger">Bloqueado</span>
                @else
                  <span class="pill pill-success">Rehabilitado</span>
                @endif
              </td>
              <td class="text-muted small">
                {{ $falta->motivo }}
              </td>
              <td>
                @if($falta->bloqueado)
                <button
                  data-bs-toggle="modal"
                  data-bs-target="#rehabilitarModal-{{ $falta->id_falta }}"
                  class="btn btn-sm btn-primary">
                  <i class="bi bi-unlock"></i> Rehabilitar
                </button>

                @include('docente.faltas._rehabilitar_modal', ['falta' => $falta])
                @else
                <span class="text-muted small">—</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

  </div>
</div>

@endsection
