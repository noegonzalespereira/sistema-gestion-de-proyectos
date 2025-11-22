@extends('layouts.app')
@section('title','Recuperar Contraseña')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow border-0">
        <div class="card-header bg-white">
          <h5 class="mb-0">Recuperar Contraseña</h5>
        </div>
        <div class="card-body">
          @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
          @endif
          @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
          @endif
          <form method="POST" action="{{ route('password.update.simple') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Correo Institucional</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña nueva</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirmar contraseña</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button class="btn btn-success w-100">Cambiar Contraseña</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
