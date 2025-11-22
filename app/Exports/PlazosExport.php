<?php
namespace App\Exports;

use App\Models\Proyecto;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlazosExport implements FromCollection, WithHeadings
{
    public function __construct(private int $dias = 30) {}

    public function collection()
    {
        $limite = Carbon::today()->addDays($this->dias);

        return Proyecto::with(['estudiante.usuario','tutor.usuario','carrera','programa'])
            ->whereDate('fecha_defensa','<=',$limite)
            ->orderBy('fecha_defensa')
            ->get()
            ->map(function($p){
                return [
                    'Proyecto'   => $p->titulo,
                    'Estudiante' => $p->estudiante->usuario->name ?? '—',
                    'Tutor'      => $p->tutor->usuario->name ?? '—',
                    'Carrera'    => $p->carrera->nombre ?? '—',
                    'Programa'   => $p->programa->nombre ?? '—',
                    'Fecha Límite' => $p->fecha_defensa,
                    'Días restantes' => now()->diffInDays(\Carbon\Carbon::parse($p->fecha_defensa), false),
                ];
            });
    }

    public function headings(): array
    {
        return ['Proyecto','Estudiante','Tutor','Carrera','Programa','Fecha Límite','Días restantes'];
    }
}
