<?php

namespace Tests\Unit\Services;

use App\Models\Todo;
use App\Repositories\Contracts\TodoRepositoryInterface;
use App\Services\TodoService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TodoServiceTest extends TestCase
{
    use RefreshDatabase;

    private TodoService $todoService;

    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un mock du repository
        $this->mockRepository = Mockery::mock(TodoRepositoryInterface::class);
        $this->todoService = new TodoService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test de récupération de tous les todos.
     */
    public function test_get_all_todos(): void
    {
        $expectedTodos = new Collection([
            Todo::factory()->make(),
            Todo::factory()->make(),
        ]);

        $this->mockRepository
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($expectedTodos);

        $result = $this->todoService->getAllTodos();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    /**
     * Test de récupération d'un todo par ID.
     */
    public function test_find_todo_by_id(): void
    {
        $todo = Todo::factory()->make(['id' => 1]);

        $this->mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($todo);

        $result = $this->todoService->findTodoById(1);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals(1, $result->id);
    }

    /**
     * Test de création d'un todo.
     */
    public function test_create_todo(): void
    {
        $todoData = [
            'title' => 'Test Todo',
            'description' => 'Description du todo',
            'completed' => false,
        ];

        $createdTodo = Todo::factory()->make($todoData);

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($todoData)
            ->andReturn($createdTodo);

        $result = $this->todoService->createTodo($todoData);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals($todoData['title'], $result->title);
    }

    /**
     * Test de mise à jour d'un todo.
     */
    public function test_update_todo(): void
    {
        $todo = Todo::factory()->create();
        $updateData = [
            'title' => 'Updated Todo',
            'completed' => true,
        ];

        $updatedTodo = Todo::factory()->make([
            'id' => $todo->id,
            'title' => $updateData['title'],
            'completed' => $updateData['completed'],
        ]);

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($todo, $updateData)
            ->andReturn($updatedTodo);

        $result = $this->todoService->updateTodo($todo, $updateData);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals($updateData['title'], $result->title);
    }

    /**
     * Test de suppression d'un todo.
     */
    public function test_delete_todo(): void
    {
        $todo = Todo::factory()->create();

        $this->mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with($todo)
            ->andReturn(true);

        $result = $this->todoService->deleteTodo($todo);

        $this->assertTrue($result);
    }
}
