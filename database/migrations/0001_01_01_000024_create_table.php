<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('correcciones', function (Blueprint $table) {
            if (!Schema::hasColumn('correcciones', 'id_avance')) {
                $table->unsignedBigInteger('id_avance')
                      ->nullable()
                      ->after('id_modulo');

                $table->foreign('id_avance')
                      ->references('id_avance')->on('avances')
                      ->onDelete('cascade');
            }

            if (!Schema::hasColumn('correcciones', 'nota')) {
                $table->decimal('nota', 5, 2)->nullable()->after('comentario');
            }
        });
    }

    public function down(): void
    {
        Schema::table('correcciones', function (Blueprint $table) {
            if (Schema::hasColumn('correcciones', 'nota')) {
                $table->dropColumn('nota');
            }
            if (Schema::hasColumn('correcciones', 'id_avance')) {
                $table->dropForeign(['id_avance']);
                $table->dropColumn('id_avance');
            }
        });
    }
};
