<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avances', function (Blueprint $table) {
            $table->unsignedBigInteger('id_modulo')->nullable()->after('id_asignacion');

            // si quieres la FK (opcional pero recomendado)
            $table->foreign('id_modulo')
                  ->references('id_modulo')
                  ->on('modulos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('avances', function (Blueprint $table) {
            $table->dropForeign(['id_modulo']);
            $table->dropColumn('id_modulo');
        });
    }
};
