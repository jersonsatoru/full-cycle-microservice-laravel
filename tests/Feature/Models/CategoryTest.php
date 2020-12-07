<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    use DatabaseMigrations;

    public function testListCategory()
    {
        factory(Category::class, 100)->create();
        $categories = Category::all();
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertCount(100, $categories);
        $this->assertEqualsCanonicalizing(
            $categoryKeys,
            ['id', 'name', 'is_active', 'description', 'created_at', 'deleted_at', 'updated_at'],
        );
    }

    public function testCreateCategory()
    {
        $c = Category::create([
            'name' => 'Jerson',
        ]);

        $c->refresh();

        $this->assertEquals("Jerson", $c->name);
        $this->assertNull($c->description);
        $this->assertTrue($c->is_active);
        $this->assertEquals(36, mb_strlen($c->id, 'utf-8'));

        $c = Category::create([
            'name' => 'Batata',
            'description' => 'Batata',
            'is_active' => false,
        ]);

        $this->assertEquals('Batata', $c->description);
        $this->assertFalse($c->is_active);
    }

    public function testUpdateCategory()
    {
        $category = factory(Category::class)->create([
            'description' => 'teste',
            'is_active' => false,
        ]);

        $category->update([
            'description' => 'new',
            'is_active' => true,
        ]);

        $this->assertTrue($category->is_active);
        $this->assertEquals('new', $category->description);
    }
    
    public function testDeleteCategory()
    {
        $category = Category::create(['name' => 'Terror']);
        $categories = Category::all();
        $this->assertCount(1, $categories);

        $category->delete();
        $categories = Category::all();
        $this->assertCount(0, $categories);
    }
}
