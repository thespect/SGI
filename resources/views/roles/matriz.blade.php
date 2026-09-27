<div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeIn">
    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-primary">
                <i class="fas fa-th me-2"></i>Matriz de Roles y Permisos
            </h4>
            <p class="text-muted small mb-0">Visualiza y configura los permisos de todos los roles en una sola tabla unificada.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small">
                <i class="fas fa-bolt me-1"></i> Guardado Automático en Vivo
            </span>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="colapsarTodosLosModulos()">
                <i class="fas fa-compress-alt me-1"></i> Colapsar
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="expandirTodosLosModulos()">
                <i class="fas fa-expand-alt me-1"></i> Expandir
            </button>
        </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="card-body bg-light bg-opacity-50 border-bottom p-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="buscadorMatriz" class="form-control bg-white border-start-0" placeholder="Buscar permiso o módulo (ej: vehiculo, fijos, crear, etc.)...">
                </div>
            </div>
            <div class="col-md-7 d-flex justify-content-md-end gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-dark filter-tab active" data-filter="todos">
                    Todos ({{ count($permisosPorTipo['categoria'] ?? []) + count($permisosPorTipo['otros'] ?? []) + count($permisosPorTipo['empresa'] ?? []) + count($permisosPorTipo['ubicacion'] ?? []) }})
                </button>
                <button type="button" class="btn btn-sm btn-outline-success filter-tab" data-filter="categoria">
                    <i class="fas fa-boxes me-1"></i> Productos ({{ count($permisosPorTipo['categoria'] ?? []) }})
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary filter-tab" data-filter="otros">
                    <i class="fas fa-sliders-h me-1"></i> Sistema ({{ count($permisosPorTipo['otros'] ?? []) }})
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning filter-tab" data-filter="empresa">
                    <i class="fas fa-building me-1"></i> Empresas ({{ count($permisosPorTipo['empresa'] ?? []) }})
                </button>
                <button type="button" class="btn btn-sm btn-outline-info filter-tab" data-filter="ubicacion">
                    <i class="fas fa-map-marker-alt me-1"></i> Ubicaciones ({{ count($permisosPorTipo['ubicacion'] ?? []) }})
                </button>
            </div>
        </div>
    </div>

    <!-- Formulario para Guardado en Lote (Opcional si se desea submit tradicional) -->
    <form action="{{ route('permisos.actualizarMatriz') }}" method="POST" id="formMatrizPermisos">
        @csrf
        @foreach($rolesTodos as $rol)
            @if($rol->id != 1)
                <input type="hidden" name="roles_afectados[]" value="{{ $rol->id }}">
            @endif
        @endforeach

        <div class="table-responsive" style="max-height: 720px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0 matrix-table">
                <thead class="table-light sticky-top shadow-sm" style="z-index: 10;">
                    <tr>
                        <th style="min-width: 280px; width: 35%;">Módulo y Acción del Permiso</th>
                        <th style="width: 15%; text-align: center;">Tipo</th>
                        @foreach($rolesTodos as $rol)
                        <th style="min-width: 150px; text-align: center;" class="{{ $rol->id == 1 ? 'bg-primary bg-opacity-10' : '' }}">
                            <div class="fw-bold fs-6">{{ $rol->nombre }}</div>
                            @if($rol->id == 1)
                                <span class="badge bg-primary text-white rounded-pill small px-2 py-1">
                                    <i class="fas fa-lock me-1"></i> Total (Inmutable)
                                </span>
                            @else
                                <div class="btn-group btn-group-sm mt-1" role="group">
                                    <button type="button" class="btn btn-xs btn-outline-success py-0 px-1" onclick="toggleColumna({{ $rol->id }}, true)" title="Marcar todos los visibles para este rol">
                                        <i class="fas fa-check-double"></i> Todo
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="toggleColumna({{ $rol->id }}, false)" title="Desmarcar todos los visibles para este rol">
                                        <i class="fas fa-times"></i> Nada
                                    </button>
                                </div>
                            @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                    $secciones = [
                        'categoria' => [
                            'titulo' => 'Categorías y Módulos de Productos',
                            'icono' => 'fas fa-boxes text-success',
                            'badge' => 'bg-success',
                            'items' => $permisosPorTipo['categoria'] ?? []
                        ],
                        'otros' => [
                            'titulo' => 'Permisos Generales y Administrativos',
                            'icono' => 'fas fa-sliders-h text-primary',
                            'badge' => 'bg-primary',
                            'items' => $permisosPorTipo['otros'] ?? []
                        ],
                        'empresa' => [
                            'titulo' => 'Empresas Propietarias',
                            'icono' => 'fas fa-building text-warning',
                            'badge' => 'bg-warning text-dark',
                            'items' => $permisosPorTipo['empresa'] ?? []
                        ],
                        'ubicacion' => [
                            'titulo' => 'Ubicaciones y Almacenes',
                            'icono' => 'fas fa-map-marker-alt text-info',
                            'badge' => 'bg-info',
                            'items' => $permisosPorTipo['ubicacion'] ?? []
                        ],
                    ];
                    @endphp

                    @foreach($secciones as $tipoSeccion => $seccion)
                        @if(!empty($seccion['items']))
                        <!-- Header de Sección -->
                        <tr class="table-secondary module-header" data-tipo-modulo="{{ $tipoSeccion }}" style="cursor: pointer;" onclick="toggleModuloFilas('{{ $tipoSeccion }}')">
                            <td colspan="{{ 2 + count($rolesTodos) }}" class="py-2 px-3 fw-bold">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="{{ $seccion['icono'] }} me-2"></i> {{ $seccion['titulo'] }}
                                        <span class="badge {{ $seccion['badge'] }} bg-opacity-25 text-dark ms-2">{{ count($seccion['items']) }} permisos</span>
                                    </span>
                                    <i class="fas fa-chevron-down toggle-icon-{{ $tipoSeccion }} text-muted"></i>
                                </div>
                            </td>
                        </tr>

                        <!-- Filas de Permisos -->
                        @foreach($seccion['items'] as $nombrePermiso => $data)
                        @php
                            $rowId = 'row_' . md5($tipoSeccion . '_' . $nombrePermiso);
                            $cleanName = ucwords(str_replace([' - ', '_'], ' ', $nombrePermiso));
                        @endphp
                        <tr class="matrix-row modulo-fila-{{ $tipoSeccion }}" data-tipo="{{ $tipoSeccion }}" data-search="{{ strtolower($nombrePermiso . ' ' . $tipoSeccion . ' ' . ($data['empresa_ubicacion'] ?? '')) }}">
                            <!-- Nombre Permiso -->
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ $nombrePermiso }}</div>
                                @if(!empty($data['empresa_ubicacion']))
                                    <small class="text-muted"><i class="fas fa-building me-1"></i> Empresa: {{ $data['empresa_ubicacion'] }}</small>
                                @endif
                            </td>

                            <!-- Badge Tipo -->
                            <td class="text-center">
                                <span class="badge {{ $seccion['badge'] }} bg-opacity-10 text-dark small px-2 py-1">
                                    {{ ucfirst($tipoSeccion) }}
                                </span>
                            </td>

                            <!-- Celdas de Roles -->
                            @foreach($rolesTodos as $rol)
                            @php
                                $permisoRol = $data['roles'][$rol->id] ?? null;
                                $isChecked = $rol->id == 1 ? true : ($permisoRol ? $permisoRol['otorgado'] : false);
                                $permisoId = $permisoRol['id'] ?? null;
                            @endphp
                            <td class="text-center {{ $rol->id == 1 ? 'bg-primary bg-opacity-10' : '' }}">
                                @if($rol->id == 1)
                                    <!-- Super Admin siempre activo -->
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" checked disabled title="Super Admin tiene todos los permisos activos">
                                    </div>
                                @elseif($permisoId)
                                    <div class="form-check form-switch d-inline-block">
                                        <input 
                                            class="form-check-input check-rol-{{ $rol->id }} check-tipo-{{ $tipoSeccion }} matrix-checkbox" 
                                            type="checkbox" 
                                            name="permisos[]" 
                                            value="{{ $permisoId }}" 
                                            id="chk_{{ $permisoId }}" 
                                            data-rol-id="{{ $rol->id }}"
                                            data-permiso-id="{{ $permisoId }}"
                                            data-permiso-nombre="{{ $nombrePermiso }}"
                                            data-rol-nombre="{{ $rol->nombre }}"
                                            {{ $isChecked ? 'checked' : '' }}
                                            onchange="togglePermisoLive(this)"
                                        >
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <div class="text-muted small" id="contadorResultadosMatriz">
                Mostrando todos los permisos disponibles.
            </div>
            <div>
                <button type="submit" class="btn btn-primary shadow-sm px-4">
                    <i class="fas fa-save me-2"></i> Guardar Todo en Lote
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Toast Notificación en Vivo -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
    <div id="liveToastMatriz" class="toast align-items-center text-white bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center" id="toastMessageMatriz">
                <i class="fas fa-check-circle text-success me-2 fs-5"></i> Permiso actualizado correctamente.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
