@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <aside class="col-12 col-md-3 col-lg-2 admin-sidebar p-0 border-end">
      <div class="p-3">
        <div class="d-flex align-items-center mb-3">
          <i class="bi bi-grid fs-4 me-2 text-primary"></i>
          <span class="fw-semibold">Panel Admin</span>
        </div>

        <small class="text-uppercase text-muted">General</small>
        <ul class="nav flex-column mb-3">
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
               href="{{ route('usuarios.index') }}">
              <i class="bi bi-people me-2"></i> Gestión de Usuarios
            </a>
          </li>
        </ul>

        <small class="text-uppercase text-muted">Académico</small>
        <ul class="nav flex-column mb-3">
          <li class="nav-item"><a class="nav-link d-flex align-items-center {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}" href="{{ route('asignaciones.index') }}"><i class="bi bi-diagram-3 me-2"></i> Asignaciones</a></li>
          <li class="nav-item"><a class="nav-link d-flex align-items-center {{ request()->routeIs('programas.*') ? 'active' : '' }}" href="{{ route('programas.index') }}"><i class="bi bi-mortarboard me-2"></i> Programas</a></li>
          <li class="nav-item"><a class="nav-link d-flex align-items-center {{ request()->routeIs('carreras.*') ? 'active' : '' }}" href="{{ route('carreras.index') }}"><i class="bi bi-collection me-2"></i> Carreras</a></li>
          <li class="nav-item"><a class="nav-link d-flex align-items-center {{ request()->routeIs('tutores.*') ? 'active' : '' }}" href="{{ route('tutores.index') }}"> <i class="bi bi-person-badge me-2"></i> Tutores
          <li class="nav-item"><a class="nav-link d-flex align-items-center {{ request()->routeIs('estudiantes.*') ? 'active' : '' }}" href="{{ route('estudiantes.index') }}"> <i class="bi bi-mortarboard me-2"></i> Estudiantes </a> </li>
  </a>
</li>
          <li class="nav-item"><a class="nav-link d-flex align-items-center {{ request()->routeIs('tribunales.*') ? 'active' : '' }}" href="{{ route('tribunales.index') }}"><i class="bi bi-people-fill me-2"></i> Tribunal</a></li>
        </ul>

        <small class="text-uppercase text-muted">Configuración</small>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link d-flex align-items-center {{ request()->routeIs('institucion.*') ? 'active' : '' }}" href="{{ route('institucion.index') }}"><i class="bi bi-building me-2"></i> Institución</a></li>
          <li class="nav-item"><a class="nav-link d-flex align-items-center {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}"><i class="bi bi-bar-chart-line me-2"></i> Reportes</a></li>
        </ul>
      </div>
    </aside>

    <!-- Contenido -->
    <section class="col-12 col-md-9 col-lg-10 p-4">
      @yield('panel')
    </section>
  </div>
</div>
@endsection
