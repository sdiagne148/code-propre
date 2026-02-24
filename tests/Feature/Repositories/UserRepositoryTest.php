<?php

namespace Tests\Feature\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository;
    }

    /**
     * Test de récupération des utilisateurs vérifiés.
     */
    public function test_get_verified_users(): void
    {
        // Créer des utilisateurs vérifiés et non vérifiés
        $verifiedUser1 = User::factory()->create(['email_verified_at' => now()]);
        $verifiedUser2 = User::factory()->create(['email_verified_at' => now()]);
        $unverifiedUser = User::factory()->unverified()->create();

        $result = $this->userRepository->getVerifiedUsers();

        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($verifiedUser1));
        $this->assertTrue($result->contains($verifiedUser2));
        $this->assertFalse($result->contains($unverifiedUser));
    }

    /**
     * Test de récupération d'un utilisateur par ID.
     */
    public function test_find_user_by_id(): void
    {
        $user = User::factory()->create();

        $result = $this->userRepository->findById($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals($user->name, $result->name);
        $this->assertEquals($user->email, $result->email);
    }

    /**
     * Test de récupération d'un utilisateur inexistant.
     */
    public function test_find_user_by_id_not_found(): void
    {
        $result = $this->userRepository->findById(999);

        $this->assertNull($result);
    }

    /**
     * Test de création d'un utilisateur.
     */
    public function test_create_user(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ];

        $result = $this->userRepository->create($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userData['name'], $result->name);
        $this->assertEquals($userData['email'], $result->email);
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /**
     * Test de mise à jour d'un utilisateur.
     */
    public function test_update_user(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $result = $this->userRepository->update($user, $updateData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
        $this->assertEquals($updateData['email'], $result->email);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updateData['name'],
            'email' => $updateData['email'],
        ]);
    }

    /**
     * Test de suppression d'un utilisateur.
     */
    public function test_delete_user(): void
    {
        $user = User::factory()->create();

        $result = $this->userRepository->delete($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
