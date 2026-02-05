<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Détails de la tâche</h3>
        </div>
        <div class="card-body">
            <h4>{{ $todo->title }}</h4>
            <hr>

            <p><strong>Description :</strong></p>
            <p>{{ $todo->description ?? 'Aucune description' }}</p>

            <p><strong>Statut :</strong>
                @if($todo->completed)
                    <span class="badge bg-success">Terminé</span>
                @else
                    <span class="badge bg-warning">À faire</span>
                @endif
            </p>

            <p><strong>Date création :</strong> {{ $todo->created_at->format('d/m/Y H:i') }}</p>

            <div class="mt-3">
                <a href="{{ route('todos.index') }}" class="btn btn-secondary">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
