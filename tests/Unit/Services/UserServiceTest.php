<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un mock du repository
        $this->mockRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test de récupération des utilisateurs vérifiés.
     */
    public function test_get_verified_users(): void
    {
        $expectedUsers = new Collection([
            User::factory()->make(['email_verified_at' => now()]),
            User::factory()->make(['email_verified_at' => now()]),
        ]);

        $this->mockRepository
            ->shouldReceive('getVerifiedUsers')
            ->once()
            ->andReturn($expectedUsers);

        $result = $this->userService->getVerifiedUsers();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    /**
     * Test de récupération d'un utilisateur par ID.
     */
    public function test_find_user_by_id(): void
    {
        $user = User::factory()->make(['id' => 1]);

        $this->mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($user);

        $result = $this->userService->findById(1);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
    }

    /**
     * Test de création d'un utilisateur.
     */
    public function test_create_user(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $createdUser = User::factory()->make([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'email_verified_at' => now(),
        ]);

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($userData) {
                return $data['name'] === $userData['name']
                    && $data['email'] === $userData['email']
                    && isset($data['password'])
                    && isset($data['email_verified_at']);
            }))
            ->andReturn($createdUser);

        $result = $this->userService->createUser($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userData['name'], $result->name);
        $this->assertEquals($userData['email'], $result->email);
    }

    /**
     * Test de mise à jour d'un utilisateur sans mot de passe.
     */
    public function test_update_user_without_password(): void
    {
        $user = User::factory()->create();
        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $updatedUser = User::factory()->make([
            'id' => $user->id,
            'name' => $updateData['name'],
            'email' => $updateData['email'],
        ]);

        // Le service enrichit $updateData avec phone, bio, avatar_url (null si absents)
        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($user, Mockery::on(function ($data) use ($updateData) {
                return $data['name'] === $updateData['name']
                    && $data['email'] === $updateData['email']
                    && array_key_exists('phone', $data)
                    && array_key_exists('bio', $data)
                    && array_key_exists('avatar_url', $data)
                    && !isset($data['password']);
            }))
            ->andReturn($updatedUser);

        $result = $this->userService->updateUser($user, $updateData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($updateData['name'], $result->name);
        $this->assertEquals($updateData['email'], $result->email);
    }

    /**
     * Test de mise à jour d'un utilisateur avec mot de passe.
     */
    public function test_update_user_with_password(): void
    {
        $user = User::factory()->create();
        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'newpassword123',
        ];

        $updatedUser = User::factory()->make([
            'id' => $user->id,
            'name' => $updateData['name'],
            'email' => $updateData['email'],
        ]);

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($user, Mockery::on(function ($data) use ($updateData) {
                return $data['name'] === $updateData['name']
                    && $data['email'] === $updateData['email']
                    && isset($data['password']);
            }))
            ->andReturn($updatedUser);

        $result = $this->userService->updateUser($user, $updateData);

        $this->assertInstanceOf(User::class, $result);
    }

    /**
     * Test de suppression d'un utilisateur.
     */
    public function test_delete_user(): void
    {
        $user = User::factory()->create();

        $this->mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with($user)
            ->andReturn(true);

        $result = $this->userService->deleteUser($user);

        $this->assertTrue($result);
    }
}
