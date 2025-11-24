@extends('layouts.app')
@section('title','Revisar Avances')

@section('content')
<style>
  :root {
    --color-primary: #2b6cb0;
    --color-primary-dark: #1a365d;
    --color-bg: #edf2f7;
    --color-gray: #718096;
    --color-gray-light: #e2e8f0;
    --color-white:#ffffff;
  }
  .page-header {
    background: var(--color-primary-dark);
    color: #fff;
  }
  .small-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: var(--color-gray);
  }
  .modulo-card {
    border-radius: 16px;
    border: 1px solid var(--color-gray-light);
    background: var(--color-white);
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(15,23,42,0.08);
  }
  .modulo-header {
    padding: 12px 16px;
    border-bottom: 1px solid #edf2f7;
    background: #f7fafc;
  }
  .modulo-title {
    font-weight: 600;
    color: #1a365d;
    font-size: 0.98rem;
  }
  .modulo-subtitle {
    font-size: 0.8rem;
    color: #4a5568;
  }

  .avance-card {
    position: relative;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 10px 12px;
    margin-bottom: 10px;
  }
  .avance-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    border-radius: 14px 0 0 14px;
    background: #3182ce;
  }
  .avance-title {
    font-weight: 600;
    font-size: 0.9rem;
    color:#1a365d;
  }
  .avance-meta {
    font-size: 0.8rem;
    color:#4a5568;
  }
</style>

<div class="min-h-screen" style="background:var(--color-bg);">
  <header class="page-header py-4 shadow-sm">
    <div class="container">
      <h5 class="mb-0">
        Revisar avances — 
        <span class="text-white-50">
          {{ $asignacion->titulo_proyecto ?? 'Proyecto' }}
        </span>
      </h5>
      <div class="small text-white-50 mt-1">
        Estudiante: {{ $asignacion->estudiante?->usuario?->name ?? '—' }}
      </div>
    </div>
  </header>

  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <span class="small-label d-block mb-1">Avances por módulo</span>
        <div class="text-muted small">
          Revisa rápidamente qué avances corresponden a cada módulo.
        </div>
      </div>
      <a href="{{ route('docente.asignaciones') }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left"></i> Volver a mis asignaciones
      </a>
    </div>

    @forelse($avancesPorModulo as $idModulo => $lista)
      @php
        /** @var \App\Models\Avance $ejemplo */
        $ejemplo = $lista->first();
        $modulo  = $ejemplo?->modulo;
      @endphp

      <div class="modulo-card">
        <div class="modulo-header">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
              <div class="modulo-title">
                Módulo: {{ $modulo?->titulo ?? ('ID '.$idModulo) }}
              </div>
              @if($modulo?->descripcion)
                <div class="modulo-subtitle">
                  {{ $modulo->descripcion }}
                </div>
              @endif
            </div>
            <div class="modulo-subtitle">
              Total de avances: <strong>{{ $lista->count() }}</strong>
            </div>
          </div>
        </div>

        <div class="p-3">
          @foreach($lista as $index => $av)
            <div class="avance-card">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                  <div class="avance-title">
                    Avance #{{ $index + 1 }} — {{ $av->titulo }}
                  </div>
                  <div class="avance-meta">
                    Enviado {{ $av->created_at?->format('d/m/Y H:i') }}
                    · por {{ $av->usuario?->name ?? '—' }}
                  </div>
                  @if($av->descripcion)
                    <div class="small mt-1">{{ $av->descripcion }}</div>
                  @endif
                </div>
                <div>
                  @if($av->path)
                    <a class="btn btn-sm btn-outline-primary"
                       target="_blank"
                       href="{{ asset('storage/'.$av->path) }}">
                      <i class="bi bi-download"></i> Ver archivo
                    </a>
                  @else
                    <span class="badge bg-secondary">Sin archivo</span>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="text-muted">
        Aún no hay avances cargados para este proyecto.
      </div>
    @endforelse
  </div>
</div>
@endsection
