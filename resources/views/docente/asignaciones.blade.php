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

  /* NUEVO: SIDEBAR sticky */
  .sidebar-sticky {
    position: sticky;
    top: 90px;
  }
  .modal {
  z-index: 99999 !important;
}
.modal-backdrop.show {
  z-index: 99998 !important;
}

</style>

<div class="min-h-screen" style="background:var(--color-bg);">
  <header class="page-header py-5 text-center shadow-sm">
    <h2 class="fw-bold mb-1">Mis Estudiantes</h2>
    <div class="text-white-50">
      Gestiona tus módulos base y revisa el avance de cada proyecto.
    </div>
  </header>

  <div class="container-fluid py-4">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif


    {{-- GRID PRINCIPAL: IZQUIERDA -> ESTUDIANTES | DERECHA -> MÓDULOS BASE --}}
    <div class="row">

      {{-- ============================================= --}}
      {{-- 🟦 COLUMNA IZQUIERDA: CREAR MÓDULO + ESTUDIANTES --}}
      {{-- ============================================= --}}
      <div class="col-lg-8 col-md-7">

        {{-- 🔵 PANEL PARA CREAR MÓDULO BASE --}}
        <div class="card card-soft shadow-sm mb-4">
          <div class="card-body">
            <div class="small-label mb-2">Crear módulo base</div>

            <p class="text-muted small mb-3">
              Este módulo se replicará automáticamente para todos tus estudiantes,
              con estado <strong>pendiente</strong> y <strong>sin fecha límite</strong>.
            </p>

            <form class="row g-2"
                  action="{{ route('docente.modulos.store') }}"
                  method="POST">
              @csrf

              <div class="col-md-6">
                <label class="form-label small mb-1">Título *</label>
                <input type="text"
                       name="titulo"
                       class="form-control"
                       placeholder="Módulo 1: Introducción"
                       required>
              </div>

              <div class="col-md-6">
                <label class="form-label small mb-1">Descripción</label>
                <input type="text"
                       name="descripcion"
                       class="form-control"
                       placeholder="Descripción general del módulo">
              </div>

              <div class="col-12 text-end">
                <button class="btn btn-primary mt-2">
                  <i class="bi bi-plus-circle me-1"></i> Crear módulo
                </button>
              </div>
            </form>
          </div>
        </div>


        {{-- 🟩 TARJETAS DE ESTUDIANTES --}}
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

                {{-- PROGRESO GLOBAL --}}
                <div class="text-end">
                  <span class="small-label d-block mb-1">Avance global</span>

                  <span class="badge rounded-pill bg-warning-subtle text-dark border">
                    {{ $progress }}%
                  </span>

                  <div class="progress progress-slim my-2" style="width:220px;">
                    <div class="progress-bar" role="progressbar"
                         style="width: {{ $progress }}%"></div>
                  </div>

                  <div class="small text-muted">
                    {{ $modsOk }}/{{ $modsTotal }} módulos aprobados
                  </div>
                </div>
              </div>

              {{-- BOTONES --}}
              <div class="mt-3 d-flex gap-2 flex-wrap">
                <a class="btn btn-primary"
                   href="{{ route('docente.asignaciones.show', $asignacion->id_asignacion) }}">
                  <i class="bi bi-columns-gap me-1"></i> Gestionar avances
                </a>

                <a class="btn btn-ghost"
                   href="{{ route('docente.avances.index', $asignacion->id_asignacion) }}">
                  <i class="bi bi-folder2-open me-1"></i> Ver avances agrupados
                </a>

                <a class="btn btn-danger"
                   href="{{ route('docente.faltas.asignacion', $asignacion->id_asignacion) }}">
                  <i class="bi bi-exclamation-circle me-1"></i> Faltas del estudiante
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

        {{-- PAGINACIÓN --}}
        @if(method_exists($asignaciones,'links'))
          <div class="mt-3">
            {{ $asignaciones->links() }}
          </div>
        @endif

      </div> {{-- FIN COLUMNA IZQUIERDA --}}



            {{-- 🟧 COLUMNA DERECHA: SIDEBAR DE MÓDULOS BASE --}}
      
