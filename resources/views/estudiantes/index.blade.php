@extends('layouts.app')
@section('title', 'Estudiantes')
@section('content')
<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4"><div><h1>Estudiantes</h1><p class="subtitle mb-0">Administra los registros y la información académica.</p></div><a href="{{ route('estudiantes.create') }}" class="btn btn-uns px-4 py-2">+ Nuevo estudiante</a></div>
<div class="d-flex gap-2 flex-wrap mb-4"><div class="stat-pill"><strong>{{ $estudiantes->total() }}</strong> estudiantes registrados</div><div class="stat-pill">5 registros por página</div></div>
<section class="card"><div class="card-header d-flex justify-content-between align-items-center"><strong>Listado general</strong><span class="small text-secondary">Página {{ $estudiantes->currentPage() }} de {{ $estudiantes->lastPage() }}</span></div>
<div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Estudiante</th><th>Correo electrónico</th><th>Ciclo</th><th>Promedio</th><th class="text-end">Acciones</th></tr></thead><tbody>
@forelse($estudiantes as $estudiante)
<tr><td><div class="d-flex align-items-center"><span class="student-avatar" aria-hidden="true">{{ mb_substr($estudiante->nombres,0,1) }}{{ mb_substr($estudiante->apellidos,0,1) }}</span><div><strong>{{ $estudiante->apellidos }}, {{ $estudiante->nombres }}</strong><div class="student-code">{{ $estudiante->codigo }}</div></div></div></td><td>{{ $estudiante->email }}</td><td><span class="badge-cycle">{{ $estudiante->ciclo }}</span></td><td class="score">{{ $estudiante->promedio }}</td><td class="text-end action-cell"><a class="btn btn-outline-secondary btn-sm" href="{{ route('estudiantes.show',$estudiante) }}">Ver</a> <a class="btn btn-outline-secondary btn-sm" href="{{ route('estudiantes.edit',$estudiante) }}">Editar</a> <form class="d-inline" method="POST" action="{{ route('estudiantes.destroy',$estudiante) }}" onsubmit="return confirm('¿Está seguro de eliminar a este estudiante? Esta acción no se puede deshacer.');">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Eliminar</button></form></td></tr>
@empty
<tr><td colspan="5" class="text-center py-5"><h2 class="h5">Aún no hay estudiantes registrados</h2><p class="text-secondary mb-0">Selecciona «Nuevo estudiante» para comenzar.</p></td></tr>
@endforelse
</tbody></table></div>
<div class="card-footer bg-white d-flex flex-wrap gap-3 justify-content-between align-items-center p-3"><small class="text-secondary">Mostrando {{ $estudiantes->firstItem() ?? 0 }}–{{ $estudiantes->lastItem() ?? 0 }} de {{ $estudiantes->total() }} registros</small><div>{{ $estudiantes->links('pagination::bootstrap-5') }}</div></div></section>
@endsection
