<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Services\TodoService;
use Illuminate\View\View;

class TodoController extends Controller
{
    /**
     * @param TodoService $todoService
     */
    public function __construct(
        private readonly TodoService $todoService
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
}