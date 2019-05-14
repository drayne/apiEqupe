<?php

namespace Tests\Feature;

use App\Task;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    
    /** @test */
    function it_will_show_all_tasks()
    {
        $tasks = factory(Task::class, 10)->create();
//        dd(route('tasks.index'));
        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);
        $response->assertJson($tasks->toArray());
    }

    /** @test */
    function it_will_create_tasks()
    {
        $response =$this->post(route('tasks.store'), [
            'title' => 'This is a title',
            'description' => 'This is a description'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'title' => 'This is a title'
        ]);

        $response->assertJsonStructure([
            'message',
            'task' => [
                'title', 'description', 'updated_at', 'created_at', 'id'
            ]
        ]);
    }

    /** @test */
    function it_will_show_a_task()
    {
        $response =$this->post(route('tasks.store'), [
            'title' => 'This is a title',
            'description' => 'This is a description'
        ]);

        $task = Task::all()->first();
        $response = $this->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertJson($task->toArray());
    }

    /** @test */
    function it_will_update_a_task()
    {
        $this->post(route('tasks.store'), [
            'title' => 'This is a title',
            'description' => 'This is a desrciption'
        ]);

        $task = Task::all()->first();

        $response = $this->put(route('tasks.update', $task), [
            'title' => 'This is a new title'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'task' => [
                'title', 'description', 'updated_at', 'created_at', 'id'
            ]
        ]);

        $task = $task->fresh();
        $this->assertEquals('This is a new title', $task->title);

    }
    
    /** @test */
    function it_will_delete_a_task()
    {
        $this->post(route('tasks.store'), [
            'title'       => 'This is a title',
            'description' => 'This is a description'
        ]);

        $task = Task::all()->first();


        $response = $this->delete(route('tasks.delete', $task->id));
        $task = $task->fresh();
        $this->assertNull($task);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Successfully deleted task!'
        ]);
    }



}
