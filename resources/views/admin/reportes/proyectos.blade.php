@extends('admin.layouts.panel')
@section('title','Reporte de Proyectos')

@section('panel')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 fw-bold">Reporte de Proyectos</h4>
    <small class="text-muted">Filtra y exporta el listado de proyectos</small>
  </div>
  <a href="{{ route('reportes.proyectos.export', request()->query()) }}" class="btn btn-success">
    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
  </a>
</div>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-3">
    <select name="id_carrera" class="form-select">
      <option value="">Todas las carreras</option>
      @foreach($carreras as $c)
        <option value="{{ $c->id_carrera }}" @selected(request('id_carrera')==$c->id_carrera)>{{ $c->nombre }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <select name="id_programa" class="form-select">
      <option value="">Todos los programas</option>
      @foreach($programas as $p)
        <option value="{{ $p->id_programa }}" @selected(request('id_programa')==$p->id_programa)>{{ $p->nombre }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2">
    <select name="estado" class="form-select">
      <option value="">Todos los estados</option>
      <option value="aprobado" @selected(request('estado')==='aprobado')>Aprobado</option>
      <option value="revision"  @selected(request('estado')==='revision')>En revisión</option>
    </select>
  </div>
  <div class="col-md-2"><input type="date" name="desde" value="{{ request('desde') }}" class="form-control" /></div>
  <div class="col-md-2"><input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" /></div>
  <div class="col-12 text-end">
    <button class="btn btn-primary">Aplicar filtros</button>
  </div>
</form>

<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Título</th><th>Estudiante</th><th>Tutor</th><th>Carrera</th><th>Programa</th><th>Estado</th><th>Defensa</th>
        </tr>
      </thead>
      <tbody>
        @forelse($proyectos as $p)
        <tr>
          <td>{{ $p->titulo }}</td>
          <td>{{ $p->estudiante->usuario->name ?? '—' }}</td>
          <td>{{ $p->tutor->usuario->name ?? '—' }}</td>
          <td>{{ $p->carrera->nombre ?? '—' }}</td>
          <td>{{ $p->programa->nombre ?? '—' }}</td>
          <td>
            <span class="badge bg-{{ $p->calificacion ? 'success' : 'warning text-dark' }}">
              {{ $p->calificacion ? 'Aprobado' : 'En revisión' }}
            </span>
          </td>
          <td>{{ $p->fecha_defensa }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Sin resultados</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white">
    {{ $proyectos->links() }}
  </div>
</div>
@endsection
