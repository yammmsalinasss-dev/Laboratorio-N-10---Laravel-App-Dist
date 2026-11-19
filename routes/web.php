<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstudianteController;
Route::redirect('/', '/estudiantes');
Route::resource('estudiantes', EstudianteController::class)->parameters(['estudiantes' => 'estudiante']);
