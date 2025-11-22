<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tutores', function (Blueprint $table) {
            $table->id('id_tutor');
            
            // 👇 FK a la tabla USERS (para nombre, email, etc.)
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->foreign('id_usuario')->references('id')->on('users')->cascadeOnDelete();
            
            // Datos propios del Tutor
            $table->string('item', 50)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tutores');
    }
};