<div class="modal fade" id="rehabilitarModal-{{ $falta->id_falta }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header" style="background:#2b6cb0; color:white;">
        <h5 class="modal-title">
          <i class="bi bi-unlock"></i> Rehabilitar módulo
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="{{ route('docente.faltas.rehabilitar', $falta->id_falta) }}">
        @csrf

        <div class="modal-body">

          <p class="small text-muted">
            Estudiante: <strong>{{ $falta->estudiante->usuario->name }}</strong><br>
            Módulo: <strong>{{ $falta->modulo->titulo }}</strong>
          </p>

          <div class="mb-3">
            <label class="form-label small">Nueva fecha límite *</label>
            <input type="date" name="nueva_fecha_limite" required class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label small">Comentario (opcional)</label>
            <textarea name="comentario" rows="2" class="form-control"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">
            Confirmar
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
