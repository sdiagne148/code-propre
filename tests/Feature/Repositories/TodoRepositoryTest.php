<?php

namespace Tests\Feature\Repositories;

use App\Models\Todo;
use App\Models\User;
use App\Repositories\TodoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TodoRepository $todoRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->todoRepository = new TodoRepository();
    }

    /**
     * Test de récupération de tous les todos.
     */
    public function test_get_all_todos(): void
    {
        $user = User::factory()->create();
        $todo1 = Todo::factory()->create(['user_id' => $user->id]);
        $todo2 = Todo::factory()->create(['user_id' => $user->id]);

        $result = $this->todoRepository->getAll();

        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($todo1));
        $this->assertTrue($result->contains($todo2));
    }

    /**
     * Test de récupération d'un todo par ID.
     */
    public function test_find_todo_by_id(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $result = $this->todoRepository->findById($todo->id);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals($todo->id, $result->id);
        $this->assertEquals($todo->title, $result->title);
    }

    /**
     * Test de récupération d'un todo inexistant.
     */
    public function test_find_todo_by_id_not_found(): void
    {
        $result = $this->todoRepository->findById(999);

        $this->assertNull($result);
    }

    /**
     * Test de création d'un todo.
     */
    public function test_create_todo(): void
    {
        $user = User::factory()->create();
        $todoData = [
            'title' => 'Test Todo',
            'description' => 'Description du todo',
            'completed' => false,
            'user_id' => $user->id,
        ];

        $result = $this->todoRepository->create($todoData);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals($todoData['title'], $result->title);
        $this->assertDatabaseHas('todos', [
            'title' => $todoData['title'],
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test de mise à jour d'un todo.
     */
    public function test_update_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'completed' => false,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'completed' => true,
        ];

        $result = $this->todoRepository->update($todo, $updateData);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals($updateData['title'], $result->title);
        $this->assertTrue($result->completed);
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => $updateData['title'],
            'completed' => true,
        ]);
    }

    /**
     * Test de suppression d'un todo.
     */
    public function test_delete_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $result = $this->todoRepository->delete($todo);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }
}
