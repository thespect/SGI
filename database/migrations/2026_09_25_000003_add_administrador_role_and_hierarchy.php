<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\RolesAndHierarchySeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new RolesAndHierarchySeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se puede marcar como inactivo o mantener registros
    }
};
