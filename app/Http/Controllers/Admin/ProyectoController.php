<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Carrera;
use App\Models\Programa;
use App\Models\Estudiante;
use App\Models\Tutor;
use App\Models\Tribunal;
// Usamos el modelo User por defecto si es la tabla 'users'
use App\Models\User; 
use Illuminate\Support\Facades\Storage;

class ProyectoController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Asegúrate que el modelo User/Usuario tenga el campo 'rol'
            if (!auth()->check() || auth()->user()->rol !== 'Administrador') {
                abort(403, 'Acceso denegado: solo para administradores.');
            }
            return $next($request);
        });
    }


    public function index()
    {
        $proyectos = Proyecto::with('estudiante', 'carrera', 'programa', 'tutor', 'tribunal')->get();
        return view('admin.proyectos.index', compact('proyectos'));
    }


    public function create()
    {
        $programas = Programa::all();
        $carreras = Carrera::all();
        $estudiantes = Estudiante::all();
        $tutores = Tutor::all();
        $tribunales = Tribunal::all();

        return view('admin.proyectos.create', compact('programas', 'carreras', 'estudiantes', 'tutores', 'tribunales'));
    }


    public function store(Request $request)
{
    // Validación de los datos
    $request->validate([
        'titulo' => 'required|string|max:255',
        'resumen'=> 'required|string|max:255',
        'id_programa' => 'required|exists:programas,id_programa',
        'id_carrera' => 'required|exists:carreras,id_carrera',
        'id_estudiante' => 'required|exists:estudiantes,id_estudiante',
        'id_tutor' => 'required|exists:tutores,id_tutor',
        'id_tribunal' => 'required|exists:tribunales,id_tribunal',
        'anio' => 'required|integer',
        'fecha_defensa' => 'required|date',
        'archivo_pdf' => 'required|mimes:pdf|max:20480', // Máximo 20MB
    ]);

    // Guardar el proyecto
    $proyecto = new Proyecto();
    $proyecto->titulo = $request->titulo;
    $proyecto->resumen =$request->resumen;
    $proyecto->id_programa = $request->id_programa;
    $proyecto->id_carrera = $request->id_carrera;
    $proyecto->id_estudiante = $request->id_estudiante;
    $proyecto->id_tutor = $request->id_tutor;
    $proyecto->id_tribunal = $request->id_tribunal;
    $proyecto->anio = $request->anio;
    $proyecto->fecha_defensa = $request->fecha_defensa;
    $proyecto->fecha_aprobacion = $request->fecha_aprobacion;
    $proyecto->calificacion = $request->calificacion;
    
    $proyecto->id_usuario = auth()->id();
    // Guardar archivo PDF
    $archivo_pdf = $request->file('archivo_pdf');
    $path = $archivo_pdf->storeAs('proyectos', time() . '.' . $archivo_pdf->extension(), 'public');
    $proyecto->link_pdf = $path;

    $proyecto->save();

    return redirect()->route('proyectos.index')->with('success', 'Proyecto registrado exitosamente');
}
}