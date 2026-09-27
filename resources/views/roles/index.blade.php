@extends('layouts.navigation')
@section('title', 'Roles y Permisos')
@section('content')
<div class="container-fluid py-3 px-md-4">
    <!-- Header de la pantalla con Selector de Vistas -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--text-main, #242423);">
                <i class="fas fa-shield-alt text-primary me-2"></i>Gestión de Roles y Permisos
            </h1>
            <p class="text-muted small mb-0">Administra los roles del sistema y la matriz de permisos para cada uno.</p>
        </div>
        
        <!-- Pestañas Switcher de Vista -->
        <div class="bg-light p-1 rounded-pill border d-inline-flex shadow-sm">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold tab-view-btn active" id="btnVistaMatriz" onclick="cambiarVistaRoles('matriz')">
                <i class="fas fa-th me-1"></i> Matriz de Roles (Tabla)
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold text-muted tab-view-btn" id="btnVistaDetallada" onclick="cambiarVistaRoles('detallada')">
                <i class="fas fa-list-ul me-1"></i> Vista Detalle por Rol
            </button>
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

    <!-- 1. VISTA: MATRIZ DE ROLES (TABLA) -->
    <div id="contenedorMatrizRoles" class="vista-seccion animate__animated animate__fadeIn">
        @include('roles.matriz', [
            'rolesTodos' => $rolesTodos ?? [],
            'permisosPorTipo' => $permisosPorTipo ?? []
        ])
    </div>

    <!-- 2. VISTA: DETALLADA POR ROL (LIVEWIRE) -->
    <div id="contenedorDetalleRoles" class="vista-seccion d-none animate__animated animate__fadeIn">
        <div class="row g-3">
            <!-- Sidebar de Roles -->
            <div class="col-lg-3 col-md-4" id="roleSidebar">
                @livewire('role-list')
            </div>

            <!-- Contenido Principal: Permisos del Rol Seleccionado -->
            <div class="col-lg-9 col-md-8">
                @livewire('role-details')
            </div>
        </div>
    </div>
</div>

<script>
    function cambiarVistaRoles(vista) {
        const contenedorMatriz = document.getElementById('contenedorMatrizRoles');
        const contenedorDetalle = document.getElementById('contenedorDetalleRoles');
        const btnMatriz = document.getElementById('btnVistaMatriz');
        const btnDetalle = document.getElementById('btnVistaDetallada');

        if (vista === 'matriz') {
            contenedorMatriz.classList.remove('d-none');
            contenedorDetalle.classList.add('d-none');

            btnMatriz.classList.add('active', 'btn-primary', 'text-white');
            btnMatriz.classList.remove('text-muted');

            btnDetalle.classList.remove('active', 'btn-primary', 'text-white');
            btnDetalle.classList.add('text-muted');

            localStorage.setItem('sgi_vista_roles', 'matriz');
        } else {
            contenedorMatriz.classList.add('d-none');
            contenedorDetalle.classList.remove('d-none');

            btnDetalle.classList.add('active', 'btn-primary', 'text-white');
            btnDetalle.classList.remove('text-muted');

            btnMatriz.classList.remove('active', 'btn-primary', 'text-white');
            btnMatriz.classList.add('text-muted');

            localStorage.setItem('sgi_vista_roles', 'detallada');
        }
    }

    // Restaurar preferencia del usuario o por defecto mostrar Matriz
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const vistaUrl = params.get('vista');
        const guardada = vistaUrl || localStorage.getItem('sgi_vista_roles') || 'matriz';
        cambiarVistaRoles(guardada);
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('seleccionar-todo')) {
            const target = event.target.getAttribute('data-target');
            const container = document.querySelector(target);
            if (container) {
                container.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
            }
        }

        if (event.target.classList.contains('deseleccionar-todo')) {
            const target = event.target.getAttribute('data-target');
            const container = document.querySelector(target);
            if (container) {
                container.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            }
        }
    });
</script>

<style>
.tab-view-btn.active {
    background-color: var(--primary, #0d6efd) !important;
    color: #fff !important;
}
</style>

@endsection

@include('roles.modales.rolModal')