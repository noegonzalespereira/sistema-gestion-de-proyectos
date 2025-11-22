@extends('admin.layouts.panel')
@section('title','Reporte de Plazos')

@section('panel')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0 fw-bold">Reporte de Plazos</h4>
  <a href="{{ route('reportes.plazos.export', request()->query()) }}" class="btn btn-success">
    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
  </a>
</div>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-3">
    <label class="form-label">Días próximos</label>
    <input type="number" class="form-control" name="dias" value="{{ $dias }}" min="1" />
  </div>
  <div class="col-md-3 d-flex align-items-end">
    <button class="btn btn-primary">Aplicar</button>
  </div>
</form>

<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead class="table-light">
        <tr>
          <th>Proyecto</th><th>Estudiante</th><th>Tutor</th><th>Carrera</th><th>Programa</th><th>Fecha Límite</th><th>Días</th>
        </tr>
      </thead>
      <tbody>
        @foreach($proyectos as $p)
          @php $diasRest = now()->diffInDays(\Carbon\Carbon::parse($p->fecha_defensa), false); @endphp
          <tr>
            <td>{{ $p->titulo }}</td>
            <td>{{ $p->estudiante->usuario->name ?? '—' }}</td>
            <td>{{ $p->tutor->usuario->name ?? '—' }}</td>
            <td>{{ $p->carrera->nombre ?? '—' }}</td>
            <td>{{ $p->programa->nombre ?? '—' }}</td>
            <td>{{ $p->fecha_defensa }}</td>
            <td>
              <span class="badge bg-{{ $diasRest < 0 ? 'secondary' : ($diasRest <= 7 ? 'danger' : 'warning text-dark') }}">
                {{ $diasRest }}
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
