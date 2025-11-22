<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asignacion_proyecto', function (Blueprint $table) {
            $table->id('id_asignacion');

            // 🔹 Usuario asignador (Administrador o Docente)
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // 🔹 Título del proyecto asignado
            $table->string('titulo_proyecto')->nullable();

            // 🔹 Relaciones opcionales
            $table->unsignedBigInteger('id_tutor')->nullable();
            $table->foreign('id_tutor')
                  ->references('id_tutor')
                  ->on('tutores')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->unsignedBigInteger('id_estudiante')->nullable();
            $table->foreign('id_estudiante')
                  ->references('id_estudiante')
                  ->on('estudiantes')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->unsignedBigInteger('id_carrera')->nullable();
            $table->foreign('id_carrera')
                  ->references('id_carrera')
                  ->on('carreras')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->unsignedBigInteger('id_programa')->nullable();
            $table->foreign('id_programa')
                  ->references('id_programa')
                  ->on('programas')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            // 🔹 Datos de seguimiento
            $table->date('fecha_asignacion')->nullable();
            $table->string('estado', 50)->nullable();
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('asignacion_proyecto');
    }
};
