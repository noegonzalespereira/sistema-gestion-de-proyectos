@extends('admin.layouts.panel')
@section('title','Editar Estudiante')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Editar Estudiante</h4>
    <a href="{{ route('estudiantes.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

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
      <strong>Datos del Estudiante</strong>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('estudiantes.update', $estudiante) }}">
        @csrf @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Usuario *</label>
            <select name="id_usuario" class="form-select" required>
              @foreach($usuariosDisponibles as $u)
                <option value="{{ $u->id }}" {{ $u->id == $estudiante->id_usuario ? 'selected' : '' }}>
                  {{ $u->name }} — {{ $u->email }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">CI *</label>
            <input type="text" name="ci" class="form-control" maxlength="30"
                   value="{{ old('ci', $estudiante->ci) }}" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Carrera</label>
            <select name="id_carrera" class="form-select">
              <option value="">— Sin carrera —</option>
              @foreach($carreras as $c)
                <option value="{{ $c->id_carrera }}" {{ $c->id_carrera == $estudiante->id_carrera ? 'selected' : '' }}>
                  {{ $c->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Fecha de creación</label>
            <input type="text" class="form-control" value="{{ $estudiante->created_at?->format('d/m/Y H:i') }}" disabled>
          </div>
        </div>

        <div class="text-end mt-4">
          <a href="{{ route('estudiantes.index') }}" class="btn btn-light">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
