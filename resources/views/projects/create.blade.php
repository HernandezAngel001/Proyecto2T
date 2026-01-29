@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">Crear Nuevo Proyecto</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('projects.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Título del Proyecto</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" class="form-control" rows="4"
                                    required>{{ old('description') }}</textarea>
                                <div class="form-text">Describe tu idea, tu equipo, y por qué se debería invertir en ella.
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Meta Mínima (€)</label>
                                    <input type="number" name="funding_min" class="form-control"
                                        value="{{ old('funding_min') }}" min="100" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Meta Máxima (€)</label>
                                    <input type="number" name="funding_max" class="form-control"
                                        value="{{ old('funding_max') }}" min="100" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha Límite</label>
                                <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">URL de Imagen Principal</label>
                                <input type="url" name="image_url" class="form-control" value="{{ old('image_url') }}"
                                    placeholder="https://..." required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">URL de Video (Opcional)</label>
                                <input type="url" name="video_url" class="form-control" value="{{ old('video_url') }}">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Enviar para Aprobación</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection