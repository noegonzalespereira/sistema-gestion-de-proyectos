@extends('admin.layouts.panel')
@section('title','Editar Usuario')

@section('panel')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Editar Usuario</h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

{{-- ✅ Mensaje de éxito --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ✅ Errores --}}
@if ($errors->any())
<div class="alert alert-danger">
    <strong>Revisa los siguientes errores:</strong>
    <ul class="mb-0 small">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <strong>Datos del Usuario</strong>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('usuarios.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">

                {{-- ✅ Nombre --}}
                <div class="col-md-6">
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" name="name" class="form-control" required
                           value="{{ old('name', $user->name) }}">
                </div>

                {{-- ✅ Email --}}
                <div class="col-md-6">
                    <label class="form-label">Email institucional *</label>
                    <input type="email" name="email" class="form-control" required
                           value="{{ old('email', $user->email) }}">
                </div>

                {{-- ✅ Rol --}}
                <div class="col-md-4">
                    <label class="form-label">Rol *</label>
                    @php $rol = old('rol', $user->rol); @endphp
                    <select name="rol" class="form-select" required>
                        <option value="Administrador" {{ $rol==='Administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="Docente"       {{ $rol==='Docente' ? 'selected' : '' }}>Docente</option>
                        <option value="Estudiante"    {{ $rol==='Estudiante' ? 'selected' : '' }}>Estudiante</option>
                    </select>
                </div>

                {{-- ✅ Estado --}}
                <div class="col-md-4">
                    <label class="form-label d-block">Estado</label>
                    <div class="form-check form-switch mt-2">

                        {{-- ⚠️ IMPORTANTE: Para enviar "0" si se desmarca --}}
                        <input type="hidden" name="activo" value="0">

                        <input class="form-check-input" type="checkbox" id="activo" name="activo"
                               value="1" {{ old('activo', $user->activo) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Activo</label>
                    </div>
                </div>

                {{-- ✅ Fecha de creación (solo lectura) --}}
                <div class="col-md-4">
                    <label class="form-label">Fecha de creación</label>
                    <input type="text" class="form-control" value="{{ $user->created_at?->format('d/m/Y') }}" disabled>
                </div>

            </div>

            <hr class="my-4">

            {{-- ✅ Sección de cambio de contraseña --}}
            <h6 class="fw-bold text-primary mb-3">
                <i class="bi bi-shield-lock"></i> Cambiar contraseña (opcional)
            </h6>

            <div class="row g-3">
                {{-- Nueva contraseña --}}
                <div class="col-md-6">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" name="password" class="form-control"
                           minlength="8" autocomplete="new-password"
                           placeholder="Dejar vacío para mantener la actual">
                    <div class="form-text">Mínimo 8 caracteres.</div>
                </div>

                {{-- Confirmación --}}
                <div class="col-md-6">
                    <label class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control"
                           minlength="8" autocomplete="new-password"
                           placeholder="Repite la nueva contraseña">
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('usuarios.index') }}" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar cambios
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
