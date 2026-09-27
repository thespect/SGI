<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        if (!Session::has('usuario')) {
            return appRedirectToLogin('Inicia sesión para acceder a los logs');
        }

        if (!esSuperAdmin() && !tienePermiso('leerUsuarios') && !tienePermiso('leerRol')) {
            return appRedirectToHome('No cuentas con los permisos necesarios para ver los logs del sistema.');
        }

        $query = Movimiento::with('usuario')->orderBy('fecha', 'desc')->orderBy('id', 'desc');

        // Filtro por tipo de acción agrupada
        if ($request->filled('tipo_accion')) {
            $tipo = $request->tipo_accion;
            if ($tipo === 'creacion') {
                $query->where(function($q) {
                    $q->where('accion', 'like', '%registro%')
                      ->orWhere('accion', 'like', '%crea%')
                      ->orWhere('accion', 'like', '%alta%');
                });
            } elseif ($tipo === 'edicion') {
                $query->where(function($q) {
                    $q->where('accion', 'like', '%actualiz%')
                      ->orWhere('accion', 'like', '%modifi%')
                      ->orWhere('accion', 'like', '%cambio%')
                      ->orWhere('accion', 'like', '%subida%');
                });
            } elseif ($tipo === 'eliminacion') {
                $query->where(function($q) {
                    $q->where('accion', 'like', '%baja%')
                      ->orWhere('accion', 'like', '%elimina%')
                      ->orWhere('accion', 'like', '%desactiv%');
                });
            } elseif ($tipo === 'consumo') {
                $query->where(function($q) {
                    $q->where('accion', 'like', '%consumo%')
                      ->orWhere('accion', 'like', '%disminuir%');
                });
            }
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Filtro por usuario
        if ($request->filled('usuario_id')) {
            $query->where('id_user', $request->usuario_id);
        }

        // Filtro por fecha desde / hasta
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Búsqueda por texto libre
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function($q) use ($buscar) {
                $q->where('comentario', 'like', "%{$buscar}%")
                  ->orWhere('accion', 'like', "%{$buscar}%");
            });
        }

        $movimientos = $query->paginate(20)->appends($request->query());

        // KPIs estadísticos
        $stats = [
            'total' => Movimiento::count(),
            'hoy' => Movimiento::whereDate('fecha', now()->toDateString())->count(),
            'creaciones' => Movimiento::where('accion', 'like', '%registro%')->orWhere('accion', 'like', '%crea%')->count(),
            'ediciones' => Movimiento::where('accion', 'like', '%actualiz%')->orWhere('accion', 'like', '%modifi%')->count(),
            'eliminaciones' => Movimiento::where('accion', 'like', '%baja%')->orWhere('accion', 'like', '%elimina%')->count(),
            'consumos' => Movimiento::where('accion', 'like', '%consumo%')->orWhere('accion', 'like', '%disminuir%')->count(),
        ];

        $usuarios = Usuario::where('estado', 'activo')->orderBy('nombre')->get();

        return view('movimientos.index', compact('movimientos', 'stats', 'usuarios'));
    }
}
