<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Récupère tous les utilisateurs vérifiés.
     *
     * @return Collection
     */
    public function getVerifiedUsers(): Collection;

    /**
     * Récupère un utilisateur par son ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Crée un nouvel utilisateur.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Met à jour un utilisateur.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User;

    /**
     * Supprime un utilisateur.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool;
}
