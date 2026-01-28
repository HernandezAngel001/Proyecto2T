@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="bg-primary text-white py-5 mb-5 text-center"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <h1 class="display-4 fw-bold">STEM Funding</h1>
            <p class="lead mb-4">Apoyando el futuro de la Ciencia, Tecnología, Ingeniería y Matemáticas.</p>

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form action="{{ route('projects.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-lg me-2"
                            placeholder="Buscar proyectos...">
                        <button class="btn btn-light btn-lg px-4" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Featured Projects (Carousel) -->
        @if($featured->count() > 0)
            <div class="mb-5">
                <h2 class="mb-4 fw-bold border-bottom pb-2">Éxitos Recientes <small class="text-muted fs-5">(Destacados)</small>
                </h2>

                <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded shadow-lg overflow-hidden">
                        @foreach($featured as $index => $project)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="position-relative" style="height: 400px;">
                                    <img src="{{ $project->image_url }}" class="d-block w-100 h-100"
                                        style="object-fit: cover; filter: brightness(0.6);">
                                    <div class="carousel-caption d-none d-md-block text-start p-4 bg-dark bg-opacity-50 rounded"
                                        style="bottom: 20px; left: 50px; right: 50px;">
                                        <h3 class="fw-bold">{{ $project->title }}</h3>
                                        <p class="text-truncate">{{ Str::limit($project->description, 100) }}</p>
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-primary">Ver Proyecto</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($featured->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <!-- All Projects Grid -->
        <div class="mb-5">
            <h2 class="mb-4 fw-bold">Todos los Proyectos</h2>

            <div class="row">
                @forelse($projects as $project)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm hover-shadow transition-all">
                            <img src="{{ $project->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-truncate">{{ $project->title }}</h5>
                                <p class="card-text text-muted small flex-grow-1">{{ Str::limit($project->description, 80) }}
                                </p>

                                <div class="mt-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span
                                            class="fw-bold text-primary">{{ number_format($project->funding_current) }}€</span>
                                        <span class="text-muted">{{ number_format($project->funding_max) }}€ meta</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ min(($project->funding_current / $project->funding_max) * 100, 100) }}%">
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary mt-3 w-100">Ver
                                    Detalles</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <h4 class="text-muted">No se encontraron proyectos activos.</h4>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
@endsection