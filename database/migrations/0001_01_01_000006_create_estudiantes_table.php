<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id('id_estudiante');
            
          
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->foreign('id_usuario')->references('id')->on('users')->cascadeOnDelete();
        
            $table->string('ci', 30)->unique();
            
           
            $table->unsignedBigInteger('id_carrera')->nullable();

            $table->foreign('id_carrera')
                  ->references('id_carrera')
                  ->on('carreras')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            $table->timestamps();
        });

    }

    public function down(): void {
        Schema::dropIfExists('estudiantes');
    }
};