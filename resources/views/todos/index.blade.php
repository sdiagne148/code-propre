<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Todos — {{ config('app.name', 'Laravel') }}</title>

  <style>
    :root{
      --bg: #f6f7fb;
      --card: #ffffff;
      --text: #0f172a;
      --muted: #64748b;
      --border: #e2e8f0;
      --black: #0b1220;

      --open: #f59e0b;
      --done: #22c55e;

      --radius: 14px;
      --shadow: 0 10px 30px rgba(15, 23, 42, .08);
      --shadow-soft: 0 8px 20px rgba(15, 23, 42, .06);
      --font: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, "Noto Sans", "Liberation Sans", sans-serif;
    }

    *{ box-sizing: border-box; }
    body{
      margin:0;
      font-family: var(--font);
      background: radial-gradient(1200px 700px at 10% 0%, #eef2ff 0%, transparent 55%),
                  radial-gradient(900px 600px at 90% 10%, #ecfeff 0%, transparent 55%),
                  var(--bg);
      color: var(--text);
    }

    .container{
      max-width: 980px;
      margin: 0 auto;
      padding: 28px 18px 48px;
    }

    .topbar{
      display:flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 18px;
    }

    .title{
      margin:0;
      font-size: 28px;
      letter-spacing: -0.02em;
      line-height: 1.15;
    }

    .subtitle{
      margin: 8px 0 0;
      color: var(--muted);
      font-size: 14px;
    }

    .home-link{
      margin-top: 6px;
      font-size: 14px;
      color: var(--text);
      text-decoration: none;
      border-bottom: 1px solid rgba(15, 23, 42, .35);
      padding-bottom: 2px;
      white-space: nowrap;
    }
    .home-link:hover{ border-bottom-color: rgba(15, 23, 42, .9); }

    .filters{
      display:flex;
      flex-wrap: wrap;
      gap: 10px;
      margin: 16px 0 18px;
    }

    .chip{
      display:inline-flex;
      align-items:center;
      gap: 8px;
      padding: 9px 12px;
      border: 1px solid var(--border);
      background: rgba(255,255,255,.75);
      backdrop-filter: blur(6px);
      border-radius: 999px;
      text-decoration: none;
      color: var(--text);
      font-size: 13px;
      box-shadow: 0 6px 16px rgba(15,23,42,.05);
      transition: transform .08s ease, background .15s ease, border-color .15s ease;
    }
    .chip:hover{ transform: translateY(-1px); border-color: #cbd5e1; }
    .chip.is-active{
      background: var(--black);
      color:#fff;
      border-color: var(--black);
    }
    .badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width: 28px;
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 12px;
      line-height: 18px;
      background: rgba(15, 23, 42, .06);
      color: rgba(15, 23, 42, .85);
    }
    .chip.is-active .badge{
      background: rgba(255,255,255,.14);
      color:#fff;
    }

    .card{
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow:hidden;
    }

    .card-head{
      display:flex;
      justify-content: space-between;
      align-items:center;
      gap: 10px;
      padding: 14px 16px;
      border-bottom: 1px solid var(--border);
      background: linear-gradient(to bottom, rgba(248,250,252,.9), rgba(255,255,255,.9));
    }
    .card-head .meta{
      color: var(--muted);
      font-size: 13px;
    }

    .list{
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .item{
      display:flex;
      gap: 14px;
      padding: 16px;
      border-top: 1px solid var(--border);
    }
    .item:first-child{ border-top: 0; }

    .dot{
      width: 10px;
      height: 10px;
      border-radius: 50%;
      margin-top: 6px;
      flex: 0 0 auto;
      box-shadow: 0 0 0 4px rgba(2,6,23,.03);
    }
    .dot.open{ background: var(--open); }
    .dot.done{ background: var(--done); }

    .content{
      flex: 1 1 auto;
      min-width: 0;
    }

    .row{
      display:flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .todo-title{
      font-weight: 650;
      font-size: 15px;
      margin: 0;
    }
    .todo-title.is-done{
      text-decoration: line-through;
      color: #64748b;
      font-weight: 600;
    }

    .status{
      font-size: 12px;
      padding: 4px 10px;
      border-radius: 999px;
      border: 1px solid var(--border);
      background: #f8fafc;
      color: #334155;
    }
    .status.open{
      border-color: rgba(245, 158, 11, .30);
      background: rgba(245, 158, 11, .12);
      color: #92400e;
    }
    .status.done{
      border-color: rgba(34, 197, 94, .30);
      background: rgba(34, 197, 94, .12);
      color: #166534;
    }

    .desc{
      margin: 10px 0 0;
      color: #334155;
      font-size: 14px;
      line-height: 1.45;
      white-space: pre-line;
    }

    .date{
      margin: 10px 0 0;
      font-size: 12px;
      color: var(--muted);
    }

    .empty{
      padding: 28px 16px;
      text-align:center;
      color: var(--muted);
    }

    .pagination{
      padding: 14px 16px;
      border-top: 1px solid var(--border);
      background: #fff;
    }

    /* Rendu pagination Laravel simple (sans Tailwind) */
    .pagination nav{
      display:flex;
      justify-content:center;
    }
    .pagination nav > div{
      width: 100%;
      display:flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .pagination a, .pagination span{
      font-size: 13px;
    }
    .pagination a{
      color: var(--text);
      text-decoration: none;
      border: 1px solid var(--border);
      padding: 8px 10px;
      border-radius: 10px;
      background: #fff;
      box-shadow: var(--shadow-soft);
    }
    .pagination a:hover{ border-color: #cbd5e1; transform: translateY(-1px); }
  </style>
</head>

<body>
  <main class="container">
    <header class="topbar">
      <div>
        <h1 class="title">Mes tâches</h1>
        <p class="subtitle">
          Filtre :
          <strong>{{ $filter }}</strong>
        </p>
      </div>

      <a class="home-link" href="{{ url('/') }}">Accueil</a>
    </header>

    <section class="filters">
      <a class="chip {{ $filter === 'all' ? 'is-active' : '' }}"
         href="{{ route('todos.index', ['filter' => 'all']) }}">
        Toutes <span class="badge">{{ $counts['all'] }}</span>
      </a>

      <a class="chip {{ $filter === 'open' ? 'is-active' : '' }}"
         href="{{ route('todos.index', ['filter' => 'open']) }}">
        À faire <span class="badge">{{ $counts['open'] }}</span>
      </a>

      <a class="chip {{ $filter === 'done' ? 'is-active' : '' }}"
         href="{{ route('todos.index', ['filter' => 'done']) }}">
        Terminées <span class="badge">{{ $counts['done'] }}</span>
      </a>
    </section>

    <section class="card">
      <div class="card-head">
        <div class="meta">{{ $todos->total() }} tâche(s)</div>
        <div class="meta">Page {{ $todos->currentPage() }} / {{ $todos->lastPage() }}</div>
      </div>

      <ul class="list">
        @forelse($todos as $todo)
          <li class="item">
            <span class="dot {{ $todo->completed ? 'done' : 'open' }}"></span>

            <div class="content">
              <div class="row">
                <p class="todo-title {{ $todo->completed ? 'is-done' : '' }}">
                  {{ $todo->title }}
                </p>

                <span class="status {{ $todo->completed ? 'done' : 'open' }}">
                  {{ $todo->completed ? 'Done' : 'Open' }}
                </span>
              </div>

              @if($todo->description)
                <p class="desc">{{ $todo->description }}</p>
              @endif

              <p class="date">
                Créée le {{ $todo->created_at->format('d/m/Y H:i') }}
              </p>
            </div>
          </li>
        @empty
          <li class="empty">Aucune tâche pour ce filtre.</li>
        @endforelse
      </ul>

      @if ($todos->hasPages())
        <div class="pagination">
          {{ $todos->links() }}
        </div>
      @endif
    </section>
  </main>
</body>
</html>