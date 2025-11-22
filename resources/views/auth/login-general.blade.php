@extends('layouts.app')
@section('title','Login General')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow border-0">
        {{-- Header --}}
        <div class="card-header text-center bg-primary text-white">
          <h5 class="mb-0">Sistema de Proyectos Académicos</h5>
          <small>Accede a tu cuenta para gestionar proyectos</small>
        </div>

        <div class="card-body">
          {{-- Errores --}}
          @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
          @endif

          {{-- Botones de rol --}}
          <div class="d-flex justify-content-center mb-3 gap-2">
            <button class="btn btn-warning text-dark fw-semibold">
              <i class="bi bi-mortarboard-fill"></i> Estudiante
            </button>
            <button class="btn btn-info text-white fw-semibold">
              <i class="bi bi-person-badge-fill"></i> Docente
            </button>
          </div>

          {{-- Formulario de login --}}
          <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Correo Institucional</label>
              <input 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                class="form-control" 
                placeholder="usuario@institucion.edu" 
                required>
            </div>

            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input 
                type="password" 
                name="password" 
                class="form-control" 
                placeholder="********" 
                required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Recordarme</label>
              </div>
              <a href="#" class="small text-decoration-none">¿Olvidaste tu contraseña?</a>
            </div>

            <button class="btn btn-success w-100" type="submit">
              <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
            </button>
          </form>

          {{-- Nota --}}
          <div class="alert alert-warning mt-4 small border-start border-4 border-warning">
            <i class="bi bi-info-circle-fill text-warning me-2"></i>
            <strong>Nota:</strong> Si no tienes una cuenta, debes solicitarla al administrador del sistema.<br>
            <a href="#" class="text-success fw-semibold text-decoration-none">
              Contactar al administrador →
            </a>
          </div>

          {{-- Acceso administrativo --}}
          <div class="text-center mt-3">
            <a href="{{ route('login.admin') }}" class="text-decoration-none small">
              <i class="bi bi-key-fill"></i> Acceso administrativo
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
