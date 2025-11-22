<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programas')->insert([
            ['nombre' => 'Licenciatura', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Maestría', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Doctorado', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}