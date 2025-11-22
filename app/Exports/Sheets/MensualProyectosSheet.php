<?php
namespace App\Exports\Sheets;

use App\Models\Proyecto;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class MensualProyectosSheet implements FromArray, WithTitle
{
    public function __construct(private Carbon $inicio, private Carbon $fin) {}

    public function array(): array
    {
        $rows = [
            ['Título','Estudiante','Tutor','Carrera','Programa','Estado','Fecha creación']
        ];

        Proyecto::with(['estudiante.usuario','tutor.usuario','carrera','programa'])
            ->whereBetween('created_at',[$this->inicio,$this->fin])
            ->orderBy('created_at','desc')
            ->get()
            ->each(function($p) use (&$rows) {
                $rows[] = [
                    $p->titulo,
                    $p->estudiante->usuario->name ?? '—',
                    $p->tutor->usuario->name ?? '—',
                    $p->carrera->nombre ?? '—',
                    $p->programa->nombre ?? '—',
                    $p->calificacion ? 'Aprobado' : 'En revisión',
                    optional($p->created_at)->format('Y-m-d'),
                ];
            });

        return $rows;
    }

    public function title(): string
    {
        return 'Proyectos';
    }
}
