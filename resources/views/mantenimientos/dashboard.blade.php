@extends('layouts.navigation')

@section('title', 'Dashboard de Mantenimientos')

@section('content')
<div class="container-fluid py-4 px-md-4">
    <!-- Header Principal -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--text-main, #242423);">
                <i class="fas fa-tools text-primary me-2"></i>Dashboard de Mantenimientos
            </h1>
            <p class="text-muted small mb-0">Control general de servicios vehiculares, kilometrajes y visualización de la persona a cargo de cada unidad.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if(esSuperAdmin() || tienePermiso('vehiculo - modificar') || tienePermiso('vehiculo - insertar'))
            <button class="btn btn-primary btn-sm shadow-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNuevoMantenimientoRapido">
                <i class="fas fa-plus-circle me-1"></i> Registrar Mantenimiento
            </button>
            @endif
            @if(tienePermiso('vehiculo - leer'))
            <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-car me-1"></i> Ver Flota de Vehículos
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- KPIs Estadísticos -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Servicios Registrados</span>
                        <h3 class="fw-bold mb-0 text-dark mt-1">{{ number_format($stats['total_servicios']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-wrench fa-lg"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block"><i class="fas fa-calendar-alt me-1"></i>Historial acumulado</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Flota Total</span>
                        <h3 class="fw-bold mb-0 text-success mt-1">{{ number_format($stats['total_vehiculos']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-car-side fa-lg"></i>
                    </div>
                </div>
                <small class="text-success mt-2 d-block"><i class="fas fa-check-circle me-1"></i>Vehículos activos</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Con Responsable</span>
                        <h3 class="fw-bold mb-0 text-info mt-1">{{ number_format($stats['vehiculos_con_responsable']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-user-shield fa-lg"></i>
                    </div>
                </div>
                <small class="text-info mt-2 d-block"><i class="fas fa-user-check me-1"></i>Unidades a cargo</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Últimos 30 días</span>
                        <h3 class="fw-bold mb-0 text-warning mt-1">{{ number_format($stats['servicios_recientes']) }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                </div>
                <small class="text-warning mt-2 d-block"><i class="fas fa-history me-1"></i>Servicios recientes</small>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('mantenimientos.dashboard') }}" method="GET" class="row g-2 align-items-end">
                <!-- Filtro A Cargo De (Responsable) -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="fas fa-user-tie text-primary me-1"></i> A cargo de (Responsable)
                    </label>
                    <select name="responsable_id" class="form-select form-select-sm">
                        <option value="">Todos los responsables</option>
                        @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ request('responsable_id') == $resp->id ? 'selected' : '' }}>
                            {{ $resp->nombre }} {{ $resp->apellido }} ({{ $resp->gmail }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Vehículo -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="fas fa-car text-success me-1"></i> Vehículo
                    </label>
                    <select name="vehiculo_id" class="form-select form-select-sm">
                        <option value="">Todos los vehículos</option>
                        @foreach($vehiculos as $veh)
                        <option value="{{ $veh->id }}" {{ request('vehiculo_id') == $veh->id ? 'selected' : '' }}>
                            {{ $veh->marca }} {{ $veh->modelo }} ({{ $veh->placas }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo de Servicio -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Tipo de Servicio</label>
                    <input type="text" name="tipo_servicio" class="form-control form-control-sm" placeholder="Ej: Preventivo, Agencia..." value="{{ request('tipo_servicio') }}">
                </div>

                <!-- Buscador Libre -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Buscar</label>
                    <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Taller, placas, detalle..." value="{{ request('buscar') }}">
                </div>

                <!-- Botones -->
                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 shadow-sm">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['responsable_id', 'vehiculo_id', 'tipo_servicio', 'buscar', 'fecha_desde', 'fecha_hasta']))
                    <a href="{{ route('mantenimientos.dashboard') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Pestañas de Vista (Tarjetas vs Tabla) -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fas fa-clipboard-list text-muted me-2"></i>Registros de Mantenimiento
            <span class="badge bg-light text-muted border ms-2">{{ $mantenimientos->total() }} registros</span>
        </h5>
        <div class="btn-group btn-group-sm bg-light p-1 rounded-pill border shadow-xs" role="group">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 active" id="btnModoTarjetas" onclick="cambiarModoVistaMantenimiento('tarjetas')">
                <i class="fas fa-th-large me-1"></i> Tarjetas
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-muted" id="btnModoTabla" onclick="cambiarModoVistaMantenimiento('tabla')">
                <i class="fas fa-table me-1"></i> Tabla
            </button>
        </div>
    </div>

    <!-- 1. VISTA TARJETAS BENTO CON RESPONSABLE DESTACADO -->
    <div id="vistaTarjetasMantenimiento" class="row g-3 mb-4 animate__animated animate__fadeIn">
        @forelse($mantenimientos as $mant)
        @php
            $veh = $mant->vehiculo;
            $responsable = $veh?->usuarioResponsable;
        @endphp
        <div class="col-lg-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: var(--card-bg, #fff);">
                <!-- Header de la Tarjeta con Vehículo y Placas -->
                <div class="card-header bg-light bg-opacity-75 border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-car text-primary me-2"></i>{{ $veh?->marca ?? 'Vehículo' }} {{ $veh?->modelo ?? '' }}
                        </h6>
                        <small class="text-muted">Placas: <span class="badge bg-dark text-white rounded-pill px-2">{{ $veh?->placas ?? 'S/P' }}</span></small>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill small">
                        {{ $veh?->año ?? 'N/A' }}
                    </span>
                </div>

                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <!-- SECCION DESTACADA: A CARGO DE QUIEN ESTA -->
                    <div class="p-3 rounded-3 mb-3 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-user-shield text-primary"></i>
                            <span class="small text-uppercase fw-bold text-primary" style="font-size: 0.72rem; letter-spacing: 0.5px;">A Cargo De (Responsable):</span>
                        </div>
                        @if($responsable)
                        <div class="d-flex align-items-center mt-2">
                            <div class="rounded-circle bg-white text-primary border fw-bold d-flex align-items-center justify-content-center me-2 shadow-xs" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                {{ strtoupper(substr($responsable->nombre, 0, 1) . substr($responsable->apellido ?? '', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;">
                                    {{ $responsable->nombre }} {{ $responsable->apellido }}
                                </div>
                                <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.75rem;">
                                    <span class="badge bg-primary bg-opacity-25 text-primary py-0 px-1">{{ $responsable->rol?->nombre ?? 'Usuario' }}</span>
                                    <span class="text-truncate">{{ $responsable->gmail }}</span>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="text-danger small fst-italic mt-1">
                            <i class="fas fa-exclamation-circle me-1"></i> Unidad sin responsable asignado actualmente
                        </div>
                        @endif
                    </div>

                    <!-- Datos del Mantenimiento -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1 rounded-pill small fw-semibold">
                                <i class="fas fa-wrench me-1 text-warning"></i> {{ $mant->tipo_servicio ?? 'Servicio' }}
                            </span>
                            <small class="text-muted">
                                <i class="far fa-calendar-alt me-1"></i>{{ $mant->fecha ? \Carbon\Carbon::parse($mant->fecha)->format('d/m/Y') : 'N/A' }}
                            </small>
                        </div>

                        <div class="row g-2 small text-muted mb-2">
                            <div class="col-6">
                                <strong>Kilometraje:</strong> {{ number_format($mant->kilometraje ?? 0) }} km
                            </div>
                            <div class="col-6">
                                <strong>Taller:</strong> {{ $mant->taller ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="p-2 rounded bg-light small text-secondary" style="min-height: 48px; line-height: 1.4;">
                            {{ $mant->descripcion ?? 'Sin descripción adicional' }}
                        </div>
                    </div>

                    <!-- Acciones del pie -->
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $veh?->ubicacion?->nombre ?? 'N/A' }}
                        </small>
                        @if($veh)
                        <a href="{{ route('vehiculos.ver', $veh->id) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1">
                            <i class="fas fa-eye me-1"></i> Ver Unidad
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="fas fa-tools display-4 text-muted opacity-25 mb-3 d-block"></i>
                <h5 class="fw-bold">No se encontraron registros de mantenimiento</h5>
                <p class="small text-muted">Ajusta los filtros o registra el primer servicio para un vehículo.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- 2. VISTA TABLA DETALLADA -->
    <div id="vistaTablaMantenimiento" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 d-none animate__animated animate__fadeIn">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px;">Fecha</th>
                        <th style="min-width: 180px;">Vehículo</th>
                        <th style="min-width: 240px;" class="bg-primary bg-opacity-10 text-primary">A Cargo De (Responsable)</th>
                        <th>Tipo de Servicio</th>
                        <th style="width: 130px;">Kilometraje</th>
                        <th style="min-width: 150px;">Taller</th>
                        <th>Descripción</th>
                        <th style="width: 80px; text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mantenimientos as $mant)
                    @php
                        $veh = $mant->vehiculo;
                        $responsable = $veh?->usuarioResponsable;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $mant->fecha ? \Carbon\Carbon::parse($mant->fecha)->format('d/m/Y') : 'N/A' }}</div>
                            <small class="text-muted">{{ $mant->fecha ? \Carbon\Carbon::parse($mant->fecha)->diffForHumans() : '' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $veh?->marca }} {{ $veh?->modelo }}</div>
                            <small class="text-muted">Placas: <span class="badge bg-light text-dark border">{{ $veh?->placas ?? 'S/P' }}</span></small>
                        </td>
                        <td class="bg-primary bg-opacity-10">
                            @if($responsable)
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-white text-primary border fw-bold d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($responsable->nombre, 0, 1) . substr($responsable->apellido ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $responsable->nombre }} {{ $responsable->apellido }}</div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $responsable->gmail }}</small>
                                </div>
                            </div>
                            @else
                            <span class="badge bg-secondary bg-opacity-25 text-dark">Sin Asignar</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                {{ $mant->tipo_servicio }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ number_format($mant->kilometraje ?? 0) }}</span> <small class="text-muted">km</small>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $mant->taller }}</div>
                        </td>
                        <td>
                            <span class="small text-secondary text-truncate d-inline-block" style="max-width: 250px;" title="{{ $mant->descripcion }}">
                                {{ $mant->descripcion }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($veh)
                            <a href="{{ route('vehiculos.ver', $veh->id) }}" class="btn btn-sm btn-outline-primary p-1" title="Ver detalles del vehículo">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            No se encontraron registros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if($mantenimientos->hasPages())
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
        <span class="text-muted small">Mostrando {{ $mantenimientos->firstItem() }} al {{ $mantenimientos->lastItem() }} de {{ $mantenimientos->total() }} registros</span>
        <div>
            {{ $mantenimientos->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>

<!-- Modal para Registrar Mantenimiento Rápido -->
<div class="modal fade" id="modalNuevoMantenimientoRapido" tabindex="-1" aria-labelledby="modalNuevoMantenimientoRapidoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="modalNuevoMantenimientoRapidoLabel">
                    <i class="fas fa-tools me-2"></i>Registrar Servicio de Mantenimiento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('mantenimientos.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Seleccionar Vehículo *</label>
                        <select name="vehiculo_id" class="form-select" required>
                            <option value="">Selecciona la unidad...</option>
                            @foreach($vehiculos as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->marca }} {{ $v->modelo }} (Placas: {{ $v->placas }}) - A cargo: {{ $v->usuarioResponsable ? ($v->usuarioResponsable->nombre . ' ' . $v->usuarioResponsable->apellido) : 'Sin asignar' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Fecha del Servicio *</label>
                            <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Kilometraje Actual *</label>
                            <input type="number" name="kilometraje" class="form-control" placeholder="Ej: 15400" min="0" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Tipo de Servicio *</label>
                            <input type="text" name="tipo_servicio" class="form-control" placeholder="Ej: Mantenimiento 10,000 km, Cambio de frenos..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Taller o Agencia *</label>
                            <input type="text" name="taller" class="form-control" placeholder="Ej: Agencia Oficial, Taller Central..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Descripción y Trabajos Realizados *</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalla las reparaciones, refacciones reemplazadas y observaciones..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Guardar Mantenimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cambiarModoVistaMantenimiento(modo) {
    const vistaTarjetas = document.getElementById('vistaTarjetasMantenimiento');
    const vistaTabla = document.getElementById('vistaTablaMantenimiento');
    const btnTarjetas = document.getElementById('btnModoTarjetas');
    const btnTabla = document.getElementById('btnModoTabla');

    if (modo === 'tarjetas') {
        vistaTarjetas.classList.remove('d-none');
        vistaTabla.classList.add('d-none');
        btnTarjetas.classList.add('active', 'btn-primary', 'text-white');
        btnTarjetas.classList.remove('text-muted');
        btnTabla.classList.remove('active', 'btn-primary', 'text-white');
        btnTabla.classList.add('text-muted');
        localStorage.setItem('sgi_vista_mantenimientos', 'tarjetas');
    } else {
        vistaTarjetas.classList.add('d-none');
        vistaTabla.classList.remove('d-none');
        btnTabla.classList.add('active', 'btn-primary', 'text-white');
        btnTabla.classList.remove('text-muted');
        btnTarjetas.classList.remove('active', 'btn-primary', 'text-white');
        btnTarjetas.classList.add('text-muted');
        localStorage.setItem('sgi_vista_mantenimientos', 'tabla');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const modoGuardado = localStorage.getItem('sgi_vista_mantenimientos') || 'tarjetas';
    cambiarModoVistaMantenimiento(modoGuardado);
});
</script>

<style>
.btn-xs {
    padding: 0.2rem 0.5rem;
    font-size: 0.75rem;
}
.shadow-xs {
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
</style>
@endsection
