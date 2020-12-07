<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{

    use DatabaseMigrations;

    public function testListGenre()
    {
        factory(Genre::class, 100)->create();
        $genres = Genre::all();
        $this->assertCount(100, $genres);
    }

    public function testCreateGenre()
    {
        $genre = Genre::create(['name' => 'Legal']);
        $genre->refresh();

        $this->assertEquals('Legal', $genre->name);
        $this->assertEquals(36, mb_strlen($genre->id, 'utf-8'));
        $this->assertTrue($genre->is_active);

        $genre = Genre::create([
            'name' => 'Legal 2',
            'is_active' => false,
        ]);

        $this->assertEquals('Legal 2', $genre->name);
        $this->assertEquals(36, mb_strlen($genre->id, 'utf-8'));
        $this->assertFalse($genre->is_active);
    }

    public function testUpdateGenre()
    {
        $genre = factory(Genre::class)->create([
            'name' => 'Batata',
            'is_active' => false,
        ]);

        $id = $genre->id;

        $this->assertEquals('Batata', $genre->name);
        $this->assertFalse($genre->is_active);

        $genre->update([
            'name' => 'Outra batata',
            'is_active' => true, 
        ]);

        $this->assertEquals('Outra batata', $genre->name);
        $this->assertTrue($genre->is_active);
        $this->assertEquals($id, $genre->id);
    }

    public function testDeleteGenre()
    {
        $genre = factory(Genre::class)->create([
            'name' => 'Batata',
            'is_active' => false,
        ]);

        $genres = Genre::all();
        $this->assertCount(1, $genres);

        $genre->delete();
        $genres = Genre::all();
        $this->assertCount(0, $genres);
    }
}
