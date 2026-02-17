<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Services\TodoService;
use App\Services\UserService;
use Illuminate\View\View;
use App\Models\User;

class TodoController extends Controller
{
    /**
     * @param TodoService $todoService
     */
    public function __construct(
        private readonly TodoService $todoService,
        private readonly UserService $userService
    ) {
    }

    /**
     * Affiche la liste des tâches.
     *
     * @return View
     */
    public function index(): View
    {
        $todos = $this->todoService->getAllTodos();

        return view('todos.index', compact('todos'));
    }

    public function create(): View 
    
    {
        $users = $this->userService->getVerifiedUsers();
        
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