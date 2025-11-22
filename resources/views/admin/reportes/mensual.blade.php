@extends('admin.layouts.panel')
@section('title','Reporte Mensual')

@section('panel')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0 fw-bold">Reporte Mensual</h4>
  <a href="{{ route('reportes.mensual.export', request()->query()) }}" class="btn btn-success">
    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
  </a>
</div>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-2">
    <select class="form-select" name="month">
      @for($m=1;$m<=12;$m++)
        <option value="{{ $m }}" @selected($month==$m)>{{ \Carbon\Carbon::create(null,$m,1)->isoFormat('MMMM') }}</option>
      @endfor
    </select>
  </div>
  <div class="col-md-2">
    <select class="form-select" name="year">
      @for($y=now()->year; $y>=now()->year-5; $y--)
        <option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>
      @endfor
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <button class="btn btn-primary">Aplicar</button>
  </div>
</form>

<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body text-center">
    <div class="text-muted">Total proyectos</div><div class="fs-3 fw-bold">{{ $metrics['total'] }}</div>
  </div></div></div>
  <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body text-center">
    <div class="text-muted">Aprobados</div><div class="fs-3 fw-bold text-success">{{ $metrics['aprobados'] }}</div>
  </div></div></div>
  <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body text-center">
    <div class="text-muted">En revisión</div><div class="fs-3 fw-bold text-warning">{{ $metrics['revision'] }}</div>
  </div></div></div>
</div>

<div class="card shadow-sm border-0">
  <div class="card-header bg-white"><strong>Proyectos del mes</strong></div>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead class="table-light"><tr>
        <th>Título</th><th>Carrera</th><th>Programa</th><th>Estado</th><th>Creado</th>
      </tr></thead>
      <tbody>
        @foreach($proyectos as $p)
        <tr>
          <td>{{ $p->titulo }}</td>
          <td>{{ $p->carrera->nombre ?? '—' }}</td>
          <td>{{ $p->programa->nombre ?? '—' }}</td>
          <td>
            <span class="badge bg-{{ $p->calificacion ? 'success' : 'warning text-dark' }}">
              {{ $p->calificacion ? 'Aprobado' : 'En revisión' }}
            </span>
          </td>
          <td>{{ optional($p->created_at)->format('Y-m-d') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
