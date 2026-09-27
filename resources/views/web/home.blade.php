@extends('layouts.navigation')

@section('title', 'Inicio - Nodo Group')

@section('content')
<div class="container main-content">
    <!-- Welcome Section Premium -->
    <div class="welcome-section animate__animated animate__fadeInUp">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 class="welcome-title">Bienvenido, <span style="color: var(--primary);">{{ nombreCompletoUsuario() }}</span>!</h1>
                <p class="welcome-subtitle">Panel principal del gestor de inventario NODO.</p>
            </div>
            <a href="https://sgi.naoproyectos.com/guias/GUIA%20DEL%20PRIMER%20USO%20DEL%20SISTEMA%20DE%20GESTI%C3%93N%20DE%20INVENTARIOS%20(SIG).pdf" 
               class="btn btn-primary" 
               target="_blank" 
               rel="noopener noreferrer">
               <i class="fas fa-file-pdf"></i> Guía del Sistema
            </a>
        </div>
    </div>

    @if(esSuperAdmin() || tienePermiso('leerUsuarios'))
    <!-- Bento Grid Estadísticas (Super Admin y Administrador) -->
    <div class="bento-grid">
        <!-- Registros -->
        <div class="bento-card primary animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="bento-card-icon"><i class="fas fa-clipboard-check"></i></div>
            <div>
                <div class="stat-label">Registros</div>
                <div class="stat-value" id="stat-registros">{{ $registros7Dias }}</div>
                <div style="font-size: 0.8rem; color: var(--text-tertiary); margin-top: 8px;">Últimos 7 días</div>
            </div>
        </div>

        <!-- Movimientos -->
        <div class="bento-card warning animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="bento-card-icon"><i class="fas fa-exchange-alt"></i></div>
            <div>
                <div class="stat-label">Movimientos</div>
                <div class="stat-value" id="stat-movimientos">{{ $movimientos7Dias }}</div>
                <div style="font-size: 0.8rem; color: var(--text-tertiary); margin-top: 8px;">Últimos 7 días</div>
            </div>
        </div>

        <!-- Usuarios (Solo Super Admin) -->
        <a href="{{ route('usuarios.index') }}" class="bento-card success animate__animated animate__fadeInUp text-decoration-none" style="animation-delay: 0.3s; color: inherit;">
            <div class="bento-card-icon"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-label">Usuarios Activos</div>
                <div class="stat-value" id="stat-usuarios">{{ $usuariosActivos }}</div>
                <div style="font-size: 0.8rem; color: var(--text-tertiary); margin-top: 8px;">En el sistema</div>
            </div>
        </a>
        
        <!-- Tarjeta Grande Informativa CTA -->
        <div class="bento-card info bento-large animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div style="display: flex; justify-content: space-between; align-items: center; height: 100%;">
                <div>
                    <h3 style="margin-bottom: 12px; font-weight: 700; color: var(--text-main);">Gestión Inteligente</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 450px; margin-bottom: 24px; line-height: 1.6;">
                        Administra el inventario corporativo, controla los activos fijos, consumibles, vehículos y da seguimiento a todas las operaciones del grupo en tiempo real.
                    </p>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        @if (tienePermiso('fijos - leer') || tienePermiso('consumible - leer') || tienePermiso('compra/venta - leer'))
                        <a href="{{ route('productos') }}" class="btn btn-primary"><i class="fas fa-boxes"></i> Ver Inventario</a>
                        @endif
                        <a href="{{ route('usuarios.perfil') }}" class="btn btn-secondary"><i class="fas fa-user-cog"></i> Mi Perfil</a>
                    </div>
                </div>
                <div class="d-none d-md-block" style="padding-right: 20px;">
                    <i class="fas fa-cubes" style="font-size: 8rem; color: var(--info-bg); transform: rotate(-10deg);"></i>
                </div>
            </div>
        </div>
    @endif

    {{-- Card de Notificaciones/Alertas de Consumibles al 10% (Críticos y Faltantes) --}}
    @if(isset($consumiblesCriticos) && (esSuperAdmin() || tienePermiso('consumible - leer')))
    <div class="mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.15s;">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden {{ $totalConsumiblesCriticos > 0 ? 'border-start border-danger border-4' : 'border-start border-success border-4' }}" style="background: var(--card-bg, #fff);">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 {{ $totalConsumiblesCriticos > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }}" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas {{ $totalConsumiblesCriticos > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' }}"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h4 class="fw-bold mb-0 text-dark">Alerta de Consumibles Críticos (Stock ≤ 10%)</h4>
                                @if($totalConsumiblesCriticos > 0)
                                <span class="badge bg-danger rounded-pill px-3 py-1">{{ $totalConsumiblesCriticos }} productos requieren atención</span>
                                @else
                                <span class="badge bg-success rounded-pill px-3 py-1">Stock saludable</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-0 mt-1">Monitoreo automático de insumos agotados o con existencias mínimas para compra y reabastecimiento.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-xs" onclick="activarPushNotificaciones()">
                            <i class="fas fa-bell me-1"></i> Activar Notificaciones Push
                        </button>
                        @if (tienePermiso('consumible - leer'))
                        <a href="{{ route('productos_consumibles.index') }}" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs">
                            <i class="fas fa-boxes me-1"></i> Gestionar Consumibles
                        </a>
                        @endif
                    </div>
                </div>

                @if($totalConsumiblesCriticos > 0)
                <!-- Indicadores Rápidos -->
                <div class="row g-2 mb-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="p-2 rounded-3 bg-light d-flex align-items-center justify-content-between">
                            <span class="text-muted small"><i class="fas fa-ban text-danger me-1"></i> Completamente Agotados:</span>
                            <span class="badge bg-danger fw-bold fs-6">{{ $consumiblesAgotados }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="p-2 rounded-3 bg-light d-flex align-items-center justify-content-between">
                            <span class="text-muted small"><i class="fas fa-battery-quarter text-warning me-1"></i> Por Agotarse (1 a 10):</span>
                            <span class="badge bg-warning text-dark fw-bold fs-6">{{ $consumiblesPorAgotar }}</span>
                        </div>
                    </div>
                </div>

                <!-- Grilla de Productos Faltantes / Críticos -->
                <div class="row g-3">
                    @foreach($consumiblesCriticos->take(6) as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="border rounded-3 p-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0 text-dark text-truncate me-2" title="{{ $item->nombre }}">
                                        {{ $item->nombre }}
                                    </h6>
                                    @if($item->existencia <= 0)
                                        <span class="badge bg-danger px-2 py-1 rounded-pill">0 unidades</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">{{ $item->existencia }} rest.</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $item->ubicacion_nombre ?? 'Almacén General' }}
                                </small>
                            </div>
                            
                            <div>
                                @php
                                    $porcentaje = min(100, max(0, ($item->existencia / 20) * 100));
                                    $colorBarra = $item->existencia <= 0 ? 'bg-danger' : 'bg-warning';
                                @endphp
                                <div class="progress" style="height: 6px;" title="Nivel de stock estimado">
                                    <div class="progress-bar {{ $colorBarra }}" role="progressbar" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($totalConsumiblesCriticos > 6)
                <div class="text-center mt-3 pt-2 border-top">
                    <a href="{{ route('productos_consumibles.index') }}" class="text-decoration-none fw-semibold small text-danger">
                        Ver los {{ $totalConsumiblesCriticos - 6 }} consumibles críticos restantes <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @endif

                @else
                <div class="alert alert-success border-0 bg-success bg-opacity-10 d-flex align-items-center mb-0 mt-2 py-2">
                    <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                    <span class="small text-success fw-medium">Excelente estado: todos los consumibles cuentan con existencias superiores al 10%.</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Cards para Agregar Tipos de Productos -->
    <div class="mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--text-main);">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Registrar Productos en el Inventario
                </h4>
                <p class="text-muted small mb-0">Selecciona el tipo de producto que deseas agregar al sistema:</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Card 1: Activos Fijos -->
            <div class="col-lg-4 col-md-6">
                <div class="bento-card primary h-100 shadow-sm p-4 d-flex flex-column justify-content-between position-relative">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bento-card-icon" style="width: 52px; height: 52px; font-size: 1.6rem;">
                                <i class="fas fa-archive"></i>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-semibold">
                                Activo Permanente
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2" style="color: var(--text-main);">Activos Fijos</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; min-height: 48px;">
                            Equipos de cómputo, mobiliario, maquinaria y bienes duraderos asignados con responsable y número de serie.
                        </p>
                    </div>

                    <div class="pt-3 border-top mt-3" style="border-color: var(--border-color) !important;">
                        @if (tienePermiso('fijos - insertar'))
                        <a href="{{ route('productos.indexFijos', ['crear' => 1]) }}" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm mb-2">
                            <i class="fas fa-plus-circle"></i> Agregar Activo Fijo
                        </a>
                        @endif
                        @if (tienePermiso('fijos - leer'))
                        <a href="{{ route('productos.indexFijos') }}" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                            <i class="fas fa-boxes"></i> Ver Activos Fijos
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Consumibles -->
            <div class="col-lg-4 col-md-6">
                <div class="bento-card warning h-100 shadow-sm p-4 d-flex flex-column justify-content-between position-relative">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bento-card-icon" style="width: 52px; height: 52px; font-size: 1.6rem;">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small fw-semibold">
                                Insumos y Suministros
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2" style="color: var(--text-main);">Consumibles</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; min-height: 48px;">
                            Materiales de uso recurrente, papelería, cafetería e insumos operativos con control de existencias mínimas y máximas.
                        </p>
                    </div>

                    <div class="pt-3 border-top mt-3" style="border-color: var(--border-color) !important;">
                        @if (tienePermiso('consumible - insertar'))
                        <a href="{{ route('productos_consumibles.index', ['crear' => 1]) }}" class="btn text-white w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm mb-2" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none;">
                            <i class="fas fa-plus-circle"></i> Agregar Consumible
                        </a>
                        @endif
                        @if (tienePermiso('consumible - leer'))
                        <a href="{{ route('productos_consumibles.index') }}" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                            <i class="fas fa-layer-group"></i> Ver Consumibles
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 3: Compra / Venta -->
            <div class="col-lg-4 col-md-6">
                <div class="bento-card success h-100 shadow-sm p-4 d-flex flex-column justify-content-between position-relative">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bento-card-icon" style="width: 52px; height: 52px; font-size: 1.6rem;">
                                <i class="fas fa-cart-plus"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small fw-semibold">
                                Comercialización
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2" style="color: var(--text-main);">Compra-Venta</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; min-height: 48px;">
                            Mercancía para venta, adquisición para proyectos específicos o comercialización con empresas y clientes externos.
                        </p>
                    </div>

                    <div class="pt-3 border-top mt-3" style="border-color: var(--border-color) !important;">
                        @if (tienePermiso('compra/venta - insertar'))
                        <a href="{{ route('productos_compra_venta.index', ['crear' => 1]) }}" class="btn text-white w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm mb-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                            <i class="fas fa-plus-circle"></i> Agregar Compra-Venta
                        </a>
                        @endif
                        @if (tienePermiso('compra/venta - leer'))
                        <a href="{{ route('productos_compra_venta.index') }}" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                            <i class="fas fa-store"></i> Ver Compra-Venta
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(esSuperAdmin() || tienePermiso('leerUsuarios'))
<!--AJAX para obtener datos del dashboard (Super Admin y Administrador)  -->
<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const res = await fetch("{{ route('api.dashboard') }}");
        const json = await res.json();
        if (json.success && json.data) {
            const r = document.getElementById('stat-registros');
            const m = document.getElementById('stat-movimientos');
            const u = document.getElementById('stat-usuarios');
            if (r) r.textContent = json.data.registros_7_dias ?? 0;
            if (m) m.textContent = json.data.movimientos_7_dias ?? 0;
            if (u) u.textContent = json.data.usuarios_activos ?? 0;
        }
    } catch(e) {
        console.error('Error actualizando dashboard API:', e);
    }
});
</script>
@endif
@endsection