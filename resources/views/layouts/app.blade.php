<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Estudiantes') · UNS</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<header class="site-header"><div class="container d-flex align-items-center justify-content-between gap-3 py-3">
    <a href="{{ route('estudiantes.index') }}" class="brand"><img src="{{ asset('img/logo-uns.png') }}" alt="Universidad Nacional del Santa"><span>EPISI <small>Gestión académica</small></span></a>
    <span class="course-label">Aplicaciones Distribuidas I <span class="badge text-bg-light ms-2">Práctica 10</span></span>
</div></header>
<main class="container py-4 py-md-5">
    <div class="eyebrow mb-2">UNIVERSIDAD NACIONAL DEL SANTA</div>
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>@endif
    @yield('content')
</main>
<footer class="container pb-4 text-secondary small">Ingeniería de Sistemas e Informática <span class="mx-2">·</span> Laravel 12 + Eloquent + MySQL</footer>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body></html>
