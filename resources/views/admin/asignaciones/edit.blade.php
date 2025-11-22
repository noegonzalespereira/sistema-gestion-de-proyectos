@extends('admin.layouts.panel')
@section('title','Editar Asignación')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Editar Asignación</h4>
    <a href="{{ route('asignaciones.index') }}" class="btn btn-outline-secondary">
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
      <strong>Datos de la Asignación</strong>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('asignaciones.update', $asignacion) }}">
        @csrf @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Estudiante</label>
            <select name="id_estudiante" class="form-select">
              <option value="">— Seleccione —</option>
              @foreach($estudiantes as $e)
                <option value="{{ $e->id_estudiante }}" @selected(old('id_estudiante',$asignacion->id_estudiante)==$e->id_estudiante)>
                  {{ $e->usuario->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tutor</label>
            <select name="id_tutor" class="form-select">
              <option value="">— Seleccione —</option>
              @foreach($tutores as $t)
                <option value="{{ $t->id_tutor }}" @selected(old('id_tutor',$asignacion->id_tutor)==$t->id_tutor)>
                  {{ $t->usuario->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Carrera</label>
            <select name="id_carrera" class="form-select">
              <option value="">— Seleccione —</option>
              @foreach($carreras as $c)
                <option value="{{ $c->id_carrera }}" @selected(old('id_carrera',$asignacion->id_carrera)==$c->id_carrera)>
                  {{ $c->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Programa</label>
            <select name="id_programa" class="form-select">
              <option value="">— Seleccione —</option>
              @foreach($programas as $p)
                <option value="{{ $p->id_programa }}" @selected(old('id_programa',$asignacion->id_programa)==$p->id_programa)>
                  {{ $p->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-8">
            <label class="form-label">Título del proyecto</label>
            <input type="text" name="titulo_proyecto" class="form-control"
                   value="{{ old('titulo_proyecto',$asignacion->titulo_proyecto) }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">Fecha asignación</label>
            <input type="date" name="fecha_asignacion" class="form-control"
                   value="{{ old('fecha_asignacion',$asignacion->fecha_asignacion) }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
              @foreach(['En revisión','Asignado','Aprobado','Observado'] as $st)
                <option value="{{ $st }}" @selected(old('estado',$asignacion->estado)===$st)>{{ $st }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-8">
            <label class="form-label">Observación</label>
            <input type="text" name="observacion" class="form-control"
                   value="{{ old('observacion',$asignacion->observacion) }}">
          </div>
        </div>

        <div class="text-end mt-4">
          <a href="{{ route('asignaciones.index') }}" class="btn btn-light">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
