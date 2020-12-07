<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{
    /** @var Genre */
    private $genre;

    protected function setUp() : void
    {
        $this->genre = new Genre();
    }

    public function testFillableAttributes()
    {
        $this->assertEqualsCanonicalizing(
            ['name', 'is_active'],
            $this->genre->getFillable(),
        );
    }

    public function testDatesAttributes()
    {
        $this->assertEqualsCanonicalizing(
            ['created_at', 'updated_at', 'deleted_at'],
            $this->genre->getDates(),
        );
    }

    public function testIncrementingAttributes()
    {
        $this->assertEquals(
            false,
            $this->genre->incrementing,
        );
    }

    public function testCastsAttributes()
    {
        $this->assertEqualsCanonicalizing(
            ['is_active' => 'boolean', 'id' => 'string'],
            $this->genre->getCasts(),
        );
    }

    public function testIfUsingTraits()
    {
        $genreKeys = array_keys(class_uses(Genre::class));
        $this->assertEquals(
            [SoftDeletes::class, Uuid::class],
            $genreKeys, 
        );
    }
}
