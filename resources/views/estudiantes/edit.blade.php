@extends('layouts.app')
@section('title', 'Editar estudiante')
@section('content')
<div class="mb-4"><a class="small text-secondary text-decoration-none" href="{{ route('estudiantes.index') }}">← Volver al listado</a><h1 class="mt-3">Editar estudiante</h1><p class="subtitle">Actualiza la información y guarda los cambios.</p></div>
<div class="card mx-auto" style="max-width:850px"><div class="card-header"><strong>Información del estudiante</strong></div><div class="card-body"><form method="POST" action="{{ route('estudiantes.update',$estudiante) }}">@include('estudiantes._form')</form></div></div>
@endsection
