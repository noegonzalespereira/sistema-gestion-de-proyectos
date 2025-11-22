<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avances', function (Blueprint $table) {
            // Si ya tienes 'id_asignacion', la nueva va después (puede ser en otro lugar también)
            $table->unsignedBigInteger('id_usuario')->after('id_asignacion');

            $table->foreign('id_usuario')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('avances', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropColumn('id_usuario');
        });
    }
};
