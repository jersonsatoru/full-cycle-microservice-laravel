<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{

    use DatabaseMigrations;

    public function testListGenre()
    {
        factory(Genre::class, 100)->create();

        $this->json('GET', route('genres.index'))
             ->assertStatus(Response::HTTP_OK)
             ->assertJsonCount(100);
    }

    public function testShowGenre()
    {
        $genre = factory(Genre::class)->create();
        $genre->refresh();

        $this->json('GET', route('genres.show', ['genre' => $genre->id]))
             ->assertStatus(Response::HTTP_OK)
             ->assertJsonFragment(['name' => $genre->name])
             ->assertJsonFragment(['is_active' => $genre->is_active]);
    }

    public function testGenreCreateValidation()
    {
        $this->json('POST', route('genres.store'), [])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonValidationErrors(['name'])
             ->assertJsonMissingValidationErrors(['is_active'])
             ->assertJsonFragment([\Lang::get('validation.required', ['attribute' => 'name'])]);

        $this->json('POST', route('genres.store'), ['name' => str_repeat('s', 256)])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonMissingValidationErrors(['is_active'])
             ->assertJsonValidationErrors(['name'])
             ->assertJsonFragment([
                 \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255]
             )]);

        $this->json('POST', route('genres.store'), ['name' => 'Batata', 'is_active' => 123])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonValidationErrors(['is_active'])
             ->assertJsonMissingValidationErrors(['name'])
             ->assertJsonFragment([\Lang::get('validation.boolean', ['attribute' => 'is active'])]);
    }

    public function testGenreUpdateValidation()
    {
        $genre = factory(Genre::class)->create();
        $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonValidationErrors(['name'])
             ->assertJsonMissingValidationErrors(['is_active'])
             ->assertJsonFragment([\Lang::get('validation.required', ['attribute' => 'name'])]);

        $this->json('PUT', route('genres.update', ['genre' => $genre->id]), ['name' => str_repeat('s', 256)])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonMissingValidationErrors(['is_active'])
             ->assertJsonValidationErrors(['name'])
             ->assertJsonFragment([
                 \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255]
             )]);

        $this->json('PUT', route('genres.update', ['genre' => $genre->id]), ['name' => 'Batata', 'is_active' => 123])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonValidationErrors(['is_active'])
             ->assertJsonMissingValidationErrors(['name'])
             ->assertJsonFragment([\Lang::get('validation.boolean', ['attribute' => 'is active'])]);
    }

    public function testCreateGenre()
    {
        $this->json('POST', route('genres.store'), [ 'name' => 'Jerson' ])
             ->assertStatus(Response::HTTP_CREATED)
             ->assertJsonFragment(['name' => 'Jerson'])
             ->assertJsonFragment(['is_active' => true]);
    }

    public function testUpdateGenre()
    {
        $genre = factory(Genre::class)->create([ 'name' => 'Jerson' ]);
        $this->json(
            'PUT', 
            route('genres.update', ['genre' => $genre->id]), 
            ['name' => 'Batata', 'is_active' => false]
        )->assertStatus(Response::HTTP_OK)
         ->assertJsonFragment(['name' => 'Batata'])
         ->assertJsonFragment(['is_active' => false]);
    }

    public function testDeleteGenre()
    {
        $genre = factory(Genre::class)->create();
        $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]))
             ->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
