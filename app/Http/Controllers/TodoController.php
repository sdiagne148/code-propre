<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        // all | open | done
        $filter = $request->string('filter')->toString();
        if (!in_array($filter, ['all', 'open', 'done'], true)) {
            $filter = 'all';
        }

        $user = $request->user();

        // Base query
        $query = Todo::query()->latest();

        // Périmètre: si connecté => ses todos, sinon => tous les todos
        if ($user) {
            $query->where('user_id', $user->id);
        }

        // Filtre
        if ($filter === 'open') {
            $query->where('completed', false);
        } elseif ($filter === 'done') {
            $query->where('completed', true);
        }

        $todos = $query->paginate(10)->withQueryString();

        // Counts (même périmètre que la liste)
        $baseCountQuery = Todo::query();
        if ($user) {
            $baseCountQuery->where('user_id', $user->id);
        }

        $counts = [
            'all'  => (clone $baseCountQuery)->count(),
            'open' => (clone $baseCountQuery)->where('completed', false)->count(),
            'done' => (clone $baseCountQuery)->where('completed', true)->count(),
        ];

        return view('todos.index', compact('todos', 'filter', 'counts'));
    }
}