.matrix-table tbody tr:hover td {
    background-color: rgba(13, 110, 253, 0.04);
}
.matrix-checkbox:checked {
    background-color: #198754;
    border-color: #198754;
}
.btn-xs {
    padding: 0.15rem 0.4rem;
    font-size: 0.72rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}
</style>

<script>
// Filtro por pestañas de categoría
document.querySelectorAll('.filter-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab').forEach(b => {
            b.classList.remove('active', 'btn-dark');
            b.classList.add('btn-outline-' + (b.getAttribute('data-filter') === 'categoria' ? 'success' : (b.getAttribute('data-filter') === 'otros' ? 'primary' : (b.getAttribute('data-filter') === 'empresa' ? 'warning' : (b.getAttribute('data-filter') === 'ubicacion' ? 'info' : 'secondary')))));
        });
        this.classList.add('active', 'btn-dark');
        
        const filter = this.getAttribute('data-filter');
        aplicarFiltros(filter, document.getElementById('buscadorMatriz').value);
    });
});

// Buscador en tiempo real
document.getElementById('buscadorMatriz').addEventListener('input', function() {
    const activeFilter = document.querySelector('.filter-tab.active').getAttribute('data-filter');
    aplicarFiltros(activeFilter, this.value);
});

function aplicarFiltros(tipoFilter, query) {
    query = (query || '').toLowerCase().trim();
    let visibles = 0;

    document.querySelectorAll('.matrix-row').forEach(row => {
        const rowTipo = row.getAttribute('data-tipo');
        const rowSearch = row.getAttribute('data-search') || '';

        const coincideTipo = (tipoFilter === 'todos' || rowTipo === tipoFilter);
        const coincideQuery = (query === '' || rowSearch.includes(query));

        if (coincideTipo && coincideQuery) {
            row.style.display = '';
            visibles++;
        } else {
            row.style.display = 'none';
        }
    });

    // Control de encabezados de módulo
    document.querySelectorAll('.module-header').forEach(header => {
        const modTipo = header.getAttribute('data-tipo-modulo');
        if (tipoFilter !== 'todos' && modTipo !== tipoFilter) {
            header.style.display = 'none';
        } else {
            // Verificar si tiene alguna fila visible
            const filasVisibles = document.querySelectorAll('.modulo-fila-' + modTipo + ':not([style*="display: none"])');
            header.style.display = filasVisibles.length > 0 ? '' : 'none';
        }
    });

    const contador = document.getElementById('contadorResultadosMatriz');
    if (contador) {
        contador.textContent = `Mostrando ${visibles} permisos filtrados.`;
    }
}

