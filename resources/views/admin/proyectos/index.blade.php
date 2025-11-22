@extends('layouts.app')
@section('title','Repositorio de Proyectos')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Repositorio Digital de Proyectos Académicos</h4>
    <a href="{{ route('proyectos.create') }}" class="btn btn-success">
      <i class="bi bi-plus-circle"></i> Registrar Proyecto
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">
      <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
  @endif

  <table class="table table-striped table-hover">
    <thead class="table-primary">
      <tr>
        <th>Título</th>
        <th>Estudiante</th>
        <th>Carrera</th>
        <th>Programa</th>
        <th>Fecha Defensa</th>
        <th>Calificación</th>
        <th>Archivo</th>
      </tr>
    </thead>
    <tbody>
      @foreach($proyectos as $p)
      <tr>
        <td>{{ $p->titulo }}</td>
        <td>{{ $p->estudiante->usuario->name ?? '—' }}</td>
        <td>{{ $p->carrera->nombre }}</td>
        <td>{{ $p->programa->nombre }}</td>
        <td>{{ $p->fecha_defensa }}</td>
        <td>{{ $p->calificacion ?? '—' }}</td>
        
        <td>
          <a href="{{ asset('storage/'.$p->link_pdf) }}" class="btn btn-sm btn-outline-primary" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i> Ver PDF
          </a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
