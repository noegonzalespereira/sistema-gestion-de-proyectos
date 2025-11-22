@extends('layouts.app')
@section('title','Mi Proyecto')

@section('content')
<div class="min-h-screen bg-gray-50">
  <header class="py-5 shadow text-white text-center" style="background:#1a365d;">
    <h1 class="h3 fw-bold mb-0">Mi Proyecto Académico</h1>
  </header>

  <div class="container py-4">
    @if(!$asignacion)
      <div class="alert alert-warning mt-3">
        No tienes un proyecto asignado todavía.
      </div>
    @else
      {{-- Tarjeta principal de proyecto --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h4 class="fw-bold text-primary mb-1">
            {{ $asignacion->titulo_proyecto ?? 'Proyecto sin título' }}
          </h4>
          <div class="text-muted small mb-2">
            <strong>Programa:</strong> {{ $asignacion->programa?->nombre ?? '—' }} ·
            <strong>Carrera:</strong> {{ $asignacion->carrera?->nombre ?? '—' }} ·
            <strong>Tutor:</strong> {{ $asignacion->tutor?->usuario->name ?? 'No asignado' }}
          </div>
          @if($asignacion->observacion)
            <p class="mb-0">{{ $asignacion->observacion }}</p>
          @endif
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          {{-- MÓDULOS --}}
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
              <strong>Módulos del Proyecto</strong>
            </div>
            <div class="card-body">

              @php
                $modulos = $asignacion->modulos ?? collect();
              @endphp

              @if($modulos->isEmpty())
                <div class="alert alert-light border">
                  Aún no hay módulos definidos por tu tutor.
                </div>
              @else
                <div class="accordion" id="accordionModulos">
                  @foreach($modulos as $mIndex => $mod)
                    @php
                      // avances del estudiante en ESTE módulo
                      $misAvancesModulo = ($mod->avances ?? collect())
                          ->where('id_usuario', auth()->id());
                    @endphp

                    <div class="accordion-item mb-2 border rounded">
                      <h2 class="accordion-header" id="heading-{{ $mod->id_modulo }}">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#mod-{{ $mod->id_modulo }}">
                          <div>
                            <div class="fw-semibold">{{ $mod->titulo }}</div>
                            <div class="small text-muted">
                              {{ $mod->descripcion ?? 'Sin descripción' }}
                            </div>
                          </div>
                        </button>
                      </h2>

                      <div id="mod-{{ $mod->id_modulo }}"
                           class="accordion-collapse collapse"
                           data-bs-parent="#accordionModulos">
                        <div class="accordion-body">
                          <div class="small text-secondary mb-2">
                            Estado:
                            @php
                              $badge = ['pendiente'=>'secondary','observado'=>'warning','aprobado'=>'success'][$mod->estado ?? 'pendiente'] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badge }} text-uppercase">
                              {{ $mod->estado ?? 'pendiente' }}
                            </span>
                            @if(!is_null($mod->calificacion))
                              · Nota: <span class="badge bg-info text-dark">{{ $mod->calificacion }}</span>
                            @endif
                            @if($mod->fecha_limite)
                              · Límite: {{ \Carbon\Carbon::parse($mod->fecha_limite)->format('d/m/Y') }}
                            @endif
                          </div>

                          {{-- Materiales del módulo (subidos por el docente) --}}
                          @if($mod->materiales?->count())
                            <div class="mb-3">
                              <div class="small fw-semibold mb-1">Materiales del módulo</div>
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

                          <hr>

                          {{-- FORMULARIO: subir avance a este módulo --}}
                          <div class="mb-3">
                            <button class="btn btn-sm btn-primary"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#avanceForm-{{ $mod->id_modulo }}">
                              <i class="bi bi-upload me-1"></i> Subir avance
                            </button>

                            <div class="collapse mt-3" id="avanceForm-{{ $mod->id_modulo }}">
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
                                  <textarea name="descripcion" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="mb-2">
                                  <label class="form-label small">Archivo (opcional)</label>
                                  <input type="file" name="archivo" class="form-control">
                                </div>
                                <div class="text-end">
                                  <button class="btn btn-success btn-sm">
                                    Enviar avance
                                  </button>
                                </div>
                              </form>
                            </div>
                          </div>

                          {{-- MIS AVANCES EN ESTE MÓDULO --}}
                          <div>
                            <div class="small fw-semibold mb-1">Mis envíos a este módulo</div>

                            @if($misAvancesModulo->isEmpty())
                              <div class="small text-muted">Aún no subiste avances para este módulo.</div>
                            @else
                              <ul class="list-group list-group-flush">
                                @foreach($misAvancesModulo as $av)
                                  <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                      <div class="fw-semibold small">{{ $av->titulo }}</div>
                                      <div class="small text-muted">
                                        {{ $av->created_at?->format('d/m/Y H:i') }}
                                        @if($av->descripcion)
                                          · {{ $av->descripcion }}
                                        @endif
                                      </div>
                                      @if($av->path)
                                        <a href="{{ asset('storage/'.$av->path) }}" target="_blank"
                                           class="small">
                                          <i class="bi bi-download"></i> Ver archivo
                                        </a>
                                      @endif
                                    </div>
                                    <div class="d-flex gap-1">
                                      {{-- Si luego quieres edición real, apuntas aquí a un modal o formulario --}}
                                      {{-- <a href="#" class="btn btn-sm btn-outline-secondary">Editar</a> --}}

                                      <form method="POST"
                                            action="{{ route('estudiante.avances.destroy', $av->id_avance) }}"
                                            onsubmit="return confirm('¿Eliminar este avance?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                          <i class="bi bi-trash"></i>
                                        </button>
                                      </form>
                                    </div>
                                  </li>
                                @endforeach
                              </ul>
                            @endif
                          </div>

                        </div> {{-- /accordion-body --}}
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Correcciones del tutor --}}
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
              <strong>Correcciones del Tutor</strong>
            </div>
            <div class="card-body">
              @php
                $correcciones = $asignacion->correcciones ?? collect();
              @endphp

              @if($correcciones->isEmpty())
                <div class="alert alert-light border">
                  No tienes correcciones aún.
                </div>
              @else
                <ul class="list-group list-group-flush">
                  @foreach($correcciones as $c)
                    <li class="list-group-item">
                      <div class="small text-muted">
                        {{ $c->created_at?->format('d/m/Y') }} —
                        {{ $c->tutor?->usuario?->name ?? 'Tutor' }}
                      </div>
                      <div>{{ $c->comentario ?: '—' }}</div>
                      @if($c->fecha_limite)
                        <div class="small mt-1">
                          <i class="bi bi-calendar-event"></i>
                          Límite: {{ \Carbon\Carbon::parse($c->fecha_limite)->format('d/m/Y') }}
                        </div>
                      @endif
                      @if($c->path)
                        <a class="btn btn-sm btn-outline-secondary mt-2"
                           target="_blank"
                           href="{{ asset('storage/'.$c->path) }}">
                          <i class="bi bi-paperclip"></i> Ver archivo
                        </a>
                      @endif
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
