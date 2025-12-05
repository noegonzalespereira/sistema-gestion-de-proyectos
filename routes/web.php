<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ProyectoController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\RepositorioController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\AsignacionProyectoController;
use App\Http\Controllers\TribunalController;
use App\Http\Controllers\Admin\ReportesController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Admin\EstudianteAdminController;
use App\Http\Controllers\Docente\ModuloController;
use App\Http\Controllers\Docente\AvanceController;
use App\Http\Controllers\Docente\CorreccionController;
use App\Http\Controllers\Docente\CalificacionController;
use App\Http\Controllers\Docente\AsignacionController;
use App\Http\Controllers\Docente\FaltasController;

/*
|--------------------------------------------------------------------------
| Página de inicio (landing)
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'));
//  Rutas del Repositorio (públicas)
Route::get('/repositorio', [RepositorioController::class, 'index'])->name('repositorio.index');
Route::get('/repositorio/buscar', [RepositorioController::class, 'buscar'])->name('repositorio.buscar');
/*
|--------------------------------------------------------------------------
| Autenticación general
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('login.admin');
Route::get('/login', [AuthController::class, 'showGeneralLogin'])->name('login.general');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Panel de ADMINISTRADOR
|--------------------------------------------------------------------------
*/
/*Panel Administrativo */
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// // Crear nuevo usuario desde el modal del admin
// Route::post('/usuarios/crear', [UserController::class, 'registro'])->name('usuarios.registro');
// Gestión de usuarios (admin)
Route::prefix('admin')->middleware(['auth',RoleMiddleware::class . ':Administrador'])->group(function () {
    Route::get('/usuarios',              [UserController::class,'index'])->name('usuarios.index');
    Route::post('/usuarios',             [UserController::class,'registro'])->name('usuarios.registro');
    Route::get('/usuarios/{user}/editar',[UserController::class,'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{user}',       [UserController::class,'update'])->name('usuarios.update');
    Route::patch('/usuarios/{user}/toggle',[UserController::class,'toggle'])->name('usuarios.toggle');
    Route::delete('/usuarios/{user}',    [UserController::class,'destroy'])->name('usuarios.destroy');
});
//GESTION DE INSTITUCION
Route::prefix('admin')->middleware(['auth',RoleMiddleware::class . ':Administrador'])->group(function () {
    Route::get('/institucion',                [InstitucionController::class, 'index'])->name('institucion.index');
    Route::post('/institucion',               [InstitucionController::class, 'store'])->name('institucion.store');
    Route::get('/institucion/{institucion}/edit', [InstitucionController::class, 'edit'])->name('institucion.edit');
    Route::put('/institucion/{institucion}',  [InstitucionController::class, 'update'])->name('institucion.update');
    Route::delete('/institucion/{institucion}', [InstitucionController::class, 'destroy'])->name('institucion.destroy');
});

// GESTIÓN DE CARRERAS
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':Administrador'])->group(function () {

    // Listado
    Route::get('/carreras', [CarreraController::class, 'index'])
         ->name('carreras.index');

    // Crear
    Route::post('/carreras', [CarreraController::class, 'store'])
         ->name('carreras.store');

    // Editar
    Route::get('/carreras/{carrera}/edit', [CarreraController::class, 'edit'])
         ->name('carreras.edit');

    // Actualizar
    Route::put('/carreras/{carrera}', [CarreraController::class, 'update'])
         ->name('carreras.update');

    // Eliminar
    Route::delete('/carreras/{carrera}', [CarreraController::class, 'destroy'])
         ->name('carreras.destroy');
});
// GESTIÓN DE PROGRAMAS
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':Administrador'])->group(function () {
    Route::get('/programas',                  [ProgramaController::class, 'index'])->name('programas.index');
    Route::post('/programas',                 [ProgramaController::class, 'store'])->name('programas.store');
    Route::get('/programas/{programa}/edit',  [ProgramaController::class, 'edit'])->name('programas.edit');
    Route::put('/programas/{programa}',       [ProgramaController::class, 'update'])->name('programas.update');
    Route::delete('/programas/{programa}',    [ProgramaController::class, 'destroy'])->name('programas.destroy');
});