{{-- ######################################################## --}}
{{-- 🟧 COLUMNA DERECHA: SIDEBAR DE MÓDULOS BASE --}}
{{-- ######################################################## --}}
<div class="col-lg-4 col-md-5">
    <div class="sidebar-sticky">

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <h6 class="small-label mb-3">Módulos base</h6>

                @php $contadorModulo = 1; @endphp

                @if(isset($modulosBase) && $modulosBase->isNotEmpty())

                    @foreach($modulosBase->groupBy(fn($m)=>$m->titulo.'|'.$m->descripcion) as $key => $grupo)

                        @php
                            $base = $grupo->first();
                            [$titulo,$descr] = explode('|',$key);
                        @endphp

                        <div class="border rounded p-3 mb-3 bg-white shadow-sm">

                            {{-- Título + acciones --}}
                            <div class="d-flex justify-content-between">
                                <div>

                                    {{-- 🔵 Mostrar número de módulo --}}
                                    <strong>Módulo {{ $contadorModulo }} — {{ $titulo }}</strong>

                                    @if($descr)
                                        <div class="text-muted small">{{ $descr }}</div>
                                    @endif

                                    <span class="badge bg-light text-dark mt-1">
                                        {{ $grupo->count() }} estudiantes
                                    </span>
                                </div>

                                <div class="d-flex gap-1">

                                    {{-- EDITAR --}}
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-edit-modulo-{{ $base->id_modulo }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- ELIMINAR --}}
                                    <form method="POST"
                                          action="{{ route('docente.modulos.base.destroy',$base->id_modulo) }}"
                                          onsubmit="return confirm('¿Eliminar este módulo base para TODOS los estudiantes?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </div>

                            {{-- LISTA DE MATERIALES --}}
                            @foreach($base->materiales as $mat)
                                <div class="d-flex justify-content-between small mt-1 p-1 bg-light rounded">

                                    <div>
                                        <i class="bi bi-paperclip me-1"></i>
                                        {{ $mat->titulo ?? strtoupper($mat->tipo) }}
                                    </div>

                                    <div class="d-flex gap-1">

                                        {{-- EDITAR MATERIAL --}}
                                        <button class="btn btn-sm btn-ghost"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-edit-material-{{ $mat->id_material }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- ELIMINAR MATERIAL --}}
                                        <form method="POST"
                                              action="{{ route('docente.modulos.materiales.destroy.global',$mat->id_material) }}"
                                              onsubmit="return confirm('¿Eliminar este material en TODOS los estudiantes?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>

                                    </div>

                                </div>
                            @endforeach


                            {{-- AGREGAR MATERIAL --}}
                            <form class="mt-2"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  action="{{ route('docente.modulos.materiales.store.global',$base->id_modulo) }}">
                                @csrf

                                <select name="tipo" class="form-select form-select-sm mb-1" required>
                                    <option value="pdf">PDF</option>
                                    <option value="enlace">Enlace</option>
                                    <option value="video">Video</option>
                                </select>

                                <input name="titulo" class="form-control form-control-sm mb-1" placeholder="Título">
                                <input name="url" class="form-control form-control-sm mb-1" placeholder="URL">
                                <input type="file" name="archivo" class="form-control form-control-sm mb-1">

                                <button class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-upload"></i> Agregar material
                                </button>

                            </form>

                        </div>

                        @php $contadorModulo++; @endphp
                    @endforeach

                @else
                    <div class="text-muted small">No hay módulos base aún.</div>
                @endif

            </div>
        </div>

    </div>
</div>





    </div> {{-- FIN GRID --}}
  </div>
</div>
{{-- MODALES DE EDICIÓN GLOBAL --}}

@foreach($modulosBase as $mod)
<div class="modal fade" id="modal-edit-modulo-{{ $mod->id_modulo }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form method="POST" action="{{ route('docente.modulos.update.base', $mod->id_modulo) }}">
        @csrf @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title">Editar módulo base</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <label class="form-label small">Título</label>
          <input type="text" name="titulo" value="{{ $mod->titulo }}" class="form-control mb-2">

          <label class="form-label small">Descripción</label>
          <input type="text" name="descripcion" value="{{ $mod->descripcion }}" class="form-control">
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary">Guardar cambios</button>
        </div>

      </form>

    </div>
  </div>
</div>
@endforeach


{{-- MODALES DE EDICIÓN DE MATERIALES --}}
@foreach($modulosBase as $mod)
  @foreach($mod->materiales as $mat)
    <div class="modal fade" id="modal-edit-material-{{ $mat->id_material }}" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <form method="POST"
                action="{{ route('docente.modulos.materiales.update.global', $mat->id_material) }}"
                enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="modal-header">
              <h5 class="modal-title">Editar material</h5>
              <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

              <label class="form-label small">Tipo</label>
              <select name="tipo" class="form-select form-select-sm mb-2">
                <option value="pdf" @selected($mat->tipo=='pdf')>PDF</option>
                <option value="enlace" @selected($mat->tipo=='enlace')>Enlace</option>
                <option value="video" @selected($mat->tipo=='video')>Video</option>
              </select>

              <label class="form-label small">Título</label>
              <input type="text" name="titulo" value="{{ $mat->titulo }}" class="form-control form-control-sm mb-2">

              <label class="form-label small">URL</label>
              <input type="text" name="url" value="{{ $mat->url }}" class="form-control form-control-sm mb-2">

              <label class="form-label small">Archivo (opcional)</label>
              <input type="file" name="archivo" class="form-control form-control-sm">

            </div>

            <div class="modal-footer">
              <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
              <button class="btn btn-primary">
                <i class="bi bi-save"></i> Guardar
              </button>
            </div>

          </form>

        </div>
      </div>
    </div>
  @endforeach
@endforeach

@endsection