// Colapsar y expandir módulos
function toggleModuloFilas(tipo) {
    const filas = document.querySelectorAll('.modulo-fila-' + tipo);
    const icono = document.querySelector('.toggle-icon-' + tipo);
    let estanOcultas = false;

    filas.forEach(f => {
        if (f.style.display === 'none') {
            f.style.display = '';
        } else {
            f.style.display = 'none';
            estanOcultas = true;
        }
    });

    if (icono) {
        icono.className = estanOcultas 
            ? 'fas fa-chevron-right toggle-icon-' + tipo + ' text-muted' 
            : 'fas fa-chevron-down toggle-icon-' + tipo + ' text-muted';
    }
}

function colapsarTodosLosModulos() {
    ['categoria', 'otros', 'empresa', 'ubicacion'].forEach(tipo => {
        document.querySelectorAll('.modulo-fila-' + tipo).forEach(f => f.style.display = 'none');
        const icono = document.querySelector('.toggle-icon-' + tipo);
        if (icono) icono.className = 'fas fa-chevron-right toggle-icon-' + tipo + ' text-muted';
    });
}

function expandirTodosLosModulos() {
    ['categoria', 'otros', 'empresa', 'ubicacion'].forEach(tipo => {
        document.querySelectorAll('.modulo-fila-' + tipo).forEach(f => f.style.display = '');
        const icono = document.querySelector('.toggle-icon-' + tipo);
        if (icono) icono.className = 'fas fa-chevron-down toggle-icon-' + tipo + ' text-muted';
    });
}

