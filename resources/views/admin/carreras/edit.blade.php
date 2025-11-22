@extends('admin.layouts.panel')
@section('title','Editar Carrera')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Editar Carrera</h4>
    <a href="{{ route('carreras.index') }}" class="btn btn-outline-secondary">
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
    <div class="card-header bg-white"><strong>Datos de la Carrera</strong></div>
    <div class="card-body">
      <form method="POST" action="{{ route('carreras.update', $carrera) }}">
        @csrf @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre" class="form-control" required
                   value="{{ old('nombre', $carrera->nombre) }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Sigla</label>
            <input type="text" name="sigla" class="form-control" maxlength="20"
                   value="{{ old('sigla', $carrera->sigla) }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Institución *</label>
            <select name="id_institucion" class="form-select" required>
              @foreach($instituciones as $ins)
                <option value="{{ $ins->id_institucion }}"
                        {{ old('id_institucion', $carrera->id_institucion)==$ins->id_institucion ? 'selected' : '' }}>
                  {{ $ins->nombre }} {{ $ins->sigla ? "({$ins->sigla})" : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Fecha de creación</label>
            <input type="text" class="form-control" disabled
                   value="{{ $carrera->created_at?->format('d/m/Y') }}">
          </div>
        </div>

        <div class="text-end mt-4">
          <a href="{{ route('carreras.index') }}" class="btn btn-light">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
