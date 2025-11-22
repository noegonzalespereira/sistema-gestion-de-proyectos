<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('seguimiento_proyecto', function (Blueprint $table) {
            $table->id('id_seguimiento');

            // 🔹 Relación con la asignación de proyecto
            $table->unsignedBigInteger('id_asignacion');
            $table->foreign('id_asignacion')
                  ->references('id_asignacion')
                  ->on('asignacion_proyecto')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // 🔹 Campos de seguimiento
            $table->string('modulo', 150)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('estado', 50)->nullable();
            $table->text('observacion')->nullable();
            $table->string('archivo_url', 255)->nullable();
            $table->string('archivo_correccion_url', 255)->nullable();
            $table->date('fecha_limite')->nullable();
            $table->timestamp('fecha_actualizacion')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('seguimiento_proyecto');
    }
};
