<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Récupère tous les utilisateurs vérifiés.
     */
    public function getVerifiedUsers(): Collection;

    /**
     * Récupère un utilisateur par son ID.
     */
    public function findById(int $id): ?User;

    /**
     * Crée un nouvel utilisateur.
     */
    public function create(array $data): User;

    /**
     * Met à jour un utilisateur.
     */
    public function update(User $user, array $data): User;

    /**
     * Supprime un utilisateur.
     */
    public function delete(User $user): bool;
}
