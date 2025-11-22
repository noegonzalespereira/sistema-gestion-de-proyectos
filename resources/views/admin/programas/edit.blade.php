@extends('admin.layouts.panel')
@section('title','Editar Programa')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Editar Programa</h4>
    <a href="{{ route('programas.index') }}" class="btn btn-outline-secondary">
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
      <strong>Datos del Programa</strong>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('programas.update', $programa) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Nombre *</label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $programa->nombre) }}">
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" rows="4" class="form-control"
                    placeholder="Opcional">{{ old('descripcion', $programa->descripcion) }}</textarea>
        </div>

        <div class="text-end">
          <a href="{{ route('programas.index') }}" class="btn btn-light">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
