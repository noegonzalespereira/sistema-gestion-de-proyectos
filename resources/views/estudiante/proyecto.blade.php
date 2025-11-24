@extends('layouts.app')
@section('title','Mi Proyecto')

@section('content')
<style>
  :root {
    --color-primary: #2b6cb0;
    --color-primary-dark: #1a365d;
    --color-primary-light: #90cdf4;
    --color-accent: #38b2ac;
    --color-bg: #edf2f7;
    --color-gray: #718096;
    --color-gray-light: #e2e8f0;
    --color-white: #ffffff;
  }

  .page-header {
    background: var(--color-primary-dark);
    color: var(--color-white);
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
  .step-number {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:600;
    margin-right:8px;
  }
  .step-number.pending {
    background:#e2e8f0;
    color:#4a5568;
  }
  .step-number.active {
    background:var(--color-primary);
    color:#fff;
  }
  .step-number.done {
    background:#48bb78;
    color:#fff;
  }
  .module-card {
    border-radius: 16px;
    border:1px solid #e2e8f0;
    background:#fff;
    overflow:hidden;
  }
  .module-header {
    background:#f7fafc;
    cursor:pointer;
  }
  .module-header:hover {
    background:#edf2f7;
  }
  .small-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: var(--color-gray);
  }

  /* Tarjetas de avances del estudiante */
  .avance-card {
    position: relative;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 12px 16px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
  }
  .avance-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    border-radius: 14px 0 0 14px;
    background: #a0aec0; /* default */
  }
  .avance-card.aprobado::before {
    background: #38a169; /* verde */
  }
  .avance-card.observado::before {
    background: #dd6b20; /* naranja */
  }
  .avance-card.entregado::before {
    background: #3182ce; /* azul */
  }
  .avance-title {
    font-weight: 600;
    font-size: 0.92rem;
    color: #1a365d;
  }
  .avance-meta {
    font-size: 0.78rem;
    color: #4a5568;
  }
  .avance-delete-btn {
    border-radius: 999px;
    padding-inline: 0.6rem;
  }
</style>