//gestion tribunales
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':Administrador'])->group(function () {
    Route::get   ('/tribunales',                 [TribunalController::class, 'index'])->name('tribunales.index');
    Route::post  ('/tribunales',                 [TribunalController::class, 'store'])->name('tribunales.store');
    Route::get   ('/tribunales/{tribunal}/edit', [TribunalController::class, 'edit'])->name('tribunales.edit');
    Route::put   ('/tribunales/{tribunal}',      [TribunalController::class, 'update'])->name('tribunales.update');
    Route::delete('/tribunales/{tribunal}',      [TribunalController::class, 'destroy'])->name('tribunales.destroy');
});
//GESION ASIGNACIONES
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':Administrador'])->group(function () {
    Route::get   ('/asignaciones',                    [AsignacionProyectoController::class, 'index'])->name('asignaciones.index');
    Route::post  ('/asignaciones',                    [AsignacionProyectoController::class, 'store'])->name('asignaciones.store');
    Route::get   ('/asignaciones/{asignacion}/edit',  [AsignacionProyectoController::class, 'edit'])->name('asignaciones.edit');
    Route::put   ('/asignaciones/{asignacion}',       [AsignacionProyectoController::class, 'update'])->name('asignaciones.update');
    Route::delete('/asignaciones/{asignacion}',       [AsignacionProyectoController::class, 'destroy'])->name('asignaciones.destroy');
});
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':Administrador'])->group(function () {
    Route::get('/reportes',                           [ReportesController::class, 'index'])->name('reportes.index');

    // Proyectos
    Route::get('/reportes/proyectos',                 [ReportesController::class, 'proyectos'])->name('reportes.proyectos');
    Route::get('/reportes/proyectos/export',          [ReportesController::class, 'exportProyectos'])->name('reportes.proyectos.export');

    // Avance
    Route::get('/reportes/avance',                    [ReportesController::class, 'avance'])->name('reportes.avance');
    Route::get('/reportes/avance/export',             [ReportesController::class, 'exportAvance'])->name('reportes.avance.export');

    // Plazos
    Route::get('/reportes/plazos',                    [ReportesController::class, 'plazos'])->name('reportes.plazos');
    Route::get('/reportes/plazos/export',             [ReportesController::class, 'exportPlazos'])->name('reportes.plazos.export');

    // Mensual
    Route::get('/reportes/mensual',                   [ReportesController::class, 'mensual'])->name('reportes.mensual');
    Route::get('/reportes/mensual/export',            [ReportesController::class, 'exportMensual'])->name('reportes.mensual.export');
});
// GESTIÓN DE TUTORES
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':Administrador'])->group(function () {
    Route::get('/tutores',                   [TutorController::class, 'index'])->name('tutores.index');
    Route::post('/tutores',                  [TutorController::class, 'store'])->name('tutores.store');
    Route::get('/tutores/{tutor}/edit',      [TutorController::class, 'edit'])->name('tutores.edit');
    Route::put('/tutores/{tutor}',           [TutorController::class, 'update'])->name('tutores.update');
    Route::delete('/tutores/{tutor}',        [TutorController::class, 'destroy'])->name('tutores.destroy');
});
// GESTIÓN DE ESTUDIANTES
Route::prefix('admin')->middleware(['auth', RoleMiddleware::class . ':Administrador'])->group(function () {
    Route::get('/estudiantes',                   [EstudianteAdminController::class, 'index'])->name('estudiantes.index');
    Route::post('/estudiantes',                  [EstudianteAdminController::class, 'store'])->name('estudiantes.store');
    Route::get('/estudiantes/{estudiante}/edit', [EstudianteAdminController::class, 'edit'])->name('estudiantes.edit');
    Route::put('/estudiantes/{estudiante}',      [EstudianteAdminController::class, 'update'])->name('estudiantes.update');
    Route::delete('/estudiantes/{estudiante}',   [EstudianteAdminController::class, 'destroy'])->name('estudiantes.destroy');
});
/*
|--------------------------------------------------------------------------
| Módulo de PROYECTOS (solo administrador)
|--------------------------------------------------------------------------
*/
Route::prefix('admin/proyectos')->middleware('auth',RoleMiddleware::class . ':Administrador')->group(function () {

        Route::get('/', [ProyectoController::class, 'index'])->name('proyectos.index');
        Route::get('/crear', [ProyectoController::class, 'create'])->name('proyectos.create');
        Route::post('/guardar', [ProyectoController::class, 'store'])->name('proyectos.store');
        
});

