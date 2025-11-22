<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('correcciones', function (Blueprint $table) {
            // SI YA TIENES id_tutor, comenta todo este bloque
            if (!Schema::hasColumn('correcciones', 'id_tutor')) {
                $table->unsignedBigInteger('id_tutor')
                      ->nullable()
                      ->after('id_modulo');

                $table->foreign('id_tutor')
                      ->references('id_tutor')->on('tutores')
                      ->onDelete('cascade');
            }

            if (!Schema::hasColumn('correcciones', 'comentario')) {
                $table->text('comentario')->nullable()->after('id_tutor');
            }

            if (!Schema::hasColumn('correcciones', 'fecha_limite')) {
                $table->date('fecha_limite')->nullable()->after('comentario');
            }
        });
    }

    public function down(): void
    {
        Schema::table('correcciones', function (Blueprint $table) {
            if (Schema::hasColumn('correcciones', 'fecha_limite')) {
                $table->dropColumn('fecha_limite');
            }
            if (Schema::hasColumn('correcciones', 'comentario')) {
                $table->dropColumn('comentario');
            }
            if (Schema::hasColumn('correcciones', 'id_tutor')) {
                $table->dropForeign(['id_tutor']);
                $table->dropColumn('id_tutor');
            }
        });
    }
};
