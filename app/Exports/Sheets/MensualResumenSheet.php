<?php
namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Carbon;

class MensualResumenSheet implements FromArray, WithTitle
{
    public function __construct(private array $metrics, private Carbon $inicio) {}

    public function array(): array
    {
        return [
            ['Resumen del mes', $this->inicio->format('F Y')],
            ['Total proyectos', $this->metrics['total_proyectos']],
            ['Aprobados',       $this->metrics['proyectos_aprobados']],
            ['En revisión',     $this->metrics['proyectos_revision']],
            ['Nuevos usuarios', $this->metrics['nuevos_usuarios']],
        ];
    }

    public function title(): string
    {
        return 'Resumen';
    }
}