/*
|--------------------------------------------------------------------------
| Panel DOCENTE
|--------------------------------------------------------------------------
*/
Route::prefix('docente')->middleware(['auth', RoleMiddleware::class . ':Docente'])->group(function () {

    /* 📌 ASIGNACIONES DEL DOCENTE */
    Route::get('/asignaciones', [AsignacionController::class, 'index'])
        ->name('docente.asignaciones');

    Route::get('/asignaciones/{asignacion}', [AsignacionController::class, 'show'])
        ->name('docente.asignaciones.show');

    /* 📌 FALTAS */
    Route::get('/faltas', [FaltasController::class, 'index'])
        ->name('docente.faltas.index');

    Route::get('/asignaciones/{id}/faltas', [FaltasController::class, 'porAsignacion'])
        ->name('docente.faltas.asignacion');

    Route::post('/faltas/{falta}/rehabilitar', [FaltasController::class, 'rehabilitar'])
        ->name('docente.faltas.rehabilitar');


    /* 📌 MÓDULOS INDIVIDUALES */
    Route::post('/modulos', [ModuloController::class, 'store'])
        ->name('docente.modulos.store');

    Route::put('/modulos/{modulo}', [ModuloController::class, 'update'])
        ->name('docente.modulos.update');

    Route::delete('/modulos/{modulo}', [ModuloController::class, 'destroy'])
        ->name('docente.modulos.destroy');


    /* 📌 MATERIALES INDIVIDUALES */
    Route::post('/modulos/{modulo}/materiales', [ModuloController::class, 'storeMaterial'])
        ->name('docente.modulos.materiales.store');

    Route::put('/materiales/{material}', [ModuloController::class, 'updateMaterial'])
        ->name('docente.modulos.materiales.update');

    Route::delete('/materiales/{material}', [ModuloController::class, 'destroyMaterial'])
        ->name('docente.modulos.materiales.destroy');
    Route::post('/modulos/{modulo}/evaluar', 
    [\App\Http\Controllers\Docente\ModuloController::class,'evaluar']
)->name('docente.modulos.evaluar');


    /*
    |--------------------------------------------------------------------------
    | 🌍 MÓDULOS BASE — ACCIONES GLOBALES
    |--------------------------------------------------------------------------
    */

    // EDITAR módulo base global
    Route::put('/modulos/base/{id}', [ModuloController::class, 'updateBase'])
    ->name('docente.modulos.update.base');


    // ELIMINAR módulo base global
    Route::delete('/modulos/base/{id}', [ModuloController::class, 'destroyBase'])
        ->name('docente.modulos.base.destroy');


    /*
    |--------------------------------------------------------------------------
    | 🌍 MATERIALES GLOBALES
    |--------------------------------------------------------------------------
    */

    // AGREGAR material globalmente
    Route::post('/modulos/{id}/material/global', [ModuloController::class, 'storeMaterialGlobal'])
        ->name('docente.modulos.materiales.store.global');

    // EDITAR material globalmente
    Route::put('/modulos/material/{id}/global', [ModuloController::class, 'updateMaterialGlobal'])
        ->name('docente.modulos.materiales.update.global');

    // ELIMINAR material globalmente
    Route::delete('/modulos/material/{id}/global', [ModuloController::class, 'destroyMaterialGlobal'])
        ->name('docente.modulos.materiales.destroy.global');


    /*
    |--------------------------------------------------------------------------
    | 📌 AVANCES
    |--------------------------------------------------------------------------
    */
    Route::get('/asignaciones/{asignacion}/avances', [AvanceController::class, 'index'])
        ->name('docente.avances.index');

    Route::post('/asignaciones/{asignacion}/avances', [AvanceController::class, 'store'])
        ->name('docente.avances.store');

    Route::post('/avances/{avance}/correcciones', [CorreccionController::class, 'storeForAvance'])
        ->name('docente.avances.correcciones.store');
});

/*
|--------------------------------------------------------------------------
| Panel ESTUDIANTE
|--------------------------------------------------------------------------
*/
Route::prefix('estudiante')->middleware(['auth',RoleMiddleware::class . ':Estudiante'])->group(function () {

    Route::get('/proyecto', [EstudianteController::class, 'proyecto'])->name('estudiante.proyecto');
    
   // subir avance a un MÓDULO específico
    Route::post('/modulos/{modulo}/avances', [EstudianteController::class, 'subirAvance'])
        ->name('estudiante.modulos.avances.store');

    // actualizar un avance (por si quieres permitir edición)
    Route::put('/avances/{avance}', [EstudianteController::class, 'actualizarAvance'])
        ->name('estudiante.avances.update');

    // eliminar un avance
    Route::delete('/avances/{avance}', [EstudianteController::class, 'eliminarAvance'])
        ->name('estudiante.avances.destroy');
});