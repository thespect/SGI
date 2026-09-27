<?php

namespace App\Http\Controllers;

use App\Models\PermisoRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PermisosController extends Controller
{

    public function actualizarPermisos(Request $request)
    {
        if (!Session::has('usuario')  || !tienePermiso('modificarRol')) {
            return appRedirectToHome('No cuenta con los permisos necesarios');
        }
        if($request->rol_id == 1) {
            return redirect()->back()->with('error', 'No se pueden modificar los permisos del rol de administrador.');
        }

        try {
            $request->validate([
                'permisos' => 'required|array',
            ]);

            PermisoRol::where('rol_id', $request->rol_id)->update(['status' => 'inactivo']);

            PermisoRol::whereIn('id', $request->permisos)->update(['status' => 'activo']);
            return redirect()->back()->with('success', 'Permisos actualizados correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar permisos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar los permisos.');
        }
    }

    public function actualizarMatriz(Request $request)
    {
        if (!Session::has('usuario') || (!esSuperAdmin() && !tienePermiso('modificarRol'))) {
            return redirect()->back()->with('error', 'No cuenta con los permisos necesarios');
        }

        try {
            $permisosActivos = $request->input('permisos', []);
            $roles = $request->input('roles_afectados', []);

            $rolesValidos = array_filter($roles, fn($id) => (int)$id !== 1);

            if (!empty($rolesValidos)) {
                // Desactivar todos los permisos para los roles modificados
                PermisoRol::whereIn('rol_id', $rolesValidos)->update(['status' => 'inactivo']);

                // Activar los seleccionados
                if (!empty($permisosActivos)) {
                    PermisoRol::whereIn('id', $permisosActivos)
                        ->whereIn('rol_id', $rolesValidos)
                        ->update(['status' => 'activo']);
                }
            }

            return redirect()->back()->with('success', 'Matriz de roles y permisos actualizada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar matriz de roles: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar los cambios: ' . $e->getMessage());
        }
    }

    public function togglePermisoAjax(Request $request)
    {
        if (!Session::has('usuario') || (!esSuperAdmin() && !tienePermiso('modificarRol'))) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $request->validate([
            'permiso_rol_id' => 'required|integer|exists:permisos_rol,id',
            'status' => 'required|in:activo,inactivo',
        ]);

        $permisoRol = PermisoRol::findOrFail($request->permiso_rol_id);

        if ($permisoRol->rol_id == 1) {
            return response()->json(['success' => false, 'message' => 'No se pueden modificar los permisos del Super Admin.'], 400);
        }

        $permisoRol->status = $request->status;
        $permisoRol->save();

        return response()->json([
            'success' => true,
            'message' => 'Permiso actualizado correctamente',
            'permiso_rol_id' => $permisoRol->id,
            'nuevo_status' => $permisoRol->status
        ]);
    }
}

