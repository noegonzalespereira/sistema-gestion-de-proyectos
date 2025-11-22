<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title','Sistema de Proyectos Académicos')</title>
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-50">

  <!-- 🌐 NAVBAR GLOBAL -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#0a2b6b;">
    <div class="container">
      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('repositorio.index') }}">
        Sistema de Proyectos Académicos
      </a>

      <!-- Botón responsive -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
              aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav ms-auto align-items-center">

          @auth
              {{-- 🔹 ADMINISTRADOR --}}
              @if(auth()->user()->rol === 'Administrador')
                  <li class="nav-item"><a class="nav-link" href="{{ route('repositorio.index') }}">Repositorio</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('proyectos.create') }}">Registrar</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
              @endif

              {{-- 🔹 DOCENTE --}}
              @if(auth()->user()->rol === 'Docente')
                  <li class="nav-item"><a class="nav-link" href="{{ route('docente.asignaciones') }}">Asignaciones</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('repositorio.index') }}">Repositorio</a></li>
              @endif

              {{-- 🔹 ESTUDIANTE --}}
              @if(auth()->user()->rol === 'Estudiante')
                  <li class="nav-item"><a class="nav-link" href="{{ route('estudiante.proyecto') }}">Mi Proyecto</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('repositorio.index') }}">Repositorio</a></li>
              @endif
          @else
              {{-- 🔹 INVITADO --}}
              <li class="nav-item"><a class="nav-link" href="{{ route('repositorio.index') }}">Repositorio</a></li>
          @endauth

          {{-- PERFIL --}}
          <li class="nav-item dropdown ms-3">
              @auth
                  <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li><span class="dropdown-item-text text-muted small">Rol: {{ auth()->user()->rol }}</span></li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                          <form action="{{ route('logout') }}" method="POST">@csrf
                              <button type="submit" class="dropdown-item text-danger">
                                  <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                              </button>
                          </form>
                      </li>
                  </ul>
              @else
                  <a class="btn btn-outline-light ms-2" href="{{ route('login.general') }}">
                      <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                  </a>
              @endauth
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- 📄 Contenido -->
  <main class="py-4">
    @yield('content')
  </main>
   @stack('scripts')

</body>
</html>
