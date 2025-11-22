@extends('admin.layouts.panel')
@section('title','Reportes')

@section('panel')
  {{-- Encabezado --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold">Reportes</h4>
      <small class="text-muted">Genera reportes y exporta a Excel</small>
    </div>
  </div>

  {{-- Flash success/error --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Tarjetas de reportes --}}
  <div class="row g-3">

    {{-- 1) Reporte de Proyectos --}}
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start">
            <div class="me-3">
              <span class="badge bg-primary p-3"><i class="bi bi-folder2-open fs-5"></i></span>
            </div>
            <div class="flex-grow-1">
              <h5 class="card-title mb-1">Reporte de Proyectos</h5>
              <p class="text-muted small mb-3">Listado completo de proyectos con sus datos principales.</p>

              <div class="d-flex gap-2">
                {{-- Enlace a vista previa/parametrización (opcional) --}}
                <a href="{{ route('reportes.proyectos') }}" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-eye"></i> Ver
                </a>

                {{-- Exportar Excel (GET) --}}
                <a href="{{ route('reportes.proyectos.export') }}" class="btn btn-success btn-sm">
                  <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 2) Reporte de Avance de Proyectos --}}
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start">
            <div class="me-3">
              <span class="badge bg-info p-3"><i class="bi bi-graph-up fs-5"></i></span>
            </div>
            <div class="flex-grow-1">
              <h5 class="card-title mb-1">Reporte de Avance</h5>
              <p class="text-muted small mb-3">Progreso por carrera y programa (seguimiento/estados).</p>

              <div class="d-flex gap-2">
                <a href="{{ route('reportes.avance') }}" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-sliders"></i> Configurar
                </a>
                <a href="{{ route('reportes.avance.export') }}" class="btn btn-success btn-sm">
                  <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 3) Reporte de Plazos (fechas límite/defensa) --}}
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start">
            <div class="me-3">
              <span class="badge bg-warning p-3"><i class="bi bi-alarm fs-5 text-dark"></i></span>
            </div>
            <div class="flex-grow-1">
              <h5 class="card-title mb-1">Reporte de Plazos</h5>
              <p class="text-muted small mb-3">Próximas defensas y tareas cercanas a vencer.</p>

              <div class="d-flex gap-2">
                <a href="{{ route('reportes.plazos') }}" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-filter"></i> Filtrar
                </a>
                <a href="{{ route('reportes.plazos.export') }}" class="btn btn-success btn-sm">
                  <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 4) Reporte Mensual --}}
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start">
            <div class="me-3">
              <span class="badge bg-secondary p-3"><i class="bi bi-calendar3 fs-5"></i></span>
            </div>
            <div class="flex-grow-1">
              <h5 class="card-title mb-1">Reporte Mensual</h5>
              <p class="text-muted small mb-3">Resumen mensual de actividades y proyectos.</p>

              <div class="d-flex gap-2">
                <a href="{{ route('reportes.mensual') }}" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-calendar-event"></i> Elegir mes
                </a>
                <a href="{{ route('reportes.mensual.export') }}" class="btn btn-success btn-sm">
                  <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
@endsection
