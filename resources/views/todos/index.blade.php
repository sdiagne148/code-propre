<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Liste des tâches</title>

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
      background: #f5f7fa;
      color: #2d3748;
      padding: 20px;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
    }

    h1 {
      font-size: 28px;
      margin-bottom: 24px;
      color: #1a202c;
    }

    .card {
      background: white;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .card-header {
      padding: 16px 20px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      font-weight: 600;
      font-size: 14px;
      color: #64748b;
    }

    .list {
      list-style: none;
    }

    .item {
      display: flex;
      align-items: start;
      gap: 14px;
      padding: 18px 20px;
      border-bottom: 1px solid #f1f5f9;
    }

    .item:last-child {
      border-bottom: none;
    }

    .status-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      margin-top: 6px;
      flex-shrink: 0;
    }

    .status-dot.completed {
      background: #10b981;
    }

    .status-dot.pending {
      background: #f59e0b;
    }

    .content {
      flex: 1;
      min-width: 0;
    }

    .title {
      font-size: 16px;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 6px;
    }

    .title.completed {
      text-decoration: line-through;
      color: #94a3b8;
    }

    .description {
      font-size: 14px;
      color: #64748b;
      line-height: 1.5;
      margin-bottom: 8px;
    }

    .meta {
      font-size: 12px;
      color: #94a3b8;
    }

    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      margin-top: 4px;
    }

    .badge.completed {
      background: #d1fae5;
      color: #065f46;
    }

    .badge.pending {
      background: #fef3c7;
      color: #92400e;
    }

    .empty {
      padding: 48px 20px;
      text-align: center;
      color: #94a3b8;
    }

    .home-link {
      display: inline-block;
      margin-top: 20px;
      color: #3b82f6;
      text-decoration: none;
      font-size: 14px;
    }

    .home-link:hover {
      text-decoration: underline;
    }
  </style>
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
