<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carreras', function (Blueprint $table) {
            $table->id('id_carrera');
            
            // 👇 CAMBIO CRÍTICO: Añadir la clave foránea a Institución
            $table->unsignedBigInteger('id_institucion');
            
            $table->string('nombre');
            $table->string('sigla', 20)->nullable(); // Añadido 'sigla' que usa el Seeder
            $table->timestamps();

            // Definición de la Clave Foránea
            $table->foreign('id_institucion')
                  ->references('id_institucion')
                  ->on('institucion')
                  ->restrictOnDelete() // No permitir borrar Institución si tiene Carreras
                  ->cascadeOnUpdate();
        });

    }

    public function down(): void {
        Schema::dropIfExists('carreras');
    }
};