<div class="min-h-screen" style="background:var(--color-bg);">
  <header class="page-header py-4 shadow-sm text-center">
    <h1 class="h4 fw-bold mb-0">Mi Proyecto Académico</h1>
    <div class="small text-white-50 mt-1">
      Seguimiento de módulos, avances y correcciones
    </div>
  </header>

  <div class="container py-4">
    @if(!$asignacion)
      <div class="alert alert-warning mt-3">
        Aún no tienes un proyecto asignado. Cuando el administrador registre tu tema,
        podrás ver aquí tu progreso.
      </div>
    @else
      {{-- Tarjeta resumen del proyecto --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between flex-wrap gap-3">
            <div>
              <div class="small-label mb-1">Proyecto</div>
              <h4 class="fw-bold mb-1 text-primary">
                {{ $asignacion->titulo_proyecto ?? 'Proyecto sin título' }}
              </h4>
              <div class="text-muted small mb-1">
                <strong>Programa:</strong> {{ $asignacion->programa?->nombre ?? '—' }} ·
                <strong>Carrera:</strong> {{ $asignacion->carrera?->nombre ?? '—' }}
              </div>
              <div class="text-muted small">
                <strong>Tutor:</strong> {{ $asignacion->tutor?->usuario?->name ?? 'No asignado' }}
              </div>
            </div>

            @php
              $modsTotal = $asignacion->modulos?->count() ?? 0;
              $modsOk    = $asignacion->modulos?->where('estado','aprobado')->count() ?? 0;
              $progress  = $modsTotal ? intval($modsOk*100/$modsTotal) : 0;
            @endphp

            <div class="text-end">
              <div class="small-label mb-1">Avance global</div>
              <div class="fw-bold mb-1">{{ $progress }}% completado</div>
              <div class="progress" style="width:220px; height:8px;">
                <div class="progress-bar" style="width: {{ $progress }}%;"></div>
              </div>
              <div class="small text-muted mt-1">
                {{ $modsOk }}/{{ $modsTotal }} módulos aprobados
              </div>
            </div>
          </div>

          @if($asignacion->observacion)
            <hr>
            <div class="small text-muted">
              <span class="small-label d-block mb-1">Observación general</span>
              {{ $asignacion->observacion }}
            </div>
          @endif
        </div>
      </div>

      <div class="row g-4">
        {{-- COLUMNA PRINCIPAL: MÓDULOS --}}
        <div class="col-lg-8">

          <div class="mb-2 d-flex align-items-center gap-2">
            <div class="small-label">Ruta de módulos</div>
            <span class="pill pill-muted">
              Completa el módulo 1, luego el 2, etc. Tu tutor debe aprobar cada uno.
            </span>
          </div>

          @php
            $modulos = $asignacion->modulos ?? collect();
          @endphp

          @if($modulos->isEmpty())
            <div class="alert alert-light border">
              Tu tutor aún no definió los módulos de tu proyecto.
            </div>
          @else
            <div class="accordion" id="accordionModulos">
              @foreach($modulos as $mIndex => $mod)
                @php
                  // Estado visual del módulo
                  $badge = [
                    'pendiente' => 'secondary',
                    'observado' => 'warning',
                    'aprobado'  => 'success'
                  ][$mod->estado ?? 'pendiente'] ?? 'secondary';

                  $stepClass = 'pending';
                  if($mod->estado === 'aprobado') {
                    $stepClass = 'done';
                  } elseif($mIndex === 0 || ($modulos[$mIndex-1]->estado ?? null) === 'aprobado') {
                    $stepClass = 'active';
                  }

                  // Puede subir avances?
                  $puedeSubir = ($mod->estado !== 'aprobado');
                  if ($puedeSubir && $mIndex > 0) {
                    $modAnterior = $modulos[$mIndex - 1];
                    if ($modAnterior->estado !== 'aprobado') {
                      $puedeSubir = false;
                    }
                  }

                  // Mis avances en este módulo
                  $misAvancesModulo = ($mod->avances ?? collect())
                      ->where('id_usuario', auth()->id())
                      ->values();
                @endphp

                <div class="module-card mb-3">
                  <h2 class="accordion-header" id="heading-{{ $mod->id_modulo }}">
                    <button class="accordion-button module-header collapsed" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#mod-{{ $mod->id_modulo }}">
                      <div class="d-flex align-items-center">
                        <div class="step-number {{ $stepClass }}">
                          {{ $mIndex+1 }}
                        </div>
                        <div>
                          <div class="fw-semibold">
                            {{ $mod->titulo }}
                          </div>
                          <div class="small text-muted">
                            {{ $mod->descripcion ?? 'Sin descripción' }}
                          </div>
                          <div class="small mt-1">
                            <span class="pill pill-{{ $badge }} text-uppercase">
                              {{ $mod->estado ?? 'pendiente' }}
                            </span>

                            @if(!is_null($mod->calificacion))
                              <span class="pill pill-muted ms-1">
                                Nota módulo: <strong>{{ number_format($mod->calificacion,2) }}</strong>
                              </span>
                            @endif

                            @if($mod->fecha_limite)
                              <span class="pill pill-muted ms-1">
                                Límite: {{ \Carbon\Carbon::parse($mod->fecha_limite)->format('d/m/Y') }}
                              </span>
                            @endif
                          </div>
                        </div>
                      </div>
                    </button>
                  </h2>

                  <div id="mod-{{ $mod->id_modulo }}"
                       class="accordion-collapse collapse"
                       data-bs-parent="#accordionModulos">
                    <div class="accordion-body">

                      {{-- MATERIAL DEL MÓDULO --}}
                      @if($mod->materiales?->count())
                        <div class="mb-3">
                          <div class="small-label mb-1">Material del módulo</div>
                          <ul class="list-unstyled small mb-0">
                            @foreach($mod->materiales as $mat)
                              <li class="mb-1">
                                <i class="bi bi-paperclip me-1"></i>
                                {{ $mat->titulo ?? strtoupper($mat->tipo) }}
                                @if($mat->url)
                                  · <a href="{{ $mat->url }}" target="_blank">Ver enlace</a>
                                @endif
                                @if($mat->path)
                                  · <a href="{{ asset('storage/'.$mat->path) }}" target="_blank">Descargar archivo</a>
                                @endif
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      @endif

                      {{-- SUBIR AVANCE --}}
                      <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                          <div class="small-label">Nuevo avance</div>
                          @if($puedeSubir)
                            <span class="pill pill-success">Disponible para subir</span>
                          @else
                            <span class="pill pill-warning">
                              No puedes subir avances en este momento
                            </span>
                          @endif
                        </div>

                        <button class="btn btn-sm btn-primary mt-2"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#avanceForm-{{ $mod->id_modulo }}"
                                @if(!$puedeSubir) disabled @endif>
                          <i class="bi bi-upload me-1"></i> Subir avance
                        </button>

                        @if(!$puedeSubir)
                          <div class="small text-danger mt-2">
                            @if($mod->estado === 'aprobado')
                              Este módulo ya está <strong>aprobado</strong>; no puedes subir más avances.
                            @elseif($mIndex > 0 && ($modulos[$mIndex-1]->estado ?? null) !== 'aprobado')
                              Debes tener <strong>aprobado el módulo {{ $mIndex }}</strong> para habilitar este.
                            @else
                              No puedes subir avances en este módulo por el momento.
                            @endif
                          </div>
                        @endif

                        <div class="collapse mt-3" id="avanceForm-{{ $mod->id_modulo }}">
                          @if($puedeSubir)
                            <form method="POST"
                                  action="{{ route('estudiante.modulos.avances.store', $mod->id_modulo) }}"
                                  enctype="multipart/form-data">
                              @csrf
                              <div class="mb-2">
                                <label class="form-label small">Título *</label>
                                <input type="text" name="titulo" class="form-control" required>
                              </div>
                              <div class="mb-2">
                                <label class="form-label small">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2"
                                          placeholder="Describe brevemente qué envías en este avance"></textarea>
                              </div>
                              <div class="mb-2">
                                <label class="form-label small">Archivo (PDF / DOC / ZIP)</label>
                                <input type="file" name="archivo" class="form-control">
                              </div>
                              <div class="text-end">
                                <button class="btn btn-success btn-sm">
                                  Enviar avance
                                </button>
                              </div>
                            </form>
                          @endif
                        </div>
                      </div>

                      {{-- MIS AVANCES EN ESTE MÓDULO --}}
                      <div>
                        <div class="small-label mb-1">Historial de mis avances</div>

                        @if($misAvancesModulo->isEmpty())
                          <div class="small text-muted">
                            Aún no enviaste avances para este módulo.
                          </div>
                        @else
                          <ul class="list-unstyled mb-0">
                            @foreach($misAvancesModulo as $index => $av)
                              @php
                                // Última corrección (la más reciente)
                                $ultimaCor = $av->correcciones
                                  ? $av->correcciones->sortByDesc('created_at')->first()
                                  : null;

                                // Estado visual del avance según la última nota
                                $estadoAvance = 'entregado';
                                if ($ultimaCor && !is_null($ultimaCor->nota)) {
                                  $estadoAvance = $ultimaCor->nota >= 51 ? 'aprobado' : 'observado';
                                }
                              @endphp

                              <li class="avance-card {{ $estadoAvance }}">
                                <div class="d-flex justify-content-between align-items-start gap-3">

                                  {{-- Info principal --}}
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
                                      <a href="{{ asset('storage/'.$av->path) }}" target="_blank"
                                         class="small">
                                        <i class="bi bi-download"></i> Ver archivo enviado
                                      </a>
                                    @endif

                                    {{-- Última corrección --}}
                                    <div class="mt-3 p-2 rounded-3 small"
                                         style="background:#edf2f7; border:1px solid #e2e8f0;">
                                      @if($ultimaCor)
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                          <span class="fw-semibold">Última corrección del tutor:</span>

                                          <span class="pill pill-muted">
                                            {{ $ultimaCor->created_at?->format('d/m/Y H:i') }}
                                          </span>

                                          @if(!is_null($ultimaCor->nota))
                                            <span class="pill pill-success">
                                              Nota: {{ number_format($ultimaCor->nota,2) }}
                                            </span>
                                          @endif

                                          @if($ultimaCor->fecha_limite)
                                            <span class="pill pill-warning">
                                              Límite: {{ \Carbon\Carbon::parse($ultimaCor->fecha_limite)->format('d/m/Y') }}
                                            </span>
                                          @endif
                                        </div>

                                        @if($ultimaCor->comentario)
                                          <div>
                                            <span class="fw-semibold">Comentario:</span>
                                            <span>{{ $ultimaCor->comentario }}</span>
                                          </div>
                                        @endif

                                        @if($ultimaCor->path)
                                          <div class="mt-2">
                                            <a href="{{ asset('storage/'.$ultimaCor->path) }}"
                                               target="_blank" class="small">
                                              <i class="bi bi-paperclip"></i> Ver archivo de corrección
                                            </a>
                                          </div>
                                        @endif
                                      @else
                                        <div class="text-muted">
                                          Tu tutor aún no registró correcciones para este avance.
                                        </div>
                                      @endif
                                    </div>
                                  </div>

                                  {{-- Eliminar avance --}}
                                  <div class="d-flex flex-column align-items-end">
                                    <form method="POST"
                                          action="{{ route('estudiante.avances.destroy', $av->id_avance) }}"
                                          onsubmit="return confirm('¿Eliminar este avance?');">
                                      @csrf
                                      @method('DELETE')
                                      <button class="btn btn-sm btn-outline-danger avance-delete-btn">
                                        <i class="bi bi-trash"></i>
                                      </button>
                                    </form>
                                  </div>

                                </div>
                              </li>
                            @endforeach
                          </ul>
                        @endif
                      </div>

                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>        
    @endif 
   </div> 
</div> 
@endsection
