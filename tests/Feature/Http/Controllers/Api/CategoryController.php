<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Tests\Traits\TestValidations;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    
    use DatabaseMigrations, TestValidations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $this->get(route('categories.index'))
             ->assertStatus(Response::HTTP_OK)
             ->assertJsonCount(1)
             ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $this->get(route('categories.show', ['category' => $category->id]))
             ->assertStatus(Response::HTTP_OK)
             ->assertJson($category->toArray());
    }

    public function testCreateValidation()
    {
        $response = $this->json('POST', route('categories.store'), []);
        $this->assertRequiredFields($response, ['name']);

        $response = $this->json('POST', route('categories.store', ['is_active' => 'a']));
        $this->assertValidation($response, ['is_active'], 'boolean');

        $response = $this->json('POST', route('categories.store', ['name' => str_repeat('s', 256)]));
        $this->assertMax255CharsFields($response, ['name']);
    }

    protected function assertRequiredFields(TestResponse $response, array $validationErrors) 
    {
        $this->assertValidation($response, $validationErrors, 'required');
    }

    protected function assertBooleanFields(TestResponse $response, array $validationErrors)
    {
        $this->assertValidation($response, $validationErrors, 'boolean');
    }

    protected function assertMax255CharsFields(TestResponse $response, array $validationErrors)
    {
        $this->assertValidation($response, $validationErrors, 'max.string', ['max' => 255]);
    }

    public function testUpdateValidation()
    {
        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), []);
        $this->assertRequiredFields($response, ['name']);

        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT', 
            route('categories.update', ['category' => $category->id]),
            ['name' => str_repeat('a', 256), 'is_active' => 'a'],
        );

        $this->assertBooleanFields($response, ['is_active']);
        $this->assertMax255CharsFields($response, ['name']);
    }

    public function testStoreCategory()
    {
        $response = $this->json('POST', route('categories.store', [
            'name' => 'Batata',
            'description' => 'Uma batata',
        ]));

        $category = Category::find($response->json('id'));

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonFragment(['name' => 'Batata'])
                 ->assertJsonFragment(['id' => $category->id])
                 ->assertJsonFragment(['is_active' => true])
                 ->assertJsonFragment(['description' => 'Uma batata'])
                 ->assertJson($category->toArray());

            
        $response = $this->json('POST', route('categories.store', [
            'name' => 'Batata 2',
            'is_active' => false,
        ]));

        $category = Category::find($response->json('id'));

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonFragment(['name' => 'Batata 2'])
                 ->assertJsonFragment(['id' => $category->id])
                 ->assertJsonFragment(['is_active' => false])
                 ->assertJsonFragment(['description' => null])
                 ->assertJson($category->toArray());
    }

    public function testUpdateCategory()
    {
        $category = factory(Category::class)->create([
            'name' => 'Batata',
            'is_active' => false,
        ]);

        $response = $this->json('PUT', route('categories.update', ['category' =>  $category->id]), [
            'name' => 'Batata frita',
            'is_active' => true,
            'description' => 'Uma batata frita',
        ]);

        $category = Category::find($response->json('id'));

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson($category->toArray())
                 ->assertJsonFragment(['name' => 'Batata frita'])
                 ->assertJsonFragment(['is_active' => true])
                 ->assertJsonFragment(['description' => 'Uma batata frita']);
    }

    public function testInvalidIdToUpdateCategory()
    {
        $this->json('PUT', route('categories.update', ['category' => 0]))
             ->assertStatus(404);
    }

    public function testDestroyCategory()
    {
        factory(Category::class)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);

        $categoryId = $categories->first()->id;

        $this->json('DELETE', route('categories.destroy', ['category' => $categoryId]))
             ->assertStatus(Response::HTTP_NO_CONTENT);
             
        $this->json('GET', route('categories.show', ['category' => $categoryId]))
             ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
