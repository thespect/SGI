<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('esSuperAdmin')) {
    function esSuperAdmin(): bool
    {
        $usuario = Session::get('usuario');
        return $usuario && (int)($usuario->id_rol ?? 0) === 1;
    }
}

if (!function_exists('esAdmin')) {
    function esAdmin(): bool
    {
        if (esSuperAdmin()) {
            return true;
        }
        $usuario = Session::get('usuario');
        return $usuario && (int)($usuario->id_rol ?? 0) === 4;
    }
}

function tienePermiso(string $nombrePermiso): bool
{
    if (esSuperAdmin()) {
        return true;
    }

    $permisos = Session::get('usuario_permisos', []);

    foreach ($permisos as $permiso) {
        if (
            $permiso->permiso_nombre === $nombrePermiso &&
            $permiso->permiso_otorgado === 'activo' &&
            $permiso->permiso_activo === 'activo'
        ) {
            return true;
        }
    }

    return false;
}

function nombreCompletoUsuario(): string
{
    $usuario = Session::get('usuario');
    if ($usuario) {
        return "{$usuario->nombre} {$usuario->apellido} {$usuario->apellido_m}";
    }
    return 'Invitado';
}

// app/helpers.php

if (!function_exists('formatCamelCase')) {
    function formatCamelCase($text)
    {
        return ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', $text));
    }
}

if (!function_exists('getPermisoIcon')) {
    function getPermisoIcon($permiso)
    {
        return match (true) {
            str_contains($permiso, 'leer') => 'bi-book text-info',
            str_contains($permiso, 'insertar') => 'bi-plus-circle text-success',
            str_contains($permiso, 'modificar') => 'bi-pencil-square text-warning',
            str_contains($permiso, 'desactivar') => 'bi-slash-circle text-danger',
            default => 'bi-shield-lock text-primary'
        };
    }
}

if (!function_exists('cleanAction')) {
    function cleanAction($permisoNombre)
    {
        return trim(explode('-', $permisoNombre)[1] ?? $permisoNombre);
    }
}

if (!function_exists('appRedirect')) {
    function appRedirect(string $path = '', array $params = []): \Illuminate\Http\RedirectResponse
    {
        $url = rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
        $response = redirect($url);
        foreach ($params as $key => $value) {
            $response = $response->with($key, $value);
        }
        return $response;
    }
}

if (!function_exists('appRedirectToLogin')) {
    function appRedirectToLogin(?string $error = null): \Illuminate\Http\RedirectResponse
    {
        $response = appRedirect();
        if ($error) {
            $response = $response->with('error', $error);
        }
        return $response;
    }
}

if (!function_exists('appRedirectToHome')) {
    function appRedirectToHome(?string $error = null): \Illuminate\Http\RedirectResponse
    {
        $response = appRedirect('home');
        if ($error) {
            $response = $response->with('error', $error);
        }
        return $response;
    }
}

if (!function_exists('obtenerConsumiblesCriticos')) {
    function obtenerConsumiblesCriticos()
    {
        return \Illuminate\Support\Facades\Cache::remember('consumibles_criticos_nav', 60, function() {
            return \Illuminate\Support\Facades\DB::table('productos_consumibles as pc')
                ->join('productos as p', 'pc.producto_id', '=', 'p.id')
                ->leftJoin('ubicacion as u', 'p.ubicacion_id', '=', 'u.id')
                ->select('pc.id as consumible_id', 'p.nombre', 'pc.existencia', 'u.nombre as ubicacion_nombre')
                ->where('pc.existencia', '<=', 10)
                ->orderBy('pc.existencia', 'asc')
                ->limit(10)
                ->get();
        });
    }
}

if (!function_exists('contarConsumiblesCriticos')) {
    function contarConsumiblesCriticos()
    {
        return \Illuminate\Support\Facades\Cache::remember('count_consumibles_criticos', 60, function() {
            return \Illuminate\Support\Facades\DB::table('productos_consumibles')
                ->where('existencia', '<=', 10)
                ->count();
        });
    }
}

