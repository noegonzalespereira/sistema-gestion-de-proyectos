<?php
namespace App\Exports;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MensualExport implements WithMultipleSheets
{
    public function __construct(private int $year, private int $month) {}

    public function sheets(): array
    {
        $inicio = Carbon::create($this->year,$this->month,1)->startOfMonth();
        $fin    = (clone $inicio)->endOfMonth();

        $metrics = [
            'total_proyectos'     => Proyecto::whereBetween('created_at',[$inicio,$fin])->count(),
            'proyectos_aprobados' => Proyecto::whereBetween('created_at',[$inicio,$fin])->whereNotNull('calificacion')->count(),
            'proyectos_revision'  => Proyecto::whereBetween('created_at',[$inicio,$fin])->whereNull('calificacion')->count(),
            'nuevos_usuarios'     => User::whereBetween('created_at',[$inicio,$fin])->count(),
        ];

        return [
            new \App\Exports\Sheets\MensualResumenSheet($metrics, $inicio),
            new \App\Exports\Sheets\MensualProyectosSheet($inicio,$fin),
        ];
    }
}
