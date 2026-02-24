<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Récupère tous les utilisateurs vérifiés.
     */
    public function getVerifiedUsers(): Collection
    {
        return User::verified()->get();
    }

    /**
     * Récupère un utilisateur par son ID.
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Crée un nouvel utilisateur.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    /**
     * Supprime un utilisateur.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
