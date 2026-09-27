<!-- Modal de Confirmación de Baja de Vehículo -->
<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEliminarVehiculo" action="{{ route('vehiculos.eliminar', $vehiculo->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title fw-bold" id="modalEliminarLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Baja de Vehículo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center mb-2" style="width: 64px; height: 64px;">
                            <i class="fas fa-car fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-1 text-dark">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h5>
                        <span class="badge bg-dark font-monospace px-3 py-1">{{ $vehiculo->placas }}</span>
                    </div>

                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark small py-2 px-3 rounded-3 mb-3">
                        <i class="fas fa-info-circle text-warning me-1"></i>
                        <strong>Aviso:</strong> Este vehículo <strong>no será eliminado definitivamente</strong> del sistema, sino que se dará de baja y su estado pasará a <strong>Inactivo</strong>.
                    </div>

                    <div class="mb-2">
                        <label for="comentario_baja" class="form-label fw-medium small text-muted">Motivo de la baja (opcional):</label>
                        <input type="text" class="form-control" name="comentario" id="comentario_baja" placeholder="Ej. Fuera de servicio, venta, siniestro...">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                        <i class="fas fa-ban me-1"></i> Dar de Baja
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>