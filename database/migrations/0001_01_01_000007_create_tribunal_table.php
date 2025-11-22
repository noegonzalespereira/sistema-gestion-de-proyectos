<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tribunales', function (Blueprint $table) {
    $table->id('id_tribunal');
    $table->string('nombre');
    $table->string('email')->nullable();
    $table->timestamps();
});

    }

    public function down(): void {
        Schema::dropIfExists('tribunal');
    }
};
