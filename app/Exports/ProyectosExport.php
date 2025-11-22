<?php
namespace App\Exports;

use App\Models\Proyecto;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProyectosExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private ?int $idCarrera = null,
        private ?int $idPrograma = null,
        private ?string $estado = null, // 'aprobado' | 'revision' | null
        private ?string $desde = null,
        private ?string $hasta = null
    ) {}

    public function query()
    {
        $q = Proyecto::with(['carrera','programa','tutor.usuario','estudiante.usuario'])
            ->orderBy('fecha_defensa','desc');

        if ($this->idCarrera)  $q->where('id_carrera',  $this->idCarrera);
        if ($this->idPrograma) $q->where('id_programa', $this->idPrograma);

        if ($this->estado === 'aprobado') $q->whereNotNull('calificacion');
        if ($this->estado === 'revision') $q->whereNull('calificacion');

        if ($this->desde) $q->whereDate('created_at', '>=', $this->desde);
        if ($this->hasta) $q->whereDate('created_at', '<=', $this->hasta);

        return $q;
    }

    public function headings(): array
    {
        return ['Título','Estudiante','Tutor','Carrera','Programa','Estado','Fecha defensa','Año'];
    }

    public function map($p): array
    {
        $estado = $p->calificacion ? 'Aprobado' : 'En revisión';
        return [
            $p->titulo,
            $p->estudiante->usuario->name ?? '—',
            $p->tutor->usuario->name ?? '—',
            $p->carrera->nombre ?? '—',
            $p->programa->nombre ?? '—',
            $estado,
            $p->fecha_defensa,
            $p->anio,
        ];
    }
}
