<?php
namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use App\Models\ModuloMaterial;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    public function store(Request $request, $asignacionId) {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
        ]);
        $data['id_asignacion'] = $asignacionId;
        Modulo::create($data);
        return back()->with('success','Módulo creado.');
    }

    public function destroy(Modulo $modulo) {
        $modulo->delete();
        return back()->with('success','Módulo eliminado.');
    }

    public function storeMaterial(Request $request, Modulo $modulo) {
        $data = $request->validate([
            'tipo'   => 'required|in:pdf,enlace,video',
            'titulo' => 'nullable|string|max:200',
            'url'    => 'nullable|url',
            'archivo'=> 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:5120',
        ]);
        $mat = new ModuloMaterial([
            'tipo'   => $data['tipo'],
            'titulo' => $data['titulo'] ?? null,
            'url'    => $data['url'] ?? null,
        ]);
        if ($request->hasFile('archivo')) {
            $mat->path = $request->file('archivo')->store('modulos','public');
        }
        $modulo->materiales()->save($mat);
        return back()->with('success','Material agregado.');
    }

    public function evaluar(Request $request, Modulo $modulo) {
        $data = $request->validate([
            'estado' => 'required|in:pendiente,observado,aprobado',
            'calificacion' => 'nullable|numeric|min:0|max:100',
            'fecha_limite' => 'nullable|date',
        ]);
        $modulo->update($data);
        return back()->with('success','Módulo evaluado.');
    }
}
