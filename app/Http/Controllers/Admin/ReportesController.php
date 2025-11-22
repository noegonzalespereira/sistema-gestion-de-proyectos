<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Proyecto, Carrera, Programa};
use App\Exports\{ProyectosExport, AvanceExport, PlazosExport, MensualExport};
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;

class ReportesController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    // ---------- PROYECTOS ----------
    public function proyectos(Request $r)
    {
        $carreras  = Carrera::orderBy('nombre')->get();
        $programas = Programa::orderBy('nombre')->get();

        $proyectos = Proyecto::with(['carrera','programa','tutor.usuario','estudiante.usuario'])
            ->when($r->id_carrera,  fn($q)=>$q->where('id_carrera',$r->id_carrera))
            ->when($r->id_programa, fn($q)=>$q->where('id_programa',$r->id_programa))
            ->when($r->estado === 'aprobado', fn($q)=>$q->whereNotNull('calificacion'))
            ->when($r->estado === 'revision', fn($q)=>$q->whereNull('calificacion'))
            ->when($r->desde, fn($q)=>$q->whereDate('created_at','>=',$r->desde))
            ->when($r->hasta, fn($q)=>$q->whereDate('created_at','<=',$r->hasta))
            ->orderBy('fecha_defensa','desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reportes.proyectos', compact('carreras','programas','proyectos'));
    }

    public function exportProyectos(Request $r)
    {
        $file = 'reporte_proyectos_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(
            new ProyectosExport($r->id_carrera, $r->id_programa, $r->estado, $r->desde, $r->hasta),
            $file
        );
    }

    // ---------- AVANCE ----------
    public function avance(Request $r)
    {
        $carreras  = Carrera::orderBy('nombre')->get();
        $programas = Programa::orderBy('nombre')->get();

        $proyectos = Proyecto::with(['carrera','programa','tutor.usuario','estudiante.usuario'])
            ->when($r->id_carrera,  fn($q)=>$q->where('id_carrera',$r->id_carrera))
            ->when($r->id_programa, fn($q)=>$q->where('id_programa',$r->id_programa))
            ->orderBy('created_at','desc')
            ->paginate(15)->withQueryString();

        return view('admin.reportes.avance', compact('carreras','programas','proyectos'));
    }

    public function exportAvance(Request $r)
    {
        $file = 'reporte_avance_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(new AvanceExport($r->id_carrera, $r->id_programa), $file);
    }

    // ---------- PLAZOS ----------
    public function plazos(Request $r)
    {
        $dias = (int)($r->dias ?? 30);
        $limite = Carbon::today()->addDays($dias);

        $proyectos = Proyecto::with(['estudiante.usuario','tutor.usuario','carrera','programa'])
            ->whereDate('fecha_defensa','<=',$limite)
            ->orderBy('fecha_defensa')
            ->paginate(20)->withQueryString();

        return view('admin.reportes.plazos', compact('proyectos','dias'));
    }

    public function exportPlazos(Request $r)
    {
        $dias = (int)($r->dias ?? 30);
        $file = 'reporte_plazos_'.$dias.'d_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(new PlazosExport($dias), $file);
    }

    // ---------- MENSUAL ----------
    public function mensual(Request $r)
    {
        $year  = (int)($r->year  ?? now()->year);
        $month = (int)($r->month ?? now()->month);

        $inicio = Carbon::create($year,$month,1)->startOfMonth();
        $fin    = (clone $inicio)->endOfMonth();

        $metrics = [
            'total'      => Proyecto::whereBetween('created_at',[$inicio,$fin])->count(),
            'aprobados'  => Proyecto::whereBetween('created_at',[$inicio,$fin])->whereNotNull('calificacion')->count(),
            'revision'   => Proyecto::whereBetween('created_at',[$inicio,$fin])->whereNull('calificacion')->count(),
        ];

        $proyectos = Proyecto::with(['carrera','programa','tutor.usuario','estudiante.usuario'])
            ->whereBetween('created_at',[$inicio,$fin])
            ->orderBy('created_at','desc')
            ->paginate(15)->withQueryString();

        return view('admin.reportes.mensual', compact('year','month','metrics','proyectos'));
    }

    public function exportMensual(Request $r)
    {
        $year  = (int)($r->year  ?? now()->year);
        $month = (int)($r->month ?? now()->month);
        $file  = "reporte_mensual_{$year}_{$month}.xlsx";
        return Excel::download(new MensualExport($year,$month), $file);
    }
}
