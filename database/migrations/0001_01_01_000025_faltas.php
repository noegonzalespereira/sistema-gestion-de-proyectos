<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faltas_modulo', function (Blueprint $table) {
    $table->id('id_falta');

    $table->unsignedBigInteger('id_asignacion');
    $table->unsignedBigInteger('id_modulo');
    $table->unsignedBigInteger('id_estudiante');

    $table->date('fecha_limite_original');
    $table->dateTime('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));

    $table->string('motivo')->default('No entregó avance en la fecha límite');
    $table->boolean('bloqueado')->default(true);
    $table->boolean('rehabilitado')->default(false);
    $table->date('nueva_fecha_limite')->nullable();

    $table->foreign('id_asignacion')->references('id_asignacion')->on('asignacion_proyecto')->onDelete('cascade');
    $table->foreign('id_modulo')->references('id_modulo')->on('modulos')->onDelete('cascade');
});

    }

    public function down(): void
    {
        
    }
};
