@extends('layouts.app')
@section('title','Registrar Proyecto')

@section('content')
<div class="container py-4">
  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="bi bi-folder-plus"></i> Registrar Nuevo Proyecto</h5>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('proyectos.store') }}" enctype="multipart/form-data">
        @csrf

        <h6 class="fw-bold text-primary mt-3">Información Básica</h6>
        <hr>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Título del Proyecto *</label>
            <input type="text" name="titulo" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Programa *</label>
            <select name="id_programa" class="form-select" required>
              <option value="">Seleccione...</option>
              @foreach($programas as $p)
              <option value="{{ $p->id_programa }}">{{ $p->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Carrera *</label>
            <select name="id_carrera" class="form-select" required>
              <option value="">Seleccione...</option>
              @foreach($carreras as $c)
              <option value="{{ $c->id_carrera }}">{{ $c->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12">
            <label class="form-label">Resumen *</label>
            <textarea name="resumen" class="form-control" maxlength="500" rows="3"></textarea>
          </div>
        </div>

        <h6 class="fw-bold text-primary mt-4">Participantes</h6>
        <hr>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Estudiante *</label>
            <select name="id_estudiante" class="form-select" required>
              @foreach($estudiantes as $e)
              <option value="{{ $e->id_estudiante }}">{{ $e->usuario->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tutor *</label>
            <select name="id_tutor" class="form-select" required>
              @foreach($tutores as $t)
              <option value="{{ $t->id_tutor }}">{{ $t->usuario->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tribunal *</label>
            <select name="id_tribunal" class="form-select" required>
              @foreach($tribunales as $t)
              <option value="{{ $t->id_tribunal }}">{{ $t->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <h6 class="fw-bold text-primary mt-4">Fechas y Calificación</h6>
        <hr>
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Año *</label>
            <input type="number" name="anio" class="form-control" min="2000" max="2099" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Fecha de Defensa *</label>
            <input type="date" name="fecha_defensa" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Fecha de Aprobación</label>
            <input type="date" name="fecha_aprobacion" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Calificación</label>
            <input type="number" name="calificacion" class="form-control" max="100">
          </div>
        </div>

        <h6 class="fw-bold text-primary mt-4">Documento</h6>
        <hr>
        <div class="mb-3">
          <label class="form-label">Archivo PDF *</label>
          <input type="file" name="archivo_pdf" accept="application/pdf" class="form-control" required>
          <small class="text-muted">Máximo 20 MB</small>
        </div>

        <div class="text-end mt-3">
          <a href="{{ route('repositorio.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-success">Registrar Proyecto</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
