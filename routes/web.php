<?php

use App\Http\Controllers\AccesoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDatosController;
use App\Http\Controllers\AdminDocumentoController;
use App\Http\Controllers\AdminReporteController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\EventoAsistenciaController;
use App\Http\Controllers\EventoInscripcionController;
use App\Http\Controllers\FuncionarioController;
use Illuminate\Support\Facades\Route;

// Pantalla principal de acceso por cédula
Route::get('/', [AccesoController::class, 'index'])->name('acceso');
Route::post('/ingresar', [AccesoController::class, 'ingresar'])->name('ingresar');
Route::post('/salir', [AccesoController::class, 'salir'])->name('salir');

// Rutas protegidas para el funcionario
Route::middleware(['auth.funcionario'])->group(function () {
    Route::get('/mis-datos', [FuncionarioController::class, 'formulario'])->name('funcionario.formulario');
    Route::post('/mis-datos', [FuncionarioController::class, 'guardar'])->name('funcionario.guardar');

    Route::get('/eventos', [EventoInscripcionController::class, 'index'])->name('eventos.index');
    Route::post('/eventos/inscribir/{evento}', [EventoInscripcionController::class, 'inscribir'])->name('eventos.inscribir');
    Route::get('/calendario', [EventoInscripcionController::class, 'calendario'])->name('eventos.calendario');

    Route::get('/encuestas/{encuesta}', [EncuestaController::class, 'mostrar'])->name('encuestas.mostrar');
    Route::post('/encuestas/{encuesta}', [EncuestaController::class, 'responder'])->name('encuestas.responder');

});

Route::middleware('auth.admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/calendario', [AdminController::class, 'calendario'])->name('calendario');
    Route::get('/periodos/crear', [AdminController::class, 'crearPeriodo'])->name('periodos.crear');
    Route::post('/periodos', [AdminController::class, 'guardarPeriodo'])->name('periodos.guardar');
    Route::get('/eventos/crear', [AdminController::class, 'crearEvento'])->name('eventos.crear');
    Route::post('/eventos', [AdminController::class, 'guardarEvento'])->name('eventos.guardar');
    Route::get('/eventos/{evento}/inscritos', [AdminController::class, 'inscritos'])->name('inscritos');
    Route::get('/eventos/{evento}/inscritos/exportar', [AdminController::class, 'exportarInscritos'])->name('inscritos.exportar');
    Route::get('/asistencia/{token}', [EventoAsistenciaController::class, 'confirmar'])->name('asistencia.confirmar');
    Route::post('/asistencia/{token}', [EventoAsistenciaController::class, 'registrar'])->name('asistencia.registrar');
    Route::get('/documentos/{documento}/descargar', [AdminDocumentoController::class, 'descargar'])->name('documentos.descargar');
    Route::get('/reportes', [AdminReporteController::class, 'index'])->name('reportes');
    Route::get('/reportes/exportar', [AdminReporteController::class, 'exportar'])->name('reportes.exportar');
    Route::get('/datos/configuracion', [AdminDatosController::class, 'index'])->name('datos.configuracion');
    Route::post('/datos/periodo', [AdminDatosController::class, 'guardarPeriodo'])->name('datos.periodo.guardar');
    Route::post('/datos/autorizar', [AdminDatosController::class, 'autorizarEdicion'])->name('datos.autorizar');
    Route::get('/eventos/{evento}/encuestas/crear', [AdminController::class, 'crearEncuesta'])->name('encuestas.crear');
    Route::post('/eventos/{evento}/encuestas', [AdminController::class, 'guardarEncuesta'])->name('encuestas.guardar');
    Route::get('/funcionarios/importar', [AdminController::class, 'formularioImportar'])->name('funcionarios.importar');
    Route::post('/funcionarios/importar', [AdminController::class, 'importarFuncionarios'])->name('funcionarios.importar.guardar');
});
