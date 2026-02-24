<?php

namespace App\Http\Controllers;

use App\Services\TodoService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function __construct(
        private readonly TodoService $todoService,
        private readonly UserService $userService
    ) {}

    /**
     * Affiche la liste des tâches.
     */
    public function index(): View
    {
        $todos = $this->todoService->getAllTodos();

        return view('todos.index', compact('todos'));
    }

    public function create(): View
    {
        $users = $this->userService->getVerifiedUsers();

        return view('todos.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $this->todoService->createTodo($request->only('title', 'description', 'user_id'));

        return redirect()->route('todos.index')->with('success', 'Tâche créée avec succès.');
    }
}
