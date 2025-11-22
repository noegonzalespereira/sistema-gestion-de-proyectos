@extends('admin.layouts.panel')
@section('title','Gestión de Estudiantes')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold">Gestión de Estudiantes</h4>
      <small class="text-muted">Administra estudiantes (usuario, CI y carrera)</small>
    </div>

    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearEstudiante">
      <i class="bi bi-person-plus me-1"></i> Crear Estudiante
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
      <strong>Estudiantes Registrados</strong>
    </div>

    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>CI</th>
            <th>Carrera</th>
            <th>Fecha creación</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($estudiantes as $e)
            <tr>
              <td>{{ $e->usuario->name ?? '—' }}</td>
              <td>{{ $e->usuario->email ?? '—' }}</td>
              <td>{{ $e->ci }}</td>
              <td>{{ $e->carrera->nombre ?? '—' }}</td>
              <td>{{ $e->created_at?->format('d/m/Y') }}</td>
              <td class="text-end">
                <a href="{{ route('estudiantes.edit', $e) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('estudiantes.destroy', $e) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Eliminar al estudiante {{ $e->usuario->name }}?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No hay estudiantes registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal Crear Estudiante --}}
  <div class="modal fade" id="modalCrearEstudiante" tabindex="-1" aria-labelledby="crearEstudianteLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="crearEstudianteLabel">
            <i class="bi bi-person-plus me-2"></i> Registrar Estudiante
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form method="POST" action="{{ route('estudiantes.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Usuario (rol Estudiante) *</label>
              <select name="id_usuario" class="form-select" required>
                <option value="">Seleccione un usuario</option>
                @foreach($usuariosDisponibles as $u)
                  <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                @endforeach
              </select>
              <div class="form-text">Solo se listan usuarios con rol Estudiante que no tienen ficha.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">CI *</label>
              <input type="text" name="ci" class="form-control" maxlength="30" required>
            </div>

            <div class="mb-0">
              <label class="form-label">Carrera (opcional)</label>
              <select name="id_carrera" class="form-select">
                <option value="">— Sin carrera —</option>
                @foreach($carreras as $c)
                  <option value="{{ $c->id_carrera }}">{{ $c->nombre }}</option>
                @endforeach
              </select>
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
