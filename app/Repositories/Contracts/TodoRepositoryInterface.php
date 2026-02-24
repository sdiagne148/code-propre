<?php

namespace App\Repositories\Contracts;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

interface TodoRepositoryInterface
{
    /**
     * Récupère tous les todos.
     */
    public function getAll(): Collection;

    /**
     * Récupère un todo par son ID.
     */
    public function findById(int $id): ?Todo;

    /**
     * Crée un nouveau todo.
     */
    public function create(array $data): Todo;

    /**
     * Met à jour un todo.
     */
    public function update(Todo $todo, array $data): Todo;

    /**
     * Supprime un todo.
     */
    public function delete(Todo $todo): bool;
}
