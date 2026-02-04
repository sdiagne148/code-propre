<?php

namespace App\Repositories;

use App\Models\Todo;
use App\Repositories\Contracts\TodoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TodoRepository implements TodoRepositoryInterface
{
    /**
     * Récupère tous les todos.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Todo::all();
    }

    /**
     * Récupère un todo par son ID.
     *
     * @param int $id
     * @return Todo|null
     */
    public function findById(int $id): ?Todo
    {
        return Todo::find($id);
    }

    /**
     * Crée un nouveau todo.
     *
     * @param array $data
     * @return Todo
     */
    public function create(array $data): Todo
    {
        return Todo::create($data);
    }

    /**
     * Met à jour un todo.
     *
     * @param Todo $todo
     * @param array $data
     * @return Todo
     */
    public function update(Todo $todo, array $data): Todo
    {
        $todo->update($data);
        return $todo->fresh();
    }

    /**
     * Supprime un todo.
     *
     * @param Todo $todo
     * @return bool
     */
    public function delete(Todo $todo): bool
    {
        return $todo->delete();
    }
}
