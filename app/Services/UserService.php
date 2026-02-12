<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Récupère tous les utilisateurs vérifiés.
     *
     * @return Collection
     */
    public function getVerifiedUsers(): Collection
    {
        return $this->userRepository->getVerifiedUsers();
    }

    /**
     * Récupère un utilisateur par son ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
            'bio' => $data['bio'] ?? null,
            'avatar_url' => $data['avatar_url'] ?? null,

        ];

        return $this->userRepository->create($userData);
    }

    /**
     * Met à jour un utilisateur.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
            'avatar_url' => $data['avatar_url'] ?? null,

        ];

        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($user, $updateData);
    }

    /**
     * Supprime un utilisateur.
     *
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}
