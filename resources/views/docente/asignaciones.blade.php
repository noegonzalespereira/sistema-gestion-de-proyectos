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
  .btn-ghost {
    background:#f8fafc;
    border:1px solid #e2e8f0;
  }
  .progress-slim{ height:8px; }

  .small-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: var(--color-gray);
  }
  .pill {
    border-radius: 999px;
    padding: 2px 10px;
    font-size: 11px;
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
  .module-chip {
    font-size: 0.8rem;
    padding: 2px 8px;
    border-radius: 999px;
    background:#e2e8f0;
    color:#4a5568;
  }

  /* Avances dentro del modal (lado docente) */
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
    background: #3182ce; /* por defecto: enviado */
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
</style>

<div class="min-h-screen" style="background:var(--color-bg);">
  <header class="page-header py-5 text-center shadow-sm">
    <h2 class="fw-bold mb-1">Mis Estudiantes</h2>
    <div class="text-white-50">
      Gestión de módulos, materiales, avances y correcciones
    </div>
  </header>

  <div class="container py-4">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="mb-3">
      <span class="small-label">Resumen</span>
      <div class="text-muted small">
        Desde aquí puedes crear módulos, subir materiales, revisar avances y registrar correcciones
        para cada estudiante.
      </div>
    </div>

    @forelse($asignaciones as $asignacion)
      @php
        $titulo = $asignacion->titulo_proyecto ?? 'Proyecto sin título';
        $estu   = $asignacion->estudiante?->usuario?->name
                  ?? $asignacion->estudiante?->usuario?->nombre ?? '—';
        $carr   = $asignacion->carrera?->nombre ?? '—';
        $prog   = $asignacion->programa?->nombre ?? '—';
        $plazo  = $asignacion->fecha_asignacion
                  ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d \d\e M')
                  : '—';

        $modsTotal = $asignacion->modulos?->count() ?? 0;
        $modsOk    = $asignacion->modulos?->where('estado','aprobado')->count() ?? 0;
        $progress  = $modsTotal ? intval($modsOk * 100 / $modsTotal) : 0;

        $rowId = $asignacion->id_asignacion;
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

          <div class="mt-3 d-flex gap-2 flex-wrap">
            {{-- Ver Módulos (modal) --}}
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalModulos-{{ $rowId }}">
              <i class="bi bi-columns-gap me-1"></i> Ver módulos y calificar
            </button>

            {{-- Revisar Avances (vista agrupada por asignación) --}}
            <a class="btn btn-ghost" href="{{ route('docente.avances.index', $asignacion->id_asignacion) }}">
              <i class="bi bi-folder2-open me-1"></i> Ver avances en lista
            </a>

            {{-- Subir Corrección GENERAL (opcional) --}}
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalCorreccion-{{ $rowId }}">
              <i class="bi bi-file-earmark-arrow-up me-1"></i> Corrección general
            </button>
          </div>
        </div>
      </div>

      {{-- MODAL: MÓDULOS --}}
      <div class="modal fade" id="modalModulos-{{ $rowId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <div>
                <div class="small-label mb-1">Seguimiento del proyecto</div>
                <h5 class="modal-title mb-0">
                  {{ $titulo }}
                  <span class="text-muted">— {{ $estu }}</span>
                </h5>
              </div>
              <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

              {{-- Crear módulo --}}
              <div class="card border-0 mb-3">
                <div class="card-body bg-success-subtle rounded-3">
                  <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <span class="small-label">Nuevo módulo</span>
                    <span class="module-chip">
                      Define el trabajo por etapas (Marco Teórico, Desarrollo, Resultados…)
                    </span>
                  </div>
                  <form class="row g-2 align-items-end"
                        action="{{ route('docente.modulos.store', $asignacion->id_asignacion) }}"
                        method="POST">
                    @csrf
                    <div class="col-md-5">
                      <label class="form-label mb-0">Título *</label>
                      <input class="form-control" name="titulo" placeholder="Módulo X: ..." required>
                    </div>
                    <div class="col-md-5">
                      <label class="form-label mb-0">Descripción</label>
                      <input class="form-control" name="descripcion" placeholder="Objetivos / contenido…">
                    </div>
                    <div class="col-md-2 d-grid">
                      <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Crear
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              {{-- Lista de módulos --}}
              @forelse($asignacion->modulos as $mod)
                @php
                  $badge = [
                    'pendiente' => 'secondary',
                    'observado' => 'warning',
                    'aprobado'  => 'success'
                  ][$mod->estado ?? 'pendiente'] ?? 'secondary';

                  $estadoLabel = ucfirst($mod->estado ?? 'pendiente');
                  $avancesModulo = $mod->avances ?? collect();

                  $ultimaFechaEnvio = $avancesModulo->sortByDesc('created_at')->first()?->created_at;
                @endphp

                <div class="border rounded p-3 mb-3 bg-white">
                  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div class="w-100">
                      <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                          <div class="fw-semibold">
                            {{ $mod->titulo }}
                            <span class="badge bg-{{ $badge }} ms-2 text-uppercase">
                              {{ $estadoLabel }}
                            </span>
                            @if($mod->estado === 'aprobado')
                              <span class="badge bg-success-subtle text-success border ms-1">
                                Módulo completado
                              </span>
                            @endif
                          </div>
                          @if($mod->descripcion)
                            <div class="small text-muted">{{ $mod->descripcion }}</div>
                          @endif
                          <div class="small text-secondary mt-1">
                            Creado: {{ $mod->created_at?->format('d/m/Y') }}
                            @if($mod->fecha_limite)
                              • Límite: {{ \Carbon\Carbon::parse($mod->fecha_limite)->format('d/m/Y') }}
                            @endif
                            @if(!is_null($mod->calificacion))
                              • Nota módulo: <strong>{{ $mod->calificacion }}</strong>
                            @endif
                            @if($ultimaFechaEnvio)
                              • Último avance: {{ $ultimaFechaEnvio->format('d/m/Y H:i') }}
                            @endif
                          </div>
                        </div>

                        {{-- Eliminar módulo --}}
                        <form action="{{ route('docente.modulos.destroy', $mod->id_modulo) }}"
                              method="POST" onsubmit="return confirm('¿Eliminar módulo completo?');">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </div>

                      {{-- Materiales del módulo --}}
                      @if($mod->materiales?->count())
                        <div class="mt-2">
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

                      {{-- Subir material --}}
                      <form class="row g-2 mt-2"
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

                      {{-- AVANCES DEL ESTUDIANTE EN ESTE MÓDULO --}}
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

                              // Estado visual del avance
                              $estadoAvance = 'entregado';
                              if ($ultimaCor && !is_null($ultimaCor->nota)) {
                                  $estadoAvance = $ultimaCor->nota >= 51 ? 'aprobado' : 'observado';
                              }
                            @endphp

                            <div class="avance-card {{ $estadoAvance }}">
                              <div class="d-flex justify-content-between gap-3 flex-wrap">

                                {{-- Info del avance --}}
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

                                  {{-- Historial resumido de correcciones --}}
                                  <div class="mt-2 small">
                                    @if($corrsAvance->isNotEmpty())
                                      <div class="fw-semibold mb-1">
                                        Correcciones registradas ({{ $corrsAvance->count() }})
                                      </div>
                                      @if($ultimaCor)
                                        <div class="mb-1 text-muted">
                                          Última: {{ $ultimaCor->created_at?->format('d/m/Y H:i') }}
                                          @if(!is_null($ultimaCor->nota))
                                            · Nota: <strong>{{ $ultimaCor->nota }}</strong>
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
                                      @endif
                                    @else
                                      <div class="text-muted">
                                        Aún no registraste correcciones para este avance.
                                      </div>
                                    @endif
                                  </div>
                                </div>

                                {{-- Formulario nueva corrección --}}
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

                      {{-- Evaluar módulo (estado + nota general del módulo) --}}
                      <form class="row g-2 mt-1"
                            action="{{ route('docente.modulos.evaluar', $mod->id_modulo) }}"
                            method="POST" id="formEvaluar-{{ $mod->id_modulo }}">
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
                          <input type="number" step="0.1" min="0" max="100" name="calificacion"
                                 value="{{ $mod->calificacion }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label mb-0 small">Fecha límite</label>
                          <input type="date" name="fecha_limite" value="{{ $mod->fecha_limite }}"
                                 class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 d-grid align-end">
                          <button class="btn btn-sm btn-success mt-3">
                            <i class="bi bi-check2-circle me-1"></i>
                            Guardar evaluación
                          </button>
                        </div>
                      </form>

                      <div class="small text-muted mt-1">
                        🔔 Cuando marques el estado como <strong>“Aprobado”</strong>,
                        el estudiante verá este módulo como <strong>completado</strong> en su panel.
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <div class="text-muted">No hay módulos aún.</div>
              @endforelse

            </div>

            <div class="modal-footer">
              <button class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>

      {{-- MODAL: Subir corrección general (opcional) --}}
      <div class="modal fade" id="modalCorreccion-{{ $rowId }}" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <form action="{{ route('docente.avances.correcciones.store', $asignacion->id_asignacion) }}"
                  method="POST" enctype="multipart/form-data">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title">
                  <i class="bi bi-clipboard-check me-2"></i>
                  Corrección / observación general del proyecto
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Comentario</label>
                  <textarea name="comentario" rows="4" class="form-control"
                            placeholder="Observaciones globales sobre el proyecto"></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Archivo (opcional)</label>
                  <input type="file" name="archivo" class="form-control"
                         accept=".pdf,.doc,.docx,.zip,.rar">
                </div>
                <div class="mb-3">
                  <label class="form-label">Fecha límite (si aplica)</label>
                  <input type="date" name="fecha_limite" class="form-control">
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger">Enviar corrección</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    @empty
      <div class="text-center text-muted py-5">
        <i class="bi bi-archive fs-3 d-block mb-2"></i>
        No tienes asignaciones todavía.
      </div>
    @endforelse

    {{-- Paginación --}}
    @if(method_exists($asignaciones,'links'))
      <div class="mt-3">
        {{ $asignaciones->links() }}
      </div>
    @endif

  </div>
</div>
@endsection
