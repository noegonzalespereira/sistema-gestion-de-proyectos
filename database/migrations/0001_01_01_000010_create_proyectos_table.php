<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id('id_proyecto');
            $table->string('titulo');
            $table->text('resumen');

            // Relaciones principales
            $table->unsignedBigInteger('id_programa');
            $table->unsignedBigInteger('id_carrera');
            $table->unsignedBigInteger('id_estudiante');
            $table->unsignedBigInteger('id_tutor');
            $table->unsignedBigInteger('id_tribunal');
            $table->unsignedBigInteger('id_usuario'); // quien registró

            // 🔹 Foreign Keys
            $table->foreign('id_programa')->references('id_programa')->on('programas')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('id_carrera')->references('id_carrera')->on('carreras')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('id_estudiante')->references('id_estudiante')->on('estudiantes')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('id_tutor')->references('id_tutor')->on('tutores')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('id_tribunal')->references('id_tribunal')->on('tribunales')
                  ->cascadeOnUpdate()->restrictOnDelete();

            // 👇 Aquí el cambio importante
            $table->foreign('id_usuario')->references('id')->on('users')
                  ->cascadeOnUpdate()->restrictOnDelete();

            // Campos adicionales
            $table->integer('anio');
            $table->date('fecha_defensa');
            $table->date('fecha_aprobacion')->nullable();
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->string('link_pdf')->nullable();
            $table->date('fecha_registro')->nullable();
            $table->string('estado', 50)->default('Registrado');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('proyectos');
    }
};
