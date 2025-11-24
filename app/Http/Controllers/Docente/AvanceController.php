<?php 
namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Avance;
use App\Models\AsignacionProyecto;
use Illuminate\Http\Request;

class AvanceController extends Controller
{
    public function index(AsignacionProyecto $asignacion) {
        $avances = Avance::with('usuario')
            ->where('id_asignacion', $asignacion->id_asignacion)
            ->orderBy('id_modulo')
            ->orderBy('created_at')
            ->get();
        $avancesPorModulo = $avances->groupBy('id_modulo');

        // crea una vista simple o devuélvelos como quieras
        return view('docente.partials.avances', [
            'asignacion'       => $asignacion,
            'avancesPorModulo' => $avancesPorModulo,
        ]);
    }

    public function store(Request $request, AsignacionProyecto $asignacion) {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);
        $avance = new Avance([
            'id_asignacion' => $asignacion->id_asignacion,
            'id_usuario'    => auth()->id(),
            'titulo'        => $data['titulo'],
            'descripcion'   => $data['descripcion'] ?? null,
        ]);
        if ($request->hasFile('archivo')) {
            $avance->path = $request->file('archivo')->store('avances','public');
        }
        $avance->save();
        return back()->with('success','Avance registrado.');
    }
}
