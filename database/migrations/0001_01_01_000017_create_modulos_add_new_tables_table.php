<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulo_materiales', function (Blueprint $table) {
            $table->id('id_material');
            $table->unsignedBigInteger('id_modulo');

            $table->enum('tipo', ['pdf', 'enlace', 'video']);
            $table->string('titulo')->nullable();
            $table->string('url')->nullable();
            $table->string('path')->nullable();

            $table->timestamps();

            $table->foreign('id_modulo')
                  ->references('id_modulo')
                  ->on('modulos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulo_materiales');
    }
};
