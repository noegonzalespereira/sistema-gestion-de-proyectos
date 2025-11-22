<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Institucion;

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uc = Institucion::where('sigla', 'UC')->first();
        $ets = Institucion::where('sigla', 'ETS')->first();

        DB::table('carreras')->insert([
            [
                'id_institucion' => $uc->id_institucion,
                'nombre' => 'Ingeniería de Sistemas',
                'sigla' => 'IS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_institucion' => $uc->id_institucion,
                'nombre' => 'Administración de Empresas',
                'sigla' => 'AE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_institucion' => $ets->id_institucion,
                'nombre' => 'Desarrollo de Software',
                'sigla' => 'DS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}