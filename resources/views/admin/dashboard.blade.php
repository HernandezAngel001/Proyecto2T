@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Panel de Administración</h1>
            <div>
                <a href="{{ route('admin.projects') }}" class="btn btn-outline-primary">Gestionar Proyectos</a>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-dark">Gestionar Usuarios</a>
            </div>
        </div>

        <!-- KPIs -->
        <div class="row text-center mb-5">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h2 class="display-4 fw-bold text-primary">{{ $stats['active_projects'] }}</h2>
                        <p class="text-muted">Proyectos Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h2 class="display-4 fw-bold text-success">{{ number_format($stats['total_funding']) }}€</h2>
                        <p class="text-muted">Fondos Recaudados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h2 class="display-4 fw-bold text-warning">{{ $stats['pending_review'] }}</h2>
                        <p class="text-muted">Pendientes de Revisión</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h2 class="display-4 fw-bold text-dark">{{ $stats['total_users'] }}</h2>
                        <p class="text-muted">Usuarios Totales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Projects Actions -->
        @if($pendingProjects->count() > 0)
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-header bg-warning bg-opacity-10 text-warning fw-bold border-0">
                    <i class="bi bi-hourglass-split me-2"></i> Proyectos Pendientes de Revisión
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Proyecto</th>
                                    <th>Emprendedor</th>
                                    <th>Meta</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingProjects as $project)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                @if($project->image_url)
                                                    <img src="{{ $project->image_url }}" class="rounded me-3" width="50" height="50" style="object-fit:cover;">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $project->title }}</h6>
                                                    <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($project->user->photo_url)
                                                    <img src="{{ $project->user->photo_url }}" class="rounded-circle me-2" width="30" height="30">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                        {{ substr($project->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <span>{{ $project->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ number_format($project->funding_min) }}€ - {{ number_format($project->funding_max) }}€</td>
                                        <td>
                                            <form action="{{ route('admin.projects.review', $project) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm me-1" onclick="return confirm('¿Aprobar este proyecto?')">
                                                    <i class="bi bi-check-lg"></i> Aprobar
                                                </button>
                                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm" onclick="return confirm('¿Rechazar este proyecto?')">
                                                    <i class="bi bi-x-lg"></i> Rechazar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection