<?php
namespace App\Exports;

use App\Models\Proyecto;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AvanceExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private ?int $idCarrera = null, private ?int $idPrograma = null) {}

    public function query()
    {
        $q = Proyecto::with(['carrera','programa','tutor.usuario','estudiante.usuario'])
            ->orderBy('created_at','desc');

        if ($this->idCarrera)  $q->where('id_carrera',  $this->idCarrera);
        if ($this->idPrograma) $q->where('id_programa', $this->idPrograma);

        return $q;
    }

    public function headings(): array
    {
        return ['Proyecto','Estudiante','Tutor','Carrera','Programa','% Avance','Estado','Fecha creación'];
    }

    public function map($p): array
    {
        $avance = $p->calificacion ? 100 : 40; // regla simple si no tienes campo
        $estado = $p->calificacion ? 'Aprobado' : 'En revisión';

        return [
            $p->titulo,
            $p->estudiante->usuario->name ?? '—',
            $p->tutor->usuario->name ?? '—',
            $p->carrera->nombre ?? '—',
            $p->programa->nombre ?? '—',
            $avance.'%',
            $estado,
            optional($p->created_at)->format('Y-m-d'),
        ];
    }
}
