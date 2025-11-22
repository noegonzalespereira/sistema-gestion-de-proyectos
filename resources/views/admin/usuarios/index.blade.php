@extends('admin.layouts.panel')
@section('title','Gestión de Usuarios')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold">Gestión de Usuarios</h4>
      <small class="text-muted">Administra cuentas y estados</small>
    </div>

    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
      <i class="bi bi-plus-circle me-1"></i> Crear Usuario
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
      <strong>Usuarios Registrados</strong>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Fecha Creación</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($usuarios as $u)
          <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>
              @php
                $roleClass = [
                  'Administrador' => 'danger',
                  'Docente' => 'primary',
                  'Estudiante' => 'secondary',
                ][$u->rol] ?? 'secondary';
              @endphp
              <span class="badge bg-{{ $roleClass }}">{{ $u->rol }}</span>
            </td>
            <td>
              <span class="badge bg-{{ $u->activo ? 'success' : 'secondary' }}">
                {{ $u->activo ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td>{{ $u->created_at?->format('d/m/Y') }}</td>
            <td class="text-end">
              {{-- Editar --}}
              <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                <i class="bi bi-pencil-square"></i>
              </a>

              {{-- Activar/Desactivar --}}
              <form action="{{ route('usuarios.toggle', $u) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm btn-outline-warning" title="Activar/Desactivar">
                  <i class="bi bi-toggle-{{ $u->activo ? 'on' : 'off' }}"></i>
                </button>
              </form>

              {{-- Eliminar --}}
              <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('¿Eliminar al usuario {{ $u->name }}?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Crear Usuario -->
  <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="crearUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="crearUsuarioLabel">
            <i class="bi bi-person-plus me-2"></i> Crear Nuevo Usuario
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="{{ route('usuarios.registro') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre completo *</label>
              <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Email institucional *</label>
              <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Rol *</label>
              <select name="rol" class="form-select" required>
                <option value="">Seleccione un rol</option>
                <option value="Administrador" @selected(old('rol')==='Administrador')>Administrador</option>
                <option value="Docente" @selected(old('rol')==='Docente')>Docente</option>
                <option value="Estudiante" @selected(old('rol')==='Estudiante')>Estudiante</option>
              </select>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Contraseña *</label>
                <input type="password" name="password" class="form-control" minlength="8" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Confirmar contraseña *</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
              </div>
            </div>

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" name="activo" id="chkActivo" checked>
              <label class="form-check-label" for="chkActivo">Activo</label>
            </div>

            {{-- Errores --}}
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
            <button type="submit" class="btn btn-success">Crear Usuario</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      @if ($errors->any())
        const modal = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
        modal.show();
      @endif
    });
  </script>
  @endpush
 
@endsection
