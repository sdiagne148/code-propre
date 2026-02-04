<?php

namespace App\Repositories\Contracts;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

interface TodoRepositoryInterface
{
    /**
     * Récupère tous les todos.
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Récupère un todo par son ID.
     *
     * @param int $id
     * @return Todo|null
     */
    public function findById(int $id): ?Todo;

    /**
     * Crée un nouveau todo.
     *
     * @param array $data
     * @return Todo
     */
    public function create(array $data): Todo;

    /**
     * Met à jour un todo.
     *
     * @param Todo $todo
     * @param array $data
     * @return Todo
     */
    public function update(Todo $todo, array $data): Todo;

    /**
     * Supprime un todo.
     *
     * @param Todo $todo
     * @return bool
     */
    public function delete(Todo $todo): bool;
}
