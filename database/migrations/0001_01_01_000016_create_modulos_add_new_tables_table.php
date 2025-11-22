<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ============= MODULOS =============
        Schema::create('modulos', function (Blueprint $table) {
            $table->id('id_modulo');

            // Relación con asignación de proyecto
            $table->unsignedBigInteger('id_asignacion')->nullable();

            // Datos del módulo
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();

            // Estos campos ya los ibas agregando en la mig 15
            $table->enum('estado', ['pendiente', 'observado', 'aprobado'])
                  ->default('pendiente');
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->date('fecha_limite')->nullable();

            $table->timestamps();

            $table->foreign('id_asignacion')
                  ->references('id_asignacion')
                  ->on('asignacion_proyecto')
                  ->cascadeOnDelete();
        });

        // ============= AVANCES =============
        Schema::create('avances', function (Blueprint $table) {
            $table->id('id_avance');

            $table->unsignedBigInteger('id_asignacion')->nullable();

            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('archivo_url', 255)->nullable();

            $table->timestamps();

            $table->foreign('id_asignacion')
                  ->references('id_asignacion')
                  ->on('asignacion_proyecto')
                  ->cascadeOnDelete();
        });

        // ============= CORRECCIONES =============
        Schema::create('correcciones', function (Blueprint $table) {
            $table->id('id_correccion');

            $table->unsignedBigInteger('id_asignacion')->nullable();

            $table->text('descripcion')->nullable();
            $table->string('archivo_correccion_url', 255)->nullable();

            $table->timestamps();

            $table->foreign('id_asignacion')
                  ->references('id_asignacion')
                  ->on('asignacion_proyecto')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correcciones');
        Schema::dropIfExists('avances');
        Schema::dropIfExists('modulos');
    }
};
