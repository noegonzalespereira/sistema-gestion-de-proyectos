@extends('layouts.app')
@section('title','Gestión de módulos y avances')

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

  body {
    background: var(--color-bg);
  }

  .page-header {
    background: var(--color-primary-dark);
    color: var(--color-white);
  }
  .small-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--color-gray);
  }

  .summary-card {
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    background: #f7fafc;
    box-shadow: 0 2px 6px rgba(15,23,42,0.08);
  }
  .summary-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a365d;
  }
  .progress-slim {
    height: 8px;
    border-radius: 999px;
    overflow: hidden;
  }

  .pill {
    border-radius: 999px;
    padding: 2px 10px;
    font-size: 11px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .pill-muted {
    background: #e2e8f0;
    color: #4a5568;
  }
  .pill-success {
    background: #c6f6d5;
    color: #22543d;
  }
  .pill-warning {
    background: #fefcbf;
    color: #744210;
  }
  .pill-danger {
    background: #fed7d7;
    color: #822727;
  }

  /* Ruta de módulos (accordion) */
  .module-header {
    background: #f7fafc;
    border-radius: 14px;
    margin-bottom: 8px;
  }
  .accordion-button.module-header:not(.collapsed) {
    background: #ebf4ff;
    box-shadow: none;
  }
  .accordion-button.module-header {
    border-radius: 14px;
    padding-top: 10px;
    padding-bottom: 10px;
  }

  .step-number {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
  }
  .step-number.pending {
    background: #e2e8f0;
    color: #4a5568;
  }
  .step-number.active {
    background: var(--color-primary-light);
    color: #1a365d;
  }
  .step-number.done {
    background: #48bb78;
    color: white;
  }

  .module-body {
    border-radius: 0 0 14px 14px;
    border: 1px solid #e2e8f0;
    border-top: none;
    background: #ffffff;
  }

  /* Avances (tarjetitas) */
  .avance-card {
    position: relative;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 10px 12px;
    margin-bottom: 10px;
  }
  .avance-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    border-radius: 14px 0 0 14px;
    background: #3182ce; /* entregado */
  }
  .avance-card.aprobado::before {
    background:#38a169;
  }
  .avance-card.observado::before {
    background:#dd6b20;
  }
  .avance-title {
    font-weight: 600;
    font-size: 0.9rem;
    color:#1a365d;
  }
  .avance-meta {
    font-size: 0.78rem;
    color:#4a5568;
  }

  .btn-ghost {
    background:#f8fafc;
    border:1px solid #e2e8f0;
  }
</style>

@php
  $asig      = $asignacion;
  $titulo    = $asig->titulo_proyecto ?? 'Proyecto sin título';
  $estu      = $asig->estudiante?->usuario?->name
               ?? $asig->estudiante?->usuario?->nombre ?? '—';
  $carr      = $asig->carrera?->nombre ?? '—';
  $prog      = $asig->programa?->nombre ?? '—';
  $tutorName = $asig->tutor?->usuario?->name ?? '—';

  $modsTotal = $asig->modulos?->count() ?? 0;
  $modsOk    = $asig->modulos?->where('estado','aprobado')->count() ?? 0;
  $progress  = $modsTotal ? intval($modsOk * 100 / $modsTotal) : 0;
@endphp

