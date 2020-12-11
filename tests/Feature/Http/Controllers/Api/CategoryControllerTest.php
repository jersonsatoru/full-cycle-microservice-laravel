<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    
    use DatabaseMigrations;

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
        $this->assertInvalidParametersNameRequiredCategoryCreation($response);

        $response = $this->json('POST', route('categories.store', ['name' => str_repeat('s', 256), 'is_active' => 'a']));
        $this->assertInvalidParametersMaxNameCharsAndIsActiveBooleanCategoryCreation($response);
    }

    protected function assertInvalidParametersNameRequiredCategoryCreation(TestResponse $response) 
    {
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonValidationErrors(['name'])
             ->assertJsonMissingValidationErrors('is_active')
             ->assertJsonFragment([
                 \Lang::get('validation.required', ['attribute' => 'name']),
             ]);
    }

    protected function assertInvalidParametersMaxNameCharsAndIsActiveBooleanCategoryCreation(TestResponse $response)
    {
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJsonValidationErrors(['name', 'is_active'])
             ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
             ])
             ->assertJsonFragment([
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
             ]);
    }

    public function testUpdateValidation()
    {
        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), []);
        $this->assertInvalidParametersNameRequiredCategoryCreation($response);

        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT', 
            route('categories.update', ['category' => $category->id]),
            ['name' => str_repeat('a', 256), 'is_active' => 'a'],
        );
        $this->assertInvalidParametersMaxNameCharsAndIsActiveBooleanCategoryCreation($response);
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
