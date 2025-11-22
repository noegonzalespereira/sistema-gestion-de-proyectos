@extends('layouts.app')
@section('title','Mis Asignaciones')

@section('content')
<style>
  .card-soft { border: 1px solid #edf2f7; border-radius:14px; }
  .btn-ghost { background:#f8fafc; border:1px solid #e2e8f0; }
  .progress-slim{ height:8px; }
</style>

<div class="min-h-screen bg-gray-50">
  <header class="py-5 text-white text-center" style="background:#0a2b6b;">
    <h2 class="fw-bold mb-1">Mis Estudiantes</h2>
    <div class="text-white-50">Seguimiento por módulos, avances y correcciones</div>
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
                  ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d \d\e M')
                  : '—';

        // progreso por módulos aprobados (por asignación)
        $modsTotal = $asignacion->modulos?->count() ?? 0;
        $modsOk    = $asignacion->modulos?->where('estado','aprobado')->count() ?? 0;
        $progress  = $modsTotal ? intval($modsOk * 100 / $modsTotal) : 0;

        $rowId = $asignacion->id_asignacion;
      @endphp

      <div class="card card-soft shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h5 class="mb-1">{{ $titulo }}</h5>
              <div class="small text-muted">
                <span class="text-success">●</span> {{ $estu }}
              </div>
              <div class="small text-secondary">Carrera: {{ $carr }} – {{ $prog }}</div>
              <div class="small">Asignado: <strong>{{ $plazo }}</strong></div>
            </div>
            <div class="text-end">
              <span class="badge rounded-pill bg-warning-subtle text-dark border">
                {{ $progress }}% completado
              </span>
              <div class="small text-muted mt-1">
                {{ $modsOk }}/{{ $modsTotal }} módulos aprobados
              </div>
            </div>
          </div>

          <div class="progress progress-slim my-3">
            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%"></div>
          </div>

          <div class="d-flex gap-2 flex-wrap">
            {{-- Ver Módulos (modal) --}}
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalModulos-{{ $rowId }}">
              <i class="bi bi-columns-gap me-1"></i> Ver módulos y calificar
            </button>

            {{-- Revisar Avances (vista agrupada por asignación) --}}
            <a class="btn btn-ghost" href="{{ route('docente.avances.index', $asignacion->id_asignacion) }}">
              <i class="bi bi-folder2-open me-1"></i> Ver todos los avances
            </a>

            {{-- Subir Corrección GENERAL (si ya no la quieres, puedes borrar este botón y el modal de abajo) --}}
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalCorreccion-{{ $rowId }}">
              <i class="bi bi-file-earmark-arrow-up me-1"></i> Enviar corrección general
            </button>
          </div>
        </div>
      </div>

      {{-- MODAL: MÓDULOS (lista + materiales + avances + correcciones + evaluar) --}}
      <div class="modal fade" id="modalModulos-{{ $rowId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                Seguimiento: {{ $titulo }} <span class="text-muted">— {{ $estu }}</span>
              </h5>
              <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

              {{-- Crear módulo --}}
              <div class="card border-0 mb-3">
                <div class="card-body bg-success-subtle rounded-3">
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
                  $badge = ['pendiente'=>'secondary','observado'=>'warning','aprobado'=>'success'][$mod->estado ?? 'pendiente'] ?? 'secondary';
                  $estadoLabel = ucfirst($mod->estado ?? 'pendiente');
                @endphp

                <div class="border rounded p-3 mb-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                      <div class="d-flex justify-content-between">
                        <div>
                          <div class="fw-semibold">
                            {{ $mod->titulo }}
                            <span class="badge bg-{{ $badge }} ms-2 text-uppercase">
                              {{ $estadoLabel }}
                            </span>
                            @if($mod->estado === 'aprobado')
                              <span class="badge bg-success-subtle text-success border ms-1">
                                ✔ Módulo completado
                              </span>
                            @endif
                          </div>
                          @if($mod->descripcion)
                            <div class="small text-muted">{{ $mod->descripcion }}</div>
                          @endif
                          <div class="small text-secondary mt-1">
                            Creado: {{ $mod->created_at?->format('d/m/Y') }}
                            @if($mod->fecha_limite) • Límite: {{ \Carbon\Carbon::parse($mod->fecha_limite)->format('d/m/Y') }} @endif
                            @if(!is_null($mod->calificacion)) • Nota: <strong>{{ $mod->calificacion }}</strong>@endif
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
                          <div class="small fw-semibold mb-1">Materiales del módulo</div>
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
                          <select name="tipo" class="form-select" required>
                            <option value="pdf">PDF / Documento</option>
                            <option value="enlace">Enlace Web</option>
                            <option value="video">Video</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <input name="titulo" class="form-control" placeholder="Título (opcional)">
                        </div>
                        <div class="col-md-3">
                          <input name="url" class="form-control" placeholder="URL (si enlace/video)">
                        </div>
                        <div class="col-md-3">
                          <input type="file" name="archivo" class="form-control">
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
                        <div class="small fw-semibold mb-1">Avances del estudiante en este módulo</div>
                        @php
                          $avancesModulo = $mod->avances ?? collect();
                        @endphp

                        @if($avancesModulo->isEmpty())
                          <div class="small text-muted">
                            Aún no hay envíos para este módulo.
                          </div>
                        @else
                          <ul class="list-group list-group-flush">
                            @foreach($avancesModulo as $av)
                              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                  <div class="small fw-semibold">{{ $av->titulo }}</div>
                                  <div class="small text-muted">
                                    {{ $av->created_at?->format('d/m/Y H:i') }}
                                    @if($av->descripcion)
                                      · {{ $av->descripcion }}
                                    @endif
                                  </div>
                                  @if($av->path)
                                    <a href="{{ asset('storage/'.$av->path) }}" target="_blank" class="small">
                                      <i class="bi bi-download"></i> Ver archivo
                                    </a>
                                  @endif
                                </div>
                              </li>
                            @endforeach
                          </ul>
                        @endif
                      </div>

                      <hr>

                      {{-- CORRECCIONES ESPECÍFICAS DE ESTE MÓDULO --}}
                      <div class="mt-2">
                        <div class="small fw-semibold mb-1">Correcciones para este módulo</div>
                        @php
                          $corrsModulo = $mod->correcciones ?? collect();
                        @endphp

                        @forelse($corrsModulo as $cor)
                          <div class="small mb-1">
                            <strong>{{ $cor->created_at?->format('d/m/Y') }}</strong>
                            @if($cor->fecha_limite)
                              · Límite: {{ \Carbon\Carbon::parse($cor->fecha_limite)->format('d/m/Y') }}
                            @endif
                            <br>
                            {{ $cor->comentario ?: 'Sin comentario' }}
                            @if($cor->path)
                              · <a href="{{ asset('storage/'.$cor->path) }}" target="_blank">
                                  <i class="bi bi-download"></i> Ver archivo
                                </a>
                            @endif
                          </div>
                        @empty
                          <div class="small text-muted">Aún no has enviado correcciones para este módulo.</div>
                        @endforelse
                      </div>

                      {{-- FORMULARIO: ENVIAR CORRECCIÓN PARA ESTE MÓDULO --}}
                      <form class="row g-2 mt-2"
                            action="{{ route('docente.correcciones.store', $asignacion->id_asignacion) }}"
                            method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id_modulo" value="{{ $mod->id_modulo }}">

                        <div class="col-md-6">
                          <label class="form-label mb-0 small">Comentario</label>
                          <textarea name="comentario" rows="2" class="form-control form-control-sm"></textarea>
                        </div>

                        <div class="col-md-3">
                          <label class="form-label mb-0 small">Archivo (opcional)</label>
                          <input type="file" name="archivo"
                                 class="form-control form-control-sm"
                                 accept=".pdf,.doc,.docx,.zip,.rar">
                        </div>

                        <div class="col-md-2">
                          <label class="form-label mb-0 small">Fecha límite</label>
                          <input type="date" name="fecha_limite" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-1 d-grid align-end">
                          <button class="btn btn-sm btn-outline-danger mt-4">
                            <i class="bi bi-send"></i>
                          </button>
                        </div>
                      </form>

                      <hr>

                      {{-- Evaluar módulo (estado + nota) --}}
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
                        el estudiante verá este módulo como <strong>completado</strong> en su panel
                        y podrás continuar con el siguiente módulo.
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
            <form action="{{ route('docente.correcciones.store', $asignacion->id_asignacion) }}"
                  method="POST" enctype="multipart/form-data">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title">
                  <i class="bi bi-clipboard-check me-2"></i>
                  Subir corrección / observación general
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Comentario</label>
                  <textarea name="comentario" rows="4" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Archivo (opcional)</label>
                  <input type="file" name="archivo" class="form-control" accept=".pdf,.doc,.docx,.zip,.rar">
                </div>
                <div class="mb-3">
                  <label class="form-label">Fecha límite</label>
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
