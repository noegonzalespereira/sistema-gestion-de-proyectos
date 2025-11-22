<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Esta migración era un parche para una BD vieja.
        // En el nuevo esquema limpio ya no hace nada.
    }

    public function down(): void
    {
        // Nada
    }
};
