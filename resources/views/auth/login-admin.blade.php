@extends('layouts.app')
@section('title','Login Administrador')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow border-0">
        <div class="card-header text-center bg-primary text-white">
          <h5 class="mb-0">Sistema de Proyectos Académicos</h5>
          <small>Panel de Administración</small>
          <div class="badge bg-warning text-dark mt-2">ADMINISTRADOR</div>
        </div>
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
          @endif

          <div class="alert alert-success small">
            Como administrador, puede crear nuevas cuentas institucionales.
          </div>

          <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Correo Electrónico</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="admin@institucion.edu" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Recordarme</label>
              </div>
              <a href="#">¿Olvidaste tu contraseña?</a>
            </div>
            <button class="btn btn-success w-100" type="submit">Iniciar Sesión</button>
          </form>

          <hr>
          <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalCrearCuenta">
            + Crear Nueva Cuenta
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Crear Cuenta (solo interfaz, acción la harás en módulo de usuarios) --}}
<div class="modal fade" id="modalCrearCuenta" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear Nueva Cuenta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        {{-- 🔔 Alertas de éxito o error --}}
        @if (session('success'))
          <div class="alert alert-success text-center small">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger text-center small">
            <i class="bi bi-x-circle-fill me-1"></i> {{ $errors->first() }}
          </div>
        @endif

        {{--Formulario para crear usuario --}}
        <form method="POST" action="{{ route('usuarios.registro') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre Completo *</label>
              <input name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Institucional *</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Rol *</label>
              <select name="rol" class="form-select" required>
                <option value="Estudiante">Estudiante</option>
                <option value="Docente">Docente</option>
                <option value="Administrador">Administrador</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contraseña Temporal *</label>
              <input name="password" type="password" class="form-control" minlength="8" required>
            </div>
          </div>
          <div class="mt-3 text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary">Crear Usuario</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    @endif
});
</script>
@endsection


@endsection
