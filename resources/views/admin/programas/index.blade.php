@extends('admin.layouts.panel')
@section('title','Gestión de Programas')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold">Gestión de Programas</h4>
      <small class="text-muted">Administra los programas académicos</small>
    </div>

    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearPrograma">
      <i class="bi bi-plus-circle me-1"></i> Crear Programa
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white">
      <strong>Programas Registrados</strong>
    </div>

    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha creación</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($programas as $p)
            <tr>
              <td class="fw-medium">{{ $p->nombre }}</td>
              <td class="text-muted" style="max-width:520px;">
                <div class="text-truncate">{{ $p->descripcion ?: '—' }}</div>
              </td>
              <td>{{ $p->created_at?->format('d/m/Y') }}</td>
              <td class="text-end">
                <a href="{{ route('programas.edit', $p) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>

                <form action="{{ route('programas.destroy', $p) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Eliminar el programa \"{{ $p->nombre }}\"?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No hay programas registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Crear Programa -->
  <div class="modal fade" id="modalCrearPrograma" tabindex="-1" aria-labelledby="crearProgramaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="crearProgramaLabel">
            <i class="bi bi-bookmark-plus me-2"></i> Crear Programa
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form method="POST" action="{{ route('programas.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre *</label>
              <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
            </div>
            <div class="mb-0">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3" placeholder="Opcional">{{ old('descripcion') }}</textarea>
            </div>

            @if ($errors->any())
              <div class="alert alert-danger mt-3">
                <ul class="mb-0 small">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Crear Programa</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
