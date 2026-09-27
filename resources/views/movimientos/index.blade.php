@extends('layouts.navigation')

@section('title', 'Historial de Logs y Auditoría')

@section('content')
<div class="container-fluid py-4 px-md-4">
    <!-- Header Principal -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--text-main, #242423);">
                <i class="fas fa-history text-primary me-2"></i>Historial de Logs y Auditoría
            </h1>
            <p class="text-muted small mb-0">Registro y trazabilidad completa de creaciones, ediciones, bajas y consumos en el sistema.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-sync-alt me-1"></i> Refrescar
            </a>
            @if(request()->hasAny(['tipo_accion', 'categoria', 'usuario_id', 'fecha_desde', 'fecha_hasta', 'buscar']))
            <a href="{{ route('movimientos.index') }}" class="btn btn-light btn-sm text-danger border">
                <i class="fas fa-times-circle me-1"></i> Limpiar Filtros
            </a>
            @endif
        </div>
    </div>

    <!-- KPIs Estadísticos Bento Grid -->
    <div class="row g-3 mb-4">
        <!-- Total General -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Logs</span>
                        <h3 class="fw-bold mb-0 text-dark mt-1">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-list-check fa-lg"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block"><i class="fas fa-clock me-1"></i>{{ $stats['hoy'] }} registrados hoy</small>
            </div>
        </div>

        <!-- Creaciones -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <a href="{{ route('movimientos.index', ['tipo_accion' => 'creacion']) }}" class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white text-decoration-none hover-shadow">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Creaciones</span>
                        <h3 class="fw-bold mb-0 text-success mt-1">{{ number_format($stats['creaciones']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-plus-circle fa-lg"></i>
                    </div>
                </div>
                <small class="text-success mt-2 d-block"><i class="fas fa-box-open me-1"></i>Altas y registros</small>
            </a>
        </div>

        <!-- Ediciones -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <a href="{{ route('movimientos.index', ['tipo_accion' => 'edicion']) }}" class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white text-decoration-none hover-shadow">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Ediciones</span>
                        <h3 class="fw-bold mb-0 text-info mt-1">{{ number_format($stats['ediciones']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-pencil-alt fa-lg"></i>
                    </div>
                </div>
                <small class="text-info mt-2 d-block"><i class="fas fa-edit me-1"></i>Actualizaciones</small>
            </a>
        </div>

        <!-- Eliminaciones / Bajas -->
        <div class="col-xl-3 col-md-6 col-sm-6">
            <a href="{{ route('movimientos.index', ['tipo_accion' => 'eliminacion']) }}" class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white text-decoration-none hover-shadow">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Bajas / Eliminaciones</span>
                        <h3 class="fw-bold mb-0 text-danger mt-1">{{ number_format($stats['eliminaciones']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-trash-alt fa-lg"></i>
                    </div>
                </div>
                <small class="text-danger mt-2 d-block"><i class="fas fa-ban me-1"></i>Bajas definitivas o descartes</small>
            </a>
        </div>

        <!-- Consumos -->
        <div class="col-xl-3 col-md-6 col-sm-6">
            <a href="{{ route('movimientos.index', ['tipo_accion' => 'consumo']) }}" class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white text-decoration-none hover-shadow">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Consumos</span>
                        <h3 class="fw-bold mb-0 text-warning mt-1">{{ number_format($stats['consumos']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-boxes-packing fa-lg"></i>
                    </div>
                </div>
                <small class="text-warning mt-2 d-block"><i class="fas fa-hand-holding me-1"></i>Salidas y mermas de insumos</small>
            </a>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('movimientos.index') }}" method="GET" class="row g-2 align-items-end">
                <!-- Tipo de Acción -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Tipo de Acción</label>
                    <select name="tipo_accion" class="form-select form-select-sm">
                        <option value="">Todas las acciones</option>
                        <option value="creacion" {{ request('tipo_accion') == 'creacion' ? 'selected' : '' }}>🟢 Creación / Registro</option>
                        <option value="edicion" {{ request('tipo_accion') == 'edicion' ? 'selected' : '' }}>🔵 Edición / Cambio</option>
                        <option value="eliminacion" {{ request('tipo_accion') == 'eliminacion' ? 'selected' : '' }}>🔴 Baja / Eliminación</option>
                        <option value="consumo" {{ request('tipo_accion') == 'consumo' ? 'selected' : '' }}>🟠 Consumo de Existencia</option>
                    </select>
                </div>

                <!-- Categoría -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Categoría</label>
                    <select name="categoria" class="form-select form-select-sm">
                        <option value="">Todas las categorías</option>
                        <option value="fijos" {{ request('categoria') == 'fijos' ? 'selected' : '' }}>Activos Fijos</option>
                        <option value="consumibles" {{ request('categoria') == 'consumibles' ? 'selected' : '' }}>Consumibles</option>
                        <option value="compraventa" {{ request('categoria') == 'compraventa' ? 'selected' : '' }}>Compra/Venta</option>
                        <option value="vehiculo" {{ request('categoria') == 'vehiculo' ? 'selected' : '' }}>Vehículos</option>
                    </select>
                </div>

                <!-- Usuario -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Usuario</label>
                    <select name="usuario_id" class="form-select form-select-sm">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->nombre }} {{ $u->apellido }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fechas -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-select-sm" value="{{ request('fecha_desde') }}">
                </div>

                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-select-sm" value="{{ request('fecha_hasta') }}">
                </div>

                <!-- Búsqueda y Botón -->
                <div class="col-lg-2 col-md-6 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 shadow-sm">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['tipo_accion', 'categoria', 'usuario_id', 'fecha_desde', 'fecha_hasta', 'buscar']))
                    <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla Principal de Logs -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-list text-muted me-2"></i>Registros de Movimientos
                <span class="badge bg-light text-muted border ms-2">{{ $movimientos->total() }} registros</span>
            </h5>
            <div class="small text-muted">
                Página {{ $movimientos->currentPage() }} de {{ $movimientos->lastPage() }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 170px;">Fecha y Hora</th>
                        <th style="width: 220px;">Usuario Responsable</th>
                        <th style="width: 180px;">Acción Realizada</th>
                        <th style="width: 130px;">Categoría</th>
                        <th style="min-width: 200px;">Producto / Activo</th>
                        <th>Detalle / Comentario</th>
                        <th style="width: 90px; text-align: center;">Doc</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                    @php
                        $accionLower = strtolower($mov->accion);
                        $badgeAccion = match(true) {
                            str_contains($accionLower, 'registro') || str_contains($accionLower, 'crea') || str_contains($accionLower, 'alta') => 'badge-soft-success',
                            str_contains($accionLower, 'consumo') || str_contains($accionLower, 'disminuir') => 'badge-soft-warning',
                            str_contains($accionLower, 'baja') || str_contains($accionLower, 'elimina') || str_contains($accionLower, 'desactiv') => 'badge-soft-danger',
                            str_contains($accionLower, 'mantenimiento') => 'badge-soft-purple',
                            default => 'badge-soft-info'
                        };

                        $iconAccion = match(true) {
                            str_contains($accionLower, 'registro') || str_contains($accionLower, 'crea') => 'fa-plus-circle',
                            str_contains($accionLower, 'consumo') || str_contains($accionLower, 'disminuir') => 'fa-boxes-packing',
                            str_contains($accionLower, 'baja') || str_contains($accionLower, 'elimina') => 'fa-trash-alt',
                            str_contains($accionLower, 'mantenimiento') => 'fa-tools',
                            default => 'fa-edit'
                        };

                        $badgeCat = match($mov->categoria) {
                            'fijos' => 'bg-primary',
                            'consumibles' => 'bg-warning text-dark',
                            'compraventa' => 'bg-success',
                            'vehiculo' => 'bg-info text-dark',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <tr>
                        <!-- Fecha y Hora -->
                        <td>
                            <div class="fw-semibold text-dark" style="font-size: 0.88rem;">
                                {{ $mov->fecha ? \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') : 'N/A' }}
                            </div>
                            <small class="text-muted" style="font-size: 0.78rem;">
                                <i class="far fa-clock me-1"></i>{{ $mov->fecha ? \Carbon\Carbon::parse($mov->fecha)->format('h:i A') : '' }}
                            </small>
                        </td>

                        <!-- Usuario -->
                        <td>
                            @if($mov->usuario)
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light border text-primary fw-bold d-flex align-items-center justify-content-center me-2" style="width: 34px; height: 34px; font-size: 0.8rem;">
                                    {{ strtoupper(substr($mov->usuario->nombre, 0, 1) . substr($mov->usuario->apellido ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size: 0.88rem;">
                                        {{ $mov->usuario->nombre }} {{ $mov->usuario->apellido }}
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $mov->usuario->gmail }}</small>
                                </div>
                            </div>
                            @else
                            <span class="text-muted fst-italic">Usuario eliminado</span>
                            @endif
                        </td>

                        <!-- Acción -->
                        <td>
                            <span class="custom-badge {{ $badgeAccion }}">
                                <i class="fas {{ $iconAccion }} me-1"></i>{{ $mov->accion }}
                            </span>
                        </td>

                        <!-- Categoría -->
                        <td>
                            <span class="badge {{ $badgeCat }} rounded-pill px-2 py-1 small">
                                {{ ucfirst($mov->categoria) }}
                            </span>
                        </td>

                        <!-- Producto -->
                        <td>
                            <div class="fw-semibold text-dark" style="font-size: 0.88rem;">
                                {{ $mov->nombreProducto() }}
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">ID Referencia: #{{ $mov->id_producto }}</small>
                        </td>

                        <!-- Comentario -->
                        <td>
                            @if(!empty($mov->comentario))
                            <span class="text-secondary small" style="line-height: 1.4;">
                                {{ $mov->comentario }}
                            </span>
                            @else
                            <span class="text-muted fst-italic small">Sin comentarios adicionales</span>
                            @endif
                        </td>

                        <!-- Documentación -->
                        <td class="text-center">
                            @if(!empty($mov->documentacion))
                            <a href="{{ $mov->documentacion }}" target="_blank" class="btn btn-sm btn-outline-primary p-1" title="Ver documento adjunto">
                                <i class="fas fa-file-alt"></i>
                            </a>
                            @else
                            <span class="text-muted opacity-25">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-clipboard-list display-4 text-muted opacity-25 mb-3 d-block"></i>
                                <h5 class="fw-bold">No se encontraron movimientos registrados</h5>
                                <p class="small text-muted">Intenta cambiar los filtros seleccionados o realiza nuevas operaciones en el sistema.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movimientos->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Mostrando registros {{ $movimientos->firstItem() }} al {{ $movimientos->lastItem() }}</span>
            <div>
                {{ $movimientos->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.2s ease;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}
.custom-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.65rem;
    font-size: 0.78rem;
    font-weight: 600;
    border-radius: 50rem;
    line-height: 1.2;
}
.badge-soft-success {
    background-color: rgba(25, 135, 84, 0.12);
    color: #198754;
    border: 1px solid rgba(25, 135, 84, 0.2);
}
.badge-soft-info {
    background-color: rgba(13, 110, 253, 0.12);
    color: #0d6efd;
    border: 1px solid rgba(13, 110, 253, 0.2);
}
.badge-soft-danger {
    background-color: rgba(220, 53, 69, 0.12);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
}
.badge-soft-warning {
    background-color: rgba(255, 193, 7, 0.18);
    color: #b45309;
    border: 1px solid rgba(255, 193, 7, 0.3);
}
.badge-soft-purple {
    background-color: rgba(111, 66, 193, 0.12);
    color: #6f42c1;
    border: 1px solid rgba(111, 66, 193, 0.2);
}
</style>
@endsection
