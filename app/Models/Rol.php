<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $fillable = ['nombre', 'status', 'eliminado'];
    public $timestamps = false;

    /**
     * Ordenar los roles por su jerarquía de importancia definida:
     * 1. Super Admin, 2. Administrador, 3. Almacenistas, 4. Auxiliares
     */
    protected static function booted()
    {
        static::addGlobalScope('jerarquia', function ($builder) {
            $builder->orderByRaw("FIELD(nombre, 'Super Admin', 'Administrador', 'Admin', 'Almacenistas', 'Auxiliares'), id ASC");
        });
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }

    public function permisosUbicacion()
    {
        return $this->hasMany(PermisoUbicacion::class, 'rol_id');
    }

    public function permisosCategoria()
    {
        return $this->hasMany(PermisoCategoria::class, 'rol_id');
    }

    public function permisosOtros()
    {
        return PermisoOtro::where('rol_id', $this->id)->get();
    }

    public function permisosEmpresa()
    {
        return $this->hasMany(PermisoEmpresa::class, 'rol_id');
    }

    public function users_count()
    {
        return $this->usuarios()->count();
    }
}
