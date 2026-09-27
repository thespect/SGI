<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Rol;
use App\Models\PermisoRol;

class RolesAndHierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Crear o recuperar el Rol Administrador (Tier 2)
            $rolAdmin = Rol::withoutGlobalScopes()->firstOrCreate(
                ['nombre' => 'Administrador'],
                ['status' => 'activo', 'eliminado' => 1]
            );

            // 2. Clonar plantilla de permisos de Super Admin (rol_id = 1) para Administrador
            $templatePermisos = PermisoRol::where('rol_id', 1)->get();
            foreach ($templatePermisos as $p) {
                PermisoRol::firstOrCreate([
                    'rol_id' => $rolAdmin->id,
                    'permiso_id' => $p->permiso_id,
                    'permiso_tipo' => $p->permiso_tipo,
                ], [
                    'status' => 'activo'
                ]);
            }

            // 3. Permisos de Administrador: Totalidad operativa y de administración de usuarios/empresas/ubicaciones
            PermisoRol::where('rol_id', $rolAdmin->id)->update(['status' => 'activo']);

            // Excluir la gestión estructural de roles (reservada para Super Admin)
            $rolesGestionIds = DB::table('permisos_otros')
                ->whereIn('nombre', ['insertarRol', 'modificarRol', 'desactivarRol'])
                ->pluck('id');

            PermisoRol::where('rol_id', $rolAdmin->id)
                ->where('permiso_tipo', 'otros')
                ->whereIn('permiso_id', $rolesGestionIds)
                ->update(['status' => 'inactivo']);

            // El Administrador NO puede borrar definitivamente nada (exclusivo Super Admin)
            $desactivarIds = DB::table('vista_permisos')
                ->where('rol', $rolAdmin->id)
                ->where('permiso_nombre', 'like', '%desactivar%')
                ->pluck('id');

            PermisoRol::whereIn('id', $desactivarIds)
                ->update(['status' => 'inactivo']);

            // 4. Permisos para Almacenistas (ID 3):
            // Control operativo total de inventario (fijos, compra/venta, consumibles, vehiculos)
            PermisoRol::where('rol_id', 3)->where('permiso_tipo', 'categoria')->update(['status' => 'activo']);

            // Permisos generales de almacén
            $almacenOtrosActivos = DB::table('permisos_otros')
                ->whereIn('nombre', ['leerEmpresaInterna', 'leerEmpresaExterna', 'leerUbicaciones', 'leerEtiquetas', 'insertarEtiquetas'])
                ->pluck('id');

            PermisoRol::where('rol_id', 3)->where('permiso_tipo', 'otros')->update(['status' => 'inactivo']);
            PermisoRol::where('rol_id', 3)->where('permiso_tipo', 'otros')->whereIn('permiso_id', $almacenOtrosActivos)->update(['status' => 'activo']);

            // 5. Permisos para Auxiliares (ID 2):
            // Operación básica de soporte: lectura y captura diaria, sin facultades destructivas ni edición de fijos
            $catAuxInactivas = DB::table('permisos_categoria')
                ->whereIn('accion', [
                    'fijos - modificar', 'fijos - desactivar',
                    'compra/venta - desactivar',
                    'consumible - desactivar',
                    'vehiculo - insertar', 'vehiculo - modificar', 'vehiculo - desactivar'
                ])->pluck('id');

            PermisoRol::where('rol_id', 2)->where('permiso_tipo', 'categoria')->update(['status' => 'activo']);
            PermisoRol::where('rol_id', 2)->where('permiso_tipo', 'categoria')->whereIn('permiso_id', $catAuxInactivas)->update(['status' => 'inactivo']);

            $auxOtrosActivos = DB::table('permisos_otros')
                ->whereIn('nombre', ['leerEmpresaInterna', 'leerEmpresaExterna', 'leerUbicaciones', 'leerEtiquetas'])
                ->pluck('id');

            PermisoRol::where('rol_id', 2)->where('permiso_tipo', 'otros')->update(['status' => 'inactivo']);
            PermisoRol::where('rol_id', 2)->where('permiso_tipo', 'otros')->whereIn('permiso_id', $auxOtrosActivos)->update(['status' => 'activo']);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
