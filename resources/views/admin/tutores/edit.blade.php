@extends('admin.layouts.panel')
@section('title','Editar Tutor')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Editar Tutor</h4>
    <a href="{{ route('tutores.index') }}" class="btn btn-outline-secondary">
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
      <strong>Datos del Tutor</strong>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('tutores.update', $tutor) }}">
        @csrf @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Docente *</label>
            <select name="id_usuario" class="form-select" required>
              @foreach($docentesDisponibles as $d)
                <option value="{{ $d->id }}" {{ $d->id == $tutor->id_usuario ? 'selected' : '' }}>
                  {{ $d->name }} — {{ $d->email }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Ítem (opcional)</label>
            <input type="text" name="item" class="form-control" maxlength="50"
                   value="{{ old('item', $tutor->item) }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Fecha de creación</label>
            <input type="text" class="form-control" value="{{ $tutor->created_at?->format('d/m/Y H:i') }}" disabled>
          </div>
        </div>

        <div class="text-end mt-4">
          <a href="{{ route('tutores.index') }}" class="btn btn-light">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
