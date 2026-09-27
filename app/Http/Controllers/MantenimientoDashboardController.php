<?php

namespace App\Http\Controllers;

use App\Models\MantenimientoVehiculo;
use App\Models\Movimiento;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MantenimientoDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!Session::has('usuario')) {
            return appRedirectToLogin('Inicia sesión para acceder');
        }

        if (!esSuperAdmin() && !tienePermiso('vehiculo - leer')) {
            return appRedirectToHome('No cuentas con los permisos necesarios para ver mantenimientos.');
        }

        $query = MantenimientoVehiculo::with([
            'vehiculo' => function($q) {
                $q->with(['usuarioResponsable.rol', 'ubicacion']);
            }
        ])->orderBy('fecha', 'desc')->orderBy('id', 'desc');

        // Filtro por Vehículo
        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        // Filtro por Responsable ("A cargo de...")
        if ($request->filled('responsable_id')) {
            $responsableId = $request->responsable_id;
            $query->whereHas('vehiculo', function($q) use ($responsableId) {
                $q->where('responsable', $responsableId);
            });
        }

        // Filtro por tipo de servicio
        if ($request->filled('tipo_servicio')) {
            $query->where('tipo_servicio', 'like', '%' . $request->tipo_servicio . '%');
        }

        // Filtro por fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Búsqueda por texto (marca, modelo, placas, taller, descripción)
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function($q) use ($buscar) {
                $q->where('tipo_servicio', 'like', "%{$buscar}%")
                  ->orWhere('taller', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%")
                  ->orWhereHas('vehiculo', function($vq) use ($buscar) {
                      $vq->where('marca', 'like', "%{$buscar}%")
                         ->orWhere('modelo', 'like', "%{$buscar}%")
                         ->orWhere('placas', 'like', "%{$buscar}%");
                  });
            });
        }

        $mantenimientos = $query->paginate(15)->appends($request->query());

        // KPIs Estadísticos
        $stats = [
            'total_servicios' => MantenimientoVehiculo::count(),
            'total_vehiculos' => Vehiculo::where('eliminado', 0)->count(),
            'vehiculos_con_responsable' => Vehiculo::where('eliminado', 0)->whereNotNull('responsable')->count(),
            'servicios_recientes' => MantenimientoVehiculo::where('fecha', '>=', now()->subDays(30))->count(),
        ];

        // Listas para filtros y modal
        $vehiculos = Vehiculo::where('eliminado', 0)->with('usuarioResponsable')->orderBy('marca')->get();
        $responsables = Usuario::where('estado', 'activo')
            ->whereIn('id', Vehiculo::where('eliminado', 0)->pluck('responsable')->filter())
            ->orderBy('nombre')
            ->get();

        return view('mantenimientos.dashboard', compact('mantenimientos', 'stats', 'vehiculos', 'responsables'));
    }

    public function store(Request $request)
    {
        if (!Session::has('usuario') || (!esSuperAdmin() && !tienePermiso('vehiculo - modificar') && !tienePermiso('vehiculo - insertar'))) {
            return redirect()->back()->with('error', 'No cuentas con los permisos necesarios para registrar mantenimiento.');
        }

        $validated = $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha' => 'required|date',
            'kilometraje' => 'required|integer|min:0',
            'tipo_servicio' => 'required|string|max:100',
            'taller' => 'required|string|max:100',
            'descripcion' => 'required|string',
        ]);

        try {
            $mantenimiento = MantenimientoVehiculo::create($validated);

            $vehiculo = Vehiculo::with('usuarioResponsable')->find($request->vehiculo_id);
            $nombreResponsable = $vehiculo?->usuarioResponsable ? ($vehiculo->usuarioResponsable->nombre . ' ' . $vehiculo->usuarioResponsable->apellido) : 'Sin asignar';

            // Registrar movimiento en logs
            Movimiento::create([
                'id_user' => session('usuario')->id,
                'id_producto' => $request->vehiculo_id,
                'categoria' => 'vehiculo',
                'accion' => 'Registro de mantenimiento',
                'comentario' => "Servicio: {$request->tipo_servicio} en {$request->taller} ({$request->kilometraje} KM). Responsable a cargo: {$nombreResponsable}. {$request->descripcion}",
            ]);

            return redirect()->back()->with('success', 'Mantenimiento vehicular registrado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al registrar mantenimiento desde dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al registrar el mantenimiento: ' . $e->getMessage());
        }
    }
}