<div class="min-h-screen">
  {{-- HEADER --}}
  <header class="page-header py-4 shadow-sm mb-3">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <div class="small-label mb-1">Panel docente</div>
        <h4 class="mb-0">Gestión de módulos y avances</h4>
        <div class="small text-white-50">
          Estás revisando el proyecto del estudiante.
        </div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('docente.asignaciones') }}" class="btn btn-light btn-sm">
          <i class="bi bi-arrow-left"></i> Volver a mis asignaciones
        </a>
      </div>
    </div>
  </header>

  <div class="container pb-5">

    {{-- FLASH --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- TARJETA RESUMEN --}}
    <div class="summary-card p-4 mb-4">
      <div class="d-flex justify-content-between flex-wrap gap-3">
        <div class="flex-grow-1">
          <div class="small-label mb-1">Proyecto</div>
          <div class="summary-title mb-1">
            {{ $titulo }}
          </div>
          <div class="small text-muted">
            <strong>Programa:</strong> {{ $prog }} ·
            <strong>Carrera:</strong> {{ $carr }}
          </div>
          <div class="small text-muted mt-1">
            <strong>Estudiante:</strong> {{ $estu }} ·
            <strong>Tutor:</strong> {{ $tutorName }}
          </div>
        </div>
        <div style="min-width:230px;">
          <div class="small-label mb-1">Avance global</div>
          <div class="fw-semibold">
            {{ $progress }}% completado
          </div>
          <div class="progress progress-slim my-2">
            <div class="progress-bar" role="progressbar"
                 style="width: {{ $progress }}%"></div>
          </div>
          <div class="small text-muted">
            {{ $modsOk }}/{{ $modsTotal }} módulos aprobados
          </div>
        </div>
      </div>
    </div>

    {{-- ACCIONES
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <div>
        <span class="small-label d-block">Ruta de módulos</span>
        <div class="text-muted small">
          Crea módulos, revisa avances y envía correcciones con nueva fecha límite.
        </div>
      </div>
      <button class="btn btn-primary" data-bs-toggle="collapse"
              data-bs-target="#panelCrearModulo"
              aria-expanded="false">
        <i class="bi bi-plus-circle me-1"></i> Crear nuevo módulo
      </button>
    </div>

    {{-- PANEL CREAR MÓDULO --}}
    {{-- <div id="panelCrearModulo" class="collapse mb-4"> 
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="small-label mb-2">Nuevo módulo</div>
          <form class="row g-2 align-items-end"
                action="{{ route('docente.modulos.store', $asig->id_asignacion) }}"
                method="POST">
            @csrf
            <div class="col-md-4">
              <label class="form-label mb-0">Título *</label>
              <input class="form-control" name="titulo"
                     placeholder="Módulo 1: Marco teórico" required>
            </div>
            <div class="col-md-4">
              <label class="form-label mb-0">Descripción</label>
              <input class="form-control" name="descripcion"
                     placeholder="Objetivos / contenidos principales">
            </div>
            <div class="col-md-2">
              <label class="form-label mb-0">Fecha límite</label>
              <input type="date" class="form-control" name="fecha_limite">
            </div>
            <div class="col-md-2 d-grid">
              <button class="btn btn-primary mt-3 mt-md-0">
                <i class="bi bi-save me-1"></i> Guardar módulo
              </button>
            </div>
          </form>
        </div>
      </div>
    </div> --}

    {{-- LISTA DE MÓDULOS --}}
    @php
      $modulos = $asig->modulos ?? collect();
    @endphp

    @if($modulos->isEmpty())
      <div class="alert alert-light border">
        Este estudiante aún no tiene módulos definidos.  
        Usa el botón <strong>“Crear nuevo módulo”</strong> para comenzar.
      </div>
    @else
      <div class="accordion" id="accordionModulos-{{ $asig->id_asignacion }}">
        @foreach($modulos as $mIndex => $mod)
          @php
            $badge = [
              'pendiente' => 'secondary',
              'observado' => 'warning',
              'aprobado'  => 'success'
            ][$mod->estado ?? 'pendiente'] ?? 'secondary';

            $estadoLabel      = ucfirst($mod->estado ?? 'pendiente');
            $avancesModulo    = $mod->avances ?? collect();
            $ultimaFechaEnvio = $avancesModulo->sortByDesc('created_at')->first()?->created_at;

            $stepClass = 'pending';
            if ($mod->estado === 'aprobado') {
              $stepClass = 'done';
            } elseif ($mIndex === 0 || ($modulos[$mIndex-1]->estado ?? null) === 'aprobado') {
              $stepClass = 'active';
            }

            $isFirst = $mIndex === 0;
          @endphp

          <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header" id="heading-{{ $mod->id_modulo }}">
              <button
                class="accordion-button module-header {{ $isFirst ? '' : 'collapsed' }}"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mod-{{ $mod->id_modulo }}"
                aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                aria-controls="mod-{{ $mod->id_modulo }}"
              >
                <div class="d-flex align-items-center w-100">
                  <div class="step-number {{ $stepClass }} me-3">
                    {{ $mIndex + 1 }}
                  </div>

                  <div class="flex-grow-1">
                    <div class="small text-muted mb-1">
                      módulo {{ $mIndex + 1 }}
                    </div>
                    <div class="fw-semibold">
                      {{ $mod->titulo }}
                    </div>
                    @if($mod->descripcion)
                      <div class="small text-muted">
                        {{ $mod->descripcion }}
                      </div>
                    @endif

                    <div class="small text-secondary mt-1 d-flex flex-wrap gap-1">
                      <span class="pill
                        @if($badge==='success') pill-success
                        @elseif($badge==='warning') pill-warning
                        @else pill-muted @endif
                        text-uppercase">
                        {{ $estadoLabel }}
                      </span>

                      @if(!is_null($mod->calificacion))
                        <span class="pill pill-muted">
                          Nota módulo: <strong>{{ $mod->calificacion }}</strong>
                        </span>
                      @endif
                      @if($mod->fecha_limite)
                        <span class="pill pill-muted">
                          Límite: {{ \Carbon\Carbon::parse($mod->fecha_limite)->format('d/m/Y') }}
                        </span>
                      @endif
                      @if($ultimaFechaEnvio)
                        <span class="pill pill-muted">
                          Último avance: {{ $ultimaFechaEnvio->format('d/m/Y H:i') }}
                        </span>
                      @endif
                    </div>
                  </div>

                  <form action="{{ route('docente.modulos.destroy', $mod->id_modulo) }}"
                        method="POST"
                        onsubmit="return confirm('¿Eliminar módulo completo?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger ms-2">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </button>
            </h2>

            <div id="mod-{{ $mod->id_modulo }}"
                 class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                 aria-labelledby="heading-{{ $mod->id_modulo }}"
                 data-bs-parent="#accordionModulos-{{ $asig->id_asignacion }}">
              <div class="accordion-body module-body">

                {{-- MATERIALES --}}
                @if($mod->materiales?->count())
                  <div class="mb-3">
                    <div class="small-label mb-1">Materiales del módulo</div>
                    @foreach($mod->materiales as $mat)
                      <div class="small mb-1">
                        <i class="bi bi-paperclip me-1"></i>
                        {{ $mat->titulo ?? strtoupper($mat->tipo) }} —
                        @if($mat->url)
                          <a href="{{ $mat->url }}" target="_blank">Ver enlace</a>
                        @endif
                        @if($mat->path)
                          <a href="{{ asset('storage/'.$mat->path) }}" target="_blank">Descargar archivo</a>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @endif

                {{-- FORM AGREGAR MATERIAL --}}
                <form class="row g-2 mb-3"
                      action="{{ route('docente.modulos.materiales.store', $mod->id_modulo) }}"
                      method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="col-md-3">
                    <select name="tipo" class="form-select form-select-sm" required>
                      <option value="pdf">PDF / Documento</option>
                      <option value="enlace">Enlace Web</option>
                      <option value="video">Video</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <input name="titulo" class="form-control form-control-sm"
                           placeholder="Título (opcional)">
                  </div>
                  <div class="col-md-3">
                    <input name="url" class="form-control form-control-sm"
                           placeholder="URL (si enlace/video)">
                  </div>
                  <div class="col-md-3">
                    <input type="file" name="archivo" class="form-control form-control-sm">
                  </div>
                  <div class="col-12 text-end">
                    <button class="btn btn-sm btn-outline-primary mt-1">
                      <i class="bi bi-upload me-1"></i> Agregar material
                    </button>
                  </div>
                </form>

                <hr>

                {{-- AVANCES --}}
                <div class="mt-2">
                  <div class="small-label mb-1">Avances del estudiante en este módulo</div>

                  @if($avancesModulo->isEmpty())
                    <div class="small text-muted">
                      Aún no hay envíos para este módulo.
                    </div>
                  @else
                    @foreach($avancesModulo as $index => $av)
                      @php
                        $corrsAvance = $av->correcciones ?? collect();
                        $ultimaCor   = $corrsAvance->sortByDesc('created_at')->first();
                        $estadoAvance = 'entregado';
                        if ($ultimaCor && !is_null($ultimaCor->nota)) {
                          $estadoAvance = $ultimaCor->nota >= 51 ? 'aprobado' : 'observado';
                        }
                      @endphp

                      <div class="avance-card {{ $estadoAvance }}">
                        <div class="d-flex justify-content-between gap-3 flex-wrap">
                          {{-- INFO AVANCE --}}
                          <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                              <div class="avance-title">
                                Avance #{{ $index + 1 }} — {{ $av->titulo }}
                              </div>
                              <span class="pill
                                @if($estadoAvance === 'aprobado') pill-success
                                @elseif($estadoAvance === 'observado') pill-warning
                                @else pill-muted @endif
                                text-uppercase">
                                {{ $estadoAvance }}
                              </span>
                            </div>

                            <div class="avance-meta mb-1">
                              Enviado: {{ $av->created_at?->format('d/m/Y H:i') }}
                              @if($av->descripcion)
                                · {{ $av->descripcion }}
                              @endif
                            </div>

                            @if($av->path)
                              <a href="{{ asset('storage/'.$av->path) }}"
                                 target="_blank" class="small">
                                <i class="bi bi-download"></i> Ver archivo enviado
                              </a>
                            @endif

                            {{-- ÚLTIMA CORRECCIÓN --}}
                            <div class="mt-2 small">
                              @if($corrsAvance->isNotEmpty() && $ultimaCor)
                                <div class="fw-semibold mb-1">
                                  Última corrección
                                </div>
                                <div class="mb-1 text-muted">
                                  {{ $ultimaCor->created_at?->format('d/m/Y H:i') }}
                                  @if(!is_null($ultimaCor->nota))
                                    · Nota: <strong>{{ $ultimaCor->nota }}</strong>
                                  @endif
                                  @if($ultimaCor->fecha_limite)
                                    · Nuevo límite:
                                    {{ \Carbon\Carbon::parse($ultimaCor->fecha_limite)->format('d/m/Y') }}
                                  @endif
                                </div>
                                @if($ultimaCor->comentario)
                                  <div class="mb-1">
                                    <span class="fw-semibold">Comentario:</span>
                                    {{ $ultimaCor->comentario }}
                                  </div>
                                @endif
                                @if($ultimaCor->path)
                                  <div>
                                    <a href="{{ asset('storage/'.$ultimaCor->path) }}"
                                       target="_blank" class="small">
                                      <i class="bi bi-paperclip"></i> Ver archivo de corrección
                                    </a>
                                  </div>
                                @endif
                              @else
                                <div class="text-muted">
                                  Aún no registraste correcciones para este avance.
                                </div>
                              @endif
                            </div>
                          </div>

                          {{-- FORM NUEVA CORRECCIÓN --}}
                          <div style="min-width:260px;">
                            <form method="POST"
                                  action="{{ route('docente.avances.correcciones.store', $av->id_avance) }}"
                                  enctype="multipart/form-data"
                                  class="border rounded p-2 bg-white">
                              @csrf
                              <div class="small fw-semibold mb-1">
                                Nueva corrección
                              </div>
                              <div class="mb-1">
                                <input type="number"
                                       name="nota"
                                       step="0.1"
                                       min="0" max="100"
                                       class="form-control form-control-sm"
                                       placeholder="Nota (0-100)">
                              </div>
                              <div class="mb-1">
                                <textarea name="comentario"
                                          rows="2"
                                          class="form-control form-control-sm"
                                          placeholder="Comentario al estudiante"></textarea>
                              </div>
                              <div class="mb-1">
                                <label class="small mb-0">Nueva fecha límite</label>
                                <input type="date"
                                       name="fecha_limite"
                                       class="form-control form-control-sm">
                              </div>
                              <div class="mb-1">
                                <input type="file"
                                       name="archivo"
                                       class="form-control form-control-sm"
                                       accept=".pdf,.doc,.docx,.zip,.rar">
                              </div>
                              <div class="text-end">
                                <button class="btn btn-sm btn-outline-danger">
                                  <i class="bi bi-send"></i> Enviar
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>

                <hr>

                {{-- EVALUAR MÓDULO --}}
                <form class="row g-2 mt-1"
                      action="{{ route('docente.modulos.evaluar', $mod->id_modulo) }}"
                      method="POST">
                  @csrf
                  <div class="col-md-3">
                    <label class="form-label mb-0 small">Estado del módulo</label>
                    <select name="estado" class="form-select form-select-sm">
                      <option value="pendiente" @selected(($mod->estado ?? '')==='pendiente')>Pendiente</option>
                      <option value="observado" @selected(($mod->estado ?? '')==='observado')>Observado</option>
                      <option value="aprobado"  @selected(($mod->estado ?? '')==='aprobado')>Aprobado</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label mb-0 small">Calificación</label>
                    <input type="number" 
                           class="form-control form-control-sm"
                           value="{{ $mod->calificacion }}"
                           readonly
                           style="background:#e2e8f0; cursor:not-allowed;">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label mb-0 small">Fecha límite</label>
                    <input type="date" name="fecha_limite"
                           value="{{ $mod->fecha_limite }}"
                           class="form-control form-control-sm">
                  </div>
                  <div class="col-md-3 d-grid align-end">
                    <button class="btn btn-sm btn-success mt-3">
                      <i class="bi bi-check2-circle me-1"></i>
                      Guardar evaluación
                    </button>
                  </div>
                </form>

              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </div>
</div>
@endsection
