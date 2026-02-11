<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Liste des tâches</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
  <div class="container">
    <h1>📝 Liste des tâches</h1>

    <div class="card">
      <div class="card-header">
        {{ count($todos) }} tâche(s)
      </div>

      <ul class="list">
        @forelse($todos as $todo)
          <li class="item">
            <span class="status-dot {{ $todo->completed ? 'completed' : 'pending' }}"></span>

            <div class="content">
              <div class="title {{ $todo->completed ? 'completed' : '' }}">
                {{ $todo->title }}
              </div>

              @if($todo->description)
                <div class="description">
                  {{ $todo->description }}
                </div>
              @endif

              <div class="meta">
                Créée le {{ $todo->created_at->format('d/m/Y à H:i') }}
              </div>

              <span class="badge {{ $todo->completed ? 'completed' : 'pending' }}">
                {{ $todo->completed ? '✓ Terminée' : '⏳ En cours' }}
              </span>
            </div>
          </li>
        @empty
          <li class="empty">
            Aucune tâche pour le moment
          </li>
        @endforelse
      </ul>
    </div>

    <a href="{{ url('/') }}" class="home-link">← Retour à l'accueil</a>
  </div>
</body>
</html>
