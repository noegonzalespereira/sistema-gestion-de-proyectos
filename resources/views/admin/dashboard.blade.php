{{-- @extends('layouts.app') --}}
@extends('admin.layouts.panel')

@section('title', 'Panel Administrativo')

@section('panel')
{{-- <div class="d-flex">
  <!-- SIDEBAR -->
  <aside class="bg-light border-end vh-100 p-3" style="width: 240px;">
    <h5 class="text-primary fw-bold mb-4"><i class="bi bi-speedometer2"></i> GENERAL</h5>
    <ul class="nav flex-column">
      <li><a href="{{ route('admin.dashboard') }}" class="nav-link fw-bold text-primary"><i class="bi bi-house"></i> Dashboard</a></li>
      <li><a href="#" class="nav-link text-dark"><i class="bi bi-people"></i> Gestión de Usuarios</a></li>

      <hr>
      <h6 class="text-secondary fw-bold mt-3"><i class="bi bi-mortarboard"></i> ACADÉMICO</h6>
      <li><a href="#" class="nav-link text-dark"><i class="bi bi-journal-check"></i> Asignaciones</a></li>
      <li><a href="#" class="nav-link text-dark"><i class="bi bi-collection"></i> Programas</a></li>
      <li><a href="#" class="nav-link text-dark"><i class="bi bi-book"></i> Carreras</a></li>
      <li><a href="#" class="nav-link text-dark"><i class="bi bi-person-badge"></i> Tutores</a></li>
      <li><a href="#" class="nav-link text-dark"><i class="bi bi-people-fill"></i> Tribunal</a></li>

      <hr>
      <h6 class="text-secondary fw-bold mt-3"><i class="bi bi-gear"></i> CONFIGURACIÓN</h6>
      <li><a href="#" class="nav-link text-dark"><i class="bi bi-building"></i> Institución</a></li>
      <li><a href="#" class="nav-link text-dark"><i class="bi bi-bar-chart"></i> Reportes</a></li>
    </ul>
  </aside> --}}

  <!-- CONTENIDO PRINCIPAL -->
  <div class="flex-grow-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold text-primary">Dashboard Administrativo</h3>
      <div>
        
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-outline-danger">Salir</button>
        </form>
      </div>
    </div>

    <!-- TARJETAS -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <h5 class="fw-bold text-primary">Proyectos Totales</h5>
            <h3 class="text-success">{{ $totalProyectos }}</h3>
            <p class="text-muted small">Proyectos registrados en el sistema</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <h5 class="fw-bold text-warning">En Revisión</h5>
            <h3 class="text-warning">{{ $proyectosRevision }}</h3>
            <p class="text-muted small">Proyectos pendientes de aprobación</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <h5 class="fw-bold text-success">Aprobados</h5>
            <h3 class="text-success">{{ $proyectosAprobados }}</h3>
            <p class="text-muted small">Proyectos finalizados</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <h5 class="fw-bold text-primary">Usuarios Activos</h5>
            <h3 class="text-primary">{{ $usuariosActivos }}</h3>
            <p class="text-muted small">Docentes y estudiantes</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <h5 class="fw-bold text-info">Tutores</h5>
            <h3 class="text-info">{{ $tutores }}</h3>
            <p class="text-muted small">Tutores disponibles</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <h5 class="fw-bold text-secondary">Carreras</h5>
            <h3 class="text-secondary">{{ $carreras }}</h3>
            <p class="text-muted small">Programas académicos</p>
          </div>
        </div>
      </div>
    </div>

    <!-- PROYECTOS RECIENTES -->
    <div class="card shadow-sm border-0 mt-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-folder2-open"></i> Proyectos Recientes</h5>
      </div>
      <div class="card-body">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Título del Proyecto</th>
              <th>Carrera</th>
              <th>Estado</th>
              <th>Tutor</th>
              <th>Año</th>
            </tr>
          </thead>
          <tbody>
            @foreach($proyectosRecientes as $p)
              <tr>
                <td>{{ $p->titulo }}</td>
                <td>{{ $p->carrera->nombre ?? '—' }}</td>
                <td>
                  @if($p->calificacion)
                    <span class="badge bg-success">Aprobado</span>
                  @else
                    <span class="badge bg-warning text-dark">En Revisión</span>
                  @endif
                </td>
                <td>{{ $p->tutor->usuario->name ?? '—' }}</td>
                <td>{{ $p->anio }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
