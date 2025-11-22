@extends('admin.layouts.panel')
@section('title','Gestión de Tutores')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold">Gestión de Tutores</h4>
      <small class="text-muted">Administra los tutores (docentes asignables a proyectos)</small>
    </div>

    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearTutor">
      <i class="bi bi-person-plus me-1"></i> Crear Tutor
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white">
      <strong>Tutores Registrados</strong>
    </div>

    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Ítem</th>
            <th>Fecha creación</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tutores as $t)
            <tr>
              <td>{{ $t->usuario->name ?? '—' }}</td>
              <td>{{ $t->usuario->email ?? '—' }}</td>
              <td>{{ $t->item ?? '—' }}</td>
              <td>{{ $t->created_at?->format('d/m/Y') }}</td>
              <td class="text-end">
                <a href="{{ route('tutores.edit', $t) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>

                <form action="{{ route('tutores.destroy', $t) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Eliminar al tutor {{ $t->usuario->name }}?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No hay tutores registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal Crear Tutor --}}
  <div class="modal fade" id="modalCrearTutor" tabindex="-1" aria-labelledby="crearTutorLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="crearTutorLabel">
            <i class="bi bi-person-plus me-2"></i> Registrar Tutor
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form method="POST" action="{{ route('tutores.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Docente *</label>
              <select name="id_usuario" class="form-select" required>
                <option value="">Seleccione un docente</option>
                @foreach($docentesDisponibles as $d)
                  <option value="{{ $d->id }}">{{ $d->name }} — {{ $d->email }}</option>
                @endforeach
              </select>
              <div class="form-text">Solo aparecen docentes que aún no son tutores.</div>
            </div>

            <div class="mb-0">
              <label class="form-label">Ítem (opcional)</label>
              <input type="text" name="item" class="form-control" maxlength="50" placeholder="Ej. Ítem institucional">
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
            <button type="submit" class="btn btn-success">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
