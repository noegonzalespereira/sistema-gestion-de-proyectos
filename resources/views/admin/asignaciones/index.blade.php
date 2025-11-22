@extends('admin.layouts.panel')
@section('title','Asignaciones')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold">Gestión de Asignaciones</h4>
      <small class="text-muted">Administra la relación Estudiante – Tutor – Carrera – Programa</small>
    </div>

    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearAsignacion">
      <i class="bi bi-plus-circle me-1"></i> Crear Asignación
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Filtros simples --}}
  <form class="row g-2 mb-3" method="GET" action="{{ route('asignaciones.index') }}">
    <div class="col-md-3">
      <select name="estado" class="form-select">
        <option value="">Todos los estados</option>
        @foreach(['Asignado','Aprobado','Observado'] as $st)
          <option value="{{ $st }}" @selected(request('estado')===$st)>{{ $st }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <select name="id_carrera" class="form-select">
        <option value="">Todas las carreras</option>
        @foreach($carreras as $c)
          <option value="{{ $c->id_carrera }}" @selected(request('id_carrera')==$c->id_carrera)>{{ $c->nombre }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <select name="id_programa" class="form-select">
        <option value="">Todos los programas</option>
        @foreach($programas as $p)
          <option value="{{ $p->id_programa }}" @selected(request('id_programa')==$p->id_programa)>{{ $p->nombre }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-outline-primary w-100">Aplicar filtros</button>
    </div>
  </form>

  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Título</th>
            <th>Estudiante</th>
            <th>Tutor</th>
            <th>Carrera</th>
            <th>Programa</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($asignaciones as $a)
            <tr>
              <td class="fw-medium">{{ $a->titulo_proyecto ?? '—' }}</td>
              <td>{{ $a->estudiante->usuario->name ?? '—' }}</td>
              <td>{{ $a->tutor->usuario->name ?? '—' }}</td>
              <td>{{ $a->carrera->nombre ?? '—' }}</td>
              <td>{{ $a->programa->nombre ?? '—' }}</td>
              <td>{{ $a->fecha_asignacion ? \Illuminate\Support\Carbon::parse($a->fecha_asignacion)->format('d/m/Y') : '—' }}</td>
              <td>
                @php
                  $color = match($a->estado){
                    'Aprobado'   => 'success',
                    'Asignado'   => 'primary',
                    'Observado'  => 'warning',
                    default      => 'secondary'
                  };
                @endphp
                <span class="badge bg-{{ $color }}">{{ $a->estado ?? '—' }}</span>
              </td>
              <td class="text-end">
                <a href="{{ route('asignaciones.edit', $a) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('asignaciones.destroy', $a) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Eliminar la asignación?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No hay asignaciones registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal Crear Asignación --}}
  <div class="modal fade" id="modalCrearAsignacion" tabindex="-1" aria-labelledby="crearAsignacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="crearAsignacionLabel">
            <i class="bi bi-journal-check me-2"></i> Crear Asignación
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form method="POST" action="{{ route('asignaciones.store') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-3">

              {{-- ESTUDIANTE --}}
              <div class="col-md-6">
                <label class="form-label">Estudiante</label>
                <select name="id_estudiante" id="select-estudiante" class="form-select">
                  <option value="">— Seleccione —</option>
                  @foreach($estudiantes as $e)
                    <option value="{{ $e->id_estudiante }}"
                            data-carrera="{{ $e->id_carrera ?? '' }}">
                      {{ $e->usuario->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- TUTOR --}}
              <div class="col-md-6">
                <label class="form-label">Tutor</label>
                <select name="id_tutor" class="form-select">
                  <option value="">— Seleccione —</option>
                  @foreach($tutores as $t)
                    <option value="{{ $t->id_tutor }}">{{ $t->usuario->name }}</option>
                  @endforeach
                </select>
              </div>

              {{-- CARRERA (se rellena sola) --}}
              <div class="col-md-6">
                <label class="form-label">Carrera</label>
                <select name="id_carrera" id="select-carrera" class="form-select">
                  <option value="">— Seleccione —</option>
                  @foreach($carreras as $c)
                    <option value="{{ $c->id_carrera }}">{{ $c->nombre }}</option>
                  @endforeach
                </select>
                <div class="form-text">
                  Se rellenará automáticamente según la carrera del estudiante, pero puedes cambiarla si es necesario.
                </div>
              </div>

              {{-- PROGRAMA --}}
              <div class="col-md-6">
                <label class="form-label">Programa</label>
                <select name="id_programa" class="form-select">
                  <option value="">— Seleccione —</option>
                  @foreach($programas as $p)
                    <option value="{{ $p->id_programa }}">{{ $p->nombre }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-8">
                <label class="form-label">Título del proyecto</label>
                <input type="text" name="titulo_proyecto" class="form-control" maxlength="255">
              </div>

              <div class="col-md-4">
                <label class="form-label">Fecha asignación</label>
                <input type="date" name="fecha_asignacion" class="form-control">
              </div>

              <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                  @foreach(['Asignado','Aprobado','Observado'] as $st)
                    <option value="{{ $st }}">{{ $st }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-8">
                <label class="form-label">Observación</label>
                <input type="text" name="observacion" class="form-control">
              </div>
            </div>

            @if ($errors->any())
              <div class="alert alert-danger mt-3">
                <ul class="mb-0 small">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Crear Asignación</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selEst  = document.getElementById('select-estudiante');
    const selCarr = document.getElementById('select-carrera');

    if (!selEst || !selCarr) return;

    selEst.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const carreraId = opt.getAttribute('data-carrera');

        if (carreraId) {
            selCarr.value = carreraId;   // selecciona la carrera del estudiante
        } else {
            selCarr.value = '';          // deja vacío si el estudiante no tiene carrera
        }
    });
});
</script>
@endpush
