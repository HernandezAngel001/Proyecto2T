@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-5">Listado de Proyectos</h1>
                <p class="text-muted">Explora proyectos innovadores de nuestros emprendedores.</p>
            </div>
            <div class="col-md-4 text-end">
                @if(Auth::check() && Auth::user()->role === 'entrepreneur')
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        + Crear Proyecto
                    </a>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('projects.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Buscar proyectos..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Solo Activos</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completados
                            </option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Cancelados
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        @if($projects->count())
            <div class="row">
                @foreach($projects as $project)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ $project->image_url }}" class="card-img-top" alt="{{ $project->title }}"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary fw-bold">{{ $project->title }}</h5>
                                <p class="card-text text-truncate">{{ Str::limit($project->description, 80) }}</p>

                                <div class="mt-auto">
                                    <div class="progress mb-2" style="height: 10px;">
                                        @php
                                            $percent = ($project->funding_current / $project->funding_max) * 100;
                                            $percent = min($percent, 100);
                                        @endphp
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;"
                                            aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>{{ number_format($project->funding_current, 0) }}€ recau.</span>
                                        <span>{{ number_format($project->funding_max, 0) }}€ meta</span>
                                    </div>

                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary w-100">Ver
                                        Detalles</a>
                                </div>
                            </div>
                            <div class="card-footer bg-light text-muted small">
                                Por {{ $project->user->name }} • Finaliza {{ $project->deadline->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $projects->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                No se encontraron proyectos con esos criterios.
            </div>
        @endif
    </div>
@endsection