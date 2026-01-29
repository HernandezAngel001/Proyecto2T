@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Project Details (Left) -->
            <div class="col-md-8">
                <img src="{{ $project->image_url }}" class="img-fluid rounded shadow-sm mb-4 w-100"
                    style="max-height: 400px; object-fit: cover;">

                <h1 class="fw-bold">{{ $project->title }}</h1>
                <div class="d-flex align-items-center mb-3">
                    <span class="badge 
                                @if($project->status == 'active') bg-success 
                                @elseif($project->status == 'completed') bg-primary
                                @elseif($project->status == 'canceled') bg-danger
                                @else bg-secondary @endif 
                                me-2">
                        @if($project->status == 'active') Activo
                        @elseif($project->status == 'completed') Completado
                        @elseif($project->status == 'canceled') Cancelado
                        @elseif($project->status == 'rejected') Rechazado
                        @else Pendiente @endif
                    </span>
                    <span class="text-muted">Por {{ $project->user->name }}</span>
                </div>

                <div class="lead mb-5">
                    {{ $project->description }}
                </div>

                <!-- Carousel for Updates (Requirement: Mandatory Carousel) -->
                @if($project->updates->count() > 0)
                    <h3 class="mb-3">Actualizaciones del Proyecto</h3>
                    <div id="updatesCarousel" class="carousel slide mb-5 border rounded" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($project->updates as $index => $update)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <div class="p-4 text-center bg-light">
                                        @if($update->photo_url)
                                            <img src="{{ $update->photo_url }}" class="d-block mx-auto mb-3 rounded"
                                                style="max-height: 200px;">
                                        @endif
                                        <h5>{{ $update->title }}</h5>
                                        <p>{{ $update->description }}</p>
                                        <small class="text-muted">{{ $update->created_at->format('d M, Y') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($project->updates->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#updatesCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#updatesCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar (Right) - Funding & Actions -->
            <div class="col-md-4">
                <div class="card shadow border-0 sticky-top" style="top: 2rem;">
                    <div class="card-body">
                        <h2 class="display-6 fw-bold">{{ number_format($project->funding_current) }}€</h2>
                        <div class="text-muted mb-2">recaudados de {{ number_format($project->funding_max) }}€ meta</div>

                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ min(($project->funding_current / $project->funding_max) * 100, 100) }}%">
                            </div>
                        </div>

                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="bi bi-bullseye text-primary me-2"></i> Meta Mínima:
                                <strong>{{ number_format($project->funding_min) }}€</strong>
                            </li>
                            <li class="mb-2"><i class="bi bi-calendar text-primary me-2"></i> Finaliza:
                                <strong>{{ $project->deadline->format('d M, Y') }}</strong>
                            </li>
                            <li class="mb-2"><i class="bi bi-clock text-primary me-2"></i> Días Restantes:
                                <strong>{{ max(0, now()->diffInDays($project->deadline)) }}</strong>
                            </li>
                        </ul>

                        <!-- Action Buttons -->
                        @auth
                            @if(Auth::user()->role === 'investor' && $project->status === 'active')
                                <!-- Invest Button (Trigger Modal) -->
                                <button class="btn btn-primary w-100 btn-lg mb-2" data-bs-toggle="modal"
                                    data-bs-target="#investModal">
                                    Respaldar Proyecto
                                </button>
                            @elseif(Auth::user()->role === 'entrepreneur' && Auth::id() === $project->user_id)
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-dark w-100 mb-2">Editar
                                    Proyecto</a>
                                <!-- Add Update Button -->
                                <button class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#updateModal">Añadir Actualización</button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Inicia Sesión para Invertir</a>
                        @endauth

                        <!-- Social Share (Optional aesthetics) -->
                        <div class="mt-4 text-center">
                            <small class="text-muted">Compartir este proyecto</small>
                            <div class="mt-2">
                                <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-facebook"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-twitter"></i></a>
                                <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investment History (For Investor) -->
                @auth
                    @if(Auth::user()->role === 'investor')
                        <div class="card mt-4 shadow-sm">
                            <div class="card-header bg-white fw-bold">Tus Inversiones</div>
                            <ul class="list-group list-group-flush">
                                @forelse($userInvestments as $investment)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ number_format($investment->amount) }}€</strong>
                                            <div class="small text-muted">{{ $investment->created_at->diffForHumans() }}</div>
                                        </div>
                                        @if($investment->created_at->diffInHours(now()) < 24 && $project->status === 'active')
                                            <form action="{{ route('investments.destroy', $investment) }}" method="POST"
                                                onsubmit="return confirm('¿Estás seguro de que deseas retirar esta inversión?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Retirar</button>
                                            </form>
                                        @endif
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted small text-center">Aún no has respaldado este proyecto.</li>
                                @endforelse
                            </ul>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Invest Modal -->
    <div class="modal fade" id="investModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Respaldar este Proyecto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('investments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div class="mb-3">
                            <label class="form-label">Cantidad (€)</label>
                            <input type="number" name="amount" class="form-control" min="1"
                                max="{{ $project->funding_max - $project->funding_current }}" required>
                            <div class="form-text">Disponible en cartera: {{ Auth::check() ? Auth::user()->wallet : 0 }}€
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Confirmar Pago</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Publicar Actualización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('updates.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL de Foto (Opcional)</label>
                            <input type="url" name="photo_url" class="form-control" placeholder="https://...">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Publicar Actualización</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection