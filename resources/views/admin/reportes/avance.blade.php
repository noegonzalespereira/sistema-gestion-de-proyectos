@extends('admin.layouts.panel')
@section('title','Reporte de Avance')

@section('panel')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0 fw-bold">Reporte de Avance</h4>
  <a href="{{ route('reportes.avance.export', request()->query()) }}" class="btn btn-success">
    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
  </a>
</div>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-4">
    <select name="id_carrera" class="form-select">
      <option value="">Todas las carreras</option>
      @foreach($carreras as $c)
        <option value="{{ $c->id_carrera }}" @selected(request('id_carrera')==$c->id_carrera)>{{ $c->nombre }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <select name="id_programa" class="form-select">
      <option value="">Todos los programas</option>
      @foreach($programas as $p)
        <option value="{{ $p->id_programa }}" @selected(request('id_programa')==$p->id_programa)>{{ $p->nombre }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-12 text-end">
    <button class="btn btn-primary">Aplicar filtros</button>
  </div>
</form>

<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Proyecto</th><th>Estudiante</th><th>Tutor</th><th>Carrera</th><th>Programa</th><th>Avance</th><th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @foreach($proyectos as $p)
          @php
            $avance = $p->calificacion ? 100 : 40;
          @endphp
          <tr>
            <td>{{ $p->titulo }}</td>
            <td>{{ $p->estudiante->usuario->name ?? '—' }}</td>
            <td>{{ $p->tutor->usuario->name ?? '—' }}</td>
            <td>{{ $p->carrera->nombre ?? '—' }}</td>
            <td>{{ $p->programa->nombre ?? '—' }}</td>
            <td style="min-width:160px">
              <div class="progress" role="progressbar" aria-valuenow="{{ $avance }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: {{ $avance }}%">{{ $avance }}%</div>
              </div>
            </td>
            <td>
              <span class="badge bg-{{ $p->calificacion ? 'success' : 'warning text-dark' }}">
                {{ $p->calificacion ? 'Aprobado' : 'En revisión' }}
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white">{{ $proyectos->links() }}</div>
</div>
@endsection
