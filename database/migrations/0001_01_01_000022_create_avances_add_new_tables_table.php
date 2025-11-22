<?PHP
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('correcciones', function (Blueprint $table) {
            // si quieres que sea obligatorio, quita "nullable()"
            $table->unsignedBigInteger('id_tutor')
                  ->nullable()
                  ->after('id_modulo');

            $table->foreign('id_tutor')
                  ->references('id_tutor')->on('tutores')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('correcciones', function (Blueprint $table) {
            $table->dropForeign(['id_tutor']);
            $table->dropColumn('id_tutor');
        });
    }
};
