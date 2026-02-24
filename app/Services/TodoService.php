<?php

namespace App\Services;

use App\Models\Todo;
use App\Repositories\Contracts\TodoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TodoService
{
    public function __construct(
        private readonly TodoRepositoryInterface $todoRepository
    ) {}

    /**
     * Récupère tous les todos.
     */
    public function getAllTodos(): Collection
    {
        return $this->todoRepository->getAll();
    }

    /**
     * Récupère un todo par son ID.
     */
    public function findTodoById(int $id): ?Todo
    {
        return $this->todoRepository->findById($id);
    }

    /**
     * Crée un nouveau todo.
     */
    public function createTodo(array $data): Todo
    {
        return $this->todoRepository->create($data);
    }

    /**
     * Met à jour un todo.
     */
    public function updateTodo(Todo $todo, array $data): Todo
    {
        return $this->todoRepository->update($todo, $data);
    }

    /**
     * Supprime un todo.
     */
    public function deleteTodo(Todo $todo): bool
    {
        return $this->todoRepository->delete($todo);
    }
}
