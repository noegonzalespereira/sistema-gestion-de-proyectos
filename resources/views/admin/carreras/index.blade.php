@extends('admin.layouts.panel')
@section('title','Gestión de Carreras')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold">Gestión de Carreras</h4>
      <small class="text-muted">Administra las carreras académicas por institución</small>
    </div>

    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearCarrera">
      <i class="bi bi-plus-circle me-1"></i> Crear Carrera
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white"><strong>Carreras Registradas</strong></div>

    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
        <tr>
          <th>Nombre</th>
          <th>Sigla</th>
          <th>Institución</th>
          <th>Fecha creación</th>
          <th class="text-end">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse($carreras as $c)
          <tr>
            <td class="fw-semibold">{{ $c->nombre }}</td>
            <td>{{ $c->sigla ?? '—' }}</td>
            <td>{{ $c->institucion?->nombre ?? '—' }}</td>
            <td>{{ $c->created_at?->format('d/m/Y') }}</td>
            <td class="text-end">
              <a href="{{ route('carreras.edit',$c) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                <i class="bi bi-pencil-square"></i>
              </a>

              <form action="{{ route('carreras.destroy',$c) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('¿Eliminar la carrera {{ $c->nombre }}?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">No hay carreras registradas.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal Crear --}}
  <div class="modal fade" id="modalCrearCarrera" tabindex="-1" aria-labelledby="crearCarreraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="crearCarreraLabel"><i class="bi bi-collection me-2"></i> Crear Carrera</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form method="POST" action="{{ route('carreras.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre *</label>
              <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
            </div>

            <div class="mb-3">
              <label class="form-label">Sigla</label>
              <input type="text" name="sigla" class="form-control" maxlength="20" value="{{ old('sigla') }}">
            </div>

            <div class="mb-3">
              <label class="form-label">Institución *</label>
              <select name="id_institucion" class="form-select" required>
                <option value="">Seleccione...</option>
                @foreach($instituciones as $ins)
                  <option value="{{ $ins->id_institucion }}" @selected(old('id_institucion')==$ins->id_institucion)>
                    {{ $ins->nombre }} {{ $ins->sigla ? "({$ins->sigla})" : '' }}
                  </option>
                @endforeach
              </select>
            </div>

            @if ($errors->any())
              <div class="alert alert-danger mt-2">
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
            <button type="submit" class="btn btn-success">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
