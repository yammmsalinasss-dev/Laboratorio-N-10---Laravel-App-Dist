<?php
namespace App\Http\Controllers;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class EstudianteController extends Controller
{
    public function index() {
        $estudiantes = Estudiante::latest()->orderByDesc('id')->paginate(5);
        return view('estudiantes.index', compact('estudiantes'));
    }
    public function create() { return view('estudiantes.create'); }
    public function store(Request $request) {
        Estudiante::create($this->validar($request));
        return redirect()->route('estudiantes.index')->with('success', 'Estudiante registrado exitosamente.');
    }
    public function show(Estudiante $estudiante) { return view('estudiantes.show', compact('estudiante')); }
    public function edit(Estudiante $estudiante) { return view('estudiantes.edit', compact('estudiante')); }
    public function update(Request $request, Estudiante $estudiante) {
        $estudiante->update($this->validar($request, $estudiante));
        return redirect()->route('estudiantes.index')->with('success', 'Datos del estudiante actualizados correctamente.');
    }
    public function destroy(Estudiante $estudiante) {
        $estudiante->delete();
        return redirect()->route('estudiantes.index')->with('success', 'Estudiante eliminado correctamente.');
    }
    private function validar(Request $request, ?Estudiante $estudiante = null): array {
        // ignore recibe un modelo resuelto por Laravel, nunca un ID del formulario.
        return $request->validate([
            'codigo' => ['bail', 'required', 'regex:/^[0-9]{10}$/', Rule::unique('estudiantes', 'codigo')->ignore($estudiante)],
            'nombres' => ['required', 'string', 'max:60'],
            'apellidos' => ['required', 'string', 'max:60'],
            'email' => ['bail', 'required', 'email', 'max:80', Rule::unique('estudiantes', 'email')->ignore($estudiante)],
            'ciclo' => ['required', Rule::in(['I','II','III','IV','V','VI','VII','VIII','IX','X'])],
            'promedio' => ['bail', 'required', 'numeric', 'between:0,20', 'decimal:0,2'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'codigo.regex' => 'El código debe contener exactamente 10 dígitos.',
            'codigo.unique' => 'Este código ya está registrado.',
            'email.unique' => 'Este correo ya está registrado.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'max' => 'El campo :attribute admite como máximo :max caracteres.',
            'ciclo.in' => 'Selecciona un ciclo válido entre I y X.',
            'promedio.numeric' => 'El promedio debe ser un número.',
            'promedio.between' => 'El promedio debe estar entre 0 y 20.',
            'promedio.decimal' => 'El promedio admite como máximo dos decimales.',
        ], ['codigo'=>'código', 'email'=>'correo', 'promedio'=>'promedio']);
    }
}
