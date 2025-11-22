@extends('admin.layouts.panel')
@section('title','Institución')

@section('panel')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold">Institución</h4>
      <small class="text-muted">Gestiona el registro institucional</small>
    </div>

    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearInstitucion">
      <i class="bi bi-plus-circle me-1"></i> Crear Institución
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
      <strong>Instituciones Registradas</strong>
    </div>

    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Sigla</th>
            <th>Descripción</th>
            <th>Fecha Creación</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($instituciones as $i)
            <tr>
              <td class="fw-semibold">{{ $i->nombre }}</td>
              <td>{{ $i->sigla ?: '—' }}</td>
              <td class="text-muted" style="max-width:420px;">
                <div class="text-truncate">{{ $i->descripcion ?: '—' }}</div>
              </td>
              <td>{{ $i->created_at?->format('d/m/Y') }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('institucion.edit',$i) }}" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('institucion.destroy',$i) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Eliminar la institución {{ $i->nombre }}?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No hay instituciones registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal Crear Institución --}}
  <div class="modal fade" id="modalCrearInstitucion" tabindex="-1" aria-labelledby="crearInstitucionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="crearInstitucionLabel">
            <i class="bi bi-building-add me-2"></i> Crear Institución
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form method="POST" action="{{ route('institucion.store') }}">
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

            <div class="mb-2">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3" maxlength="1000"
                        placeholder="Descripción breve">{{ old('descripcion') }}</textarea>
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
            <button type="submit" class="btn btn-success">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