// Marcar / Desmarcar columna completa
function toggleColumna(rolId, marcar) {
    const checkboxes = document.querySelectorAll('.check-rol-' + rolId);
    checkboxes.forEach(cb => {
        // Solo si la fila es visible
        if (cb.closest('tr').style.display !== 'none') {
            if (cb.checked !== marcar) {
                cb.checked = marcar;
                togglePermisoLive(cb, false); // No spammear toast
            }
        }
    });

    mostrarToast(`Se han ${marcar ? 'activado' : 'desactivado'} los permisos visibles para el rol.`);
}

// Guardado en vivo via AJAX
async function togglePermisoLive(checkbox, showIndividualToast = true) {
    const permisoRolId = checkbox.getAttribute('data-permiso-id');
    const nuevoStatus = checkbox.checked ? 'activo' : 'inactivo';
    const permisoNombre = checkbox.getAttribute('data-permiso-nombre');
    const rolNombre = checkbox.getAttribute('data-rol-nombre');

    try {
        const res = await fetch("{{ route('permisos.toggleAjax') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                permiso_rol_id: permisoRolId,
                status: nuevoStatus
            })
        });

        const data = await res.json();
        if (data.success) {
            if (showIndividualToast) {
                mostrarToast(`Permiso "${permisoNombre}" ${nuevoStatus == 'activo' ? 'activado' : 'desactivado'} para ${rolNombre}.`);
            }
        } else {
            checkbox.checked = !checkbox.checked; // Revertir
            mostrarToast("Error: " + (data.message || 'No se pudo actualizar.'), true);
        }
    } catch (e) {
        console.error("Error al actualizar permiso:", e);
        checkbox.checked = !checkbox.checked; // Revertir
        mostrarToast("Error de conexión al actualizar permiso.", true);
    }
}

function mostrarToast(mensaje, esError = false) {
    const toastEl = document.getElementById('liveToastMatriz');
    const msgEl = document.getElementById('toastMessageMatriz');
    if (!toastEl || !msgEl) return;

    toastEl.className = esError 
        ? 'toast align-items-center text-white bg-danger border-0 shadow-lg'
        : 'toast align-items-center text-white bg-dark border-0 shadow-lg';

    msgEl.innerHTML = (esError ? '<i class="fas fa-exclamation-triangle text-warning me-2 fs-5"></i> ' : '<i class="fas fa-check-circle text-success me-2 fs-5"></i> ') + mensaje;

    const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
    toast.show();
}
</script>
